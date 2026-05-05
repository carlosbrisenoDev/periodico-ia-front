import React, { useEffect, useState } from "react";
import { useSearchParams } from "react-router-dom";
import { apiFetch, getPublicCategories } from "../libs/http.ts";
import { API_BASE_URL } from "../libs/config.ts";
import type { PublicArticle, PublicCategory } from "../libs/types.ts";
import PublicNavbar from "../components/PublicNavbar.tsx";
import PublicFooter from "../components/PublicFooter.tsx";
import { SearchIcon } from "../components/Icons.tsx";

const FilterIcon = () => (
  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
  </svg>
);

/* ── helpers ─────────────────────────────────────────── */




const formatArticleDate = (dateStr: string) => {
  const d = new Date(dateStr);
  const date = d.toLocaleDateString("es-ES", { day: "numeric", month: "long", year: "numeric" });
  const time = d.toLocaleTimeString("es-ES", { hour: "2-digit", minute: "2-digit" });
  return { date, time };
};

const SearchPage = () => {
  const [searchParams, setSearchParams] = useSearchParams();
  const query = searchParams.get("q") || "";
  const sort = searchParams.get("sort") || "newest";
  
  const [inputValue, setInputValue] = useState(query);
  const [results, setResults] = useState<PublicArticle[]>([]);
  const [loading, setLoading] = useState(false);
  const [categories, setCategories] = useState<PublicCategory[]>([]);

  useEffect(() => {
    const controller = new AbortController();
    
    const fetchData = async () => {
      setLoading(true);
      try {
        // Fetch categories for navbar
        const cats = await getPublicCategories(controller.signal);
        setCategories(cats);

        if (query) {
          const data = await apiFetch<{ items: PublicArticle[] }>(
            `${API_BASE_URL}/api/v1/public/search?q=${encodeURIComponent(query)}&sort=${sort}`,
            { signal: controller.signal }
          );
          setResults(data.items || []);
        } else {
          setResults([]);
        }
      } catch (err) {
        console.error("Search fetch error:", err);
      } finally {
        setLoading(false);
      }
    };

    fetchData();
    return () => controller.abort();
  }, [query, sort]);

  const handleSearchSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (inputValue.trim()) {
      setSearchParams({ q: inputValue.trim(), sort });
    }
  };

  const handleSortChange = (newSort: string) => {
    setSearchParams({ q: query, sort: newSort });
  };

  return (
    <div className="search-page">
      {/* Navbar (Mini version or reuse homepage navbar styles) */}
      <PublicNavbar categories={categories} />

      <header className="search-banner">
        <div className="search-banner-inner">
          <h1 className="search-banner-title">Búsqueda</h1>
          <form className="search-input-wrapper" onSubmit={handleSearchSubmit}>
            <span className="search-input-icon">
              <SearchIcon />
            </span>
            <input
              type="text"
              className="search-input-field"
              placeholder="Buscar noticias..."
              value={inputValue}
              onChange={(e) => setInputValue(e.target.value)}
            />
          </form>
        </div>
      </header>

      <div className="search-results-summary">
        <div className="search-results-summary-inner">
          <div className="search-results-text">
            {query ? (
              <>Resultados para: <strong>"{query}"</strong> - {results.length} resultados</>
            ) : (
              <>Ingresa un término para buscar</>
            )}
          </div>
          <button className="search-filters-toggle">
            <FilterIcon />
            Mostrar filtros
          </button>
        </div>
      </div>

      <main className="search-results-container">
        <div className="search-sort-area">
          <span className="search-sort-label">Ordenar por:</span>
          <select 
            className="search-sort-select" 
            value={sort}
            onChange={(e) => handleSortChange(e.target.value)}
          >
            <option value="relevant">Más Relevantes</option>
            <option value="newest">Más Nuevos</option>
            <option value="oldest">Más Antiguos</option>
          </select>
        </div>

        <div className="search-results-list">
          {loading ? (
            <div className="center" style={{ padding: "50px" }}>Buscando...</div>
          ) : results.length > 0 ? (
            results.map((article) => {
              const { date, time } = formatArticleDate(article.createdAt);
              return (
                <a key={article.id} className="search-article-card" href={`/articulo/${article.id}`}>
                  <div className="search-article-image-wrapper">
                    <img 
                      src={article.featuredImageUrl || "https://via.placeholder.com/400x250?text=No+Image"} 
                      alt={article.title} 
                      className="search-article-image" 
                    />
                  </div>
                  <div className="search-article-content">
                    <span className="search-article-category">{article.categoryName}</span>
                    <h3 className="search-article-title">{article.title}</h3>
                    <p className="search-article-excerpt">{article.excerpt}</p>
                    <div className="search-article-footer">
                      <div className="search-article-footer-item">
                        {date}
                      </div>
                      <div className="search-article-footer-item">
                        • {time}
                      </div>
                      <div className="search-article-footer-item">
                        • <span className="search-article-author">{article.authorName}</span>
                      </div>
                    </div>
                  </div>
                </a>
              );
            })
          ) : query ? (
            <div className="center" style={{ padding: "50px", color: "#666" }}>
              No se encontraron resultados para "{query}"
            </div>
          ) : null}
        </div>
      </main>

      <PublicFooter variant="search" />
    </div>
  );
};

export default SearchPage;
