import {BrowserRouter, Route, Routes} from "react-router-dom";
import "./App.css";
import Dashboard from "./pags/dashboard.tsx";
import AdminLoginPage from "./pags/adminloginpage.tsx";
import AllEntries from "./pags/allentries.tsx";

const router = () => {
    return (<BrowserRouter>
            <Routes>
                {/*public*/}
                <Route path="/" element={<h1>Bienvenido a la página de noticias
                <a href={"/adminlogin"}>Iniciar sesión</a>
                </h1>}/>

                {/*auth*/}
                <Route path="/adminlogin" element={<AdminLoginPage/>}/>

                {/*admin*/}
                <Route path="/dashboard" element={<Dashboard/>}/>
                <Route path="/allentries" element={<AllEntries/>}/>
                {/*<Route path="/search/:query" element={<SearchPage/>} />*/}
            </Routes>
        </BrowserRouter>);
};

export default router;
