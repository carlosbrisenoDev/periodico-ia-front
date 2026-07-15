import React from "react";
const logoSrc = "/logo.png";
import { FacebookIcon, XIcon, InstagramIcon, YoutubeIcon, TiktokIcon } from "./Icons.tsx";
import type { PublicCategory } from "../libs/types.ts";
import { Link } from "react-router-dom";


interface PublicFooterProps {
  categories?: PublicCategory[];
  variant?: "full" | "search";
}

const PublicFooter: React.FC<PublicFooterProps> = ({ categories = [], variant = "full" }) => {
  
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
                <Link to="/reportar" style={{ color: "var(--text-muted)", textDecoration: "none" }}>
                  Reporte Ciudadano
                </Link>
              </li>
            </ul>
            <h4 className="public-footer-title">Redes Sociales</h4>
            <div className="public-footer-social">
              <a href="https://www.facebook.com/Informaciondealtura" target="_blank" rel="noopener noreferrer" className="public-social-button" aria-label="Facebook"><FacebookIcon /></a>
              <a href="https://x.com/AlturaVeracruz" target="_blank" rel="noopener noreferrer" className="public-social-button" aria-label="X (Twitter)"><XIcon /></a>
              <a href="https://www.instagram.com/informaciondealtura?igsh=Z3lneHZ4OGJ3azM1" target="_blank" rel="noopener noreferrer" className="public-social-button" aria-label="Instagram"><InstagramIcon /></a>
              <a href="https://www.youtube.com/@informaci%C3%B3ndealtura" target="_blank" rel="noopener noreferrer" className="public-social-button" aria-label="YouTube"><YoutubeIcon /></a>
              <a href="https://www.tiktok.com/@informaciondealtura" target="_blank" rel="noopener noreferrer" className="public-social-button" aria-label="TikTok"><TiktokIcon /></a>
            </div>
          </div>
        </div>
        <div className="public-footer-bottom">
          © {new Date().getFullYear()} Información de Altura. Todos los derechos reservados.
        </div>
      </div>
      

    </footer>
  );
};

export default PublicFooter;
