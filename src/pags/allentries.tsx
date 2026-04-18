import { useEffect, useMemo, useState } from "react";
import { Sidebar } from "../components/sidebar.tsx";
import { API_BASE_URL } from "../libs/config.ts";

type ArticleEntry = {
  id: string;
  title: string;
  status: string;
  createdAt: string;
  authorName: string;
  categoryName: string;
};

type ArticleListResponse = {
  items?: unknown[];
  message?: string;
};

const apiUrl = API_BASE_URL;

const fallbackEntries: ArticleEntry[] = [
  {
    id: "sample-1",
    title: "Nueva reforma económica aprobada",
    status: "published",
    createdAt: "2026-04-13T14:30:00.000Z",
    authorName: "Equipo Editorial",
    categoryName: "Política",
  },
  {
    id: "sample-2",
    title: "Entrevista exclusiva con el ministro de salud",
    status: "draft",
    createdAt: "2026-04-10T09:15:00.000Z",
    authorName: "Equipo Editorial",
    categoryName: "Salud",
  },
  {
    id: "sample-3",
    title: "Análisis del mercado financiero",
    status: "published",
    createdAt: "2026-04-08T16:45:00.000Z",
    authorName: "Equipo Editorial",
    categoryName: "Economía",
  },
  {
    id: "sample-4",
    title: "Cambios en la política educativa",
    status: "published",
    createdAt: "2026-04-05T11:20:00.000Z",
    authorName: "Equipo Editorial",
    categoryName: "Educación",
  },
];

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

export const AllEntries = () => {
  const [entries, setEntries] = useState<ArticleEntry[]>([]);
  const [authors, setAuthors] = useState<string[]>([]);
  const [statusFilter, setStatusFilter] = useState<string>("all");
  const [authorFilter, setAuthorFilter] = useState<string>("all");
  const [loading, setLoading] = useState<boolean>(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const controller = new AbortController();

    const loadAuthors = async () => {
      try {
        const res = await fetch(apiUrl + "/api/v1/author", {
          method: "GET",
          credentials: "include",
          signal: controller.signal,
        });

        const payload = (await res.json()) as unknown;

        if (!res.ok || !Array.isArray(payload)) {
          setAuthors([]);
          return;
        }

        const names = payload
          .map((item) => {
            if (!item || typeof item !== "object") {
              return null;
            }

            const author = item as Record<string, unknown>;
            return typeof author.name === "string" ? author.name : null;
          })
          .filter((name): name is string => Boolean(name));

        setAuthors(
          Array.from(new Set(names)).sort((a, b) => a.localeCompare(b, "es")),
        );
      } catch (err: unknown) {
        if (err instanceof Error && err.name === "AbortError") {
          return;
        }

        setAuthors([]);
      }
    };

    loadAuthors();

    return () => controller.abort();
  }, []);

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

        const res = await fetch(
          apiUrl + "/api/v1/article?" + params.toString(),
          {
            method: "GET",
            credentials: "include",
            signal: controller.signal,
          },
        );

        const payload = (await res.json()) as ArticleListResponse;

        if (!res.ok) {
          setError(payload.message ?? "No se pudo cargar la lista de entradas");
          setEntries([]);
          return;
        }

        const normalized = (Array.isArray(payload.items) ? payload.items : [])
          .map((item, index) => normalizeEntry(item, index))
          .filter((item): item is ArticleEntry => item !== null);

        setEntries(normalized);
        setError(null);
      } catch (err: unknown) {
        if (err instanceof Error && err.name === "AbortError") {
          return;
        }

        setError(err instanceof Error ? err.message : "Error desconocido");
        setEntries([]);
      } finally {
        setLoading(false);
      }
    };

    loadEntries();

    return () => controller.abort();
  }, [statusFilter]);

  const sourceEntries = useMemo(() => {
    if (entries.length > 0) {
      return entries;
    }

    return fallbackEntries;
  }, [entries]);

  const authorOptions = useMemo(() => {
    const namesFromEntries = sourceEntries.map((entry) => entry.authorName);
    return Array.from(new Set([...authors, ...namesFromEntries])).sort((a, b) =>
      a.localeCompare(b, "es"),
    );
  }, [authors, sourceEntries]);

  const visibleEntries = useMemo(() => {
    return sourceEntries.filter((entry) => {
      const matchStatus =
        statusFilter === "all" ? true : entry.status === statusFilter;
      const matchAuthor =
        authorFilter === "all" ? true : entry.authorName === authorFilter;

      return matchStatus && matchAuthor;
    });
  }, [sourceEntries, statusFilter, authorFilter]);

  return (
    <div className="layout dashboard-layout">
      <aside className="sidebar">
        <Sidebar />
      </aside>

      <main className="content entries-content">
        <header className="entries-header">
          <div>
            <h1 className="entries-title">Mis Entradas</h1>
            <p className="entries-subtitle">Gestiona todas tus publicaciones</p>
          </div>

          <button type="button" className="entries-new-button">
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
        </div>

        <section className="entries-table-card">
          {loading && entries.length === 0 ? (
            <p className="entries-info">Cargando entradas...</p>
          ) : null}

          {!loading && error && entries.length === 0 ? (
            <p className="entries-info error">
              Mostrando una vista de ejemplo mientras no hay datos reales.
            </p>
          ) : null}

          <div className="entries-table-wrap">
            <table className="entries-table">
              <thead>
                <tr>
                  <th>Título</th>
                  <th>Categoría</th>
                  <th>Estado</th>
                  <th>Fecha</th>
                  <th>Acciones</th>
                </tr>
              </thead>

              <tbody>
                {visibleEntries.length === 0 ? (
                  <tr>
                    <td colSpan={5} className="entries-empty-row">
                      No se encontraron publicaciones con estos filtros.
                    </td>
                  </tr>
                ) : null}

                {visibleEntries.map((entry) => {
                  const dateTime = formatDateTime(entry.createdAt);

                  return (
                    <tr key={entry.id}>
                      <td className="entries-title-cell">{entry.title}</td>
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
                              <path d="m12 20 8-8-3-3-8 8-1 4z" />
                              <path d="m14 7 3 3" />
                            </svg>
                          </button>

                          <button
                            type="button"
                            className="entries-action-button delete"
                            title="Eliminar"
                            aria-label="Eliminar publicación"
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
                            className="entries-action-button"
                            title="Destacar"
                            aria-label="Destacar publicación"
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
