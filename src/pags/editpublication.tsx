import { type FormEvent, useEffect, useMemo, useState } from "react";
import { useNavigate, useParams } from "react-router-dom";
import { ArticleContentEditor } from "../components/articlecontenteditor.tsx";
import { Sidebar } from "../components/sidebar.tsx";
import { API_BASE_URL } from "../libs/config.ts";
import { ApiError, apiFetch } from "../libs/http.ts";

type PublicationStatus = "draft" | "scheduled" | "published";

type PublicationForm = {
  title: string;
  excerpt: string;
  content: string;
  status: PublicationStatus;
  categoryId: string;
  tags: string;
  authorId: string;
  scheduledAt: string;
  featuredImageUrl: string;
};

type AuthorOption = {
  id: string;
  name: string;
  avatarUrl: string | null;
  bio: string | null;
};

type CategoryOption = {
  id: string;
  name: string;
};

type ArticleDetailResponse = {
  id?: string;
  title?: string;
  excerpt?: string;
  content?: string;
  status?: string;
  authorId?: string;
  categoryIds?: unknown;
  tags?: unknown;
  scheduledAt?: string | null;
  featuredImageUrl?: string | null;
};

const INITIAL_FORM: PublicationForm = {
  title: "",
  excerpt: "",
  content: "",
  status: "draft",
  categoryId: "",
  tags: "",
  authorId: "",
  scheduledAt: "",
  featuredImageUrl: "",
};

const toDatetimeLocal = (isoValue: string | null | undefined): string => {
  if (!isoValue) {
    return "";
  }

  const date = new Date(isoValue);
  if (Number.isNaN(date.getTime())) {
    return "";
  }

  const year = String(date.getFullYear());
  const month = String(date.getMonth() + 1).padStart(2, "0");
  const day = String(date.getDate()).padStart(2, "0");
  const hours = String(date.getHours()).padStart(2, "0");
  const minutes = String(date.getMinutes()).padStart(2, "0");

  return `${year}-${month}-${day}T${hours}:${minutes}`;
};

const toScheduledIso = (value: string): string | null => {
  if (!value) {
    return null;
  }

  const parsed = new Date(value);
  if (Number.isNaN(parsed.getTime())) {
    return null;
  }

  return parsed.toISOString();
};

const parseTagsInput = (value: string): string[] => {
  return Array.from(
    new Set(
      value
        .split(",")
        .map((tag) => tag.trim())
        .filter((tag) => tag.length > 0),
    ),
  );
};

const EditPublication = () => {
  const navigate = useNavigate();
  const { id } = useParams<{ id: string }>();
  const [form, setForm] = useState<PublicationForm>(INITIAL_FORM);
  const [authors, setAuthors] = useState<AuthorOption[]>([]);
  const [categories, setCategories] = useState<CategoryOption[]>([]);
  const [loading, setLoading] = useState<boolean>(true);
  const [submitting, setSubmitting] = useState<boolean>(false);
  const [message, setMessage] = useState<string>("");
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    if (!id) {
      setError("No se encontro el ID de la publicacion.");
      setLoading(false);
      return;
    }

    const controller = new AbortController();

    const loadInitialData = async () => {
      try {
        setLoading(true);
        setError(null);

        const [authorsPayload, categoriesPayload, article] = await Promise.all([
          apiFetch<unknown[]>(`${API_BASE_URL}/api/v1/author`, {
            method: "GET",
            credentials: "include",
            signal: controller.signal,
          }),
          apiFetch<unknown[]>(`${API_BASE_URL}/api/v1/category`, {
            method: "GET",
            credentials: "include",
            signal: controller.signal,
          }),
          apiFetch<ArticleDetailResponse>(`${API_BASE_URL}/api/v1/article/${id}`, {
            method: "GET",
            credentials: "include",
            signal: controller.signal,
          }),
        ]);

        const authorOptions = (Array.isArray(authorsPayload) ? authorsPayload : [])
          .map((item): AuthorOption | null => {
            if (!item || typeof item !== "object") {
              return null;
            }

            const record = item as Record<string, unknown>;
            if (typeof record.id !== "string" || typeof record.name !== "string") {
              return null;
            }

            return {
              id: record.id,
              name: record.name,
              avatarUrl: typeof record.avatarUrl === "string" ? record.avatarUrl : null,
              bio: typeof record.bio === "string" ? record.bio : null,
            };
          })
          .filter((item): item is AuthorOption => item !== null);

        const categoryOptions = (Array.isArray(categoriesPayload) ? categoriesPayload : [])
          .map((item): CategoryOption | null => {
            if (!item || typeof item !== "object") {
              return null;
            }

            const record = item as Record<string, unknown>;
            if (typeof record.id !== "string" || typeof record.name !== "string") {
              return null;
            }

            return {
              id: record.id,
              name: record.name,
            };
          })
          .filter((item): item is CategoryOption => item !== null);

        setAuthors(authorOptions);
        setCategories(categoryOptions);

        const categoryIds = Array.isArray(article.categoryIds)
          ? article.categoryIds.filter((value): value is string => typeof value === "string")
          : [];

        setForm({
          title: typeof article.title === "string" ? article.title : "",
          excerpt: typeof article.excerpt === "string" ? article.excerpt : "",
          content: typeof article.content === "string" ? article.content : "",
          status:
            article.status === "published" ||
            article.status === "scheduled" ||
            article.status === "draft"
              ? article.status
              : "draft",
          authorId: typeof article.authorId === "string" ? article.authorId : "",
          categoryId: categoryIds[0] ?? "",
          tags: Array.isArray(article.tags)
            ? article.tags.filter((tag): tag is string => typeof tag === "string").join(", ")
            : "",
          scheduledAt: toDatetimeLocal(article.scheduledAt),
          featuredImageUrl:
            typeof article.featuredImageUrl === "string" ? article.featuredImageUrl : "",
        });
      } catch (err: unknown) {
        if (err instanceof Error && err.name === "AbortError") {
          return;
        }

        if (err instanceof ApiError && (err.status === 401 || err.status === 403)) {
          navigate("/adminlogin", { replace: true });
          return;
        }

        if (err instanceof ApiError && err.status === 404) {
          setError("La publicacion no existe o fue eliminada.");
        } else {
          setError(err instanceof Error ? err.message : "No se pudo cargar la publicacion.");
        }
      } finally {
        setLoading(false);
      }
    };

    void loadInitialData();

    return () => controller.abort();
  }, [id, navigate]);

  const selectedAuthorName = useMemo(() => {
    const found = authors.find((author) => author.id === form.authorId);
    return found?.name ?? "Sin autor";
  }, [authors, form.authorId]);

  const selectedAuthor = useMemo(() => {
    return authors.find((author) => author.id === form.authorId) ?? null;
  }, [authors, form.authorId]);

  const selectedCategoryName = useMemo(() => {
    const found = categories.find((category) => category.id === form.categoryId);
    return found?.name ?? "General";
  }, [categories, form.categoryId]);

  const updateField = <K extends keyof PublicationForm>(
    field: K,
    value: PublicationForm[K],
  ) => {
    setForm((prev) => ({ ...prev, [field]: value }));
  };

  const submitUpdate = async (event: FormEvent) => {
    event.preventDefault();

    if (!id) {
      setError("No se encontro el ID de la publicacion.");
      return;
    }

    const title = form.title.trim();
    const excerpt = form.excerpt.trim();
    const content = form.content.trim();

    if (title.length < 3) {
      setError("El titulo debe tener al menos 3 caracteres.");
      return;
    }

    if (excerpt.length < 3) {
      setError("La descripcion debe tener al menos 3 caracteres.");
      return;
    }

    if (content.length < 10) {
      setError("El contenido debe tener al menos 10 caracteres.");
      return;
    }

    if (!form.authorId) {
      setError("Debes seleccionar un autor valido.");
      return;
    }

    const scheduledAtIso = toScheduledIso(form.scheduledAt);
    const tags = parseTagsInput(form.tags);
    if (form.status === "scheduled" && !scheduledAtIso) {
      setError("Para programar, indica una fecha y hora validas.");
      return;
    }

    setSubmitting(true);
    setError(null);
    setMessage("");

    try {
      const payload: Record<string, unknown> = {
        title,
        excerpt,
        content,
        status: form.status,
        authorId: form.authorId,
        categoryIds: form.categoryId ? [form.categoryId] : [],
        tags,
        featuredImageUrl: form.featuredImageUrl.trim() || null,
      };

      if (form.status === "scheduled") {
        payload.scheduledAt = scheduledAtIso;
      }

      await apiFetch<Record<string, unknown>>(`${API_BASE_URL}/api/v1/article/${id}`, {
        method: "PATCH",
        credentials: "include",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(payload),
      });

      setMessage("Publicacion actualizada correctamente.");
    } catch (err: unknown) {
      if (err instanceof ApiError && (err.status === 401 || err.status === 403)) {
        navigate("/adminlogin", { replace: true });
        return;
      }

      setError(err instanceof Error ? err.message : "No se pudo actualizar la publicacion.");
    } finally {
      setSubmitting(false);
    }
  };

  const handlePreview = () => {
    if (!id) {
      return;
    }

    const previewData = {
      article: {
        id,
        title: form.title.trim() || "Vista previa sin titulo",
        excerpt: form.excerpt.trim() || "Aun no has escrito una descripcion.",
        content: form.content.trim() || "El contenido del articulo se mostrara aqui.",
        featuredImageUrl: form.featuredImageUrl.trim() || null,
        tags: parseTagsInput(form.tags),
        authorName: selectedAuthorName,
        authorAvatarUrl: selectedAuthor?.avatarUrl,
        authorRole: selectedAuthor?.bio,
        categoryName: selectedCategoryName,
        publishedAt: form.status === "published" ? new Date().toISOString() : null,
      },
    };
    
    localStorage.setItem("periodico_preview_draft", JSON.stringify(previewData));
    window.open(`/publication/${id}/preview`, "_blank");
  };

  return (
    <div className="layout dashboard-layout">
      <aside className="sidebar">
        <Sidebar />
      </aside>

      <main className="content new-publication-content">
        <header className="new-publication-header">
          <div>
            <h1 className="new-publication-title">Editar Publicacion</h1>
            <p className="new-publication-subtitle">Modifica la entrada y guarda los cambios</p>
          </div>

          <div className="new-publication-header-actions">
            <button
              type="button"
              className="new-publication-outline-button"
              onClick={() => navigate("/allentries")}
              disabled={submitting}
            >
              Volver
            </button>
            <button
              type="button"
              className="new-publication-outline-button"
              onClick={handlePreview}
              disabled={submitting || loading}
            >
              Vista previa
            </button>
            <button
              type="submit"
              form="edit-publication-form"
              className="new-publication-primary-button"
              disabled={submitting || loading}
            >
              {submitting ? "Guardando..." : "Guardar cambios"}
            </button>
          </div>
        </header>

        {error ? <p className="new-publication-message error">{error}</p> : null}
        {!error && message ? <p className="new-publication-message success">{message}</p> : null}
        {loading ? <p className="new-publication-message">Cargando publicacion...</p> : null}

        <form id="edit-publication-form" className="new-publication-grid" onSubmit={submitUpdate}>
          <section className="new-publication-main-column">
            <article className="new-publication-card">
              <label className="new-publication-label" htmlFor="edit-publication-title">
                Titulo
              </label>
              <input
                id="edit-publication-title"
                className="new-publication-input"
                type="text"
                placeholder="Escribe un titulo atractivo..."
                value={form.title}
                onChange={(event) => updateField("title", event.target.value)}
                disabled={loading}
              />
            </article>

            <article className="new-publication-card">
              <label className="new-publication-label" htmlFor="edit-publication-excerpt">
                Descripcion general
              </label>
              <textarea
                id="edit-publication-excerpt"
                className="new-publication-textarea"
                rows={4}
                maxLength={200}
                placeholder="Escribe una breve descripcion del articulo..."
                value={form.excerpt}
                onChange={(event) => updateField("excerpt", event.target.value)}
                disabled={loading}
              />
              <p className="new-publication-helper-text">
                Recomendacion: no mas de 3 lineas. ({form.excerpt.length}/200 caracteres)
              </p>
            </article>

            <article className="new-publication-card">
              <p className="new-publication-label">
                Contenido del articulo
              </p>
              <ArticleContentEditor
                value={form.content}
                onChange={(content) => updateField("content", content)}
                disabled={loading}
                onUnauthorized={() => navigate("/adminlogin", { replace: true })}
              />
            </article>
          </section>

          <aside className="new-publication-side-column">
            <article className="new-publication-card">
              <label className="new-publication-label" htmlFor="edit-publication-status">
                Estado
              </label>
              <select
                id="edit-publication-status"
                className="new-publication-select"
                value={form.status}
                onChange={(event) => updateField("status", event.target.value as PublicationStatus)}
                disabled={loading}
              >
                <option value="draft">Borrador</option>
                <option value="scheduled">Programado</option>
                <option value="published">Publicado</option>
              </select>

              {form.status === "scheduled" ? (
                <>
                  <label className="new-publication-label mt-12" htmlFor="edit-publication-scheduled-at">
                    Fecha de publicacion
                  </label>
                  <input
                    id="edit-publication-scheduled-at"
                    className="new-publication-input"
                    type="datetime-local"
                    value={form.scheduledAt}
                    onChange={(event) => updateField("scheduledAt", event.target.value)}
                    disabled={loading}
                  />
                </>
              ) : null}
            </article>

            <article className="new-publication-card">
              <label className="new-publication-label" htmlFor="edit-publication-category">
                Categoria
              </label>
              <select
                id="edit-publication-category"
                className="new-publication-select"
                value={form.categoryId}
                onChange={(event) => updateField("categoryId", event.target.value)}
                disabled={loading}
              >
                <option value="">Seleccionar categoria</option>
                {categories.map((category) => (
                  <option key={category.id} value={category.id}>
                    {category.name}
                  </option>
                ))}
              </select>
            </article>

            <article className="new-publication-card">
              <label className="new-publication-label" htmlFor="edit-publication-image-url">
                Imagen destacada (URL)
              </label>
              <input
                id="edit-publication-image-url"
                className="new-publication-input"
                type="url"
                placeholder="https://..."
                value={form.featuredImageUrl}
                onChange={(event) => updateField("featuredImageUrl", event.target.value)}
                disabled={loading}
              />
              {form.featuredImageUrl ? (
                <img
                  src={form.featuredImageUrl}
                  alt="Vista previa de imagen destacada"
                  className="new-publication-image-preview"
                />
              ) : null}
            </article>

            <article className="new-publication-card">
              <label className="new-publication-label" htmlFor="edit-publication-tags">
                Etiquetas
              </label>
              <input
                id="edit-publication-tags"
                className="new-publication-input"
                type="text"
                placeholder="Separadas por comas"
                value={form.tags}
                onChange={(event) => updateField("tags", event.target.value)}
                disabled={loading}
              />
              <p className="new-publication-helper-text">Separadas por comas ({parseTagsInput(form.tags).length})</p>
            </article>

            <article className="new-publication-card">
              <label className="new-publication-label" htmlFor="edit-publication-author">
                Autor
              </label>
              <select
                id="edit-publication-author"
                className="new-publication-select"
                value={form.authorId}
                onChange={(event) => updateField("authorId", event.target.value)}
                disabled={loading}
              >
                <option value="">Seleccionar autor</option>
                {authors.map((author) => (
                  <option key={author.id} value={author.id}>
                    {author.name}
                  </option>
                ))}
              </select>
              <p className="new-publication-author">Seleccionado: {selectedAuthorName}</p>
            </article>
          </aside>
        </form>
      </main>
    </div>
  );
};

export default EditPublication;
