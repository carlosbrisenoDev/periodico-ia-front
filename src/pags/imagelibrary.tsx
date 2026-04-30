import {type ChangeEvent, useEffect, useMemo, useRef, useState} from "react";
import {useNavigate} from "react-router-dom";
import {Sidebar} from "../components/sidebar.tsx";
import {API_BASE_URL} from "../libs/config.ts";
import {ApiError, apiFetch} from "../libs/http.ts";

type ImageViewMode = "grid" | "list";

type ImageAsset = {
    id: string; filename: string; url: string; mimeType: string; size: number; createdAt: string;
};

type UploadImageResponse = {
    id?: string; filename?: string; url?: string;
};

type DeleteImageResponse = {
    message?: string; id?: string; file?: {
        status?: string; path?: string;
    };
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

const formatFileSize = (sizeInBytes: number): string => {
    if (!Number.isFinite(sizeInBytes) || sizeInBytes < 0) {
        return "0 B";
    }

    if (sizeInBytes < 1024) {
        return `${sizeInBytes} B`;
    }

    const sizeInKb = sizeInBytes / 1024;
    if (sizeInKb < 1024) {
        return `${sizeInKb.toFixed(1)} KB`;
    }

    const sizeInMb = sizeInKb / 1024;
    return `${sizeInMb.toFixed(1)} MB`;
};

const formatDate = (isoValue: string): string => {
    const parsed = new Date(isoValue);
    if (Number.isNaN(parsed.getTime())) {
        return "Fecha desconocida";
    }

    return new Intl.DateTimeFormat("es-ES", {
        day: "2-digit", month: "short", year: "numeric",
    })
        .format(parsed)
        .replace(".", "");
};

const normalizeImageList = (payload: unknown[]): ImageAsset[] => {
    return (Array.isArray(payload) ? payload : [])
        .map((item): ImageAsset | null => {
            if (!item || typeof item !== "object") {
                return null;
            }

            const record = item as Record<string, unknown>;
            if (typeof record.id !== "string" || typeof record.filename !== "string" || typeof record.url !== "string") {
                return null;
            }

            return {
                id: record.id,
                filename: record.filename,
                url: normalizeImageUrl(record.url),
                mimeType: typeof record.mimeType === "string" ? record.mimeType : "image/jpeg",
                size: typeof record.size === "number" ? record.size : 0,
                createdAt: typeof record.createdAt === "string" ? record.createdAt : new Date().toISOString(),
            };
        })
        .filter((item): item is ImageAsset => item !== null);
};

const ImageLibrary = () => {
    const navigate = useNavigate();
    const fileInputRef = useRef<HTMLInputElement | null>(null);

    const [images, setImages] = useState<ImageAsset[]>([]);
    const [loading, setLoading] = useState<boolean>(true);
    const [uploading, setUploading] = useState<boolean>(false);
    const [deletingId, setDeletingId] = useState<string | null>(null);
    const [viewMode, setViewMode] = useState<ImageViewMode>("grid");
    const [message, setMessage] = useState<string>("");
    const [error, setError] = useState<string | null>(null);

    const hasImages = images.length > 0;

    const sortedImages = useMemo(() => {

        console.log("Sorting images:", images);
        return [...images].sort((left, right) => {
            return new Date(right.createdAt).getTime() - new Date(left.createdAt).getTime();
        });
    }, [images]);

    const loadImages = async (controller?: AbortController) => {
        try {
            setLoading(true);
            setError(null);

            const payload = await apiFetch<unknown[]>(`${API_BASE_URL}/api/v1/image?limit=100`, {
                method: "GET", credentials: "include", signal: controller?.signal,
            });

            setImages(normalizeImageList(payload));
            console.log("Loaded images:", payload);
        } catch (err: unknown) {
            if (err instanceof Error && err.name === "AbortError") {
                return;
            }

            if (err instanceof ApiError && (err.status === 401 || err.status === 403)) {
                navigate("/adminlogin", {replace: true});
                return;
            }

            setError(err instanceof Error ? err.message : "No se pudieron cargar las imágenes.");
            setImages([]);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        const controller = new AbortController();
        void loadImages(controller);

        return () => controller.abort();
    }, []);

    const handleFileUpload = async (event: ChangeEvent<HTMLInputElement>) => {
        const selectedFile = event.target.files?.[0];
        event.target.value = "";

        if (!selectedFile) {
            return;
        }

        setUploading(true);
        setMessage("");
        setError(null);

        try {
            const formData = new FormData();
            formData.append("image", selectedFile);

            const uploaded = await apiFetch<UploadImageResponse>(`${API_BASE_URL}/api/v1/image/upload`, {
                method: "POST", credentials: "include", body: formData,
            });

            if (typeof uploaded.url !== "string") {
                setError("No se pudo interpretar la imagen cargada.");
                return;
            }

            setMessage("Imagen subida correctamente.");
            await loadImages();
        } catch (err: unknown) {
            if (err instanceof ApiError && (err.status === 401 || err.status === 403)) {
                navigate("/adminlogin", {replace: true});
                return;
            }

            setError(err instanceof Error ? err.message : "No se pudo subir la imagen.");
        } finally {
            setUploading(false);
        }
    };

    const handleDelete = async (image: ImageAsset) => {
        const canDelete = window.confirm(`Eliminar ${image.filename} de la biblioteca?`);
        if (!canDelete) {
            return;
        }

        setDeletingId(image.id);
        setMessage("");
        setError(null);

        try {
            const response = await apiFetch<DeleteImageResponse>(`${API_BASE_URL}/api/v1/image/${image.id}`, {
                method: "DELETE", credentials: "include",
            });

            setImages((previous) => previous.filter((candidate) => candidate.id !== image.id));
            setMessage(response.message ?? "Imagen eliminada.");
        } catch (err: unknown) {
            if (err instanceof ApiError && (err.status === 401 || err.status === 403)) {
                navigate("/adminlogin", {replace: true});
                return;
            }

            setError(err instanceof Error ? err.message : "No se pudo eliminar la imagen.");
        } finally {
            setDeletingId(null);
        }
    };

    return (<div className="layout dashboard-layout">
            <aside className="sidebar">
                <Sidebar/>
            </aside>

            <main className="content image-library-content">
                <header className="image-library-header">
                    <div>
                        <h1 className="image-library-title">Biblioteca de Imágenes</h1>
                        <p className="image-library-subtitle">Gestiona todas las imágenes de tus publicaciones</p>
                    </div>

                    <button
                        type="button"
                        className="image-library-upload-button"
                        onClick={() => fileInputRef.current?.click()}
                        disabled={uploading}
                    >
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 16V4"/>
                            <path d="m7 9 5-5 5 5"/>
                            <path d="M5 14v4a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-4"/>
                        </svg>
                        {uploading ? "Subiendo..." : "Subir Imagen"}
                    </button>
                </header>

                <input
                    ref={fileInputRef}
                    type="file"
                    accept="image/jpeg,image/png"
                    className="image-library-file-input"
                    onChange={handleFileUpload}
                />

                {error ? <p className="image-library-info error">{error}</p> : null}
                {!error && message ? <p className="image-library-info success">{message}</p> : null}
                {loading ? <p className="image-library-info">Cargando imágenes...</p> : null}

                <div className="image-library-view-toggle" role="group" aria-label="Cambiar vista">
                    <button
                        type="button"
                        className={`image-library-view-button${viewMode === "grid" ? " active" : ""}`}
                        onClick={() => setViewMode("grid")}
                    >
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <rect x="3" y="3" width="7" height="7" rx="1"/>
                            <rect x="14" y="3" width="7" height="7" rx="1"/>
                            <rect x="3" y="14" width="7" height="7" rx="1"/>
                            <rect x="14" y="14" width="7" height="7" rx="1"/>
                        </svg>
                    </button>

                    <button
                        type="button"
                        className={`image-library-view-button${viewMode === "list" ? " active" : ""}`}
                        onClick={() => setViewMode("list")}
                    >
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M8 6h13"/>
                            <path d="M8 12h13"/>
                            <path d="M8 18h13"/>
                            <circle cx="4" cy="6" r="1"/>
                            <circle cx="4" cy="12" r="1"/>
                            <circle cx="4" cy="18" r="1"/>
                        </svg>
                    </button>
                </div>

                {!loading && !hasImages ? (
                    <p className="image-library-info">Aún no hay imágenes en la biblioteca.</p>) : null}

                {!loading && hasImages && viewMode === "grid" ? (<div className="image-library-grid">
                        {sortedImages.map((image) => (<article key={image.id} className="image-library-card">
                                <img src={image.url} alt={image.filename} className="image-library-card-image"/>

                                <div className="image-library-card-body">
                                    <p className="image-library-card-name">{image.filename}</p>
                                    <p className="image-library-card-meta">
                                        {formatFileSize(image.size)} · {formatDate(image.createdAt)}
                                    </p>

                                    <button
                                        type="button"
                                        className="image-library-delete-button"
                                        onClick={() => {
                                            void handleDelete(image);
                                        }}
                                        disabled={deletingId === image.id}
                                    >
                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M3 6h18"/>
                                            <path d="M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2"/>
                                            <path d="M6 6v14a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V6"/>
                                            <path d="M10 10v7"/>
                                            <path d="M14 10v7"/>
                                        </svg>
                                        {deletingId === image.id ? "Eliminando..." : "Eliminar"}
                                    </button>
                                </div>
                            </article>))}
                    </div>) : null}

                {!loading && hasImages && viewMode === "list" ? (<div className="image-library-table-wrap">
                        <table className="image-library-table">
                            <thead>
                            <tr>
                                <th>Vista Previa</th>
                                <th>Nombre</th>
                                <th>Tamano</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            {sortedImages.map((image) => (<tr key={image.id}>
                                    <td>
                                        <img src={image.url} alt={image.filename}
                                             className="image-library-table-thumb"/>
                                    </td>
                                    <td>{image.filename}</td>
                                    <td>{formatFileSize(image.size)}</td>
                                    <td>{formatDate(image.createdAt)}</td>
                                    <td>
                                        <button
                                            type="button"
                                            className="image-library-delete-icon"
                                            onClick={() => {
                                                void handleDelete(image);
                                            }}
                                            disabled={deletingId === image.id}
                                            aria-label={`Eliminar ${image.filename}`}
                                        >
                                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="M3 6h18"/>
                                                <path d="M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2"/>
                                                <path d="M6 6v14a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V6"/>
                                                <path d="M10 10v7"/>
                                                <path d="M14 10v7"/>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>))}
                            </tbody>
                        </table>
                    </div>) : null}
            </main>
        </div>);
};

export default ImageLibrary;

