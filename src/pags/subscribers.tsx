import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import { Sidebar } from "../components/sidebar.tsx";
import { API_BASE_URL } from "../libs/config.ts";
import { ApiError, apiFetch } from "../libs/http.ts";

type SubscriberRecord = {
  id: string;
  username: string;
  email: string;
  role: string;
  status: string;
  active: boolean;
  age?: number;
  phone?: string;
  location?: string;
};

type SubscribersResponse = {
  subscribers?: unknown[];
};

const normalizeSubscriber = (item: unknown, index: number): SubscriberRecord | null => {
  if (!item || typeof item !== "object") {
    return null;
  }

  const record = item as Record<string, unknown>;

  return {
    id: typeof record.id === "string" ? record.id : `sub-${index}`,
    username: typeof record.username === "string" ? record.username : "Sin nombre",
    email: typeof record.email === "string" ? record.email : "",
    role: typeof record.role === "string" ? record.role : "subscriber",
    status: typeof record.status === "string" ? record.status : "active",
    active: typeof record.active === "boolean" ? record.active : true,
    age: typeof record.age === "number" ? record.age : undefined,
    phone: typeof record.phone === "string" ? record.phone : undefined,
    location: typeof record.location === "string" ? record.location : undefined,
  };
};

const Subscribers = () => {
  const navigate = useNavigate();
  const [subscribers, setSubscribers] = useState<SubscriberRecord[]>([]);
  const [loading, setLoading] = useState<boolean>(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const controller = new AbortController();

    const loadSubscribers = async () => {
      try {
        setLoading(true);

        const payload = await apiFetch<SubscribersResponse>(`${API_BASE_URL}/api/v1/subscribers/users`, {
          method: "GET",
          credentials: "include",
          signal: controller.signal,
        });

        const normalized = (Array.isArray(payload.subscribers) ? payload.subscribers : [])
          .map((item, index) => normalizeSubscriber(item, index))
          .filter((item): item is SubscriberRecord => item !== null);

        setSubscribers(normalized);
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
        setSubscribers([]);
      } finally {
        setLoading(false);
      }
    };

    void loadSubscribers();

    return () => controller.abort();
  }, [navigate]);

  return (
    <div className="layout dashboard-layout">
      <aside className="sidebar">
        <Sidebar />
      </aside>

      <main className="content authors-users-content">
        <header className="authors-users-header">
          <div>
            <h1 className="authors-users-title">Suscriptores</h1>
            <p className="authors-users-subtitle">
              Gestiona los suscriptores del periódico.
            </p>
          </div>
        </header>

        {loading ? <p className="authors-users-info">Cargando suscriptores...</p> : null}
        {!loading && error ? <p className="authors-users-info error">{error}</p> : null}
        {!loading && !error && subscribers.length === 0 ? (
          <p className="authors-users-info">No hay suscriptores para mostrar.</p>
        ) : null}

        {!loading && !error && subscribers.length > 0 ? (
          <div style={{ overflowX: "auto", marginTop: "1rem" }}>
            <table style={{ width: "100%", borderCollapse: "collapse", textAlign: "left" }}>
              <thead>
                <tr style={{ borderBottom: "2px solid #eee" }}>
                  <th style={{ padding: "12px 8px" }}>Nombre</th>
                  <th style={{ padding: "12px 8px" }}>Email</th>
                  <th style={{ padding: "12px 8px" }}>Edad</th>
                  <th style={{ padding: "12px 8px" }}>Teléfono</th>
                  <th style={{ padding: "12px 8px" }}>Ubicación</th>
                  <th style={{ padding: "12px 8px" }}>Estado</th>
                </tr>
              </thead>
              <tbody>
                {subscribers.map(sub => (
                  <tr key={sub.id} style={{ borderBottom: "1px solid #eee" }}>
                    <td style={{ padding: "12px 8px", fontWeight: "bold" }}>{sub.username}</td>
                    <td style={{ padding: "12px 8px" }}>{sub.email}</td>
                    <td style={{ padding: "12px 8px" }}>{sub.age ?? "-"}</td>
                    <td style={{ padding: "12px 8px" }}>{sub.phone || "-"}</td>
                    <td style={{ padding: "12px 8px" }}>{sub.location || "-"}</td>
                    <td style={{ padding: "12px 8px" }}>
                      <span className={sub.active ? "author-card-role authors-users-role-admin" : "author-card-inactive"} style={{ display: "inline-block", padding: "4px 8px", borderRadius: "12px", fontSize: "12px" }}>
                        {sub.active ? "Activo" : "Inactivo"}
                      </span>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        ) : null}
      </main>
    </div>
  );
};

export default Subscribers;
