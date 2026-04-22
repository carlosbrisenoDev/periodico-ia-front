import { useEffect, useState } from "react";
import { useLocation, useNavigate } from "react-router-dom";
import { getMe, logout } from "../libs/http.ts";
import type { ProfileData } from "../libs/types.ts";

type NavItem = {
  label: string;
  icon:
    | "home"
    | "doc"
    | "grid"
    | "users"
    | "folder"
    | "plus"
    | "image"
    | "trash"
    | "settings"
    | "logout";
  path?: string;
};

const PRIMARY_ITEMS: NavItem[] = [
  { label: "Dashboard", icon: "home", path: "/dashboard" },
  { label: "Mis Entradas", icon: "doc", path: "/allentries" },
  { label: "Todas las Entradas", icon: "grid", path: "/all-entries" },
  { label: "Autores y Usuarios", icon: "users", path: "/authors-users" },
  { label: "Categorías", icon: "folder", path: "/categories" },
  { label: "Nueva Publicación", icon: "plus", path: "/new-publication" },
  { label: "Biblioteca de Imágenes", icon: "image", path: "/image-library" },
  { label: "Entradas Borradas", icon: "trash" },
];

const FOOTER_ITEMS: NavItem[] = [
  { label: "Configuración", icon: "settings" },
  { label: "Cerrar Sesión", icon: "logout" },
];

const Icon = ({ icon }: { icon: NavItem["icon"] }) => {
  if (icon === "home") {
    return (
      <svg
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        strokeWidth="2"
        strokeLinecap="round"
        strokeLinejoin="round"
        aria-hidden="true"
      >
        <path d="M3 10.5 12 3l9 7.5V21a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1z" />
      </svg>
    );
  }

  if (icon === "doc") {
    return (
      <svg
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        strokeWidth="2"
        strokeLinecap="round"
        strokeLinejoin="round"
        aria-hidden="true"
      >
        <path d="M14 2H7a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7z" />
        <path d="M14 2v5h5" />
        <path d="M9 13h6" />
        <path d="M9 17h6" />
      </svg>
    );
  }

  if (icon === "grid") {
    return (
      <svg
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        strokeWidth="2"
        strokeLinecap="round"
        strokeLinejoin="round"
        aria-hidden="true"
      >
        <rect x="3" y="3" width="7" height="7" rx="1" />
        <rect x="14" y="3" width="7" height="7" rx="1" />
        <rect x="3" y="14" width="7" height="7" rx="1" />
        <rect x="14" y="14" width="7" height="7" rx="1" />
      </svg>
    );
  }

  if (icon === "users") {
    return (
      <svg
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        strokeWidth="2"
        strokeLinecap="round"
        strokeLinejoin="round"
        aria-hidden="true"
      >
        <circle cx="9" cy="8" r="3" />
        <path d="M3 20v-1a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v1" />
        <path d="M17 10a3 3 0 1 0 0-6" />
        <path d="M21 20v-1a4 4 0 0 0-3-3.87" />
      </svg>
    );
  }

  if (icon === "folder") {
    return (
      <svg
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        strokeWidth="2"
        strokeLinecap="round"
        strokeLinejoin="round"
        aria-hidden="true"
      >
        <path d="M3 6a2 2 0 0 1 2-2h5l2 2h7a2 2 0 0 1 2 2v2H3z" />
        <path d="M3 10h18l-1.6 8a2 2 0 0 1-2 1.6H5.6a2 2 0 0 1-2-1.6z" />
      </svg>
    );
  }

  if (icon === "plus") {
    return (
      <svg
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        strokeWidth="2"
        strokeLinecap="round"
        strokeLinejoin="round"
        aria-hidden="true"
      >
        <circle cx="12" cy="12" r="9" />
        <path d="M12 8v8" />
        <path d="M8 12h8" />
      </svg>
    );
  }

  if (icon === "image") {
    return (
      <svg
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        strokeWidth="2"
        strokeLinecap="round"
        strokeLinejoin="round"
        aria-hidden="true"
      >
        <rect x="3" y="3" width="18" height="18" rx="2" />
        <circle cx="9" cy="9" r="1.5" />
        <path d="m21 15-4.5-4.5a1.5 1.5 0 0 0-2.1 0L6 19" />
      </svg>
    );
  }

  if (icon === "trash") {
    return (
      <svg
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        strokeWidth="2"
        strokeLinecap="round"
        strokeLinejoin="round"
        aria-hidden="true"
      >
        <path d="M3 6h18" />
        <path d="M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2" />
        <path d="M6 6v14a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V6" />
        <path d="M10 10v7" />
        <path d="M14 10v7" />
      </svg>
    );
  }

  if (icon === "settings") {
    return (
      <svg
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        strokeWidth="2"
        strokeLinecap="round"
        strokeLinejoin="round"
        aria-hidden="true"
      >
        <circle cx="12" cy="12" r="3" />
        <path d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.7 1.7 0 0 0-1.88-.34 1.7 1.7 0 0 0-1 1.55V21a2 2 0 1 1-4 0v-.09a1.7 1.7 0 0 0-1-1.55 1.7 1.7 0 0 0-.34-1.88l-.06-.06a2 2 0 1 1-2.83-2.83l.06-.06a1.7 1.7 0 0 0 .34-1.88 1.7 1.7 0 0 0-1.55-1H3a2 2 0 1 1 0-4h.09a1.7 1.7 0 0 0 1.55-1 1.7 1.7 0 0 0-.34-1.88l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.7 1.7 0 0 0 1.88.34h.01a1.7 1.7 0 0 0 1-1.55V3a2 2 0 1 1 4 0v.09a1.7 1.7 0 0 0 1 1.55h.01a1.7 1.7 0 0 0 1.88-.34l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.7 1.7 0 0 0-.34 1.88v.01a1.7 1.7 0 0 0 1.55 1H21a2 2 0 1 1 0 4h-.09a1.7 1.7 0 0 0-1.55 1z" />
      </svg>
    );
  }

  return (
    <svg
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      strokeWidth="2"
      strokeLinecap="round"
      strokeLinejoin="round"
      aria-hidden="true"
    >
      <path d="M9 21H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h3" />
      <path d="M16 17l5-5-5-5" />
      <path d="M21 12H9" />
    </svg>
  );
};

export const Sidebar = () => {
  const navigate = useNavigate();
  const location = useLocation();
  const [profile, setProfile] = useState<ProfileData | null>(null);
  const [loading, setLoading] = useState<boolean>(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const controller = new AbortController();

    const loadProfile = async () => {
      try {
        setLoading(true);

        const userData = await getMe(controller.signal);
        setProfile(userData);
        setError(null);
      } catch (err: unknown) {
        if (err instanceof Error && err.name === "AbortError") {
          return;
        }

        setError(err instanceof Error ? err.message : "Error desconocido");
        setProfile(null);
      } finally {
        setLoading(false);
      }
    };

    void loadProfile();

    return () => controller.abort();
  }, []);

  const handleLoginRedirect = () => {
    navigate("/adminlogin");
  };

  const handleLogout = async () => {
    try {
      await logout();
    } catch {
      // Even if logout request fails, force a local redirect to the login page.
    } finally {
      navigate("/adminlogin", { replace: true });
    }
  };

  const renderUserName = () => {
    if (loading) {
      return "Cargando perfil...";
    }

    if (error) {
      return (
        <button
          type="button"
          className="sidebar-inline-login"
          onClick={handleLoginRedirect}
        >
          Inicia sesión
        </button>
      );
    }

    return profile?.name ? `Hola, ${profile.name}` : "Perfil no disponible";
  };

  return (
    <div className="dashboard-sidebar">
      <div className="sidebar-scroll">
        <div className="sidebar-brand-area">
          <div className="sidebar-brand-placeholder" aria-hidden="true" />
          <div className="sidebar-user">{renderUserName()}</div>
        </div>

        <nav className="sidebar-nav" aria-label="Navegación principal">
          {PRIMARY_ITEMS.map((item) => {
            const isActive = item.path
              ? location.pathname === item.path
              : false;

            return (
              <button
                type="button"
                key={item.label}
                className={`sidebar-nav-item${isActive ? " active" : ""}`}
                onClick={
                  item.path ? () => navigate(item.path as string) : undefined
                }
                aria-current={isActive ? "page" : undefined}
              >
                <span className="sidebar-icon">
                  <Icon icon={item.icon} />
                </span>
                <span>{item.label}</span>
              </button>
            );
          })}
        </nav>
      </div>

      <div className="sidebar-footer">
        {FOOTER_ITEMS.map((item) => (
          <button
            key={item.label}
            type="button"
            onClick={item.icon === "logout" ? handleLogout : undefined}
            className="sidebar-nav-item"
          >
            <span className="sidebar-icon">
              <Icon icon={item.icon} />
            </span>
            <span>{item.label}</span>
          </button>
        ))}
      </div>
    </div>
  );
};
