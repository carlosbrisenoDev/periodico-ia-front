import { useEffect, useState } from "react";
import { apiFetch, getPublicCategories } from "../libs/http.ts";
import "./recentpage.css";
import { API_BASE_URL } from "../libs/config.ts";
import type { PublicArticle, PublicCategory } from "../libs/types.ts";
import PublicNavbar from "../components/PublicNavbar.tsx";
import PublicFooter from "../components/PublicFooter.tsx";
import { NewspaperIcon } from "../components/Icons.tsx";
import { FormattedText } from "../components/FormattedText.tsx";


const formatArticleDate = (iso: string): string => {
  const d = new Date(iso);
  if (Number.isNaN(d.getTime())) return "";
  return new Intl.DateTimeFormat("es-ES", {
    day: "numeric",
    month: "long",
    year: "numeric",
  }).format(d);
};

const formatArticleTime = (iso: string): string => {
  const d = new Date(iso);
  if (Number.isNaN(d.getTime())) return "";
  return new Intl.DateTimeFormat("es-ES", {
    hour: "2-digit",
    minute: "2-digit",
  }).format(d);
};

const RecentPage = () => {
  const [articles, setArticles] = useState<PublicArticle[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [categories, setCategories] = useState<PublicCategory[]>([]);

  useEffect(() => {
    const controller = new AbortController();

    const loadData = async () => {
      try {
        setLoading(true);
        const [articlesData, fetchedCats] = await Promise.all([
          apiFetch<PublicArticle[]>(`${API_BASE_URL}/api/v1/public/recent`, {
            signal: controller.signal,
          }),
          getPublicCategories(controller.signal),
        ]);

        setArticles(Array.isArray(articlesData) ? articlesData : []);
        setCategories(fetchedCats);
      } catch (err) {
        if (err instanceof Error && err.name === "AbortError") return;
        setError("No se pudieron cargar las noticias recientes.");
      } finally {
        setLoading(false);
      }
    };

    void loadData();
    return () => controller.abort();
  }, []);

  return (
    <div className="ph-page">
      <PublicNavbar categories={categories} activeCategorySlug="recientes" />

      <header className="recent-banner">
        <div className="recent-banner-inner">
          <h1 className="recent-banner-title">
            <NewspaperIcon />
            Noticias Recientes
          </h1>
          <p className="recent-banner-subtitle">
            Todas las noticias de los últimos dos meses, ordenadas por fecha
          </p>
        </div>
      </header>

      <main className="recent-main">
        {loading && (
          <div className="ph-loading">
            <div className="ph-spinner" />
            <p className="ph-loading-text">Cargando noticias...</p>
          </div>
        )}

        {!loading && error && <p className="ph-error">{error}</p>}

        {!loading && !error && articles.length === 0 && (
          <p className="ph-empty">No hay noticias recientes para mostrar.</p>
        )}

        {!loading && !error && articles.length > 0 && (
          <div className="recent-list">
            {articles.map((article) => (
              <a key={article.id} className="ph-horiz-card" href={`/articulo/${article.slug || article.id}`}>
                <div className="ph-horiz-image">
                  {article.featuredImageUrl ? (
                    <img src={article.featuredImageUrl} alt={article.title} style={{ objectPosition: article.featuredImagePosition || "center" }} />
                  ) : (
                    <div className="ph-horiz-image-placeholder">
                      {article.categoryName}
                    </div>
                  )}
                </div>
                <div className="ph-horiz-body">
                  <div className="ph-small-category">{article.categoryName || "Noticias"}</div>
                  <h3 className="ph-horiz-title"><FormattedText text={article.title} /></h3>
                  <p className="ph-horiz-excerpt"><FormattedText text={article.excerpt} /></p>
                  <div className="ph-horiz-meta">
                    <span>{formatArticleDate(article.createdAt)}</span>
                    <span>•</span>
                    <span>{formatArticleTime(article.createdAt)}</span>
                    <span>•</span>
                    <span className="ph-meta-author">{article.authorName}</span>
                  </div>
                </div>
              </a>
            ))}
          </div>
        )}
      </main>

      <PublicFooter categories={categories} />
    </div>
  );
};

export default RecentPage;
