import { useEffect, useMemo, useState, type FormEvent } from "react";
import { useNavigate } from "react-router-dom";
import { Sidebar } from "../components/sidebar.tsx";
import { API_BASE_URL } from "../libs/config.ts";
import { ApiError, apiFetch } from "../libs/http.ts";

type AuthorRecord = {
  id: string;
  name: string;
  bio: string;
  avatarUrl: string | null;
  userId: string | null;
};

type AdminUserRecord = {
  id: string;
  name: string;
  email: string;
  role: string;
  active: boolean;
};

type UsersResponse = {
  users?: unknown[];
};

type RegisterResponse = {
  user?: unknown;
};

type CreateAuthorResponse = {
  id?: string;
  name?: string;
  bio?: string;
  avatarUrl?: string;
  userId?: string | null;
};

type NewAuthorRole = "admin" | "editor";

type NewAuthorForm = {
  name: string;
  email: string;
  bio: string;
  password: string;
  role: NewAuthorRole;
};

type EditAuthorForm = {
  id: string;
  name: string;
  bio: string;
};

type AuthorCard = {
  id: string;
  authorId: string;
  name: string;
  subtitle: string;
  role: string;
  active: boolean;
  avatarUrl: string | null;
};

const INITIAL_FORM: NewAuthorForm = {
  name: "",
  email: "",
  bio: "",
  password: "",
  role: "editor",
};

const normalizeAuthor = (item: unknown, index: number): AuthorRecord | null => {
  if (!item || typeof item !== "object") {
    return null;
  }

  const record = item as Record<string, unknown>;
  const id = typeof record.id === "string" ? record.id : `author-${index}`;
  const name = typeof record.name === "string" ? record.name : "Autor sin nombre";

  return {
    id,
    name,
    bio: typeof record.bio === "string" ? record.bio : "",
    avatarUrl: typeof record.avatarUrl === "string" ? record.avatarUrl : null,
    userId: typeof record.userId === "string" ? record.userId : null,
  };
};

const normalizeAdminUser = (item: unknown, index: number): AdminUserRecord | null => {
  if (!item || typeof item !== "object") {
    return null;
  }

  const record = item as Record<string, unknown>;

  return {
    id: typeof record.id === "string" ? record.id : `user-${index}`,
    name: typeof record.name === "string" ? record.name : "Usuario",
    email: typeof record.email === "string" ? record.email : "",
    role: typeof record.role === "string" ? record.role : "editor",
    active: typeof record.active === "boolean" ? record.active : true,
  };
};

const roleLabel = (role: string): string => {
  if (role === "admin") {
    return "Administrador";
  }

  return "Editor";
};

const roleClass = (role: string): string => {
  if (role === "admin") {
    return "authors-users-role-admin";
  }

  return "authors-users-role-editor";
};

const inferSubtitle = (author: AuthorRecord, user: AdminUserRecord | null): string => {
  if (author.bio.trim().length > 0) {
    return author.bio;
  }

  if (user?.role === "admin") {
    return "Equipo Administrativo";
  }

  return "Equipo Editorial";
};

const initialsFromName = (name: string): string => {
  const tokens = name
    .trim()
    .split(/\s+/)
    .filter(Boolean)
    .slice(0, 2);

  if (tokens.length === 0) {
    return "AU";
  }

  return tokens
    .map((token) => token[0]?.toUpperCase() ?? "")
    .join("");
};

const randomPassword = (): string => {
  const chars = "ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789!@#$%";
  const bytes = new Uint32Array(12);
  crypto.getRandomValues(bytes);

  return Array.from(bytes)
    .map((value) => chars[value % chars.length])
    .join("");
};

const AuthorsUsers = () => {
  const navigate = useNavigate();
  const [authors, setAuthors] = useState<AuthorRecord[]>([]);
  const [users, setUsers] = useState<AdminUserRecord[]>([]);
  const [loading, setLoading] = useState<boolean>(true);
  const [error, setError] = useState<string | null>(null);
  const [showCreateModal, setShowCreateModal] = useState<boolean>(false);
  const [editForm, setEditForm] = useState<EditAuthorForm | null>(null);
  const [createError, setCreateError] = useState<string | null>(null);
  const [editError, setEditError] = useState<string | null>(null);
  const [creating, setCreating] = useState<boolean>(false);
  const [savingEdit, setSavingEdit] = useState<boolean>(false);
  const [deletingById, setDeletingById] = useState<Record<string, boolean>>({});
  const [form, setForm] = useState<NewAuthorForm>(INITIAL_FORM);

  useEffect(() => {
    const controller = new AbortController();

    const loadPageData = async () => {
      try {
        setLoading(true);

        const [authorsPayload, usersPayload] = await Promise.all([
          apiFetch<unknown[]>(`${API_BASE_URL}/api/v1/author`, {
            method: "GET",
            credentials: "include",
            signal: controller.signal,
          }),
          apiFetch<UsersResponse>(`${API_BASE_URL}/api/v1/auth/users`, {
            method: "GET",
            credentials: "include",
            signal: controller.signal,
          }),
        ]);

        const normalizedAuthors = (Array.isArray(authorsPayload) ? authorsPayload : [])
          .map((item, index) => normalizeAuthor(item, index))
          .filter((item): item is AuthorRecord => item !== null);

        const normalizedUsers = (Array.isArray(usersPayload.users) ? usersPayload.users : [])
          .map((item, index) => normalizeAdminUser(item, index))
          .filter((item): item is AdminUserRecord => item !== null);

        setAuthors(normalizedAuthors);
        setUsers(normalizedUsers);
        setError(null);
      } catch (err: unknown) {
        if (err instanceof Error && err.name === "AbortError") {
          return;
        }

        if (err instanceof ApiError && (err.status === 401 || err.status === 403)) {
          navigate("/adminlogin", { replace: true });
          return;
        }

        setError(err instanceof Error ? err.message : "Error desconocido");
        setAuthors([]);
        setUsers([]);
      } finally {
        setLoading(false);
      }
    };

    void loadPageData();

    return () => controller.abort();
  }, [navigate]);

  const cards = useMemo<AuthorCard[]>(() => {
    const usersById = new Map<string, AdminUserRecord>(users.map((user) => [user.id, user]));

    return authors.map((author) => {
      const linkedUser = author.userId ? usersById.get(author.userId) ?? null : null;
      const cardName = author.name || linkedUser?.name || "Autor sin nombre";

      return {
        id: author.id,
        authorId: author.id,
        name: cardName,
        subtitle: inferSubtitle(author, linkedUser),
        role: linkedUser?.role ?? "editor",
        active: linkedUser?.active ?? true,
        avatarUrl: author.avatarUrl,
      };
    });
  }, [authors, users]);

  const openCreateModal = () => {
    setForm({ ...INITIAL_FORM, password: randomPassword() });
    setCreateError(null);
    setShowCreateModal(true);
  };

  const closeCreateModal = () => {
    if (creating) {
      return;
    }

    setShowCreateModal(false);
    setCreateError(null);
  };

  const updateFormField = <K extends keyof NewAuthorForm>(
    field: K,
    value: NewAuthorForm[K],
  ) => {
    setForm((prev) => ({ ...prev, [field]: value }));
  };

  const createAuthor = async (event: FormEvent<HTMLFormElement>) => {
    event.preventDefault();

    const name = form.name.trim();
    const email = form.email.trim();
    const bio = form.bio.trim();
    const password = form.password.trim();

    if (!name || !email || !password) {
      setCreateError("Completa nombre, correo y contrasena.");
      return;
    }

    try {
      setCreating(true);
      setCreateError(null);

      const registerPayload = await apiFetch<RegisterResponse>(
        `${API_BASE_URL}/api/v1/auth/register`,
        {
          method: "POST",
          credentials: "include",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            name,
            email,
            password,
            role: form.role,
          }),
        },
      );

      const createdUserRaw =
        registerPayload.user && typeof registerPayload.user === "object"
          ? (registerPayload.user as Record<string, unknown>)
          : null;

      const createdUserId =
        createdUserRaw && typeof createdUserRaw.id === "string"
          ? createdUserRaw.id
          : null;

      if (!createdUserId) {
        throw new Error("No se pudo crear el usuario del autor.");
      }

      const newUser: AdminUserRecord = {
        id: createdUserId,
        name,
        email,
        role: form.role,
        active: true,
      };

      setUsers((prev) => [newUser, ...prev]);

      try {
        const authorBody: Record<string, string> = {
          name,
          userId: createdUserId,
        };
        
        if (bio) {
          authorBody.bio = bio;
        }

        const authorPayload = await apiFetch<CreateAuthorResponse>(
          `${API_BASE_URL}/api/v1/author`,
          {
            method: "POST",
            credentials: "include",
            headers: {
              "Content-Type": "application/json",
            },
            body: JSON.stringify(authorBody),
          },
        );

        const newAuthor: AuthorRecord = {
          id: typeof authorPayload.id === "string" ? authorPayload.id : `author-${Date.now()}`,
          name: typeof authorPayload.name === "string" ? authorPayload.name : name,
          bio: typeof authorPayload.bio === "string" ? authorPayload.bio : bio,
          avatarUrl: typeof authorPayload.avatarUrl === "string" ? authorPayload.avatarUrl : null,
          userId:
            typeof authorPayload.userId === "string"
              ? authorPayload.userId
              : createdUserId,
        };

        setAuthors((prev) => [newAuthor, ...prev]);
        setShowCreateModal(false);
        setForm(INITIAL_FORM);
      } catch (authorErr: unknown) {
        setCreateError(authorErr instanceof Error ? `El usuario se creó, pero falló el autor: ${authorErr.message}` : "El usuario se creó, pero no se pudo generar el autor.");
      }
    } catch (err: unknown) {
      if (err instanceof ApiError && (err.status === 401 || err.status === 403)) {
        navigate("/adminlogin", { replace: true });
        return;
      }

      setCreateError(err instanceof Error ? err.message : "No se pudo crear el autor.");
    } finally {
      setCreating(false);
    }
  };

  const openEditModal = (author: AuthorRecord) => {
    setEditForm({
      id: author.id,
      name: author.name,
      bio: author.bio,
    });
    setEditError(null);
  };

  const closeEditModal = () => {
    if (savingEdit) {
      return;
    }

    setEditForm(null);
    setEditError(null);
  };

  const submitEditAuthor = async (event: FormEvent<HTMLFormElement>) => {
    event.preventDefault();

    if (!editForm) {
      return;
    }

    const name = editForm.name.trim();
    const bio = editForm.bio.trim();

    if (name.length < 2) {
      setEditError("El nombre debe tener al menos 2 caracteres.");
      return;
    }

    try {
      setSavingEdit(true);
      setEditError(null);

      const payload = await apiFetch<CreateAuthorResponse>(
        `${API_BASE_URL}/api/v1/author/${editForm.id}`,
        {
          method: "PATCH",
          credentials: "include",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            name,
            bio,
          }),
        },
      );

      const resolvedName = typeof payload.name === "string" ? payload.name : name;
      const resolvedBio = typeof payload.bio === "string" ? payload.bio : bio;

      setAuthors((prev) =>
        prev.map((author) =>
          author.id === editForm.id ? { ...author, name: resolvedName, bio: resolvedBio } : author,
        ),
      );
      setEditForm(null);
    } catch (err: unknown) {
      if (err instanceof ApiError && (err.status === 401 || err.status === 403)) {
        navigate("/adminlogin", { replace: true });
        return;
      }

      setEditError(err instanceof Error ? err.message : "No se pudo actualizar el autor.");
    } finally {
      setSavingEdit(false);
    }
  };

  const deleteAuthor = async (author: AuthorCard) => {
    const confirmed = window.confirm(`Eliminar al autor \"${author.name}\"?`);
    if (!confirmed) {
      return;
    }

    try {
      setCreateError(null);
      setDeletingById((prev) => ({ ...prev, [author.authorId]: true }));

      await apiFetch<{ message?: string }>(`${API_BASE_URL}/api/v1/author/${author.authorId}`, {
        method: "DELETE",
        credentials: "include",
      });

      setAuthors((prev) => prev.filter((item) => item.id !== author.authorId));
    } catch (err: unknown) {
      if (err instanceof ApiError && (err.status === 401 || err.status === 403)) {
        navigate("/adminlogin", { replace: true });
        return;
      }

      setCreateError(err instanceof Error ? err.message : "No se pudo eliminar el autor.");
    } finally {
      setDeletingById((prev) => {
        const next = { ...prev };
        delete next[author.authorId];
        return next;
      });
    }
  };

  return (
    <div className="layout dashboard-layout">
      <aside className="sidebar">
        <Sidebar />
      </aside>

      <main className="content authors-users-content">
        <header className="authors-users-header">
          <div>
            <h1 className="authors-users-title">Autores y Usuarios</h1>
            <p className="authors-users-subtitle">
              Gestiona los autores del periodico y sus permisos.
            </p>
          </div>

          <button
            type="button"
            className="entries-new-button"
            onClick={openCreateModal}
          >
            + Nuevo Autor
          </button>
        </header>

        {loading ? <p className="authors-users-info">Cargando autores...</p> : null}

        {!loading && error ? <p className="authors-users-info error">{error}</p> : null}

        {!loading && !error && cards.length === 0 ? (
          <p className="authors-users-info">No hay autores para mostrar.</p>
        ) : null}

        <section className="authors-users-grid" aria-label="Listado de autores">
          {cards.map((card) => (
            <article key={card.id} className="author-card">
              <div className="author-card-main">
                <div className="author-card-avatar" aria-hidden="true">
                  {card.avatarUrl ? (
                    <img src={card.avatarUrl} alt="" />
                  ) : (
                    <span>{initialsFromName(card.name)}</span>
                  )}
                </div>

                <div>
                  <h2 className="author-card-name">{card.name}</h2>
                  <p className="author-card-subtitle">{card.subtitle}</p>
                </div>
              </div>

              <div className="author-card-role-row">
                <span className={`author-card-role ${roleClass(card.role)}`}>
                  {roleLabel(card.role)}
                </span>

                {card.active ? null : (
                  <span className="author-card-inactive" title="Usuario inactivo">
                    Inactivo
                  </span>
                )}
              </div>

              <div className="author-card-actions">
                <button
                  type="button"
                  className="author-card-edit-button"
                  title="Editar autor"
                  aria-label="Editar autor"
                  onClick={() => {
                    const source = authors.find((author) => author.id === card.authorId);
                    if (source) {
                      openEditModal(source);
                    }
                  }}
                  disabled={Boolean(deletingById[card.authorId])}
                >
                  Editar
                </button>

                <button
                  type="button"
                  className="author-card-delete-button"
                  title="Eliminar autor"
                  aria-label="Eliminar autor"
                  onClick={() => {
                    void deleteAuthor(card);
                  }}
                  disabled={Boolean(deletingById[card.authorId])}
                >
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
                    <path d="M10 11v6" />
                    <path d="M14 11v6" />
                  </svg>
                </button>
              </div>
            </article>
          ))}
        </section>

        {showCreateModal ? (
          <div
            className="authors-users-modal-overlay"
            onClick={closeCreateModal}
            aria-hidden="true"
          >
            <div
              className="authors-users-modal"
              onClick={(event) => event.stopPropagation()}
              role="dialog"
              aria-modal="true"
              aria-labelledby="new-author-title"
            >
              <h2 id="new-author-title" className="authors-users-modal-title">
                Nuevo Autor
              </h2>

              <form className="authors-users-form" onSubmit={createAuthor}>
                <label htmlFor="new-author-name">Nombre completo</label>
                <input
                  id="new-author-name"
                  type="text"
                  placeholder="Ej: Juan Perez"
                  value={form.name}
                  onChange={(event) => updateFormField("name", event.target.value)}
                />

                <label htmlFor="new-author-email">Correo electronico</label>
                <input
                  id="new-author-email"
                  type="email"
                  placeholder="Ej: juan.perez@periodico.com"
                  value={form.email}
                  onChange={(event) => updateFormField("email", event.target.value)}
                />

                <label htmlFor="new-author-bio">Cargo / Biografia</label>
                <input
                  id="new-author-bio"
                  type="text"
                  placeholder="Ej: Editor en Jefe, Periodista de Investigacion..."
                  value={form.bio}
                  onChange={(event) => updateFormField("bio", event.target.value)}
                />

                <label htmlFor="new-author-password">Contrasena</label>
                <div className="authors-users-password-row">
                  <input
                    id="new-author-password"
                    type="text"
                    placeholder="Genera una contrasena"
                    value={form.password}
                    onChange={(event) => updateFormField("password", event.target.value)}
                  />

                  <button
                    type="button"
                    className="authors-users-generate-password"
                    onClick={() => updateFormField("password", randomPassword())}
                  >
                    Generar
                  </button>
                </div>

                <fieldset className="authors-users-role-fieldset">
                  <legend>Rol del usuario</legend>

                  <label className="authors-users-role-option" htmlFor="new-author-role-admin">
                    <input
                      id="new-author-role-admin"
                      type="radio"
                      name="new-author-role"
                      checked={form.role === "admin"}
                      onChange={() => updateFormField("role", "admin")}
                    />
                    <div>
                      <p>Administrador</p>
                      <span>
                        Acceso completo a todas las funciones, puede gestionar usuarios y
                        categorias.
                      </span>
                    </div>
                  </label>

                  <label className="authors-users-role-option" htmlFor="new-author-role-editor">
                    <input
                      id="new-author-role-editor"
                      type="radio"
                      name="new-author-role"
                      checked={form.role === "editor"}
                      onChange={() => updateFormField("role", "editor")}
                    />
                    <div>
                      <p>Editor</p>
                      <span>Puede crear y editar sus propias publicaciones.</span>
                    </div>
                  </label>
                </fieldset>

                {createError ? <p className="authors-users-modal-error">{createError}</p> : null}

                <div className="authors-users-modal-actions">
                  <button type="button" onClick={closeCreateModal} disabled={creating}>
                    Cancelar
                  </button>
                  <button type="submit" className="primary" disabled={creating}>
                    {creating ? "Creando..." : "Crear Autor"}
                  </button>
                </div>
              </form>
            </div>
          </div>
        ) : null}

        {editForm ? (
          <div className="authors-users-modal-overlay" onClick={closeEditModal} aria-hidden="true">
            <div
              className="authors-users-modal"
              onClick={(event) => event.stopPropagation()}
              role="dialog"
              aria-modal="true"
              aria-labelledby="edit-author-title"
            >
              <h2 id="edit-author-title" className="authors-users-modal-title">
                Editar Autor
              </h2>

              <form className="authors-users-form" onSubmit={submitEditAuthor}>
                <label htmlFor="edit-author-name">Nombre completo</label>
                <input
                  id="edit-author-name"
                  type="text"
                  value={editForm.name}
                  onChange={(event) =>
                    setEditForm((prev) => (prev ? { ...prev, name: event.target.value } : prev))
                  }
                />

                <label htmlFor="edit-author-bio">Cargo / Biografia</label>
                <input
                  id="edit-author-bio"
                  type="text"
                  value={editForm.bio}
                  onChange={(event) =>
                    setEditForm((prev) => (prev ? { ...prev, bio: event.target.value } : prev))
                  }
                />

                {editError ? <p className="authors-users-modal-error">{editError}</p> : null}

                <div className="authors-users-modal-actions">
                  <button type="button" onClick={closeEditModal} disabled={savingEdit}>
                    Cancelar
                  </button>
                  <button type="submit" className="primary" disabled={savingEdit}>
                    {savingEdit ? "Guardando..." : "Guardar cambios"}
                  </button>
                </div>
              </form>
            </div>
          </div>
        ) : null}
      </main>
    </div>
  );
};

export default AuthorsUsers;

