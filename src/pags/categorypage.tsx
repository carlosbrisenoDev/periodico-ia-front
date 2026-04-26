import { useEffect, useState } from "react";
import { Link, useParams } from "react-router-dom";
import { API_BASE_URL } from "../libs/config.ts";
import { apiFetch } from "../libs/http.ts";
import type { PublicArticle } from "../libs/types.ts";
import logoSrc from "../assets/logo.png";

/* ── helpers ─────────────────────────────────────────── */

const NAV_CATEGORIES = [
  { label: "Noticias", slug: "noticias" },
  { label: "Seguridad", slug: "seguridad" },
  { label: "Deportes", slug: "deportes" },
  { label: "Cultura", slug: "cultura" },
  { label: "Comunidad", slug: "comunidad" },
  { label: "Opinión", slug: "opinion" },
];

const formatFullDate = (): string =>
  new Intl.DateTimeFormat("es-ES", {
    weekday: "long",
    day: "numeric",
    month: "long",
    year: "numeric",
  })
    .format(new Date())
    .replace(/^\w/, (c) => c.toUpperCase());

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

const slugify = (t: string) =>
  t
    .toLowerCase()
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .replace(/[^a-z0-9]+/g, "-")
    .replace(/(^-|-$)/g, "");

const articleHref = (a: PublicArticle) => `/noticia/${a.slug || slugify(a.title)}`;

/* ── SVG icons ───────────────────────────────────────── */

const MenuIcon = () => (
  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><line x1="4" x2="20" y1="12" y2="12" /><line x1="4" x2="20" y1="6" y2="6" /><line x1="4" x2="20" y1="18" y2="18" /></svg>
);

const CloseIcon = () => (
  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><line x1="18" x2="6" y1="6" y2="18" /><line x1="6" x2="18" y1="6" y2="18" /></svg>
);

const SearchIcon = () => (
  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="11" cy="11" r="8" /><path d="m21 21-4.3-4.3" /></svg>
);

const PhoneIcon = () => (
  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" /></svg>
);

const FacebookIcon = () => (
  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" /></svg>
);

const TwitterIcon = () => (
  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z" /></svg>
);

const InstagramIcon = () => (
  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect width="20" height="20" x="2" y="2" rx="5" ry="5" /><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" /><line x1="17.5" x2="17.51" y1="6.5" y2="6.5" /></svg>
);

const LinkedInIcon = () => (
  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z" /><rect width="4" height="12" x="2" y="9" /><circle cx="4" cy="4" r="2" /></svg>
);

/* ── Data Fetching ───────────────────────────────────── */

type CategoryPageResponse = {
  category?: { id?: string; name?: string; slug?: string };
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
  };
};

const fetchCategoryArticles = async (
  slug: string,
  signal?: AbortSignal,
): Promise<{ name: string; articles: PublicArticle[] }> => {
  /* "Noticias" is not a real category — it shows ALL articles */
  if (slug === "noticias") {
    const home = await apiFetch<{ recent?: unknown[]; featured?: unknown[]; latest?: unknown[] }>(
      `${API_BASE_URL}/api/v1/public/home`,
      { method: "GET", signal },
    );

    const raw = Array.isArray(home.recent) ? home.recent : [];
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
    `${API_BASE_URL}/api/v1/public/category/${slug}`,
    { method: "GET", signal },
  );

  const name =
    data.category && typeof data.category.name === "string" ? data.category.name : slug;

  const articles = Array.isArray(data.articles)
    ? (data.articles
        .map((item, i) => normalizePublicArticle(item, i))
        .filter((a): a is PublicArticle => a !== null))
    : [];

  return { name, articles };
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
      <span className="pc-badge">{category}</span>
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
      <div className="pc-small-category">{category}</div>
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
      <div className="pc-small-category">{category}</div>
      <h3 className="pc-grid-title">{article.title}</h3>
      <div className="pc-meta">
        <span>{formatArticleDate(article.createdAt)}</span>
        <span>•</span>
        <span>{formatArticleTime(article.createdAt)}</span>
      </div>
    </div>
  </a>
);

/* ── Main Component ──────────────────────────────────── */

const CategoryPage = () => {
  const { slug } = useParams<{ slug: string }>();
  const capitalizedSlug = slug ? slug.charAt(0).toUpperCase() + slug.slice(1) : "";
  const [categoryName, setCategoryName] = useState(capitalizedSlug);
  const [articles, setArticles] = useState<PublicArticle[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [mobileOpen, setMobileOpen] = useState(false);

  useEffect(() => {
    if (!slug) return;

    const controller = new AbortController();

    const load = async () => {
      try {
        setLoading(true);
        setError(null);
        const data = await fetchCategoryArticles(slug, controller.signal);
        setCategoryName(data.name);
        setArticles(data.articles);
      } catch (err: unknown) {
        if (err instanceof Error && err.name === "AbortError") return;
        setError("No se pudieron cargar las publicaciones de esta categoría.");
      } finally {
        setLoading(false);
      }
    };

    void load();
    return () => controller.abort();
  }, [slug]);

  const featured = articles.slice(0, 1)[0] ?? null;
  const sideCards = articles.slice(1, 3);
  const allCards = articles.slice(3);

  const activeSlug = slug ?? "";

  return (
    <div className="ph-page">
      {/* ── Navbar ─────────────────────────────────── */}
      <nav className="public-nav-container">
        <div className="public-nav-inner">
          <div className="public-nav-top">
            <button className="ph-mobile-toggle" onClick={() => setMobileOpen(true)} aria-label="Abrir menú">
              <MenuIcon />
            </button>
            <a className="public-nav-logo-link" href="/">
              <img className="public-nav-logo" src={logoSrc} alt="Información de Altura" />
            </a>
            <div className="public-nav-actions">
              <div className="public-nav-date">{formatFullDate()}</div>
              <Link className="public-nav-subscribe" to="/suscripcion">Suscribirse</Link>
            </div>
          </div>
          <div className="public-nav-bottom">
            <div className="public-nav-links">
              {NAV_CATEGORIES.map((c) => (
                <a
                  key={c.slug}
                  className={`public-nav-link ${activeSlug === c.slug ? "active" : ""}`}
                  href={`/categoria/${c.slug}`}
                >
                  {c.label}
                </a>
              ))}
              <a className="public-nav-link" href="/recientes">Recientes</a>
            </div>
            <div className="public-nav-search">
              <span className="public-nav-search-icon"><SearchIcon /></span>
              <input className="public-nav-search-input" type="search" placeholder="Buscar noticias..." />
            </div>
          </div>
        </div>
      </nav>

      {/* Mobile drawer */}
      <div className={`ph-mobile-overlay ${mobileOpen ? "open" : ""}`} onClick={() => setMobileOpen(false)} />
      <aside className={`ph-mobile-drawer ${mobileOpen ? "open" : ""}`}>
        <div className="ph-mobile-drawer-header">
          <img src={logoSrc} alt="Información de Altura" />
          <button className="ph-mobile-close" onClick={() => setMobileOpen(false)} aria-label="Cerrar menú"><CloseIcon /></button>
        </div>
        <div className="ph-mobile-nav-links">
          {NAV_CATEGORIES.map((c) => (
            <a
              key={c.slug}
              className={`ph-mobile-nav-link ${activeSlug === c.slug ? "active" : ""}`}
              href={`/categoria/${c.slug}`}
            >
              {c.label}
            </a>
          ))}
          <a className="ph-mobile-nav-link" href="/recientes">Recientes</a>
        </div>
        <div className="ph-mobile-search">
          <div className="ph-mobile-search-wrap">
            <SearchIcon />
            <input className="ph-mobile-search-input" type="search" placeholder="Buscar noticias..." />
          </div>
        </div>
      </aside>

      {/* ── Category Banner ───────────────────────── */}
      <section className="pc-banner">
        <h1 className="pc-banner-title">{categoryName || slug}</h1>
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
            {/* Destacadas */}
            <section className="pc-section">
              <div className="pc-section-header">
                <h2 className="pc-section-title">Destacadas</h2>
              </div>

              <div className="pc-featured-grid">
                {featured && (
                  <FeaturedCard article={featured} category={slug === "noticias" ? featured.categoryName : categoryName} />
                )}
                {sideCards.length > 0 && (
                  <div className="pc-side-stack">
                    {sideCards.map((a) => (
                      <SmallCard key={a.id} article={a} category={slug === "noticias" ? a.categoryName : categoryName} />
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
                    <GridCard key={a.id} article={a} category={slug === "noticias" ? a.categoryName : categoryName} />
                  ))}
                </div>
              </section>
            )}
          </>
        )}
      </main>

      {/* ── Footer ────────────────────────────────── */}
      <footer className="public-footer">
        <div className="public-footer-inner">
          <div className="public-footer-grid">
            <div className="public-footer-brand">
              <a href="/" style={{ display: "inline-block", marginBottom: 16 }}>
                <img src={logoSrc} alt="Información de Altura" style={{ height: 80, width: "auto" }} />
              </a>
              <p>Periodismo independiente para el mundo moderno.</p>
              <div className="public-footer-phone">
                <PhoneIcon />
                <span>+34 900 123 456</span>
              </div>
            </div>
            <div>
              <h4 className="public-footer-title">Secciones</h4>
              <ul className="public-footer-links">
                {NAV_CATEGORIES.map((c) => (
                  <li key={c.slug}>
                    <a className="public-footer-link" href={`/categoria/${c.slug}`}>{c.label}</a>
                  </li>
                ))}
              </ul>
            </div>
            <div>
              <h4 className="public-footer-title">Redes Sociales</h4>
              <div className="public-footer-social">
                <a href="#" className="public-social-button" aria-label="Facebook"><FacebookIcon /></a>
                <a href="#" className="public-social-button" aria-label="Twitter"><TwitterIcon /></a>
                <a href="#" className="public-social-button" aria-label="Instagram"><InstagramIcon /></a>
                <a href="#" className="public-social-button" aria-label="LinkedIn"><LinkedInIcon /></a>
              </div>
            </div>
          </div>
          <div className="public-footer-bottom">© 2026 Información de Altura. Todos los derechos reservados.</div>
        </div>
      </footer>
    </div>
  );
};

export default CategoryPage;
