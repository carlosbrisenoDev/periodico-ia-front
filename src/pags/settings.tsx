import { useEffect, useMemo, useState, type FormEvent } from "react";
import { useNavigate } from "react-router-dom";
import { Sidebar } from "../components/sidebar.tsx";
import { ApiError, changeAuthPassword, getMe, updateAuthProfile } from "../libs/http.ts";
import type { ProfileData } from "../libs/types.ts";

type ProfileForm = {
  name: string;
  email: string;
};

type PasswordForm = {
  currentPassword: string;
  newPassword: string;
  confirmNewPassword: string;
};

const getRoleLabel = (role: string): string => {
  if (role === "admin") return "Administrador";
  if (role === "editor") return "Editor";
  return role || "Usuario";
};

const getInitials = (name: string): string =>
  name
    .trim()
    .split(/\s+/)
    .filter(Boolean)
    .slice(0, 2)
    .map((part) => part[0]?.toUpperCase() ?? "")
    .join("") || "AC";

const SettingsPage = () => {
  const navigate = useNavigate();
  const [profile, setProfile] = useState<ProfileData | null>(null);
  const [profileForm, setProfileForm] = useState<ProfileForm>({ name: "", email: "" });
  const [passwordForm, setPasswordForm] = useState<PasswordForm>({
    currentPassword: "",
    newPassword: "",
    confirmNewPassword: "",
  });
  const [loading, setLoading] = useState<boolean>(true);
  const [savingProfile, setSavingProfile] = useState<boolean>(false);
  const [savingPassword, setSavingPassword] = useState<boolean>(false);
  const [profileMessage, setProfileMessage] = useState<string | null>(null);
  const [passwordMessage, setPasswordMessage] = useState<string | null>(null);
  const [profileError, setProfileError] = useState<string | null>(null);
  const [passwordError, setPasswordError] = useState<string | null>(null);
  const [pageError, setPageError] = useState<string | null>(null);

  useEffect(() => {
    const controller = new AbortController();

    const loadProfile = async () => {
      try {
        setLoading(true);
        const me = await getMe(controller.signal);
        setProfile(me);
        setProfileForm({ name: me.name, email: me.email });
        setPageError(null);
      } catch (error: unknown) {
        if (error instanceof Error && error.name === "AbortError") {
          return;
        }

        if (error instanceof ApiError && (error.status === 401 || error.status === 403)) {
          navigate("/adminlogin", { replace: true });
          return;
        }

        setPageError(error instanceof Error ? error.message : "No se pudo cargar la configuracion.");
      } finally {
        setLoading(false);
      }
    };

    void loadProfile();

    return () => controller.abort();
  }, [navigate]);

  const roleLabel = useMemo(() => getRoleLabel(profile?.role ?? ""), [profile?.role]);

  const updateProfileField = <K extends keyof ProfileForm>(field: K, value: ProfileForm[K]) => {
    setProfileForm((prev) => ({ ...prev, [field]: value }));
  };

  const updatePasswordField = <K extends keyof PasswordForm>(field: K, value: PasswordForm[K]) => {
    setPasswordForm((prev) => ({ ...prev, [field]: value }));
  };

  const emitSessionUpdate = () => {
    window.dispatchEvent(new Event("periodico:session-updated"));
  };

  const submitProfile = async (event: FormEvent<HTMLFormElement>) => {
    event.preventDefault();

    const name = profileForm.name.trim();
    const email = profileForm.email.trim();

    if (name.length < 2) {
      setProfileError("El nombre debe tener al menos 2 caracteres.");
      return;
    }

    if (!email.includes("@")) {
      setProfileError("Ingresa un correo valido.");
      return;
    }

    try {
      setSavingProfile(true);
      setProfileError(null);
      setProfileMessage(null);

      const updated = await updateAuthProfile({ name, email });
      setProfile(updated);
      setProfileForm({ name: updated.name, email: updated.email });
      setProfileMessage("Perfil actualizado correctamente.");
      emitSessionUpdate();
    } catch (error: unknown) {
      if (error instanceof ApiError && (error.status === 401 || error.status === 403)) {
        navigate("/adminlogin", { replace: true });
        return;
      }

      setProfileError(error instanceof Error ? error.message : "No se pudo actualizar el perfil.");
    } finally {
      setSavingProfile(false);
    }
  };

  const submitPassword = async (event: FormEvent<HTMLFormElement>) => {
    event.preventDefault();

    const currentPassword = passwordForm.currentPassword.trim();
    const newPassword = passwordForm.newPassword.trim();
    const confirmNewPassword = passwordForm.confirmNewPassword.trim();

    if (currentPassword.length < 8 || newPassword.length < 8) {
      setPasswordError("Las contrasenas deben tener al menos 8 caracteres.");
      return;
    }

    if (newPassword !== confirmNewPassword) {
      setPasswordError("La nueva contrasena y su confirmacion no coinciden.");
      return;
    }

    try {
      setSavingPassword(true);
      setPasswordError(null);
      setPasswordMessage(null);

      const message = await changeAuthPassword({ currentPassword, newPassword });
      setPasswordForm({ currentPassword: "", newPassword: "", confirmNewPassword: "" });
      setPasswordMessage(message);
    } catch (error: unknown) {
      if (error instanceof ApiError && (error.status === 401 || error.status === 403)) {
        navigate("/adminlogin", { replace: true });
        return;
      }

      setPasswordError(error instanceof Error ? error.message : "No se pudo actualizar la contrasena.");
    } finally {
      setSavingPassword(false);
    }
  };

  return (
    <div className="layout dashboard-layout">
      <aside className="sidebar">
        <Sidebar />
      </aside>

      <main className="content utility-page-content settings-content">
        <header className="settings-header">
          <button type="button" className="settings-back-button" onClick={() => navigate(-1)}>
            <span aria-hidden="true">←</span>
            Volver
          </button>

          <div>
            <h1>Configuración de Cuenta</h1>
            <p>Administra tu información personal y tu seguridad desde un solo lugar.</p>
          </div>
        </header>

        {pageError ? <p className="settings-banner error">{pageError}</p> : null}
        {loading ? <p className="settings-banner">Cargando datos de tu cuenta...</p> : null}

        {!loading && profile ? (
          <div className="settings-grid">
            <section className="settings-card">
              <div className="settings-card-head">
                <div>
                  <h2>Información del Perfil</h2>
                  <p>Actualiza tu nombre y correo electrónico.</p>
                </div>
              </div>

              <div className="settings-profile-summary">
                <div className="settings-avatar" aria-hidden="true">
                  <span>{getInitials(profile.name)}</span>
                </div>

                <div className="settings-summary-copy">
                  <strong>{profile.name}</strong>
                  <span>{profile.email}</span>
                  <span className="settings-role-chip">{roleLabel}</span>
                </div>
              </div>

              <form className="settings-form" onSubmit={submitProfile}>
                <label htmlFor="settings-name">Nombre completo</label>
                <input
                  id="settings-name"
                  type="text"
                  value={profileForm.name}
                  onChange={(event) => updateProfileField("name", event.target.value)}
                  disabled={savingProfile}
                />

                <label htmlFor="settings-email">Correo electrónico</label>
                <input
                  id="settings-email"
                  type="email"
                  value={profileForm.email}
                  onChange={(event) => updateProfileField("email", event.target.value)}
                  disabled={savingProfile}
                />

                {profileError ? <p className="settings-feedback error">{profileError}</p> : null}
                {profileMessage ? <p className="settings-feedback success">{profileMessage}</p> : null}

                <div className="settings-actions">
                  <button type="submit" className="settings-primary-button" disabled={savingProfile}>
                    {savingProfile ? "Guardando..." : "Guardar Cambios"}
                  </button>
                </div>
              </form>
            </section>

            <section className="settings-card">
              <div className="settings-card-head">
                <div>
                  <h2>Cambiar Contraseña</h2>
                  <p>Usa una contraseña segura y distinta a la anterior.</p>
                </div>
              </div>

              <form className="settings-form" onSubmit={submitPassword}>
                <label htmlFor="settings-current-password">Contraseña actual</label>
                <input
                  id="settings-current-password"
                  type="password"
                  value={passwordForm.currentPassword}
                  onChange={(event) => updatePasswordField("currentPassword", event.target.value)}
                  disabled={savingPassword}
                />

                <label htmlFor="settings-new-password">Nueva contraseña</label>
                <input
                  id="settings-new-password"
                  type="password"
                  value={passwordForm.newPassword}
                  onChange={(event) => updatePasswordField("newPassword", event.target.value)}
                  disabled={savingPassword}
                />

                <label htmlFor="settings-confirm-password">Confirmar nueva contraseña</label>
                <input
                  id="settings-confirm-password"
                  type="password"
                  value={passwordForm.confirmNewPassword}
                  onChange={(event) => updatePasswordField("confirmNewPassword", event.target.value)}
                  disabled={savingPassword}
                />

                {passwordError ? <p className="settings-feedback error">{passwordError}</p> : null}
                {passwordMessage ? <p className="settings-feedback success">{passwordMessage}</p> : null}

                <div className="settings-actions">
                  <button type="submit" className="settings-primary-button" disabled={savingPassword}>
                    {savingPassword ? "Actualizando..." : "Actualizar contraseña"}
                  </button>
                </div>
              </form>
            </section>
          </div>
        ) : null}
      </main>
    </div>
  );
};

export default SettingsPage;
