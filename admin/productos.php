<?php 
require_once '../includes/header_admin.php';
require_once '../includes/conexion.php';
require_once '../includes/funciones.php';

// Filtro por nivel
$nivel = isset($_GET['nivel']) && in_array($_GET['nivel'], ['principiante', 'intermedio', 'avanzado']) ? $_GET['nivel'] : '';

// Productos
$sql  = "SELECT cod, nombre_corto, descripcion, marca, nivel, forma, peso, pvp, exclusiva, imagen FROM producto" . ($nivel ? " WHERE nivel = :nivel" : "");
$stmt = $conexion->prepare($sql);
if ($nivel) $stmt->bindValue(':nivel', $nivel);
$stmt->execute();
$productos = $stmt->fetchAll();
?>

<div class="admin-contenedor">
    <h1 class="admin-titulo">Administración de productos</h1>

    <form method="get" class="filtro-container">
        <label>Filtrar por nivel:</label>
        <select name="nivel" onchange="this.form.submit()">
            <option value="">Todos</option>
            <option value="principiante" <?php echo $nivel === 'principiante' ? 'selected' : ''; ?>>Principiante</option>
            <option value="intermedio"   <?php echo $nivel === 'intermedio'   ? 'selected' : ''; ?>>Intermedio</option>
            <option value="avanzado"     <?php echo $nivel === 'avanzado'     ? 'selected' : ''; ?>>Avanzado</option>
        </select>
    </form>

    <?php if (count($productos) > 0): ?>
        <table class='tabla-admin'>
            <tr>
                <th>Imagen</th><th>Código</th><th>Nombre</th><th>Descripción</th>
                <th>Marca</th><th>Nivel</th><th>Forma</th><th>Peso</th>
                <th>PVP</th><th>Exclusiva</th><th>Stock</th><th>Acciones</th>
            </tr>
            <?php foreach ($productos as $p): ?>
                <?php
                $s = $conexion->prepare("SELECT unidades FROM stock WHERE producto = :cod AND tienda = 1");
                $s->bindValue(':cod', $p['cod']);
                $s->execute();
                $stock    = $s->fetch();
                $unidades = $stock ? $stock['unidades'] : 0;
                ?>
                <tr>
                    <td><?php echo $p['imagen'] ? "<img src='../static/img/" . htmlspecialchars($p['imagen']) . "' style='max-width:60px; border-radius:6px;'>" : 'Sin imagen'; ?></td>
                    <td><?php echo $p['cod']; ?></td>
                    <td><?php echo $p['nombre_corto']; ?></td>
                    <td><?php echo $p['descripcion']; ?></td>
                    <td><?php echo $p['marca']; ?></td>
                    <td><?php echo $p['nivel']; ?></td>
                    <td><?php echo $p['forma']; ?></td>
                    <td><?php echo $p['peso']; ?> g</td>
                    <td class='precio-admin'><?php echo number_format($p['pvp'], 2); ?> €</td>
                    <td><?php echo $p['exclusiva'] ? 'Sí' : 'No'; ?></td>
                    <td><?php echo $unidades; ?> ud</td>
                    <td class='acciones-admin'>
                        <a class='boton-editar' href='producto_editar.php?cod=<?php echo $p['cod']; ?>'>Editar</a>
                        <a class='boton-borrar' href='producto_borrar.php?cod=<?php echo $p['cod']; ?>'>Borrar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No hay productos para mostrar.</p>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer_admin.php'; ?>