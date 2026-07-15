import { useEffect, useMemo, useState } from "react";
import { useLocation, useNavigate, useParams } from "react-router-dom";
import { API_BASE_URL } from "../libs/config.ts";
import { parseContentBlocks } from "../libs/contentBlocks.ts";
import { ApiError, apiFetch, getArticleRecommendations } from "../libs/http.ts";
import { CalendarIcon, ClockIcon } from "../components/Icons.tsx";
import { FormattedText } from "../components/FormattedText.tsx";
import PublicNavbar from "../components/PublicNavbar.tsx";
import PublicFooter from "../components/PublicFooter.tsx";
import type {
  ArticlePreviewData,
  ArticleRecommendation,
  ArticlePreviewLocationState,
} from "../libs/types.ts";
import { CommentsSection } from "../components/CommentsSection.tsx";
import { AdBlock } from "../components/AdBlock.tsx";

type ArticleDetailResponse = {
  id: string;
  title: string;
  slug: string;
  excerpt: string;
  content: string;
  featuredImageUrl: string | null;
  featuredImageCaption: string | null;
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
  isVideoGallery?: boolean;
  videoUrl?: string | null;
  allowComments?: boolean;
};

const getYoutubeEmbedUrl = (url?: string | null) => {
  if (!url) return null;
  const match = url.match(/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=|shorts\/|live\/))([\w-]{11})/i);
  return match ? `https://www.youtube.com/embed/${match[1]}` : null;
};

type SimpleCategory = {
  id: string;
  name: string;
  slug?: string;
};

type PublicationPreviewArticle = ArticlePreviewData & {
  slug?: string;
  authorAvatarUrl?: string | null;
  categorySlug?: string | null;
  categoryId?: string | null;
  isVideoGallery?: boolean;
  videoUrl?: string | null;
  allowComments?: boolean;
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
    slug: fallback?.slug ?? detail.slug,
    title,
    excerpt,
    content,
    featuredImageUrl: fallback?.featuredImageUrl ?? detail.featuredImageUrl ?? null,
    featuredImageCaption: fallback?.featuredImageCaption ?? detail.featuredImageCaption ?? null,
    tags: fallback?.tags?.length ? fallback.tags : (Array.isArray(detail.tags) ? detail.tags : []),
    authorName: resolvedAuthorName,
    authorAvatarUrl: fallback?.authorAvatarUrl ?? author?.avatarUrl ?? null,
    authorRole: fallback?.authorRole ?? author?.bio ?? null, 
    categoryName: resolvedCategoryName,
    categoryId: resolvedCategoryId,
    categorySlug: resolvedCategorySlug,
    isVideoGallery: fallback?.isVideoGallery ?? detail.isVideoGallery ?? false,
    videoUrl: fallback?.videoUrl ?? detail.videoUrl ?? null,
    allowComments: fallback?.allowComments ?? detail.allowComments ?? true,
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

const useArticleSEO = (article: PublicationPreviewArticle | null) => {
  useEffect(() => {
    if (!article) return;

    let plainTextContent = "";
    try {
      if (article.content) {
        const blocks = JSON.parse(article.content);
        if (Array.isArray(blocks)) {
          plainTextContent = blocks
            .filter((b: any) => b.type === "paragraph" || b.type === "header")
            .map((b: any) => b.content?.replace(/<[^>]+>/g, "") || "")
            .join(" ")
            .trim();
        }
      }
    } catch(e) {
      plainTextContent = article.content?.replace(/<[^>]+>/g, "").trim() || "";
    }
    
    let description = article.excerpt || plainTextContent;
    if (description.length > 150) {
      description = description.substring(0, 147) + "...";
    }
    if (!description) {
      description = "Contenido de alta calidad en Información de Altura";
    }

    const title = article.title ? `${article.title} | Información de Altura` : "Información de Altura";
    const url = `${window.location.origin}/articulo/${article.slug || article.id}`;
    const imageUrl = normalizeAssetUrl(article.featuredImageUrl) || `${window.location.origin}/default-share.jpg`;

    document.title = title;

    const setMetaTag = (attrName: string, attrValue: string, content: string) => {
      let element = document.querySelector(`meta[${attrName}="${attrValue}"]`);
      if (!element) {
        element = document.createElement("meta");
        element.setAttribute(attrName, attrValue);
        document.head.appendChild(element);
      }
      element.setAttribute("content", content);
    };

    setMetaTag("name", "description", description);
    
    setMetaTag("property", "og:type", "article");
    setMetaTag("property", "og:title", title);
    setMetaTag("property", "og:description", description);
    setMetaTag("property", "og:image", imageUrl);
    setMetaTag("property", "og:url", url);
    setMetaTag("property", "og:site_name", "Información de Altura");

    setMetaTag("name", "twitter:card", "summary_large_image");
    setMetaTag("name", "twitter:title", title);
    setMetaTag("name", "twitter:description", description);
    setMetaTag("name", "twitter:image", imageUrl);

    if (article.authorName) setMetaTag("property", "article:author", article.authorName);
    if (article.publishedAt) setMetaTag("property", "article:published_time", article.publishedAt);
    if (article.categoryName) setMetaTag("property", "article:section", article.categoryName);
    if (article.tags && article.tags.length > 0) {
      setMetaTag("property", "article:tag", article.tags.join(", "));
    }

    let script = document.querySelector("script[id='seo-jsonld']") as HTMLScriptElement;
    if (!script) {
      script = document.createElement("script");
      script.type = "application/ld+json";
      script.id = "seo-jsonld";
      document.head.appendChild(script);
    }
    
    const jsonLd = {
      "@context": "https://schema.org",
      "@type": "NewsArticle",
      "headline": article.title,
      "image": [imageUrl],
      "datePublished": article.publishedAt || new Date().toISOString(),
      "dateModified": article.publishedAt || new Date().toISOString(),
      "author": [{
        "@type": "Person",
        "name": article.authorName || "Redacción"
      }],
      "publisher": {
        "@type": "Organization",
        "name": "Información de Altura",
        "logo": {
          "@type": "ImageObject",
          "url": `${window.location.origin}/logo.png`
        }
      },
      "description": description
    };
    script.textContent = JSON.stringify(jsonLd);
  }, [article]);
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
        if (id && data.article?.id === id) {
          return data;
        }
        if (!id) {
          return data;
        }
      }
    } catch {
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

  const [showReportModal, setShowReportModal] = useState(false);
  const [reportReason, setReportReason] = useState("");
  const [reportEmail, setReportEmail] = useState("");
  const [reporting, setReporting] = useState(false);
  const [reportSuccess, setReportSuccess] = useState(false);
  const [reportError, setReportError] = useState<string | null>(null);

  const [isPlaying, setIsPlaying] = useState(false);
  const [isPaused, setIsPaused] = useState(false);

  useArticleSEO(article);

  useEffect(() => {
    return () => {
      if ("speechSynthesis" in window) {
        window.speechSynthesis.cancel();
      }
    };
  }, []);

  const handleTTS = () => {
    if (!("speechSynthesis" in window)) {
      alert("Tu navegador no soporta lectura por voz.");
      return;
    }

    if (isPlaying) {
      if (isPaused) {
        window.speechSynthesis.resume();
        setIsPaused(false);
      } else {
        window.speechSynthesis.pause();
        setIsPaused(true);
      }
      return;
    }

    window.speechSynthesis.cancel();
    
    const textToRead = `
      ${article?.title || ""} 
      ${article?.excerpt || ""} 
      ${contentBlocks.filter((b: any) => b.type === "text" || b.type === "subtitle").map((b: any) => b.text).join(". ")}
    `;

    const utterance = new SpeechSynthesisUtterance(textToRead);
    utterance.lang = "es-ES";
    utterance.onend = () => {
      setIsPlaying(false);
      setIsPaused(false);
    };
    
    setIsPlaying(true);
    setIsPaused(false);
    window.speechSynthesis.speak(utterance);
  };

  const handleStopTTS = () => {
    if ("speechSynthesis" in window) {
      window.speechSynthesis.cancel();
      setIsPlaying(false);
      setIsPaused(false);
    }
  };

  const handleReportSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!article?.id) return;
    if (!reportReason.trim() || reportReason.trim().length > 500) {
      setReportError("Por favor ingresa un motivo válido (máximo 500 caracteres).");
      return;
    }
    
    try {
      setReporting(true);
      setReportError(null);
      await apiFetch(`${API_BASE_URL}/api/v1/reports`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          targetType: "article",
          targetId: article.id,
          reason: reportReason,
          contactEmail: reportEmail.trim() || undefined
        })
      });
      setReportSuccess(true);
      setReportReason("");
      setReportEmail("");
    } catch (err: unknown) {
      setReportError(err instanceof Error ? err.message : "Error al enviar el reporte.");
    } finally {
      setReporting(false);
    }
  };

  const handleShare = (network: string) => {
    let slugToShare = article?.slug || (article?.title ? slugify(article.title) : article?.id);
    const currentUrl = `${window.location.origin}/articulo/${slugToShare}`;
    const title = article?.title || "Información de Altura";

    if (network === "facebook") {
      window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(currentUrl)}`, "_blank");
    } else if (network === "twitter") {
      window.open(`https://twitter.com/intent/tweet?url=${encodeURIComponent(currentUrl)}&text=${encodeURIComponent(title)}`, "_blank");
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

        let nextArticle = locationState?.article ?? localPreviewData?.article ?? null;

        if (id) {
          const isMongoId = /^[a-fA-F0-9]{24}$/.test(id);
          const endpoint = isMongoId 
            ? `${API_BASE_URL}/api/v1/public/article/id/${id}`
            : `${API_BASE_URL}/api/v1/public/article/${id}`;
            
          const [detail, categoriesPayload] = await Promise.all([
            apiFetch<ArticleDetailResponse>(endpoint, {
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

          if (isMongoId && detail.slug) {
            navigate(`/articulo/${detail.slug}`, { replace: true, state: locationState });
            return;
          }

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
          <div className="public-article" style={{ textAlign: "center", color: "var(--theme-primary-color)" }}>{error}</div>
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

            {article.tags && article.tags.length > 0 ? (
              <div style={{ display: "flex", gap: "8px", flexWrap: "wrap", marginBottom: "16px" }}>
                {article.tags.map((tag, idx) => (
                  <span key={idx} onClick={() => navigate(`/buscar?q=${encodeURIComponent(tag)}`)} style={{ padding: "4px 10px", backgroundColor: "var(--bg-surface)", border: "1px solid var(--border)", borderRadius: "16px", fontSize: "0.75rem", cursor: "pointer", color: "var(--text-muted)", display: "inline-block" }}>
                    #{tag}
                  </span>
                ))}
              </div>
            ) : null}
            <div className="public-article-header">
              <h1 className="public-article-title"><FormattedText text={article.title} /></h1>
              {article.excerpt && <p className="public-article-excerpt"><FormattedText text={article.excerpt} /></p>}
            </div>

            <div style={{ display: "flex", gap: "10px", marginBottom: "20px" }}>
              <button 
                onClick={handleTTS} 
                style={{ display: "flex", alignItems: "center", gap: "6px", padding: "8px 12px", border: "1px solid var(--border)", borderRadius: "20px", background: "var(--bg-surface)", cursor: "pointer", fontSize: "0.875rem", color: "var(--text-main)" }}
              >
                {isPlaying && !isPaused ? (
                  <>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="6" y="4" width="4" height="16"></rect><rect x="14" y="4" width="4" height="16"></rect></svg>
                    Pausar lectura
                  </>
                ) : (
                  <>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                    {isPaused ? "Reanudar lectura" : "Escuchar artículo"}
                  </>
                )}
              </button>
              
              {isPlaying && (
                <button 
                  onClick={handleStopTTS} 
                  style={{ display: "flex", alignItems: "center", gap: "6px", padding: "8px 12px", border: "1px solid var(--border)", borderRadius: "20px", background: "var(--bg-surface)", cursor: "pointer", fontSize: "0.875rem", color: "var(--theme-primary-color)" }}
                >
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect></svg>
                  Detener
                </button>
              )}
            </div>

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
              {article.isVideoGallery && getYoutubeEmbedUrl(article.videoUrl) ? (
                <div style={{ position: "relative", paddingBottom: "56.25%", height: 0, overflow: "hidden", margin: 0 }}>
                  <iframe
                    src={getYoutubeEmbedUrl(article.videoUrl)!}
                    style={{ position: "absolute", top: 0, left: 0, width: "100%", height: "100%", border: 0 }}
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowFullScreen
                    title={article.title}
                  />
                </div>
              ) : article.featuredImageUrl ? (
                <figure style={{ margin: 0 }}>
                  <img
                    src={normalizeAssetUrl(article.featuredImageUrl) ?? ""}
                    alt={article.title}
                  />
                  {article.featuredImageCaption && (
                    <figcaption className="public-article-featured-caption">
                      {article.featuredImageCaption}
                    </figcaption>
                  )}
                </figure>
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
                      <FormattedText text={block.text} />
                    </h3>
                  );
                }

                if (block.type === "paragraph") {
                  return (
                    <p key={`${block.text.slice(0, 24)}-${index}`} style={{ textAlign: (block as any).align || "justify" }}>
                      <FormattedText text={block.text} />
                    </p>
                  );
                }

                if (block.type === "image") {
                  const imageUrl = normalizeAssetUrl(block.url);
                  if (!imageUrl) return null;

                  return (
                    <figure key={`${block.url.slice(0, 24)}-${index}`}>
                      <div className="public-article-inline-image-wrapper">
                        <img src={imageUrl} alt={block.caption || "Imagen del contenido"} />
                      </div>
                      {block.caption && (
                        <figcaption style={{ textAlign: "center", marginTop: "8px", color: "var(--text-muted)", fontSize: "0.875rem" }}>
                          {block.caption}
                        </figcaption>
                      )}
                    </figure>
                  );
                }

                if (block.type === "video") {
                  return (
                    <div key={`${block.url.slice(0, 24)}-${index}`} style={{ position: "relative", paddingBottom: "56.25%", height: 0, overflow: "hidden", margin: "24px 0" }}>
                      <iframe
                        src={getYoutubeEmbedUrl(block.url) || block.url}
                        style={{ position: "absolute", top: 0, left: 0, width: "100%", height: "100%", border: 0 }}
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowFullScreen
                        title="Video"
                      />
                    </div>
                  );
                }

                if (block.type === "image-row") {
                  return (
                    <div key={`row-${index}`} className={`image-row-grid image-row-${block.layout || 'equal'}`}>
                      {block.urls.map((url, i) => (
                        <div key={i} className={`image-row-item image-row-item-${i}`}>
                          <img src={normalizeAssetUrl(url) ?? ""} alt="Imagen" />
                        </div>
                      ))}
                    </div>
                  );
                }

                return null;
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
                <button className="public-share-button share" aria-label="Share" onClick={() => handleShare('share')}>
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" x2="15.42" y1="13.51" y2="17.49"></line><line x1="15.41" x2="8.59" y1="6.51" y2="10.49"></line></svg>
                </button>
              </div>
              <div style={{ marginTop: "24px", paddingTop: "24px", borderTop: "1px solid var(--border)" }}>
                <button 
                  className="entries-new-button" 
                  style={{ backgroundColor: "transparent", color: "var(--text-muted)", border: "1px solid var(--border)", padding: "8px 16px", borderRadius: "6px", fontSize: "0.875rem", display: "inline-flex", alignItems: "center", gap: "8px", cursor: "pointer" }}
                  onClick={() => setShowReportModal(true)}
                >
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className="lucide lucide-flag"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" x2="4" y1="22" y2="15"/></svg>
                  Reportar contenido
                </button>
              </div>
            </div>

            {article.id && !article.id.startsWith("public-") && !article.id.startsWith("article-") && (
              <CommentsSection articleId={article.id} allowComments={article.allowComments} />
            )}
            
            <AdBlock style={{ marginTop: "2rem" }} adSlot="ARTICLE_BOTTOM" />
          </article>
        ) : null}

        {article ? (
          <section className="public-recommendations">
            <div className="public-recommendations-inner">
              <h2 className="public-recommendations-title">{recommendationTitle}</h2>

              {recommendations.length > 0 ? (
                <div className="public-recommendations-grid">
                  {recommendations.map((item) => (
                    <a key={item.id} href={`/articulo/${item.slug || item.id}`} className="public-card">
                      <div className="public-card-image-wrap">
                        {item.featuredImageUrl ? (
                          <img src={item.featuredImageUrl} alt={item.title} />
                        ) : (
                          <div style={{ width: "100%", height: "100%", backgroundColor: "var(--border)" }} />
                        )}
                      </div>
                      <div className="public-card-content">
                        {item.categoryName ? (
                          <div className="public-card-category">{item.categoryName}</div>
                        ) : null}
                        <h3 className="public-card-title"><FormattedText text={item.title} /></h3>
                        {item.excerpt && <p className="public-card-excerpt"><FormattedText text={item.excerpt} /></p>}
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
                <p style={{ color: "var(--text-muted)" }}>Todavía no hay recomendaciones para este artículo.</p>
              )}
            </div>
          </section>
        ) : null}

        {showReportModal && (
          <div className="categories-modal-overlay" onClick={() => setShowReportModal(false)}>
            <div className="categories-modal" onClick={(e) => e.stopPropagation()} style={{ maxWidth: "500px" }}>
              <h2 className="categories-modal-title">Reportar Contenido</h2>
              
              {reportSuccess ? (
                <div style={{ padding: "24px 0", textAlign: "center" }}>
                  <p style={{ color: "#059669", fontWeight: "bold", marginBottom: "16px" }}>¡Gracias por tu reporte!</p>
                  <p style={{ color: "var(--text-muted)", marginBottom: "24px" }}>Revisaremos esta publicación lo antes posible.</p>
                  <button className="primary" onClick={() => {
                    setShowReportModal(false);
                    setReportSuccess(false);
                  }}>Cerrar</button>
                </div>
              ) : (
                <form className="categories-form" onSubmit={handleReportSubmit}>
                  <p style={{ marginBottom: "16px", color: "var(--text-muted)", fontSize: "0.875rem" }}>
                    Si consideras que este contenido viola nuestras normas o contiene información falsa, por favor detalla el motivo.
                  </p>
                  
                  <label htmlFor="report-reason">Motivo del reporte *</label>
                  <textarea
                    id="report-reason"
                    className="new-publication-input"
                    style={{ minHeight: "100px", resize: "vertical" }}
                    placeholder="Describe detalladamente el problema..."
                    value={reportReason}
                    onChange={(e) => setReportReason(e.target.value)}
                    maxLength={500}
                    required
                  />

                  <label htmlFor="report-email">Tu correo electrónico (opcional)</label>
                  <input
                    id="report-email"
                    type="email"
                    className="new-publication-input"
                    placeholder="Para contactarte si es necesario"
                    value={reportEmail}
                    onChange={(e) => setReportEmail(e.target.value)}
                  />

                  {reportError && <p className="categories-modal-error">{reportError}</p>}

                  <div className="categories-modal-actions">
                    <button type="button" onClick={() => setShowReportModal(false)} disabled={reporting}>
                      Cancelar
                    </button>
                    <button type="submit" className="primary" disabled={reporting} style={{ backgroundColor: "var(--theme-primary-color)" }}>
                      {reporting ? "Enviando..." : "Enviar Reporte"}
                    </button>
                  </div>
                </form>
              )}
            </div>
          </div>
        )}
      </main>

      <PublicFooter categories={categories} />
    </div>
  );
};

export default PublicationPreview;
