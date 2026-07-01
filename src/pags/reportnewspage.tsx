import { useEffect, useState } from "react";
import { getSubscriberMe } from "../libs/http.ts";
import { CitizenReportForm } from "../components/CitizenReportForm.tsx";
import { SubscriberLoginForm } from "../components/SubscriberLoginForm.tsx";

const ReportNewsPage = () => {
  const [loading, setLoading] = useState(true);
  const [isAuthenticated, setIsAuthenticated] = useState(false);

  const checkAuth = async () => {
    setLoading(true);
    const user = await getSubscriberMe();
    if (user) {
      setIsAuthenticated(true);
    } else {
      setIsAuthenticated(false);
    }
    setLoading(false);
  };

  useEffect(() => {
    checkAuth();
  }, []);

  if (loading) {
    return (
      <div style={{ display: "flex", justifyContent: "center", alignItems: "center", minHeight: "100vh" }}>
        <p>Cargando...</p>
      </div>
    );
  }

  return (
    <div style={{ minHeight: "100vh", backgroundColor: "var(--bg-main)", padding: "2rem 1rem" }}>
      <div style={{ maxWidth: "800px", margin: "0 auto", paddingBottom: "2rem" }}>
        <div style={{ marginBottom: "2rem", display: "flex", alignItems: "center", gap: "1rem" }}>
          <a href="/" style={{ color: "var(--primary-color)", textDecoration: "none", fontWeight: "600" }}>
            ← Volver al inicio
          </a>
        </div>

        {isAuthenticated ? (
          <CitizenReportForm />
        ) : (
          <SubscriberLoginForm onSuccess={() => setIsAuthenticated(true)} />
        )}
      </div>
    </div>
  );
};

export default ReportNewsPage;
