import { useCallback, useEffect, useState } from "react";
import { Sidebar } from "../components/sidebar.tsx";
import { API_BASE_URL } from "../libs/config.ts";
import { ApiError, apiFetch } from "../libs/http.ts";
import "../App.css";

type VideoAsset = {
  id: string;
  url: string;
  platform: string;
  videoExternalId: string;
  title?: string;
};

const getThumbnailUrl = (platform: string, externalId: string) => {
  if (platform === "youtube") {
    return `https://img.youtube.com/vi/${externalId}/hqdefault.jpg`;
  }
  // No simple thumbnail for twitter, maybe a placeholder
  return ""; 
};

export default function VideosPage() {
  const [videos, setVideos] = useState<VideoAsset[]>([]);
  const [loading, setLoading] = useState<boolean>(true);
  const [error, setError] = useState<string | null>(null);
  const [showModal, setShowModal] = useState(false);
  const [saving, setSaving] = useState(false);
  const [newVideoUrl, setNewVideoUrl] = useState("");
  const [newVideoTitle, setNewVideoTitle] = useState("");
  
  const loadVideos = useCallback(async () => {
    try {
      setLoading(true);
      setError(null);
      const data = await apiFetch<VideoAsset[]>(`${API_BASE_URL}/api/v1/video?limit=100`, {
        method: "GET",
        credentials: "include",
      });
      setVideos(Array.isArray(data) ? data : []);
    } catch (err: unknown) {
      if (err instanceof ApiError && err.status === 401) {
        window.location.href = "/adminlogin";
        return;
      }
      setError(err instanceof Error ? err.message : "No se pudieron cargar los videos.");
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    void loadVideos();
  }, [loadVideos]);

  const handleAddVideo = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!newVideoUrl.trim()) return;

    try {
      setSaving(true);
      setError(null);
      await apiFetch(`${API_BASE_URL}/api/v1/video`, {
        method: "POST",
        credentials: "include",
        body: JSON.stringify({ url: newVideoUrl, title: newVideoTitle || undefined }),
        headers: { "Content-Type": "application/json" }
      });
      
      setShowModal(false);
      setNewVideoUrl("");
      setNewVideoTitle("");
      void loadVideos();
    } catch (err: unknown) {
      setError(err instanceof Error ? err.message : "Error al agregar video.");
    } finally {
      setSaving(false);
    }
  };

  const handleDelete = async (id: string) => {
    if (!window.confirm("¿Seguro que deseas eliminar este video?")) return;
    
    try {
      await apiFetch(`${API_BASE_URL}/api/v1/video/${id}`, {
        method: "DELETE",
        credentials: "include",
      });
      setVideos(videos.filter(v => v.id !== id));
    } catch (err: unknown) {
      alert("Error al eliminar video");
    }
  };

  return (
    <div className="layout-wrapper dashboard-layout layout">
      <aside className="sidebar">
        <Sidebar />
      </aside>

      <main className="main-content">
        <header className="mobile-header">
          <button
            type="button"
            className="menu-button"
            aria-label="Abrir menú"
          >
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              strokeWidth="2"
              strokeLinecap="round"
              strokeLinejoin="round"
              className="menu-icon"
            >
              <line x1="3" y1="12" x2="21" y2="12" />
              <line x1="3" y1="6" x2="21" y2="6" />
              <line x1="3" y1="18" x2="21" y2="18" />
            </svg>
          </button>
          <h1>Videos</h1>
        </header>

        <div className="dashboard-content">
          <header className="categories-header">
            <div>
              <h2 className="categories-page-title">Videos</h2>
              <p className="categories-page-desc">Gestiona los videos de YouTube o X para los artículos.</p>
            </div>
            <button
              type="button"
              className="categories-btn categories-btn-primary"
              onClick={() => setShowModal(true)}
            >
              + Agregar Video
            </button>
          </header>

          {error && <p className="categories-error">{error}</p>}

          {loading ? (
            <p>Cargando videos...</p>
          ) : videos.length === 0 ? (
            <div className="empty-state">
              <h3>No hay videos</h3>
              <p>Agrega videos de YouTube o X para usarlos en tus notas.</p>
            </div>
          ) : (
            <div className="categories-grid" style={{ gridTemplateColumns: "repeat(auto-fill, minmax(250px, 1fr))" }}>
              {videos.map(video => (
                <div key={video.id} className="categories-card" style={{ display: "flex", flexDirection: "column", gap: "12px", padding: 0, overflow: "hidden" }}>
                  {video.platform === "youtube" ? (
                    <img src={getThumbnailUrl(video.platform, video.videoExternalId)} alt={video.title} style={{ width: "100%", height: "150px", objectFit: "cover" }} />
                  ) : (
                    <div style={{ height: "150px", backgroundColor: "#f3f4f6", display: "flex", alignItems: "center", justifyContent: "center" }}>
                      <span style={{ color: "#6b7280", fontSize: "14px" }}>Video de {video.platform}</span>
                    </div>
                  )}
                  <div style={{ padding: "0 16px 16px" }}>
                    <h4 className="categories-card-title" style={{ marginBottom: "4px", whiteSpace: "nowrap", overflow: "hidden", textOverflow: "ellipsis" }}>
                      {video.title || video.videoExternalId}
                    </h4>
                    <p style={{ margin: "0 0 16px 0", fontSize: "12px", color: "#6b7280", wordBreak: "break-all", display: "-webkit-box", WebkitLineClamp: 2, WebkitBoxOrient: "vertical", overflow: "hidden" }}>
                      {video.url}
                    </p>
                    <div style={{ display: "flex", justifyContent: "flex-end" }}>
                      <button 
                        type="button" 
                        onClick={() => handleDelete(video.id)}
                        className="categories-btn categories-btn-danger"
                      >
                        Eliminar
                      </button>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          )}
        </div>
      </main>

      {showModal && (
        <div className="categories-modal-overlay" onClick={() => setShowModal(false)}>
          <div className="categories-modal" onClick={(e) => e.stopPropagation()}>
            <h2 className="categories-modal-title">Agregar Video</h2>
            <form className="categories-form" onSubmit={handleAddVideo}>
              <label>URL del Video</label>
              <input 
                type="url" 
                value={newVideoUrl} 
                onChange={e => setNewVideoUrl(e.target.value)} 
                placeholder="Ej. https://www.youtube.com/watch?v=..."
                required
              />
              <label>Título (Opcional)</label>
              <input 
                type="text" 
                value={newVideoTitle} 
                onChange={e => setNewVideoTitle(e.target.value)} 
                placeholder="Título descriptivo..."
              />
              <div className="categories-modal-actions">
                <button 
                  type="button" 
                  onClick={() => setShowModal(false)}
                  className="categories-btn categories-btn-secondary"
                >
                  Cancelar
                </button>
                <button 
                  type="submit" 
                  disabled={saving}
                  className="categories-btn categories-btn-primary"
                >
                  {saving ? "Guardando..." : "Guardar"}
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}
