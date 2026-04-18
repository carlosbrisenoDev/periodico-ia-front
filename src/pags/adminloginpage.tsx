import { useEffect } from "react";
import { API_BASE_URL } from "../libs/config.ts";

const Adminloginpage = () => {
    const apiUrl = API_BASE_URL;

    const meFetch = async () => {
        try {
            const response = await fetch(`${apiUrl}/api/v1/auth/me`, {
                method: "GET",
                credentials: "include",
                headers: {
                    "Content-Type": "application/json",
                },
            });

            if (response.status === 200) {
                window.location.href = "/dashboard";
            }
        } catch (error) {
            console.error("Error:", error);
        }
    };

    useEffect(() => {
        void meFetch();
    }, []);

    const logInFetch = async () => {
        const email = (document.getElementById("email") as HTMLInputElement).value;
        const password = (document.getElementById("password") as HTMLInputElement).value;

        try {
            const response = await fetch(`${apiUrl}/api/v1/auth/login`, {
                method: "POST",
                credentials: "include",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    email,
                    password,
                }),
            });

            if (!response.ok) {
                alert("Credenciales invalidas o servidor no disponible");
                return;
            }

            window.location.href = "/dashboard";
        } catch (error) {
            console.error("Error:", error);
            alert("Error al conectar con el servidor");
        }
    };

    return (<div className="login">
            <div className="loginform">
                <div className="svg-black center admin-icon">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        strokeWidth="2"
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        className="white-text"
                    >
                        <path d="M15 18h-5"></path>
                        <path d="M18 14h-8"></path>
                        <path
                            d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-4 0v-9a2 2 0 0 1 2-2h2"></path>
                        <rect width="8" height="4" x="10" y="6" rx="1"></rect>
                    </svg>
                </div>

                <h1 className="login-title">Panel de Administración</h1>
                <p className="text-muted login-subtitle">Ingresa tus credenciales para continuar</p>

                <form className="login-form">
                    <label htmlFor="email">Correo electrónico</label>
                    <input id="email" name="email" type="email" placeholder="admin@periodico.com" required/>

                    <label htmlFor="password">Contraseña</label>
                    <input id="password" name="password" type="password" placeholder="••••••••" required/>

                    <button className="login-button" onClick={(e) => {
                        e.preventDefault();
                        logInFetch();
                    }}>
                        Iniciar Sesión
                    </button>
                </form>
            </div>
        </div>)
}

export default Adminloginpage;