import { useEffect, useMemo, useState } from "react";
import { useNavigate } from "react-router-dom";
import { Sidebar } from "../components/sidebar.tsx";
import { API_BASE_URL } from "../libs/config.ts";
import { ApiError, apiFetch, getMe } from "../libs/http.ts";

type ArticleEntry = {
  id: string;
  title: string;
  status: string;
  createdAt: string;
  authorId: string;
  authorName: string;
  categoryName: string;
  isFeatured: boolean;
};

type EntryPageVariant = "mine" | "all";

type AllEntriesProps = {
  variant?: EntryPageVariant;
};

type ArticleListResponse = {
  items?: unknown[];
  message?: string;
};

type DeleteArticleResponse = {
  message?: string;
};

const normalizeEntry = (item: unknown, index: number): ArticleEntry | null => {
  if (!item || typeof item !== "object") {
    return null;
  }

  const record = item as Record<string, unknown>;
  const title = typeof record.title === "string" ? record.title : null;

  if (!title) {
    return null;
  }

  const recordAuthor =
    record.author && typeof record.author === "object"
      ? (record.author as Record<string, unknown>)
      : null;

  const recordCategory =
    record.category && typeof record.category === "object"
      ? (record.category as Record<string, unknown>)
      : null;

  const categories = Array.isArray(record.categories) ? record.categories : [];
  const firstCategory =
    categories.length > 0 && categories[0] && typeof categories[0] === "object"
      ? (categories[0] as Record<string, unknown>)
      : null;

  return {
    id:
      typeof record.id === "string"
        ? record.id
        : typeof record._id === "string"
          ? record._id
          : typeof record.slug === "string"
            ? record.slug
            : `entry-${index}`,
    title,
    status: typeof record.status === "string" ? record.status : "draft",
    createdAt:
      typeof record.createdAt === "string"
        ? record.createdAt
        : new Date().toISOString(),
    authorId: typeof record.authorId === "string" ? record.authorId : "",
    authorName:
      typeof record.authorName === "string"
        ? record.authorName
        : recordAuthor && typeof recordAuthor.name === "string"
          ? recordAuthor.name
          : "Redacción",
    categoryName:
      typeof record.categoryName === "string"
        ? record.categoryName
        : recordCategory && typeof recordCategory.name === "string"
          ? recordCategory.name
          : firstCategory && typeof firstCategory.name === "string"
            ? firstCategory.name
            : "General",
    isFeatured: typeof record.isFeatured === "boolean" ? record.isFeatured : false,
  };
};

const statusLabel = (status: string): string => {
  if (status === "published") {
    return "Publicado";
  }

  if (status === "scheduled") {
    return "Programado";
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

  return "dashboard-status-draft";
};

const formatDateTime = (
  isoDate: string,
): {
  date: string;
  time: string;
} => {
  const dateValue = new Date(isoDate);

  if (Number.isNaN(dateValue.getTime())) {
    return {
      date: "Fecha desconocida",
      time: "--:--",
    };
  }

  const months = [
    "Ene",
    "Feb",
    "Mar",
    "Abr",
    "May",
    "Jun",
    "Jul",
    "Ago",
    "Sep",
    "Oct",
    "Nov",
    "Dic",
  ];

  const day = String(dateValue.getDate());
  const month = months[dateValue.getMonth()];
  const year = String(dateValue.getFullYear());
  const hours = String(dateValue.getHours()).padStart(2, "0");
  const minutes = String(dateValue.getMinutes()).padStart(2, "0");

  return {
    date: `${day} ${month} ${year}`,
    time: `${hours}:${minutes}`,
  };
};

export const AllEntries = ({ variant = "mine" }: AllEntriesProps) => {
  const navigate = useNavigate();
  const [entries, setEntries] = useState<ArticleEntry[]>([]);
  const [authors, setAuthors] = useState<string[]>([]);
  const [myAuthorIds, setMyAuthorIds] = useState<string[]>([]);
  const [statusFilter, setStatusFilter] = useState<string>("all");
  const [authorFilter, setAuthorFilter] = useState<string>("all");
  const [loading, setLoading] = useState<boolean>(true);
  const [error, setError] = useState<string | null>(null);
  const [actionError, setActionError] = useState<string | null>(null);
  const [togglingFeaturedById, setTogglingFeaturedById] = useState<Record<string, boolean>>(
    {},
  );
  const [deletingById, setDeletingById] = useState<Record<string, boolean>>({});

  useEffect(() => {
    const controller = new AbortController();

    const loadAuthors = async () => {
      try {
        const [profile, payload] = await Promise.all([
          getMe(controller.signal),
          apiFetch<unknown[]>(`${API_BASE_URL}/api/v1/author`, {
            method: "GET",
            credentials: "include",
            signal: controller.signal,
          }),
        ]);

        const authorRecords = payload
          .map((item): { id: string; name: string; userId: string | null } | null => {
            if (!item || typeof item !== "object") {
              return null;
            }

            const author = item as Record<string, unknown>;
            if (typeof author.id !== "string" || typeof author.name !== "string") {
              return null;
            }

            return {
              id: author.id,
              name: author.name,
              userId: typeof author.userId === "string" ? author.userId : null,
            };
          })
          .filter((author): author is { id: string; name: string; userId: string | null } =>
            author !== null,
          );

        const names = authorRecords.map((author) => author.name);
        const linkedAuthorIds = authorRecords
          .filter((author) => author.userId === profile.id)
          .map((author) => author.id);

        setAuthors(
          Array.from(new Set(names)).sort((a, b) => a.localeCompare(b, "es")),
        );
        setMyAuthorIds(linkedAuthorIds);
      } catch (err: unknown) {
        if (err instanceof Error && err.name === "AbortError") {
          return;
        }

        if (err instanceof ApiError && (err.status === 401 || err.status === 403)) {
          navigate("/adminlogin", { replace: true });
          return;
        }

        setAuthors([]);
        setMyAuthorIds([]);
      }
    };

    void loadAuthors();

    return () => controller.abort();
  }, [navigate]);

  useEffect(() => {
    const controller = new AbortController();

    const loadEntries = async () => {
      try {
        setLoading(true);

        const params = new URLSearchParams({
          page: "1",
          limit: "50",
        });

        if (statusFilter !== "all") {
          params.set("status", statusFilter);
        }

        const payload = await apiFetch<ArticleListResponse>(
          `${API_BASE_URL}/api/v1/article?${params.toString()}`,
          {
            method: "GET",
            credentials: "include",
            signal: controller.signal,
          },
        );

        const normalized = (Array.isArray(payload.items) ? payload.items : [])
          .map((item, index) => normalizeEntry(item, index))
          .filter((item): item is ArticleEntry => item !== null);

        const filteredByVariant =
          variant === "mine"
            ? normalized.filter((entry) => myAuthorIds.includes(entry.authorId))
            : normalized;

        setEntries(filteredByVariant);
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
        setEntries([]);
      } finally {
        setLoading(false);
      }
    };

    void loadEntries();

    return () => controller.abort();
  }, [myAuthorIds, navigate, statusFilter, variant]);

  const authorOptions = useMemo(() => {
    const namesFromEntries = entries.map((entry) => entry.authorName);
    return Array.from(new Set([...authors, ...namesFromEntries])).sort((a, b) =>
      a.localeCompare(b, "es"),
    );
  }, [authors, entries]);

  const visibleEntries = useMemo(() => {
    return entries.filter((entry) => {
      const matchStatus =
        statusFilter === "all" ? true : entry.status === statusFilter;
      const matchAuthor =
        authorFilter === "all" ? true : entry.authorName === authorFilter;

      return matchStatus && matchAuthor;
    });
  }, [entries, statusFilter, authorFilter]);

  const toggleFeatured = async (entryId: string, currentValue: boolean) => {
    const nextValue = !currentValue;

    setActionError(null);
    setTogglingFeaturedById((prev) => ({ ...prev, [entryId]: true }));
    setEntries((prev) =>
      prev.map((entry) =>
        entry.id === entryId ? { ...entry, isFeatured: nextValue } : entry,
      ),
    );

    try {
      const payload = await apiFetch<{ isFeatured?: boolean }>(
        `${API_BASE_URL}/api/v1/article/${entryId}/feature`,
        {
          method: "PATCH",
          credentials: "include",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            isFeatured: nextValue,
          }),
        },
      );

      if (typeof payload.isFeatured === "boolean") {
        const resolvedIsFeatured = payload.isFeatured;
        setEntries((prev) =>
          prev.map((entry) =>
            entry.id === entryId
              ? { ...entry, isFeatured: resolvedIsFeatured }
              : entry,
          ),
        );
      }
    } catch (err: unknown) {
      if (err instanceof ApiError && (err.status === 401 || err.status === 403)) {
        navigate("/adminlogin", { replace: true });
        return;
      }

      setEntries((prev) =>
        prev.map((entry) =>
          entry.id === entryId ? { ...entry, isFeatured: currentValue } : entry,
        ),
      );
      setActionError(
        err instanceof Error
          ? err.message
          : "No se pudo actualizar el destacado de la publicacion.",
      );
    } finally {
      setTogglingFeaturedById((prev) => {
        const next = { ...prev };
        delete next[entryId];
        return next;
      });
    }
  };

  const deleteEntry = async (entryId: string, title: string) => {
    const confirmed = window.confirm(`Eliminar la publicacion \"${title}\"?`);
    if (!confirmed) {
      return;
    }

    setActionError(null);
    setDeletingById((prev) => ({ ...prev, [entryId]: true }));

    try {
      await apiFetch<DeleteArticleResponse>(`${API_BASE_URL}/api/v1/article/${entryId}`, {
        method: "DELETE",
        credentials: "include",
      });

      setEntries((prev) => prev.filter((entry) => entry.id !== entryId));
    } catch (err: unknown) {
      if (err instanceof ApiError && (err.status === 401 || err.status === 403)) {
        navigate("/adminlogin", { replace: true });
        return;
      }

      setActionError(
        err instanceof Error ? err.message : "No se pudo eliminar la publicacion.",
      );
    } finally {
      setDeletingById((prev) => {
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

      <main className="content entries-content">
        <header className="entries-header">
          <div>
            <h1 className="entries-title">{variant === "all" ? "Todas las Entradas" : "Mis Entradas"}</h1>
            <p className="entries-subtitle">
              {variant === "all"
                ? "Visualiza y gestiona todas las publicaciones del equipo"
                : "Gestiona todas tus publicaciones"}
            </p>
          </div>

          <button
            type="button"
            className="entries-new-button"
            onClick={() => navigate("/new-publication")}
          >
            Nueva Entrada
          </button>
        </header>

        <div className="entries-filters">
          <select
            className="entries-select"
            value={statusFilter}
            onChange={(event) => setStatusFilter(event.target.value)}
            aria-label="Filtrar por estado"
          >
            <option value="all">Todos los estados</option>
            <option value="published">Publicado</option>
            <option value="draft">Borrador</option>
            <option value="scheduled">Programado</option>
          </select>

          {variant === "all" ? (
            <select
              className="entries-select"
              value={authorFilter}
              onChange={(event) => setAuthorFilter(event.target.value)}
              aria-label="Filtrar por autor"
            >
              <option value="all">Todos los autores</option>
              {authorOptions.map((authorName) => (
                <option key={authorName} value={authorName}>
                  {authorName}
                </option>
              ))}
            </select>
          ) : null}
        </div>

        {actionError ? <p className="entries-info error">{actionError}</p> : null}

        <section className="entries-table-card">
          {loading && entries.length === 0 ? (
            <p className="entries-info">Cargando entradas...</p>
          ) : null}

          {!loading && error && entries.length === 0 ? (
            <p className="entries-info error">{error}</p>
          ) : null}

          <div className="entries-table-wrap">
            <table className="entries-table">
              <thead>
                <tr>
                  <th>Título</th>
                  {variant === "all" ? <th>Autor</th> : null}
                  <th>Categoría</th>
                  <th>Estado</th>
                  <th>Fecha</th>
                  <th>Acciones</th>
                </tr>
              </thead>

              <tbody>
                {visibleEntries.length === 0 ? (
                  <tr>
                    <td colSpan={variant === "all" ? 6 : 5} className="entries-empty-row">
                      No se encontraron publicaciones con estos filtros.
                    </td>
                  </tr>
                ) : null}

                {visibleEntries.map((entry) => {
                  const dateTime = formatDateTime(entry.createdAt);
                  const isTogglingFeatured = Boolean(togglingFeaturedById[entry.id]);

                  return (
                    <tr key={entry.id}>
                      <td className="entries-title-cell">{entry.title}</td>
                      {variant === "all" ? <td>{entry.authorName}</td> : null}
                      <td>
                        <span className="entries-category-chip">
                          {entry.categoryName}
                        </span>
                      </td>
                      <td>
                        <span
                          className={`dashboard-status ${statusClass(entry.status)}`}
                        >
                          {statusLabel(entry.status)}
                        </span>
                      </td>
                      <td className="entries-date-cell">
                        <span>{dateTime.date}</span>
                        <span className="entries-date-dot">•</span>
                        <span className="entries-time-with-icon">
                          <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            strokeWidth="2"
                            strokeLinecap="round"
                            strokeLinejoin="round"
                            aria-hidden="true"
                          >
                            <circle cx="12" cy="12" r="9" />
                            <path d="M12 7v5l3 2" />
                          </svg>
                          {dateTime.time}
                        </span>
                      </td>
                      <td>
                        <div className="entries-actions">
                          <button
                            type="button"
                            className="entries-action-button"
                            title="Abrir"
                            aria-label="Abrir publicación"
                            onClick={() => navigate(`/publication/${entry.id}/preview`)}
                            disabled={Boolean(deletingById[entry.id])}
                          >
                            <svg
                              viewBox="0 0 24 24"
                              fill="none"
                              stroke="currentColor"
                              strokeWidth="2"
                              strokeLinecap="round"
                              strokeLinejoin="round"
                              aria-hidden="true"
                            >
                              <path d="M14 3h7v7" />
                              <path d="M10 14 21 3" />
                              <path d="M21 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5" />
                            </svg>
                          </button>

                          <button
                            type="button"
                            className="entries-action-button"
                            title="Editar"
                            aria-label="Editar publicación"
                            onClick={() => navigate(`/publication/${entry.id}/edit`)}
                            disabled={Boolean(deletingById[entry.id])}
                          >
                            <svg
                              viewBox="0 0 24 24"
                              fill="none"
                              stroke="currentColor"
                              strokeWidth="2"
                              strokeLinecap="round"
                              strokeLinejoin="round"
                              aria-hidden="true"
                            >
                              <path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z" />
                            </svg>
                          </button>

                          <button
                            type="button"
                            className="entries-action-button delete"
                            title="Eliminar"
                            aria-label="Eliminar publicación"
                            onClick={() => {
                              void deleteEntry(entry.id, entry.title);
                            }}
                            disabled={Boolean(deletingById[entry.id])}
                          >
                            <svg
                              viewBox="0 0 24 24"
                              fill="none"
                              stroke="currentColor"
                              strokeWidth="2"
                              strokeLinecap="round"
                              strokeLinejoin="round"
                              aria-hidden="true"
                            >
                              <path d="M3 6h18" />
                              <path d="M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2" />
                              <path d="M6 6v14a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V6" />
                              <path d="M10 11v6" />
                              <path d="M14 11v6" />
                            </svg>
                          </button>

                          <button
                            type="button"
                            className={`entries-action-button star${
                              entry.isFeatured ? " active" : ""
                            }`}
                            title={entry.isFeatured ? "Quitar destacado" : "Destacar"}
                            aria-label={
                              entry.isFeatured
                                ? "Quitar destacado de la publicacion"
                                : "Destacar publicacion"
                            }
                            aria-pressed={entry.isFeatured}
                            disabled={isTogglingFeatured || Boolean(deletingById[entry.id])}
                            onClick={() => {
                              void toggleFeatured(entry.id, entry.isFeatured);
                            }}
                          >
                            <svg
                              viewBox="0 0 24 24"
                              fill={entry.isFeatured ? "currentColor" : "none"}
                              stroke="currentColor"
                              strokeWidth="2"
                              strokeLinecap="round"
                              strokeLinejoin="round"
                              aria-hidden="true"
                            >
                              <path d="m12 3 2.9 5.9 6.6 1-4.8 4.7 1.1 6.6-5.8-3.1-5.8 3.1 1.1-6.6L2.5 9.9l6.6-1z" />
                            </svg>
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
      </main>
    </div>
  );
};

export default AllEntries;
