import { useEffect, useRef } from "react";

type AdBlockProps = {
  adClient?: string;
  adSlot?: string;
  adFormat?: "auto" | "fluid" | "rectangle";
  fullWidthResponsive?: boolean;
  style?: React.CSSProperties;
  className?: string;
};

export const AdBlock = ({ 
  adClient = "ca-pub-XXXXXXXXXXXXXXXX", 
  adSlot = "XXXXXXXXXX", 
  adFormat = "auto", 
  fullWidthResponsive = true,
  style,
  className
}: AdBlockProps) => {
  const adRef = useRef<HTMLModElement>(null);
  const isDev = import.meta.env.DEV; // Vite env flag

  useEffect(() => {
    if (!isDev) {
      try {
        // Required for Google AdSense to push ads into the ins element
        ((window as any).adsbygoogle = (window as any).adsbygoogle || []).push({});
      } catch (err) {
        console.error("AdSense Error", err);
      }
    }
  }, [isDev]);

  if (isDev) {
    return (
      <div 
        className={`ad-block-placeholder ${className || ""}`}
        style={{
          display: "flex",
          alignItems: "center",
          justifyContent: "center",
          background: "#e5e7eb",
          color: "#9ca3af",
          border: "1px dashed #9ca3af",
          minHeight: "100px",
          width: "100%",
          padding: "1rem",
          textAlign: "center",
          fontFamily: "monospace",
          fontSize: "0.875rem",
          ...style
        }}
      >
        [Espacio Publicitario] <br /> {adFormat} - {adSlot}
      </div>
    );
  }

  return (
    <div className={`ad-block-container ${className || ""}`} style={{ overflow: "hidden", ...style }}>
      <ins
        ref={adRef}
        className="adsbygoogle"
        style={{ display: "block", ...style }}
        data-ad-client={adClient}
        data-ad-slot={adSlot}
        data-ad-format={adFormat}
        data-full-width-responsive={fullWidthResponsive ? "true" : "false"}
      />
    </div>
  );
};
