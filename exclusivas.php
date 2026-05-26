<?php
// Carga la cabecera y con ella inicia la sesión
include_once 'static/header.php';

// Solo los usuarios logueados pueden ver las exclusivas
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php?redirigido=true");
    exit();
}
?>

<main>
    <div class="banner-completo">
        <div class="banner-imagen-estatica">
            <img src="includes/img/exclusivas.jpg" alt="Palas exclusivas">
        </div>
        <div class="banner-texto">
            <span class="banner-tag">Tienda de pádel</span>
            <h1 class="banner-titulo">Palas Exclusivas</h1>
            <p class="banner-desc">Descubre las palas más exclusivas de esta temporada</p>
            
        </div>
    </div>

    <?php
    // Carga la conexión y obtiene solo los productos marcados como exclusivos
    require_once 'static/conexion.php';
    $sql = "SELECT cod_producto, nombre_corto, pvp, imagen FROM producto WHERE exclusiva = TRUE";
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $exclusivas = $stmt->fetchAll();
    ?>

    <div class="contenedorgrid">
        <?php foreach ($exclusivas as $p): ?>
            <div class="card">
                <a href="producto.php?cod=<?php echo htmlspecialchars($p['cod_producto']); ?>">
                    <?php if ($p['imagen']): ?>
                        <img src="includes/img/<?php echo htmlspecialchars($p['imagen']); ?>" alt="<?php echo htmlspecialchars($p['nombre_corto']); ?>">
                    <?php else: ?>
                        <img src="includes/img/default.jpg" alt="Sin imagen">
                    <?php endif; ?>
                    <p><?php echo htmlspecialchars($p['nombre_corto']); ?> <br>
                        <?php echo number_format($p['pvp'], 2, ',', '.'); ?>€</p>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<?php include_once 'static/footer.php'; ?>