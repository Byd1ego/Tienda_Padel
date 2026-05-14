<?php
// Inicia la sesión para poder destruirla
session_start();

// 1. Vacía todos los datos de la sesión en el servidor
$_SESSION = [];

// 2. Elimina la cookie de sesión del navegador del usuario
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Destruye la sesión completamente en el servidor
session_destroy();

// 4. Redirige al inicio tras cerrar sesión
header("Location: index.php");
exit();
?>

