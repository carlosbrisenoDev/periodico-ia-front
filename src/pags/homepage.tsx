import { useEffect, useState } from "react";
import { useNavigate, Link } from "react-router-dom";
import "./homepage.css";
import { getHomeData, getLatestPublications, getPublicCategories, getCategoryArticles, apiFetch } from "../libs/http.ts";
import { API_BASE_URL } from "../libs/config.ts";
import type { PublicArticle, PublicCategory } from "../libs/types.ts";

type VideoAsset = {
  id: string;
  url: string;
  platform: string;
  videoExternalId: string;
  title?: string;
};

import PublicFooter from "../components/PublicFooter.tsx";
import PublicNavbar from "../components/PublicNavbar.tsx";

import MobileBottomNav from "../components/MobileBottomNav.tsx";
import { MailIcon, BadgeIcon, PlayIcon } from "../components/Icons.tsx";

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

const sectionBorderMap: Record<string, string> = {
  "Deportes": "ph-section-title-deportes",
  "Cultura": "ph-section-title-cultura",
  "Seguridad": "ph-section-title-seguridad",
  "Comunidad": "ph-section-title-comunidad",
  "Opinión": "ph-section-title-opinion",
  "Noticias": "ph-section-title-noticias",
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
  linkStyle?: "btn" | "text" | "arrow";
  titleClass?: string;
}) => (
  <div className="ph-section-header">
    <h2 className={`ph-section-title ${titleClass || ""}`}>{title}</h2>
    <a className={linkStyle === "btn" ? "ph-section-link-btn" : "ph-section-link"} href={href}>
      {linkStyle === "arrow" ? (
        <>
          <span className="hide-mobile">{linkLabel || "VER TODAS"}</span>
          <span className="ph-arrow-icon">&gt;</span>
        </>
      ) : (
        linkLabel || "VER TODAS >"
      )}
    </a>
  </div>
);





/* ── Main HomePage ───────────────────────────────────── */

const HomePage = () => {
  const navigate = useNavigate();
  const [articles, setArticles] = useState<PublicArticle[]>([]);
  const [featuredArticles, setFeaturedArticles] = useState<PublicArticle[]>([]);
  const [categories, setCategories] = useState<PublicCategory[]>([]);
  const [videos, setVideos] = useState<VideoAsset[]>([]);
  const [categoryArticlesMap, setCategoryArticlesMap] = useState<Record<string, PublicArticle[]>>({});
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [settings, setSettings] = useState<any>(null);

  useEffect(() => {
    const controller = new AbortController();

    const load = async () => {
      try {
        setLoading(true);

        const [homeData, fetchedCats, settingsData, videosRes] = await Promise.all([
          getHomeData(controller.signal),
          getPublicCategories(controller.signal),
          apiFetch<any>(`https://api.informaciondealtura.com/api/v1/settings`, { signal: controller.signal }).catch(() => ({})),
          fetch(`${API_BASE_URL}/api/v1/public/videos?limit=5`, { signal: controller.signal }).catch(() => null)
        ]);

        if (videosRes && videosRes.ok) {
          const vData = await videosRes.json();
          setVideos(Array.isArray(vData) ? vData : []);
        }

        setCategories(fetchedCats);
        setFeaturedArticles(homeData.featured);
        setSettings(settingsData);

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
    if (headlines.length >= 3) return headlines.slice(0, 3);
    
    // Fallback: use any featured that isn't the main, then any recent
    const pool = allKnownArticles.filter(a => a.id !== heroMain?.id);
    return pool.slice(0, 3);
  })();

  const latestThree = articles.slice(0, 3);

  return (
    <div className="ph-page">
      <PublicNavbar categories={categories} />

      <main className="ph-main-content">
        {/* Top Action Bar for Mobile */}
        <div className="ph-mobile-action-bar">
          <button className="ph-action-btn live" disabled style={{ opacity: 0.5, filter: 'grayscale(1)', cursor: 'not-allowed' }}><span className="red-dot"></span> EN VIVO</button>
          <button className="ph-action-btn outline" onClick={() => navigate("/reportar")}><MailIcon /> ENVÍA TU NOTA</button>
          <button className="ph-action-btn outline" onClick={() => navigate("/suscripcion")}><BadgeIcon /> SUSCRÍBETE</button>
        </div>

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
                    <HorizCard key={a.id} article={a} category={a.categoryName} />
                  ))}
                </div>
              </div>
            </section>

            {/* Tendencias / Últimas Noticias */}
            {latestThree.length > 0 && (
              <div className="ph-section">
                <SectionHeader
                  title="TENDENCIAS"
                  href="/recientes"
                  linkLabel="VER TODAS"
                  linkStyle="arrow"
                  titleClass="ph-section-title-tendencias"
                />
                <div className="ph-list-grid">
                  {latestThree.map((a) => (
                    <HorizCard key={a.id} article={a} category={a.categoryName} />
                  ))}
                </div>
              </div>
            )}

            {/* Las 5 de X */}
            <div id="las-5-de-x" className="ph-las-5-section">
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

            {/* Print Edition Banner */}
            <div className="ph-print-banner">
              {settings?.printEditionImageUrl ? (
                <a href={settings.printEditionLink || "#"} target="_blank" rel="noopener noreferrer" style={{ display: 'block', width: '100%' }}>
                  <img src={settings.printEditionImageUrl} alt="Edición Impresa" style={{ width: '100%', height: 'auto', display: 'block' }} />
                </a>
              ) : (
                <div className="ph-print-banner-inner">
                  <img src="/logo.png" alt="" className="ph-print-logo" style={{width: 200, height: 200, objectFit: 'contain'}} />
                  <div className="ph-print-text">
                    <h3>BUSCA LA EDICIÓN IMPRESA</h3>
                    <p>MARTES Y VIERNES</p>
                  </div>
                </div>
              )}
            </div>

            {/* Dynamic Categories */}
            {[...categories].sort((a, b) => (a.order || 0) - (b.order || 0)).map((catObj) => {
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
              const titleClass = sectionBorderMap[title] || "";

              // On mobile, render as a simple horizontal list for "Estado", "Córdoba", "Análisis y Opinión" etc.
              // For "Investigación Especial" we want a dark hero. We will simulate that based on the title.
              return (
                <div key={slug} className={`ph-section ${title.toUpperCase().includes("INVESTIGACIÓN") ? "ph-investigacion-section" : ""}`}>
                  <SectionHeader title={title.toUpperCase()} href={`/categoria/${slug}`} linkLabel="VER TODAS" linkStyle="arrow" titleClass={titleClass} />
                  <div className="ph-category-content">
                    {catArticles[0] && (
                      <FeaturedCard article={catArticles[0]} category={title} />
                    )}
                    <div className="ph-list-grid" style={{ marginTop: '16px', gridTemplateColumns: '1fr' }}>
                      {catArticles.slice(1, 3).map((a) => (
                        <HorizCard key={a.id} article={a} category={title} />
                      ))}
                    </div>
                  </div>
                </div>
              );
            })}

            {/* Videografía Block */}
            <div className="ph-videografia-section">
              <SectionHeader title="VIDEOGRAFÍA" href="/videoteca" linkLabel="VER TODOS" linkStyle="arrow" />
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
                        <Link to={`/video/${videos[0].id}`} state={{ video: videos[0] }}><PlayIcon /></Link>
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
                          <Link to={`/video/${video.id}`} state={{ video }} style={{ display: 'block', width: '100%', height: '100%' }}>
                            {video.platform === 'youtube' ? (
                              <img src={`https://img.youtube.com/vi/${video.videoExternalId}/mqdefault.jpg`} style={{ width: '100%', height: '100%', objectFit: 'cover' }} alt="thumbnail" />
                            ) : (
                              <PlayIcon />
                            )}
                          </Link>
                        </div>
                        <div className="ph-video-info" style={{ display: 'flex', alignItems: 'center' }}>
                          <Link to={`/video/${video.id}`} state={{ video }} style={{ textDecoration: 'none' }}>
                            <span>Ver video</span>
                          </Link>
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

            {/* Newsletter Subscription */}
            <div className="ph-newsletter-section">
              <div className="ph-newsletter-icon"><MailIcon /></div>
              <h3>NEWSLETTER</h3>
              <p>Recibe las noticias más importantes en tu correo cada mañana.</p>
              <form className="ph-newsletter-form">
                <input type="email" placeholder="Tu correo electrónico" required />
                <button type="submit">SUSCRÍBEME</button>
              </form>
            </div>
          </>
        )}
      </main>

      <PublicFooter categories={categories} />
      <MobileBottomNav />
    </div>
  );
};

export default HomePage;
