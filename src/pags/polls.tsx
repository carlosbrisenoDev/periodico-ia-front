import { useCallback, useEffect, useState } from "react";
import { Sidebar } from "../components/sidebar.tsx";
import ImageSelectorModal from "../components/ImageSelectorModal.tsx";
import { API_BASE_URL } from "../libs/config.ts";
import { ApiError, apiFetch } from "../libs/http.ts";
import type { Poll, PollOption } from "../libs/types.ts";
import "../App.css";
import "./polls.css";

export default function PollsPage() {
  const [polls, setPolls] = useState<Poll[]>([]);
  const [loading, setLoading] = useState<boolean>(true);
  const [error, setError] = useState<string | null>(null);
  
  // Form modal state
  const [showFormModal, setShowFormModal] = useState<boolean>(false);
  const [editingId, setEditingId] = useState<string | null>(null);
  const [title, setTitle] = useState<string>("");
  const [description, setDescription] = useState<string>("");
  const [imageUrl, setImageUrl] = useState<string>("");
  const [allowMultiple, setAllowMultiple] = useState<boolean>(false);
  const [allowOther, setAllowOther] = useState<boolean>(false);
  const [active, setActive] = useState<boolean>(true);
  const [order, setOrder] = useState<number>(0);
  const [options, setOptions] = useState<PollOption[]>([]);
  const [saving, setSaving] = useState<boolean>(false);

  // Image selector modal state
  const [imageSelectorTarget, setImageSelectorTarget] = useState<'poll' | number | null>(null);

  // Responses viewer modal state
  const [selectedPollForResponses, setSelectedPollForResponses] = useState<Poll | null>(null);

  const loadPolls = useCallback(async () => {
    try {
      setLoading(true);
      setError(null);
      const data = await apiFetch<Poll[]>(`${API_BASE_URL}/api/v1/polls`, {
        method: "GET",
        credentials: "include",
      });
      setPolls(Array.isArray(data) ? data : []);
    } catch (err: unknown) {
      if (err instanceof ApiError && err.status === 401) {
        window.location.href = "/adminlogin";
        return;
      }
      setError(err instanceof Error ? err.message : "No se pudieron cargar las encuestas.");
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    void loadPolls();
  }, [loadPolls]);

  const openCreateModal = () => {
    setEditingId(null);
    setTitle("");
    setDescription("");
    setImageUrl("");
    setAllowMultiple(false);
    setAllowOther(false);
    setActive(true);
    setOrder(0);
    setOptions([
      { id: crypto.randomUUID(), text: "Opción 1", votes: 0 },
      { id: crypto.randomUUID(), text: "Opción 2", votes: 0 },
    ]);
    setShowFormModal(true);
  };

  const openEditModal = (poll: Poll) => {
    setEditingId(poll.id);
    setTitle(poll.title || "");
    setDescription(poll.description || "");
    setImageUrl(poll.imageUrl || "");
    setAllowMultiple(Boolean(poll.allowMultiple));
    setAllowOther(Boolean(poll.allowOther));
    setActive(poll.active !== undefined ? poll.active : true);
    setOrder(poll.order || 0);
    setOptions((poll.options || []).map(o => ({ ...o })));
    setShowFormModal(true);
  };

  const handleAddOption = () => {
    setOptions(prev => [...prev, { id: crypto.randomUUID(), text: `Opción ${prev.length + 1}`, votes: 0 }]);
  };

  const handleRemoveOption = (index: number) => {
    if (options.length <= 1) {
      alert("La encuesta debe tener al menos una opción.");
      return;
    }
    setOptions(prev => prev.filter((_, i) => i !== index));
  };

  const handleOptionTextChange = (index: number, val: string) => {
    setOptions(prev => prev.map((opt, i) => i === index ? { ...opt, text: val } : opt));
  };

  const handleImageSelected = (url: string) => {
    if (imageSelectorTarget === 'poll') {
      setImageUrl(url);
    } else if (typeof imageSelectorTarget === 'number') {
      setOptions(prev => prev.map((opt, i) => i === imageSelectorTarget ? { ...opt, imageUrl: url } : opt));
    }
    setImageSelectorTarget(null);
  };

  const handleRemoveOptionImage = (index: number) => {
    setOptions(prev => prev.map((opt, i) => i === index ? { ...opt, imageUrl: undefined } : opt));
  };

  const handleSave = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!title.trim()) {
      alert("El título de la encuesta es requerido.");
      return;
    }
    if (options.some(o => !o.text.trim())) {
      alert("Todas las opciones deben tener un texto válido.");
      return;
    }

    try {
      setSaving(true);
      setError(null);

      const payload = {
        title: title.trim(),
        description: description.trim() || undefined,
        imageUrl: imageUrl.trim() || undefined,
        allowMultiple,
        allowOther,
        active,
        order: Number(order) || 0,
        options
      };

      const url = editingId ? `${API_BASE_URL}/api/v1/polls/${editingId}` : `${API_BASE_URL}/api/v1/polls`;
      const method = editingId ? "PATCH" : "POST";

      await apiFetch(url, {
        method,
        credentials: "include",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      });

      setShowFormModal(false);
      void loadPolls();
    } catch (err: unknown) {
      alert(err instanceof Error ? err.message : "Error al guardar la encuesta.");
    } finally {
      setSaving(false);
    }
  };

  const handleDelete = async (id: string) => {
    if (!window.confirm("¿Seguro que deseas eliminar esta encuesta definitivamente?")) return;

    try {
      await apiFetch(`${API_BASE_URL}/api/v1/polls/${id}`, {
        method: "DELETE",
        credentials: "include",
      });
      setPolls(prev => prev.filter(p => p.id !== id));
    } catch (err: unknown) {
      alert("Error al eliminar la encuesta");
      console.error(err);
    }
  };

  const getTotalVotes = (poll: Poll) => {
    const optsVotes = (poll.options || []).reduce((sum, opt) => sum + (opt.votes || 0), 0);
    return optsVotes + (poll.otherVotes || 0);
  };

  return (
    <div className="layout-wrapper dashboard-layout layout">
      <aside className="sidebar">
        <Sidebar />
      </aside>

      <main className="main-content">
        <header className="mobile-header">
          <button type="button" className="menu-button" aria-label="Abrir menú">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className="menu-icon">
              <line x1="3" y1="12" x2="21" y2="12" />
              <line x1="3" y1="6" x2="21" y2="6" />
              <line x1="3" y1="18" x2="21" y2="18" />
            </svg>
          </button>
          <h1>Encuestas</h1>
        </header>

        <div className="dashboard-content">
          <div className="polls-admin-header">
            <div>
              <h2 className="categories-page-title">Encuestas y Sondeos</h2>
              <p className="categories-page-desc">
                Crea sondeos de opinión con selección única o múltiple y campos abiertos personalizados para el Home del periódico.
              </p>
            </div>
            <button type="button" className="entries-new-button" onClick={openCreateModal}>
              + Nueva Encuesta
            </button>
          </div>

          {error && <p className="categories-error">{error}</p>}

          {loading ? (
            <p>Cargando encuestas...</p>
          ) : polls.length === 0 ? (
            <div className="empty-state">
              <h3>No hay encuestas creadas</h3>
              <p>Haz clic en "+ Nueva Encuesta" para lanzar tu primer sondeo interactivo con la comunidad.</p>
            </div>
          ) : (
            <div className="polls-admin-grid">
              {polls.map((poll) => {
                const total = getTotalVotes(poll);
                return (
                  <div key={poll.id} className="poll-card-admin">
                    {poll.imageUrl ? (
                      <img src={poll.imageUrl} alt={poll.title} className="poll-card-header-img" />
                    ) : null}
                    <div className="poll-card-body">
                      <div className="poll-badges-row">
                        <span className={`poll-badge ${poll.active ? "active" : "inactive"}`}>
                          {poll.active ? "Activa" : "Inactiva"}
                        </span>
                        <span className="poll-badge type">
                          {poll.allowMultiple ? "Múltiple" : "Selección Única"}
                        </span>
                        {poll.allowOther && <span className="poll-badge other">Con opción 'Otro'</span>}
                      </div>

                      <h3 className="poll-title-text">{poll.title}</h3>
                      {poll.description && <p className="poll-desc-text">{poll.description}</p>}

                      <div className="poll-results-preview">
                        <div style={{ fontSize: "0.85rem", fontWeight: 700, color: "#475569", marginBottom: "4px" }}>
                          Total de Votos: {total}
                        </div>
                        {poll.options.map((opt) => {
                          const percent = total > 0 ? Math.round(((opt.votes || 0) / total) * 100) : 0;
                          return (
                            <div key={opt.id} className="poll-result-item">
                              <div className="poll-result-header">
                                <span>{opt.text}</span>
                                <span>{opt.votes || 0} ({percent}%)</span>
                              </div>
                              <div className="poll-result-bar-bg">
                                <div className="poll-result-bar-fill" style={{ width: `${percent}%` }} />
                              </div>
                            </div>
                          );
                        })}
                        {poll.allowOther && (
                          <div className="poll-result-item" style={{ marginTop: "6px" }}>
                            <div className="poll-result-header">
                              <span>Otro (Respuesta escrita)</span>
                              <span>{poll.otherVotes || 0} ({total > 0 ? Math.round(((poll.otherVotes || 0) / total) * 100) : 0}%)</span>
                            </div>
                            <div className="poll-result-bar-bg">
                              <div className="poll-result-bar-fill" style={{ width: `${total > 0 ? Math.round(((poll.otherVotes || 0) / total) * 100) : 0}%`, background: "#9333ea" }} />
                            </div>
                            {poll.otherResponses && poll.otherResponses.length > 0 && (
                              <button
                                type="button"
                                onClick={() => setSelectedPollForResponses(poll)}
                                style={{ marginTop: "6px", background: "none", border: "none", color: "var(--theme-primary-color, #2563eb)", textDecoration: "underline", fontSize: "0.85rem", cursor: "pointer", textAlign: "left", padding: 0 }}
                              >
                                Ver listado de {poll.otherResponses.length} respuesta(s) en texto &rarr;
                              </button>
                            )}
                          </div>
                        )}
                      </div>

                      <div className="poll-actions-footer">
                        <button type="button" className="poll-btn-secondary" onClick={() => openEditModal(poll)}>
                          Editar
                        </button>
                        <button type="button" className="poll-btn-danger" onClick={() => handleDelete(poll.id)}>
                          Eliminar
                        </button>
                      </div>
                    </div>
                  </div>
                );
              })}
            </div>
          )}
        </div>
      </main>

      {/* Modal para ver respuestas escritas en "Otro" */}
      {selectedPollForResponses && (
        <div className="categories-modal-overlay" onClick={() => setSelectedPollForResponses(null)}>
          <div className="poll-modal-content" onClick={(e) => e.stopPropagation()}>
            <h3 style={{ marginTop: 0, marginBottom: "8px", fontSize: "1.25rem", color: "#0f172a" }}>
              Respuestas escritas en "Otro"
            </h3>
            <p style={{ fontSize: "0.9rem", color: "#64748b", marginBottom: "16px" }}>
              Encuesta: <strong>{selectedPollForResponses.title}</strong>
            </p>
            <div className="poll-responses-list">
              {(selectedPollForResponses.otherResponses || []).map((resp, idx) => (
                <div key={idx} className="poll-response-box">
                  <div>"{resp.text}"</div>
                  <div className="poll-response-meta">
                    {resp.createdAt ? new Date(resp.createdAt).toLocaleString("es-ES") : "Fecha no registrada"}
                  </div>
                </div>
              ))}
            </div>
            <div style={{ display: "flex", justifyContent: "flex-end", marginTop: "20px" }}>
              <button type="button" className="poll-btn-secondary" onClick={() => setSelectedPollForResponses(null)}>
                Cerrar
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Modal de Crear / Editar Encuesta */}
      {showFormModal && (
        <div className="categories-modal-overlay" onClick={() => setShowFormModal(false)}>
          <div className="poll-modal-content" onClick={(e) => e.stopPropagation()}>
            <h2 style={{ marginTop: 0, marginBottom: "20px", fontSize: "1.5rem", fontWeight: 700 }}>
              {editingId ? "Editar Encuesta" : "Nueva Encuesta"}
            </h2>
            <form onSubmit={handleSave}>
              <div className="poll-form-group">
                <label className="poll-form-label">Pregunta o Título del Sondeo *</label>
                <input
                  type="text"
                  className="poll-form-input"
                  placeholder="Ej: ¿Qué opinas sobre el nuevo plan de vialidad en el centro?"
                  value={title}
                  onChange={(e) => setTitle(e.target.value)}
                  required
                />
              </div>

              <div className="poll-form-group">
                <label className="poll-form-label">Descripción o Contexto (Opcional)</label>
                <textarea
                  className="poll-form-textarea"
                  rows={2}
                  placeholder="Brinda un breve resumen sobre por qué se realiza este sondeo..."
                  value={description}
                  onChange={(e) => setDescription(e.target.value)}
                />
              </div>

              <div className="poll-form-group">
                <label className="poll-form-label">Imagen de la Encuesta (Opcional)</label>
                {imageUrl ? (
                  <div style={{ display: "flex", alignItems: "center", gap: "12px", marginTop: "8px" }}>
                    <img src={imageUrl} alt="preview" style={{ width: "120px", height: "70px", objectFit: "cover", borderRadius: "6px", border: "1px solid #cbd5e1" }} />
                    <button type="button" className="poll-btn-secondary" onClick={() => setImageSelectorTarget('poll')}>
                      Cambiar Imagen
                    </button>
                    <button type="button" className="poll-btn-danger" onClick={() => setImageUrl("")}>
                      Quitar
                    </button>
                  </div>
                ) : (
                  <button type="button" className="poll-btn-secondary" onClick={() => setImageSelectorTarget('poll')} style={{ marginTop: "6px" }}>
                    + Seleccionar Imagen
                  </button>
                )}
              </div>

              <div style={{ display: "grid", gridTemplateColumns: "repeat(auto-fit, minmax(200px, 1fr))", gap: "12px", margin: "16px 0" }}>
                <label className="poll-form-checkbox-row">
                  <input
                    type="checkbox"
                    className="poll-form-checkbox"
                    checked={active}
                    onChange={(e) => setActive(e.target.checked)}
                  />
                  <div>
                    <strong style={{ display: "block", fontSize: "0.9rem" }}>Encuesta Activa</strong>
                    <span style={{ fontSize: "0.75rem", color: "#64748b" }}>Mostrar inmediatamente en el Home</span>
                  </div>
                </label>

                <label className="poll-form-checkbox-row">
                  <input
                    type="checkbox"
                    className="poll-form-checkbox"
                    checked={allowMultiple}
                    onChange={(e) => setAllowMultiple(e.target.checked)}
                  />
                  <div>
                    <strong style={{ display: "block", fontSize: "0.9rem" }}>Selección Múltiple</strong>
                    <span style={{ fontSize: "0.75rem", color: "#64748b" }}>Permitir votar por más de una opción</span>
                  </div>
                </label>

                <label className="poll-form-checkbox-row">
                  <input
                    type="checkbox"
                    className="poll-form-checkbox"
                    checked={allowOther}
                    onChange={(e) => setAllowOther(e.target.checked)}
                  />
                  <div>
                    <strong style={{ display: "block", fontSize: "0.9rem" }}>Campo "Otro"</strong>
                    <span style={{ fontSize: "0.75rem", color: "#64748b" }}>Permitir al usuario escribir una respuesta</span>
                  </div>
                </label>
              </div>

              <div className="poll-form-group">
                <label className="poll-form-label" style={{ borderBottom: "1px solid #e2e8f0", paddingBottom: "8px" }}>
                  Opciones de Respuesta ({options.length})
                </label>
                <div className="poll-option-builder">
                  {options.map((opt, index) => (
                    <div key={opt.id} className="poll-option-row">
                      <span style={{ fontWeight: 700, color: "#64748b", width: "24px" }}>#{index + 1}</span>
                      <input
                        type="text"
                        className="poll-option-input"
                        placeholder="Escribe el texto de esta opción..."
                        value={opt.text}
                        onChange={(e) => handleOptionTextChange(index, e.target.value)}
                        required
                      />

                      {opt.imageUrl ? (
                        <div style={{ display: "flex", alignItems: "center", gap: "8px" }}>
                          <img src={opt.imageUrl} alt="" className="poll-option-img-preview" />
                          <button
                            type="button"
                            onClick={() => handleRemoveOptionImage(index)}
                            style={{ background: "#fef2f2", border: "1px solid #fecaca", color: "#dc2626", borderRadius: "4px", padding: "4px 8px", cursor: "pointer", fontSize: "0.8rem" }}
                            title="Quitar imagen de esta opción"
                          >
                            Quitar Img
                          </button>
                        </div>
                      ) : (
                        <button
                          type="button"
                          onClick={() => setImageSelectorTarget(index)}
                          style={{ background: "#f8fafc", border: "1px dashed #94a3b8", color: "#475569", borderRadius: "6px", padding: "6px 12px", cursor: "pointer", fontSize: "0.85rem", whiteSpace: "nowrap" }}
                        >
                          + Img Opción
                        </button>
                      )}

                      <button
                        type="button"
                        onClick={() => handleRemoveOption(index)}
                        style={{ background: "transparent", border: "none", color: "#ef4444", fontSize: "1.2rem", cursor: "pointer", padding: "4px 8px" }}
                        title="Eliminar opción"
                      >
                        &times;
                      </button>
                    </div>
                  ))}
                </div>
                <button
                  type="button"
                  onClick={handleAddOption}
                  style={{ marginTop: "12px", background: "#f1f5f9", border: "1px solid #cbd5e1", color: "#334155", padding: "8px 16px", borderRadius: "6px", fontWeight: 600, cursor: "pointer" }}
                >
                  + Añadir Opción
                </button>
              </div>

              <div className="poll-form-group" style={{ maxWidth: "160px" }}>
                <label className="poll-form-label">Orden de Despliegue</label>
                <input
                  type="number"
                  className="poll-form-input"
                  value={order}
                  onChange={(e) => setOrder(parseInt(e.target.value, 10) || 0)}
                />
              </div>

              <div style={{ display: "flex", justifyContent: "flex-end", gap: "12px", marginTop: "28px", paddingTop: "16px", borderTop: "1px solid #e2e8f0" }}>
                <button type="button" className="poll-btn-secondary" onClick={() => setShowFormModal(false)}>
                  Cancelar
                </button>
                <button type="submit" className="poll-btn-primary" disabled={saving}>
                  {saving ? "Guardando..." : "Guardar Encuesta"}
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      {/* Selector de Imágenes */}
      {imageSelectorTarget !== null && (
        <ImageSelectorModal
          onSelect={handleImageSelected}
          onClose={() => setImageSelectorTarget(null)}
        />
      )}
    </div>
  );
}
