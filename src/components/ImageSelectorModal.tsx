import { useState, useRef, type ChangeEvent } from "react";
import { apiFetch } from "../libs/http.ts";
import { API_BASE_URL } from "../libs/config.ts";

type ImageAsset = {
  id: string;
  filename: string;
  url: string;
};

type UploadImageResponse = {
  message?: string;
  url?: string;
};

const normalizeImageUrl = (value: string): string => {
  if (value.startsWith("http")) return value;
  let cleanUrl = value;
  if (cleanUrl.startsWith("/api/v1/")) {
    cleanUrl = cleanUrl.replace("/api/v1/", "");
  }
  if (cleanUrl.startsWith("/")) {
    cleanUrl = cleanUrl.substring(1);
  }
  return `${API_BASE_URL}/${cleanUrl}`;
};

const MAX_UPLOAD_MB = 5;

type ImageSelectorModalProps = {
  onSelect: (url: string) => void;
  onClose: () => void;
};

export default function ImageSelectorModal({ onSelect, onClose }: ImageSelectorModalProps) {
  const [showLibraryModal, setShowLibraryModal] = useState<boolean>(false);
  const [images, setImages] = useState<ImageAsset[]>([]);
  const [loadingImages, setLoadingImages] = useState<boolean>(false);
  const [uploadingImage, setUploadingImage] = useState<boolean>(false);
  const [imageError, setImageError] = useState<string | null>(null);
  
  const fileInputRef = useRef<HTMLInputElement | null>(null);

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
          if (!item || typeof item !== "object") return null;
          const record = item as Record<string, unknown>;
          if (typeof record.id !== "string" || typeof record.filename !== "string" || typeof record.url !== "string") {
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
      setImageError(err instanceof Error ? err.message : "No se pudo cargar la biblioteca.");
    } finally {
      setLoadingImages(false);
    }
  };

  const openLibrary = async () => {
    setShowLibraryModal(true);
    await loadImageLibrary();
  };

  const uploadFeaturedImage = async (event: ChangeEvent<HTMLInputElement>) => {
    const selectedFile = event.target.files?.[0];
    event.target.value = "";
    if (!selectedFile) return;

    if (selectedFile.size > MAX_UPLOAD_MB * 1024 * 1024) {
      setImageError(`La imagen no debe superar ${MAX_UPLOAD_MB}MB.`);
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
        setImageError("No se recibio la URL de la imagen subida.");
        return;
      }
      onSelect(uploadedUrl);
    } catch (err: unknown) {
      setImageError(err instanceof Error ? err.message : "No se pudo subir la imagen.");
    } finally {
      setUploadingImage(false);
    }
  };

  if (showLibraryModal) {
    return (
      <div className="new-publication-modal-overlay" role="dialog" aria-modal="true">
        <div className="new-publication-modal new-publication-library-modal">
          <div className="new-publication-modal-head">
            <h2>Biblioteca de Imágenes</h2>
            <button type="button" className="new-publication-modal-close" onClick={() => setShowLibraryModal(false)}>x</button>
          </div>
          {imageError ? <p className="new-publication-message error">{imageError}</p> : null}
          <div className="new-publication-library-grid">
            {loadingImages ? (
              <p>Cargando imágenes...</p>
            ) : images.length > 0 ? (
              images.map((img) => (
                <div key={img.id} className="new-publication-library-item" onClick={() => onSelect(img.url)}>
                  <img src={img.url} alt={img.filename} />
                </div>
              ))
            ) : (
              <p>No hay imágenes en la biblioteca.</p>
            )}
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="new-publication-modal-overlay" role="dialog" aria-modal="true">
      <div className="new-publication-modal">
        <div className="new-publication-modal-head">
          <h2>Subir Imagen</h2>
          <button type="button" className="new-publication-modal-close" onClick={onClose}>x</button>
        </div>
        {imageError ? <p className="new-publication-message error">{imageError}</p> : null}
        
        <input ref={fileInputRef} type="file" accept="image/jpeg,image/png,image/webp" style={{ display: 'none' }} onChange={uploadFeaturedImage} />
        
        <button type="button" className="new-publication-modal-option" onClick={() => fileInputRef.current?.click()} disabled={uploadingImage}>
          <span className="new-publication-upload-title">Haz clic para subir una imagen</span>
          <span className="new-publication-upload-subtitle">PNG o JPG</span>
          <span className="new-publication-modal-option-action">{uploadingImage ? "Subiendo..." : "Seleccionar Archivo"}</span>
        </button>
        <span className="new-publication-upload-separator">o</span>
        <button type="button" className="new-publication-modal-option" onClick={openLibrary} disabled={uploadingImage}>
          <span className="new-publication-upload-title">Seleccionar de la biblioteca</span>
          <span className="new-publication-upload-subtitle">Elige de tus imágenes guardadas</span>
        </button>
      </div>
    </div>
  );
}
