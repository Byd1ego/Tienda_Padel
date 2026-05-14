<?php
// Carga la cabecera y con ella inicia la sesión
include_once 'includes/header.php';

// Solo los usuarios logueados pueden ver las exclusivas
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php?redirigido=true");
    exit();
}
?>

<main>
    <div class="banner-grid">
        <div class="banner-imagen">
            <img src="static/img/exclusivas.png" alt="Palas exclusivas">
        </div>
        <div class="banner-texto">
            <span class="banner-tag">Tienda de pádel</span>
            <h1 class="banner-titulo">Palas Exclusivas</h1>
            <p class="banner-desc">Descubre las palas mas exclusivas de esta temporada y cual se adapta mejor a tu juego.</p>
            <div class="banner-stats">
                <div class="banner-stat">
                    <strong>+20</strong>
                    <span>Modelos</span>
                </div>
                <div class="banner-stat">
                    <strong>3</strong>
                    <span>Niveles</span>
                </div>
                <div class="banner-stat">
                    <strong>6</strong>
                    <span>Marcas</span>
                </div>
            </div>
        </div>
    </div>

    <?php
    // Carga la conexión y obtiene solo los productos marcados como exclusivos
    require_once 'includes/conexion.php';
    $sql = "SELECT cod, nombre_corto, pvp, imagen FROM producto WHERE exclusiva = TRUE";
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $exclusivas = $stmt->fetchAll();
    ?>

    <div class="contenedorgrid">
        <?php foreach ($exclusivas as $p): ?>
            <div class="card">
                <!-- Muestra la imagen del producto o una por defecto si no tiene -->
                <?php if ($p['imagen']): ?>
                    <img src="static/img/<?php echo htmlspecialchars($p['imagen']); ?>"
                        alt="<?php echo htmlspecialchars($p['nombre_corto']); ?>" height="300px" width="300px">
                <?php else: ?>
                    <img src="static/img/default.jpg" alt="Sin imagen">
                <?php endif; ?>
                <p><?php echo htmlspecialchars($p['nombre_corto']); ?> <br>
                    <?php echo number_format($p['pvp'], 2, ',', '.'); ?>€</p>
                <!-- El botón de añadir al carrito solo aparece si el usuario está logueado -->
                <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'usuario'): ?>
                    <form method="post" action="añadir_carrito.php">
                        <input type="hidden" name="cod" value="<?php echo htmlspecialchars($p['cod']); ?>">
                        <input type="hidden" name="origen" value="exclusivas.php">
                        <button type="submit" class="boton-carrito">Añadir al carrito</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<?php include_once 'includes/footer.php'; ?>