import { useEffect, useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import { getHomeData, getLatestPublications, getPublicCategories, getCategoryArticles } from "../libs/http.ts";
import type { PublicArticle, PublicCategory } from "../libs/types.ts";
import PublicFooter from "../components/PublicFooter.tsx";
import PublicNavbar from "../components/PublicNavbar.tsx";
import { AdBlock } from "../components/AdBlock.tsx";

/* ── helpers ─────────────────────────────────────────── */


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

const articleHref = (a: PublicArticle) => `/articulo/${a.id}`;

const slugify = (text: string) =>
  text
    .toLowerCase()
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .replace(/[^a-z0-9]+/g, "-")
    .replace(/(^-|-$)+/g, "");

const groupByCategory = (articles: PublicArticle[]) => {
  const map: Record<string, PublicArticle[]> = {};
  for (const a of articles) {
    const cat = (a.categoryName || "General").toLowerCase().trim();
    if (!map[cat]) map[cat] = [];
    map[cat].push(a);
  }
  return map;
};

/* ── Card Components ─────────────────────────────────── */

const FeaturedCard = ({ article, category }: { article: PublicArticle; category: string }) => (
  <a className="ph-featured-card" href={articleHref(article)}>
    <div className="ph-featured-image">
      {article.featuredImageUrl ? (
        <img src={article.featuredImageUrl} alt={article.title} />
      ) : (
        <div className="ph-image-placeholder">{category}</div>
      )}
    </div>
    <div className="ph-featured-body">
      <span className="ph-badge">{category}</span>
      <h2 className="ph-featured-title">{article.title}</h2>
      <p className="ph-featured-excerpt">{article.excerpt}</p>
      <div className="ph-meta">
        <span>{formatArticleDate(article.createdAt)}</span>
        <span>•</span>
        <span>{formatArticleTime(article.createdAt)}</span>
        <span>•</span>
        <span className="ph-meta-author">{article.authorName}</span>
      </div>
    </div>
  </a>
);

const SmallCard = ({ article, category }: { article: PublicArticle; category: string }) => (
  <a className="ph-small-card" href={articleHref(article)}>
    <div className="ph-small-image">
      {article.featuredImageUrl ? (
        <img src={article.featuredImageUrl} alt={article.title} />
      ) : (
        <div className="ph-image-placeholder">{category}</div>
      )}
    </div>
    <div className="ph-small-body">
      <div className="ph-small-category">{category}</div>
      <h3 className="ph-small-title">{article.title}</h3>
      <div className="ph-meta">
        <span>{formatArticleDate(article.createdAt)}</span>
        <span>•</span>
        <span>{formatArticleTime(article.createdAt)}</span>
      </div>
    </div>
  </a>
);

const HorizCard = ({ article, category }: { article: PublicArticle; category: string }) => (
  <a className="ph-horiz-card" href={articleHref(article)}>
    <div className="ph-horiz-image">
      {article.featuredImageUrl ? (
        <img src={article.featuredImageUrl} alt={article.title} />
      ) : (
        <div className="ph-image-placeholder">{category}</div>
      )}
    </div>
    <div className="ph-horiz-body">
      <div className="ph-small-category">{category}</div>
      <h3 className="ph-horiz-title">{article.title}</h3>
      <div className="ph-meta">
        <span>{formatArticleDate(article.createdAt)}</span>
        <span>•</span>
        <span>{formatArticleTime(article.createdAt)}</span>
        <span>•</span>
        <span className="ph-meta-author">{article.authorName}</span>
      </div>
    </div>
  </a>
);

/* ── Section Components ──────────────────────────────── */

const SectionHeader = ({
  title,
  href,
  linkLabel,
  linkStyle,
  titleClass,
}: {
  title: string;
  href: string;
  linkLabel?: string;
  linkStyle?: "btn" | "text";
  titleClass?: string;
}) => (
  <div className="ph-section-header">
    <h2 className={`ph-section-title ${titleClass || ""}`}>{title}</h2>
    <a className={linkStyle === "btn" ? "ph-section-link-btn" : "ph-section-link"} href={href}>
      {linkLabel || "Ver más →"}
    </a>
  </div>
);

/** Layout A: 2 small cards left + 1 featured card right (Deportes, Seguridad, Opinión) */
const CategorySectionA = ({
  title,
  categoryId,
  articles,
  grey,
}: {
  title: string;
  categoryId: string;
  articles: PublicArticle[];
  grey: boolean;
}) => {
  if (articles.length === 0) return null;
  const featured = articles.find(a => a.featuredType === 'category_hero' || a.featuredType === 'hero') || articles[0];
  const sides = (() => {
    const breaking = articles.filter(a => (a.featuredType === 'breaking' || a.featuredType === 'headline') && a.id !== featured.id);
    if (breaking.length >= 2) return breaking.slice(0, 2);
    return articles.filter(a => a.id !== featured.id).slice(0, 2);
  })();


  const content = (
    <div className="ph-section">
      <SectionHeader
        title={title}
        href={`/categoria/${categoryId}`}
        titleClass={`ph-section-title-${slugify(title)}`}
      />
      <div className="ph-cat-a-grid">
        <div className="ph-cat-a-left">
          {sides.map((a) => (
            <SmallCard key={a.id} article={a} category={title} />
          ))}
        </div>
        <FeaturedCard article={featured} category={title} />
      </div>
    </div>
  );

  return grey ? <div className="ph-section-grey">{content}</div> : content;
};

/** Layout B: 1 featured left + 2 horizontal cards right (Cultura, Comunidad) */
const CategorySectionB = ({
  title,
  articles,
  categoryId,
  grey,
}: {
  title: string;
  categoryId: string;
  articles: PublicArticle[];
  grey: boolean;
}) => {
  if (articles.length === 0) return null;
  const featured = articles.find(a => a.featuredType === 'category_hero' || a.featuredType === 'hero') || articles[0];
  const sides = (() => {
    const breaking = articles.filter(a => (a.featuredType === 'breaking' || a.featuredType === 'headline') && a.id !== featured.id);
    if (breaking.length >= 2) return breaking.slice(0, 2);
    return articles.filter(a => a.id !== featured.id).slice(0, 2);
  })();


  const content = (
    <div className="ph-section">
      <SectionHeader
        title={title}
        href={`/categoria/${categoryId}`}
        titleClass={`ph-section-title-${slugify(title)}`}
      />
      <div className="ph-cat-b-grid">
        <FeaturedCard article={featured} category={title} />
        <div className="ph-cat-b-right">
          {sides.map((a) => (
            <HorizCard key={a.id} article={a} category={title} />
          ))}
        </div>
      </div>
    </div>
  );

  return grey ? <div className="ph-section-grey">{content}</div> : content;
};

/* ── Main HomePage ───────────────────────────────────── */

const HomePage = () => {
  const navigate = useNavigate();
  const [articles, setArticles] = useState<PublicArticle[]>([]);
  const [featuredArticles, setFeaturedArticles] = useState<PublicArticle[]>([]);
  const [categories, setCategories] = useState<PublicCategory[]>([]);
  const [categoryArticlesMap, setCategoryArticlesMap] = useState<Record<string, PublicArticle[]>>({});
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const controller = new AbortController();

    const load = async () => {
      try {
        setLoading(true);

        const [homeData, fetchedCats] = await Promise.all([
          getHomeData(controller.signal),
          getPublicCategories(controller.signal)
        ]);

        setCategories(fetchedCats);
        setFeaturedArticles(homeData.featured);

        const recent = homeData.recent.length > 0 ? homeData.recent : await getLatestPublications(controller.signal);
        setArticles(recent);

        const catPromises = fetchedCats.map(async (cat) => {
          if (!cat.slug && !cat.id) return { key: (cat.name || "").toLowerCase().trim(), articles: [] };
          const slug = cat.slug || cat.id || "";
          const catArts = await getCategoryArticles(slug, controller.signal).catch(() => []);
          return { key: (cat.name || "").toLowerCase().trim(), articles: catArts };
        });
        
        const catResults = await Promise.all(catPromises);
        const newCatMap: Record<string, PublicArticle[]> = {};
        for (const res of catResults) {
          if (res.key) newCatMap[res.key] = res.articles;
        }
        setCategoryArticlesMap(newCatMap);

        setError(null);
      } catch (err: unknown) {
        if (err instanceof Error && err.name === "AbortError") return;
        setArticles([]);
        setError("No se pudieron cargar publicaciones recientes.");
      } finally {
        setLoading(false);
      }
    };

    void load();
    return () => controller.abort();
  }, []);

  // Combine all articles we have for grouping, ensuring featured articles are included in their categories
  const featuredIds = new Set(featuredArticles.map(a => a.id));
  const allKnownArticles = [...featuredArticles, ...articles.filter(a => !featuredIds.has(a.id))];
  const grouped = groupByCategory(allKnownArticles);

  // Hero Selection
  const heroMain = featuredArticles.find(a => a.featuredType === 'hero') 
    || featuredArticles[0] 
    || articles[0] 
    || null;

  const heroSide = (() => {
    const headlines = featuredArticles.filter(a => a.featuredType === 'headline' && a.id !== heroMain?.id);
    if (headlines.length >= 2) return headlines.slice(0, 2);
    
    // Fallback: use any featured that isn't the main, then any recent
    const pool = allKnownArticles.filter(a => a.id !== heroMain?.id);
    return pool.slice(0, 2);
  })();

  const latestThree = articles.slice(0, 3);

  return (
    <div className="ph-page">
      <PublicNavbar categories={categories} />

      <main>
        {loading && (
          <div className="ph-loading">
            <div className="ph-spinner" />
            <p className="ph-loading-text">Cargando contenido...</p>
          </div>
        )}

        {!loading && error && <p className="ph-error">{error}</p>}

        {!loading && !error && articles.length === 0 && (
          <p className="ph-empty">Aún no hay publicaciones disponibles.</p>
        )}

        {!loading && !error && articles.length > 0 && (
          <>
            {/* Hero Section */}
            <section className="ph-hero">
              <div className="ph-hero-grid">
                {heroMain && (
                  <FeaturedCard article={heroMain} category={heroMain.categoryName} />
                )}
                <div className="ph-hero-side">
                  {heroSide.map((a) => (
                    <SmallCard key={a.id} article={a} category={a.categoryName} />
                  ))}
                </div>
              </div>
            </section>

            <AdBlock style={{ margin: "2rem auto", maxWidth: "728px" }} adSlot="HOME_MIDDLE" />

            {/* Últimas Noticias */}
            {latestThree.length > 0 && (
              <div className="ph-section-grey">
                <div className="ph-section">
                  <SectionHeader
                    title="Últimas Noticias"
                    href="/recientes"
                    linkLabel="Ver todas →"
                    linkStyle="btn"
                    titleClass="ph-section-title-noticias"
                  />
                  <div className="ph-latest-grid">
                    {latestThree.map((a) => (
                      <SmallCard key={a.id} article={a} category={a.categoryName} />
                    ))}
                  </div>
                </div>
              </div>
            )}

            {/* Category Sections */}
            {[...categories].sort((a, b) => (a.order || 0) - (b.order || 0)).map((catObj, index) => {
              const catKey = (catObj.name || "").toLowerCase().trim();
              
              const fromGroup = grouped[catKey] || [];
              const fromFetch = categoryArticlesMap[catKey] || [];
              
              const seen = new Set<string>();
              const catArticles: PublicArticle[] = [];
              for (const a of [...fromGroup, ...fromFetch]) {
                if (!seen.has(a.id)) {
                  seen.add(a.id);
                  catArticles.push(a);
                }
              }
              
              if (catArticles.length === 0) return null;
              
              const title = catObj.name || "Categoría";
              const slug = catObj.slug || slugify(title);
              const layout = index % 2 === 0 ? "A" : "B";
              const grey = index % 2 !== 0;

              return layout === "A" ? (
                <CategorySectionA
                  key={catObj.id || slug}
                  title={title}
                  categoryId={slug}
                  articles={catArticles}
                  grey={grey}
                />
              ) : (
                <CategorySectionB
                  key={catObj.id || slug}
                  title={title}
                  categoryId={slug}
                  articles={catArticles}
                  grey={grey}
                />
              );
            })}

            {/* CTA Banner */}
            <section className="ph-cta">
              <div className="ph-cta-inner">
                <h2 className="ph-cta-title">Mantente Informado</h2>
                <p className="ph-cta-text">
                  Recibe <strong>gratis</strong> un resumen claro de lo más importante del día, sin
                  publicidad intrusiva.
                </p>
                <Link className="ph-cta-button" to="/suscripcion">
                  Suscríbete Ahora
                </Link>
              </div>
            </section>

            {/* Citizen Report Banner */}
            <section className="ph-cta" style={{ background: "var(--status-draft-bg)", color: "var(--text-main)", marginTop: "24px" }}>
              <div className="ph-cta-inner">
                <h2 className="ph-cta-title">¿Tienes una Noticia?</h2>
                <p className="ph-cta-text">
                  Si fuiste testigo de algún acontecimiento importante, haz tu <strong>denuncia ciudadana</strong> aquí.
                </p>
                <button 
                  className="ph-cta-button" 
                  onClick={() => navigate("/reportar")}
                  style={{ background: "var(--text-main)", color: "var(--bg-surface)", border: "none", cursor: "pointer", fontSize: "1rem", padding: "12px 24px" }}
                >
                  Reportar Noticia
                </button>
              </div>
            </section>
          </>
        )}
      </main>

      <PublicFooter categories={categories} />
    </div>
  );
};

export default HomePage;
