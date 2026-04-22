import { useEffect, useState, type FormEvent } from "react";
import { useNavigate } from "react-router-dom";
import { Sidebar } from "../components/sidebar.tsx";
import { API_BASE_URL } from "../libs/config.ts";
import { ApiError, apiFetch } from "../libs/http.ts";

type CategoryItem = {
  id: string;
  name: string;
  slug: string;
  description: string;
  color?: string;
};

type CreateCategoryResponse = {
  id?: string;
  name?: string;
  slug?: string;
  description?: string;
};

type NewCategoryForm = {
  name: string;
  slug: string;
  color: string;
};

const INITIAL_FORM: NewCategoryForm = {
  name: "",
  slug: "",
  color: "#3B82F6",
};

const normalizeCategory = (item: unknown, index: number): CategoryItem | null => {
  if (!item || typeof item !== "object") {
    return null;
  }

  const record = item as Record<string, unknown>;
  const name = typeof record.name === "string" ? record.name.trim() : "";

  if (!name) {
    return null;
  }

  const slugValue = typeof record.slug === "string" ? record.slug.trim() : "";
  const safeSlug = slugValue || name.toLowerCase().replace(/\s+/g, "-");

  return {
    id:
      typeof record.id === "string"
        ? record.id
        : typeof record._id === "string"
          ? record._id
          : `category-${index}`,
    name,
    slug: safeSlug,
    description:
      typeof record.description === "string" && record.description.trim().length > 0
        ? record.description.trim()
        : `/${safeSlug}`,
    color: typeof record.color === "string" ? record.color : undefined,
  };
};

const slugify = (value: string): string => {
  return value
    .toLowerCase()
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .replace(/[^a-z0-9\s-]/g, "")
    .trim()
    .replace(/\s+/g, "-")
    .replace(/-+/g, "-");
};

const getInitial = (name: string): string => {
  const letter = name.trim().charAt(0).toUpperCase();
  return letter || "C";
};

const BADGE_COLORS = [
  "#3B82F6",
  "#10B981",
  "#F59E0B",
  "#8B5CF6",
  "#06B6D4",
  "#EC4899",
  "#6366F1",
  "#EF4444",
];

const colorForCategory = (seed: string): string => {
  let hash = 0;

  for (let index = 0; index < seed.length; index += 1) {
    hash = (hash << 5) - hash + seed.charCodeAt(index);
    hash |= 0;
  }

  return BADGE_COLORS[Math.abs(hash) % BADGE_COLORS.length];
};

const Categories = () => {
  const navigate = useNavigate();
  const [categories, setCategories] = useState<CategoryItem[]>([]);
  const [loading, setLoading] = useState<boolean>(true);
  const [error, setError] = useState<string | null>(null);
  const [showCreateModal, setShowCreateModal] = useState<boolean>(false);
  const [creating, setCreating] = useState<boolean>(false);
  const [createError, setCreateError] = useState<string | null>(null);
  const [form, setForm] = useState<NewCategoryForm>(INITIAL_FORM);

  useEffect(() => {
    const controller = new AbortController();

    const loadCategories = async () => {
      try {
        setLoading(true);

        const payload = await apiFetch<unknown[]>(`${API_BASE_URL}/api/v1/category`, {
          method: "GET",
          credentials: "include",
          signal: controller.signal,
        });

        const normalized = (Array.isArray(payload) ? payload : [])
          .map((item, index) => normalizeCategory(item, index))
          .filter((item): item is CategoryItem => item !== null);

        setCategories(normalized);
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
        setCategories([]);
      } finally {
        setLoading(false);
      }
    };

    void loadCategories();

    return () => controller.abort();
  }, [navigate]);

  const openCreateModal = () => {
    setForm(INITIAL_FORM);
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

  const updateFormField = <K extends keyof NewCategoryForm>(
    field: K,
    value: NewCategoryForm[K],
  ) => {
    setForm((prev) => ({ ...prev, [field]: value }));
  };

  const createCategory = async (event: FormEvent<HTMLFormElement>) => {
    event.preventDefault();

    const name = form.name.trim();
    const finalSlug = (form.slug.trim() || slugify(name)).replace(/^\/+/, "");

    if (name.length < 2) {
      setCreateError("El nombre debe tener al menos 2 caracteres.");
      return;
    }

    if (!finalSlug) {
      setCreateError("Ingresa un slug valido para la categoria.");
      return;
    }

    try {
      setCreating(true);
      setCreateError(null);

      const payload = await apiFetch<CreateCategoryResponse>(
        `${API_BASE_URL}/api/v1/category`,
        {
          method: "POST",
          credentials: "include",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            name,
            slug: finalSlug,
          }),
        },
      );

      const newCategory: CategoryItem = {
        id: typeof payload.id === "string" ? payload.id : `category-${Date.now()}`,
        name: typeof payload.name === "string" ? payload.name : name,
        slug: typeof payload.slug === "string" ? payload.slug : finalSlug,
        description:
          typeof payload.description === "string" && payload.description.trim().length > 0
            ? payload.description
            : `/${finalSlug}`,
        color: form.color,
      };

      setCategories((prev) => [newCategory, ...prev]);
      setShowCreateModal(false);
      setForm(INITIAL_FORM);
    } catch (err: unknown) {
      if (err instanceof ApiError && (err.status === 401 || err.status === 403)) {
        navigate("/adminlogin", { replace: true });
        return;
      }

      setCreateError(err instanceof Error ? err.message : "No se pudo crear la categoria.");
    } finally {
      setCreating(false);
    }
  };

  return (
    <div className="layout dashboard-layout">
      <aside className="sidebar">
        <Sidebar />
      </aside>

      <main className="content categories-content">
        <header className="categories-header">
          <div>
            <h1 className="categories-title">Categorias</h1>
            <p className="categories-subtitle">Organiza tus publicaciones por categorias.</p>
          </div>

          <button
            type="button"
            className="entries-new-button"
            onClick={openCreateModal}
          >
            + Nueva Categoria
          </button>
        </header>

        {loading ? <p className="categories-info">Cargando categorias...</p> : null}
        {!loading && error ? <p className="categories-info error">{error}</p> : null}
        {!loading && !error && categories.length === 0 ? (
          <p className="categories-info">Aun no hay categorias creadas.</p>
        ) : null}

        <section className="categories-grid" aria-label="Listado de categorias">
          {categories.map((category) => (
            <article key={category.id} className="category-card">
              <span
                className="category-badge"
                style={{ backgroundColor: category.color ?? colorForCategory(category.slug) }}
                aria-hidden="true"
              >
                {getInitial(category.name)}
              </span>

              <h2 className="category-name">{category.name}</h2>
              <p className="category-slug">/{category.slug}</p>
            </article>
          ))}
        </section>

        {showCreateModal ? (
          <div
            className="categories-modal-overlay"
            onClick={closeCreateModal}
            aria-hidden="true"
          >
            <div
              className="categories-modal"
              onClick={(event) => event.stopPropagation()}
              role="dialog"
              aria-modal="true"
              aria-labelledby="new-category-title"
            >
              <h2 id="new-category-title" className="categories-modal-title">
                Nueva Categoria
              </h2>

              <form className="categories-form" onSubmit={createCategory}>
                <label htmlFor="new-category-name">Nombre</label>
                <input
                  id="new-category-name"
                  type="text"
                  placeholder="Ej: Opinion"
                  value={form.name}
                  onChange={(event) => updateFormField("name", event.target.value)}
                />

                <label htmlFor="new-category-slug">Slug (URL)</label>
                <input
                  id="new-category-slug"
                  type="text"
                  placeholder="Ej: opinion"
                  value={form.slug}
                  onChange={(event) => updateFormField("slug", event.target.value)}
                />

                <label htmlFor="new-category-color">Color</label>
                <input
                  id="new-category-color"
                  type="color"
                  className="category-color-input"
                  value={form.color}
                  onChange={(event) => updateFormField("color", event.target.value)}
                />

                {createError ? <p className="categories-modal-error">{createError}</p> : null}

                <div className="categories-modal-actions">
                  <button type="button" onClick={closeCreateModal} disabled={creating}>
                    Cancelar
                  </button>
                  <button type="submit" className="primary" disabled={creating}>
                    {creating ? "Creando..." : "Crear Categoria"}
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

export default Categories;


