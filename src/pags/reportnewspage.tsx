import { useEffect, useState } from "react";
import { getSubscriberMe, getPublicCategories } from "../libs/http.ts";
import type { PublicCategory } from "../libs/types.ts";
import { CitizenReportForm } from "../components/CitizenReportForm.tsx";
import PublicNavbar from "../components/PublicNavbar.tsx";
import PublicFooter from "../components/PublicFooter.tsx";
import { useNavigate } from "react-router-dom";

const ReportNewsPage = () => {
  const [loading, setLoading] = useState(true);
  const [user, setUser] = useState<{ username?: string; email?: string } | null>(null);
  const [categories, setCategories] = useState<PublicCategory[]>([]);
  const navigate = useNavigate();

  const checkAuth = async () => {
    setLoading(true);
    try {
      const authUser = await getSubscriberMe();
      if (!authUser) {
        navigate("/suscripcion", { state: { redirectTo: "/reportar" }, replace: true });
        return;
      }
      setUser(authUser);
    } catch {
      navigate("/suscripcion", { state: { redirectTo: "/reportar" }, replace: true });
      return;
    }
    setLoading(false);
  };

  useEffect(() => {
    checkAuth();

    const controller = new AbortController();
    getPublicCategories(controller.signal)
      .then(setCategories)
      .catch(() => {});
    return () => controller.abort();
  }, [navigate]);

  if (loading) {
    return (
      <div style={{ display: "flex", justifyContent: "center", alignItems: "center", minHeight: "100vh" }}>
        <p>Cargando...</p>
      </div>
    );
  }

  return (
    <div className="ph-page">
      <PublicNavbar categories={categories} />

      <main className="ps-main" style={{ minHeight: "100vh", backgroundColor: "var(--bg-main)", padding: "2rem 1rem" }}>
        <div style={{ maxWidth: "800px", margin: "0 auto", paddingBottom: "2rem" }}>

        <CitizenReportForm prefillUser={user || undefined} />
      </div>
      </main>

      <PublicFooter categories={categories} />
    </div>
  );
};

export default ReportNewsPage;
