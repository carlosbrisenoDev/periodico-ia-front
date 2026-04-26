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
import Dashboard from "./pags/dashboard.tsx";
import AdminLoginPage from "./pags/adminloginpage.tsx";
import AllEntries from "./pags/allentries.tsx";
import AllEntriesAdmin from "./pags/allentriesadmin.tsx";
import AuthorsUsers from "./pags/authorsusers.tsx";
import Categories from "./pags/categories.tsx";
import NewPublication from "./pags/newpublication.tsx";
import EditPublication from "./pags/editpublication.tsx";
import ImageLibrary from "./pags/imagelibrary.tsx";
import SubscriptionPage from "./pags/subscription.tsx";
import DeletedEntries from "./pags/deletedentries.tsx";
import SettingsPage from "./pags/settings.tsx";
import { PublicationPreview } from "./pags/publicationpreview.tsx";
import HomePage from "./pags/homepage.tsx";
import CategoryPage from "./pags/categorypage.tsx";
import {
  getOptionalMe,
  isAdmin,
} from "./libs/http.ts";
import type { ProfileData } from "./libs/types.ts";



const AdminRoute = () => {
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
    return <p className="route-guard-loading">Validando sesion...</p>;
  }

  if (!profile || !isAdmin(profile)) {
    return (
      <Navigate
        to="/adminlogin"
        replace
        state={{ redirectTo: location.pathname + location.search }}
      />
    );
  }

  return <Outlet />;
};

const App = () => {
  return (
    <BrowserRouter>
      <Routes>
        <Route path="/" element={<HomePage />} />
        <Route path="/categoria/:slug" element={<CategoryPage />} />
        <Route path="/suscripcion" element={<SubscriptionPage />} />
        <Route path="/adminlogin" element={<AdminLoginPage />} />

        <Route element={<AdminRoute />}>
          <Route path="/dashboard" element={<Dashboard />} />
          <Route path="/allentries" element={<AllEntries />} />
          <Route path="/all-entries" element={<AllEntriesAdmin />} />
          <Route path="/new-publication" element={<NewPublication />} />
          <Route path="/image-library" element={<ImageLibrary />} />
          <Route path="/publication/preview" element={<PublicationPreview />} />
          <Route path="/publication/:id/preview" element={<PublicationPreview />} />
          <Route path="/publication/:id/edit" element={<EditPublication />} />
          <Route path="/authors-users" element={<AuthorsUsers />} />
          <Route path="/categories" element={<Categories />} />
          <Route path="/deleted-entries" element={<DeletedEntries />} />
          <Route path="/settings" element={<SettingsPage />} />
        </Route>
      </Routes>
    </BrowserRouter>
  );
};

export default App;
