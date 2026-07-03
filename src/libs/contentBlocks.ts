export type ContentBlock =
  | { type: "paragraph"; text: string }
  | { type: "subtitle"; text: string }
  | { type: "image"; url: string; caption?: string }
  | { type: "video"; url: string }
  | { type: "image-row"; urls: string[]; layout?: "equal" | "left-large" | "right-large" };

const SUBTITLE_PATTERN = /^\[\[subtitle:(.+)]]$/i;
const IMAGE_PATTERN = /^\[\[image:([^|\]]+)(?:\|(.*))?]]$/i;
const VIDEO_PATTERN = /^\[\[video:(.+)]]$/i;
const IMAGEROW_PATTERN = /^\[\[image-row(?::layout=(equal|left-large|right-large)|):(.+)]]$/i;

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
      const caption = imageMatch[2]?.trim();
      if (url) {
        blocks.push({ type: "image", url, caption });
      }
      continue;
    }

    const videoMatch = line.match(VIDEO_PATTERN);
    if (videoMatch) {
      flushParagraph();
      const url = videoMatch[1]?.trim();
      if (url) {
        blocks.push({ type: "video", url });
      }
      continue;
    }

    const imageRowMatch = line.match(IMAGEROW_PATTERN);
    if (imageRowMatch) {
      flushParagraph();
      const layout = (imageRowMatch[1] || "equal") as "equal" | "left-large" | "right-large";
      const urlsRaw = imageRowMatch[2] || "";
      const urls = urlsRaw.split("|").map(u => u.trim()).filter(Boolean);
      if (urls.length > 0) {
        blocks.push({ type: "image-row", urls, layout });
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

      if (block.type === "video") {
        const url = block.url.trim();
        return url ? [`[[video: ${url}]]`] : [];
      }

      if (block.type === "image-row") {
        if (block.urls.length === 0) return [];
        const layoutSegment = block.layout && block.layout !== 'equal' ? `:layout=${block.layout}` : '';
        return [`[[image-row${layoutSegment}: ${block.urls.join(' | ')}]]`];
      }

      if (block.type === "image") {
        const url = block.url.trim();
        const caption = block.caption?.trim();
        if (!url) return [];
        return caption ? [`[[image: ${url} | ${caption}]]`] : [`[[image: ${url}]]`];
      }

      return [];
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
