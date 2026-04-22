const rawApiUrl = import.meta.env.VITE_API_URL as string | undefined;

const removeTrailingSlash = (value: string): string => value.replace(/\/$/, "");

const isLoopbackHost = (hostname: string): boolean =>
  hostname === "localhost" || hostname === "127.0.0.1";

const normalizeApiBaseUrl = (value: string): string => {
  const base = removeTrailingSlash(value.trim());

  if (typeof window === "undefined") {
    return base;
  }

  try {
    const parsed = new URL(base);

    if (isLoopbackHost(parsed.hostname) && isLoopbackHost(window.location.hostname)) {
      parsed.hostname = window.location.hostname;
    }

    return removeTrailingSlash(parsed.toString());
  } catch {
    return base;
  }
};

// Normalize trailing slash and keep loopback host aligned with the browser origin.
export const API_BASE_URL = normalizeApiBaseUrl(
  rawApiUrl?.trim() || "http://localhost:3000",
);
