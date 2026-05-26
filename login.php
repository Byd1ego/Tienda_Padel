<?php
include_once 'includes/header.php';

// Si ya hay sesión activa y no viene del redirigido, manda a su página correspondiente
if (isset($_SESSION['usuario']) && !isset($_GET['redirigido'])) {
    if ($_SESSION['rol'] === 'admin') {
        header("Location: admin/productos.php");
    } else {
        header("Location: index.php?acceso_denegado=1");
    }
    exit();
}

$error = '';

// Si el formulario ha sido enviado, comprueba las credenciales
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $clave   = $_POST['clave']   ?? '';

    // Comprueba si es admin y guarda su rol en la sesión
    if ($usuario === 'admin' && $clave === '1234') {
        $_SESSION['usuario'] = $usuario;
        $_SESSION['rol']     = 'admin';
        header("Location: admin/productos.php");
        exit();
    // Comprueba si es usuario normal y guarda su rol en la sesión
    } elseif ($usuario === 'usuario' && $clave === '5678') {
        $_SESSION['usuario'] = $usuario;
        $_SESSION['rol']     = 'usuario';
        header("Location: index.php");
        exit();
    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Tienda</title>
    <link rel="stylesheet" href="./static/css/estilos.css">
</head>
<body>

<div class="login-contenedor">
    <h1 class="login-titulo">Iniciar Sesión</h1>

    <!-- Aviso cuando el usuario llega al login porque intentó acceder a una página protegida -->
    <?php if (isset($_GET['redirigido'])): ?>
        <p class="alerta">Identifícate para acceder a esa página.</p>
    <?php endif; ?>

    <!-- Mensaje de error si las credenciales son incorrectas -->
    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form action="" method="post" class="formulario">
        <div class="form-grupo">
            <label for="usuario">Usuario</label>
            <input type="text" name="usuario" id="usuario" required>
        </div>
        <div class="form-grupo">
            <label for="clave">Contraseña</label>
            <input type="password" name="clave" id="clave" required>
        </div>
        <div class="form-botones">
            <a href="index.php" class="boton-cerrar">Volver</a>
            <button type="submit" class="boton-nuevo">Iniciar Sesion</button>
        </div>
    </form>
</div>

</body>
</html>
<?php include_once 'includes/footer.php'; ?>