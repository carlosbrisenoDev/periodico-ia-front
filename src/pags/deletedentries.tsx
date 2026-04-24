import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import { Sidebar } from "../components/sidebar.tsx";
import { API_BASE_URL } from "../libs/config.ts";
import { ApiError, apiFetch } from "../libs/http.ts";

type DeletedArticleRow = {
  id: string;
  title: string;
  authorName: string;
  categoryName: string;
  createdAt: string;
  deletedAt: string;
};

type DeletedArticlesResponse = {
  items?: unknown[];
};

const normalizeDeletedArticle = (item: unknown): DeletedArticleRow | null => {
  if (!item || typeof item !== "object") {
    return null;
  }

  const record = item as Record<string, unknown>;
  if (typeof record.id !== "string" || typeof record.title !== "string") {
    return null;
  }

  const author = record.author && typeof record.author === "object" ? (record.author as Record<string, unknown>) : null;
  const categories = Array.isArray(record.categories) ? record.categories : [];
  const firstCategory =
    categories.length > 0 && categories[0] && typeof categories[0] === "object"
      ? (categories[0] as Record<string, unknown>)
      : null;

  return {
    id: record.id,
    title: record.title,
    authorName:
      typeof record.authorName === "string"
        ? record.authorName
        : author && typeof author.name === "string"
          ? author.name
          : "Redacción",
    categoryName:
      typeof record.categoryName === "string"
        ? record.categoryName
        : firstCategory && typeof firstCategory.name === "string"
          ? firstCategory.name
          : "General",
    createdAt: typeof record.createdAt === "string" ? record.createdAt : new Date().toISOString(),
    deletedAt: typeof record.deletedAt === "string" ? record.deletedAt : new Date().toISOString(),
  };
};

const formatDate = (value: string): string => {
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) {
    return "Fecha desconocida";
  }

  return new Intl.DateTimeFormat("es-ES", {
    day: "2-digit",
    month: "short",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  })
    .format(date)
    .replace(".", "");
};

const DeletedEntries = () => {
  const navigate = useNavigate();
  const [entries, setEntries] = useState<DeletedArticleRow[]>([]);
  const [loading, setLoading] = useState<boolean>(true);
  const [error, setError] = useState<string | null>(null);
  const [message, setMessage] = useState<string>("");
  const [restoringById, setRestoringById] = useState<Record<string, boolean>>({});
  const [purgingById, setPurgingById] = useState<Record<string, boolean>>({});

  useEffect(() => {
    const controller = new AbortController();

    const loadDeletedEntries = async () => {
      try {
        setLoading(true);
        const payload = await apiFetch<DeletedArticlesResponse>(`${API_BASE_URL}/api/v1/article/deleted`, {
          method: "GET",
          credentials: "include",
          signal: controller.signal,
        });

        const normalized = (Array.isArray(payload.items) ? payload.items : [])
          .map((item) => normalizeDeletedArticle(item))
          .filter((item): item is DeletedArticleRow => item !== null);

        setEntries(normalized);
        setError(null);
      } catch (err: unknown) {
        if (err instanceof Error && err.name === "AbortError") {
          return;
        }

        if (err instanceof ApiError && (err.status === 401 || err.status === 403)) {
          navigate("/adminlogin", { replace: true });
          return;
        }

        setError(err instanceof Error ? err.message : "No se pudo cargar la papelera.");
        setEntries([]);
      } finally {
        setLoading(false);
      }
    };

    void loadDeletedEntries();

    return () => controller.abort();
  }, [navigate]);

  const restoreEntry = async (entryId: string) => {
    setMessage("");
    setRestoringById((prev) => ({ ...prev, [entryId]: true }));

    try {
      await apiFetch(`${API_BASE_URL}/api/v1/article/${entryId}/restore`, {
        method: "PATCH",
        credentials: "include",
      });

      setEntries((prev) => prev.filter((entry) => entry.id !== entryId));
      setMessage("Entrada restaurada correctamente.");
    } catch (err: unknown) {
      if (err instanceof ApiError && (err.status === 401 || err.status === 403)) {
        navigate("/adminlogin", { replace: true });
        return;
      }

      setMessage(err instanceof Error ? err.message : "No se pudo restaurar la entrada.");
    } finally {
      setRestoringById((prev) => {
        const next = { ...prev };
        delete next[entryId];
        return next;
      });
    }
  };

  const purgeEntry = async (entryId: string, title: string) => {
    const confirmed = window.confirm(`Eliminar permanentemente \"${title}\"?`);
    if (!confirmed) {
      return;
    }

    setMessage("");
    setPurgingById((prev) => ({ ...prev, [entryId]: true }));

    try {
      await apiFetch(`${API_BASE_URL}/api/v1/article/${entryId}/permanent`, {
        method: "DELETE",
        credentials: "include",
      });

      setEntries((prev) => prev.filter((entry) => entry.id !== entryId));
      setMessage("Entrada eliminada permanentemente.");
    } catch (err: unknown) {
      if (err instanceof ApiError && (err.status === 401 || err.status === 403)) {
        navigate("/adminlogin", { replace: true });
        return;
      }

      setMessage(err instanceof Error ? err.message : "No se pudo eliminar permanentemente la entrada.");
    } finally {
      setPurgingById((prev) => {
        const next = { ...prev };
        delete next[entryId];
        return next;
      });
    }
  };

  return (
    <div className="layout dashboard-layout">
      <aside className="sidebar">
        <Sidebar />
      </aside>

      <main className="content utility-page-content">
        <header className="utility-page-header">
          <h1>Entradas Borradas</h1>
          <p>
            Las entradas eliminadas se guardan aquí antes de ser borradas definitivamente.
            Puedes restaurarlas o eliminarlas de forma permanente.
          </p>
        </header>

        <div className="utility-page-card" style={{ marginTop: 16, borderColor: "#f4d96a", background: "#fffdf5" }}>
          <strong style={{ display: "block", marginBottom: 6, color: "#9a7605" }}>
            Eliminación automática en 30 días
          </strong>
          <span style={{ color: "#7a6a2c" }}>
            Las entradas en la papelera se eliminarán automáticamente después de 30 días.
          </span>
        </div>

        {message ? <p className="entries-info success">{message}</p> : null}
        {loading ? <p className="entries-info">Cargando entradas borradas...</p> : null}
        {!loading && error ? <p className="entries-info error">{error}</p> : null}

        {!loading && !error && entries.length === 0 ? (
          <div className="utility-page-card">
            <p style={{ margin: 0, color: "var(--text-muted)" }}>No hay entradas en la papelera.</p>
            <button
              type="button"
              className="entries-new-button"
              onClick={() => navigate("/allentries")}
              style={{ marginTop: 12 }}
            >
              Volver a Mis Entradas
            </button>
          </div>
        ) : null}

        {entries.length > 0 ? (
          <section className="entries-table-card" style={{ marginTop: 18 }}>
            <div className="entries-table-wrap">
              <table className="entries-table">
                <thead>
                  <tr>
                    <th>Título</th>
                    <th>Autor</th>
                    <th>Categoría</th>
                    <th>Fecha Original</th>
                    <th>Eliminado</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  {entries.map((entry) => {
                    const isRestoring = Boolean(restoringById[entry.id]);
                    const isPurging = Boolean(purgingById[entry.id]);

                    return (
                      <tr key={entry.id}>
                        <td className="entries-title-cell">{entry.title}</td>
                        <td>{entry.authorName}</td>
                        <td>
                          <span className="entries-category-chip">{entry.categoryName}</span>
                        </td>
                        <td className="entries-date-cell">{formatDate(entry.createdAt)}</td>
                        <td className="entries-date-cell">{formatDate(entry.deletedAt)}</td>
                        <td>
                          <div className="entries-actions" style={{ flexWrap: "wrap" }}>
                            <button
                              type="button"
                              onClick={() => {
                                void restoreEntry(entry.id);
                              }}
                              disabled={isRestoring || isPurging}
                              style={{
                                border: 0,
                                borderRadius: 12,
                                minHeight: 40,
                                padding: "0 14px",
                                background: "#daf1e3",
                                color: "#2f7f50",
                                fontWeight: 700,
                                cursor: "pointer",
                              }}
                            >
                              {isRestoring ? "Restaurando..." : "Restaurar"}
                            </button>
                            <button
                              type="button"
                              onClick={() => {
                                void purgeEntry(entry.id, entry.title);
                              }}
                              disabled={isRestoring || isPurging}
                              style={{
                                border: 0,
                                borderRadius: 12,
                                minHeight: 40,
                                padding: "0 14px",
                                background: "#dc3545",
                                color: "#ffffff",
                                fontWeight: 700,
                                cursor: "pointer",
                              }}
                            >
                              {isPurging ? "Eliminando..." : "Eliminar"}
                            </button>
                          </div>
                        </td>
                      </tr>
                    );
                  })}
                </tbody>
              </table>
            </div>
          </section>
        ) : null}
      </main>
    </div>
  );
};

export default DeletedEntries;

