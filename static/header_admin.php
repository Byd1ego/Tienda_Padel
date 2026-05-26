<?php
// Inicia la sesión para poder leer los datos del usuario logueado
session_start();

// Si el usuario no es admin, lo manda al index y detiene la ejecución
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../index.php?acceso_denegado=1");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <!-- Hace que la web se vea bien en móvil -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Carga los iconos de Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../includes/css/estilos.css">
    <title>Admin - Tienda Pádel</title>
</head>
<body>

<header class="header-admin">
    <!-- Checkbox oculto que controla el menú hamburguesa en móvil -->
    <input type="checkbox" id="hamburguesa-admin">

    <!-- Logo que lleva a la tienda -->
    <a href="../index.php">
        <img src="../includes/img/logo.png" alt="Logo" width="180">
    </a>

    <!-- Icono de menú hamburguesa, visible solo en móvil -->
    <label for="hamburguesa-admin" id="icono-admin">
        <i class="fa fa-bars"></i>
    </label>

    <!-- Menú de navegación del panel de administración -->
    <nav>
        <ul class="menu-admin">
            <li><a href="productos.php">Productos</a></li>
            <li><a href="pedidos.php">Pedidos</a></li>
            <li><a href="contacto.php">Contacto</a></li>
            <li><a href="producto_nuevo.php">Nuevo producto</a></li>
            <li><a href="exportar_pdf.php">Exportar PDF</a></li>
            <li><a href="../index.php">Tienda</a></li>
            <li><a href="../logout.php">Cerrar sesión</a></li>
        </ul>
    </nav>
</header>