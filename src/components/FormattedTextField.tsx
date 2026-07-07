import { useRef } from "react";

type FormattedTextFieldProps = {
  id?: string;
  value: string;
  onChange: (value: string) => void;
  placeholder?: string;
  disabled?: boolean;
  multiline?: boolean;
  rows?: number;
  maxLength?: number;
  className?: string;
};

export function FormattedTextField({
  id,
  value,
  onChange,
  placeholder,
  disabled,
  multiline = false,
  rows = 4,
  maxLength,
  className,
}: FormattedTextFieldProps) {
  const ref = useRef<HTMLInputElement | HTMLTextAreaElement | null>(null);

  const wrapSelection = (prefix: string, suffix: string) => {
    const el = ref.current;
    if (!el || disabled) return;
    const start = el.selectionStart ?? 0;
    const end = el.selectionEnd ?? 0;
    const text = el.value;
    const selected = text.slice(start, end);
    const newText = text.slice(0, start) + prefix + selected + suffix + text.slice(end);
    onChange(newText);
    requestAnimationFrame(() => {
      el.focus();
      const newCursorPos =
        selected.length > 0
          ? start + prefix.length + selected.length + suffix.length
          : start + prefix.length;
      el.setSelectionRange(newCursorPos, newCursorPos);
    });
  };

  return (
    <div className="formatted-field-wrap">
      <div className="editor-format-toolbar">
        <button
          type="button"
          className="editor-format-btn"
          title="Negrita"
          onClick={() => wrapSelection("**", "**")}
          disabled={disabled}
        >
          <strong>B</strong>
        </button>
        <button
          type="button"
          className="editor-format-btn"
          title="Itálica"
          onClick={() => wrapSelection("*", "*")}
          disabled={disabled}
        >
          <em>I</em>
        </button>
        <button
          type="button"
          className="editor-format-btn"
          title="Subrayado"
          onClick={() => wrapSelection("__", "__")}
          disabled={disabled}
        >
          <u>U</u>
        </button>
      </div>
      {multiline ? (
        <textarea
          id={id}
          className={`new-publication-textarea formatted-field-input ${className || ""}`}
          rows={rows}
          maxLength={maxLength}
          placeholder={placeholder}
          value={value}
          onChange={(e) => onChange(e.target.value)}
          disabled={disabled}
          ref={(el) => {
            ref.current = el;
          }}
        />
      ) : (
        <input
          id={id}
          className={`new-publication-input formatted-field-input ${className || ""}`}
          type="text"
          placeholder={placeholder}
          value={value}
          onChange={(e) => onChange(e.target.value)}
          disabled={disabled}
          ref={(el) => {
            ref.current = el;
          }}
        />
      )}
    </div>
  );
}
