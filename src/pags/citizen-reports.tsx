import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import { Sidebar } from "../components/sidebar.tsx";
import { API_BASE_URL } from "../libs/config.ts";
import { ApiError, apiFetch } from "../libs/http.ts";

type CitizenReport = {
  id: string;
  name: string;
  email: string;
  phone?: string;
  subject: string;
  description: string;
  imageUrl?: string;
  status: "new" | "reviewed" | "resolved";
  createdAt: string;
};

type ReportsResponse = {
  data: CitizenReport[];
  meta: {
    total: number;
    page: number;
    limit: number;
  };
};

const CitizenReportsModeration = () => {
  const navigate = useNavigate();
  const [reports, setReports] = useState<CitizenReport[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  
  const [statusFilter, setStatusFilter] = useState<string>("all");
  const [currentPage, setCurrentPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);

  const fetchReports = async (page: number, status: string, signal?: AbortSignal) => {
    try {
      setLoading(true);
      setError(null);
      let url = `${API_BASE_URL}/api/v1/citizen-reports?page=${page}&limit=20`;
      if (status !== "all") {
        url += `&status=${status}`;
      }
      
      const res = await apiFetch<ReportsResponse>(url, {
        method: "GET",
        credentials: "include",
        signal,
      });

      setReports(res.data || []);
      setTotalPages(Math.ceil((res.meta?.total || 0) / (res.meta?.limit || 20)) || 1);
    } catch (err: unknown) {
      if (err instanceof Error && err.name === "AbortError") return;
      if (err instanceof ApiError && (err.status === 401 || err.status === 403)) {
        navigate("/adminlogin", { replace: true });
        return;
      }
      setError("Error al cargar reportes ciudadanos.");
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    const controller = new AbortController();
    fetchReports(currentPage, statusFilter, controller.signal);
    return () => controller.abort();
  }, [currentPage, statusFilter, navigate]);

  const handleStatusChange = async (id: string, newStatus: "new" | "reviewed" | "resolved") => {
    try {
      await apiFetch(`${API_BASE_URL}/api/v1/citizen-reports/${id}/status`, {
        method: "PATCH",
        credentials: "include",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ status: newStatus }),
      });
      
      setReports(prev => prev.map(r => r.id === id ? { ...r, status: newStatus } : r));
    } catch (err) {
      alert("Error al actualizar el estado del reporte.");
    }
  };

  const handleDelete = async (id: string) => {
    if (!window.confirm("¿Estás seguro de eliminar este reporte permanentemente?")) return;
    try {
      await apiFetch(`${API_BASE_URL}/api/v1/citizen-reports/${id}`, {
        method: "DELETE",
        credentials: "include",
      });
      setReports(prev => prev.filter(r => r.id !== id));
    } catch (err) {
      alert("Error al eliminar el reporte.");
    }
  };

  const getStatusColor = (status: string) => {
    switch (status) {
      case "new": return { bg: "#fee2e2", text: "#991b1b", label: "Nuevo" };
      case "reviewed": return { bg: "#fef3c7", text: "#92400e", label: "En Revisión" };
      case "resolved": return { bg: "#d1fae5", text: "#065f46", label: "Resuelto" };
      default: return { bg: "#f3f4f6", text: "#374151", label: status };
    }
  };

  return (
    <div className="layout dashboard-layout">
      <aside className="sidebar">
        <Sidebar />
      </aside>

      <main className="content authors-users-content">
        <header className="authors-users-header">
          <div>
            <h1 className="authors-users-title">Reportes Ciudadanos</h1>
            <p className="authors-users-subtitle">Gestiona y revisa los reportes enviados por la comunidad.</p>
          </div>
          
          <div style={{ display: "flex", gap: "1rem", alignItems: "center" }}>
            <label style={{ fontSize: "0.875rem", fontWeight: "500", color: "#374151" }}>Filtrar por estado:</label>
            <select 
              value={statusFilter}
              onChange={(e) => {
                setStatusFilter(e.target.value);
                setCurrentPage(1);
              }}
              style={{ padding: "0.5rem", borderRadius: "6px", border: "1px solid #d1d5db" }}
            >
              <option value="all">Todos</option>
              <option value="new">Nuevos</option>
              <option value="reviewed">En Revisión</option>
              <option value="resolved">Resueltos</option>
            </select>
          </div>
        </header>

        {loading ? (
          <p className="authors-users-info">Cargando reportes...</p>
        ) : error ? (
          <p className="authors-users-info error">{error}</p>
        ) : reports.length === 0 ? (
          <p className="authors-users-info">No hay reportes para mostrar.</p>
        ) : (
          <div style={{ display: "flex", flexDirection: "column", gap: "1.5rem", marginTop: "1.5rem" }}>
            {reports.map((report) => {
              const statusInfo = getStatusColor(report.status);
              return (
                <div key={report.id} style={{ 
                  padding: "1.5rem", 
                  backgroundColor: "#fff", 
                  borderRadius: "8px", 
                  border: "1px solid #e5e7eb",
                  boxShadow: "0 1px 2px 0 rgba(0, 0, 0, 0.05)"
                }}>
                  <div style={{ display: "flex", justifyContent: "space-between", alignItems: "flex-start", marginBottom: "1rem" }}>
                    <div>
                      <h3 style={{ fontSize: "1.125rem", fontWeight: "600", margin: "0 0 0.5rem 0" }}>{report.subject}</h3>
                      <p style={{ fontSize: "0.875rem", color: "#4b5563", margin: "0 0 0.25rem 0" }}>
                        <strong>De:</strong> {report.name} ({report.email})
                      </p>
                      {report.phone && (
                        <p style={{ fontSize: "0.875rem", color: "#4b5563", margin: "0 0 0.25rem 0" }}>
                          <strong>Teléfono:</strong> {report.phone}
                        </p>
                      )}
                      <p style={{ fontSize: "0.75rem", color: "#9ca3af", margin: "0.5rem 0 0 0" }}>
                        Recibido el: {new Date(report.createdAt).toLocaleDateString("es-ES")} {new Date(report.createdAt).toLocaleTimeString("es-ES")}
                      </p>
                    </div>
                    <div>
                      <span style={{ 
                        padding: "4px 8px", 
                        borderRadius: "12px", 
                        fontSize: "12px", 
                        fontWeight: "500",
                        backgroundColor: statusInfo.bg,
                        color: statusInfo.text
                      }}>
                        {statusInfo.label}
                      </span>
                    </div>
                  </div>
                  
                  <div style={{ margin: "1rem 0", color: "#374151", whiteSpace: "pre-wrap", backgroundColor: "#f9fafb", padding: "1rem", borderRadius: "6px" }}>
                    {report.description}
                  </div>

                  {report.imageUrl && (
                    <div style={{ margin: "1rem 0" }}>
                      <p style={{ fontSize: "0.875rem", fontWeight: "500", marginBottom: "0.5rem" }}>Imagen Adjunta:</p>
                      <a href={report.imageUrl.startsWith("http") ? report.imageUrl : `${API_BASE_URL}${report.imageUrl}`} target="_blank" rel="noreferrer">
                        <img 
                          src={report.imageUrl.startsWith("http") ? report.imageUrl : `${API_BASE_URL}${report.imageUrl}`} 
                          alt="Adjunto del reporte" 
                          style={{ maxWidth: "300px", maxHeight: "300px", borderRadius: "8px", border: "1px solid #d1d5db", objectFit: "contain" }} 
                        />
                      </a>
                    </div>
                  )}
                  
                  <div style={{ display: "flex", gap: "0.5rem", marginTop: "1.5rem" }}>
                    {report.status !== "reviewed" && (
                      <button 
                        onClick={() => handleStatusChange(report.id, "reviewed")}
                        style={{ padding: "0.5rem 1rem", backgroundColor: "#3b82f6", color: "white", border: "none", borderRadius: "4px", cursor: "pointer", fontSize: "0.875rem", fontWeight: "500" }}
                      >
                        Marcar en Revisión
                      </button>
                    )}
                    {report.status !== "resolved" && (
                      <button 
                        onClick={() => handleStatusChange(report.id, "resolved")}
                        style={{ padding: "0.5rem 1rem", backgroundColor: "#10b981", color: "white", border: "none", borderRadius: "4px", cursor: "pointer", fontSize: "0.875rem", fontWeight: "500" }}
                      >
                        Marcar Resuelto
                      </button>
                    )}
                    {report.status !== "new" && (
                      <button 
                        onClick={() => handleStatusChange(report.id, "new")}
                        style={{ padding: "0.5rem 1rem", backgroundColor: "#6b7280", color: "white", border: "none", borderRadius: "4px", cursor: "pointer", fontSize: "0.875rem", fontWeight: "500" }}
                      >
                        Marcar como Nuevo
                      </button>
                    )}
                    <button 
                      onClick={() => handleDelete(report.id)}
                      style={{ padding: "0.5rem 1rem", backgroundColor: "#ef4444", color: "white", border: "none", borderRadius: "4px", cursor: "pointer", fontSize: "0.875rem", fontWeight: "500", marginLeft: "auto" }}
                    >
                      Eliminar
                    </button>
                  </div>
                </div>
              );
            })}
          </div>
        )}

        {totalPages > 1 && (
          <div style={{ display: "flex", justifyContent: "center", gap: "0.5rem", marginTop: "2rem" }}>
            <button
              onClick={() => setCurrentPage(p => Math.max(1, p - 1))}
              disabled={currentPage === 1}
              style={{ padding: "0.5rem 1rem", border: "1px solid #d1d5db", borderRadius: "4px", backgroundColor: currentPage === 1 ? "#f3f4f6" : "white", cursor: currentPage === 1 ? "not-allowed" : "pointer" }}
            >
              Anterior
            </button>
            <span style={{ display: "flex", alignItems: "center", fontSize: "0.875rem", color: "#4b5563" }}>
              Página {currentPage} de {totalPages}
            </span>
            <button
              onClick={() => setCurrentPage(p => Math.min(totalPages, p + 1))}
              disabled={currentPage === totalPages}
              style={{ padding: "0.5rem 1rem", border: "1px solid #d1d5db", borderRadius: "4px", backgroundColor: currentPage === totalPages ? "#f3f4f6" : "white", cursor: currentPage === totalPages ? "not-allowed" : "pointer" }}
            >
              Siguiente
            </button>
          </div>
        )}
      </main>
    </div>
  );
};

export default CitizenReportsModeration;
