import { useEffect, useState } from "react";
import { useLocation, useNavigate } from "react-router-dom";
import { getMe, logout } from "../libs/http.ts";
import type { ProfileData } from "../libs/types.ts";

type NavItem = {
    label: string;
    icon: | "home" | "doc" | "grid" | "users" | "folder" | "plus" | "image" | "video" | "trash" | "settings" | "logout" | "message-circle" | "alert-triangle";
    path?: string;
};

const PRIMARY_ITEMS: NavItem[] = [{ label: "Dashboard", icon: "home", path: "/dashboard" }, {
    label: "Mis Entradas",
    icon: "doc",
    path: "/allentries"
}, { label: "Todas las Entradas", icon: "grid", path: "/all-entries" }, {
    label: "Autores y Usuarios",
    icon: "users",
    path: "/authors-users"
}, {
    label: "Suscriptores",
    icon: "users",
    path: "/subscribers"
}, {
    label: "Comentarios",
    icon: "message-circle",
    path: "/comments"
}, {
    label: "Reportes Ciudadanos",
    icon: "alert-triangle",
    path: "/citizen-reports"
}, { label: "Categorías", icon: "folder", path: "/categories" }, {
    label: "Nueva Publicación",
    icon: "plus",
    path: "/new-publication"
}, { label: "Biblioteca de Imágenes", icon: "image", path: "/image-library" }, {
    label: "Videos",
    icon: "video",
    path: "/videos"
}, {
    label: "Entradas Borradas",
    icon: "trash",
    path: "/deleted-entries"
},];

const FOOTER_ITEMS: NavItem[] = [
    { label: "Mi Cuenta", icon: "settings", path: "/settings" },
    { label: "Ajustes", icon: "settings", path: "/global-settings" },
    { label: "Cerrar Sesión", icon: "logout" },
];

const SIDEBAR_LOGO_PATH = "/logo.png";

const Icon = ({ icon }: { icon: NavItem["icon"] }) => {
    if (icon === "home") {
        return (<svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            strokeWidth="2"
            strokeLinecap="round"
            strokeLinejoin="round"
            aria-hidden="true"
        >
            <path d="M3 10.5 12 3l9 7.5V21a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1z" />
        </svg>);
    }

    if (icon === "doc") {
        return (<svg
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
        </svg>);
    }

    if (icon === "grid") {
        return (<svg
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
        </svg>);
    }
    
    if (icon === "video") {
        return (<svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            strokeWidth="2"
            strokeLinecap="round"
            strokeLinejoin="round"
            aria-hidden="true"
        >
            <path d="m22 8-6 4 6 4V8Z" />
            <rect width="14" height="12" x="2" y="6" rx="2" ry="2" />
        </svg>);
    }

    if (icon === "users") {
        return (<svg
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
        </svg>);
    }

    if (icon === "folder") {
        return (<svg
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
        </svg>);
    }

    if (icon === "plus") {
        return (<svg
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
        </svg>);
    }

    if (icon === "image") {
        return (<svg
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
        </svg>);
    }

    if (icon === "trash") {
        return (<svg
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
        </svg>);
    }

    if (icon === "settings") {
        return (<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"
            className="lucide lucide-settings w-5 h-5 flex-shrink-0">
            <path
                d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"
            >
            </path>
            <circle cx="12" cy="12" r="3"></circle>
        </svg>);
    }
    if (icon === "message-circle") {
        return (<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>);
    }

    if (icon === "alert-triangle") {
        return (<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>);
    }

    return (<svg
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
    </svg>);
};

export const Sidebar = () => {
    const navigate = useNavigate();
    const location = useLocation();
    const [profile, setProfile] = useState<ProfileData | null>(null);
    const [loading, setLoading] = useState<boolean>(true);
    const [error, setError] = useState<string | null>(null);
    const [logoFailed, setLogoFailed] = useState<boolean>(false);
    const [isMobile, setIsMobile] = useState<boolean>(false);
    const [isMobileMenuOpen, setIsMobileMenuOpen] = useState<boolean>(false);

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

        const handleSessionUpdate = () => {
            void loadProfile();
        };

        window.addEventListener("periodico:session-updated", handleSessionUpdate);

        return () => {
            controller.abort();
            window.removeEventListener("periodico:session-updated", handleSessionUpdate);
        };
    }, []);

    useEffect(() => {
        const media = window.matchMedia("(max-width: 768px)");

        const updateMobileState = () => {
            setIsMobile(media.matches);
            if (!media.matches) {
                setIsMobileMenuOpen(false);
            }
        };

        updateMobileState();
        media.addEventListener("change", updateMobileState);

        return () => media.removeEventListener("change", updateMobileState);
    }, []);

    useEffect(() => {
        if (isMobile) {
            setIsMobileMenuOpen(false);
        }
    }, [isMobile, location.pathname]);

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

    const handleNavigate = (path: string) => {
        navigate(path);
        if (isMobile) {
            setIsMobileMenuOpen(false);
        }
    };

    const renderUserName = () => {
        if (loading) {
            return "Cargando perfil...";
        }

        if (error) {
            return (<button
                type="button"
                className="sidebar-inline-login"
                onClick={handleLoginRedirect}
            >
                Inicia sesión
            </button>);
        }

        return profile?.name ? `Hola, ${profile.name}` : "Perfil no disponible";
    };

    return (<div className="dashboard-sidebar">
        <div className="sidebar-mobile-topbar">
            <button
                type="button"
                className="sidebar-mobile-toggle"
                aria-label={isMobileMenuOpen ? "Cerrar menú" : "Abrir menú"}
                aria-expanded={isMobileMenuOpen}
                onClick={() => setIsMobileMenuOpen((prev) => !prev)}
            >
                <span />
                <span />
                <span />
            </button>
            <span className="sidebar-mobile-title">Panel</span>
        </div>

        {isMobile && isMobileMenuOpen ? (<button
            type="button"
            className="sidebar-mobile-overlay"
            aria-label="Cerrar menú"
            onClick={() => setIsMobileMenuOpen(false)}
        />) : null}

        <div className={`sidebar-panel${isMobileMenuOpen ? " open" : ""}`}>
            <div className="sidebar-scroll">
                <div className="sidebar-brand-area">
                    {!logoFailed ? (<img
                        src={SIDEBAR_LOGO_PATH}
                        alt="Logo del periódico"
                        className="sidebar-brand-logo"
                        onError={() => setLogoFailed(true)}
                    />) : (<div className="sidebar-brand-placeholder" aria-hidden="true" />)}
                    <div className="sidebar-user">{renderUserName()}</div>
                </div>

                <nav className="sidebar-nav" aria-label="Navegación principal">
                    {PRIMARY_ITEMS.filter(item => {
                        if (!profile) return false;
                        if (profile.role === "admin") return true;
                        // Editors can only see these paths:
                        const editorAllowedPaths = [
                            "/dashboard",
                            "/allentries",
                            "/new-publication",
                            "/image-library",
                            "/videos",
                            "/authors-users",
                            "/categories",
                            "/subscribers",
                            "/comments",
                            "/citizen-reports"
                        ];
                        return editorAllowedPaths.includes(item.path as string);
                    }).map((item) => {
                        const isActive = item.path ? location.pathname === item.path : false;

                        return (<button
                            type="button"
                            key={item.label}
                            className={`sidebar-nav-item${isActive ? " active" : ""}`}
                            onClick={item.path ? () => handleNavigate(item.path as string) : undefined}
                            aria-current={isActive ? "page" : undefined}
                        >
                            <span className="sidebar-icon">
                                <Icon icon={item.icon} />
                            </span>
                            <span>{item.label}</span>
                        </button>);
                    })}
                </nav>
            </div>

            <div className="sidebar-footer">
                {FOOTER_ITEMS.filter(item => {
                    if (!profile) return false;
                    if (profile.role === "admin") return true;
                    // Editors can only logout, cannot see settings
                    return item.icon === "logout";
                }).map((item) => (<button
                    key={item.label}
                    type="button"
                    onClick={item.icon === "logout" ? handleLogout : item.path ? () => handleNavigate(item.path as string) : undefined}
                    className="sidebar-nav-item"
                >
                    <span className="sidebar-icon">
                        <Icon icon={item.icon} />
                    </span>
                    <span>{item.label}</span>
                </button>))}
            </div>
        </div>
    </div>);
};
