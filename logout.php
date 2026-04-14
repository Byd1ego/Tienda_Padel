<?php
    session_start();
    
    // 1. Limpiamos el array de sesión en el servidor
    $_SESSION = [];

    // 2. Destruimos la cookie en el cliente con sus parámetros exactos
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // 3. Destruimos la sesión en el servidor
    session_destroy();

    // 4. Redirigimos al inicio
    header("Location: index.php");
    exit();
?>

