export type ContentBlock =
  | { type: "paragraph"; text: string }
  | { type: "subtitle"; text: string }
  | { type: "image"; url: string };

const SUBTITLE_PATTERN = /^\[\[subtitle:(.+)\]\]$/i;
const IMAGE_PATTERN = /^\[\[image:(.+)\]\]$/i;

const normalizeLine = (line: string): string => line.trim();

export const parseContentBlocks = (content: string): ContentBlock[] => {
  const lines = content.split(/\r?\n/);
  const blocks: ContentBlock[] = [];
  const paragraphBuffer: string[] = [];

  const flushParagraph = () => {
    if (paragraphBuffer.length === 0) {
      return;
    }

    const text = paragraphBuffer.join("\n").trim();
    paragraphBuffer.length = 0;

    if (!text) {
      return;
    }

    blocks.push({ type: "paragraph", text });
  };

  for (const rawLine of lines) {
    const line = normalizeLine(rawLine);

    if (!line) {
      flushParagraph();
      continue;
    }

    const subtitleMatch = line.match(SUBTITLE_PATTERN);
    if (subtitleMatch) {
      flushParagraph();
      const text = subtitleMatch[1]?.trim();
      if (text) {
        blocks.push({ type: "subtitle", text });
      }
      continue;
    }

    const imageMatch = line.match(IMAGE_PATTERN);
    if (imageMatch) {
      flushParagraph();
      const url = imageMatch[1]?.trim();
      if (url) {
        blocks.push({ type: "image", url });
      }
      continue;
    }

    paragraphBuffer.push(rawLine.trimEnd());
  }

  flushParagraph();
  return blocks;
};

export const serializeContentBlocks = (blocks: ContentBlock[]): string => {
  return blocks
    .flatMap((block) => {
      if (block.type === "paragraph") {
        const text = block.text.trim();
        return text ? [text] : [];
      }

      if (block.type === "subtitle") {
        const text = block.text.trim();
        return text ? [`[[subtitle: ${text}]]`] : [];
      }

      const url = block.url.trim();
      return url ? [`[[image: ${url}]]`] : [];
    })
    .join("\n\n");
};

export const insertBlockTemplate = (
  currentValue: string,
  template: string,
  selectionStart: number,
  selectionEnd: number,
): { value: string; caretPosition: number } => {
  const before = currentValue.slice(0, selectionStart);
  const after = currentValue.slice(selectionEnd);

  const needsLeadingBreak = before.length > 0 && !before.endsWith("\n\n");
  const needsTrailingBreak = after.length > 0 && !after.startsWith("\n\n");

  const insertion = `${needsLeadingBreak ? "\n\n" : ""}${template}${needsTrailingBreak ? "\n\n" : ""}`;
  const value = `${before}${insertion}${after}`;

  return {
    value,
    caretPosition: before.length + insertion.length,
  };
};
