import { useState, useEffect } from "react";
import { Sidebar } from "../components/sidebar.tsx";
import { apiFetch } from "../libs/http.ts";
import { API_BASE_URL } from "../libs/config.ts";

export default function GlobalSettingsPage() {
  const [adsenseEnabled, setAdsenseEnabled] = useState(false);
  const [adsenseClientId, setAdsenseClientId] = useState("");
  const [commentBlocklist, setCommentBlocklist] = useState("");
  const [saving, setSaving] = useState(false);
  const [printEditionImageUrl, setPrintEditionImageUrl] = useState("");
  const [printEditionLink, setPrintEditionLink] = useState("");
  const [message, setMessage] = useState<{ type: "success" | "error"; text: string } | null>(null);

  useEffect(() => {
    fetchSettings();
  }, []);

  const fetchSettings = async () => {
    try {
      const data = await apiFetch<any>(`${API_BASE_URL}/api/v1/settings`);
        setAdsenseEnabled(data.adsenseEnabled || false);
        setAdsenseClientId(data.adsenseClientId || "");
        if (Array.isArray(data.commentBlocklist)) {
          setCommentBlocklist(data.commentBlocklist.join(", "));
        }
        setPrintEditionImageUrl(data.printEditionImageUrl || "");
        setPrintEditionLink(data.printEditionLink || "");
    } catch (err) {
      console.error("Error fetching settings:", err);
    }
  };

  const handleSave = async (e: React.FormEvent) => {
    e.preventDefault();
    setSaving(true);
    setMessage(null);

    const blocklistArray = commentBlocklist
      .split(",")
      .map((s) => s.trim())
      .filter((s) => s.length > 0);

    try {
      await apiFetch<any>(`${API_BASE_URL}/api/v1/settings`, {
        method: "PATCH",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          adsenseEnabled,
          adsenseClientId,
          commentBlocklist: blocklistArray,
          printEditionImageUrl,
          printEditionLink,
        }),
      });

      setMessage({ type: "success", text: "Configuración guardada correctamente." });
    } catch (err) {
      setMessage({ type: "error", text: "Error de red al guardar la configuración." });
    } finally {
      setSaving(false);
    }
  };

  return (
    <div className="layout dashboard-layout">
      <aside className="sidebar">
        <Sidebar />
      </aside>

      <main className="content utility-page-content settings-content">
        <div style={{ padding: "24px", maxWidth: "800px", margin: "0 auto" }}>
          <h1 style={{ fontSize: "2rem", marginBottom: "24px", color: "var(--text-main)" }}>Ajustes del Sitio</h1>

          {message && (
            <div
              style={{
                padding: "16px",
                marginBottom: "24px",
                borderRadius: "8px",
                backgroundColor: message.type === "success" ? "#d4edda" : "#f8d7da",
                color: message.type === "success" ? "#155724" : "#721c24",
              }}
            >
              {message.text}
            </div>
          )}

          <form onSubmit={handleSave} style={{ display: "flex", flexDirection: "column", gap: "24px" }}>
            {/* AdSense Section */}
            <div style={{ background: "var(--surface)", padding: "24px", borderRadius: "8px", border: "1px solid var(--border)" }}>
              <h2 style={{ fontSize: "1.5rem", marginBottom: "16px", color: "var(--text-main)" }}>Google AdSense</h2>
              
              <label style={{ display: "flex", alignItems: "center", gap: "12px", marginBottom: "16px", cursor: "pointer", color: "var(--text-main)" }}>
                <input
                  type="checkbox"
                  checked={adsenseEnabled}
                  onChange={(e) => setAdsenseEnabled(e.target.checked)}
                  style={{ width: "20px", height: "20px" }}
                />
                Habilitar anuncios de Google AdSense
              </label>

              <div style={{ display: "flex", flexDirection: "column", gap: "8px" }}>
                <label style={{ fontWeight: "bold", color: "var(--text-main)" }}>Client ID (ej. ca-pub-123456789)</label>
                <input
                  type="text"
                  value={adsenseClientId}
                  onChange={(e) => setAdsenseClientId(e.target.value)}
                  placeholder="ca-pub-..."
                  disabled={!adsenseEnabled}
                  style={{
                    padding: "12px",
                    borderRadius: "8px",
                    border: "1px solid var(--border)",
                    fontSize: "1rem",
                    background: !adsenseEnabled ? "var(--bg-main)" : "transparent",
                    color: "var(--text-main)"
                  }}
                />
              </div>
            </div>

            {/* Comment Moderation Section */}
            <div style={{ background: "var(--surface)", padding: "24px", borderRadius: "8px", border: "1px solid var(--border)" }}>
              <h2 style={{ fontSize: "1.5rem", marginBottom: "16px", color: "var(--text-main)" }}>Moderación de Comentarios</h2>
              <p style={{ marginBottom: "16px", color: "var(--text-muted)", fontSize: "0.9rem" }}>
                Palabras prohibidas (separadas por comas). Si un usuario intenta enviar un comentario que contenga alguna de estas palabras, el comentario será bloqueado.
              </p>
              
              <div style={{ display: "flex", flexDirection: "column", gap: "8px" }}>
                <label style={{ fontWeight: "bold", color: "var(--text-main)" }}>Blocklist</label>
                <textarea
                  value={commentBlocklist}
                  onChange={(e) => setCommentBlocklist(e.target.value)}
                  placeholder="palabra1, groseria2, insulto3..."
                  rows={4}
                  style={{
                    padding: "12px",
                    borderRadius: "8px",
                    border: "1px solid var(--border)",
                    fontSize: "1rem",
                    resize: "vertical",
                    color: "var(--text-main)",
                    background: "transparent"
                  }}
                />
              </div>
            </div>

            {/* Print Edition Banner Section */}
            <div style={{ background: "var(--surface)", padding: "24px", borderRadius: "8px", border: "1px solid var(--border)" }}>
              <h2 style={{ fontSize: "1.5rem", marginBottom: "16px", color: "var(--text-main)" }}>Banner Edición Impresa</h2>
              <p style={{ marginBottom: "16px", color: "var(--text-muted)", fontSize: "0.9rem" }}>
                Configura la imagen y el enlace del banner de "Edición Impresa" que aparece en la página principal.
              </p>
              
              <div style={{ display: "flex", flexDirection: "column", gap: "16px" }}>
                <div style={{ display: "flex", flexDirection: "column", gap: "8px" }}>
                  <label style={{ fontWeight: "bold", color: "var(--text-main)" }}>URL de la Imagen</label>
                  <input
                    type="text"
                    value={printEditionImageUrl}
                    onChange={(e) => setPrintEditionImageUrl(e.target.value)}
                    placeholder="https://ejemplo.com/imagen.jpg"
                    style={{
                      padding: "12px",
                      borderRadius: "8px",
                      border: "1px solid var(--border)",
                      fontSize: "1rem",
                      background: "transparent",
                      color: "var(--text-main)"
                    }}
                  />
                </div>
                
                <div style={{ display: "flex", flexDirection: "column", gap: "8px" }}>
                  <label style={{ fontWeight: "bold", color: "var(--text-main)" }}>Enlace al hacer click</label>
                  <input
                    type="text"
                    value={printEditionLink}
                    onChange={(e) => setPrintEditionLink(e.target.value)}
                    placeholder="https://ejemplo.com/edicion-impresa"
                    style={{
                      padding: "12px",
                      borderRadius: "8px",
                      border: "1px solid var(--border)",
                      fontSize: "1rem",
                      background: "transparent",
                      color: "var(--text-main)"
                    }}
                  />
                </div>
              </div>
            </div>

            <div style={{ display: "flex", justifyContent: "flex-end", marginTop: "16px" }}>
              <button
                type="submit"
                disabled={saving}
                style={{
                  background: "#8b1f1f",
                  color: "white",
                  padding: "12px 24px",
                  borderRadius: "8px",
                  border: "none",
                  fontSize: "1.1rem",
                  fontWeight: "bold",
                  cursor: saving ? "not-allowed" : "pointer",
                  opacity: saving ? 0.7 : 1,
                }}
              >
                {saving ? "Guardando..." : "Guardar Cambios"}
              </button>
            </div>
          </form>
        </div>
      </main>
    </div>
  );
}
