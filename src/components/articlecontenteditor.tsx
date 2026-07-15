import { type ChangeEvent, useCallback, useEffect, useRef, useState } from "react";
import { API_BASE_URL, MAX_UPLOAD_MB } from "../libs/config.ts";
import { serializeContentBlocks, parseContentBlocks, type ContentBlock } from "../libs/contentBlocks.ts";
import { ApiError, apiFetch } from "../libs/http.ts";

type EditableBlock =
  | { id: string; type: "paragraph"; text: string; align?: "left" | "right" | "justify" | "center" }
  | { id: string; type: "subtitle"; text: string }
  | { id: string; type: "image"; url: string; caption?: string }
  | { id: string; type: "video"; url: string }
  | { id: string; type: "image-row"; urls: string[]; layout?: "equal" | "left-large" | "right-large" };

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
    return { id: createBlockId(), type: "image", url: block.url, caption: block.caption };
  }

  if (block.type === "video") {
    return { id: createBlockId(), type: "video", url: block.url };
  }

  if (block.type === "image-row") {
    return { id: createBlockId(), type: "image-row", urls: block.urls, layout: block.layout };
  }

  return { id: createBlockId(), type: "paragraph", text: block.text, align: block.align || "justify" };
};

const createEmptyBlock = (type: EditableBlock["type"]): EditableBlock => {
  if (type === "subtitle") {
    return { id: createBlockId(), type, text: "" };
  }

  if (type === "image") {
    return { id: createBlockId(), type, url: "", caption: "" };
  }

  if (type === "video") {
    return { id: createBlockId(), type, url: "" };
  }

  if (type === "image-row") {
    return { id: createBlockId(), type, urls: ["", "", ""], layout: "equal" };
  }

  return { id: createBlockId(), type, text: "", align: "justify" };
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
      return { type: "image", url: block.url, caption: block.caption };
    }
    if (block.type === "video") {
      return { type: "video", url: block.url };
    }
    if (block.type === "image-row") {
      return { type: "image-row", urls: block.urls, layout: block.layout };
    }

    return block.type === "subtitle"
      ? { type: "subtitle", text: block.text }
      : { type: "paragraph", text: block.text, align: block.align || "justify" };
  });

export const ArticleContentEditor = ({
  value,
  onChange,
  disabled = false,
  onUnauthorized,
}: ArticleContentEditorProps) => {
  const [blocks, setBlocks] = useState<EditableBlock[]>(() => initializeBlocks(value));
  const [activeImageBlockId, setActiveImageBlockId] = useState<string | null>(null);
  const [activeImageRowIndex, setActiveImageRowIndex] = useState<number | null>(null);
  const [showImageModal, setShowImageModal] = useState<boolean>(false);
  const [showLibraryModal, setShowLibraryModal] = useState<boolean>(false);
  const [images, setImages] = useState<ImageAsset[]>([]);
  const [loadingImages, setLoadingImages] = useState<boolean>(false);
  const [uploadingImage, setUploadingImage] = useState<boolean>(false);
  const [imageError, setImageError] = useState<string | null>(null);
  const fileInputRef = useRef<HTMLInputElement | null>(null);
  const textareaRefs = useRef<Map<string, HTMLTextAreaElement>>(new Map());
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

    if (type === "image" || type === "image-row") {
      setActiveImageBlockId(nextBlock.id);
      setActiveImageRowIndex(type === "image-row" ? 0 : null);
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
      setActiveImageRowIndex(null);
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

  const openImageMenu = (blockId: string, rowIndex: number | null = null) => {
    if (disabled) {
      return;
    }

    setActiveImageBlockId(blockId);
    setActiveImageRowIndex(rowIndex);
    setShowLibraryModal(false);
    setShowImageModal(true);
    setImageError(null);
  };

  const closeImageMenus = () => {
    setShowImageModal(false);
    setShowLibraryModal(false);
    setActiveImageBlockId(null);
    setActiveImageRowIndex(null);
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

    if (current.type === "image-row" && activeImageRowIndex !== null) {
      updateBlock(current.id, (b) => {
        if (b.type !== "image-row") return b;
        const newUrls = [...b.urls];
        newUrls[activeImageRowIndex] = url;
        return { ...b, urls: newUrls };
      });
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
  const iconVideo = <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="m22 8-6 4 6 4V8Z"/><rect width="14" height="12" x="2" y="6" rx="2" ry="2"/></svg>;
  const iconGrid = <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>;
  
  const iconAlignLeft = <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><line x1="21" x2="3" y1="6" y2="6"/><line x1="15" x2="3" y1="12" y2="12"/><line x1="17" x2="3" y1="18" y2="18"/></svg>;
  const iconAlignCenter = <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><line x1="21" x2="3" y1="6" y2="6"/><line x1="19" x2="5" y1="12" y2="12"/><line x1="21" x2="3" y1="18" y2="18"/></svg>;
  const iconAlignRight = <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><line x1="21" x2="3" y1="6" y2="6"/><line x1="21" x2="9" y1="12" y2="12"/><line x1="21" x2="7" y1="18" y2="18"/></svg>;
  const iconAlignJustify = <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><line x1="21" x2="3" y1="6" y2="6"/><line x1="21" x2="3" y1="12" y2="12"/><line x1="21" x2="3" y1="18" y2="18"/></svg>;

  /* ── Inline formatting helper ── */
  const wrapSelection = (blockId: string, prefix: string, suffix: string) => {
    const ta = textareaRefs.current.get(blockId);
    if (!ta || disabled) return;
    const start = ta.selectionStart;
    const end = ta.selectionEnd;
    const text = ta.value;
    const selected = text.slice(start, end);
    const newText = text.slice(0, start) + prefix + selected + suffix + text.slice(end);
    updateBlock(blockId, (current) => ({ ...current, text: newText }));
    // Restore cursor after React re-render
    requestAnimationFrame(() => {
      ta.focus();
      const newCursorPos = selected.length > 0 ? start + prefix.length + selected.length + suffix.length : start + prefix.length;
      ta.setSelectionRange(newCursorPos, newCursorPos);
    });
  };

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
      <button type="button" className="editor-action-chip" onClick={() => addBlock("video", blockId)} disabled={disabled}>
        {iconVideo} Video
      </button>
      <button type="button" className="editor-action-chip" onClick={() => addBlock("image-row", blockId)} disabled={disabled}>
        {iconGrid} Fila Imágenes
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
                    <span className="editor-image-label">Bloque de Imágenes</span>
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
                    <div className="editor-image-preview-wrapper" style={{ display: "flex", flexDirection: "column" }}>
                      <img
                        className="editor-image-preview"
                        src={normalizeImageUrl(block.url)}
                        alt="Imagen del contenido"
                      />
                      <input
                        className="editor-input"
                        style={{ marginTop: "12px", border: "none", borderBottom: "1px dashed var(--border)", borderRadius: 0, padding: "8px 4px", fontSize: "0.875rem" }}
                        type="text"
                        placeholder="Escribe un pie de foto (opcional)..."
                        value={block.caption || ""}
                        onChange={(event) =>
                          updateBlock(block.id, (current) => ({ ...current, caption: event.target.value }))
                        }
                        disabled={disabled}
                      />
                    </div>
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

          if (block.type === "image-row") {
            const toggleLayout = () => {
              updateBlock(block.id, (b) => {
                if (b.type !== "image-row") return b;
                const nextLayout = b.layout === 'equal' ? 'left-large' : b.layout === 'left-large' ? 'right-large' : 'equal';
                return { ...b, layout: nextLayout };
              });
            };

            return (
              <div key={block.id} className="editor-block-wrapper">
                {sideControls}
                <div className="editor-block-container">
                  <div className="editor-image-head">
                    <span className="editor-image-label">Fila de Imágenes (Grid)</span>
                    <button type="button" className="editor-trash-btn" onClick={() => removeBlock(block.id)} disabled={disabled}>
                      {iconTrash}
                    </button>
                  </div>
                  
                  <div style={{ display: "flex", gap: "10px", margin: "10px 0" }}>
                    <select
                      value={block.urls.length}
                      onChange={(e) => {
                        const newLength = parseInt(e.target.value, 10);
                        updateBlock(block.id, (b) => {
                          if (b.type !== "image-row") return b;
                          const newUrls = [...b.urls];
                          if (newLength === 2 && newUrls.length > 2) {
                            newUrls.length = 2;
                          } else if (newLength === 3 && newUrls.length < 3) {
                            newUrls.push("");
                          }
                          return { ...b, urls: newUrls, layout: newLength === 2 ? 'equal' : b.layout };
                        });
                      }}
                      style={{ padding: "4px 8px", background: "#f3f4f6", border: "1px solid #d1d5db", borderRadius: "4px" }}
                      disabled={disabled}
                    >
                      <option value={2}>2 Imágenes</option>
                      <option value={3}>3 Imágenes</option>
                    </select>
                    {block.urls.length === 3 && (
                      <button type="button" onClick={toggleLayout} style={{ padding: "4px 8px", background: "#f3f4f6", border: "1px solid #d1d5db", borderRadius: "4px" }}>
                        Distribución: {block.layout === 'equal' ? 'Iguales' : block.layout === 'left-large' ? 'Izquierda Grande' : 'Derecha Grande'}
                      </button>
                    )}
                  </div>

                  <div style={{ display: "grid", gridTemplateColumns: block.urls.length === 2 ? "1fr 1fr" : "1fr 1fr 1fr", gap: "10px" }}>
                    {block.urls.map((url, i) => (
                      <div key={i} className={`editor-block-container ${!url ? "image-empty" : ""}`} style={{ marginBottom: 0 }}>
                        {!url ? (
                          <div className="editor-image-placeholder" style={{ minHeight: "100px" }}>
                            {iconImgBig}
                          </div>
                        ) : (
                          <div className="editor-image-preview-wrapper" style={{ height: "100px" }}>
                            <img className="editor-image-preview" src={normalizeImageUrl(url)} alt={`Col ${i}`} />
                          </div>
                        )}
                        <button type="button" className="editor-primary-action" onClick={() => openImageMenu(block.id, i)} disabled={disabled} style={{ marginTop: "10px", width: "100%", justifyContent: "center" }}>
                          {iconPlus} {url ? "Cambiar" : "Agregar"}
                        </button>
                      </div>
                    ))}
                  </div>

                  <div className="editor-block-footer">
                    <div className="editor-footer-left">
                      {renderActionChips(block.id)}
                    </div>
                  </div>
                </div>
              </div>
            );
          }

          if (block.type === "video") {
            return (
              <div key={block.id} className="editor-block-wrapper">
                {sideControls}
                <div className="editor-block-container">
                  <div className="editor-image-head">
                    <span className="editor-image-label">Video Embebido (YouTube/Twitter)</span>
                    <button type="button" className="editor-trash-btn" onClick={() => removeBlock(block.id)} disabled={disabled}>
                      {iconTrash}
                    </button>
                  </div>
                  <input
                    className="editor-input"
                    type="text"
                    placeholder="Pega la URL de YouTube o X (Twitter)..."
                    value={block.url}
                    onChange={(event) =>
                      updateBlock(block.id, (current) => ({ ...current, url: event.target.value }))
                    }
                    disabled={disabled}
                    style={{ marginTop: "10px" }}
                  />
                  
                  <div className="editor-block-footer">
                    <div className="editor-footer-left">
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
                  <>
                    <div className="editor-format-toolbar">
                      <div className="editor-format-group">
                        <button type="button" className="editor-format-btn" title="Negrita" onClick={() => wrapSelection(block.id, '**', '**')} disabled={disabled}>
                          <strong>B</strong>
                        </button>
                        <button type="button" className="editor-format-btn" title="Itálica" onClick={() => wrapSelection(block.id, '*', '*')} disabled={disabled}>
                          <em>I</em>
                        </button>
                        <button type="button" className="editor-format-btn" title="Subrayado" onClick={() => wrapSelection(block.id, '__', '__')} disabled={disabled}>
                          <u>U</u>
                        </button>
                      </div>
                      <div className="editor-format-group" style={{ marginLeft: "auto", display: "flex", gap: "2px" }}>
                        <button type="button" className={`editor-format-btn ${block.align === "left" ? "active" : ""}`} title="Alinear Izquierda" onClick={() => updateBlock(block.id, (c) => ({ ...c, align: "left" }))} disabled={disabled}>
                          {iconAlignLeft}
                        </button>
                        <button type="button" className={`editor-format-btn ${block.align === "center" ? "active" : ""}`} title="Centrar" onClick={() => updateBlock(block.id, (c) => ({ ...c, align: "center" }))} disabled={disabled}>
                          {iconAlignCenter}
                        </button>
                        <button type="button" className={`editor-format-btn ${block.align === "justify" || !block.align ? "active" : ""}`} title="Justificar" onClick={() => updateBlock(block.id, (c) => ({ ...c, align: "justify" }))} disabled={disabled}>
                          {iconAlignJustify}
                        </button>
                        <button type="button" className={`editor-format-btn ${block.align === "right" ? "active" : ""}`} title="Alinear Derecha" onClick={() => updateBlock(block.id, (c) => ({ ...c, align: "right" }))} disabled={disabled}>
                          {iconAlignRight}
                        </button>
                      </div>
                    </div>
                    <textarea
                      className="editor-textarea"
                      rows={4}
                      placeholder="Escribe un párrafo..."
                      value={block.text}
                      ref={(el) => {
                        if (el) textareaRefs.current.set(block.id, el);
                        else textareaRefs.current.delete(block.id);
                      }}
                      onChange={(event) =>
                        updateBlock(block.id, (current) => ({ ...current, text: event.target.value }))
                      }
                      disabled={disabled}
                    />
                  </>
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
              <span className="new-publication-upload-subtitle">PNG o JPG</span>
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
              <span className="new-publication-upload-subtitle">Elige de tus imágenes guardadas</span>
            </button>
          </div>
        </div>
      ) : null}

      {showLibraryModal ? (
        <div className="new-publication-modal-overlay" role="dialog" aria-modal="true">
          <div className="new-publication-modal new-publication-library-modal">
            <div className="new-publication-modal-head">
              <h2>Biblioteca de Imágenes</h2>
              <button type="button" className="new-publication-modal-close" onClick={closeImageMenus}>
                x
              </button>
            </div>

            {loadingImages ? <p className="new-publication-message">Cargando imágenes...</p> : null}
            {!loadingImages && imageError ? (
              <p className="new-publication-message error">{imageError}</p>
            ) : null}

            {!loadingImages && !imageError && images.length === 0 ? (
              <p className="new-publication-message">Aún no hay imágenes guardadas.</p>
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
