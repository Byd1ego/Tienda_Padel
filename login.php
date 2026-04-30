<?php
session_start();

if (isset($_SESSION['usuario'])) {
    header("Location: admin/productos.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $clave   = $_POST['clave']   ?? '';

    if ($usuario === 'admin' && $clave === '1234') {
        $_SESSION['usuario'] = $usuario;
        $_SESSION['rol']     = 'admin';
        header("Location: admin/productos.php");
        exit();
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
    <h1 class="login-titulo">🔐 Acceso al sistema</h1>

    <?php if (isset($_GET['redirigido'])): ?>
        <p class="alerta">Por favor, identifíquese para acceder a esa página.</p>
    <?php endif; ?>

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
            <a href="index.php" class="boton-cerrar">Cancelar</a>
            <button type="submit" class="boton-nuevo">Entrar</button>
        </div>
    </form>
</div>

</body>
</html>
</html>