import { useEffect, useState } from "react";
import { useLocation } from "react-router-dom";
import PublicNavbar from "../components/PublicNavbar.tsx";
import PublicFooter from "../components/PublicFooter.tsx";
import { API_BASE_URL } from "../libs/config.ts";
import type { PublicCategory } from "../libs/types.ts";

type VideoAsset = {
  id: string;
  url: string;
  platform: string;
  videoExternalId: string;
  title?: string;
};

const getYoutubeEmbedUrl = (videoId: string) => `https://www.youtube.com/embed/${videoId}`;

export default function PublicVideosPage() {
  const [videos, setVideos] = useState<VideoAsset[]>([]);
  const [categories, setCategories] = useState<PublicCategory[]>([]);
  const [loading, setLoading] = useState(true);
  const location = useLocation();

  useEffect(() => {
    window.scrollTo(0, 0);
  }, [location.pathname]);

  useEffect(() => {
    const fetchData = async () => {
      try {
        const [videosRes, categoriesRes] = await Promise.all([
          fetch(`${API_BASE_URL}/api/v1/public/videos?limit=50`),
          fetch(`${API_BASE_URL}/api/v1/public/categories`)
        ]);

        if (videosRes.ok) {
          const vData = await videosRes.json();
          setVideos(Array.isArray(vData) ? vData : []);
        }

        if (categoriesRes.ok) {
          const cData = await categoriesRes.json();
          setCategories(Array.isArray(cData) ? cData : []);
        }
      } catch (err) {
        console.error("Error fetching videos or categories", err);
      } finally {
        setLoading(false);
      }
    };

    void fetchData();
  }, []);

  return (
    <div className="public-layout">
      <PublicNavbar categories={categories} activeCategorySlug="videos" />
      
      <main className="public-main" style={{ padding: "40px 20px", maxWidth: "1200px", margin: "0 auto" }}>
        <h1 style={{ fontSize: "2rem", marginBottom: "32px", borderBottom: "2px solid var(--theme-primary-color)", paddingBottom: "12px", color: "var(--text-main)" }}>Videoteca</h1>
        
        {loading ? (
          <p style={{ color: "var(--text-main)" }}>Cargando videos...</p>
        ) : videos.length === 0 ? (
          <p style={{ color: "var(--text-main)" }}>No hay videos disponibles por el momento.</p>
        ) : (
          <div style={{ display: "grid", gridTemplateColumns: "repeat(auto-fill, minmax(320px, 1fr))", gap: "32px" }}>
            {videos.map(video => (
              <div key={video.id} style={{ display: "flex", flexDirection: "column", gap: "12px" }}>
                {video.platform === "youtube" ? (
                  <div style={{ position: "relative", paddingBottom: "56.25%", height: 0, overflow: "hidden", borderRadius: "8px" }}>
                    <iframe
                      src={getYoutubeEmbedUrl(video.videoExternalId)}
                      style={{ position: "absolute", top: 0, left: 0, width: "100%", height: "100%", border: 0 }}
                      allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                      allowFullScreen
                      title={video.title || "Video"}
                    />
                  </div>
                ) : (
                  <div style={{ height: "200px", backgroundColor: "var(--bg-surface)", display: "flex", alignItems: "center", justifyContent: "center", borderRadius: "8px" }}>
                    <a href={video.url} target="_blank" rel="noopener noreferrer" style={{ color: "var(--theme-primary-color)", textDecoration: "underline" }}>
                      Ver video en {video.platform}
                    </a>
                  </div>
                )}
                <h3 style={{ fontSize: "1.125rem", margin: 0, color: "var(--text-main)", lineHeight: 1.4 }}>
                  {video.title || "Video sin título"}
                </h3>
              </div>
            ))}
          </div>
        )}
      </main>

      <PublicFooter categories={categories} />
    </div>
  );
}
