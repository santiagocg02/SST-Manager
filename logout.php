<?php
require_once __DIR__ . '/includes/bootstrap.php';

// 1. Iniciar la sesión para poder destruirla
startSessionIfNeeded();

// 2. Destruir todas las variables de sesión (Token, Usuario, Rol, etc.)
$_SESSION = array();

// 3. Borrar la cookie de sesión del navegador (Para seguridad extra)
// Esto asegura que el navegador olvide el ID de sesión anterior
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Destruir la sesión en el servidor
session_destroy();

// 5. Redirigir al Login
redirectToLogin();
?>