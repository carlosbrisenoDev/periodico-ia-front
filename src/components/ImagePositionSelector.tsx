import React, { useEffect, useState } from "react";
import "./ImagePositionSelector.css";

export interface ImagePositionSelectorProps {
  imageUrl?: string | null;
  value?: string | null;
  onChange: (position: string) => void;
  label?: string;
  className?: string;
  containerRatio?: number;
}

export const ImagePositionSelector: React.FC<ImagePositionSelectorProps> = ({
  imageUrl,
  value = "center",
  onChange,
  label = "Alineación y encuadre",
  className = "",
  containerRatio = 21 / 9,
}) => {
  const [orientation, setOrientation] = useState<"wide" | "tall">("wide");
  const currentVal = value || "center";

  useEffect(() => {
    if (!imageUrl) return;
    const img = new Image();
    img.onload = () => {
      const imageRatio = img.naturalWidth / (img.naturalHeight || 1);
      if (imageRatio > containerRatio) {
        setOrientation("wide");
      } else {
        setOrientation("tall");
      }
    };
    img.src = imageUrl;
  }, [imageUrl, containerRatio]);

  const renderIcon = (id: string, isSelected: boolean) => {
    const sliceFill = isSelected ? "#3b82f6" : "currentColor";
    const sliceOpacity = isSelected ? 1 : 0.75;

    return (
      <svg
        viewBox="0 0 24 24"
        className="image-position-svg"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
      >
        {/* Contenedor base de la imagen con bordes redondeados */}
        <rect
          x="2"
          y="2"
          width="20"
          height="20"
          rx="4"
          stroke="currentColor"
          strokeWidth="1.5"
          opacity={isSelected ? "0.9" : "0.35"}
        />

        {/* Sección resaltada según orientación y posición seleccionada */}
        {orientation === "tall" ? (
          <>
            {id === "center" && (
              <rect x="4" y="8.5" width="16" height="7" rx="1.5" fill={sliceFill} opacity={sliceOpacity} />
            )}
            {id === "top" && (
              <rect x="4" y="4" width="16" height="7" rx="1.5" fill={sliceFill} opacity={sliceOpacity} />
            )}
            {id === "bottom" && (
              <rect x="4" y="13" width="16" height="7" rx="1.5" fill={sliceFill} opacity={sliceOpacity} />
            )}
          </>
        ) : (
          <>
            {id === "center" && (
              <rect x="8.5" y="4" width="7" height="16" rx="1.5" fill={sliceFill} opacity={sliceOpacity} />
            )}
            {id === "left" && (
              <rect x="4" y="4" width="7" height="16" rx="1.5" fill={sliceFill} opacity={sliceOpacity} />
            )}
            {id === "right" && (
              <rect x="13" y="4" width="7" height="16" rx="1.5" fill={sliceFill} opacity={sliceOpacity} />
            )}
          </>
        )}
      </svg>
    );
  };

  const options =
    orientation === "tall"
      ? [
          { id: "center", label: "Centrado vertical (Por defecto)" },
          { id: "top", label: "Encuadre superior" },
          { id: "bottom", label: "Encuadre inferior" },
        ]
      : [
          { id: "center", label: "Centrado horizontal (Por defecto)" },
          { id: "left", label: "Encuadre izquierdo" },
          { id: "right", label: "Encuadre derecho" },
        ];

  return (
    <div className={`image-position-selector-container image-position-animated ${className}`}>
      <div className="image-position-header">
        <span>{label}</span>
        <span className="image-position-badge">
          {orientation === "tall" ? "Ajuste Vertical" : "Ajuste Horizontal"}
        </span>
      </div>
      <div className="image-position-toolbar" role="group" aria-label="Seleccionar encuadre de la imagen">
        {options.map((opt) => {
          const isSelected = currentVal === opt.id || (currentVal === "center" && opt.id === "center");
          return (
            <button
              key={opt.id}
              type="button"
              onClick={() => onChange(opt.id)}
              className={`image-position-btn ${isSelected ? "active" : ""}`}
              aria-pressed={isSelected}
              aria-label={opt.label}
            >
              <div className="image-position-icon-container">{renderIcon(opt.id, isSelected)}</div>
              <span className="image-position-tooltip">{opt.label}</span>
            </button>
          );
        })}
      </div>
    </div>
  );
};

export default ImagePositionSelector;
