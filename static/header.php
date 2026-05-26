<?php
// Inicia la sesión para poder leer los datos del usuario logueado
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <!-- Carga los iconos de Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="includes/css/estilos.css">
    <!-- Hace que la web se vea bien en móvil -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda Pádel</title>
</head>

<body>
    <header>
        <!-- Checkbox oculto que controla el menú hamburguesa en móvil -->
        <input type="checkbox" id="hamburguesa">
        <img src="includes/img/logo.png" alt="Logo" width="180">

        <!-- Icono de menú hamburguesa, visible solo en móvil -->
        <label for="hamburguesa" id="icono">
            <i class="fa fa-bars"></i>
        </label>

        <nav>
            <ul class="menu">
                <li><a href="index.php">Inicio</a></li>
                <li><a href="foro.php">Foro</a></li>

                <?php
                // El enlace a Exclusivas solo aparece si el usuario está logueado
                if (isset($_SESSION['rol'])) {
                    echo '<li><a href="exclusivas.php">Exclusivas</a></li>';
                }
                ?>

                <li><a href="contacto.php">Contacto</a></li>

                <?php
                // Si está logueado muestra Logout, si no muestra Login
                if (isset($_SESSION['rol'])) {
                    echo '<li><a href="logout.php">Logout</a></li>';
                } else {
                    echo '<li><a href="login.php">Login</a></li>';
                }

                // Si es admin muestra el enlace al panel de administración
                if (isset($_SESSION['rol']) && $_SESSION['rol'] == 'admin') {
                    echo '<li><a href="admin/productos.php">Administrar productos</a></li>';
                }
                ?>

                <?php
                // Si es usuario normal muestra el icono del carrito
                if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'usuario'): ?>
                    <li><a href="carrito.php"><i class="fa fa-shopping-cart"></i></a></li>
                <?php endif; ?>

            </ul>
        </nav>
    </header>