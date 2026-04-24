import { type FormEvent, useState } from "react";
import { Link } from "react-router-dom";
import { ApiError, registerSubscriber } from "../libs/http.ts";

type SubscriptionForm = {
  username: string;
  email: string;
  password: string;
};

const INITIAL_FORM: SubscriptionForm = {
  username: "",
  email: "",
  password: "",
};

const SubscriptionPage = () => {
  const [form, setForm] = useState<SubscriptionForm>(INITIAL_FORM);
  const [submitting, setSubmitting] = useState<boolean>(false);
  const [error, setError] = useState<string | null>(null);
  const [message, setMessage] = useState<string>("");

  const updateField = <K extends keyof SubscriptionForm>(field: K, value: SubscriptionForm[K]) => {
    setForm((prev) => ({ ...prev, [field]: value }));
  };

  const submitSubscription = async (event: FormEvent) => {
    event.preventDefault();

    const username = form.username.trim();
    const email = form.email.trim().toLowerCase();
    const password = form.password;

    if (username.length < 2) {
      setError("El nombre debe tener al menos 2 caracteres.");
      return;
    }

    if (!email || !email.includes("@")) {
      setError("Ingresa un correo valido.");
      return;
    }

    if (password.length < 8) {
      setError("La contrasena debe tener al menos 8 caracteres.");
      return;
    }

    setSubmitting(true);
    setError(null);
    setMessage("");

    try {
      const response = await registerSubscriber({ username, email, password });
      setMessage(response.message || "Suscripcion creada correctamente.");
      setForm(INITIAL_FORM);
    } catch (err: unknown) {
      if (err instanceof ApiError) {
        setError(err.message);
      } else {
        setError(err instanceof Error ? err.message : "No se pudo completar la suscripcion.");
      }
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <main className="subscription-page">
      <section className="subscription-shell">
        <header className="subscription-header">
          <div>
            <Link className="subscription-back" to="/">
              Volver al inicio
            </Link>
            <h1>Suscripcion al Periodico IA</h1>
            <p>
              Accede a boletines, noticias destacadas y contenido especial con una experiencia visual
              alineada al panel administrativo.
            </p>
          </div>

          <div className="subscription-header-actions">
            <span className="subscription-header-chip">Panel de suscripcion</span>
            <span className="subscription-header-chip muted">Estilo editorial</span>
          </div>
        </header>

        <section className="subscription-grid">
          <aside className="subscription-info-card">
            <div className="subscription-brand">
              <div className="subscription-brand-mark" aria-hidden="true">
                IA
              </div>
              <div>
                <p className="subscription-eyebrow">Periodico IA</p>
                <h2>Recibe lo importante, sin ruido.</h2>
              </div>
            </div>

            <p className="subscription-copy-text">
              Mantente al tanto de la redaccion con un acceso simple, limpio y pensado para leer en
              cualquier dispositivo.
            </p>

            <div className="subscription-benefit-grid">
              <article>
                <strong>Diario</strong>
                <span>Resumen de las noticias clave del dia.</span>
              </article>
              <article>
                <strong>Personalizado</strong>
                <span>Sugerencias segun el contenido que sigues.</span>
              </article>
              <article>
                <strong>Directo</strong>
                <span>Sin pasos innecesarios ni pantallas cargadas.</span>
              </article>
            </div>

            <div className="subscription-mini-stats">
              <div>
                <span>Noticias</span>
                <strong>24/7</strong>
              </div>
              <div>
                <span>Lectura</span>
                <strong>Rapida</strong>
              </div>
              <div>
                <span>Cancelacion</span>
                <strong>Libre</strong>
              </div>
            </div>
          </aside>

          <form className="subscription-card" onSubmit={submitSubscription}>
            <div className="subscription-card-head">
              <div>
                <h2>Crea tu suscripcion</h2>
                <p className="subscription-card-subtitle">Completa tus datos para empezar.</p>
              </div>
            </div>

            <label htmlFor="subscription-username">Nombre</label>
            <input
              id="subscription-username"
              type="text"
              value={form.username}
              onChange={(event) => updateField("username", event.target.value)}
              placeholder="Tu nombre"
              disabled={submitting}
            />

            <label htmlFor="subscription-email">Correo</label>
            <input
              id="subscription-email"
              type="email"
              value={form.email}
              onChange={(event) => updateField("email", event.target.value)}
              placeholder="correo@ejemplo.com"
              disabled={submitting}
            />

            <label htmlFor="subscription-password">Contrasena</label>
            <input
              id="subscription-password"
              type="password"
              value={form.password}
              onChange={(event) => updateField("password", event.target.value)}
              placeholder="Minimo 8 caracteres"
              disabled={submitting}
            />

            <div className="subscription-note">
              Al suscribirte aceptas recibir actualizaciones editoriales y ofertas puntuales.
            </div>

            {error ? <p className="subscription-feedback error">{error}</p> : null}
            {!error && message ? <p className="subscription-feedback success">{message}</p> : null}

            <button type="submit" disabled={submitting}>
              {submitting ? "Enviando..." : "Suscribirme"}
            </button>
          </form>
        </section>
      </section>
    </main>
  );
};

export default SubscriptionPage;

