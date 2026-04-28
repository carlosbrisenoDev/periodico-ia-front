import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import { apiFetch, getRecentPublications, getLatestPublications } from "../libs/http.ts";
import { API_BASE_URL } from "../libs/config.ts";
import type { PublicArticle } from "../libs/types.ts";
import logoSrc from "../assets/logo.png";

/* ── helpers ─────────────────────────────────────────── */

type Category = {
  id: string;
  name: string;
  slug: string;
};

const SECTION_ORDER: {
  key: string;
  label: string;
  layout: "A" | "B";
  grey: boolean;
}[] = [
  { key: "Deportes", label: "Deportes", layout: "A", grey: false },
  { key: "Cultura", label: "Cultura", layout: "B", grey: true },
  { key: "Seguridad", label: "Seguridad", layout: "A", grey: false },
  { key: "Comunidad", label: "Comunidad", layout: "B", grey: true },
  { key: "Opinión", label: "Opinión", layout: "A", grey: false },
];

const formatFullDate = (): string => {
  return new Intl.DateTimeFormat("es-ES", {
    weekday: "long",
    day: "numeric",
    month: "long",
    year: "numeric",
  })
    .format(new Date())
    .replace(/^\w/, (c) => c.toUpperCase());
};

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

const groupByCategory = (articles: PublicArticle[]) => {
  const map: Record<string, PublicArticle[]> = {};
  for (const a of articles) {
    const cat = a.categoryName || "General";
    if (!map[cat]) map[cat] = [];
    map[cat].push(a);
  }
  return map;
};

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

/* ── Card Components ─────────────────────────────────── */

const FeaturedCard = ({ article, category }: { article: PublicArticle; category?: string }) => (
  <a className="ph-featured-card" href={articleHref(article)}>
    <div className="ph-featured-image">
      {article.featuredImageUrl ? (
        <img src={article.featuredImageUrl} alt={article.title} />
      ) : (
        <div className="ph-featured-image-placeholder">{category || article.categoryName}</div>
      )}
    </div>
    <div className="ph-featured-body">
      <span className="ph-featured-badge">{category || article.categoryName}</span>
      <h2 className="ph-featured-title">{article.title}</h2>
      <p className="ph-featured-excerpt">{article.excerpt}</p>
      <div className="ph-featured-meta">
        <span>{formatArticleDate(article.createdAt)}</span>
        <span>•</span>
        <span>{formatArticleTime(article.createdAt)}</span>
        <span>•</span>
        <span className="ph-meta-author">{article.authorName}</span>
      </div>
    </div>
  </a>
);

const SmallCard = ({ article, category }: { article: PublicArticle; category?: string }) => (
  <a className="ph-small-card" href={articleHref(article)}>
    <div className="ph-small-image">
      {article.featuredImageUrl ? (
        <img src={article.featuredImageUrl} alt={article.title} />
      ) : (
        <div className="ph-small-image-placeholder">{category || article.categoryName}</div>
      )}
    </div>
    <div className="ph-small-body">
      <div className="ph-small-category">{category || article.categoryName}</div>
      <h3 className="ph-small-title">{article.title}</h3>
      <div className="ph-small-meta">
        <span>{formatArticleDate(article.createdAt)}</span>
        <span>•</span>
        <span>{formatArticleTime(article.createdAt)}</span>
      </div>
    </div>
  </a>
);

const HorizCard = ({ article, category }: { article: PublicArticle; category?: string }) => (
  <a className="ph-horiz-card" href={articleHref(article)}>
    <div className="ph-horiz-image">
      {article.featuredImageUrl ? (
        <img src={article.featuredImageUrl} alt={article.title} />
      ) : (
        <div className="ph-horiz-image-placeholder">{category || article.categoryName}</div>
      )}
    </div>
    <div className="ph-horiz-body">
      <div>
        <div className="ph-horiz-category">{category || article.categoryName}</div>
        <h3 className="ph-horiz-title">{article.title}</h3>
        <p className="ph-horiz-excerpt">{article.excerpt}</p>
      </div>
      <div className="ph-horiz-meta">
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
  articles,
  grey,
}: {
  title: string;
  articles: PublicArticle[];
  grey: boolean;
}) => {
  if (articles.length === 0) return null;
  const featured = articles[0];
  const sides = articles.slice(1, 3);
  const slug = slugify(title);

  const content = (
    <div className="ph-section">
      <SectionHeader
        title={title}
        href={`/categoria/${slug}`}
        titleClass={`ph-section-title-${slug}`}
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
  grey,
}: {
  title: string;
  articles: PublicArticle[];
  grey: boolean;
}) => {
  if (articles.length === 0) return null;
  const featured = articles[0];
  const sides = articles.slice(1, 3);
  const slug = slugify(title);

  const content = (
    <div className="ph-section">
      <SectionHeader
        title={title}
        href={`/categoria/${slug}`}
        titleClass={`ph-section-title-${slug}`}
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

/* ── Navbar ──────────────────────────────────────────── */

const PublicNavbar = ({
  mobileOpen,
  setMobileOpen,
  categories,
}: {
  mobileOpen: boolean;
  setMobileOpen: (v: boolean) => void;
  categories: Category[];
}) => {
  const visibleCategories = categories.slice(0, 7);
  const moreCategories = categories.slice(7);

  return (
    <>
      <nav className="public-nav-container">
      <div className="public-nav-inner">
        <div className="public-nav-top">
          <button
            className="ph-mobile-toggle"
            onClick={() => setMobileOpen(true)}
            aria-label="Abrir menú"
          >
            <MenuIcon />
          </button>
          <a className="public-nav-logo-link" href="/">
            <img className="public-nav-logo" src={logoSrc} alt="Información de Altura" />
          </a>
          <div className="public-nav-actions">
            <div className="public-nav-date">{formatFullDate()}</div>
            <Link className="public-nav-subscribe" to="/suscripcion">
              Suscribirse
            </Link>
          </div>
        </div>
        <div className="public-nav-bottom">
          <div className="public-nav-links">
            {visibleCategories.map((c) => (
              <a key={c.id} className="public-nav-link" href={`/categoria/${c.slug}`}>
                {c.name}
              </a>
            ))}
            
            {moreCategories.length > 0 && (
              <div className="public-nav-more-dropdown">
                <button className="public-nav-link more-btn">
                  Más categorías ▾
                </button>
                <div className="public-nav-more-menu">
                  {moreCategories.map(c => (
                    <a key={c.id} className="public-nav-more-link" href={`/categoria/${c.slug}`}>
                      {c.name}
                    </a>
                  ))}
                </div>
              </div>
            )}

            <a className="public-nav-link active" href="/recientes">
              Recientes
            </a>
          </div>
          <div className="public-nav-search">
            <span className="public-nav-search-icon">
              <SearchIcon />
            </span>
            <input
              className="public-nav-search-input"
              type="search"
              placeholder="Buscar noticias..."
            />
          </div>
        </div>
      </div>
    </nav>

    {/* Mobile drawer */}
    <div
      className={`ph-mobile-overlay ${mobileOpen ? "open" : ""}`}
      onClick={() => setMobileOpen(false)}
    />
    <aside className={`ph-mobile-drawer ${mobileOpen ? "open" : ""}`}>
      <div className="ph-mobile-drawer-header">
        <img src={logoSrc} alt="Información de Altura" />
        <button className="ph-mobile-close" onClick={() => setMobileOpen(false)} aria-label="Cerrar menú">
          <CloseIcon />
        </button>
      </div>
      <div className="ph-mobile-nav-links">
        {categories.map((c) => (
          <a key={c.id} className="ph-mobile-nav-link" href={`/categoria/${c.slug}`}>
            {c.name}
          </a>
        ))}
        <a className="ph-mobile-nav-link active" href="/recientes">
          Recientes
        </a>
      </div>
      <div className="ph-mobile-search">
        <div className="ph-mobile-search-wrap">
          <SearchIcon />
          <input className="ph-mobile-search-input" type="search" placeholder="Buscar noticias..." />
        </div>
      </div>
    </aside>
  </>
  );
};

/* ── Footer ──────────────────────────────────────────── */

const PublicFooter = ({ categories }: { categories: Category[] }) => (
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
            {categories.slice(0, 10).map((c) => (
              <li key={c.id}>
                <a className="public-footer-link" href={`/categoria/${c.slug}`}>
                  {c.name}
                </a>
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
);

/* ── Main HomePage ───────────────────────────────────── */

const HomePage = () => {
  const [articles, setArticles] = useState<PublicArticle[]>([]);
  const [categories, setCategories] = useState<Category[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [mobileOpen, setMobileOpen] = useState(false);

  useEffect(() => {
    const controller = new AbortController();

    const load = async () => {
      try {
        setLoading(true);

        const catsPromise = apiFetch<unknown[]>(`${API_BASE_URL}/api/v1/category`, {
          method: "GET",
          signal: controller.signal
        }).then(res => 
          (Array.isArray(res) ? res : []).map(item => {
            const cat = item as Record<string, unknown>;
            return {
              id: typeof cat.id === 'string' ? cat.id : '',
              name: typeof cat.name === 'string' ? cat.name : '',
              slug: typeof cat.slug === 'string' ? cat.slug : '',
            };
          }).filter(c => c.id && c.name)
        ).catch(() => [] as Category[]);

        const [recent, fetchedCats] = await Promise.all([
          getRecentPublications(controller.signal),
          catsPromise
        ]);
        
        setCategories(fetchedCats);

        if (recent.length > 0) {
          setArticles(recent);
          setError(null);
          return;
        }
        const latest = await getLatestPublications(controller.signal);
        setArticles(latest);
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

  // Group articles by category
  const grouped = groupByCategory(articles);

  // For the hero & "Últimas Noticias" we use all articles (first 3)
  const heroMain = articles[0] ?? null;
  const heroSide = articles.slice(1, 3);
  const latestThree = articles.slice(0, 3);

  return (
    <div className="ph-page">
      <PublicNavbar mobileOpen={mobileOpen} setMobileOpen={setMobileOpen} categories={categories} />

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
            {SECTION_ORDER.map((sec) => {
              const catArticles = grouped[sec.key] ?? [];
              if (catArticles.length === 0) return null;
              return sec.layout === "A" ? (
                <CategorySectionA
                  key={sec.key}
                  title={sec.label}
                  articles={catArticles}
                  grey={sec.grey}
                />
              ) : (
                <CategorySectionB
                  key={sec.key}
                  title={sec.label}
                  articles={catArticles}
                  grey={sec.grey}
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
          </>
        )}
      </main>

      <PublicFooter categories={categories} />
    </div>
  );
};

export default HomePage;
