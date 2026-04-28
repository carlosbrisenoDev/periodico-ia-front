import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import { Sidebar } from "../components/sidebar.tsx";
import { API_BASE_URL } from "../libs/config.ts";
import { ApiError, apiFetch } from "../libs/http.ts";

type DashboardArticle = {
  id: string;
  title: string;
  status: string;
  createdAt: string;
  authorName: string;
};

type DashboardSummaryResponse = {
  latestArticles?: unknown[];
};

const normalizeArticle = (
  item: unknown,
  index: number,
): DashboardArticle | null => {
  if (!item || typeof item !== "object") {
    return null;
  }

  const record = item as Record<string, unknown>;
  const title = typeof record.title === "string" ? record.title : null;

  if (!title) {
    return null;
  }

  const rawAuthor = record.author as Record<string, unknown> | undefined;
  const idFromRecord =
    typeof record.id === "string"
      ? record.id
      : typeof record._id === "string"
        ? record._id
        : typeof record.slug === "string"
          ? record.slug
          : `article-${index}`;

  const authorName =
    typeof record.authorName === "string"
      ? record.authorName
      : rawAuthor && typeof rawAuthor.name === "string"
        ? rawAuthor.name
        : "Redaccion";

  return {
    id: idFromRecord,
    title,
    status: typeof record.status === "string" ? record.status : "draft",
    createdAt:
      typeof record.createdAt === "string"
        ? record.createdAt
        : new Date().toISOString(),
    authorName,
  };
};

const formatDate = (dateIso: string): string => {
  const date = new Date(dateIso);

  if (Number.isNaN(date.getTime())) {
    return "Fecha desconocida";
  }

  const formatted = new Intl.DateTimeFormat("es-ES", {
    day: "2-digit",
    month: "short",
    year: "numeric",
  }).format(date);

  return formatted.replace(".", "");
};

const statusLabel = (status: string): string => {
  if (status === "published") {
    return "Publicado";
  }

  if (status === "scheduled") {
    return "Programado";
  }

  if (status === "deleted") {
    return "Eliminado";
  }

  return "Borrador";
};

const statusClass = (status: string): string => {
  if (status === "published") {
    return "dashboard-status-published";
  }

  if (status === "scheduled") {
    return "dashboard-status-scheduled";
  }

  if (status === "deleted") {
    return "dashboard-status-deleted";
  }

  return "dashboard-status-draft";
};

const Dashboard = () => {
  const navigate = useNavigate();
  const [articles, setArticles] = useState<DashboardArticle[]>([]);
  const [loading, setLoading] = useState<boolean>(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const controller = new AbortController();

    const loadDashboardSummary = async () => {
      try {
        setLoading(true);

        const payload = await apiFetch<DashboardSummaryResponse>(
          `${API_BASE_URL}/api/v1/dashboard/summary`,
          {
            method: "GET",
            credentials: "include",
            signal: controller.signal,
          },
        );

        const latest = Array.isArray(payload.latestArticles)
          ? payload.latestArticles
          : [];

        const normalized = latest
          .map((item, index) => normalizeArticle(item, index))
          .filter((item): item is DashboardArticle => item !== null)
          .slice(0, 3);

        setArticles(normalized);
        setError(null);
      } catch (err: unknown) {
        if (err instanceof Error && err.name === "AbortError") {
          return;
        }

        if (err instanceof ApiError && (err.status === 401 || err.status === 403)) {
          navigate("/adminlogin", { replace: true });
          return;
        }

        setError(err instanceof Error ? err.message : "Error desconocido");
        setArticles([]);
      } finally {
        setLoading(false);
      }
    };

    void loadDashboardSummary();

    return () => controller.abort();
  }, [navigate]);

  return (
    <div className="layout dashboard-layout">
      <aside className="sidebar">
        <Sidebar />
      </aside>

      <main className="content dashboard-content">
        <header className="dashboard-header">
          <h1 className="dashboard-title">Dashboard</h1>
          <p className="dashboard-subtitle">
            Bienvenido de vuelta. Aqui esta el resumen de tu periodico.
          </p>
        </header>

        <section className="dashboard-card">
          <h2 className="dashboard-card-title">Publicaciones Recientes</h2>

          <div className="dashboard-list">
            {loading ? (
              <p className="dashboard-info">Cargando publicaciones recientes...</p>
            ) : null}

            {!loading && error ? (
              <p className="dashboard-info error">{error}</p>
            ) : null}

            {!loading && !error && articles.length === 0 ? (
              <p className="dashboard-info">No hay publicaciones para mostrar.</p>
            ) : null}

            {articles.map((article) => (
              <article key={article.id} className="dashboard-list-item">
                <div>
                  <p className="dashboard-item-title">{article.title}</p>
                  <p className="dashboard-item-meta">
                    Por {article.authorName} · {formatDate(article.createdAt)}
                  </p>
                </div>

                <span className={`dashboard-status ${statusClass(article.status)}`}>
                  {statusLabel(article.status)}
                </span>
              </article>
            ))}
          </div>
        </section>
      </main>
    </div>
  );
};

export default Dashboard;
