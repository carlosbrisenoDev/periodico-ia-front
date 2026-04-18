const rawApiUrl = import.meta.env.VITE_API_URL as string | undefined;

// Normalize trailing slash to avoid doubled // when composing endpoints.
export const API_BASE_URL = (rawApiUrl?.trim() || "http://localhost:3000").replace(
  /\/$/,
  "",
);

