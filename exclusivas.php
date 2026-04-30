<?php
include_once 'includes/header.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php?redirigido=true");
    exit();
}
?>

<main>
    <h2>Exclusivas</h2>

    <?php
    require_once 'includes/conexion.php';

    $sql  = "SELECT nombre_corto, pvp, imagen FROM producto WHERE exclusiva = TRUE";
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $exclusivas = $stmt->fetchAll();
    ?>

    <div class="contenedorgrid">
        <?php foreach ($exclusivas as $p): ?>
            <div class="card">
                <?php if ($p['imagen']): ?>
                    <img src="static/img/<?php echo htmlspecialchars($p['imagen']); ?>" alt="<?php echo htmlspecialchars($p['nombre_corto']); ?>" height="300px" width="300px">
                <?php else: ?>
                    <img src="static/img/default.jpg" alt="Sin imagen" height="300px" width="300px">
                <?php endif; ?>
                <p><?php echo htmlspecialchars($p['nombre_corto']); ?> <br> <?php echo number_format($p['pvp'], 2, ',', '.'); ?>€</p>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<?php include_once 'includes/footer.php'; ?>