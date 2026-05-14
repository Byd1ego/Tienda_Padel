<?php 
// Carga la cabecera del panel de administración
require_once '../includes/header_admin.php';

// Carga la conexión a la base de datos
require_once '../includes/conexion.php';
?>

<?php
// Si no se recibe el código del producto por URL, detiene la ejecución
if (!isset($_GET['cod'])) {
    die("Código de producto no especificado.");
}

$cod = $_GET['cod'];

// Busca el producto en la base de datos para mostrar su nombre en la confirmación
$sql = "SELECT cod, nombre_corto FROM producto WHERE cod = :cod";
$stmt = $conexion->prepare($sql);
$stmt->bindValue(':cod', $cod);
$stmt->execute();
$producto = $stmt->fetch();

// Si no existe el producto, detiene la ejecución
if (!$producto) {
    die("Producto no encontrado.");
}

// Si el usuario confirma el borrado (pulsa el botón del formulario)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Primero borra el stock del producto (necesario por la clave foránea)
        $sql_stock = "DELETE FROM stock WHERE producto = :cod";
        $stmt_stock = $conexion->prepare($sql_stock);
        $stmt_stock->bindValue(':cod', $cod);
        $stmt_stock->execute();

        // Luego borra el propio producto
        $sql = "DELETE FROM producto WHERE cod = :cod";
        $stmt = $conexion->prepare($sql);
        $stmt->bindValue(':cod', $cod);
        $stmt->execute();

        // Redirige al listado de productos tras borrar
        header("Location: productos.php");
        exit();
    } catch (Exception $e) {
        echo "<p style='color:red'>Error al borrar el producto.</p>";
    }
}
?>

<div class="borrar-contenedor">
    <h1>Borrar producto</h1>

    <!-- Mensaje de confirmación con el nombre y código del producto -->
    <div class="borrar-mensaje">
        ¿Estás seguro de que deseas borrar el producto 
        <strong><?php echo htmlspecialchars($producto['nombre_corto']); ?></strong> 
        (código: <?php echo htmlspecialchars($producto['cod']); ?>)?
    </div>

    <!-- Formulario con dos opciones: cancelar o confirmar el borrado -->
    <form method="post">
        <div class="borrar-botones">
            <a href="productos.php" class="boton-tienda">Cancelar</a>
            <button type="submit" class="boton-borrar">Borrar</button>
        </div>
    </form>
</div>

<?php require_once '../includes/footer_admin.php'; ?>