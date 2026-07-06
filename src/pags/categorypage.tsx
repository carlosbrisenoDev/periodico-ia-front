import { useEffect, useState, useMemo } from "react";
import { useParams } from "react-router-dom";
import "./categorypage.css";
import { API_BASE_URL } from "../libs/config.ts";
import { apiFetch, getPublicCategories } from "../libs/http.ts";
import type { PublicArticle, PublicCategory } from "../libs/types.ts";
import PublicFooter from "../components/PublicFooter.tsx";
import PublicNavbar from "../components/PublicNavbar.tsx";
import { PlayIcon } from "../components/Icons.tsx";

type VideoAsset = {
  id: string;
  url: string;
  platform: string;
  videoExternalId: string;
  title?: string;
};

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
    featuredType:
      r.featuredType === "hero" ||
      r.featuredType === "headline" ||
      r.featuredType === "category_hero" ||
      r.featuredType === "breaking"
        ? (r.featuredType as any)
        : "none",
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


/* ── Card Components ─────────────────────────────────── */

const FeaturedCard = ({ article, category }: { article: PublicArticle; category: string }) => (
  <a className="pc-featured-card" href={articleHref(article)}>
    <div className="pc-featured-image">
      {article.featuredImageUrl ? (
        <img src={article.featuredImageUrl} alt={article.title} />
      ) : (
        <div className="pc-image-placeholder">{category}</div>
      )}
    </div>
    <div className="pc-featured-body">
      <span className="pc-badge" style={{ color: "var(--category-color, inherit)", borderColor: "var(--category-color, inherit)" }}>{category}</span>
      <h2 className="pc-featured-title">{article.title}</h2>

      <p className="pc-featured-excerpt">{article.excerpt}</p>
      <div className="pc-meta">
        <span>{formatArticleDate(article.createdAt)}</span>
        <span>•</span>
        <span>{formatArticleTime(article.createdAt)}</span>
        <span>•</span>
        <span className="pc-meta-author">{article.authorName}</span>
      </div>
    </div>
  </a>
);

const SmallCard = ({ article, category }: { article: PublicArticle; category: string }) => (
  <a className="pc-small-card" href={articleHref(article)}>
    <div className="pc-small-image">
      {article.featuredImageUrl ? (
        <img src={article.featuredImageUrl} alt={article.title} />
      ) : (
        <div className="pc-image-placeholder">{category}</div>
      )}
    </div>
    <div className="pc-small-body">
      <div className="pc-small-category" style={{ color: "var(--category-color, inherit)" }}>{category}</div>
      <h3 className="pc-small-title">{article.title}</h3>

      <div className="pc-meta">
        <span>{formatArticleDate(article.createdAt)}</span>
        <span>•</span>
        <span>{formatArticleTime(article.createdAt)}</span>
      </div>
    </div>
  </a>
);

const GridCard = ({ article, category }: { article: PublicArticle; category: string }) => (
  <a className="pc-grid-card" href={articleHref(article)}>
    <div className="pc-grid-image">
      {article.featuredImageUrl ? (
        <img src={article.featuredImageUrl} alt={article.title} />
      ) : (
        <div className="pc-image-placeholder">{category}</div>
      )}
    </div>
    <div className="pc-grid-body">
      <div className="pc-small-category" style={{ color: "var(--category-color, inherit)" }}>{category}</div>
      <h3 className="pc-grid-title">{article.title}</h3>
      <div className="pc-meta">
        <span>{formatArticleDate(article.createdAt)}</span>
        <span>•</span>
        <span>{formatArticleTime(article.createdAt)}</span>
      </div>
    </div>
  </a>
);

const MagazineCard = ({ article, category, reverse }: { article: PublicArticle; category: string; reverse?: boolean }) => (
  <a className={`pc-magazine-card ${reverse ? "pc-magazine-card-reverse" : ""}`} href={articleHref(article)}>
    <div className="pc-magazine-image">
      {article.featuredImageUrl ? (
        <img src={article.featuredImageUrl} alt={article.title} />
      ) : (
        <div className="pc-image-placeholder">{category}</div>
      )}
    </div>
    <div className="pc-magazine-body">
      <div className="pc-small-category" style={{ color: "var(--category-color, inherit)" }}>{category}</div>
      <h2 className="pc-magazine-title">{article.title}</h2>
      <p className="pc-magazine-excerpt">{article.excerpt}</p>
      <div className="pc-meta">
        <span>{formatArticleDate(article.createdAt)}</span>
        <span>•</span>
        <span>{formatArticleTime(article.createdAt)}</span>
        <span>•</span>
        <span className="pc-meta-author">{article.authorName}</span>
      </div>
    </div>
  </a>
);

const ListCard = ({ article, category }: { article: PublicArticle; category: string }) => (
  <a className="pc-list-card" href={articleHref(article)}>
    <div className="pc-list-image">
      {article.featuredImageUrl ? (
        <img src={article.featuredImageUrl} alt={article.title} />
      ) : (
        <div className="pc-image-placeholder">{category}</div>
      )}
    </div>
    <div className="pc-list-body">
      <div className="pc-small-category" style={{ color: "var(--category-color, inherit)" }}>{category}</div>
      <h3 className="pc-list-title">{article.title}</h3>
      <p className="pc-list-excerpt">{article.excerpt}</p>
      <div className="pc-meta">
        <span>{formatArticleDate(article.createdAt)}</span>
        <span>•</span>
        <span className="pc-meta-author">{article.authorName}</span>
      </div>
    </div>
  </a>
);

/* ── Main Component ──────────────────────────────────── */

const CategoryPage = () => {
  const { id } = useParams<{ id: string }>();
  const capitalizedId = id ? id.charAt(0).toUpperCase() + id.slice(1) : "";
  const [categoryName, setCategoryName] = useState(capitalizedId);
  const [template, setTemplate] = useState("default");
  const [color, setColor] = useState<string | undefined>();
  const [articles, setArticles] = useState<PublicArticle[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [categories, setCategories] = useState<PublicCategory[]>([]);
  const [videos, setVideos] = useState<VideoAsset[]>([]);


  useEffect(() => {
    if (!id) return;

    const controller = new AbortController();

    const load = async () => {
      try {
        setLoading(true);
        setError(null);
        
        const [categoryData, fetchedCats, videosRes] = await Promise.all([
          fetchCategoryArticles(id, controller.signal),
          getPublicCategories(controller.signal),
          fetch(`${API_BASE_URL}/api/v1/public/videos?limit=5`, { signal: controller.signal }).catch(() => null)
        ]);

        setCategoryName(categoryData.name);
        setArticles(categoryData.articles);
        if ('template' in categoryData) {
          setTemplate(categoryData.template as string);
        }
        if ('color' in categoryData) {
          setColor(categoryData.color as string | undefined);
        }
        setCategories(fetchedCats);
        if (videosRes && videosRes.ok) {
          const vData = await videosRes.json();
          setVideos(Array.isArray(vData) ? vData : []);
        }

      } catch (err: unknown) {
        if (err instanceof Error && err.name === "AbortError") return;
        setError("No se pudieron cargar las publicaciones de esta categoría.");
      } finally {
        setLoading(false);
      }
    };

    void load();
    return () => controller.abort();
  }, [id]);

  const featured = useMemo(() => {
    if (articles.length === 0) return null;

    // 1. Prioritize category_hero (Category-specific featured)
    const catHero = articles.find(a => a.featuredType === 'category_hero');
    if (catHero) return catHero;

    // 2. Fallback to global hero if it exists in this category
    const globalHero = articles.find(a => a.featuredType === 'hero');
    if (globalHero) return globalHero;

    // 3. Fallback to a global headline
    const headline = articles.find(a => a.featuredType === 'headline');
    if (headline) return headline;

    // 4. Prefer a non-breaking article for the main slot to leave breaking for side cards
    const firstNonBreaking = articles.find(a => a.featuredType === 'none');
    if (firstNonBreaking) return firstNonBreaking;

    // 5. Ultimate fallback
    return articles[0];
  }, [articles]);

  const sideCards = useMemo(() => {
    if (!featured) return [];

    const fId = featured.id;
    // 1. Prioritize articles specifically marked for category side slots (breaking)
    const breaking = articles.filter(a => a.featuredType === 'breaking' && a.id !== fId);
    
    // 2. Then headlines that weren't picked for the main slot
    const headlines = articles.filter(a => a.featuredType === 'headline' && a.id !== fId);
    
    // 3. Then any other articles
    const others = articles.filter(a => 
      a.id !== fId && 
      a.featuredType !== 'breaking' && 
      a.featuredType !== 'headline'
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
          <>
            {template === 'magazine' ? (
              <section className="pc-section pc-section-magazine">
                <div className="pc-magazine-list">
                  {articles.map((a, idx) => (
                    <MagazineCard key={a.id} article={a} category={id === "noticias" ? a.categoryName : categoryName} reverse={idx % 2 !== 0} />
                  ))}
                </div>
              </section>
            ) : template === 'list' ? (
              <section className="pc-section pc-section-list">
                <div className="pc-list-vertical">
                  {articles.map((a) => (
                    <ListCard key={a.id} article={a} category={id === "noticias" ? a.categoryName : categoryName} />
                  ))}
                </div>
              </section>
            ) : template === 'hero-grid' ? (
              <>
                {/* Destacadas (Hero Grid) */}
                <section className="pc-section">
                  <div className="pc-section-header">
                    <h2 className="pc-section-title">Destacadas</h2>
                  </div>

                  <div className="pc-featured-grid">
                    {featured && (
                      <FeaturedCard article={featured} category={id === "noticias" ? featured.categoryName : categoryName} />
                    )}
                    {sideCards.length > 0 && (
                      <div className="pc-side-stack">
                        {sideCards.map((a) => (
                          <SmallCard key={a.id} article={a} category={id === "noticias" ? a.categoryName : categoryName} />
                        ))}
                      </div>
                    )}
                  </div>
                </section>

                {/* Todas las publicaciones */}
                {allCards.length > 0 && (
                  <section className="pc-section pc-section-border">
                    <div className="pc-section-header">
                      <h2 className="pc-section-title">Todas las publicaciones de {categoryName}</h2>
                    </div>
                    <div className="pc-all-grid">
                      {allCards.map((a) => (
                        <GridCard key={a.id} article={a} category={id === "noticias" ? a.categoryName : categoryName} />
                      ))}
                    </div>
                  </section>
                )}
              </>
            ) : (
              <section className="pc-section">
                <div className="pc-section-header">
                  <h2 className="pc-section-title">Publicaciones Recientes</h2>
                </div>
                <div className="pc-all-grid">
                  {articles.map((a) => (
                    <GridCard key={a.id} article={a} category={id === "noticias" ? a.categoryName : categoryName} />
                  ))}
                </div>
              </section>
            )}
          </>
        )}

        {/* Conditional Xalapa / Veracruz sections */}
        {categoryName.toLowerCase().includes("xalapa") && (
          <div className="ph-videografia-section" style={{ marginTop: '2rem' }}>
            <div className="pc-section-header">
              <h2 className="pc-section-title">VIDEOGRAFÍA</h2>
              <a className="ph-section-link" href="/videoteca">VER TODOS &gt;</a>
            </div>
            <div className="ph-video-container">
              <div className="ph-video-main" style={{ display: 'flex', flexDirection: 'column', gap: '12px' }}>
                <h3 style={{ margin: 0, fontSize: '1.25rem' }}>
                  {videos.length > 0 ? (videos[0].title || "Video") : "Información de Altura en Video"}
                </h3>
                <div className="ph-video-thumbnail" style={{ padding: 0, overflow: 'hidden' }}>
                  {videos.length > 0 ? (
                    videos[0].platform === 'youtube' ? (
                      <iframe
                        src={`https://www.youtube.com/embed/${videos[0].videoExternalId}`}
                        style={{ width: "100%", height: "100%", border: 0 }}
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowFullScreen
                        title={videos[0].title || "Video"}
                      />
                    ) : (
                      <a href={videos[0].url} target="_blank" rel="noopener noreferrer"><PlayIcon /></a>
                    )
                  ) : (
                    <PlayIcon />
                  )}
                </div>
              </div>
              <div className="ph-video-list">
                {videos.length > 0 ? videos.slice(1).map((video) => (
                  <div key={video.id} className="ph-video-item" style={{ flexDirection: 'column', alignItems: 'flex-start' }}>
                    <div className="ph-video-info" style={{ width: '100%' }}>
                      <h4 style={{ marginBottom: '8px' }}>{video.title || "Video"}</h4>
                    </div>
                    <div style={{ display: 'flex', width: '100%', gap: '12px' }}>
                      <div className="ph-video-thumb-small" style={{ overflow: 'hidden' }}>
                        <a href={video.platform === 'youtube' ? `https://youtube.com/watch?v=${video.videoExternalId}` : video.url} target="_blank" rel="noopener noreferrer" style={{ display: 'block', width: '100%', height: '100%' }}>
                          {video.platform === 'youtube' ? (
                            <img src={`https://img.youtube.com/vi/${video.videoExternalId}/mqdefault.jpg`} style={{ width: '100%', height: '100%', objectFit: 'cover' }} alt="thumbnail" />
                          ) : (
                            <PlayIcon />
                          )}
                        </a>
                      </div>
                      <div className="ph-video-info" style={{ display: 'flex', alignItems: 'center' }}>
                        <a href={video.platform === 'youtube' ? `https://youtube.com/watch?v=${video.videoExternalId}` : video.url} target="_blank" rel="noopener noreferrer" style={{ textDecoration: 'none' }}>
                          <span>Ver en {video.platform}</span>
                        </a>
                      </div>
                    </div>
                  </div>
                )) : [1, 2, 3].map((i) => (
                  <div key={i} className="ph-video-item" style={{ flexDirection: 'column', alignItems: 'flex-start' }}>
                    <div className="ph-video-info" style={{ width: '100%' }}>
                      <h4 style={{ marginBottom: '8px' }}>Cargando...</h4>
                    </div>
                    <div style={{ display: 'flex', width: '100%', gap: '12px' }}>
                      <div className="ph-video-thumb-small"><PlayIcon /></div>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </div>
        )}

        {categoryName.toLowerCase().includes("veracruz") && (
          <div id="las-5-de-x" className="ph-las-5-section" style={{ marginTop: '2rem' }}>
            <div className="ph-las-5-header">
              <h2>LAS 5 DE X</h2>
              <p>Lo más importante en 1 minuto</p>
              <a href="/recientes" className="ph-las-5-btn">Ver resumen completo &rarr;</a>
            </div>
            <div className="ph-las-5-list">
              {articles.slice(0, 5).map((a, i) => (
                <a key={a.id} href={articleHref(a)} className="ph-las-5-item">
                  <div className="ph-las-5-number">{i + 1}</div>
                  <div className="ph-las-5-img">
                    {a.featuredImageUrl ? <img src={a.featuredImageUrl} alt="" /> : <div className="placeholder" />}
                  </div>
                  <div className="ph-las-5-text">{a.title}</div>
                </a>
              ))}
            </div>
          </div>
        )}

      </main>

      <PublicFooter categories={categories} />
    </div>
  );
};

export default CategoryPage;
