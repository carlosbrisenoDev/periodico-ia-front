import { useState } from "react";
import { API_BASE_URL, MAX_UPLOAD_MB } from "../libs/config.ts";
import { apiFetch } from "../libs/http.ts";

export const CitizenReportForm = ({ prefillUser }: { prefillUser?: { username?: string; email?: string } }) => {
  const [name, setName] = useState(prefillUser?.username || "");
  const [email, setEmail] = useState(prefillUser?.email || "");
  const [phone, setPhone] = useState("");
  const [subject, setSubject] = useState("");
  const [description, setDescription] = useState("");
  const [imageFile, setImageFile] = useState<File | null>(null);
  
  const [submitting, setSubmitting] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [success, setSuccess] = useState(false);


  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setSubmitting(true);
    setError(null);
    setSuccess(false);

    try {
      let imageUrl = "";

      if (imageFile) {
        if (imageFile.size > MAX_UPLOAD_MB * 1024 * 1024) {
          setError(`La imagen no debe superar ${MAX_UPLOAD_MB}MB.`);
          setSubmitting(false);
          return;
        }

        const toBase64 = (file: File) => new Promise<string>((resolve, reject) => {
          const reader = new FileReader();
          reader.readAsDataURL(file);
          reader.onload = () => resolve(reader.result as string);
          reader.onerror = (error) => reject(error);
        });
        
        imageUrl = await toBase64(imageFile);
      }

      await apiFetch(`${API_BASE_URL}/api/v1/citizen-reports`, {
        method: "POST",
        credentials: "include",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          name,
          email,
          phone: phone || undefined,
          subject,
          description,
          imageUrl: imageUrl || undefined,
        }),
      });

      setSuccess(true);
      setName(prefillUser?.username || "");
      setEmail(prefillUser?.email || "");
      setPhone("");
      setSubject("");
      setDescription("");
      setImageFile(null);
    } catch (err: any) {
      setError(err.message || "No se pudo enviar el reporte.");
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <div style={{ maxWidth: "800px", margin: "0 auto", padding: "2rem", background: "var(--bg-surface)", borderRadius: "8px", boxShadow: "0 4px 6px -1px rgba(0, 0, 0, 0.1)" }}>
      <h2 style={{ fontSize: "1.5rem", fontWeight: "bold", marginBottom: "1rem", color: "var(--text-main)" }}>Reporte Ciudadano</h2>
      
      {success ? (
          <div style={{ padding: "2rem 0", textAlign: "center" }}>
            <h3 style={{ color: "#059669", marginBottom: "1rem" }}>¡Reporte enviado con éxito!</h3>
            <p style={{ color: "#4b5563", marginBottom: "2rem" }}>Gracias por tu colaboración. Nuestro equipo revisará la información a la brevedad.</p>
            <a href="/" style={{ display: "inline-block", marginTop: "1rem", padding: "0.5rem 1rem", background: "var(--primary-color)", color: "white", borderRadius: "4px", textDecoration: "none" }}>Volver al inicio</a>
          </div>
        ) : (
          <form className="categories-form" onSubmit={handleSubmit}>
            <p style={{ color: "#6b7280", fontSize: "0.875rem", marginBottom: "1.5rem" }}>
              Utiliza este formulario para enviarnos noticias, denuncias ciudadanas o cualquier información de interés para la comunidad.
            </p>

            <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: "1rem" }}>
              <div>
                <label>Tu Nombre *</label>
                <input
                  type="text"
                  required
                  className="new-publication-input"
                  value={name}
                  onChange={(e) => setName(e.target.value)}
                  disabled={submitting}
                />
              </div>
              <div>
                <label>Tu Correo Electrónico *</label>
                <input
                  type="email"
                  required
                  className="new-publication-input"
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  disabled={submitting || !!prefillUser?.email}
                />
              </div>
            </div>

            <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: "1rem", marginTop: "1rem" }}>
              <div>
                <label>Teléfono (Opcional)</label>
                <input
                  type="tel"
                  className="new-publication-input"
                  value={phone}
                  onChange={(e) => setPhone(e.target.value)}
                  disabled={submitting}
                />
              </div>
              <div>
                <label>Asunto *</label>
                <input
                  type="text"
                  required
                  className="new-publication-input"
                  value={subject}
                  onChange={(e) => setSubject(e.target.value)}
                  disabled={submitting}
                />
              </div>
            </div>

            <div style={{ marginTop: "1rem" }}>
              <label>Descripción detallada *</label>
              <textarea
                required
                className="new-publication-input"
                style={{ minHeight: "120px", resize: "vertical" }}
                value={description}
                onChange={(e) => setDescription(e.target.value)}
                disabled={submitting}
              />
            </div>

            <div style={{ marginTop: "1rem" }}>
              <label>Adjuntar Imagen (Opcional)</label>
              <input
                type="file"
                accept="image/*"
                className="new-publication-input"
                onChange={(e) => setImageFile(e.target.files?.[0] || null)}
                disabled={submitting}
                style={{ padding: "0.5rem" }}
              />
            </div>

            {error && <p className="categories-modal-error" style={{ marginTop: "1rem" }}>{error}</p>}

            <div className="categories-modal-actions" style={{ marginTop: "2rem" }}>
              <button type="submit" className="primary" disabled={submitting} style={{ width: "100%", padding: "0.75rem" }}>
                {submitting ? "Enviando..." : "Enviar Reporte"}
              </button>
            </div>
          </form>
        )}
    </div>
  );
};
