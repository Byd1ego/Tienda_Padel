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
            <li><a href="index.php">Inicio</a></li>
            <li><a href="Acercade.php">Acerca de</a></li>
            <?php
            if(isset($_SESSION['rol'])){
            echo '<li><a href="exclusivas.php">Exclusivas</a></li>';
            }
            ?>
            <li><a href="Contacto.php">Contacto</a></li>
            <?php
            if(isset($_SESSION['rol'])){
            echo '<li><a href="logout.php">Logout</a></li>';
            }else{
            echo '<li><a href="login.php">Login</a></li>';
            }
             if(isset($_SESSION['rol'])){
                if($_SESSION['rol'] == 'admin'){
                     echo '<li><a href="admin/productos.php">Administrar productos</a></li>';
                }
            }
            ?>
            
        </ul>
    </nav>
</header>