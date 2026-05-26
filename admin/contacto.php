<?php
// Carga cabecera y conexión
require_once '../static/header_admin.php';
require_once '../static/conexion.php';

// Si se pulsa borrar, elimina el mensaje
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['borrar'])) {
    $sql = "DELETE FROM contacto WHERE id_contacto = :id";
    $stmt = $conexion->prepare($sql);
    $stmt->bindValue(':id', $_POST['borrar'], PDO::PARAM_INT);
    $stmt->execute();
    header("Location: contacto.php");
    exit();
}

// Saca todos los mensajes ordenados por fecha
$sql = "SELECT * FROM contacto ORDER BY fecha DESC";
$stmt = $conexion->prepare($sql);
$stmt->execute();
$mensajes = $stmt->fetchAll();
?>

<div class="admin-contenedor">
    <h1 class="admin-titulo">Mensajes de contacto</h1>

    <?php if (count($mensajes) > 0): ?>

        <!-- VISTA MÓVIL: tarjetas -->
        <div class="productos-cards">
            <?php foreach ($mensajes as $m): ?>
                <div class="producto-card-admin">
                    <div class="producto-card-body">
                        <p><strong><?php echo htmlspecialchars($m['nombre']); ?></strong></p>
                        <p><span class="card-label">Email:</span>
                            <!-- Enlace mailto para abrir el correo directamente -->
                            <a href="mailto:<?php echo htmlspecialchars($m['email']); ?>">
                                <?php echo htmlspecialchars($m['email']); ?>
                            </a>
                        </p>
                        <p><span class="card-label">Teléfono:</span> <?php echo htmlspecialchars($m['telefono']); ?></p>
                        <p><span class="card-label">Mensaje:</span> <?php echo htmlspecialchars($m['mensaje']); ?></p>
                        <p><span class="card-label">Fecha:</span> <?php echo $m['fecha']; ?></p>
                        <form method="post" style="margin-top:8px;">
                            <input type="hidden" name="borrar" value="<?php echo $m['id_contacto']; ?>">
                            <button type="submit" class="boton-borrar">Borrar</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- VISTA ESCRITORIO: tabla -->
        <div class="tabla-wrapper">
            <table class="tabla-admin">
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Mensaje</th>
                    <th>Fecha</th>
                    <th>Borrar</th>
                </tr>
                <?php foreach ($mensajes as $m): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($m['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($m['email']); ?></td>
                        <td><?php echo htmlspecialchars($m['telefono']); ?></td>
                        <td><?php echo htmlspecialchars($m['mensaje']); ?></td>
                        <td><?php echo $m['fecha']; ?></td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="borrar" value="<?php echo $m['id_contacto']; ?>">
                                <button type="submit" class="boton-borrar">Borrar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

    <?php else: ?>
        <p>No hay mensajes.</p>
    <?php endif; ?>
</div>

<?php require_once '../static/footer_admin.php'; ?>