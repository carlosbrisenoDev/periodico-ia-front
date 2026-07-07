import { useEffect, useState } from "react";
import PublicNavbar from "../components/PublicNavbar.tsx";
import PublicFooter from "../components/PublicFooter.tsx";
import { apiFetch, getPublicCategories } from "../libs/http.ts";
import { API_BASE_URL } from "../libs/config.ts";
import type { PublicCategory } from "../libs/types.ts";

export default function PrintEditionPage() {
  const [pdfUrl, setPdfUrl] = useState<string | null>(null);
  const [categories, setCategories] = useState<PublicCategory[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const controller = new AbortController();

    const load = async () => {
      try {
        const [settingsData, fetchedCats] = await Promise.all([
          apiFetch<any>(`${API_BASE_URL}/api/v1/public/settings`, { signal: controller.signal }).catch(() => ({})),
          getPublicCategories(controller.signal).catch(() => [])
        ]);

        if (settingsData && settingsData.printEditionLink) {
          setPdfUrl(settingsData.printEditionLink);
        }
        setCategories(fetchedCats);
      } catch (err) {
        console.error("Error fetching print edition data:", err);
      } finally {
        setLoading(false);
      }
    };
    void load();

    return () => controller.abort();
  }, []);

  return (
    <div className="ph-page">
      <PublicNavbar categories={categories} />
      
      <main className="ph-main" style={{ padding: "40px 20px", flex: 1, display: "flex", flexDirection: "column" }}>
        <h1 style={{ textAlign: "center", marginBottom: "20px", fontSize: "2rem" }}>Edición Impresa</h1>
        
        <div style={{ flex: 1, minHeight: "80vh", backgroundColor: "#f5f5f5", borderRadius: "8px", overflow: "hidden", display: "flex", alignItems: "center", justifyContent: "center" }}>
          {loading ? (
            <p>Cargando edición impresa...</p>
          ) : pdfUrl ? (
            <iframe
              src={pdfUrl}
              title="Edición Impresa"
              style={{ width: "100%", height: "100%", border: "none", minHeight: "80vh" }}
            />
          ) : (
            <p style={{ color: "#666", fontSize: "1.2rem" }}>Por el momento no hay una edición impresa disponible.</p>
          )}
        </div>
      </main>

      <PublicFooter />
    </div>
  );
}
