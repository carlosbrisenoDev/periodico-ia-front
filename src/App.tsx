import { useEffect, useState } from "react";
import {
  BrowserRouter,
  Navigate,
  Outlet,
  Route,
  Routes,
  useLocation,
} from "react-router-dom";
import "./App.css";

const useContentProtection = () => {
  const location = useLocation();

  useEffect(() => {
    const isAdminRoute = [
      "/admin", "/dashboard", "/allentries", "/all-entries", 
      "/new-publication", "/publication/", "/authors-users", 
      "/subscribers", "/comments", "/citizen-reports", "/categories", 
      "/deleted-entries", "/settings", "/image-library"
    ].some(path => location.pathname.includes(path) || location.pathname.startsWith(path));

    if (!isAdminRoute) {
      // Apply CSS protection
      document.body.style.userSelect = "none";
      document.body.style.webkitUserSelect = "none";
      
      const disableEvents = (e: Event) => {
        // Allow clicks on inputs, textareas, etc.
        const target = e.target as HTMLElement;
        if (target.tagName === "INPUT" || target.tagName === "TEXTAREA" || target.tagName === "SELECT") {
          return;
        }
        e.preventDefault();
      };

      document.addEventListener("contextmenu", disableEvents);
      document.addEventListener("copy", disableEvents);
      document.addEventListener("cut", disableEvents);

      return () => {
        document.body.style.userSelect = "";
        document.body.style.webkitUserSelect = "";
        document.removeEventListener("contextmenu", disableEvents);
        document.removeEventListener("copy", disableEvents);
        document.removeEventListener("cut", disableEvents);
      };
    } else {
      document.body.style.userSelect = "";
      document.body.style.webkitUserSelect = "";
    }
  }, [location.pathname]);
};

import Dashboard from "./pags/dashboard.tsx";
import AdminLoginPage from "./pags/adminloginpage.tsx";
import AllEntries from "./pags/allentries.tsx";
import AllEntriesAdmin from "./pags/allentriesadmin.tsx";
import AuthorsUsers from "./pags/authorsusers.tsx";
import Subscribers from "./pags/subscribers.tsx";
import CommentsModeration from "./pags/comments.tsx";
import CitizenReportsModeration from "./pags/citizen-reports.tsx";
import Categories from "./pags/categories.tsx";
import NewPublication from "./pags/newpublication.tsx";
import EditPublication from "./pags/editpublication.tsx";
import ImageLibrary from "./pags/imagelibrary.tsx";
import VideosPage from "./pags/videos.tsx";
import ReportNewsPage from "./pags/reportnewspage.tsx";
import SubscriptionPage from "./pags/subscription.tsx";
import DeletedEntries from "./pags/deletedentries.tsx";
import SettingsPage from "./pags/settings.tsx";
import GlobalSettingsPage from "./pags/globalsettings.tsx";
import { PublicationPreview } from "./pags/publicationpreview.tsx";
import HomePage from "./pags/homepage.tsx";
import CategoryPage from "./pags/categorypage.tsx";
import SearchPage from "./pags/searchpage.tsx";
import RecentPage from "./pags/recentpage.tsx";
import PublicVideosPage from "./pags/publicvideos.tsx";
import {
  getOptionalMe,
} from "./libs/http.ts";
import type { ProfileData } from "./libs/types.ts";



const ProtectedRoute = ({ allowedRoles }: { allowedRoles?: string[] }) => {
  const location = useLocation();
  const [loading, setLoading] = useState(true);
  const [profile, setProfile] = useState<ProfileData | null>(null);

  useEffect(() => {
    const controller = new AbortController();

    const validateSession = async () => {
      try {
        const me = await getOptionalMe(controller.signal);
        setProfile(me);
      } finally {
        setLoading(false);
      }
    };

    void validateSession();

    return () => controller.abort();
  }, []);

  if (loading) {
    return <p className="route-guard-loading">Validando sesión...</p>;
  }

  if (!profile) {
    return (
      <Navigate
        to="/adminlogin"
        replace
        state={{ redirectTo: location.pathname + location.search }}
      />
    );
  }

  if (allowedRoles && !allowedRoles.includes(profile.role)) {
    return <Navigate to="/dashboard" replace />;
  }

  return <Outlet />;
};

const App = () => {
  return (
    <BrowserRouter>
      <AppContent />
    </BrowserRouter>
  );
};

const AppContent = () => {
  useContentProtection();

  return (
      <Routes>
        <Route path="/" element={<HomePage />} />
        <Route path="/categoria/:id" element={<CategoryPage />} />
        <Route path="/articulo/:id" element={<PublicationPreview />} />
        <Route path="/buscar" element={<SearchPage />} />
        <Route path="/recientes" element={<RecentPage />} />
        <Route path="/videoteca" element={<PublicVideosPage />} />
        <Route path="/suscripcion" element={<SubscriptionPage />} />
        <Route path="/adminlogin" element={<AdminLoginPage />} />
        <Route path="/reportar" element={<ReportNewsPage />} />

        <Route path="/admin" element={<Navigate to="/adminlogin" replace />} />

        <Route element={<ProtectedRoute />}>
          <Route path="/dashboard" element={<Dashboard />} />
          <Route path="/allentries" element={<AllEntries />} />
          <Route path="/new-publication" element={<NewPublication />} />
          <Route path="/image-library" element={<ImageLibrary />} />
          <Route path="/videos" element={<VideosPage />} />
          <Route path="/publication/preview" element={<PublicationPreview />} />
          <Route path="/publication/:id/preview" element={<PublicationPreview />} />
          <Route path="/publication/:id/edit" element={<EditPublication />} />
          <Route path="/authors-users" element={<AuthorsUsers />} />
          <Route path="/subscribers" element={<Subscribers />} />
          <Route path="/comments" element={<CommentsModeration />} />
          <Route path="/citizen-reports" element={<CitizenReportsModeration />} />
          <Route path="/categories" element={<Categories />} />
          <Route path="/deleted-entries" element={<DeletedEntries />} />
          <Route path="/settings" element={<SettingsPage />} />

          <Route element={<ProtectedRoute allowedRoles={["admin"]} />}>
            <Route path="/global-settings" element={<GlobalSettingsPage />} />
          </Route>

        </Route>

        <Route element={<ProtectedRoute allowedRoles={["admin"]} />}>
          <Route path="/all-entries" element={<AllEntriesAdmin />} />
        </Route>

      </Routes>
  );
};

export default App;
