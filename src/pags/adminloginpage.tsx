import { useEffect, useMemo, useState } from "react";
import type { FormEvent } from "react";
import { useLocation, useNavigate } from "react-router-dom";
import { ApiError, getOptionalMe, isAdmin, login, logout } from "../libs/http.ts";

const AdminLoginPage = () => {
  const navigate = useNavigate();
  const location = useLocation();
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [submitting, setSubmitting] = useState<boolean>(false);
  const [error, setError] = useState<string | null>(null);

  const redirectTo = useMemo(() => {
    const state = location.state as { redirectTo?: string } | null;

    return state?.redirectTo && state.redirectTo !== "/adminlogin"
      ? state.redirectTo
      : "/dashboard";
  }, [location.state]);

  useEffect(() => {
    const controller = new AbortController();

    const checkSession = async () => {
      try {
        const me = await getOptionalMe(controller.signal);

        if (me && isAdmin(me)) {
          navigate(redirectTo, { replace: true });
        }
      } catch {
        // Keep user on login page if session validation fails.
      }
    };

    void checkSession();

    return () => controller.abort();
  }, [navigate, redirectTo]);

  const onSubmit = async (event: FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    setError(null);
    setSubmitting(true);

    try {
      const profile = await login({ email: email.trim(), password });

      if (!isAdmin(profile)) {
        await logout().catch(() => undefined);
        setError("Tu cuenta no tiene permisos de administrador.");
        return;
      }

      navigate(redirectTo, { replace: true });
    } catch (err: unknown) {
      if (err instanceof ApiError) {
        setError(err.message || "Credenciales invalidas o sesion no disponible.");
      } else {
        setError("Error al conectar con el servidor.");
      }
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <div className="login">
      <div className="loginform">
        <div className="svg-black center admin-icon">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="24"
            height="24"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            strokeWidth="2"
            strokeLinecap="round"
            strokeLinejoin="round"
            className="white-text"
          >
            <path d="M15 18h-5" />
            <path d="M18 14h-8" />
            <path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-4 0v-9a2 2 0 0 1 2-2h2" />
            <rect width="8" height="4" x="10" y="6" rx="1" />
          </svg>
        </div>

        <h1 className="login-title">Panel de Administracion</h1>
        <p className="text-muted login-subtitle">
          Ingresa tus credenciales para continuar.
        </p>

        <form className="login-form" onSubmit={onSubmit}>
          <label htmlFor="email">Correo electronico</label>
          <input
            id="email"
            name="email"
            type="email"
            placeholder="admin@periodico.com"
            required
            value={email}
            onChange={(event) => setEmail(event.target.value)}
          />

          <label htmlFor="password">Contrasena</label>
          <input
            id="password"
            name="password"
            type="password"
            placeholder="********"
            required
            value={password}
            onChange={(event) => setPassword(event.target.value)}
          />

          {error ? <p className="login-error">{error}</p> : null}

          <button className="login-button" type="submit" disabled={submitting}>
            {submitting ? "Ingresando..." : "Iniciar Sesion"}
          </button>
        </form>
      </div>
    </div>
  );
};

export default AdminLoginPage;

