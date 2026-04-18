export const LoginButton = () => {

    const handleRedirect = () => {
        window.location.href = "/adminlogin";
    }

    return <>
        <button onClick={handleRedirect} >Inicia sesión</button>
    </>
}