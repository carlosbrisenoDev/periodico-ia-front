import { useEffect, useState } from "react";
import {
  BrowserRouter,
  Link,
  Navigate,
  Outlet,
  Route,
  Routes,
  useLocation,
} from "react-router-dom";
import "./App.css";
import Dashboard from "./pags/dashboard.tsx";
import AdminLoginPage from "./pags/adminloginpage.tsx";
import AllEntries from "./pags/allentries.tsx";
import AllEntriesAdmin from "./pags/allentriesadmin.tsx";
import AuthorsUsers from "./pags/authorsusers.tsx";
import Categories from "./pags/categories.tsx";
import NewPublication from "./pags/newpublication.tsx";
import EditPublication from "./pags/editpublication.tsx";
import ImageLibrary from "./pags/imagelibrary.tsx";
import SubscriptionPage from "./pags/subscription.tsx";
import DeletedEntries from "./pags/deletedentries.tsx";
import SettingsPage from "./pags/settings.tsx";
import { PublicationPreview } from "./pags/publicationpreview.tsx";
import {
  getLatestPublications,
  getOptionalMe,
  getRecentPublications,
  isAdmin,
} from "./libs/http.ts";
import type { ProfileData } from "./libs/types.ts";

type HomeArticle = {
  id: string;
  title: string;
  excerpt: string;
  createdAt: string;
  authorName: string;
  categoryName: string;
};

const formatDate = (isoDate: string): string => {
  const dateValue = new Date(isoDate);

  if (Number.isNaN(dateValue.getTime())) {
    return "Fecha desconocida";
  }

  return new Intl.DateTimeFormat("es-ES", {
    day: "2-digit",
    month: "short",
    year: "numeric",
  })
    .format(dateValue)
    .replace(".", "");
};

const HomePage = () => {
  const [articles, setArticles] = useState<HomeArticle[]>([]);
  const [loading, setLoading] = useState<boolean>(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const controller = new AbortController();

    const loadHomeData = async () => {
      try {
        setLoading(true);
        const recent = await getRecentPublications(controller.signal);

        if (recent.length > 0) {
          setArticles(recent);
          setError(null);
          return;
        }

        const latest = await getLatestPublications(controller.signal);
        setArticles(latest);
        setError(null);
      } catch (err: unknown) {
        if (err instanceof Error && err.name === "AbortError") {
          return;
        }

        setArticles([]);
        setError("No se pudieron cargar publicaciones recientes.");
      } finally {
        setLoading(false);
      }
    };

    void loadHomeData();

    return () => controller.abort();
  }, []);

  return (
    <main className="public-home">
      <header className="public-home-hero">
        <div>
          <h1 className="public-home-title">Periodico IA</h1>
          <p className="public-home-subtitle">
            Noticias recientes, destacadas y editadas por la redaccion.
          </p>
        </div>

        <div className="public-home-actions">
          <Link className="public-home-link secondary" to="/adminlogin">
            Panel admin
          </Link>
          <Link className="public-home-link primary" to="/suscripcion">
            Suscribirme
          </Link>
        </div>
      </header>

      <section className="public-home-section">
        <div className="public-home-section-head">
          <h2>Publicaciones recientes</h2>
          <span>{articles.length} articulos</span>
        </div>

        {loading ? <p className="public-home-info">Cargando contenido...</p> : null}
        {!loading && error ? <p className="public-home-info error">{error}</p> : null}

        {!loading && !error && articles.length === 0 ? (
          <p className="public-home-info">Aun no hay publicaciones disponibles.</p>
        ) : null}

        <div className="public-home-grid">
          {articles.map((article) => (
            <article key={article.id} className="public-home-card">
              <p className="public-home-category">{article.categoryName}</p>
              <h3>{article.title}</h3>
              <p className="public-home-excerpt">{article.excerpt}</p>
              <p className="public-home-meta">
                {article.authorName} · {formatDate(article.createdAt)}
              </p>
            </article>
          ))}
        </div>

        <article className="public-home-subscribe-cta">
          <div>
            <h3>Quieres recibir las noticias por correo?</h3>
            <p>Crea tu suscripcion y mantente al dia con las publicaciones mas relevantes.</p>
          </div>
          <Link className="public-home-link primary" to="/suscripcion">
            Crear suscripcion
          </Link>
        </article>
      </section>
    </main>
  );
};

const AdminRoute = () => {
  const location = useLocation();
  const [loading, setLoading] = useState(true);
  const [profile, setProfile] = useState<ProfileData | null>(null);

  useEffect(() => {
    const controller = new AbortController();

    const validateSession = async () => {
      try {
        const me = await getOptionalMe(controller.signal);
        setProfile(me);
      } finally {
        setLoading(false);
      }
    };

    void validateSession();

    return () => controller.abort();
  }, []);

  if (loading) {
    return <p className="route-guard-loading">Validando sesion...</p>;
  }

  if (!profile || !isAdmin(profile)) {
    return (
      <Navigate
        to="/adminlogin"
        replace
        state={{ redirectTo: location.pathname + location.search }}
      />
    );
  }

  return <Outlet />;
};

const App = () => {
  return (
    <BrowserRouter>
      <Routes>
        <Route path="/" element={<HomePage />} />
        <Route path="/suscripcion" element={<SubscriptionPage />} />
        <Route path="/adminlogin" element={<AdminLoginPage />} />

        <Route element={<AdminRoute />}>
          <Route path="/dashboard" element={<Dashboard />} />
          <Route path="/allentries" element={<AllEntries />} />
          <Route path="/all-entries" element={<AllEntriesAdmin />} />
          <Route path="/new-publication" element={<NewPublication />} />
          <Route path="/image-library" element={<ImageLibrary />} />
          <Route path="/publication/preview" element={<PublicationPreview />} />
          <Route path="/publication/:id/preview" element={<PublicationPreview />} />
          <Route path="/publication/:id/edit" element={<EditPublication />} />
          <Route path="/authors-users" element={<AuthorsUsers />} />
          <Route path="/categories" element={<Categories />} />
          <Route path="/deleted-entries" element={<DeletedEntries />} />
          <Route path="/settings" element={<SettingsPage />} />
        </Route>
      </Routes>
    </BrowserRouter>
  );
};

export default App;
