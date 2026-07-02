import { useState, useEffect } from "react";
import { Link, useNavigate } from "react-router-dom";
import { MenuIcon, CloseIcon, SearchIcon, MoonIcon, SunIcon } from "./Icons.tsx";
import logoSrc from "../assets/logo.png";
import type { PublicCategory } from "../libs/types.ts";


interface PublicNavbarProps {
  categories: PublicCategory[];
  mobileOpen: boolean;
  setMobileOpen: (open: boolean) => void;
  activeCategorySlug?: string;
}

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

const PublicNavbar = ({ categories, activeCategorySlug }: Omit<PublicNavbarProps, 'mobileOpen' | 'setMobileOpen'>) => {
  const [searchText, setSearchText] = useState("");
  const [mobileOpen, setMobileOpen] = useState(false);
  const [isDark, setIsDark] = useState(false);
  const navigate = useNavigate();

  useEffect(() => {
    const savedTheme = localStorage.getItem("theme");
    if (savedTheme === "dark") {
      setIsDark(true);
      document.documentElement.classList.add("dark-mode");
    } else {
      setIsDark(false);
      document.documentElement.classList.remove("dark-mode");
    }
  }, []);

  const toggleTheme = () => {
    if (isDark) {
      document.documentElement.classList.remove("dark-mode");
      localStorage.setItem("theme", "light");
      setIsDark(false);
    } else {
      document.documentElement.classList.add("dark-mode");
      localStorage.setItem("theme", "dark");
      setIsDark(true);
    }
  };

  const handleSearchSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (searchText.trim()) {
      navigate(`/buscar?q=${encodeURIComponent(searchText.trim())}`);
    }
  };

  const sortedCategories = [...categories].sort((a, b) => (a.order || 0) - (b.order || 0));
  const visibleCategories = sortedCategories.slice(0, 7);
  const moreCategories = sortedCategories.slice(7);

  return (
    <>
      <nav className="public-nav-container">
        <div className="public-nav-inner">
          <div className="public-nav-top">
            <button className="ph-mobile-toggle" onClick={() => setMobileOpen(true)} aria-label="Abrir menú">
              <MenuIcon />
            </button>
            <Link className="public-nav-logo-link" to="/">
              <img className="public-nav-logo" src={logoSrc} alt="Información de Altura" />
            </Link>
            <div className="public-nav-actions">
              <button 
                onClick={toggleTheme} 
                className="theme-toggle-btn" 
                aria-label="Alternar tema"
                style={{ background: "none", border: "none", cursor: "pointer", color: "#111827", display: "flex", alignItems: "center" }}
              >
                {isDark ? <SunIcon /> : <MoonIcon />}
              </button>
              <div className="public-nav-date">{formatFullDate()}</div>
              <Link className="public-nav-subscribe hide-mobile" to="/suscripcion">
                Suscribirse
              </Link>
            </div>
          </div>
          <div className="public-nav-bottom">
            <div className="public-nav-links">
              {visibleCategories.map((c) => (
                <Link 
                  key={c.id} 
                  className={`public-nav-link ${activeCategorySlug === c.slug ? "active" : ""}`} 
                  to={`/categoria/${c.slug}`}
                >
                  {c.name}
                </Link>
              ))}

              {moreCategories.length > 0 && (
                <div className="public-nav-more-dropdown">
                  <button className="public-nav-link more-btn">
                    Más categorías ▾
                  </button>
                  <div className="public-nav-more-menu">
                    {moreCategories.map(c => (
                      <Link 
                        key={c.id} 
                        className={`public-nav-more-link ${activeCategorySlug === c.slug ? "active" : ""}`} 
                        to={`/categoria/${c.slug}`}
                      >
                        {c.name}
                      </Link>
                    ))}
                  </div>
                </div>
              )}

              <Link className={`public-nav-link ${activeCategorySlug === "recientes" ? "active" : ""}`} to="/recientes">
                Recientes
              </Link>
              <Link className={`public-nav-link ${activeCategorySlug === "videos" ? "active" : ""}`} to="/videoteca">
                Videos
              </Link>
            </div>
            <form className="public-nav-search" onSubmit={handleSearchSubmit}>
              <span className="public-nav-search-icon">
                <SearchIcon />
              </span>
              <input
                className="public-nav-search-input"
                type="search"
                placeholder="Buscar noticias..."
                value={searchText}
                onChange={(e) => setSearchText(e.target.value)}
              />
            </form>
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
            <Link 
              key={c.id} 
              className={`ph-mobile-nav-link ${activeCategorySlug === c.slug ? "active" : ""}`} 
              to={`/categoria/${c.slug}`}
              onClick={() => setMobileOpen(false)}
            >
              {c.name}
            </Link>
          ))}
          <Link 
            className={`ph-mobile-nav-link ${activeCategorySlug === "recientes" ? "active" : ""}`} 
            to="/recientes"
            onClick={() => setMobileOpen(false)}
          >
            Recientes
          </Link>
          <Link 
            className={`ph-mobile-nav-link ${activeCategorySlug === "videos" ? "active" : ""}`} 
            to="/videoteca"
            onClick={() => setMobileOpen(false)}
          >
            Videos
          </Link>
          {/* Subscribe link in mobile drawer */}
          <Link 
            className="ph-mobile-nav-link subscribe-mobile-link" 
            to="/suscripcion"
            onClick={() => setMobileOpen(false)}
            style={{ marginTop: 'auto', borderTop: '1px solid #eee', color: '#8b1f1f', fontWeight: 'bold' }}
          >
            Suscribirse
          </Link>
        </div>
        <form className="ph-mobile-search" onSubmit={handleSearchSubmit}>
          <div className="ph-mobile-search-wrap">
            <SearchIcon />
            <input
              className="ph-mobile-search-input"
              type="search"
              placeholder="Buscar noticias..."
              value={searchText}
              onChange={(e) => setSearchText(e.target.value)}
            />
          </div>
        </form>
      </aside>
    </>
  );
};

export default PublicNavbar;
