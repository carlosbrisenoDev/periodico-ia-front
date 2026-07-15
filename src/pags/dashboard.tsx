import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import { Sidebar } from "../components/sidebar.tsx";
import { API_BASE_URL } from "../libs/config.ts";
import { ApiError, apiFetch } from "../libs/http.ts";

type DashboardSummaryResponse = {
  counts?: { draft: number; published: number; scheduled: number };
  recentAuditLogs?: unknown[];
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

const actionMap: Record<string, string> = {
  create: 'Crear',
  update: 'Actualizar',
  delete: 'Eliminar',
  restore: 'Restaurar',
  publish: 'Publicar',
  login: 'Inicio de sesión',
  other: 'Otro'
};

const entityMap: Record<string, string> = {
  article: 'Artículo',
  category: 'Categoría',
  Settings: 'Configuración',
  Video: 'Video',
  comment: 'Comentario',
  Publication: 'Noticia/Artículo',
  Author: 'Autor',
  Category: 'Categoría',
  User: 'Usuario'
};

const renderAuditDetails = (details: any) => {
  if (!details) return null;
  if (typeof details === 'string') return <span>{details}</span>;
  if (typeof details === 'object') {
    return (
      <ul style={{ margin: "4px 0 0 16px", padding: 0, listStyleType: "circle" }}>
        {Object.entries(details).map(([key, value]) => {
          if (typeof value === 'object' && value !== null) {
            return (
              <li key={key}>
                <strong>{key}:</strong>
                <ul style={{ margin: "2px 0 0 16px", padding: 0, listStyleType: "square" }}>
                  {Object.entries(value).map(([subKey, subValue]) => (
                    <li key={subKey}><strong>{subKey}:</strong> {String(subValue)}</li>
                  ))}
                </ul>
              </li>
            );
          }
          return <li key={key}><strong>{key}:</strong> {String(value)}</li>;
        })}
      </ul>
    );
  }
  return <span>{JSON.stringify(details)}</span>;
};

const Dashboard = () => {
  const navigate = useNavigate();
  const [auditLogs, setAuditLogs] = useState<any[]>([]);
  const [loading, setLoading] = useState<boolean>(true);

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

          setAuditLogs(Array.isArray(payload.recentAuditLogs) ? payload.recentAuditLogs.slice(0, 5) : []);

      } catch (err: unknown) {
        if (err instanceof Error && err.name === "AbortError") {
          return;
        }

        if (err instanceof ApiError && (err.status === 401 || err.status === 403)) {
          navigate("/adminlogin", { replace: true });
          return;
        }

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

        <section className="dashboard-card" style={{ marginTop: "1.5rem" }}>
          <h2 className="dashboard-card-title">Registro de Auditoría</h2>
          <div className="dashboard-list">
            {loading ? (
              <p className="dashboard-info">Cargando registros...</p>
            ) : null}

            {!loading && auditLogs.length === 0 ? (
              <p className="dashboard-info">No hay registros recientes.</p>
            ) : null}

            {auditLogs.map((log) => (
              <div key={log.id || log._id} className="dashboard-list-item" style={{ flexDirection: "column", alignItems: "flex-start", gap: "4px" }}>
                <p className="dashboard-item-title" style={{ fontWeight: 600 }}>{actionMap[log.action] || log.action} - {entityMap[log.entityType] || log.entityType}</p>
                <p className="dashboard-item-meta" style={{ marginTop: 0 }}>
                  Por <strong>{log.userName || log.userEmail || 'Sistema'}</strong> el {formatDate(log.createdAt)} {new Date(log.createdAt).toLocaleTimeString("es-ES")}
                </p>
                <div className="dashboard-item-meta" style={{ fontSize: "0.85rem", marginTop: 0, width: "100%" }}>
                  <div style={{ marginBottom: "2px" }}><strong>Detalles:</strong></div>
                  <div style={{ background: "#f9fafb", padding: "8px", borderRadius: "4px", border: "1px solid #e5e7eb", overflowX: "auto" }}>
                    {renderAuditDetails(log.details)}
                  </div>
                </div>
              </div>
            ))}
          </div>
        </section>
      </main>
    </div>
  );
};

export default Dashboard;
