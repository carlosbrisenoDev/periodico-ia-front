import { useEffect, useMemo, useState } from "react";
import { useLocation, useNavigate, useParams } from "react-router-dom";
import { API_BASE_URL } from "../libs/config.ts";
import { parseContentBlocks } from "../libs/contentBlocks.ts";
import { ApiError, apiFetch, getArticleRecommendations } from "../libs/http.ts";
import { CalendarIcon, ClockIcon } from "../components/Icons.tsx";
import PublicNavbar from "../components/PublicNavbar.tsx";
import PublicFooter from "../components/PublicFooter.tsx";
import type {
  ArticlePreviewData,
  ArticleRecommendation,
  ArticlePreviewLocationState,
} from "../libs/types.ts";

type ArticleDetailResponse = {
  id: string;
  title: string;
  slug: string;
  excerpt: string;
  content: string;
  featuredImageUrl: string | null;
  author: {
    id: string;
    name: string;
    bio?: string | null;
    avatarUrl?: string | null;
  } | null;
  categories: {
    id: string;
    name: string;
    slug: string;
  }[];
  publishedAt: string | null;
  createdAt: string;
  tags?: string[];
};



type SimpleCategory = {
  id: string;
  name: string;
  slug?: string;
};

type PublicationPreviewArticle = ArticlePreviewData & {
  authorAvatarUrl?: string | null;
  categorySlug?: string | null;
  categoryId?: string | null;
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



const normalizeCategories = (payload: unknown[]): SimpleCategory[] =>
  (Array.isArray(payload) ? payload : [])
    .map((item): SimpleCategory | null => {
      if (!item || typeof item !== "object") {
        return null;
      }

      const record = item as Record<string, unknown>;
      console.log(record);

      if (typeof record.id !== "string" || typeof record.name !== "string") {
        return null;
      }

      return {
        id: record.id,
        name: record.name,
        slug: typeof record.slug === "string" ? record.slug : record.name.toLowerCase().replace(/\s+/g, "-")
      };
    })
    .filter((item): item is SimpleCategory => item !== null);

const buildArticleFromPublicDetail = (
  detail: ArticleDetailResponse,
  fallback?: PublicationPreviewArticle | null,
): PublicationPreviewArticle => {
  const title = fallback?.title || detail.title || "Sin título";
  const excerpt = fallback?.excerpt || detail.excerpt || "";
  const content = fallback?.content || detail.content || "";

  const author = detail.author;
  const resolvedAuthorName = fallback?.authorName || author?.name || "Redacción";

  const firstCategory = detail.categories?.[0];
  const resolvedCategoryName = fallback?.categoryName || firstCategory?.name || "General";
  const resolvedCategoryId = fallback?.categoryId || firstCategory?.id || null;

  const resolvedCategorySlug = fallback?.categorySlug || firstCategory?.slug || null;

  return {
    id: fallback?.id ?? detail.id,
    title,
    excerpt,
    content,
    featuredImageUrl: fallback?.featuredImageUrl ?? detail.featuredImageUrl ?? null,
    tags: fallback?.tags?.length ? fallback.tags : (Array.isArray(detail.tags) ? detail.tags : []),
    authorName: resolvedAuthorName,
    authorAvatarUrl: fallback?.authorAvatarUrl ?? author?.avatarUrl ?? null,
    authorRole: fallback?.authorRole ?? author?.bio ?? null,
    categoryName: resolvedCategoryName,
    categoryId: resolvedCategoryId,
    categorySlug: resolvedCategorySlug,
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

  const localPreviewData = useMemo(() => {
    try {
      const stored = localStorage.getItem("periodico_preview_draft");
      if (stored) {
        const data = JSON.parse(stored) as ArticlePreviewLocationState;
        // Only use local storage if the ID matches the current route ID
        if (id && data.article?.id === id) {
          return data;
        }
        // If we are on the generic preview route /publication/preview (no ID)
        if (!id) {
          return data;
        }
      }
    } catch {
      // Ignore errors
    }
    return null;
  }, [id]);

  const [article, setArticle] = useState<PublicationPreviewArticle | null>(
    locationState?.article ?? localPreviewData?.article ?? null
  );
  const [categories, setCategories] = useState<SimpleCategory[]>([]);
  const [recommendations, setRecommendations] = useState<ArticleRecommendation[]>([]);
  const [loading, setLoading] = useState<boolean>(true);
  const [error, setError] = useState<string | null>(null);

  const handleShare = (network: string) => {
    const currentUrl = window.location.href;
    const title = article?.title || "Información de Altura";

    if (network === "facebook") {
      window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(currentUrl)}`, "_blank");
    } else if (network === "twitter") {
      window.open(`https://twitter.com/intent/tweet?url=${encodeURIComponent(currentUrl)}&text=${encodeURIComponent(title)}`, "_blank");
    } else if (network === "linkedin") {
      window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(currentUrl)}`, "_blank");
    } else if (network === "whatsapp") {
      window.open(`https://api.whatsapp.com/send?text=${encodeURIComponent(title + " " + currentUrl)}`, "_blank");
    } else if (network === "share") {
      if (navigator.share) {
        navigator.share({ title, url: currentUrl }).catch(console.error);
      } else {
        navigator.clipboard.writeText(currentUrl).then(() => alert("Enlace copiado al portapapeles"));
      }
    }
  };

  const recommendationTitle = useMemo(() => {
    if (article?.categoryName) {
      return `Más de Noticias`;
    }

    return "Recomendaciones";
  }, [article?.categoryName]);

  useEffect(() => {
    const controller = new AbortController();

    const loadPreview = async () => {
      try {
        setLoading(true);
        setError(null);

        let nextArticle = locationState?.article ?? localPreviewData?.article ?? null;

        if (id) {
          const [detail, categoriesPayload] = await Promise.all([
            apiFetch<ArticleDetailResponse>(`${API_BASE_URL}/api/v1/public/article/id/${id}`, {
              method: "GET",
              signal: controller.signal,
            }),
            apiFetch<unknown[]>(`${API_BASE_URL}/api/v1/public/categories`, {
              method: "GET",
              signal: controller.signal,
            }),
          ]);

          const normalizedCategories = normalizeCategories(categoriesPayload);
          setCategories(normalizedCategories);

          nextArticle = buildArticleFromPublicDetail(detail, nextArticle);
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
            limit: 6,
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
  }, [id, locationState?.article, localPreviewData?.article, navigate]);

  const publishedAt = article?.publishedAt ?? new Date().toISOString();
  const contentBlocks = useMemo(() => parseContentBlocks(article?.content ?? ""), [article?.content]);

  function slugify(string: string) {
    return string
      .toLowerCase()
      .normalize("NFD")
      .replace(/[̀-ͯ]/g, "")
      .replace(/[^a-z0-9]+/g, "-")
      .replace(/(^-|-$)/g, "");
  }

  console.log({ categories });

  return (
    <div className="public-layout">
      <PublicNavbar
        categories={categories as any}
        activeCategorySlug={article?.categoryId ?? undefined}
      />

      <main className="public-main">
        <div className="public-back-bar">
          <div className="public-back-inner">
            <button
              className="public-back-link"
              onClick={() => {
                if (article?.categoryId) {
                  const matchedCategory = categories.find(c => c.id === article?.categoryId);
                  const slugToUse = article?.categorySlug || matchedCategory?.slug || slugify(article?.categoryName || "");
                  navigate(`/categoria/${slugToUse}`);
                  console.log(article);

                  return;
                }
                navigate("/");
              }}
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className="lucide lucide-arrow-left w-4 h-4" style={{ width: 16, height: 16 }}><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg>
              {article?.categoryName ? `Volver a ${article.categoryName}` : "Volver a Inicio"}
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
              <div style={{ marginBottom: 24 }} onClick={() => {
                const matchedCategory = categories.find(c => c.id === article?.categoryId);
                const slugToUse = article?.categorySlug || matchedCategory?.slug || slugify(article?.categoryName || "");
                navigate(`/categoria/${slugToUse}`);
              }}>
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
                <button className="public-share-button facebook" aria-label="Facebook" onClick={() => handleShare('facebook')}>
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
                </button>
                <button className="public-share-button twitter" aria-label="X (Twitter)" onClick={() => handleShare('twitter')}>
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" /></svg>
                </button>
                <button className="public-share-button linkedin" aria-label="LinkedIn" onClick={() => handleShare('linkedin')}>
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect width="4" height="12" x="2" y="9"></rect><circle cx="4" cy="4" r="2"></circle></svg>
                </button>
                <button className="public-share-button share" aria-label="Share" onClick={() => handleShare('share')}>
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
                    <a key={item.id} href={`/articulo/${item.id}`} className="public-card">
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
                <p style={{ color: "#4b5563" }}>Todavía no hay recomendaciones para este artículo.</p>
              )}
            </div>
          </section>
        ) : null}
      </main>

      <PublicFooter categories={categories} />
    </div>
  );
};

export default PublicationPreview;
