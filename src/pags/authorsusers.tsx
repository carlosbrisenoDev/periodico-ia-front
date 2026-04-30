import { useEffect, useMemo, useState, type FormEvent } from "react";
import { useNavigate } from "react-router-dom";
import { Sidebar } from "../components/sidebar.tsx";
import { API_BASE_URL } from "../libs/config.ts";
import { ApiError, apiFetch, getMe } from "../libs/http.ts";
import type { ProfileData } from "../libs/types.ts";

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

type EditUserForm = {
  id: string;
  name: string;
  email: string;
  role: string;
};

type CreateAuthorOnlyForm = {
  name: string;
  bio: string;
  userId: string;
};

type UserCard = {
  id: string;
  name: string;
  email: string;
  role: string;
  active: boolean;
  authors: AuthorRecord[];
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
  const [profile, setProfile] = useState<ProfileData | null>(null);
  const [loading, setLoading] = useState<boolean>(true);
  const [error, setError] = useState<string | null>(null);
  const [showCreateModal, setShowCreateModal] = useState<boolean>(false);
  const [editForm, setEditForm] = useState<EditAuthorForm | null>(null);
  const [editUserForm, setEditUserForm] = useState<EditUserForm | null>(null);
  const [createError, setCreateError] = useState<string | null>(null);
  const [editError, setEditError] = useState<string | null>(null);
  const [editUserError, setEditUserError] = useState<string | null>(null);
  const [creating, setCreating] = useState<boolean>(false);
  const [savingEdit, setSavingEdit] = useState<boolean>(false);
  const [savingUserEdit, setSavingUserEdit] = useState<boolean>(false);
  const [deletingById, setDeletingById] = useState<Record<string, boolean>>({});
  const [form, setForm] = useState<NewAuthorForm>(INITIAL_FORM);

  const [showCreateAuthorOnlyModal, setShowCreateAuthorOnlyModal] = useState<boolean>(false);
  const [authorOnlyForm, setAuthorOnlyForm] = useState<CreateAuthorOnlyForm>({ name: "", bio: "", userId: "" });
  const [createAuthorOnlyError, setCreateAuthorOnlyError] = useState<string | null>(null);
  const [creatingAuthorOnly, setCreatingAuthorOnly] = useState<boolean>(false);
  const [expandedUserIds, setExpandedUserIds] = useState<Record<string, boolean>>({});

  const toggleUserExpanded = (userId: string) => {
    setExpandedUserIds(prev => ({
      ...prev,
      [userId]: !prev[userId]
    }));
  };

  useEffect(() => {
    const controller = new AbortController();

    const loadPageData = async () => {
      try {
        setLoading(true);

        const [authorsPayload, usersPayload, profilePayload] = await Promise.all([
          apiFetch<unknown[]>(`${API_BASE_URL}/api/v1/author`, {
            method: "GET",
            credentials: "include",
            signal: controller.signal,
          }),
          apiFetch<UsersResponse>(`${API_BASE_URL}/api/v1/auth/users`, {
            method: "GET",
            credentials: "include",
            signal: controller.signal,
          }).catch(() => ({ users: [] })),
          getMe(controller.signal).catch(() => null),
        ]);

        const normalizedAuthors = (Array.isArray(authorsPayload) ? authorsPayload : [])
          .map((item, index) => normalizeAuthor(item, index))
          .filter((item): item is AuthorRecord => item !== null);

        const normalizedUsers = (Array.isArray(usersPayload.users) ? usersPayload.users : [])
          .map((item, index) => normalizeAdminUser(item, index))
          .filter((item): item is AdminUserRecord => item !== null);

        // Ensure current profile is always in the users list
        if (profilePayload && !normalizedUsers.some(u => u.id === profilePayload.id)) {
          normalizedUsers.push({
            id: profilePayload.id,
            name: profilePayload.name,
            email: profilePayload.email,
            role: profilePayload.role,
            active: true
          });
        }

        setAuthors(normalizedAuthors);
        setUsers(normalizedUsers);
        setProfile(profilePayload);
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

  const cards = useMemo<UserCard[]>(() => {
    const authorsByUserId = new Map<string, AuthorRecord[]>();
    const unlinkedAuthors: AuthorRecord[] = [];

    for (const author of authors) {
      if (author.userId) {
        if (!authorsByUserId.has(author.userId)) {
          authorsByUserId.set(author.userId, []);
        }
        authorsByUserId.get(author.userId)!.push(author);
      } else {
        unlinkedAuthors.push(author);
      }
    }

    const userCards = users
      .filter(user => profile?.role === "admin" || user.id === profile?.id)
      .map((user): UserCard => ({
        id: user.id,
        name: user.name,
        email: user.email,
        role: user.role,
        active: user.active,
        authors: authorsByUserId.get(user.id) || [],
      }));

    if (unlinkedAuthors.length > 0 && profile?.role === "admin") {
      userCards.push({
        id: "unlinked",
        name: "Autores sin usuario",
        email: "Perfiles no vinculados",
        role: "none",
        active: true,
        authors: unlinkedAuthors,
      });
    }

    return userCards;
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

  const openCreateAuthorOnlyModal = () => {
    setAuthorOnlyForm({ name: "", bio: "", userId: profile?.id ?? "" });
    setCreateAuthorOnlyError(null);
    setShowCreateAuthorOnlyModal(true);
  };

  const closeCreateAuthorOnlyModal = () => {
    if (creatingAuthorOnly) {
      return;
    }

    setShowCreateAuthorOnlyModal(false);
    setCreateAuthorOnlyError(null);
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
      setCreateError("Completa nombre, correo y contraseña.");
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

  const openEditUserModal = (user: UserCard) => {
    setEditUserForm({
      id: user.id,
      name: user.name,
      email: user.email,
      role: user.role,
    });
    setEditUserError(null);
  };

  const closeEditUserModal = () => {
    if (savingUserEdit) {
      return;
    }

    setEditUserForm(null);
    setEditUserError(null);
  };

  const submitEditUser = async (event: FormEvent<HTMLFormElement>) => {
    event.preventDefault();

    if (!editUserForm) {
      return;
    }

    const name = editUserForm.name.trim();
    const email = editUserForm.email.trim();

    if (name.length < 2) {
      setEditUserError("El nombre debe tener al menos 2 caracteres.");
      return;
    }

    if (!email) {
      setEditUserError("El correo es obligatorio.");
      return;
    }

    try {
      setSavingUserEdit(true);
      setEditUserError(null);

      const payload = await apiFetch<{ user?: AdminUserRecord }>(
        `${API_BASE_URL}/api/v1/auth/users/${editUserForm.id}`,
        {
          method: "PATCH",
          credentials: "include",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            name,
            email,
            role: editUserForm.role,
          }),
        },
      );

      const resolvedUser = payload.user || { ...editUserForm, active: true };

      setUsers((prev) =>
        prev.map((u) =>
          u.id === editUserForm.id ? { ...u, name: resolvedUser.name, email: resolvedUser.email, role: resolvedUser.role } : u,
        ),
      );
      setEditUserForm(null);
    } catch (err: unknown) {
      if (err instanceof ApiError && (err.status === 401 || err.status === 403)) {
        navigate("/adminlogin", { replace: true });
        return;
      }

      setEditUserError(err instanceof Error ? err.message : "No se pudo actualizar el usuario.");
    } finally {
      setSavingUserEdit(false);
    }
  };

  const submitDeleteUser = async () => {
    if (!editUserForm) return;
    const confirmed = window.confirm(`¿Eliminar al usuario "${editUserForm.name}"? Esta acción no se puede deshacer.`);
    if (!confirmed) return;

    try {
      setSavingUserEdit(true);
      setEditUserError(null);

      await apiFetch<{ message?: string }>(`${API_BASE_URL}/api/v1/auth/users/${editUserForm.id}`, {
        method: "DELETE",
        credentials: "include",
      });

      setUsers((prev) => prev.filter((item) => item.id !== editUserForm.id));
      closeEditUserModal();
    } catch (err: unknown) {
      if (err instanceof ApiError && (err.status === 401 || err.status === 403)) {
        navigate("/adminlogin", { replace: true });
        return;
      }

      setEditUserError(err instanceof Error ? err.message : "No se pudo eliminar el usuario.");
      setSavingUserEdit(false);
    }
  };

  const submitCreateAuthorOnly = async (event: FormEvent<HTMLFormElement>) => {
    event.preventDefault();

    const name = authorOnlyForm.name.trim();
    const bio = authorOnlyForm.bio.trim();
    const selectedUserId = profile?.role === "admin" ? authorOnlyForm.userId : profile?.id;

    if (!name) {
      setCreateAuthorOnlyError("El nombre del autor es obligatorio.");
      return;
    }

    if (!selectedUserId && profile?.role !== "admin") {
      setCreateAuthorOnlyError("Debes seleccionar un usuario para vincularlo.");
      return;
    }

    try {
      setCreatingAuthorOnly(true);
      setCreateAuthorOnlyError(null);

      const authorBody: Record<string, string> = {
        name,
      };

      if (selectedUserId) {
        authorBody.userId = selectedUserId;
      }

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
        userId: typeof authorPayload.userId === "string" ? authorPayload.userId : selectedUserId ?? null,
      };

      setAuthors((prev) => [newAuthor, ...prev]);
      setShowCreateAuthorOnlyModal(false);
    } catch (err: unknown) {
      if (err instanceof ApiError && (err.status === 401 || err.status === 403)) {
        navigate("/adminlogin", { replace: true });
        return;
      }

      setCreateAuthorOnlyError(err instanceof Error ? err.message : "No se pudo crear el autor.");
    } finally {
      setCreatingAuthorOnly(false);
    }
  };

  const deleteAuthor = async (author: AuthorRecord) => {
    const confirmed = window.confirm(`Eliminar al autor "${author.name}"?`);
    if (!confirmed) {
      return;
    }

    try {
      setCreateError(null);
      setDeletingById((prev) => ({ ...prev, [author.id]: true }));

      await apiFetch<{ message?: string }>(`${API_BASE_URL}/api/v1/author/${author.id}`, {
        method: "DELETE",
        credentials: "include",
      });

      setAuthors((prev) => prev.filter((item) => item.id !== author.id));
    } catch (err: unknown) {
      if (err instanceof ApiError && (err.status === 401 || err.status === 403)) {
        navigate("/adminlogin", { replace: true });
        return;
      }

      setCreateError(err instanceof Error ? err.message : "No se pudo eliminar el autor.");
    } finally {
      setDeletingById((prev) => {
        const next = { ...prev };
        delete next[author.id];
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
              Gestiona los autores del periódico y sus permisos.
            </p>
          </div>

          <div className="authors-users-header-actions" style={{ display: "flex", gap: "8px" }}>
            <button
              type="button"
              className="entries-new-button outline"
              onClick={openCreateAuthorOnlyModal}
            >
              + Crear Autor
            </button>
            {profile?.role === "admin" ? (
              <button
                type="button"
                className="entries-new-button"
                onClick={openCreateModal}
              >
                + Nuevo Usuario
              </button>
            ) : null}
          </div>
        </header>

        {loading ? <p className="authors-users-info">Cargando autores...</p> : null}

        {!loading && error ? <p className="authors-users-info error">{error}</p> : null}

        {!loading && !error && cards.length === 0 ? (
          <p className="authors-users-info">No hay autores para mostrar.</p>
        ) : null}

        <section className="authors-users-grid" aria-label="Listado de usuarios y autores">
          {cards.map((card) => (
            <article key={card.id} className="author-card">
              <div className="author-card-main">
                <div className="author-card-avatar" aria-hidden="true">
                  <span>{initialsFromName(card.name)}</span>
                </div>

                <div style={{ minWidth: 0, flex: 1 }}>
                  <h2 className="author-card-name">{card.name}</h2>
                  <p className="author-card-subtitle">
                    {card.authors.length > 0 && card.authors[0].bio 
                      ? card.authors[0].bio 
                      : card.email}
                  </p>
                </div>
              </div>

              <div className="author-card-role-row">
                {card.role !== "none" ? (
                  <span className={`author-card-role ${roleClass(card.role)}`}>
                    {roleLabel(card.role)}
                  </span>
                ) : null}

                {card.active ? null : (
                  <span className="author-card-inactive" title="Usuario inactivo">
                    Inactivo
                  </span>
                )}
              </div>

              <div className="author-card-actions" style={{ gridTemplateColumns: "1fr 1fr" }}>
                <button
                  type="button"
                  className="author-card-edit-button"
                  title="Editar usuario"
                  aria-label="Editar usuario"
                  onClick={() => {
                    openEditUserModal(card);
                  }}
                  disabled={card.id === "unlinked"}
                >
                  Editar
                </button>

                <button
                  type="button"
                  className="author-card-edit-button"
                  title="Ver Autores"
                  aria-label="Ver Autores"
                  onClick={() => toggleUserExpanded(card.id)}
                  style={{ backgroundColor: "var(--background)", color: "var(--text-color)", border: "1px solid var(--border-color)" }}
                >
                  {expandedUserIds[card.id] ? "Ocultar Autores" : `Autores (${card.authors.length})`}
                </button>
              </div>

              {expandedUserIds[card.id] ? (
                <div style={{ marginTop: "16px", paddingTop: "16px", borderTop: "1px solid var(--border-color)", display: "flex", flexDirection: "column", gap: "12px" }}>
                  {card.authors.length === 0 ? (
                    <p style={{ fontSize: "14px", color: "var(--text-muted)" }}>Este usuario no tiene perfiles de autor vinculados.</p>
                  ) : (
                    card.authors.map(author => (
                      <div key={author.id} style={{ display: "flex", justifyContent: "space-between", alignItems: "center", padding: "8px", backgroundColor: "var(--background)", borderRadius: "6px" }}>
                        <div style={{ display: "flex", alignItems: "center", gap: "8px" }}>
                          {author.avatarUrl ? (
                            <img src={author.avatarUrl} alt="" style={{ width: "24px", height: "24px", borderRadius: "50%", objectFit: "cover" }} />
                          ) : (
                            <div style={{ width: "24px", height: "24px", borderRadius: "50%", backgroundColor: "var(--primary-color)", color: "white", display: "flex", alignItems: "center", justifyContent: "center", fontSize: "10px", fontWeight: "bold" }}>
                              {initialsFromName(author.name)}
                            </div>
                          )}
                          <div style={{ minWidth: 0, flex: 1 }}>
                            <p style={{ fontSize: "14px", fontWeight: "600", margin: 0, overflow: "hidden", textOverflow: "ellipsis", whiteSpace: "nowrap" }}>{author.name}</p>
                            <p style={{ fontSize: "12px", color: "var(--text-muted)", margin: 0, overflow: "hidden", textOverflow: "ellipsis", whiteSpace: "nowrap" }}>{author.bio || "Sin biografía"}</p>
                          </div>
                        </div>
                        <div style={{ display: "flex", gap: "4px" }}>
                          <button
                            type="button"
                            onClick={() => openEditModal(author)}
                            style={{ padding: "4px 8px", fontSize: "12px", borderRadius: "4px", backgroundColor: "transparent", border: "1px solid var(--border-color)", cursor: "pointer" }}
                            disabled={Boolean(deletingById[author.id])}
                          >
                            Editar
                          </button>
                          <button
                            type="button"
                            onClick={() => deleteAuthor(author)}
                            style={{ padding: "4px 8px", fontSize: "12px", borderRadius: "4px", backgroundColor: "#fee2e2", color: "#ef4444", border: "none", cursor: "pointer" }}
                            disabled={Boolean(deletingById[author.id])}
                          >
                            Eliminar
                          </button>
                        </div>
                      </div>
                    ))
                  )}
                </div>
              ) : null}
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
                Nuevo Usuario
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

                <label htmlFor="new-author-email">Correo electrónico</label>
                <input
                  id="new-author-email"
                  type="email"
                  placeholder="Ej: juan.perez@periodico.com"
                  value={form.email}
                  onChange={(event) => updateFormField("email", event.target.value)}
                />

                <label htmlFor="new-author-bio">Cargo / Biografía</label>
                <input
                  id="new-author-bio"
                  type="text"
                  placeholder="Ej: Editor en Jefe, Periodista de Investigacion..."
                  value={form.bio}
                  onChange={(event) => updateFormField("bio", event.target.value)}
                />

                <label htmlFor="new-author-password">Contraseña</label>
                <div className="authors-users-password-row">
                  <input
                    id="new-author-password"
                    type="text"
                    placeholder="Genera una contraseña"
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
                    {creating ? "Creando..." : "Crear Usuario"}
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

                <label htmlFor="edit-author-bio">Cargo / Biografía</label>
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
        {showCreateAuthorOnlyModal ? (
          <div className="authors-users-modal-overlay" onClick={closeCreateAuthorOnlyModal} aria-hidden="true">
            <div
              className="authors-users-modal"
              onClick={(event) => event.stopPropagation()}
              role="dialog"
              aria-modal="true"
              aria-labelledby="create-author-only-title"
            >
              <h2 id="create-author-only-title" className="authors-users-modal-title">
                Crear Perfil de Autor
              </h2>

              <form className="authors-users-form" onSubmit={submitCreateAuthorOnly}>
                <label htmlFor="create-author-only-name">Nombre mostrado en artículos</label>
                <input
                  id="create-author-only-name"
                  type="text"
                  placeholder="Ej: Juan Perez"
                  value={authorOnlyForm.name}
                  onChange={(event) =>
                    setAuthorOnlyForm((prev) => ({ ...prev, name: event.target.value }))
                  }
                />

                <label htmlFor="create-author-only-bio">Cargo / Biografía</label>
                <input
                  id="create-author-only-bio"
                  type="text"
                  placeholder="Ej: Editor General"
                  value={authorOnlyForm.bio}
                  onChange={(event) =>
                    setAuthorOnlyForm((prev) => ({ ...prev, bio: event.target.value }))
                  }
                />

                {profile?.role === "admin" ? (
                  <>
                    <label htmlFor="create-author-only-user">Usuario vinculado</label>
                    <select
                      id="create-author-only-user"
                      value={authorOnlyForm.userId}
                      onChange={(event) =>
                        setAuthorOnlyForm((prev) => ({ ...prev, userId: event.target.value }))
                      }
                      style={{
                        padding: "10px 12px",
                        border: "1px solid #dde2ea",
                        borderRadius: "8px",
                        marginBottom: "16px",
                        width: "100%",
                      }}
                    >
                      <option value="">Selecciona un usuario...</option>
                      {users.map((user) => (
                        <option key={user.id} value={user.id}>
                          {user.name} ({user.email})
                        </option>
                      ))}
                    </select>
                  </>
                ) : null}

                {createAuthorOnlyError ? <p className="authors-users-modal-error">{createAuthorOnlyError}</p> : null}

                <div className="authors-users-modal-actions">
                  <button type="button" onClick={closeCreateAuthorOnlyModal} disabled={creatingAuthorOnly}>
                    Cancelar
                  </button>
                  <button type="submit" className="primary" disabled={creatingAuthorOnly}>
                    {creatingAuthorOnly ? "Creando..." : "Crear Autor"}
                  </button>
                </div>
              </form>
            </div>
          </div>
        ) : null}

        {editUserForm ? (
          <div className="authors-users-modal-overlay" onClick={closeEditUserModal} aria-hidden="true">
            <div
              className="authors-users-modal"
              onClick={(event) => event.stopPropagation()}
              role="dialog"
              aria-modal="true"
              aria-labelledby="edit-user-title"
            >
              <h2 id="edit-user-title" className="authors-users-modal-title">
                Editar Usuario
              </h2>

              <form className="authors-users-form" onSubmit={submitEditUser}>
                <label htmlFor="edit-user-name">Nombre completo</label>
                <input
                  id="edit-user-name"
                  type="text"
                  placeholder="Ej: Juan Perez"
                  value={editUserForm.name}
                  onChange={(event) => setEditUserForm((prev) => prev ? { ...prev, name: event.target.value } : null)}
                />

                <label htmlFor="edit-user-email">Correo electrónico</label>
                <input
                  id="edit-user-email"
                  type="email"
                  placeholder="Ej: juan@periodico.com"
                  value={editUserForm.email}
                  onChange={(event) => setEditUserForm((prev) => prev ? { ...prev, email: event.target.value } : null)}
                />

                <fieldset className="authors-users-role-fieldset">
                  <legend>Rol del usuario</legend>
                  <label className="authors-users-role-option">
                    <input
                      type="radio"
                      name="edit-user-role"
                      value="editor"
                      checked={editUserForm.role === "editor"}
                      onChange={() => setEditUserForm((prev) => prev ? { ...prev, role: "editor" } : null)}
                    />
                    <div>
                      <p><strong>Editor</strong></p>
                      <p style={{ color: "var(--text-muted)", fontSize: "0.9rem", margin: 0 }}>Puede crear y editar artículos. No puede gestionar usuarios.</p>
                    </div>
                  </label>
                  <label className="authors-users-role-option">
                    <input
                      type="radio"
                      name="edit-user-role"
                      value="admin"
                      checked={editUserForm.role === "admin"}
                      onChange={() => setEditUserForm((prev) => prev ? { ...prev, role: "admin" } : null)}
                    />
                    <div>
                      <p><strong>Administrador</strong></p>
                      <p style={{ color: "var(--text-muted)", fontSize: "0.9rem", margin: 0 }}>Acceso total. Puede gestionar usuarios y configuraciones.</p>
                    </div>
                  </label>
                </fieldset>

                {editUserError ? <p className="authors-users-modal-error">{editUserError}</p> : null}

                <div className="authors-users-modal-actions" style={{ display: "flex", justifyContent: "space-center", width: "100%" }}>
                  <button
                    type="button"
                    className="danger"
                    onClick={submitDeleteUser}
                    disabled={savingUserEdit || profile?.id === editUserForm.id}
                    style={{ backgroundColor: profile?.id === editUserForm.id ? "#fca5a5" : "#ef4444", color: "white", border: "none", padding: "8px 16px", borderRadius: "8px", fontWeight: "600", cursor: profile?.id === editUserForm.id ? "not-allowed" : "pointer" }}
                    title={profile?.id === editUserForm.id ? "No puedes eliminar tu propio usuario" : "Eliminar Usuario"}
                  >
                    Eliminar Usuario
                  </button>
                  <div style={{ display: "flex", gap: "8px" }}>
                    <button type="button" onClick={closeEditUserModal} disabled={savingUserEdit}>
                      Cancelar
                    </button>
                    <button type="submit" className="primary" disabled={savingUserEdit}>
                      {savingUserEdit ? "Guardando..." : "Guardar cambios"}
                    </button>
                  </div>
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

