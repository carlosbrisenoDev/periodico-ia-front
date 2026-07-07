import { useEffect, useState } from "react";
import { useParams, useLocation, useNavigate } from "react-router-dom";
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

const getYoutubeEmbedUrl = (videoId: string) => `https://www.youtube.com/embed/${videoId}?autoplay=1`;

export default function VideoViewPage() {
  const { id } = useParams<{ id: string }>();
  const location = useLocation();
  const navigate = useNavigate();
  
  const [video, setVideo] = useState<VideoAsset | null>(location.state?.video || null);
  const [categories, setCategories] = useState<PublicCategory[]>([]);
  const [loading, setLoading] = useState(!location.state?.video);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    window.scrollTo(0, 0);
  }, [location.pathname]);

  useEffect(() => {
    const fetchData = async () => {
      try {
        // Fetch categories for navbar/footer
        const categoriesRes = await fetch(`${API_BASE_URL}/api/v1/public/categories`);
        if (categoriesRes.ok) {
          const cData = await categoriesRes.json();
          setCategories(Array.isArray(cData) ? cData : []);
        }

        // If we didn't get the video from state, we need to fetch it
        if (!video && id) {
          // Attempt to fetch all videos and find ours, assuming there's no single video endpoint
          const videosRes = await fetch(`${API_BASE_URL}/api/v1/public/videos?limit=100`);
          if (videosRes.ok) {
            const vData = await videosRes.json();
            const foundVideo = (Array.isArray(vData) ? vData : []).find(v => v.id === id);
            if (foundVideo) {
              setVideo(foundVideo);
            } else {
              setError("No se encontró el video.");
            }
          } else {
            setError("Error al obtener los videos.");
          }
        }
      } catch (err) {
        console.error("Error fetching video or categories", err);
        setError("Ocurrió un error inesperado.");
      } finally {
        setLoading(false);
      }
    };

    void fetchData();
  }, [id, video]);

  return (
    <div className="public-layout">
      <PublicNavbar categories={categories} />
      
      <main className="public-main" style={{ padding: "40px 20px", maxWidth: "900px", margin: "0 auto", width: "100%", flex: 1, display: 'flex', flexDirection: 'column' }}>
        <button 
          onClick={() => navigate(-1)} 
          style={{ 
            alignSelf: 'flex-start', 
            background: 'none', 
            border: 'none', 
            color: 'var(--text-main)', 
            cursor: 'pointer', 
            marginBottom: '20px',
            fontSize: '1rem',
            display: 'flex',
            alignItems: 'center',
            gap: '8px'
          }}
        >
          &larr; Volver
        </button>

        {loading ? (
          <p style={{ color: "var(--text-main)" }}>Cargando video...</p>
        ) : error ? (
          <p style={{ color: "var(--text-main)" }}>{error}</p>
        ) : video ? (
          <div style={{ display: "flex", flexDirection: "column", gap: "20px" }}>
            <h1 style={{ fontSize: "2rem", margin: 0, color: "var(--text-main)", lineHeight: 1.2 }}>
              {video.title || "Video sin título"}
            </h1>
            
            <div style={{ position: "relative", paddingBottom: "56.25%", height: 0, overflow: "hidden", borderRadius: "12px", backgroundColor: "#000" }}>
              {video.platform === "youtube" ? (
                <iframe
                  src={getYoutubeEmbedUrl(video.videoExternalId)}
                  style={{ position: "absolute", top: 0, left: 0, width: "100%", height: "100%", border: 0 }}
                  allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                  allowFullScreen
                  title={video.title || "Video"}
                />
              ) : (
                <div style={{ position: "absolute", top: 0, left: 0, width: "100%", height: "100%", display: "flex", alignItems: "center", justifyContent: "center" }}>
                  <a href={video.url} target="_blank" rel="noopener noreferrer" style={{ color: "#fff", textDecoration: "underline", fontSize: "1.2rem" }}>
                    Ver video en {video.platform}
                  </a>
                </div>
              )}
            </div>
          </div>
        ) : null}
      </main>

      <PublicFooter categories={categories} />
    </div>
  );
}
