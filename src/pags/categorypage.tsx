import { useEffect, useState, useMemo } from "react";
import { useParams } from "react-router-dom";
import "./categorypage.css";
import { API_BASE_URL } from "../libs/config.ts";
import { apiFetch, getPublicCategories } from "../libs/http.ts";
import type { PublicArticle, PublicCategory } from "../libs/types.ts";
import PublicFooter from "../components/PublicFooter.tsx";
import PublicNavbar from "../components/PublicNavbar.tsx";
/* ── helpers ─────────────────────────────────────────── */






const articleHref = (a: PublicArticle) => `/articulo/${a.slug || a.id}`;

/* ── Data Fetching ───────────────────────────────────── */

type CategoryPageResponse = {
  category?: { id?: string; name?: string; slug?: string; template?: string; color?: string };
  articles?: unknown[];
};

const normalizePublicArticle = (item: unknown, index: number): PublicArticle | null => {
  if (!item || typeof item !== "object") return null;
  const r = item as Record<string, unknown>;
  const title = typeof r.title === "string" ? r.title : null;
  if (!title) return null;

  const author =
    r.author && typeof r.author === "object" ? (r.author as Record<string, unknown>) : null;
  const categories = Array.isArray(r.categories) ? r.categories : [];
  const firstCat =
    categories.length > 0 && categories[0] && typeof categories[0] === "object"
      ? (categories[0] as Record<string, unknown>)
      : null;

  return {
    id: typeof r.id === "string" ? r.id : `cat-${index}`,
    title,
    slug: typeof r.slug === "string" ? r.slug : `article-${index}`,
    excerpt: typeof r.excerpt === "string" ? r.excerpt : "",
    featuredImageUrl: typeof r.featuredImageUrl === "string" ? r.featuredImageUrl : undefined,
    createdAt:
      typeof r.publishedAt === "string"
        ? r.publishedAt
        : typeof r.createdAt === "string"
          ? r.createdAt
          : new Date().toISOString(),
    authorName:
      typeof r.authorName === "string"
        ? r.authorName
        : author && typeof author.name === "string"
          ? author.name
          : "Redacción",
    categoryName:
      typeof r.categoryName === "string"
        ? r.categoryName
        : firstCat && typeof firstCat.name === "string"
          ? firstCat.name
          : "General",
    isFeatured: typeof r.isFeatured === "boolean" ? r.isFeatured : false,
    featuredTypes: Array.isArray(r.featuredTypes) ? r.featuredTypes : [],
  };
};

const fetchCategoryArticles = async (
  id: string,
  signal?: AbortSignal,
): Promise<{ name: string; template?: string; color?: string; articles: PublicArticle[] }> => {
  /* "Noticias" is not a real category — it shows ALL articles */
  if (id === "noticias") {
    const home = await apiFetch<{ recent?: unknown[]; featured?: unknown[]; latest?: unknown[] }>(
      `${API_BASE_URL}/api/v1/public/home`,
      { method: "GET", signal },
    );

    const rawFeatured = Array.isArray(home.featured) ? home.featured : [];
    const rawRecent = Array.isArray(home.recent) ? home.recent : [];
    const raw = [...rawFeatured, ...rawRecent];
    const seen = new Set<string>();
    const articles: PublicArticle[] = [];

    for (let i = 0; i < raw.length; i++) {
      const a = normalizePublicArticle(raw[i], i);
      if (a && !seen.has(a.id)) {
        seen.add(a.id);
        articles.push(a);
      }
    }

    return { name: "Noticias", articles };
  }

  const data = await apiFetch<CategoryPageResponse>(
    `${API_BASE_URL}/api/v1/public/category/${id}`,
    { method: "GET", signal },
  );

  const name =
    data.category && typeof data.category.name === "string" ? data.category.name : id;

  const template =
    data.category && typeof data.category.template === "string" ? data.category.template : "default";

  const color =
    data.category && typeof data.category.color === "string" ? data.category.color : undefined;

  const articles = Array.isArray(data.articles)
    ? (data.articles
        .map((item, i) => normalizePublicArticle(item, i))
        .filter((a): a is PublicArticle => a !== null))
    : [];

  return { name, template, color, articles };
};




/* ── Main Component ──────────────────────────────────── */

const CategoryPage = () => {
  const { id } = useParams<{ id: string }>();
  const capitalizedId = id ? id.charAt(0).toUpperCase() + id.slice(1) : "";
  const [categoryName, setCategoryName] = useState(capitalizedId);
  const [color, setColor] = useState<string | undefined>();
  const [articles, setArticles] = useState<PublicArticle[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [categories, setCategories] = useState<PublicCategory[]>([]);
  const [visibleCount, setVisibleCount] = useState(5);


  useEffect(() => {
    if (!id) return;
    
    const controller = new AbortController();

    const load = async () => {
      try {
        setLoading(true);
        setError(null);
        
        const [categoryData, fetchedCats] = await Promise.all([
          fetchCategoryArticles(id, controller.signal),
          getPublicCategories(controller.signal),
        ]);

        setCategoryName(categoryData.name);
        setArticles(categoryData.articles);

        if ('color' in categoryData) {
          setColor(categoryData.color as string | undefined);
        }
        setCategories(fetchedCats);
        

      } catch (err: unknown) {
        if (err instanceof Error && err.name === "AbortError") return;
        setError("No se pudieron cargar las publicaciones de esta categoría.");
      } finally {
        setLoading(false);
      }
    };

    void load();
    setVisibleCount(5);
    return () => controller.abort();
  }, [id]);

  const featured = useMemo(() => {
    if (articles.length === 0) return null;

    // 1. Prioritize category_hero (Category-specific featured)
    const catHero = articles.find(a => (a.featuredTypes || []).includes('category_hero'));
    if (catHero) return catHero;

    // 2. Fallback to global hero if it exists in this category
    const globalHero = articles.find(a => (a.featuredTypes || []).includes('hero'));
    if (globalHero) return globalHero;

    // 3. Fallback to a global headline
    const headline = articles.find(a => (a.featuredTypes || []).includes('headline'));
    if (headline) return headline;

    // 4. Prefer a non-breaking article for the main slot to leave breaking for side cards
    const firstNonBreaking = articles.find(a => (!a.featuredTypes || a.featuredTypes.length === 0));
    if (firstNonBreaking) return firstNonBreaking;

    // 5. Ultimate fallback
    return articles[0];
  }, [articles]);

  const sideCards = useMemo(() => {
    if (!featured) return [];

    const fId = featured.id;
    // 1. Prioritize articles specifically marked for category side slots (breaking)
    const breaking = articles.filter(a => (a.featuredTypes || []).includes('breaking') && a.id !== fId);
    
    // 2. Then headlines that weren't picked for the main slot
    const headlines = articles.filter(a => (a.featuredTypes || []).includes('headline') && a.id !== fId);
    
    // 3. Then any other articles
    const others = articles.filter(a => 
      a.id !== fId && 
      !(a.featuredTypes || []).includes('breaking') && 
      !(a.featuredTypes || []).includes('headline')
    );

    const combined = [...breaking, ...headlines, ...others];
    return combined.slice(0, 2);
  }, [articles, featured]);

  const allCards = useMemo(() => {
    const fId = featured?.id;
    const sIds = new Set(sideCards.map(s => s.id));
    return articles.filter(a => a.id !== fId && !sIds.has(a.id));
  }, [articles, featured, sideCards]);



  return (
    <div className="ph-page" style={color ? { "--category-color": color } as React.CSSProperties : {}}>
      <PublicNavbar 
        categories={categories} 
        activeCategorySlug={id} 
      />

      {/* ── Category Banner ───────────────────────── */}
      <section className="pc-banner" style={color ? { backgroundColor: color, color: "#fff" } : {}}>
        <h1 className="pc-banner-title">{categoryName || id}</h1>
      </section>


      {/* ── Main Content ──────────────────────────── */}
      <main className="pc-main">
        {loading && (
          <div className="ph-loading">
            <div className="ph-spinner" />
            <p className="ph-loading-text">Cargando contenido...</p>
          </div>
        )}

        {!loading && error && <p className="ph-error">{error}</p>}

        {!loading && !error && articles.length === 0 && (
          <p className="ph-empty">Aún no hay publicaciones en esta categoría.</p>
        )}

        {!loading && !error && articles.length > 0 && (
          <div className="pc-new-layout">
            {featured && (
              <div className="pc-main-article">
                <a href={articleHref(featured)} className="pc-main-article-link">
                  <div className="pc-main-image">
                    {featured.featuredImageUrl ? (
                      <img src={featured.featuredImageUrl} alt={featured.title} />
                    ) : (
                      <div className="pc-image-placeholder">{id === "noticias" ? featured.categoryName : categoryName}</div>
                    )}
                  </div>
                  <div className="pc-main-content">
                    <h2 className="pc-main-title">{featured.title}</h2>
                    <p className="pc-main-excerpt">{featured.excerpt}</p>
                  </div>
                </a>
              </div>
            )}

            <div className="pc-secondary-list">
              {allCards.slice(0, visibleCount).map((a) => (
                <a key={a.id} href={articleHref(a)} className="pc-secondary-card">
                  <div className="pc-secondary-image">
                    {a.featuredImageUrl ? (
                      <img src={a.featuredImageUrl} alt={a.title} />
                    ) : (
                      <div className="pc-image-placeholder">{a.categoryName}</div>
                    )}
                  </div>
                  <div className="pc-secondary-content">
                    <span className="pc-secondary-tag">{a.categoryName}</span>
                    <h3 className="pc-secondary-title">{a.title}</h3>
                    <p className="pc-secondary-excerpt">{a.excerpt}</p>
                  </div>
                </a>
              ))}
            </div>

            {visibleCount < allCards.length && (
              <div className="pc-load-more-container">
                <button 
                  className="pc-load-more-btn" 
                  onClick={() => setVisibleCount((prev) => prev + 5)}
                >
                  Seguir leyendo
                </button>
              </div>
            )}
          </div>
        )}

      </main>

      <PublicFooter categories={categories} />
    </div>
  );
};

export default CategoryPage;
