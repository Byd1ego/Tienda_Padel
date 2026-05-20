<?php
// Carga cabecera y conexión
require_once '../includes/header_admin.php';
require_once '../includes/conexion.php';

// Si se pulsa borrar, elimina ese pedido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['borrar'])) {
    $sql = "DELETE FROM pedido WHERE id = :id";
    $stmt = $conexion->prepare($sql);
    $stmt->bindValue(':id', $_POST['borrar'], PDO::PARAM_INT);
    $stmt->execute();
    header("Location: pedidos.php");
    exit();
}

// Saca todos los pedidos ordenados por fecha
$sql = "SELECT * FROM pedido ORDER BY fecha DESC";
$stmt = $conexion->prepare($sql);
$stmt->execute();
$pedidos = $stmt->fetchAll();
?>

<div class="admin-contenedor">
    <h1 class="admin-titulo">Pedidos</h1>

    <?php if (count($pedidos) > 0): ?>

        <!-- VISTA MÓVIL: tarjetas -->
        <div class="productos-cards">
            <?php foreach ($pedidos as $p): ?>
                <div class="producto-card-admin">
                    <div class="producto-card-img">
                        <?php if ($p['imagen']): ?>
                            <img src="../static/img/<?php echo htmlspecialchars($p['imagen']); ?>">
                        <?php else: ?>
                            Sin imagen
                        <?php endif; ?>
                    </div>
                    <div class="producto-card-body">
                        <p><strong><?php echo htmlspecialchars($p['nombre_producto']); ?></strong></p>
                        <p><span class="card-label">Usuario:</span> <?php echo htmlspecialchars($p['usuario']); ?></p>
                        <p><span class="card-label">Unidades:</span> <?php echo $p['unidades']; ?></p>
                        <p><span class="card-label">Total:</span> <?php echo number_format($p['pvp'] * $p['unidades'], 2, ',', '.'); ?>€</p>
                        <p><span class="card-label">Fecha:</span> <?php echo $p['fecha']; ?></p>
                        <form method="post" onsubmit="return confirm('¿Borrar pedido?')">
                            <input type="hidden" name="borrar" value="<?php echo $p['id']; ?>">
                            <a href="pedido_borrar.php?id=<?php echo $p['id']; ?>" class="boton-borrar">Borrar</a>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- VISTA ESCRITORIO: tabla -->
        <div class="tabla-wrapper">
            <table class="tabla-admin">
                <tr>
                    <th>Imagen</th>
                    <th>ID</th>
                    <th>Producto</th>
                    <th>Usuario</th>
                    <th>Unidades</th>
                    <th>Total</th>
                    <th>Fecha</th>
                    <th>Acción</th>
                </tr>
                <?php foreach ($pedidos as $p): ?>
                    <tr>
                        <td>
                            <?php if ($p['imagen']): ?>
                                <img src="../static/img/<?php echo htmlspecialchars($p['imagen']); ?>" style="max-width:60px; border-radius:6px;">
                            <?php else: ?>
                                Sin imagen
                            <?php endif; ?>
                        </td>
                        <td><?php echo $p['id']; ?></td>
                        <td><?php echo htmlspecialchars($p['nombre_producto']); ?></td>
                        <td><?php echo htmlspecialchars($p['usuario']); ?></td>
                        <td><?php echo $p['unidades']; ?></td>
                        <td class="precio-admin"><?php echo number_format($p['pvp'] * $p['unidades'], 2, ',', '.'); ?>€</td>
                        <td><?php echo $p['fecha']; ?></td>
                        <td>
                            <form method="post" onsubmit="return confirm('¿Borrar pedido?')">
                                <input type="hidden" name="borrar" value="<?php echo $p['id']; ?>">
                                <a href="pedido_borrar.php?id=<?php echo $p['id']; ?>" class="boton-borrar">Borrar</a>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

    <?php else: ?>
        <p>No hay pedidos.</p>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer_admin.php'; ?>