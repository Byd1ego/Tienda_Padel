<?php
session_start();

// Si ya está logeado, redirigimos a index.php
if (isset($_SESSION['usuario'])) {
    header("Location: admin/productos.php");
    exit();
}

$error = '';

// Comprobamos si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $clave = $_POST['clave'] ?? '';

    // Para simplificar, usuario/admin con contraseña fija
    // Más adelante se puede hacer con tabla de usuarios en BD
    if ($usuario === 'admin' && $clave === '1234') {
        $_SESSION['usuario'] = $usuario;
        $_SESSION['rol'] = 'admin';        
        header("Location: admin/productos.php");
        exit();
    } elseif($usuario === 'usuario' && $clave === '5678'){
        $_SESSION['usuario'] = $usuario;
        $_SESSION['rol'] = 'usuario'; 
        header("Location: index.php");
        exit();  
    }else {
        $error = "Usuario o contraseña incorrectos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Tienda</title>
    <link rel="stylesheet" href="./resources/estilos.css">    
</head>
<body>
<div class="contenedor">
    <h1>🔐 Acceso al sistema</h1>

    <?php if(isset($_GET["redirigido"])) { ?>
        <p class="alerta">Por favor, identifíquese para acceder a esa página.</p>
    <?php } ?>

    <?php if ($error) { ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php } ?>

    <form action="" method="post" class="login-form">
        <div class="form-grupo">
            <label for="usuario">Usuario</label>
            <input type="text" name="usuario" id="usuario" required>
        </div>

        <div class="form-grupo">
            <label for="clave">Contraseña</label>
            <input type="password" name="clave" id="clave" required>
        </div>

        <div class="form-botones">
            <button type="submit" class="btn btn-nuevo">Entrar</button>
            <a href="index.php" class="btn btn-borrar">Cancelar</a>
        </div>
    </form>
</div>
</body>
</html>