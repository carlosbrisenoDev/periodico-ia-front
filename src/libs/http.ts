import { API_BASE_URL } from "./config.ts";
import type {
  AuthMeResponse,
  ArticleRecommendation,
  ChangePasswordInput,
  ChangePasswordResponse,
  ProfileData,
  PublicArticle,
  PublicHomeResponse,
  UpdateProfileInput,
  UpdateProfileResponse,
} from "./types.ts";

export class ApiError extends Error {
  readonly status: number;
  readonly payload: unknown;

  constructor(message: string, status: number, payload: unknown) {
    super(message);
    this.name = "ApiError";
    this.status = status;
    this.payload = payload;
  }
}

type RecommendationsResponse = {
  items?: unknown[];
};

type RegisterSubscriberResponse = {
  message?: string;
  subscriber?: {
    id?: string;
    username?: string;
    email?: string;
    role?: string;
    status?: string;
    active?: boolean;
  };
};

const AUTH_PREFIX = `${API_BASE_URL}/api/v1/auth`;
const PUBLIC_PREFIX = `${API_BASE_URL}/api/v1/public`;
const SUBSCRIBERS_PREFIX = `${API_BASE_URL}/api/v1/subscribers`;
const SESSION_CACHE_TTL_MS = 30_000;

let cachedProfile: ProfileData | null = null;
let profileCachedAt = 0;
let meRequestInFlight: Promise<ProfileData> | null = null;

const parseJsonSafely = async (response: Response): Promise<unknown> => {
  const text = await response.text();

  if (!text) {
    return null;
  }

  try {
    return JSON.parse(text) as unknown;
  } catch {
    return text;
  }
};

export const apiFetch = async <T>(
  input: RequestInfo | URL,
  init?: RequestInit,
): Promise<T> => {
  const response = await fetch(input, init);
  
  const contentType = response.headers.get("content-type");
  if (contentType && contentType.includes("text/html")) {
    throw new ApiError("Error de enrutamiento: El servidor respondió con un documento HTML en lugar de JSON. Verifica la configuración de VITE_API_URL o el proxy inverso (Nginx).", response.status, await response.text());
  }

  const payload = await parseJsonSafely(response);

  if (!response.ok) {
    const fallbackMessage = `Request failed with status ${response.status}`;
    const messageFromPayload =
      payload && typeof payload === "object" && "message" in payload
        ? String((payload as { message?: unknown }).message ?? fallbackMessage)
        : fallbackMessage;

    throw new ApiError(messageFromPayload, response.status, payload);
  }

  return payload as T;
};

const normalizeProfile = (payload: AuthMeResponse | ProfileData): ProfileData | null => {
  const user = "user" in payload ? payload.user : payload;

  if (!user || typeof user !== "object") {
    return null;
  }

  const candidate = user as Record<string, unknown>;

  if (
    typeof candidate.id !== "string" ||
    typeof candidate.name !== "string" ||
    typeof candidate.email !== "string" ||
    typeof candidate.role !== "string" ||
    typeof candidate.active !== "boolean"
  ) {
    return null;
  }

  return {
    id: candidate.id,
    name: candidate.name,
    email: candidate.email,
    role: candidate.role,
    active: candidate.active,
  };
};

const hasFreshSessionCache = (): boolean => {
  if (!cachedProfile) {
    return false;
  }

  return Date.now() - profileCachedAt < SESSION_CACHE_TTL_MS;
};

export const invalidateSessionCache = (): void => {
  cachedProfile = null;
  profileCachedAt = 0;
};

export const updateSessionCache = (profile: ProfileData): ProfileData => {
  cachedProfile = profile;
  profileCachedAt = Date.now();
  return profile;
};

export const getMe = async (
  signal?: AbortSignal,
  options?: { force?: boolean },
): Promise<ProfileData> => {
  if (!options?.force && hasFreshSessionCache()) {
    return cachedProfile as ProfileData;
  }

  if (!options?.force && meRequestInFlight) {
    return meRequestInFlight;
  }

  const request = (async () => {
    const payload = await apiFetch<AuthMeResponse | ProfileData>(`${AUTH_PREFIX}/me`, {
      method: "GET",
      credentials: "include",
      signal,
    });

    const normalized = normalizeProfile(payload);

    if (!normalized) {
      throw new Error("Respuesta de sesion invalida");
    }

    cachedProfile = normalized;
    profileCachedAt = Date.now();

    return normalized;
  })();

  meRequestInFlight = request;

  try {
    return await request;
  } finally {
    meRequestInFlight = null;
  }
};

export const getOptionalMe = async (
  signal?: AbortSignal,
  options?: { force?: boolean },
): Promise<ProfileData | null> => {
  try {
    return await getMe(signal, options);
  } catch (error) {
    if (error instanceof ApiError && (error.status === 401 || error.status === 403)) {
      invalidateSessionCache();
      return null;
    }

    throw error;
  }
};

export const login = async (input: {
  email: string;
  password: string;
}): Promise<ProfileData | null> => {
  const payload = await apiFetch<{ user?: ProfileData }>(`${AUTH_PREFIX}/login`, {
    method: "POST",
    credentials: "include",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(input),
  });

  if (!payload.user) {
    invalidateSessionCache();
    return null;
  }

  const normalized = normalizeProfile(payload.user);

  if (!normalized) {
    invalidateSessionCache();
    return null;
  }

  cachedProfile = normalized;
  profileCachedAt = Date.now();

  return normalized;
};

export const logout = async (): Promise<void> => {
  try {
    await apiFetch<{ message?: string }>(`${AUTH_PREFIX}/logout`, {
      method: "POST",
      credentials: "include",
    });
  } finally {
    invalidateSessionCache();
  }
};

export const updateAuthProfile = async (
  input: UpdateProfileInput,
): Promise<ProfileData> => {
  const payload = await apiFetch<UpdateProfileResponse>(`${AUTH_PREFIX}/me`, {
    method: "PATCH",
    credentials: "include",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(input),
  });

  if (!payload.user) {
    throw new Error("No se pudo actualizar el perfil.");
  }

  const normalized = normalizeProfile(payload.user);

  if (!normalized) {
    throw new Error("Respuesta de perfil invalida");
  }

  updateSessionCache(normalized);
  return normalized;
};

export const changeAuthPassword = async (
  input: ChangePasswordInput,
): Promise<string> => {
  const payload = await apiFetch<ChangePasswordResponse>(`${AUTH_PREFIX}/change-password`, {
    method: "POST",
    credentials: "include",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(input),
  });

  return payload.message ?? "Password updated successfully";
};

export const registerSubscriber = async (input: {
  username: string;
  email: string;
  password: string;
}): Promise<{ message: string }> => {
  const payload = await apiFetch<RegisterSubscriberResponse>(`${SUBSCRIBERS_PREFIX}/register`, {
    method: "POST",
    credentials: "include",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(input),
  });

  return {
    message: payload.message ?? "Suscripcion creada correctamente.",
  };
};

export const isAdmin = (profile: ProfileData | null): boolean => {
  return profile?.role === "admin";
};

const normalizePublicArticle = (
  item: unknown,
  index: number,
): PublicArticle | null => {
  if (!item || typeof item !== "object") {
    return null;
  }

  const record = item as Record<string, unknown>;
  const title = typeof record.title === "string" ? record.title : null;

  if (!title) {
    return null;
  }

  const author =
    record.author && typeof record.author === "object"
      ? (record.author as Record<string, unknown>)
      : null;

  const category =
    record.category && typeof record.category === "object"
      ? (record.category as Record<string, unknown>)
      : null;

  const categories = Array.isArray(record.categories) ? record.categories : [];
  const firstCategory =
    categories.length > 0 && categories[0] && typeof categories[0] === "object"
      ? (categories[0] as Record<string, unknown>)
      : null;

  return {
    id:
      typeof record.id === "string"
        ? record.id
        : typeof record._id === "string"
          ? record._id
          : `public-${index}`,
    title,
    slug:
      typeof record.slug === "string"
        ? record.slug
        : typeof record.id === "string"
          ? record.id
          : `article-${index}`,
    excerpt: typeof record.excerpt === "string" ? record.excerpt : "Sin descripcion.",
    featuredImageUrl:
      typeof record.featuredImageUrl === "string" ? record.featuredImageUrl : undefined,
    createdAt:
      typeof record.createdAt === "string"
        ? record.createdAt
        : new Date().toISOString(),
    authorName:
      typeof record.authorName === "string"
        ? record.authorName
        : author && typeof author.name === "string"
          ? author.name
          : "Redaccion",
    categoryName:
      typeof record.categoryName === "string"
        ? record.categoryName
        : category && typeof category.name === "string"
          ? category.name
          : firstCategory && typeof firstCategory.name === "string"
            ? firstCategory.name
            : "General",
  };
};

export const getRecentPublications = async (
  signal?: AbortSignal,
): Promise<PublicArticle[]> => {
  const home = await apiFetch<PublicHomeResponse>(`${PUBLIC_PREFIX}/home`, {
    method: "GET",
    signal,
  });

  const source =
    Array.isArray(home.recent) && home.recent.length > 0
      ? home.recent
      : Array.isArray(home.latest)
        ? home.latest
        : [];

  return source
    .map((item, index) => normalizePublicArticle(item, index))
    .filter((item): item is PublicArticle => item !== null)
    .slice(0, 8);
};

export const getLatestPublications = async (
  signal?: AbortSignal,
): Promise<PublicArticle[]> => {
  const latest = await apiFetch<unknown[]>(`${PUBLIC_PREFIX}/latest`, {
    method: "GET",
    signal,
  });

  return (Array.isArray(latest) ? latest : [])
    .map((item, index) => normalizePublicArticle(item, index))
    .filter((item): item is PublicArticle => item !== null)
    .slice(0, 8);
};

const normalizeRecommendation = (item: unknown, index: number): ArticleRecommendation | null => {
  if (!item || typeof item !== "object") {
    return null;
  }

  const record = item as Record<string, unknown>;

  if (typeof record.title !== "string" || typeof record.slug !== "string") {
    return null;
  }

  const tags = Array.isArray(record.tags)
    ? record.tags.filter((tag): tag is string => typeof tag === "string")
    : [];

  const matchedTags = Array.isArray(record.matchedTags)
    ? record.matchedTags.filter((tag): tag is string => typeof tag === "string")
    : [];

  return {
    id: typeof record.id === "string" ? record.id : `recommendation-${index}`,
    slug: record.slug,
    title: record.title,
    excerpt: typeof record.excerpt === "string" ? record.excerpt : "Sin descripcion.",
    featuredImageUrl:
      typeof record.featuredImageUrl === "string" ? record.featuredImageUrl : undefined,
    publishedAt:
      typeof record.publishedAt === "string" ? record.publishedAt : new Date().toISOString(),
    tags,
    matchedTags,
    authorName: typeof record.authorName === "string" ? record.authorName : undefined,
    categoryName: typeof record.categoryName === "string" ? record.categoryName : undefined,
  };
};

export const getArticleRecommendations = async (
  input: {
    tags: string[];
    excludeId?: string;
    limit?: number;
  },
  signal?: AbortSignal,
): Promise<ArticleRecommendation[]> => {
  const params = new URLSearchParams();

  if (input.tags.length > 0) {
    params.set("tags", Array.from(new Set(input.tags.map((tag) => tag.trim()).filter(Boolean))).join(","));
  }

  if (input.excludeId) {
    params.set("excludeId", input.excludeId);
  }

  if (typeof input.limit === "number") {
    params.set("limit", String(input.limit));
  }

  const response = await apiFetch<RecommendationsResponse>(`${PUBLIC_PREFIX}/recommendations?${params.toString()}`, {
    method: "GET",
    signal,
  });

  return (Array.isArray(response.items) ? response.items : [])
    .map((item, index) => normalizeRecommendation(item, index))
    .filter((item): item is ArticleRecommendation => item !== null);
};

