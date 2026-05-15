<?php 
// Carga la cabecera, la conexión y las funciones del panel de administración
require_once '../includes/header_admin.php';
require_once '../includes/conexion.php';
require_once '../includes/funciones.php';

// Recoge el filtro de nivel si viene por URL y es un valor válido
// Si no viene o no es válido, queda vacío y se muestran todos los productos
$nivel = isset($_GET['nivel']) && in_array($_GET['nivel'], ['principiante', 'intermedio', 'avanzado']) ? $_GET['nivel'] : '';

// Si hay filtro añade WHERE, si no trae todos los productos
$sql  = "SELECT cod, nombre_corto, descripcion, marca, nivel, forma, peso, pvp, exclusiva, imagen FROM producto" . ($nivel ? " WHERE nivel = :nivel" : "");
$stmt = $conexion->prepare($sql);
if ($nivel) $stmt->bindValue(':nivel', $nivel);
$stmt->execute();
$productos = $stmt->fetchAll();
?>

<div class="admin-contenedor">
    <h1 class="admin-titulo">Administración de productos</h1>

    <!-- Formulario de filtro: al cambiar el select envía el formulario automáticamente -->
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
        <!-- El wrapper permite scroll horizontal sin afectar header ni footer -->
        <div class="tabla-wrapper">
            <table class='tabla-admin'>
                <tr>
                    <th>Imagen</th><th>Código</th><th>Nombre</th><th>Descripción</th>
                    <th>Marca</th><th>Nivel</th><th>Forma</th><th>Peso</th>
                    <th>PVP</th><th>Exclusiva</th><th>Stock</th><th>Acciones</th>
                </tr>
                <?php foreach ($productos as $p): ?>
                    <?php
                    // Consulta el stock de cada producto en la tienda 1
                    $s = $conexion->prepare("SELECT unidades FROM stock WHERE producto = :cod AND tienda = 1");
                    $s->bindValue(':cod', $p['cod']);
                    $s->execute();
                    $stock    = $s->fetch();

                    // Si no hay registro de stock se muestra 0
                    $unidades = $stock ? $stock['unidades'] : 0;
                    ?>
                    <tr>
                        <!-- Muestra la imagen del producto o un texto si no tiene -->
                        <td><?php echo $p['imagen'] ? "<img src='../static/img/" . htmlspecialchars($p['imagen']) . "' style='max-width:60px; border-radius:6px;'>" : 'Sin imagen'; ?></td>
                        <td><?php echo $p['cod']; ?></td>
                        <td><?php echo $p['nombre_corto']; ?></td>
                        <td><?php echo $p['descripcion']; ?></td>
                        <td><?php echo $p['marca']; ?></td>
                        <td><?php echo $p['nivel']; ?></td>
                        <td><?php echo $p['forma']; ?></td>
                        <td><?php echo $p['peso']; ?> g</td>
                        <!-- Formatea el precio con 2 decimales -->
                        <td class='precio-admin'><?php echo number_format($p['pvp'], 2); ?> €</td>
                        <td><?php echo $p['exclusiva'] ? 'Sí' : 'No'; ?></td>
                        <td><?php echo $unidades; ?> ud</td>
                        <!-- Botones para editar o borrar el producto -->
                        <td class='acciones-admin'>
                            <a class='boton-editar' href='producto_editar.php?cod=<?php echo $p['cod']; ?>'>Editar</a>
                            <a class='boton-borrar' href='producto_borrar.php?cod=<?php echo $p['cod']; ?>'>Borrar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    <?php else: ?>
        <p>No hay productos para mostrar.</p>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer_admin.php'; ?>