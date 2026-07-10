import { useEffect, useState } from "react";
import { API_BASE_URL } from "../libs/config.ts";
import { apiFetch } from "../libs/http.ts";

type Comment = {
  id: string;
  authorName: string;
  content: string;
  createdAt: string;
};

type CommentsSectionProps = {
  articleId: string;
  allowComments?: boolean;
};

export const CommentsSection = ({ articleId, allowComments = true }: CommentsSectionProps) => {
  const [comments, setComments] = useState<Comment[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const [authorName, setAuthorName] = useState("");
  const [authorEmail, setAuthorEmail] = useState("");
  const [content, setContent] = useState("");
  const [submitting, setSubmitting] = useState(false);
  const [submitError, setSubmitError] = useState<string | null>(null);
  const [submitSuccess, setSubmitSuccess] = useState(false);

  useEffect(() => {
    if (!allowComments) {
      setLoading(false);
      return;
    }

    const controller = new AbortController();

    const fetchComments = async () => {
      try {
        setLoading(true);
        const data = await apiFetch<{ data: Comment[] }>(
          `${API_BASE_URL}/api/v1/comments?articleId=${articleId}&status=approved`,
          { signal: controller.signal }
        );
        setComments(data.data || []);
      } catch (err: any) {
        if (err.name === "AbortError") return;
        setError("Error al cargar comentarios.");
      } finally {
        setLoading(false);
      }
    };

    fetchComments();
    return () => controller.abort();
  }, [articleId, allowComments]);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setSubmitting(true);
    setSubmitError(null);
    setSubmitSuccess(false);

    try {
      await apiFetch(`${API_BASE_URL}/api/v1/comments`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          articleId,
          authorName,
          authorEmail,
          content,
        }),
      });

      setSubmitSuccess(true);
      setAuthorName("");
      setAuthorEmail("");
      setContent("");
    } catch (err: any) {
      setSubmitError(err.message || "No se pudo enviar el comentario.");
    } finally {
      setSubmitting(false);
    }
  };

  if (!allowComments) {
    return (
      <div className="comments-section" style={{ marginTop: "2rem", padding: "1.5rem", background: "#f9fafb", borderRadius: "8px" }}>
        <p style={{ textAlign: "center", color: "#6b7280" }}>Los comentarios están desactivados para este artículo.</p>
      </div>
    );
  }

  return (
    <div className="comments-section" style={{ marginTop: "3rem", borderTop: "1px solid #e5e7eb", paddingTop: "2rem" }}>
      <h3 style={{ fontSize: "1.5rem", fontWeight: "bold", marginBottom: "1.5rem" }}>Comentarios</h3>

      <div className="comments-list" style={{ marginBottom: "2.5rem" }}>
        {loading ? (
          <p>Cargando comentarios...</p>
        ) : error ? (
          <p style={{ color: "var(--theme-primary-color)" }}>{error}</p>
        ) : comments.length === 0 ? (
          <p style={{ color: "#6b7280" }}>No hay comentarios aún. ¡Sé el primero en comentar!</p>
        ) : (
          <div style={{ display: "flex", flexDirection: "column", gap: "1rem" }}>
            {comments.map((c) => (
              <div key={c.id} style={{ padding: "1rem", backgroundColor: "#f9fafb", borderRadius: "8px" }}>
                <div style={{ display: "flex", justifyContent: "space-between", marginBottom: "0.5rem" }}>
                  <span style={{ fontWeight: "600" }}>{c.authorName}</span>
                  <span style={{ fontSize: "0.875rem", color: "#6b7280" }}>
                    {new Date(c.createdAt).toLocaleDateString("es-ES")}
                  </span>
                </div>
                <p style={{ color: "#374151", margin: 0, whiteSpace: "pre-wrap" }}>{c.content}</p>
              </div>
            ))}
          </div>
        )}
      </div>

      <div className="comments-form" style={{ backgroundColor: "#fff", padding: "1.5rem", border: "1px solid #e5e7eb", borderRadius: "8px" }}>
        <h4 style={{ fontSize: "1.25rem", fontWeight: "600", marginBottom: "1rem" }}>Deja un comentario</h4>
        
        {submitSuccess ? (
          <div style={{ padding: "1rem", backgroundColor: "#d1fae5", color: "#065f46", borderRadius: "6px" }}>
            ¡Gracias por comentar! Tu comentario está siendo revisado por moderación.
          </div>
        ) : (
          <form onSubmit={handleSubmit} style={{ display: "flex", flexDirection: "column", gap: "1rem" }}>
            <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: "1rem" }}>
              <div style={{ display: "flex", flexDirection: "column", gap: "0.5rem" }}>
                <label htmlFor="commentName" style={{ fontSize: "0.875rem", fontWeight: "500" }}>Nombre *</label>
                <input
                  id="commentName"
                  type="text"
                  required
                  value={authorName}
                  onChange={(e) => setAuthorName(e.target.value)}
                  style={{ padding: "0.5rem", border: "1px solid #d1d5db", borderRadius: "4px" }}
                  disabled={submitting}
                />
              </div>
              <div style={{ display: "flex", flexDirection: "column", gap: "0.5rem" }}>
                <label htmlFor="commentEmail" style={{ fontSize: "0.875rem", fontWeight: "500" }}>Correo electrónico *</label>
                <input
                  id="commentEmail"
                  type="email"
                  required
                  value={authorEmail}
                  onChange={(e) => setAuthorEmail(e.target.value)}
                  style={{ padding: "0.5rem", border: "1px solid #d1d5db", borderRadius: "4px" }}
                  disabled={submitting}
                />
              </div>
            </div>
            
            <div style={{ display: "flex", flexDirection: "column", gap: "0.5rem" }}>
              <label htmlFor="commentContent" style={{ fontSize: "0.875rem", fontWeight: "500" }}>Comentario *</label>
              <textarea
                id="commentContent"
                required
                value={content}
                onChange={(e) => setContent(e.target.value)}
                style={{ padding: "0.5rem", border: "1px solid #d1d5db", borderRadius: "4px", minHeight: "100px", resize: "vertical" }}
                disabled={submitting}
              />
            </div>
            
            {submitError && <p style={{ color: "var(--theme-primary-color)", margin: 0 }}>{submitError}</p>}
            
            <button
              type="submit"
              disabled={submitting}
              style={{
                alignSelf: "flex-start",
                padding: "0.5rem 1.5rem",
                backgroundColor: "#0ea5e9",
                color: "white",
                border: "none",
                borderRadius: "4px",
                fontWeight: "500",
                cursor: submitting ? "not-allowed" : "pointer",
                opacity: submitting ? 0.7 : 1
              }}
            >
              {submitting ? "Enviando..." : "Enviar comentario"}
            </button>
          </form>
        )}
      </div>
    </div>
  );
};
