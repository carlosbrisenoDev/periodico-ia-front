import { type FormEvent, useState, useEffect } from "react";
import { ApiError, registerSubscriber, getPublicCategories } from "../libs/http.ts";
import type { PublicCategory } from "../libs/types.ts";
import PublicNavbar from "../components/PublicNavbar.tsx";
import PublicFooter from "../components/PublicFooter.tsx";

/* ── Form types ──────────────────────────────────────── */

type SubscriptionForm = {
  email: string;
  username: string;
  age: string;
  phone: string;
  location: string;
};

const INITIAL_FORM: SubscriptionForm = {
  email: "",
  username: "",
  age: "",
  phone: "",
  location: "",
};

/* ── Component ───────────────────────────────────────── */

const SubscriptionPage = () => {
  const [form, setForm] = useState<SubscriptionForm>(INITIAL_FORM);
  const [submitting, setSubmitting] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [message, setMessage] = useState("");
  const [categories, setCategories] = useState<PublicCategory[]>([]);

  useEffect(() => {
    const controller = new AbortController();
    getPublicCategories(controller.signal)
      .then(setCategories)
      .catch(() => {});
    return () => controller.abort();
  }, []);

  const updateField = <K extends keyof SubscriptionForm>(field: K, value: SubscriptionForm[K]) => {
    setForm((prev) => ({ ...prev, [field]: value }));
  };

  const submitSubscription = async (event: FormEvent) => {
    event.preventDefault();

    const email = form.email.trim().toLowerCase();
    const username = form.username.trim();

    if (!email || !email.includes("@")) {
      setError("Ingresa un correo electrónico válido.");
      return;
    }

    setSubmitting(true);
    setError(null);
    setMessage("");

    try {
      const response = await registerSubscriber({
        username: username || email.split("@")[0],
        email,
        password: crypto.randomUUID().slice(0, 16),
        age: form.age ? Number(form.age) : undefined,
        phone: form.phone.trim() || undefined,
        location: form.location.trim() || undefined,
      });
      setMessage(response.message || "¡Suscripción creada correctamente!");
      setForm(INITIAL_FORM);
    } catch (err: unknown) {
      if (err instanceof ApiError) {
        setError(err.message);
      } else {
        setError(err instanceof Error ? err.message : "No se pudo completar la suscripción.");
      }
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <div className="ph-page">
      <PublicNavbar categories={categories} />

      {/* ── Main Content ──────────────────────────── */}
      <main className="ps-main">
        <div className="ps-card">
          <h1 className="ps-title">Completa tu Suscripción</h1>
          <p className="ps-subtitle">
            Recibe <strong>gratis</strong> un resumen claro de lo más importante del día, sin publicidad intrusiva.
          </p>

          <form className="ps-form" onSubmit={submitSubscription}>
            <label className="ps-label" htmlFor="ps-email">
              Correo Electrónico <span className="ps-required">*</span>
            </label>
            <input
              id="ps-email"
              className="ps-input"
              type="email"
              value={form.email}
              onChange={(e) => updateField("email", e.target.value)}
              placeholder="tu@email.com"
              disabled={submitting}
              required
            />

            <label className="ps-label" htmlFor="ps-name">
              Nombre Completo <span className="ps-optional">(Opcional)</span>
            </label>
            <input
              id="ps-name"
              className="ps-input"
              type="text"
              value={form.username}
              onChange={(e) => updateField("username", e.target.value)}
              placeholder="Tu nombre"
              disabled={submitting}
            />

            <label className="ps-label" htmlFor="ps-age">
              Edad <span className="ps-optional">(Opcional)</span>
            </label>
            <input
              id="ps-age"
              className="ps-input"
              type="number"
              value={form.age}
              onChange={(e) => updateField("age", e.target.value)}
              placeholder="Ej: 30"
              min="1"
              max="120"
              disabled={submitting}
            />

            <label className="ps-label" htmlFor="ps-phone">
              Teléfono <span className="ps-optional">(Opcional)</span>
            </label>
            <input
              id="ps-phone"
              className="ps-input"
              type="tel"
              value={form.phone}
              onChange={(e) => updateField("phone", e.target.value)}
              placeholder="Tu número de teléfono"
              disabled={submitting}
            />

            <label className="ps-label" htmlFor="ps-location">
              Ubicación <span className="ps-optional">(Opcional)</span>
            </label>
            <input
              id="ps-location"
              className="ps-input"
              type="text"
              value={form.location}
              onChange={(e) => updateField("location", e.target.value)}
              placeholder="Ciudad o País"
              disabled={submitting}
            />

            {error && <p className="ps-feedback ps-feedback-error">{error}</p>}
            {!error && message && <p className="ps-feedback ps-feedback-success">{message}</p>}

            <button className="ps-button" type="submit" disabled={submitting}>
              {submitting ? "Enviando..." : "Suscribirme Ahora"}
            </button>

            <p className="ps-terms">
              Al suscribirte, aceptas nuestros Términos de Servicio y Política de Privacidad
            </p>
          </form>
        </div>
      </main>

      <PublicFooter categories={categories} />
    </div>
  );
};

export default SubscriptionPage;
