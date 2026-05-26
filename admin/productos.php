<?php 
require_once '../static/header_admin.php';
require_once '../static/conexion.php';
require_once '../static/funciones.php';

$nivel = isset($_GET['nivel']) && in_array($_GET['nivel'], ['principiante', 'intermedio', 'avanzado']) ? $_GET['nivel'] : '';

// Si hay filtro añade WHERE, si no trae todos los productos
$sql  = "SELECT cod_producto, nombre_corto, descripcion, marca, nivel, forma, peso, pvp, exclusiva, imagen FROM producto" . ($nivel ? " WHERE nivel = :nivel" : "");
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

        <!-- VISTA MÓVIL: tarjetas -->
        <div class="productos-cards">
            <?php foreach ($productos as $p): ?>
                <?php
                // Consulta el stock de cada producto en la tienda 1
                $s = $conexion->prepare("SELECT unidades FROM stock WHERE cod_producto = :cod AND cod_tienda = 1");
                $s->bindValue(':cod', $p['cod_producto']);
                $s->execute();
                $stock    = $s->fetch();
                $unidades = $stock ? $stock['unidades'] : 0;
                ?>
                <div class="producto-card-admin">
                    <div class="producto-card-img">
                        <?php echo $p['imagen'] ? "<img src='../includes/img/" . htmlspecialchars($p['imagen']) . "'>" : 'Sin imagen'; ?>
                    </div>
                    <div class="producto-card-body">
                        <p><strong><?php echo $p['nombre_corto']; ?></strong></p>
                        <p><span class="card-label">Código:</span> <?php echo $p['cod_producto']; ?></p>
                        <p><span class="card-label">Marca:</span> <?php echo $p['marca']; ?></p>
                        <p><span class="card-label">Nivel:</span> <?php echo $p['nivel']; ?></p>
                        <p><span class="card-label">Forma:</span> <?php echo $p['forma']; ?></p>
                        <p><span class="card-label">Peso:</span> <?php echo $p['peso']; ?> g</p>
                        <p><span class="card-label">PVP:</span> <strong class="precio-admin"><?php echo number_format($p['pvp'], 2); ?> €</strong></p>
                        <p><span class="card-label">Exclusiva:</span> <?php echo $p['exclusiva'] ? 'Sí' : 'No'; ?></p>
                        <p><span class="card-label">Stock:</span> <?php echo $unidades; ?> ud</p>
                        <p><span class="card-label">Descripción:</span> <?php echo $p['descripcion']; ?></p>
                        <div class="acciones-admin" style="margin-top:10px;">
                            <a class='boton-editar' href='producto_editar.php?cod=<?php echo $p['cod_producto']; ?>'>Editar</a>
                            <a class='boton-borrar' href='producto_borrar.php?cod=<?php echo $p['cod_producto']; ?>'>Borrar</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- VISTA ESCRITORIO: tabla -->
        <div class="tabla-wrapper">
            <table class='tabla-admin'>
                <tr>
                    <th>Imagen</th><th>Código</th><th>Nombre</th><th>Descripción</th>
                    <th>Marca</th><th>Nivel</th><th>Forma</th><th>Peso</th>
                    <th>PVP</th><th>Exclusiva</th><th>Stock</th><th>Acciones</th>
                </tr>
                <?php foreach ($productos as $p): ?>
                    <?php
                    $s = $conexion->prepare("SELECT unidades FROM stock WHERE cod_producto = :cod AND cod_tienda = 1");
                    $s->bindValue(':cod', $p['cod_producto']);
                    $s->execute();
                    $stock    = $s->fetch();
                    $unidades = $stock ? $stock['unidades'] : 0;
                    ?>
                    <tr>
                        <td><?php echo $p['imagen'] ? "<img src='../includes/img/" . htmlspecialchars($p['imagen']) . "' style='max-width:60px; border-radius:6px;'>" : 'Sin imagen'; ?></td>
                        <td><?php echo $p['cod_producto']; ?></td>
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
                            <a class='boton-editar' href='producto_editar.php?cod=<?php echo $p['cod_producto']; ?>'>Editar</a>
                            <a class='boton-borrar' href='producto_borrar.php?cod=<?php echo $p['cod_producto']; ?>'>Borrar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

    <?php else: ?>
        <p>No hay productos para mostrar.</p>
    <?php endif; ?>
</div>

<?php require_once '../static/footer_admin.php'; ?>