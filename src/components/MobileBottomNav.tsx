import { Link, useLocation } from "react-router-dom";
import { HomeIcon, TrendingIcon, ListIcon, UserIcon } from "./Icons.tsx";

const MobileBottomNav = () => {
  const location = useLocation();

  return (
    <div className="ph-mobile-bottom-nav">
      <Link
        to="/"
        className={`ph-mobile-bottom-nav-item ${location.pathname === "/" && !location.hash ? "active" : ""}`}
      >
        <HomeIcon />
        <span>Inicio</span>
      </Link>
      <Link
        to="/categoria/tendencias"
        className={`ph-mobile-bottom-nav-item ${location.pathname === "/categoria/tendencias" ? "active" : ""}`}
      >
        <TrendingIcon />
        <span>Tendencias</span>
      </Link>
      <a
        href="/#las-5-de-x"
        className={`ph-mobile-bottom-nav-item ${location.hash === "#las-5-de-x" ? "active" : ""}`}
      >
        <ListIcon />
        <span>Las 5 de X</span>
      </a>
      <Link
        to="/admin"
        className={`ph-mobile-bottom-nav-item ${location.pathname.startsWith("/admin") ? "active" : ""}`}
      >
        <UserIcon />
        <span>Perfil</span>
      </Link>
    </div>
  );
};

export default MobileBottomNav;
