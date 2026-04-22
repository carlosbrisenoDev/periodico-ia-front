import { type ChangeEvent, type FormEvent, useEffect, useMemo, useRef, useState } from "react";
import { useNavigate } from "react-router-dom";
import { Sidebar } from "../components/sidebar.tsx";
import { API_BASE_URL } from "../libs/config.ts";
import { ApiError, apiFetch, getMe } from "../libs/http.ts";

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
  userId: string | null;
  avatarUrl: string | null;
  bio: string | null;
};

type CategoryOption = {
  id: string;
  name: string;
};

type CreatedArticleResponse = {
  id?: string;
  title?: string;
  status?: string;
};

type ImageAsset = {
  id: string;
  filename: string;
  url: string;
};

type UploadImageResponse = {
  id?: string;
  filename?: string;
  url?: string;
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

const normalizeImageUrl = (value: string): string => {
  if (!value) {
    return "";
  }

  if (value.startsWith("http://") || value.startsWith("https://")) {
    return value;
  }

  return `${API_BASE_URL}${value.startsWith("/") ? value : `/${value}`}`;
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

const NewPublication = () => {
  const navigate = useNavigate();
  const [form, setForm] = useState<PublicationForm>(INITIAL_FORM);
  const [authors, setAuthors] = useState<AuthorOption[]>([]);
  const [categories, setCategories] = useState<CategoryOption[]>([]);
  const [loadingOptions, setLoadingOptions] = useState<boolean>(true);
  const [submitting, setSubmitting] = useState<boolean>(false);
  const [showImageModal, setShowImageModal] = useState<boolean>(false);
  const [showLibraryModal, setShowLibraryModal] = useState<boolean>(false);
  const [images, setImages] = useState<ImageAsset[]>([]);
  const [loadingImages, setLoadingImages] = useState<boolean>(false);
  const [uploadingImage, setUploadingImage] = useState<boolean>(false);
  const [imageError, setImageError] = useState<string | null>(null);
  const [message, setMessage] = useState<string>("");
  const [error, setError] = useState<string | null>(null);
  const fileInputRef = useRef<HTMLInputElement | null>(null);

  useEffect(() => {
    const controller = new AbortController();

    const loadOptions = async () => {
      try {
        setLoadingOptions(true);
        setError(null);

        const [profile, authorsPayload, categoriesPayload] = await Promise.all([
          getMe(controller.signal),
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
              userId: typeof record.userId === "string" ? record.userId : null,
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

        const authorLinkedToSession = authorOptions.find((author) => author.userId === profile.id);
        const fallbackAuthor = authorOptions[0] ?? null;
        const selectedAuthorId = authorLinkedToSession?.id ?? fallbackAuthor?.id ?? "";

        setForm((prev) => ({
          ...prev,
          authorId: prev.authorId || selectedAuthorId,
          categoryId: prev.categoryId || categoryOptions[0]?.id || "",
        }));

        if (!selectedAuthorId) {
          setError("No hay autores disponibles. Crea un autor antes de publicar.");
        }
      } catch (err: unknown) {
        if (err instanceof Error && err.name === "AbortError") {
          return;
        }

        if (err instanceof ApiError && (err.status === 401 || err.status === 403)) {
          navigate("/adminlogin", { replace: true });
          return;
        }

        setError(err instanceof Error ? err.message : "No se pudieron cargar autores/categorias.");
      } finally {
        setLoadingOptions(false);
      }
    };

    void loadOptions();

    return () => controller.abort();
  }, [navigate]);

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

  const submitArticle = async (nextStatus: PublicationStatus) => {
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
    if (nextStatus === "scheduled" && !scheduledAtIso) {
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
        status: nextStatus,
        authorId: form.authorId,
        categoryIds: form.categoryId ? [form.categoryId] : [],
        featuredImageUrl: form.featuredImageUrl.trim() || null,
        tags,
        isFeatured: false,
      };

      if (nextStatus === "scheduled") {
        payload.scheduledAt = scheduledAtIso;
      }

      const created = await apiFetch<CreatedArticleResponse>(`${API_BASE_URL}/api/v1/article`, {
        method: "POST",
        credentials: "include",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(payload),
      });

      const actionLabel =
        nextStatus === "published"
          ? "Publicacion creada y publicada."
          : nextStatus === "scheduled"
            ? "Publicacion programada correctamente."
            : "Borrador guardado correctamente.";

      setMessage(`${actionLabel}${created.id ? ` ID: ${created.id}` : ""}`);

      setForm((prev) => ({
        ...INITIAL_FORM,
        authorId: prev.authorId,
        categoryId: prev.categoryId,
      }));
    } catch (err: unknown) {
      if (err instanceof ApiError && (err.status === 401 || err.status === 403)) {
        navigate("/adminlogin", { replace: true });
        return;
      }

      setError(err instanceof Error ? err.message : "No se pudo guardar la publicacion.");
    } finally {
      setSubmitting(false);
    }
  };

  const handleDraftSave = async (event: FormEvent) => {
    event.preventDefault();
    await submitArticle("draft");
  };

  const handlePublish = async () => {
    await submitArticle("published");
  };

  const handlePreview = () => {
    navigate("/publication/preview", {
      state: {
        article: {
          title: form.title.trim() || "Vista previa sin titulo",
          excerpt: form.excerpt.trim() || "Aun no has escrito una descripcion.",
          content: form.content.trim() || "El contenido del articulo se mostrara aqui.",
          featuredImageUrl: form.featuredImageUrl.trim() || null,
          tags: parseTagsInput(form.tags),
          authorName: selectedAuthorName,
            authorAvatarUrl: selectedAuthor?.avatarUrl,
            authorRole: selectedAuthor?.bio,
          categoryName: selectedCategoryName,
        },
      },
    });
  };

  const loadImageLibrary = async () => {
    setLoadingImages(true);
    setImageError(null);

    try {
      const payload = await apiFetch<unknown[]>(`${API_BASE_URL}/api/v1/image?limit=24`, {
        method: "GET",
        credentials: "include",
      });

      const normalized = (Array.isArray(payload) ? payload : [])
        .map((item): ImageAsset | null => {
          if (!item || typeof item !== "object") {
            return null;
          }

          const record = item as Record<string, unknown>;
          if (
            typeof record.id !== "string" ||
            typeof record.filename !== "string" ||
            typeof record.url !== "string"
          ) {
            return null;
          }

          return {
            id: record.id,
            filename: record.filename,
            url: normalizeImageUrl(record.url),
          };
        })
        .filter((item): item is ImageAsset => item !== null);

      setImages(normalized);
    } catch (err: unknown) {
      if (err instanceof ApiError && (err.status === 401 || err.status === 403)) {
        navigate("/adminlogin", { replace: true });
        return;
      }

      setImageError(err instanceof Error ? err.message : "No se pudo cargar la biblioteca.");
    } finally {
      setLoadingImages(false);
    }
  };

  const openImagePicker = () => {
    setShowImageModal(true);
    setImageError(null);
  };

  const openLibrary = async () => {
    setShowImageModal(false);
    setShowLibraryModal(true);
    await loadImageLibrary();
  };

  const uploadFeaturedImage = async (event: ChangeEvent<HTMLInputElement>) => {
    const selectedFile = event.target.files?.[0];
    event.target.value = "";

    if (!selectedFile) {
      return;
    }

    setUploadingImage(true);
    setImageError(null);

    try {
      const formData = new FormData();
      formData.append("image", selectedFile);

      const uploaded = await apiFetch<UploadImageResponse>(`${API_BASE_URL}/api/v1/image/upload`, {
        method: "POST",
        credentials: "include",
        body: formData,
      });

      const uploadedUrl = typeof uploaded.url === "string" ? normalizeImageUrl(uploaded.url) : "";
      if (!uploadedUrl) {
        throw new Error("No se recibio la URL de la imagen subida.");
      }

      updateField("featuredImageUrl", uploadedUrl);
      setShowImageModal(false);
      setMessage("Imagen destacada cargada correctamente.");
    } catch (err: unknown) {
      if (err instanceof ApiError && (err.status === 401 || err.status === 403)) {
        navigate("/adminlogin", { replace: true });
        return;
      }

      setImageError(err instanceof Error ? err.message : "No se pudo subir la imagen.");
    } finally {
      setUploadingImage(false);
    }
  };

  const selectLibraryImage = (url: string) => {
    updateField("featuredImageUrl", normalizeImageUrl(url));
    setShowLibraryModal(false);
    setMessage("Imagen destacada seleccionada desde la biblioteca.");
  };

  return (
    <div className="layout dashboard-layout">
      <aside className="sidebar">
        <Sidebar />
      </aside>

      <main className="content new-publication-content">
        <header className="new-publication-header">
          <div>
            <h1 className="new-publication-title">Nueva Publicacion</h1>
            <p className="new-publication-subtitle">Crea una nueva entrada para el periodico</p>
          </div>

          <div className="new-publication-header-actions">
            <button
              type="button"
              className="new-publication-outline-button"
              onClick={handlePreview}
              disabled={submitting || loadingOptions}
            >
              Vista previa
            </button>
            <button
              type="submit"
              form="new-publication-form"
              className="new-publication-outline-button"
              disabled={submitting || loadingOptions}
            >
              {submitting ? "Guardando..." : "Guardar"}
            </button>
            <button
              type="button"
              className="new-publication-primary-button"
              onClick={() => {
                void handlePublish();
              }}
              disabled={submitting || loadingOptions}
            >
              Guardar y publicar
            </button>
          </div>
        </header>

        {error ? <p className="new-publication-message error">{error}</p> : null}
        {!error && message ? <p className="new-publication-message success">{message}</p> : null}
        {loadingOptions ? <p className="new-publication-message">Cargando autores y categorias...</p> : null}

        <form id="new-publication-form" className="new-publication-grid" onSubmit={handleDraftSave}>
          <section className="new-publication-main-column">
            <article className="new-publication-card">
              <label className="new-publication-label" htmlFor="new-publication-title">
                Titulo
              </label>
              <input
                id="new-publication-title"
                className="new-publication-input"
                type="text"
                placeholder="Escribe un titulo atractivo..."
                value={form.title}
                onChange={(event) => updateField("title", event.target.value)}
              />
            </article>

            <article className="new-publication-card">
              <label className="new-publication-label" htmlFor="new-publication-excerpt">
                Descripcion general
              </label>
              <textarea
                id="new-publication-excerpt"
                className="new-publication-textarea"
                rows={4}
                maxLength={200}
                placeholder="Escribe una breve descripcion del articulo..."
                value={form.excerpt}
                onChange={(event) => updateField("excerpt", event.target.value)}
              />
              <p className="new-publication-helper-text">
                Recomendacion: no mas de 3 lineas. ({form.excerpt.length}/200 caracteres)
              </p>
            </article>

            <article className="new-publication-card">
              <label className="new-publication-label" htmlFor="new-publication-content">
                Contenido del articulo
              </label>

              <div className="new-publication-editor-wrap">
                <textarea
                  id="new-publication-content"
                  className="new-publication-editor"
                  rows={10}
                  placeholder="Escribe un parrafo..."
                  value={form.content}
                  onChange={(event) => updateField("content", event.target.value)}
                />

                <div className="new-publication-editor-toolbar" aria-hidden="true">
                  <span className="new-publication-chip">Texto</span>
                  <span className="new-publication-chip">Imagenes</span>
                  <span className="new-publication-chip">Subtitulo</span>
                </div>
              </div>
            </article>
          </section>

          <aside className="new-publication-side-column">
            <article className="new-publication-card">
              <label className="new-publication-label" htmlFor="new-publication-status">
                Estado
              </label>
              <select
                id="new-publication-status"
                className="new-publication-select"
                value={form.status}
                onChange={(event) =>
                  updateField("status", event.target.value as PublicationStatus)
                }
              >
                <option value="draft">Borrador</option>
                <option value="scheduled">Programado</option>
                <option value="published">Publicado</option>
              </select>

              {form.status === "scheduled" ? (
                <>
                  <label className="new-publication-label mt-12" htmlFor="new-publication-scheduled-at">
                    Fecha de publicacion
                  </label>
                  <input
                    id="new-publication-scheduled-at"
                    className="new-publication-input"
                    type="datetime-local"
                    value={form.scheduledAt}
                    onChange={(event) => updateField("scheduledAt", event.target.value)}
                  />
                </>
              ) : null}
            </article>

            <article className="new-publication-card">
              <label className="new-publication-label" htmlFor="new-publication-category">
                Categoria
              </label>
              <select
                id="new-publication-category"
                className="new-publication-select"
                value={form.categoryId}
                onChange={(event) => updateField("categoryId", event.target.value)}
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
              <p className="new-publication-label">Imagen destacada</p>
              <button
                type="button"
                className="new-publication-upload-box"
                onClick={openImagePicker}
                disabled={submitting || loadingOptions}
              >
                <span className="new-publication-upload-title">Subir nueva imagen</span>
                <span className="new-publication-upload-subtitle">PNG, JPG o WEBP (max. 5MB)</span>
              </button>
              <span className="new-publication-upload-separator">o</span>
              <button
                type="button"
                className="new-publication-upload-box"
                onClick={() => {
                  void openLibrary();
                }}
                disabled={submitting || loadingOptions}
              >
                <span className="new-publication-upload-title">Seleccionar de la biblioteca</span>
                <span className="new-publication-upload-subtitle">Elige de tus imagenes guardadas</span>
              </button>

              <input
                ref={fileInputRef}
                type="file"
                accept="image/jpeg,image/png,image/webp"
                className="new-publication-file-input"
                onChange={uploadFeaturedImage}
              />

              {form.featuredImageUrl ? (
                <>
                  <img
                    src={form.featuredImageUrl}
                    alt="Vista previa de imagen destacada"
                    className="new-publication-image-preview"
                  />
                  <button
                    type="button"
                    className="new-publication-clear-image"
                    onClick={() => updateField("featuredImageUrl", "")}
                    disabled={submitting || loadingOptions}
                  >
                    Quitar imagen
                  </button>
                </>
              ) : null}
            </article>

            <article className="new-publication-card">
              <label className="new-publication-label" htmlFor="new-publication-tags">
                Etiquetas
              </label>
              <input
                id="new-publication-tags"
                className="new-publication-input"
                type="text"
                placeholder="Separadas por comas"
                value={form.tags}
                onChange={(event) => updateField("tags", event.target.value)}
              />
              <p className="new-publication-helper-text">Separadas por comas ({parseTagsInput(form.tags).length})</p>
            </article>

            <article className="new-publication-card">
              <label className="new-publication-label" htmlFor="new-publication-author">
                Autor
              </label>
              <select
                id="new-publication-author"
                className="new-publication-select"
                value={form.authorId}
                onChange={(event) => updateField("authorId", event.target.value)}
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

        {showImageModal ? (
          <div className="new-publication-modal-overlay" role="dialog" aria-modal="true">
            <div className="new-publication-modal">
              <div className="new-publication-modal-head">
                <h2>Subir Imagen Destacada</h2>
                <button
                  type="button"
                  className="new-publication-modal-close"
                  onClick={() => {
                    setShowImageModal(false);
                    setImageError(null);
                  }}
                >
                  x
                </button>
              </div>

              {imageError ? <p className="new-publication-message error">{imageError}</p> : null}

              <button
                type="button"
                className="new-publication-modal-option"
                onClick={() => fileInputRef.current?.click()}
                disabled={uploadingImage}
              >
                <span className="new-publication-upload-title">Haz clic para subir una imagen</span>
                <span className="new-publication-upload-subtitle">PNG, JPG o WEBP</span>
                <span className="new-publication-modal-option-action">
                  {uploadingImage ? "Subiendo..." : "Seleccionar Archivo"}
                </span>
              </button>

              <span className="new-publication-upload-separator">o</span>

              <button
                type="button"
                className="new-publication-modal-option"
                onClick={() => {
                  void openLibrary();
                }}
                disabled={uploadingImage}
              >
                <span className="new-publication-upload-title">Seleccionar de la biblioteca</span>
                <span className="new-publication-upload-subtitle">Elige de tus imagenes guardadas</span>
              </button>
            </div>
          </div>
        ) : null}

        {showLibraryModal ? (
          <div className="new-publication-modal-overlay" role="dialog" aria-modal="true">
            <div className="new-publication-modal new-publication-library-modal">
              <div className="new-publication-modal-head">
                <h2>Biblioteca de Imagenes</h2>
                <button
                  type="button"
                  className="new-publication-modal-close"
                  onClick={() => {
                    setShowLibraryModal(false);
                    setImageError(null);
                  }}
                >
                  x
                </button>
              </div>

              {loadingImages ? <p className="new-publication-message">Cargando imagenes...</p> : null}
              {!loadingImages && imageError ? <p className="new-publication-message error">{imageError}</p> : null}

              {!loadingImages && !imageError && images.length === 0 ? (
                <p className="new-publication-message">Aun no hay imagenes guardadas.</p>
              ) : null}

              <div className="new-publication-library-grid">
                {images.map((image) => (
                  <button
                    type="button"
                    key={image.id}
                    className="new-publication-library-item"
                    onClick={() => selectLibraryImage(image.url)}
                  >
                    <img src={image.url} alt={image.filename} />
                    <span>{image.filename}</span>
                  </button>
                ))}
              </div>
            </div>
          </div>
        ) : null}
      </main>
    </div>
  );
};

export default NewPublication;

