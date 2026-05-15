<?php
include_once 'includes/header.php';
require_once 'includes/conexion.php';

$cod = $_GET['cod'] ?? '';

if (!$cod) {
    header("Location: index.php");
    exit();
}

$sql  = "SELECT * FROM producto WHERE cod = :cod";
$stmt = $conexion->prepare($sql);
$stmt->bindValue(':cod', $cod);
$stmt->execute();
$p = $stmt->fetch();

if (!$p) {
    header("Location: index.php");
    exit();
}

$sql_stock = "SELECT unidades FROM stock WHERE producto = :cod AND tienda = 1";
$stmt_stock = $conexion->prepare($sql_stock);
$stmt_stock->bindValue(':cod', $cod);
$stmt_stock->execute();
$stock    = $stmt_stock->fetch();
$unidades = $stock ? $stock['unidades'] : 0;
?>

<main>
    <div class="producto-detalle">

        <div class="producto-imagen">
            <?php if ($p['imagen']): ?>
                <img src="static/img/<?php echo htmlspecialchars($p['imagen']); ?>" alt="<?php echo htmlspecialchars($p['nombre_corto']); ?>">
            <?php else: ?>
                <img src="static/img/default.jpg" alt="Sin imagen">
            <?php endif; ?>
        </div>

        <div class="producto-info">
            <h1 class="producto-titulo"><?php echo htmlspecialchars($p['nombre']); ?></h1>
            <p class="producto-precio"><?php echo number_format($p['pvp'], 2, ',', '.'); ?>€</p>
            <p class="producto-desc"><?php echo htmlspecialchars($p['descripcion']); ?></p>

            <div class="producto-specs">
                <div class="spec">
                    <span class="spec-label">Marca</span>
                    <span class="spec-valor"><?php echo htmlspecialchars($p['marca']); ?></span>
                </div>
                <div class="spec">
                    <span class="spec-label">Nivel</span>
                    <span class="spec-valor"><?php echo $p['nivel']; ?></span>
                </div>
                <div class="spec">
                    <span class="spec-label">Forma</span>
                    <span class="spec-valor"><?php echo $p['forma']; ?></span>
                </div>
                <div class="spec">
                    <span class="spec-label">Peso</span>
                    <span class="spec-valor"><?php echo $p['peso']; ?> g</span>
                </div>
                <div class="spec">
                    <span class="spec-label">Stock</span>
                    <span class="spec-valor"><?php echo $unidades; ?> unidades</span>
                </div>
            </div>

            <?php if (isset($_SESSION['usuario']) && $_SESSION['rol'] === 'usuario'): ?>
                <form method="post" action="añadir_carrito.php" style="display:flex; align-items:center; gap:8px; margin-top:20px;">
                    <input type="hidden" name="cod" value="<?php echo htmlspecialchars($p['cod']); ?>">
                    <input type="hidden" name="origen" value="producto.php?cod=<?php echo htmlspecialchars($p['cod']); ?>">
                    <?php if ($unidades > 0): ?>
                        <!-- Hay stock: muestra input de cantidad y botón -->
                        <input type="number" name="cantidad" min="1" max="<?php echo $unidades; ?>" value="1" style="width:60px; padding:8px; border-radius:6px; border:1px solid #ccc;">
                        <button type="submit" class="boton-carrito" style="max-width:300px;">Añadir al carrito</button>
                    <?php else: ?>
                        <!-- Sin stock: botón desactivado -->
                        <button class="boton-carrito" disabled style="background-color:#aaa; cursor:not-allowed; max-width:300px;">Sin stock</button>
                    <?php endif; ?>
                </form>
            <?php elseif (!isset($_SESSION['usuario'])): ?>
                <a href="login.php" class="boton-nuevo" style="display:inline-block; margin-top:20px;">Inicia sesión para comprar</a>
            <?php endif; ?>

            <a href="javascript:history.back()" class="boton-tienda" style="display:inline-block; margin-top:12px;">← Volver</a>
        </div>

    </div>
</main>

<?php include_once 'includes/footer.php'; ?>