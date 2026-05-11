<?php
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../login.php?redirigido=true");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../static/css/estilos.css">
    <title>Admin - Tienda Pádel</title>
</head>
<body>

<header>
    <input type="checkbox" id="hamburguesa-admin">
    <a href="../index.php">
        <img src="../static/img/logo.png" alt="Logo" width="180">
    </a>

    <label for="hamburguesa-admin" id="icono-admin">
        <i class="fa fa-bars"></i>
    </label>

    <nav>
        <ul class="menu-admin">
            <li><a href="productos.php">Productos</a></li>
            <li><a href="producto_nuevo.php">Nuevo producto</a></li>
            <li><a href="exportar_pdf.php">Exportar PDF</a></li>
            <li><a href="../index.php">Tienda</a></li>
            <li><a href="../logout.php">Cerrar sesión</a></li>
        </ul>
    </nav>
</header>