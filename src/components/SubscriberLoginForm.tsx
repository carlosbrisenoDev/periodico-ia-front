import { useState } from "react";
import { subscriberLogin } from "../libs/http.ts";

type SubscriberLoginFormProps = {
  onSuccess: () => void;
};

export const SubscriberLoginForm = ({ onSuccess }: SubscriberLoginFormProps) => {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [submitting, setSubmitting] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setSubmitting(true);
    setError(null);

    try {
      await subscriberLogin({ email, password });
      onSuccess();
    } catch (err: any) {
      setError(err.message || "Credenciales incorrectas.");
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <div style={{ maxWidth: "400px", margin: "0 auto", padding: "2rem", background: "var(--bg-surface)", borderRadius: "8px", boxShadow: "0 4px 6px -1px rgba(0, 0, 0, 0.1)" }}>
      <h2 style={{ textAlign: "center", marginBottom: "1.5rem", color: "var(--text-main)" }}>Inicia sesión para reportar</h2>
      <p style={{ textAlign: "center", marginBottom: "2rem", color: "var(--text-muted)", fontSize: "0.875rem" }}>
        Necesitas ser un usuario registrado para enviar reportes ciudadanos.
      </p>

      <form onSubmit={handleSubmit}>
        <div style={{ marginBottom: "1rem" }}>
          <label style={{ display: "block", marginBottom: "0.5rem", color: "var(--text-main)", fontWeight: "500" }}>Correo Electrónico</label>
          <input
            type="email"
            required
            className="new-publication-input"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            disabled={submitting}
            style={{ width: "100%", padding: "0.75rem", border: "1px solid var(--border-color)", borderRadius: "4px" }}
          />
        </div>
        
        <div style={{ marginBottom: "1.5rem" }}>
          <label style={{ display: "block", marginBottom: "0.5rem", color: "var(--text-main)", fontWeight: "500" }}>Contraseña</label>
          <input
            type="password"
            required
            className="new-publication-input"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            disabled={submitting}
            style={{ width: "100%", padding: "0.75rem", border: "1px solid var(--border-color)", borderRadius: "4px" }}
          />
        </div>

        {error && <p style={{ color: "red", marginBottom: "1rem", textAlign: "center" }}>{error}</p>}

        <button 
          type="submit" 
          disabled={submitting}
          style={{ width: "100%", padding: "0.75rem", background: "var(--primary-color)", color: "white", border: "none", borderRadius: "4px", fontWeight: "600", cursor: "pointer" }}
        >
          {submitting ? "Ingresando..." : "Ingresar"}
        </button>
      </form>

      <div style={{ marginTop: "1.5rem", textAlign: "center", fontSize: "0.875rem" }}>
        ¿No tienes cuenta? <a href="/subscription" style={{ color: "var(--primary-color)", textDecoration: "underline" }}>Regístrate aquí</a>
      </div>
    </div>
  );
};
