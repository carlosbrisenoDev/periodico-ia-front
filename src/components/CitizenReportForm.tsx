import { useState } from "react";
import { API_BASE_URL, MAX_UPLOAD_MB } from "../libs/config.ts";
import { apiFetch } from "../libs/http.ts";

type CitizenReportFormProps = {
  isOpen: boolean;
  onClose: () => void;
};

export const CitizenReportForm = ({ isOpen, onClose }: CitizenReportFormProps) => {
  const [name, setName] = useState("");
  const [email, setEmail] = useState("");
  const [phone, setPhone] = useState("");
  const [subject, setSubject] = useState("");
  const [description, setDescription] = useState("");
  const [imageFile, setImageFile] = useState<File | null>(null);
  
  const [submitting, setSubmitting] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [success, setSuccess] = useState(false);

  if (!isOpen) return null;

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
      setName("");
      setEmail("");
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
    <div className="categories-modal-overlay" onClick={onClose}>
      <div className="categories-modal" onClick={(e) => e.stopPropagation()} style={{ maxWidth: "600px", maxHeight: "90vh", overflowY: "auto" }}>
        <h2 className="categories-modal-title">Reporte Ciudadano</h2>
        
        {success ? (
          <div style={{ padding: "2rem 0", textAlign: "center" }}>
            <h3 style={{ color: "#059669", marginBottom: "1rem" }}>¡Reporte enviado con éxito!</h3>
            <p style={{ color: "#4b5563", marginBottom: "2rem" }}>Gracias por tu colaboración. Nuestro equipo revisará la información a la brevedad.</p>
            <button className="primary" onClick={onClose}>Cerrar</button>
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
                <label>Correo Electrónico *</label>
                <input
                  type="email"
                  required
                  className="new-publication-input"
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  disabled={submitting}
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
              <button type="button" onClick={onClose} disabled={submitting}>Cancelar</button>
              <button type="submit" className="primary" disabled={submitting}>
                {submitting ? "Enviando..." : "Enviar Reporte"}
              </button>
            </div>
          </form>
        )}
      </div>
    </div>
  );
};
