import { useEffect, useMemo, useState } from "react";
import { useLocation, useNavigate, useParams } from "react-router-dom";

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
    <div className="public-layout">
      <nav className="public-nav-container">
        <div className="public-nav-inner">
          <div className="public-nav-top">
            <button className="public-nav-mobile-btn" style={{ display: "none" }}>
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><line x1="4" x2="20" y1="12" y2="12"></line><line x1="4" x2="20" y1="6" y2="6"></line><line x1="4" x2="20" y1="18" y2="18"></line></svg>
            </button>
            <a className="public-nav-logo-link" href="#" onClick={(e) => { e.preventDefault(); navigate("/allentries"); }}>
              <span className="public-nav-logo-fallback">IF INFORMACIÓN DE ALTURA</span>
            </a>
            <div className="public-nav-actions">
              <div className="public-nav-date">
                <span>Miércoles, 15 de abril de 2026</span>
              </div>
              <button type="button" className="public-nav-subscribe" onClick={(e) => e.preventDefault()}>
                Suscribirse
              </button>
            </div>
          </div>
          <div className="public-nav-bottom">
            <div className="public-nav-links">
              <a className="public-nav-link" href="#" onClick={(e) => e.preventDefault()}>Noticias</a>
              <a className="public-nav-link" href="#" onClick={(e) => e.preventDefault()}>Seguridad</a>
              <a className="public-nav-link" href="#" onClick={(e) => e.preventDefault()}>Deportes</a>
              <a className="public-nav-link" href="#" onClick={(e) => e.preventDefault()}>Cultura</a>
              <a className="public-nav-link" href="#" onClick={(e) => e.preventDefault()}>Comunidad</a>
              <a className="public-nav-link" href="#" onClick={(e) => e.preventDefault()}>Opinión</a>
              <a className="public-nav-link active" href="#" onClick={(e) => e.preventDefault()}>Recientes</a>
            </div>
            <form className="public-nav-search" onSubmit={(e) => e.preventDefault()}>
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className="public-nav-search-icon"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.3-4.3"></path></svg>
              <input type="search" className="public-nav-search-input" placeholder="Buscar noticias..." />
            </form>
          </div>
        </div>
      </nav>

      <main className="public-main">
        <div className="public-back-bar">
          <div className="public-back-inner">
            <button
              className="public-back-link"
              onClick={() => {
                if (window.history.length > 1) {
                  navigate(-1);
                  return;
                }
                navigate(id ? `/publication/${id}/edit` : "/allentries");
              }}
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className="lucide lucide-arrow-left w-4 h-4" style={{width: 16, height: 16}}><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg>
              Volver a Edición
            </button>
          </div>
        </div>

        {loading ? (
          <div className="public-article" style={{ textAlign: "center" }}>Cargando vista previa...</div>
        ) : null}
        
        {!loading && error ? (
          <div className="public-article" style={{ textAlign: "center", color: "#8b1f1f" }}>{error}</div>
        ) : null}

        {!loading && article ? (
          <article className="public-article">
            {article.categoryName ? (
              <div style={{ marginBottom: 24 }}>
                <span className="public-article-category">{article.categoryName}</span>
              </div>
            ) : null}

            <h1 className="public-article-title">{article.title}</h1>
            <p className="public-article-excerpt">{article.excerpt}</p>

            <div className="public-article-meta">
              <div className="public-meta-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className="public-meta-icon"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                <span className="public-meta-author">{article.authorName}</span>
              </div>
              <div className="public-meta-item">
                <CalendarIcon />
                <span>{formatDate(publishedAt)}</span>
              </div>
              <div className="public-meta-item">
                <ClockIcon />
                <span>{formatTime(publishedAt)}</span>
              </div>
            </div>

            <div className="public-article-hero">
              {article.featuredImageUrl ? (
                <img
                  src={normalizeAssetUrl(article.featuredImageUrl) ?? ""}
                  alt={article.title}
                />
              ) : (
                <div style={{ width: "100%", height: "100%", display: "flex", alignItems: "center", justifyContent: "center", color: "#5a7a94" }}>
                  <span>Imagen destacada pendiente</span>
                </div>
              )}
            </div>

            <div className="public-article-prose">
              {contentBlocks.map((block, index) => {
                if (block.type === "subtitle") {
                  return (
                    <h3 key={`${block.text.slice(0, 24)}-${index}`}>
                      {block.text}
                    </h3>
                  );
                }

                if (block.type === "image") {
                  const imageUrl = normalizeAssetUrl(block.url);
                  if (!imageUrl) return null;

                  return (
                    <figure key={`${block.url.slice(0, 24)}-${index}`}>
                      <div className="public-article-inline-image-wrapper">
                        <img src={imageUrl} alt="Imagen del contenido" />
                      </div>
                    </figure>
                  );
                }

                return (
                  <p key={`${block.text.slice(0, 24)}-${index}`}>
                    {block.text}
                  </p>
                );
              })}
            </div>

            <div className="public-share-section">
              <h3 className="public-share-title">COMPARTIR:</h3>
              <div className="public-share-buttons">
                <button className="public-share-button" aria-label="Facebook">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
                </button>
                <button className="public-share-button" aria-label="Twitter">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"></path></svg>
                </button>
                <button className="public-share-button" aria-label="LinkedIn">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect width="4" height="12" x="2" y="9"></rect><circle cx="4" cy="4" r="2"></circle></svg>
                </button>
                <button className="public-share-button" aria-label="Share">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" x2="15.42" y1="13.51" y2="17.49"></line><line x1="15.41" x2="8.59" y1="6.51" y2="10.49"></line></svg>
                </button>
              </div>
            </div>
          </article>
        ) : null}

        {article ? (
          <section className="public-recommendations">
            <div className="public-recommendations-inner">
              <h2 className="public-recommendations-title">{recommendationTitle}</h2>
              
              {recommendations.length > 0 ? (
                <div className="public-recommendations-grid">
                  {recommendations.map((item) => (
                    <a key={item.id} href="#" onClick={(e) => e.preventDefault()} className="public-card">
                      <div className="public-card-image-wrap">
                        {item.featuredImageUrl ? (
                          <img src={item.featuredImageUrl} alt={item.title} />
                        ) : (
                          <div style={{ width: "100%", height: "100%", backgroundColor: "#e5e7eb" }} />
                        )}
                      </div>
                      <div className="public-card-content">
                        {item.categoryName ? (
                          <div className="public-card-category">{item.categoryName}</div>
                        ) : null}
                        <h3 className="public-card-title">{item.title}</h3>
                        <div className="public-card-meta">
                          <span>{formatDate(item.publishedAt)}</span>
                          <span>•</span>
                          <span>{formatTime(item.publishedAt)}</span>
                        </div>
                      </div>
                    </a>
                  ))}
                </div>
              ) : (
                <p style={{ color: "#4b5563" }}>Todavia no hay recomendaciones para este articulo.</p>
              )}
            </div>
          </section>
        ) : null}
      </main>

      <footer className="public-footer">
        <div className="public-footer-inner">
          <div className="public-footer-grid">
            <div className="public-footer-brand">
              <div className="public-nav-logo-fallback" style={{ marginBottom: 16 }}>IF INFORMACIÓN DE ALTURA</div>
              <p>Periodismo independiente para el mundo moderno.</p>
              <div className="public-footer-phone">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                <span>+34 900 123 456</span>
              </div>
            </div>
            
            <div>
              <h4 className="public-footer-title">Secciones</h4>
              <ul className="public-footer-links">
                <li><a href="#" className="public-footer-link" onClick={(e) => e.preventDefault()}>Noticias</a></li>
                <li><a href="#" className="public-footer-link" onClick={(e) => e.preventDefault()}>Seguridad</a></li>
                <li><a href="#" className="public-footer-link" onClick={(e) => e.preventDefault()}>Deportes</a></li>
                <li><a href="#" className="public-footer-link" onClick={(e) => e.preventDefault()}>Cultura</a></li>
                <li><a href="#" className="public-footer-link" onClick={(e) => e.preventDefault()}>Comunidad</a></li>
                <li><a href="#" className="public-footer-link" onClick={(e) => e.preventDefault()}>Opinión</a></li>
              </ul>
            </div>
            
            <div>
              <h4 className="public-footer-title">Redes Sociales</h4>
              <div className="public-footer-social">
                <a href="#" className="public-social-button" aria-label="Facebook">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
                </a>
                <a href="#" className="public-social-button" aria-label="Twitter">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"></path></svg>
                </a>
                <a href="#" className="public-social-button" aria-label="Instagram">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"></line></svg>
                </a>
                <a href="#" className="public-social-button" aria-label="LinkedIn">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect width="4" height="12" x="2" y="9"></rect><circle cx="4" cy="4" r="2"></circle></svg>
                </a>
              </div>
            </div>
          </div>
          
          <div className="public-footer-bottom">
            © 2026 El Diario. Todos los derechos reservados.
          </div>
        </div>
      </footer>
    </div>
  );
};

export default PublicationPreview;




