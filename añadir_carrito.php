<?php
// Inicia la sesión para poder leer los datos del usuario logueado
session_start();

// Si el usuario no está logueado o no es de tipo usuario, lo manda al login
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'usuario') {
    header("Location: login.php?redirigido=true");
    exit();
}

// Carga la conexión a la base de datos
require_once 'includes/conexion.php';

// Recoge el código del producto y el usuario de la sesión
$cod     = $_POST['cod']     ?? '';
$usuario = $_SESSION['usuario'];

if ($cod) {
    // Comprueba si el producto ya está en el carrito del usuario
    $sql = "SELECT id, unidades FROM carrito WHERE usuario = :usuario AND producto = :cod";
    $stmt = $conexion->prepare($sql);
    $stmt->bindValue(':usuario', $usuario);
    $stmt->bindValue(':cod',     $cod);
    $stmt->execute();
    $existe = $stmt->fetch();

    if ($existe) {
        // Si ya existe, suma una unidad al producto en el carrito
        $sql = "UPDATE carrito SET unidades = unidades + 1 WHERE id = :id";
        $stmt = $conexion->prepare($sql);
        $stmt->bindValue(':id', $existe['id'], PDO::PARAM_INT);
        $stmt->execute();
    } else {
        // Si no existe, lo añade al carrito con 1 unidad
        $sql = "INSERT INTO carrito (usuario, producto, unidades) VALUES (:usuario, :cod, 1)";
        $stmt = $conexion->prepare($sql);
        $stmt->bindValue(':usuario', $usuario);
        $stmt->bindValue(':cod',     $cod);
        $stmt->execute();
    }
}

// Redirige a la página de origen (index o exclusivas) tras añadir al carrito
$origen = $_POST['origen'] ?? 'index.php';
header("Location: " . $origen);
exit();