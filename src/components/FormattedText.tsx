import { Fragment, type ReactNode } from "react";

const regex = /(\*\*[\s\S]*?\*\*|__[\s\S]*?__|\*[\s\S]*?\*)/g;

export function parseFormattedText(text: string): ReactNode[] {
  if (!text) return [];

  const parts = text.split(regex);

  return parts.map((part, i) => {
    if (part.startsWith('**') && part.endsWith('**') && part.length >= 4) {
      return <strong key={i}>{part.slice(2, -2)}</strong>;
    }
    if (part.startsWith('__') && part.endsWith('__') && part.length >= 4) {
      return <u key={i}>{part.slice(2, -2)}</u>;
    }
    if (part.startsWith('*') && part.endsWith('*') && part.length >= 2) {
      return <em key={i}>{part.slice(1, -1)}</em>;
    }
    return <Fragment key={i}>{part}</Fragment>;
  });
}

export const FormattedText = ({ text, className }: { text: string; className?: string }) => {
  return <span className={className}>{parseFormattedText(text)}</span>;
};
