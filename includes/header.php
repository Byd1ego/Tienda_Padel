<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="static/css/estilos.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    
</head>

<body>
    <div>
        <header>
    <input type="checkbox" id="hamburguesa">
    <img src="static/img/logo.png" alt="" width="180">

    <label for="hamburguesa" id="icono">
        <i class="fa fa-bars"></i>
    </label>

    <nav>
        <ul class="menu">
            <li>Inicio</li>
            <li>Acerca de</li>
            <li>Productos</li>
            <?php
            if(isset($_SESSION['rol'])){
            echo '<li>Ofertas</li>';
            }
            ?>
            <li>Contacto</li>
            <?php
            if(isset($_SESSION['rol'])){
            echo '<li><a href="logout.php">Logout</a></li>';
            }else{
            echo '<li><a href="login.php">Login</a></li>';
            }
            ?>
            
        </ul>
    </nav>
</header>