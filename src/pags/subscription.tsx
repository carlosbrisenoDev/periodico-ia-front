import { type FormEvent, useState } from "react";
import { Link } from "react-router-dom";
import { ApiError, registerSubscriber } from "../libs/http.ts";
import logoSrc from "../assets/logo.png";

/* ── helpers ─────────────────────────────────────────── */

const NAV_CATEGORIES = [
  { label: "Noticias", href: "/categoria/noticias" },
  { label: "Seguridad", href: "/categoria/seguridad" },
  { label: "Deportes", href: "/categoria/deportes" },
  { label: "Cultura", href: "/categoria/cultura" },
  { label: "Comunidad", href: "/categoria/comunidad" },
  { label: "Opinión", href: "/categoria/opinion" },
];

const formatFullDate = (): string =>
  new Intl.DateTimeFormat("es-ES", {
    weekday: "long",
    day: "numeric",
    month: "long",
    year: "numeric",
  })
    .format(new Date())
    .replace(/^\w/, (c) => c.toUpperCase());

/* ── SVG icons ───────────────────────────────────────── */

const MenuIcon = () => (
  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><line x1="4" x2="20" y1="12" y2="12" /><line x1="4" x2="20" y1="6" y2="6" /><line x1="4" x2="20" y1="18" y2="18" /></svg>
);

const CloseIcon = () => (
  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><line x1="18" x2="6" y1="6" y2="18" /><line x1="6" x2="18" y1="6" y2="18" /></svg>
);

const SearchIcon = () => (
  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="11" cy="11" r="8" /><path d="m21 21-4.3-4.3" /></svg>
);

const PhoneIcon = () => (
  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" /></svg>
);

const FacebookIcon = () => (
  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" /></svg>
);

const TwitterIcon = () => (
  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z" /></svg>
);

const InstagramIcon = () => (
  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect width="20" height="20" x="2" y="2" rx="5" ry="5" /><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" /><line x1="17.5" x2="17.51" y1="6.5" y2="6.5" /></svg>
);

const LinkedInIcon = () => (
  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z" /><rect width="4" height="12" x="2" y="9" /><circle cx="4" cy="4" r="2" /></svg>
);

/* ── Form types ──────────────────────────────────────── */

type SubscriptionForm = {
  email: string;
  username: string;
};

const INITIAL_FORM: SubscriptionForm = {
  email: "",
  username: "",
};

/* ── Component ───────────────────────────────────────── */

const SubscriptionPage = () => {
  const [form, setForm] = useState<SubscriptionForm>(INITIAL_FORM);
  const [submitting, setSubmitting] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [message, setMessage] = useState("");
  const [mobileOpen, setMobileOpen] = useState(false);

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
      {/* ── Navbar ─────────────────────────────────── */}
      <nav className="public-nav-container">
        <div className="public-nav-inner">
          <div className="public-nav-top">
            <button className="ph-mobile-toggle" onClick={() => setMobileOpen(true)} aria-label="Abrir menú">
              <MenuIcon />
            </button>
            <a className="public-nav-logo-link" href="/">
              <img className="public-nav-logo" src={logoSrc} alt="Información de Altura" />
            </a>
            <div className="public-nav-actions">
              <div className="public-nav-date">{formatFullDate()}</div>
              <Link className="public-nav-subscribe" to="/suscripcion">Suscribirse</Link>
            </div>
          </div>
          <div className="public-nav-bottom">
            <div className="public-nav-links">
              {NAV_CATEGORIES.map((c) => (
                <a key={c.href} className="public-nav-link" href={c.href}>{c.label}</a>
              ))}
              <a className="public-nav-link active" href="/recientes">Recientes</a>
            </div>
            <div className="public-nav-search">
              <span className="public-nav-search-icon"><SearchIcon /></span>
              <input className="public-nav-search-input" type="search" placeholder="Buscar noticias..." />
            </div>
          </div>
        </div>
      </nav>

      {/* Mobile drawer */}
      <div className={`ph-mobile-overlay ${mobileOpen ? "open" : ""}`} onClick={() => setMobileOpen(false)} />
      <aside className={`ph-mobile-drawer ${mobileOpen ? "open" : ""}`}>
        <div className="ph-mobile-drawer-header">
          <img src={logoSrc} alt="Información de Altura" />
          <button className="ph-mobile-close" onClick={() => setMobileOpen(false)} aria-label="Cerrar menú"><CloseIcon /></button>
        </div>
        <div className="ph-mobile-nav-links">
          {NAV_CATEGORIES.map((c) => (
            <a key={c.href} className="ph-mobile-nav-link" href={c.href}>{c.label}</a>
          ))}
          <a className="ph-mobile-nav-link active" href="/recientes">Recientes</a>
        </div>
        <div className="ph-mobile-search">
          <div className="ph-mobile-search-wrap">
            <SearchIcon />
            <input className="ph-mobile-search-input" type="search" placeholder="Buscar noticias..." />
          </div>
        </div>
      </aside>

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

      {/* ── Footer ────────────────────────────────── */}
      <footer className="public-footer">
        <div className="public-footer-inner">
          <div className="public-footer-grid">
            <div className="public-footer-brand">
              <a href="/" style={{ display: "inline-block", marginBottom: 16 }}>
                <img src={logoSrc} alt="Información de Altura" style={{ height: 80, width: "auto" }} />
              </a>
              <p>Periodismo independiente para el mundo moderno.</p>
              <div className="public-footer-phone">
                <PhoneIcon />
                <span>+34 900 123 456</span>
              </div>
            </div>
            <div>
              <h4 className="public-footer-title">Secciones</h4>
              <ul className="public-footer-links">
                {NAV_CATEGORIES.map((c) => (
                  <li key={c.href}>
                    <a className="public-footer-link" href={c.href}>{c.label}</a>
                  </li>
                ))}
              </ul>
            </div>
            <div>
              <h4 className="public-footer-title">Redes Sociales</h4>
              <div className="public-footer-social">
                <a href="#" className="public-social-button" aria-label="Facebook"><FacebookIcon /></a>
                <a href="#" className="public-social-button" aria-label="Twitter"><TwitterIcon /></a>
                <a href="#" className="public-social-button" aria-label="Instagram"><InstagramIcon /></a>
                <a href="#" className="public-social-button" aria-label="LinkedIn"><LinkedInIcon /></a>
              </div>
            </div>
          </div>
          <div className="public-footer-bottom">© 2026 Información de Altura. Todos los derechos reservados.</div>
        </div>
      </footer>
    </div>
  );
};

export default SubscriptionPage;
