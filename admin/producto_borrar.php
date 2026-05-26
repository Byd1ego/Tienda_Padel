<?php 
require_once '../includes/header_admin.php';
require_once '../includes/conexion.php';
?>

<?php
if (!isset($_GET['cod'])) {
    die("Código de producto no especificado.");
}

$cod = $_GET['cod'];

// Busca el producto para mostrar su nombre en la confirmación
$sql = "SELECT cod_producto, nombre_corto, imagen FROM producto WHERE cod_producto = :cod";
$stmt = $conexion->prepare($sql);
$stmt->bindValue(':cod', $cod);
$stmt->execute();
$producto = $stmt->fetch();

if (!$producto) {
    die("Producto no encontrado.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Borra la imagen de la carpeta si existe
        if ($producto['imagen']) {
            $ruta = '../includes/img/' . $producto['imagen'];
            if (file_exists($ruta)) {
                unlink($ruta);
            }
        }

        // Borra el stock del producto
        $sql_stock = "DELETE FROM stock WHERE cod_producto = :cod";
        $stmt_stock = $conexion->prepare($sql_stock);
        $stmt_stock->bindValue(':cod', $cod);
        $stmt_stock->execute();

        // Borra el producto
        $sql = "DELETE FROM producto WHERE cod_producto = :cod";
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
    <h1>Borrar producto</h1>
    <div class="borrar-mensaje">
        ¿Estás seguro de que deseas borrar el producto 
        <strong><?php echo htmlspecialchars($producto['nombre_corto']); ?></strong> 
        (código: <?php echo htmlspecialchars($producto['cod_producto']); ?>)?
    </div>
    <form method="post">
        <div class="borrar-botones">
            <a href="productos.php" class="boton-tienda">Cancelar</a>
            <button type="submit" class="boton-borrar">Borrar</button>
        </div>
    </form>
</div>

<?php require_once '../static/footer_admin.php'; ?>