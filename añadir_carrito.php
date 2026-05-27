<?php
// Inicia la sesión para poder leer los datos del usuario logueado
session_start();

// Si el usuario no está logueado o no es de tipo usuario, lo manda al login
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'usuario') {
header("Location: index.php?acceso_denegado=1");
    exit();
}

// Carga la conexión a la base de datos
require_once 'static/conexion.php';

// Recoge el código del producto y el usuario de la sesión
$cod     = $_POST['cod']     ?? '';
$usuario = $_SESSION['usuario'];

if ($cod) {
    // Recoge la cantidad que quiere añadir, mínimo 1
    $cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 1;

    // Consulta el stock disponible
    $sql_stock = "SELECT unidades FROM stock WHERE cod_producto = :cod AND cod_tienda = 1";
    $stmt_stock = $conexion->prepare($sql_stock);
    $stmt_stock->bindValue(':cod', $cod);
    $stmt_stock->execute();
    $stock = $stmt_stock->fetch();
    $stock_disponible = $stock ? $stock['unidades'] : 0;

    // Comprueba cuántas unidades tiene ya en el carrito
    $sql = "SELECT id_carrito, unidades FROM carrito WHERE usuario = :usuario AND cod_producto = :cod";
    $stmt = $conexion->prepare($sql);
    $stmt->bindValue(':usuario', $usuario);
    $stmt->bindValue(':cod',     $cod);
    $stmt->execute();
    $existe = $stmt->fetch();

    $en_carrito = $existe ? $existe['unidades'] : 0;

    // Calcula cuántas se pueden añadir sin superar el stock
    $puede_añadir = min($cantidad, $stock_disponible - $en_carrito);

    if ($puede_añadir > 0) {
        if ($existe) {
            $sql = "UPDATE carrito SET unidades = unidades + :cantidad WHERE id_carrito = :id";
            $stmt = $conexion->prepare($sql);
            $stmt->bindValue(':cantidad', $puede_añadir, PDO::PARAM_INT);
            $stmt->bindValue(':id', $existe['id_carrito'], PDO::PARAM_INT);
            $stmt->execute();
        } else {
            $sql = "INSERT INTO carrito (usuario, cod_producto, unidades) VALUES (:usuario, :cod, :cantidad)";
            $stmt = $conexion->prepare($sql);
            $stmt->bindValue(':usuario', $usuario);
            $stmt->bindValue(':cod',     $cod);
            $stmt->bindValue(':cantidad', $puede_añadir, PDO::PARAM_INT);
            $stmt->execute();
        }
    }
}

// Redirige a la página de origen (index o exclusivas) tras añadir al carrito
$origen = $_POST['origen'] ?? 'index.php';
header("Location: " . $origen);
exit();