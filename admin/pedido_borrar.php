<?php
// Carga cabecera y conexión
require_once '../includes/header_admin.php';
require_once '../includes/conexion.php';

// Si no llega el id por URL, para
if (!isset($_GET['id'])) {
    header("Location: pedidos.php");
    exit();
}

$id = $_GET['id'];

// Si confirma el borrado, elimina el pedido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "DELETE FROM pedido WHERE id_pedido = :id";
    $stmt = $conexion->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    header("Location: pedidos.php");
    exit();
}

// Saca los datos del pedido para mostrarlos en el mensaje
$sql = "SELECT * FROM pedido WHERE id_pedido = :id";
$stmt = $conexion->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$pedido = $stmt->fetch();

if (!$pedido) {
    header("Location: pedidos.php");
    exit();
}
?>

<div class="borrar-contenedor">
    <p class="borrar-mensaje">
        ¿Seguro que quieres borrar el pedido de <strong><?php echo htmlspecialchars($pedido['nombre_producto']); ?></strong>
        del usuario <strong><?php echo htmlspecialchars($pedido['usuario']); ?></strong>?
    </p>
    <div class="borrar-botones">
        <a href="pedidos.php" class="boton-nuevo">Cancelar</a>
        <form method="post">
            <button type="submit" class="boton-borrar">Borrar</button>
        </form>
    </div>
</div>

<?php require_once '../includes/footer_admin.php'; ?>