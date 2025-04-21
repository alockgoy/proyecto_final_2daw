<?php
//iniciar la sesión
session_start();

//destruir todas las variables de sesión
$_SESSION = array();

//destruir la sesión
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

//redirigir al usuario a la página de inicio de sesión
header("Location: ../../index.html");
exit();
?>