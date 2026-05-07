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
    <title>Tienda - Borrar producto</title>
    <link rel="stylesheet" href="../static/css/estilos.css">    
</head>
<body>

<?php
require_once '../includes/conexion.php';

if (!isset($_GET['cod'])) {
    die("Código de producto no especificado.");
}

$cod = $_GET['cod'];

$sql = "SELECT cod, nombre_corto FROM producto WHERE cod = :cod";
$stmt = $conexion->prepare($sql);
$stmt->bindValue(':cod', $cod);
$stmt->execute();
$producto = $stmt->fetch();

if (!$producto) {
    die("Producto no encontrado.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $sql_stock = "DELETE FROM stock WHERE producto = :cod";
        $stmt_stock = $conexion->prepare($sql_stock);
        $stmt_stock->bindValue(':cod', $cod);
        $stmt_stock->execute();

        $sql = "DELETE FROM producto WHERE cod = :cod";
        $stmt = $conexion->prepare($sql);
        $stmt->bindValue(':cod', $cod);
        $stmt->execute();

        header("Location: productos.php");
        exit();
    } catch (Exception $e) {
        echo "<p style='color:red'>Error al borrar el producto.</p>";
    }
}
?>

<div class="borrar-contenedor">
    <h1>🗑️ Borrar producto</h1>
    <div class="borrar-mensaje">
        ¿Estás seguro de que deseas borrar el producto <strong><?php echo htmlspecialchars($producto['nombre_corto']); ?></strong> (código: <?php echo htmlspecialchars($producto['cod']); ?>)?
    </div>
    <form method="post">
        <div class="borrar-botones">
            <a href="productos.php" class="boton-tienda">Cancelar</a>
            <button type="submit" class="boton-borrar">Borrar</button>
        </div>
    </form>
</div>

</body>
</html>