import React from "react";
import logoSrc from "../assets/logo.png";
import { PhoneIcon, FacebookIcon, XIcon, InstagramIcon, LinkedInIcon } from "./Icons.tsx";
import type { PublicCategory } from "../libs/types.ts";
import { CitizenReportForm } from "./CitizenReportForm.tsx";


interface PublicFooterProps {
  categories?: PublicCategory[];
  variant?: "full" | "search";
}

const PublicFooter: React.FC<PublicFooterProps> = ({ categories = [], variant = "full" }) => {
  const [showCitizenReport, setShowCitizenReport] = React.useState(false);
  
  const safeCategories = Array.isArray(categories) ? categories : [];
  if (variant === "search") {
    return (
      <footer className="public-footer">
        <div className="public-footer-inner">
          <div className="public-footer-bottom" style={{ textAlign: "center", paddingTop: "20px", borderTop: "1px solid #eee", marginTop: "20px" }}>
            © {new Date().getFullYear()} Información de Altura. Todos los derechos reservados.
          </div>
        </div>
      </footer>
    );
  }

  return (
    <footer className="public-footer">
      <div className="public-footer-inner">
        <div className="public-footer-grid">
          <div className="public-footer-brand">
            <a href="/" style={{ display: "inline-block", marginBottom: 16 }}>
              <img src={logoSrc} alt="Información de Altura" style={{ height: 80, width: "auto" }} />
            </a>
            <p>Periodismo independiente para el mundo moderno.</p>
            <div className="public-footer-phone">
              <PhoneIcon />
              <span>+34 900 123 456</span>
            </div>
          </div>
          <div>
            <h4 className="public-footer-title">Secciones</h4>
            <ul className="public-footer-links">
              <li>
                <a className="public-footer-link" href="/recientes">
                  Recientes
                </a>
              </li>
              {safeCategories.slice(0, 10).map((c) => (
                <li key={c.id}>
                  <a className="public-footer-link" href={`/categoria/${c.slug || c.id}`}>
                    {c.name}
                  </a>
                </li>
              ))}
            </ul>
          </div>
          <div>
            <h4 className="public-footer-title">Participa</h4>
            <ul className="public-footer-links" style={{ marginBottom: "2rem" }}>
              <li>
                <button 
                  className="public-footer-link" 
                  style={{ background: "none", border: "none", padding: 0, cursor: "pointer", font: "inherit", color: "inherit", textAlign: "left" }}
                  onClick={() => setShowCitizenReport(true)}
                >
                  Envía tu Reporte Ciudadano
                </button>
              </li>
            </ul>
            <h4 className="public-footer-title">Redes Sociales</h4>
            <div className="public-footer-social">
              <a href="#" className="public-social-button" aria-label="Facebook"><FacebookIcon /></a>
              <a href="#" className="public-social-button" aria-label="X (Twitter)"><XIcon /></a>
              <a href="#" className="public-social-button" aria-label="Instagram"><InstagramIcon /></a>
              <a href="#" className="public-social-button" aria-label="LinkedIn"><LinkedInIcon /></a>
            </div>
          </div>
        </div>
        <div className="public-footer-bottom">
          © {new Date().getFullYear()} Información de Altura. Todos los derechos reservados.
        </div>
      </div>
      
      <CitizenReportForm 
        isOpen={showCitizenReport} 
        onClose={() => setShowCitizenReport(false)} 
      />
    </footer>
  );
};

export default PublicFooter;
