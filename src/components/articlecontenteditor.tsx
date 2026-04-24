import { type ChangeEvent, useCallback, useEffect, useRef, useState } from "react";
import { API_BASE_URL } from "../libs/config.ts";
import { serializeContentBlocks, parseContentBlocks, type ContentBlock } from "../libs/contentBlocks.ts";
import { ApiError, apiFetch } from "../libs/http.ts";

type EditableBlock =
  | { id: string; type: "paragraph"; text: string }
  | { id: string; type: "subtitle"; text: string }
  | { id: string; type: "image"; url: string };

type ImageAsset = {
  id: string;
  filename: string;
  url: string;
};

type UploadImageResponse = {
  url?: string;
};

type ArticleContentEditorProps = {
  value: string;
  onChange: (value: string) => void;
  disabled?: boolean;
  onUnauthorized?: () => void;
};

const createBlockId = (): string =>
  globalThis.crypto?.randomUUID?.() ?? `block-${Date.now()}-${Math.random().toString(16).slice(2)}`;

const normalizeImageUrl = (value: string): string => {
  if (!value) {
    return "";
  }

  if (value.startsWith("http://") || value.startsWith("https://")) {
    return value;
  }

  return `${API_BASE_URL}${value.startsWith("/") ? value : `/${value}`}`;
};

const createEditableBlock = (block: ContentBlock): EditableBlock => {
  if (block.type === "subtitle") {
    return { id: createBlockId(), type: "subtitle", text: block.text };
  }

  if (block.type === "image") {
    return { id: createBlockId(), type: "image", url: block.url };
  }

  return { id: createBlockId(), type: "paragraph", text: block.text };
};

const createEmptyBlock = (type: EditableBlock["type"]): EditableBlock => {
  if (type === "subtitle") {
    return { id: createBlockId(), type, text: "" };
  }

  if (type === "image") {
    return { id: createBlockId(), type, url: "" };
  }

  return { id: createBlockId(), type, text: "" };
};

const initializeBlocks = (value: string): EditableBlock[] => {
  const parsedBlocks = parseContentBlocks(value);

  if (parsedBlocks.length === 0) {
    return [createEmptyBlock("paragraph")];
  }

  return parsedBlocks.map(createEditableBlock);
};

const stripIds = (blocks: EditableBlock[]): ContentBlock[] =>
  blocks.map((block) => {
    if (block.type === "image") {
      return { type: "image", url: block.url };
    }

    return block.type === "subtitle"
      ? { type: "subtitle", text: block.text }
      : { type: "paragraph", text: block.text };
  });

export const ArticleContentEditor = ({
  value,
  onChange,
  disabled = false,
  onUnauthorized,
}: ArticleContentEditorProps) => {
  const [blocks, setBlocks] = useState<EditableBlock[]>(() => initializeBlocks(value));
  const [activeImageBlockId, setActiveImageBlockId] = useState<string | null>(null);
  const [showImageModal, setShowImageModal] = useState<boolean>(false);
  const [showLibraryModal, setShowLibraryModal] = useState<boolean>(false);
  const [images, setImages] = useState<ImageAsset[]>([]);
  const [loadingImages, setLoadingImages] = useState<boolean>(false);
  const [uploadingImage, setUploadingImage] = useState<boolean>(false);
  const [imageError, setImageError] = useState<string | null>(null);
  const fileInputRef = useRef<HTMLInputElement | null>(null);
  const lastValueRef = useRef(value);

  useEffect(() => {
    if (value === lastValueRef.current) {
      return;
    }

    setBlocks(initializeBlocks(value));
    lastValueRef.current = value;
  }, [value]);

  const commitBlocks = (nextBlocks: EditableBlock[]) => {
    const normalizedBlocks = nextBlocks.length > 0 ? nextBlocks : [createEmptyBlock("paragraph")];
    const content = serializeContentBlocks(stripIds(normalizedBlocks));

    lastValueRef.current = content;
    setBlocks(normalizedBlocks);
    onChange(content);
  };

  const updateBlock = (blockId: string, updater: (block: EditableBlock) => EditableBlock) => {
    if (disabled) {
      return;
    }

    commitBlocks(blocks.map((block) => (block.id === blockId ? updater(block) : block)));
  };

  const addBlock = (type: EditableBlock["type"], afterId?: string) => {
    if (disabled) {
      return;
    }

    const nextBlock = createEmptyBlock(type);
    let nextBlocks = [...blocks, nextBlock];

    if (afterId) {
      const index = blocks.findIndex((b) => b.id === afterId);
      if (index !== -1) {
        nextBlocks = [
          ...blocks.slice(0, index + 1),
          nextBlock,
          ...blocks.slice(index + 1),
        ];
      }
    }

    commitBlocks(nextBlocks);

    if (type === "image") {
      setActiveImageBlockId(nextBlock.id);
      setShowImageModal(true);
      setShowLibraryModal(false);
      setImageError(null);
    }
  };

  const removeBlock = (blockId: string) => {
    if (disabled) {
      return;
    }

    const nextBlocks = blocks.filter((block) => block.id !== blockId);
    if (activeImageBlockId === blockId) {
      setActiveImageBlockId(null);
      setShowImageModal(false);
      setShowLibraryModal(false);
    }

    commitBlocks(nextBlocks);
  };

  const moveBlockUp = (index: number) => {
    if (disabled || index === 0) return;
    const nextBlocks = [...blocks];
    [nextBlocks[index - 1], nextBlocks[index]] = [nextBlocks[index], nextBlocks[index - 1]];
    commitBlocks(nextBlocks);
  };

  const moveBlockDown = (index: number) => {
    if (disabled || index === blocks.length - 1) return;
    const nextBlocks = [...blocks];
    [nextBlocks[index], nextBlocks[index + 1]] = [nextBlocks[index + 1], nextBlocks[index]];
    commitBlocks(nextBlocks);
  };

  const openImageMenu = (blockId: string) => {
    if (disabled) {
      return;
    }

    setActiveImageBlockId(blockId);
    setShowLibraryModal(false);
    setShowImageModal(true);
    setImageError(null);
  };

  const closeImageMenus = () => {
    setShowImageModal(false);
    setShowLibraryModal(false);
    setActiveImageBlockId(null);
    setImageError(null);
  };

  const loadImageLibrary = useCallback(async () => {
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
        onUnauthorized?.();
        return;
      }

      setImageError(err instanceof Error ? err.message : "No se pudo cargar la biblioteca.");
    } finally {
      setLoadingImages(false);
    }
  }, [onUnauthorized]);

  useEffect(() => {
    if (!showLibraryModal) {
      return;
    }

    void loadImageLibrary();
  }, [showLibraryModal, loadImageLibrary]);

  const getActiveImageBlock = (): EditableBlock | null => {
    if (!activeImageBlockId) {
      return null;
    }

    const block = blocks.find((candidate) => candidate.id === activeImageBlockId);
    return block && block.type === "image" ? block : null;
  };

  const updateActiveImageBlock = (url: string) => {
    const current = getActiveImageBlock();
    if (!current) {
      setImageError("Selecciona un bloque de imagen primero.");
      return;
    }

    updateBlock(current.id, () => ({
      ...current,
      url,
    }));
  };

  const uploadImage = async (event: ChangeEvent<HTMLInputElement>) => {
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
        setImageError("No se recibio la URL de la imagen subida.");
        return;
      }

      updateActiveImageBlock(uploadedUrl);
      closeImageMenus();
    } catch (err: unknown) {
      if (err instanceof ApiError && (err.status === 401 || err.status === 403)) {
        onUnauthorized?.();
        return;
      }

      setImageError(err instanceof Error ? err.message : "No se pudo subir la imagen.");
    } finally {
      setUploadingImage(false);
    }
  };

  const selectLibraryImage = (url: string) => {
    updateActiveImageBlock(normalizeImageUrl(url));
    closeImageMenus();
  };

  const iconT = <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M4 7V4h16v3"/><path d="M12 4v16"/><path d="M8 20h8"/></svg>;
  const iconImg = <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>;
  const iconImgBig = <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>;
  const iconH = <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M4 12h16M4 18V6m16 12V6"/></svg>;
  const iconTrash = <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M3 6h18M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>;
  const iconPlus = <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M5 12h14M12 5v14"/></svg>;
  const iconUp = <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round"><path d="m18 15-6-6-6 6"/></svg>;
  const iconDown = <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round"><path d="m6 9 6 6 6-6"/></svg>;

  const renderActionChips = (blockId: string) => (
    <div className="editor-chips-group">
      <button type="button" className="editor-action-chip" onClick={() => addBlock("paragraph", blockId)} disabled={disabled}>
        {iconT} Texto
      </button>
      <button type="button" className="editor-action-chip" onClick={() => addBlock("image", blockId)} disabled={disabled}>
        {iconImg} Imágenes
      </button>
      <button type="button" className="editor-action-chip" onClick={() => addBlock("subtitle", blockId)} disabled={disabled}>
        {iconH} Subtítulo
      </button>
    </div>
  );

  return (
    <div className="article-content-editor">
      <div className="article-content-editor-blocks">
        {blocks.map((block, index) => {
          const sideControls = (
            <div className="editor-side-controls">
              <button type="button" className="editor-side-btn" onClick={() => moveBlockUp(index)} disabled={disabled || index === 0}>
                {iconUp}
              </button>
              <button type="button" className="editor-side-btn" onClick={() => moveBlockDown(index)} disabled={disabled || index === blocks.length - 1}>
                {iconDown}
              </button>
            </div>
          );

          if (block.type === "image") {
            return (
              <div key={block.id} className="editor-block-wrapper">
                {sideControls}
                <div className={`editor-block-container ${!block.url ? "image-empty" : ""}`}>
                  <div className="editor-image-head">
                    <span className="editor-image-label">Bloque de Imágenes ({index + 1}/{blocks.length})</span>
                    <button type="button" className="editor-trash-btn" onClick={() => removeBlock(block.id)} disabled={disabled}>
                      {iconTrash}
                    </button>
                  </div>

                  {!block.url ? (
                    <div className="editor-image-placeholder">
                      {iconImgBig}
                      <span>Sin imágenes</span>
                    </div>
                  ) : (
                    <img
                      className="editor-image-preview"
                      src={normalizeImageUrl(block.url)}
                      alt="Imagen del contenido"
                    />
                  )}

                  <div className="editor-block-footer">
                    <div className="editor-footer-left">
                      <button type="button" className="editor-primary-action" onClick={() => openImageMenu(block.id)} disabled={disabled}>
                        {iconPlus} {block.url ? "Cambiar Imagen" : "Agregar Imagen"}
                      </button>
                      {renderActionChips(block.id)}
                    </div>
                  </div>
                </div>
              </div>
            );
          }

          return (
            <div key={block.id} className="editor-block-wrapper">
              {sideControls}
              <div className="editor-block-container">
                {block.type === "subtitle" ? (
                  <input
                    className="editor-input"
                    type="text"
                    placeholder="Escribe un subtítulo..."
                    value={block.text}
                    onChange={(event) =>
                      updateBlock(block.id, (current) => ({ ...current, text: event.target.value }))
                    }
                    disabled={disabled}
                  />
                ) : (
                  <textarea
                    className="editor-textarea"
                    rows={4}
                    placeholder="Escribe un párrafo..."
                    value={block.text}
                    onChange={(event) =>
                      updateBlock(block.id, (current) => ({ ...current, text: event.target.value }))
                    }
                    disabled={disabled}
                  />
                )}

                <div className="editor-block-footer">
                  <div className="editor-footer-left">
                    {renderActionChips(block.id)}
                  </div>
                  <button type="button" className="editor-trash-btn" onClick={() => removeBlock(block.id)} disabled={disabled}>
                    {iconTrash}
                  </button>
                </div>
              </div>
            </div>
          );
        })}
      </div>

      {showImageModal ? (
        <div className="new-publication-modal-overlay" role="dialog" aria-modal="true">
          <div className="new-publication-modal">
            <div className="new-publication-modal-head">
              <h2>Seleccionar imagen</h2>
              <button type="button" className="new-publication-modal-close" onClick={closeImageMenus}>
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
                setShowImageModal(false);
                setShowLibraryModal(true);
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
              <button type="button" className="new-publication-modal-close" onClick={closeImageMenus}>
                x
              </button>
            </div>

            {loadingImages ? <p className="new-publication-message">Cargando imagenes...</p> : null}
            {!loadingImages && imageError ? (
              <p className="new-publication-message error">{imageError}</p>
            ) : null}

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

      <input
        ref={fileInputRef}
        type="file"
        accept="image/jpeg,image/png,image/webp"
        className="new-publication-file-input"
        onChange={uploadImage}
      />
    </div>
  );
};

export default ArticleContentEditor;
