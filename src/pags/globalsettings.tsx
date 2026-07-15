import { useState, useEffect, useRef } from "react";
import { Sidebar } from "../components/sidebar.tsx";
import { apiFetch } from "../libs/http.ts";
import { API_BASE_URL } from "../libs/config.ts";
import ImageSelectorModal from "../components/ImageSelectorModal.tsx";

export default function GlobalSettingsPage() {
  const [adsenseEnabled, setAdsenseEnabled] = useState(false);
  const [adsenseClientId, setAdsenseClientId] = useState("");
  const [commentBlocklist, setCommentBlocklist] = useState("");
  const [saving, setSaving] = useState(false);
  const [printEditionImageUrl, setPrintEditionImageUrl] = useState("");
  const [printEditionLink, setPrintEditionLink] = useState("");
  const [themeBackground, setThemeBackground] = useState("#ffffff");
  const [themeForeground, setThemeForeground] = useState("#20242b");
  const [themeNavbarBg, setThemeNavbarBg] = useState("#ffffff");
  const [themePrimaryColor, setThemePrimaryColor] = useState("#2563eb");
  const [themeFooterBg, setThemeFooterBg] = useState("#111827");
  const [themeFooterText, setThemeFooterText] = useState("#f9fafb");
  const [themeLiveBarBg, setThemeLiveBarBg] = useState("#dc2626");
  const [themeLiveBarText, setThemeLiveBarText] = useState("#ffffff");
  const [themeMutedText, setThemeMutedText] = useState("#6f7280");
  const [themeSurface, setThemeSurface] = useState("#ffffff");
  const [themeBorder, setThemeBorder] = useState("#e2e3e6");
  const [themeCardBorder, setThemeCardBorder] = useState("#c32f27");
  const [message, setMessage] = useState<{ type: "success" | "error"; text: string } | null>(null);
  const [showImageModal, setShowImageModal] = useState(false);
  const [uploadingPdf, setUploadingPdf] = useState(false);
  const pdfInputRef = useRef<HTMLInputElement | null>(null);

  useEffect(() => {
    fetchSettings();
  }, []);

  const fetchSettings = async () => {
    try {
      const data = await apiFetch<any>(`${API_BASE_URL}/api/v1/settings`, {
        credentials: "include",
      });
        setAdsenseEnabled(data.adsenseEnabled || false);
        setAdsenseClientId(data.adsenseClientId || "");
        if (Array.isArray(data.commentBlocklist)) {
          setCommentBlocklist(data.commentBlocklist.join(", "));
        }
        setPrintEditionImageUrl(data.printEditionImageUrl || "");
        setPrintEditionLink(data.printEditionLink || "");
        if (data.themeColors) {
          setThemeBackground(data.themeColors.background || "#ffffff");
          setThemeForeground(data.themeColors.foreground || "#20242b");
          setThemeNavbarBg(data.themeColors.navbarBg || "#ffffff");
          setThemePrimaryColor(data.themeColors.primaryColor || "#2563eb");
          setThemeFooterBg(data.themeColors.footerBg || "#111827");
          setThemeFooterText(data.themeColors.footerText || "#f9fafb");
          setThemeLiveBarBg(data.themeColors.liveBarBg || "#dc2626");
          setThemeLiveBarText(data.themeColors.liveBarText || "#ffffff");
          setThemeMutedText(data.themeColors.mutedText || "#6f7280");
          setThemeSurface(data.themeColors.surface || "#ffffff");
          setThemeBorder(data.themeColors.border || "#e2e3e6");
          setThemeCardBorder(data.themeColors.cardBorder || "#c32f27");
        }
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
        credentials: "include",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          adsenseEnabled,
          adsenseClientId,
          commentBlocklist: blocklistArray,
          printEditionImageUrl,
          printEditionLink,
          themeColors: {
            background: themeBackground,
            foreground: themeForeground,
            navbarBg: themeNavbarBg,
            primaryColor: themePrimaryColor,
            footerBg: themeFooterBg,
            footerText: themeFooterText,
            liveBarBg: themeLiveBarBg,
            liveBarText: themeLiveBarText,
            mutedText: themeMutedText,
            surface: themeSurface,
            border: themeBorder,
            cardBorder: themeCardBorder
          }
        }),
      });

      setMessage({ type: "success", text: "Configuración guardada correctamente." });
    } catch (err) {
      setMessage({ type: "error", text: "Error de red al guardar la configuración." });
    } finally {
      setSaving(false);
    }
  };

  const handlePdfUpload = async (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (!file) return;

    if (file.size > 10 * 1024 * 1024) { // 10MB limit for PDFs
      setMessage({ type: "error", text: "El PDF no debe superar los 10MB." });
      return;
    }

    setUploadingPdf(true);
    setMessage(null);

    try {
      const formData = new FormData();
      formData.append("image", file); // Backend expects 'image' field even for PDF

      const uploaded = await apiFetch<{ url?: string; message?: string }>(`${API_BASE_URL}/api/v1/image/upload`, {
        method: "POST",
        credentials: "include",
        body: formData,
      });

      if (uploaded.url) {
        let cleanUrl = uploaded.url;
        if (cleanUrl.startsWith("/api/v1/")) cleanUrl = cleanUrl.replace("/api/v1/", "");
        if (cleanUrl.startsWith("/")) cleanUrl = cleanUrl.substring(1);
        setPrintEditionLink(`${API_BASE_URL}/${cleanUrl}`);
        setMessage({ type: "success", text: "PDF subido correctamente." });
      }
    } catch (err: unknown) {
      setMessage({ type: "error", text: err instanceof Error ? err.message : "Error al subir el PDF." });
    } finally {
      setUploadingPdf(false);
      if (pdfInputRef.current) pdfInputRef.current.value = "";
    }
  };

  return (
    <div className="layout dashboard-layout">
      <aside className="sidebar">
        <Sidebar />
      </aside>

      <main className="content utility-page-content settings-content">
        <div style={{ padding: "24px", maxWidth: "800px", margin: "0 auto", paddingBottom: "100px" }}>
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
                  <div className="new-publication-card" style={{ padding: 0, border: 'none', background: 'transparent' }}>
                    <button
                      type="button"
                      className="new-publication-upload-box"
                      onClick={() => setShowImageModal(true)}
                    >
                      <span className="new-publication-upload-title">Seleccionar imagen</span>
                      <span className="new-publication-upload-subtitle">Subir archivo o buscar en galería</span>
                    </button>
                    {printEditionImageUrl && (
                      <div style={{ marginTop: '16px' }}>
                        <img
                          src={printEditionImageUrl}
                          alt="Vista previa del banner"
                          className="new-publication-image-preview"
                          style={{ maxWidth: '100%', borderRadius: '8px' }}
                        />
                        <button
                          type="button"
                          className="new-publication-clear-image"
                          onClick={() => setPrintEditionImageUrl("")}
                          style={{ marginTop: '8px' }}
                        >
                          Quitar imagen
                        </button>
                      </div>
                    )}
                  </div>
                </div>
                
                <div style={{ display: "flex", flexDirection: "column", gap: "8px" }}>
                  <label style={{ fontWeight: "bold", color: "var(--text-main)" }}>Enlace al hacer click o PDF</label>
                  <div style={{ display: "flex", gap: "12px", alignItems: "center" }}>
                    <input
                      type="text"
                      value={printEditionLink}
                      onChange={(e) => setPrintEditionLink(e.target.value)}
                      placeholder="https://ejemplo.com/edicion-impresa o subir PDF"
                      style={{
                        flex: 1,
                        padding: "12px",
                        borderRadius: "8px",
                        border: "1px solid var(--border)",
                        fontSize: "1rem",
                        background: "transparent",
                        color: "var(--text-main)"
                      }}
                    />
                    <button
                      type="button"
                      onClick={() => pdfInputRef.current?.click()}
                      disabled={uploadingPdf}
                      style={{
                        background: "var(--border)",
                        color: "var(--text-main)",
                        padding: "12px 16px",
                        borderRadius: "8px",
                        border: "none",
                        fontSize: "0.95rem",
                        fontWeight: "500",
                        cursor: uploadingPdf ? "not-allowed" : "pointer",
                        whiteSpace: "nowrap"
                      }}
                    >
                      {uploadingPdf ? "Subiendo..." : "Subir PDF"}
                    </button>
                    <input
                      type="file"
                      ref={pdfInputRef}
                      accept="application/pdf"
                      style={{ display: "none" }}
                      onChange={handlePdfUpload}
                    />
                  </div>
                </div>
              </div>
            </div>

            {/* Theme Colors Section */}
            <div style={{ background: "var(--surface)", padding: "24px", borderRadius: "8px", border: "1px solid var(--border)" }}>
              <h2 style={{ fontSize: "1.5rem", marginBottom: "16px", color: "var(--text-main)" }}>Colores del Tema</h2>
              <p style={{ marginBottom: "16px", color: "var(--text-muted)", fontSize: "0.9rem" }}>
                Personaliza los colores principales de la interfaz. Los cambios se aplicarán a toda la web pública.
              </p>
              
              <div style={{ display: "grid", gridTemplateColumns: "repeat(auto-fit, minmax(200px, 1fr))", gap: "16px" }}>
                <div style={{ display: "flex", flexDirection: "column", gap: "8px" }}>
                  <label style={{ fontWeight: "bold", color: "var(--text-main)" }}>Fondo de la página</label>
                  <div style={{ display: "flex", gap: "8px", alignItems: "center" }}>
                    <input type="color" value={themeBackground} onChange={e => setThemeBackground(e.target.value)} style={{ width: "40px", height: "40px", padding: "0", border: "none", borderRadius: "4px", cursor: "pointer" }} />
                    <input type="text" value={themeBackground} onChange={e => setThemeBackground(e.target.value)} style={{ padding: "8px", borderRadius: "4px", border: "1px solid var(--border)", width: "100px", background: "transparent", color: "var(--text-main)" }} />
                  </div>
                </div>

                <div style={{ display: "flex", flexDirection: "column", gap: "8px" }}>
                  <label style={{ fontWeight: "bold", color: "var(--text-main)" }}>Texto principal</label>
                  <div style={{ display: "flex", gap: "8px", alignItems: "center" }}>
                    <input type="color" value={themeForeground} onChange={e => setThemeForeground(e.target.value)} style={{ width: "40px", height: "40px", padding: "0", border: "none", borderRadius: "4px", cursor: "pointer" }} />
                    <input type="text" value={themeForeground} onChange={e => setThemeForeground(e.target.value)} style={{ padding: "8px", borderRadius: "4px", border: "1px solid var(--border)", width: "100px", background: "transparent", color: "var(--text-main)" }} />
                  </div>
                </div>

                <div style={{ display: "flex", flexDirection: "column", gap: "8px" }}>
                  <label style={{ fontWeight: "bold", color: "var(--text-main)" }}>Fondo de Navbar</label>
                  <div style={{ display: "flex", gap: "8px", alignItems: "center" }}>
                    <input type="color" value={themeNavbarBg} onChange={e => setThemeNavbarBg(e.target.value)} style={{ width: "40px", height: "40px", padding: "0", border: "none", borderRadius: "4px", cursor: "pointer" }} />
                    <input type="text" value={themeNavbarBg} onChange={e => setThemeNavbarBg(e.target.value)} style={{ padding: "8px", borderRadius: "4px", border: "1px solid var(--border)", width: "100px", background: "transparent", color: "var(--text-main)" }} />
                  </div>
                </div>

                <div style={{ display: "flex", flexDirection: "column", gap: "8px" }}>
                  <label style={{ fontWeight: "bold", color: "var(--text-main)" }}>Color Primario (Acentos)</label>
                  <div style={{ display: "flex", gap: "8px", alignItems: "center" }}>
                    <input type="color" value={themePrimaryColor} onChange={e => setThemePrimaryColor(e.target.value)} style={{ width: "40px", height: "40px", padding: "0", border: "none", borderRadius: "4px", cursor: "pointer" }} />
                    <input type="text" value={themePrimaryColor} onChange={e => setThemePrimaryColor(e.target.value)} style={{ padding: "8px", borderRadius: "4px", border: "1px solid var(--border)", width: "100px", background: "transparent", color: "var(--text-main)" }} />
                  </div>
                </div>

                <div style={{ display: "flex", flexDirection: "column", gap: "8px" }}>
                  <label style={{ fontWeight: "bold", color: "var(--text-main)" }}>Fondo del Footer</label>
                  <div style={{ display: "flex", gap: "8px", alignItems: "center" }}>
                    <input type="color" value={themeFooterBg} onChange={e => setThemeFooterBg(e.target.value)} style={{ width: "40px", height: "40px", padding: "0", border: "none", borderRadius: "4px", cursor: "pointer" }} />
                    <input type="text" value={themeFooterBg} onChange={e => setThemeFooterBg(e.target.value)} style={{ padding: "8px", borderRadius: "4px", border: "1px solid var(--border)", width: "100px", background: "transparent", color: "var(--text-main)" }} />
                  </div>
                </div>

                <div style={{ display: "flex", flexDirection: "column", gap: "8px" }}>
                  <label style={{ fontWeight: "bold", color: "var(--text-main)" }}>Texto del Footer</label>
                  <div style={{ display: "flex", gap: "8px", alignItems: "center" }}>
                    <input type="color" value={themeFooterText} onChange={e => setThemeFooterText(e.target.value)} style={{ width: "40px", height: "40px", padding: "0", border: "none", borderRadius: "4px", cursor: "pointer" }} />
                    <input type="text" value={themeFooterText} onChange={e => setThemeFooterText(e.target.value)} style={{ padding: "8px", borderRadius: "4px", border: "1px solid var(--border)", width: "100px", background: "transparent", color: "var(--text-main)" }} />
                  </div>
                </div>

                <div style={{ display: "flex", flexDirection: "column", gap: "8px" }}>
                  <label style={{ fontWeight: "bold", color: "var(--text-main)" }}>Barra Superior (Fondo)</label>
                  <div style={{ display: "flex", gap: "8px", alignItems: "center" }}>
                    <input type="color" value={themeLiveBarBg} onChange={e => setThemeLiveBarBg(e.target.value)} style={{ width: "40px", height: "40px", padding: "0", border: "none", borderRadius: "4px", cursor: "pointer" }} />
                    <input type="text" value={themeLiveBarBg} onChange={e => setThemeLiveBarBg(e.target.value)} style={{ padding: "8px", borderRadius: "4px", border: "1px solid var(--border)", width: "100px", background: "transparent", color: "var(--text-main)" }} />
                  </div>
                </div>

                <div style={{ display: "flex", flexDirection: "column", gap: "8px" }}>
                  <label style={{ fontWeight: "bold", color: "var(--text-main)" }}>Barra Superior (Texto)</label>
                  <div style={{ display: "flex", gap: "8px", alignItems: "center" }}>
                    <input type="color" value={themeLiveBarText} onChange={e => setThemeLiveBarText(e.target.value)} style={{ width: "40px", height: "40px", padding: "0", border: "none", borderRadius: "4px", cursor: "pointer" }} />
                    <input type="text" value={themeLiveBarText} onChange={e => setThemeLiveBarText(e.target.value)} style={{ padding: "8px", borderRadius: "4px", border: "1px solid var(--border)", width: "100px", background: "transparent", color: "var(--text-main)" }} />
                  </div>
                </div>

                <div style={{ display: "flex", flexDirection: "column", gap: "8px" }}>
                  <label style={{ fontWeight: "bold", color: "var(--text-main)" }}>Texto Secundario (Muted)</label>
                  <div style={{ display: "flex", gap: "8px", alignItems: "center" }}>
                    <input type="color" value={themeMutedText} onChange={e => setThemeMutedText(e.target.value)} style={{ width: "40px", height: "40px", padding: "0", border: "none", borderRadius: "4px", cursor: "pointer" }} />
                    <input type="text" value={themeMutedText} onChange={e => setThemeMutedText(e.target.value)} style={{ padding: "8px", borderRadius: "4px", border: "1px solid var(--border)", width: "100px", background: "transparent", color: "var(--text-main)" }} />
                  </div>
                </div>

                <div style={{ display: "flex", flexDirection: "column", gap: "8px" }}>
                  <label style={{ fontWeight: "bold", color: "var(--text-main)" }}>Superficies (Tarjetas)</label>
                  <div style={{ display: "flex", gap: "8px", alignItems: "center" }}>
                    <input type="color" value={themeSurface} onChange={e => setThemeSurface(e.target.value)} style={{ width: "40px", height: "40px", padding: "0", border: "none", borderRadius: "4px", cursor: "pointer" }} />
                    <input type="text" value={themeSurface} onChange={e => setThemeSurface(e.target.value)} style={{ padding: "8px", borderRadius: "4px", border: "1px solid var(--border)", width: "100px", background: "transparent", color: "var(--text-main)" }} />
                  </div>
                </div>

                <div style={{ display: "flex", flexDirection: "column", gap: "8px" }}>
                  <label style={{ fontWeight: "bold", color: "var(--text-main)" }}>Bordes</label>
                  <div style={{ display: "flex", gap: "8px", alignItems: "center" }}>
                    <input type="color" value={themeBorder} onChange={e => setThemeBorder(e.target.value)} style={{ width: "40px", height: "40px", padding: "0", border: "none", borderRadius: "4px", cursor: "pointer" }} />
                    <input type="text" value={themeBorder} onChange={e => setThemeBorder(e.target.value)} style={{ padding: "8px", borderRadius: "4px", border: "1px solid var(--border)", width: "100px", background: "transparent", color: "var(--text-main)" }} />
                  </div>
                </div>
              
                <div style={{ display: "flex", flexDirection: "column", gap: "8px" }}>
                  <label style={{ fontWeight: "bold", color: "var(--text-main)" }}>Bordes de Tarjetas</label>
                  <div style={{ display: "flex", gap: "8px", alignItems: "center" }}>
                    <input type="color" value={themeCardBorder} onChange={e => setThemeCardBorder(e.target.value)} style={{ width: "40px", height: "40px", padding: "0", border: "none", borderRadius: "4px", cursor: "pointer" }} />
                    <input type="text" value={themeCardBorder} onChange={e => setThemeCardBorder(e.target.value)} style={{ padding: "8px", borderRadius: "4px", border: "1px solid var(--border)", width: "100px", background: "transparent", color: "var(--text-main)" }} />
                  </div>
                </div>
              </div>
            </div>
            <div style={{ display: "flex", justifyContent: "flex-end", marginTop: "32px" }}>
              <button
                type="submit"
                disabled={saving}
                style={{
                  background: "#dc2626",
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

      {showImageModal && (
        <ImageSelectorModal
          onClose={() => setShowImageModal(false)}
          onSelect={(url) => {
            setPrintEditionImageUrl(url);
            setShowImageModal(false);
          }}
        />
      )}
    </div>
  );
}
