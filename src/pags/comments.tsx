import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import { Sidebar } from "../components/sidebar.tsx";
import { API_BASE_URL } from "../libs/config.ts";
import { ApiError, apiFetch } from "../libs/http.ts";

type Comment = {
  id: string;
  articleId: string;
  authorName: string;
  authorEmail: string;
  content: string;
  status: "pending" | "approved" | "rejected";
  createdAt: string;
};

type CommentsResponse = {
  comments: Comment[];
  total: number;
  page: number;
  pages: number;
};

const CommentsModeration = () => {
  const navigate = useNavigate();
  const [comments, setComments] = useState<Comment[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  
  const [statusFilter, setStatusFilter] = useState<string>("pending");
  const [currentPage, setCurrentPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);

  const fetchComments = async (page: number, status: string, signal?: AbortSignal) => {
    try {
      setLoading(true);
      setError(null);
      let url = `${API_BASE_URL}/api/v1/comments?page=${page}&limit=20`;
      if (status !== "all") {
        url += `&status=${status}`;
      }
      
      const data = await apiFetch<CommentsResponse>(url, {
        method: "GET",
        credentials: "include",
        signal,
      });

      setComments(data.comments || []);
      setTotalPages(data.pages || 1);
    } catch (err: unknown) {
      if (err instanceof Error && err.name === "AbortError") return;
      if (err instanceof ApiError && (err.status === 401 || err.status === 403)) {
        navigate("/adminlogin", { replace: true });
        return;
      }
      setError("Error al cargar comentarios.");
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    const controller = new AbortController();
    fetchComments(currentPage, statusFilter, controller.signal);
    return () => controller.abort();
  }, [currentPage, statusFilter, navigate]);

  const handleStatusChange = async (id: string, newStatus: "approved" | "rejected") => {
    try {
      await apiFetch(`${API_BASE_URL}/api/v1/comments/${id}/status`, {
        method: "PATCH",
        credentials: "include",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ status: newStatus }),
      });
      
      // Update local state instead of refetching for better UX
      setComments(prev => prev.map(c => c.id === id ? { ...c, status: newStatus } : c));
    } catch (err) {
      alert("Error al actualizar el estado del comentario.");
    }
  };

  const handleDelete = async (id: string) => {
    if (!window.confirm("¿Estás seguro de eliminar este comentario permanentemente?")) return;
    try {
      await apiFetch(`${API_BASE_URL}/api/v1/comments/${id}`, {
        method: "DELETE",
        credentials: "include",
      });
      setComments(prev => prev.filter(c => c.id !== id));
    } catch (err) {
      alert("Error al eliminar el comentario.");
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
            <h1 className="authors-users-title">Moderación de Comentarios</h1>
            <p className="authors-users-subtitle">Revisa y modera los comentarios de los usuarios.</p>
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
              <option value="pending">Pendientes</option>
              <option value="approved">Aprobados</option>
              <option value="rejected">Rechazados</option>
              <option value="all">Todos</option>
            </select>
          </div>
        </header>

        {loading ? (
          <p className="authors-users-info">Cargando comentarios...</p>
        ) : error ? (
          <p className="authors-users-info error">{error}</p>
        ) : comments.length === 0 ? (
          <p className="authors-users-info">No hay comentarios para mostrar.</p>
        ) : (
          <div style={{ display: "flex", flexDirection: "column", gap: "1rem", marginTop: "1rem" }}>
            {comments.map((comment) => (
              <div key={comment.id} style={{ 
                padding: "1.5rem", 
                backgroundColor: "#fff", 
                borderRadius: "8px", 
                border: "1px solid #e5e7eb",
                boxShadow: "0 1px 2px 0 rgba(0, 0, 0, 0.05)"
              }}>
                <div style={{ display: "flex", justifyContent: "space-between", alignItems: "flex-start", marginBottom: "1rem" }}>
                  <div>
                    <h3 style={{ fontSize: "1rem", fontWeight: "600", margin: 0 }}>{comment.authorName}</h3>
                    <p style={{ fontSize: "0.875rem", color: "#6b7280", margin: "0.25rem 0 0 0" }}>
                      {comment.authorEmail} • {new Date(comment.createdAt).toLocaleDateString("es-ES")} {new Date(comment.createdAt).toLocaleTimeString("es-ES")}
                    </p>
                    <p style={{ fontSize: "0.75rem", color: "#9ca3af", margin: "0.25rem 0 0 0" }}>
                      Artículo ID: {comment.articleId}
                    </p>
                  </div>
                  <div>
                    <span style={{ 
                      padding: "4px 8px", 
                      borderRadius: "12px", 
                      fontSize: "12px", 
                      fontWeight: "500",
                      backgroundColor: comment.status === "approved" ? "#d1fae5" : comment.status === "rejected" ? "#fee2e2" : "#fef3c7",
                      color: comment.status === "approved" ? "#065f46" : comment.status === "rejected" ? "#991b1b" : "#92400e"
                    }}>
                      {comment.status === "approved" ? "Aprobado" : comment.status === "rejected" ? "Rechazado" : "Pendiente"}
                    </span>
                  </div>
                </div>
                
                <p style={{ margin: "0 0 1.5rem 0", color: "#374151", whiteSpace: "pre-wrap", borderLeft: "3px solid #e5e7eb", paddingLeft: "1rem" }}>
                  {comment.content}
                </p>
                
                <div style={{ display: "flex", gap: "0.5rem" }}>
                  {comment.status !== "approved" && (
                    <button 
                      onClick={() => handleStatusChange(comment.id, "approved")}
                      style={{ padding: "0.5rem 1rem", backgroundColor: "#10b981", color: "white", border: "none", borderRadius: "4px", cursor: "pointer", fontSize: "0.875rem", fontWeight: "500" }}
                    >
                      Aprobar
                    </button>
                  )}
                  {comment.status !== "rejected" && (
                    <button 
                      onClick={() => handleStatusChange(comment.id, "rejected")}
                      style={{ padding: "0.5rem 1rem", backgroundColor: "#f59e0b", color: "white", border: "none", borderRadius: "4px", cursor: "pointer", fontSize: "0.875rem", fontWeight: "500" }}
                    >
                      Rechazar
                    </button>
                  )}
                  <button 
                    onClick={() => handleDelete(comment.id)}
                    style={{ padding: "0.5rem 1rem", backgroundColor: "#ef4444", color: "white", border: "none", borderRadius: "4px", cursor: "pointer", fontSize: "0.875rem", fontWeight: "500", marginLeft: "auto" }}
                  >
                    Eliminar
                  </button>
                </div>
              </div>
            ))}
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

export default CommentsModeration;
