import { useEffect, useMemo, useState } from "react";
import { useLocation, useNavigate, useParams } from "react-router-dom";
import { Sidebar } from "../components/sidebar.tsx";
import { API_BASE_URL } from "../libs/config.ts";
import { parseContentBlocks } from "../libs/contentBlocks.ts";
import { ApiError, apiFetch, getArticleRecommendations } from "../libs/http.ts";
import type {
  ArticlePreviewData,
  ArticleRecommendation,
  ArticlePreviewLocationState,
} from "../libs/types.ts";

type ArticleDetailResponse = {
  id?: string;
  title?: string;
  excerpt?: string;
  content?: string;
  featuredImageUrl?: string | null;
  tags?: unknown;
  authorId?: string;
  categoryIds?: unknown;
  publishedAt?: string | null;
  createdAt?: string;
};

type SimpleUser = {
  id: string;
  name: string;
  avatarUrl?: string | null;
  bio?: string | null;
};

type SimpleCategory = {
  id: string;
  name: string;
};

type PublicationPreviewArticle = ArticlePreviewData & {
  authorAvatarUrl?: string | null;
  authorRole?: string | null;
};


const formatDate = (isoDate: string | null | undefined): string => {
  if (!isoDate) {
    return "Fecha desconocida";
  }

  const value = new Date(isoDate);
  if (Number.isNaN(value.getTime())) {
    return "Fecha desconocida";
  }

  return new Intl.DateTimeFormat("es-ES", {
    day: "2-digit",
    month: "short",
    year: "numeric",
  })
    .format(value)
    .replace(".", "");
};

const formatTime = (isoDate: string | null | undefined): string => {
  if (!isoDate) {
    return "Hora desconocida";
  }

  const value = new Date(isoDate);
  if (Number.isNaN(value.getTime())) {
    return "Hora desconocida";
  }

  return new Intl.DateTimeFormat("es-ES", {
    hour: "2-digit",
    minute: "2-digit",
  }).format(value);
};

const CalendarIcon = () => (
  <svg
    xmlns="http://www.w3.org/2000/svg"
    width="24"
    height="24"
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    strokeWidth="2"
    strokeLinecap="round"
    strokeLinejoin="round"
    className="article-preview-meta-icon"
    aria-hidden="true"
  >
    <path d="M8 2v4" />
    <path d="M16 2v4" />
    <rect width="18" height="18" x="3" y="4" rx="2" />
    <path d="M3 10h18" />
  </svg>
);

const ClockIcon = () => (
  <svg
    xmlns="http://www.w3.org/2000/svg"
    width="24"
    height="24"
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    strokeWidth="2"
    strokeLinecap="round"
    strokeLinejoin="round"
    className="article-preview-meta-icon"
    aria-hidden="true"
  >
    <circle cx="12" cy="12" r="10" />
    <polyline points="12 6 12 12 16 14" />
  </svg>
);

const normalizeTags = (value: unknown): string[] => {
  if (!Array.isArray(value)) {
    return [];
  }

  return Array.from(
    new Set(
      value
        .map((tag) => (typeof tag === "string" ? tag.trim() : ""))
        .filter((tag) => tag.length > 0),
    ),
  );
};

const normalizeAuthors = (payload: unknown[]): SimpleUser[] =>
  (Array.isArray(payload) ? payload : [])
    .map((item): SimpleUser | null => {
      if (!item || typeof item !== "object") {
        return null;
      }

      const record = item as Record<string, unknown>;
      if (typeof record.id !== "string" || typeof record.name !== "string") {
        return null;
      }

      return {
        id: record.id,
        name: record.name,
        avatarUrl: typeof record.avatarUrl === "string" ? record.avatarUrl : null,
        bio: typeof record.bio === "string" ? record.bio : null,
      };
    })
    .filter((item): item is SimpleUser => item !== null);

const resolveAuthorRole = (fallbackRole?: string | null, bio?: string | null): string => {
  const trimmedFallback = fallbackRole?.trim();
  if (trimmedFallback) {
    return trimmedFallback;
  }

  const trimmedBio = bio?.trim();
  if (trimmedBio) {
    return trimmedBio;
  }

  return "Editor en Jefe";
};

const normalizeCategories = (payload: unknown[]): SimpleCategory[] =>
  (Array.isArray(payload) ? payload : [])
    .map((item): SimpleCategory | null => {
      if (!item || typeof item !== "object") {
        return null;
      }

      const record = item as Record<string, unknown>;
      if (typeof record.id !== "string" || typeof record.name !== "string") {
        return null;
      }

      return { id: record.id, name: record.name };
    })
    .filter((item): item is SimpleCategory => item !== null);

const buildArticleFromDetail = (
  detail: ArticleDetailResponse,
  fallback?: PublicationPreviewArticle | null,
  authors: SimpleUser[] = [],
  categories: SimpleCategory[] = [],
): PublicationPreviewArticle | null => {
  const title = fallback?.title?.trim() || (typeof detail.title === "string" ? detail.title : "");
  const excerpt = fallback?.excerpt?.trim() || (typeof detail.excerpt === "string" ? detail.excerpt : "");
  const content = fallback?.content?.trim() || (typeof detail.content === "string" ? detail.content : "");

  if (!title || !excerpt || !content) {
    return null;
  }

  const resolvedAuthor =
    typeof detail.authorId === "string"
      ? authors.find((author) => author.id === detail.authorId)
      : undefined;

  const resolvedAuthorName = fallback?.authorName || resolvedAuthor?.name || "Redaccion";

  const firstCategoryId = Array.isArray(detail.categoryIds)
    ? detail.categoryIds.find((value): value is string => typeof value === "string")
    : null;

  const resolvedCategoryName =
    fallback?.categoryName ||
    (firstCategoryId ? categories.find((category) => category.id === firstCategoryId)?.name : undefined) ||
    "General";

  return {
    id: fallback?.id ?? detail.id,
    title,
    excerpt,
    content,
    featuredImageUrl: fallback?.featuredImageUrl ?? detail.featuredImageUrl ?? null,
    tags: fallback?.tags?.length ? fallback.tags : normalizeTags(detail.tags),
    authorName: resolvedAuthorName,
    authorAvatarUrl: fallback?.authorAvatarUrl ?? resolvedAuthor?.avatarUrl ?? null,
    authorRole: resolveAuthorRole(fallback?.authorRole, resolvedAuthor?.bio),
    categoryName: resolvedCategoryName,
    publishedAt: fallback?.publishedAt ?? detail.publishedAt ?? detail.createdAt ?? null,
  };
};

const normalizeAssetUrl = (value: string | null | undefined): string | null => {
  if (!value) {
    return null;
  }

  if (value.startsWith("http://") || value.startsWith("https://")) {
    return value;
  }

  return `${API_BASE_URL}${value.startsWith("/") ? value : `/${value}`}`;
};

export const PublicationPreview = () => {
  const navigate = useNavigate();
  const { id } = useParams<{ id?: string }>();
  const location = useLocation();
  const locationState = location.state as ArticlePreviewLocationState | null;

  const [article, setArticle] = useState<PublicationPreviewArticle | null>(locationState?.article ?? null);
  const [recommendations, setRecommendations] = useState<ArticleRecommendation[]>([]);
  const [loading, setLoading] = useState<boolean>(true);
  const [error, setError] = useState<string | null>(null);

  const recommendationTitle = useMemo(() => {
    if (article?.categoryName) {
      return `Más de ${article.categoryName}`;
    }

    return "Recomendaciones";
  }, [article?.categoryName]);

  useEffect(() => {
    const controller = new AbortController();

    const loadPreview = async () => {
      try {
        setLoading(true);
        setError(null);

        let nextArticle = locationState?.article ?? null;

        if (id) {
          const [detail, authorsPayload, categoriesPayload] = await Promise.all([
            apiFetch<ArticleDetailResponse>(`${API_BASE_URL}/api/v1/article/${id}`, {
              method: "GET",
              credentials: "include",
              signal: controller.signal,
            }),
            apiFetch<unknown[]>(`${API_BASE_URL}/api/v1/author`, {
              method: "GET",
              credentials: "include",
              signal: controller.signal,
            }),
            apiFetch<unknown[]>(`${API_BASE_URL}/api/v1/category`, {
              method: "GET",
              credentials: "include",
              signal: controller.signal,
            }),
          ]);

          nextArticle = buildArticleFromDetail(
            detail,
            nextArticle,
            normalizeAuthors(authorsPayload),
            normalizeCategories(categoriesPayload),
          );
        }

        if (!nextArticle) {
          setError("No hay información suficiente para mostrar la vista previa.");
          setArticle(null);
          setRecommendations([]);
          return;
        }

        setArticle(nextArticle);

        const related = await getArticleRecommendations(
          {
            tags: nextArticle.tags,
            excludeId: nextArticle.id,
            limit: 3,
          },
          controller.signal,
        );

        setRecommendations(related);
      } catch (err: unknown) {
        if (err instanceof Error && err.name === "AbortError") {
          return;
        }

        if (err instanceof ApiError && (err.status === 401 || err.status === 403)) {
          navigate("/adminlogin", { replace: true });
          return;
        }

        setError(err instanceof Error ? err.message : "No se pudo cargar la vista previa.");
        setRecommendations([]);
      } finally {
        setLoading(false);
      }
    };

    void loadPreview();

    return () => controller.abort();
  }, [id, locationState, navigate]);

  const publishedAt = article?.publishedAt ?? new Date().toISOString();
  const contentBlocks = useMemo(() => parseContentBlocks(article?.content ?? ""), [article?.content]);

  return (
    <div className="layout dashboard-layout">
      <aside className="sidebar">
        <Sidebar />
      </aside>

      <main className="content article-preview-content">
        <header className="article-preview-header">
          <button
            type="button"
            className="article-preview-back"
            onClick={() => {
              if (window.history.length > 1) {
                navigate(-1);
                return;
              }

              navigate(id ? `/publication/${id}/edit` : "/allentries");
            }}
          >
            <span aria-hidden="true">←</span>
            Volver
          </button>

          <div className="article-preview-header-copy">
            <h1>El Periodico</h1>
            <p>Vista de Articulo</p>
          </div>

          <div className="article-preview-header-spacer" />
        </header>

        <section className="article-preview-shell">
          {loading ? <p className="article-preview-state">Cargando vista previa...</p> : null}
          {!loading && error ? <p className="article-preview-state error">{error}</p> : null}

          {!loading && article ? (
            <article className="article-preview-article">
              {article.categoryName ? <span className="article-preview-category">{article.categoryName}</span> : null}

              <h2 className="article-preview-title">{article.title}</h2>
              <p className="article-preview-excerpt">{article.excerpt}</p>

              <div className="article-preview-meta">
                <div className="article-preview-author-block">
                  <div className="article-preview-avatar">
                    {article.authorAvatarUrl ? (
                      <img
                        className="article-preview-avatar-image"
                        src={article.authorAvatarUrl}
                        alt={article.authorName}
                      />
                    ) : (
                      <span>
                        {article.authorName
                          .split(" ")
                          .slice(0, 2)
                          .map((part) => part.charAt(0).toUpperCase())
                          .join("") || "EP"}
                      </span>
                    )}
                  </div>
                  <div>
                    <p className="article-preview-author">Por {article.authorName}</p>
                    <p className="article-preview-role">{article.authorRole ?? "Editor en Jefe"}</p>
                  </div>
                </div>

                <div className="article-preview-dates">
                  <span className="article-preview-date-item">
                    <CalendarIcon />
                    <span>{formatDate(publishedAt)}</span>
                  </span>
                  <span>•</span>
                  <span className="article-preview-date-item">
                    <ClockIcon />
                    <span>{formatTime(publishedAt)}</span>
                  </span>
                </div>
              </div>

              <div className="article-preview-image-wrap">
                {article.featuredImageUrl ? (
                    <img
                    className="article-preview-image"
                      src={normalizeAssetUrl(article.featuredImageUrl) ?? ""}
                    alt={article.title}
                  />
                ) : (
                  <div className="article-preview-image placeholder">
                    <span>Imagen destacada pendiente</span>
                  </div>
                )}
              </div>

              <div className="article-preview-body">
                {contentBlocks.map((block, index) => {
                  if (block.type === "subtitle") {
                    return (
                      <h3 key={`${block.text.slice(0, 24)}-${index}`} className="article-preview-subtitle-block">
                        {block.text}
                      </h3>
                    );
                  }

                  if (block.type === "image") {
                    const imageUrl = normalizeAssetUrl(block.url);
                    if (!imageUrl) {
                      return null;
                    }

                    return (
                      <figure key={`${block.url.slice(0, 24)}-${index}`} className="article-preview-inline-image-wrap">
                        <img
                          className="article-preview-inline-image"
                          src={imageUrl}
                          alt="Imagen del contenido"
                        />
                      </figure>
                    );
                  }

                  return (
                    <p key={`${block.text.slice(0, 24)}-${index}`} className="article-preview-paragraph">
                      {block.text}
                    </p>
                  );
                })}
              </div>

              <section className="article-preview-tags-section">
                <h3>Etiquetas</h3>
                <div className="article-preview-tags">
                  {article.tags.length > 0 ? (
                    article.tags.map((tag) => (
                      <span key={tag} className="article-preview-tag">
                        #{tag}
                      </span>
                    ))
                  ) : (
                    <span className="article-preview-empty-tag">Sin etiquetas</span>
                  )}
                </div>
              </section>

              <section className="article-preview-recommendations">
                <div className="article-preview-recommendations-head">
                  <h3>{recommendationTitle}</h3>
                  <p>Articulos publicados en la ultima semana con etiquetas similares.</p>
                </div>

                {recommendations.length > 0 ? (
                  <div className="article-preview-recommendations-grid">
                    {recommendations.map((item) => (
                      <article key={item.id} className="article-preview-recommendation-card">
                        <div className="article-preview-recommendation-image-wrap">
                          {item.featuredImageUrl ? (
                            <img
                              className="article-preview-recommendation-image"
                              src={item.featuredImageUrl}
                              alt={item.title}
                            />
                          ) : (
                            <div className="article-preview-recommendation-image placeholder" />
                          )}
                        </div>

                        <div className="article-preview-recommendation-body">
                          {item.categoryName ? (
                            <span className="article-preview-recommendation-category">{item.categoryName}</span>
                          ) : null}
                          <h4>{item.title}</h4>
                          <p>{item.excerpt}</p>
                          <div className="article-preview-recommendation-meta">
                            <span>{formatDate(item.publishedAt)}</span>
                            <span>•</span>
                            <span>{item.matchedTags.slice(0, 3).map((tag) => `#${tag}`).join(" ")}</span>
                          </div>
                        </div>
                      </article>
                    ))}
                  </div>
                ) : (
                  <p className="article-preview-empty-recommendations">
                    Todavia no hay recomendaciones para este articulo.
                  </p>
                )}
              </section>
            </article>
          ) : null}
        </section>
      </main>
    </div>
  );
};

export default PublicationPreview;




