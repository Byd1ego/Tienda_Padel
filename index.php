<?php
// Carga la cabecera y con ella inicia la sesión
include_once 'includes/header.php';
require_once 'includes/conexion.php';

// Si el usuario ha seleccionado cuántos productos ver, guarda su preferencia en una cookie de 30 días
if (isset($_GET['por_pagina'])) {
    $productosPorPagina = (int)$_GET['por_pagina'];
    setcookie('productos_por_pagina', $productosPorPagina, time() + 30*24*60*60, '/');
} else {
    // Si no hay preferencia en la URL, la lee de la cookie. Si tampoco existe, muestra 6 por defecto
    $productosPorPagina = isset($_COOKIE['productos_por_pagina']) ? (int)$_COOKIE['productos_por_pagina'] : 6;
}

// Calcula en qué producto empieza la página actual
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$inicio = ($pagina - 1) * $productosPorPagina;

// Cuenta el total de productos no exclusivos para calcular el número de páginas
$stmtTotal = $conexion->prepare("SELECT COUNT(*) AS total FROM producto WHERE exclusiva = FALSE");
$stmtTotal->execute();
$totalProductos = $stmtTotal->fetch()['total'];
$totalPaginas   = ceil($totalProductos / $productosPorPagina);

// Obtiene solo los productos de la página actual usando LIMIT
$stmt = $conexion->prepare("SELECT cod, nombre_corto, pvp, imagen FROM producto WHERE exclusiva = FALSE LIMIT :inicio, :por_pagina");
$stmt->bindValue(':inicio',     $inicio,             PDO::PARAM_INT);
$stmt->bindValue(':por_pagina', $productosPorPagina, PDO::PARAM_INT);
$stmt->execute();
$ofertas = $stmt->fetchAll();
?>

<main>
    <div class="banner-grid">
        <div class="banner-imagen">
            <img src="static/img/Almejorprecio.png" alt="Empieza a jugar al mejor precio">
        </div>
        <div class="banner-texto">
            <span class="banner-tag">Tienda de pádel</span>
            <h1 class="banner-titulo">Palas más vendidas</h1>
            <p class="banner-desc">Descubre nuestra selección de palas para todos los niveles. Calidad profesional al mejor precio.</p>
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

    <!-- Slideshow de palas que rota automáticamente cada 2 segundos -->
    <div id="slideshow">
        <img src="static/img/metalbone.jpg" alt="Pala">
        <img src="static/img/Adipower.jpg" alt="Pala">
        <img src="static/img/viper.jpg" alt="Pala">
        <img src="static/img/conqueror.jpg" alt="Pala">
        <img src="static/img/ml10.jpg" alt="Pala">
        <img src="static/img/explorer.jpg" alt="Pala">
    </div>

    <!-- Grid con las cards de los productos de la página actual -->
    <div class="contenedorgrid">
        <?php foreach ($ofertas as $p): ?>
            <div class="card">
                <!-- Muestra la imagen del producto o una por defecto si no tiene -->
                <?php if ($p['imagen']): ?>
                    <img src="static/img/<?php echo htmlspecialchars($p['imagen']); ?>"
                        alt="<?php echo htmlspecialchars($p['nombre_corto']); ?>">
                <?php else: ?>
                    <img src="static/img/default.jpg" alt="Sin imagen">
                <?php endif; ?>
                <p><?php echo htmlspecialchars($p['nombre_corto']); ?> <br>
                    <?php echo number_format($p['pvp'], 2, ',', '.'); ?>€</p>
                <!-- El botón de añadir al carrito solo aparece si el usuario está logueado -->
                <?php if (isset($_SESSION['usuario']) && $_SESSION['rol'] === 'usuario'): ?>
                    <form method="post" action="añadir_carrito.php">
                        <input type="hidden" name="cod" value="<?php echo htmlspecialchars($p['cod']); ?>">
                        <input type="hidden" name="origen" value="index.php">
                        <button type="submit" class="boton-carrito">Añadir al carrito</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Selector de productos por página y botones de paginación -->
    <div class="paginacion-container">

        <!-- Al cambiar el select envía el formulario automáticamente -->
        <form method="get" style="display:flex; align-items:center; gap:8px;">
            <label for="por_pagina">Mostrar</label>
            <select name="por_pagina" id="por_pagina" onchange="this.form.submit()">
                <option value="6"  <?php if ($productosPorPagina == 6)  echo 'selected'; ?>>6</option>
                <option value="12" <?php if ($productosPorPagina == 12) echo 'selected'; ?>>12</option>
                <option value="24" <?php if ($productosPorPagina == 24) echo 'selected'; ?>>24</option>
            </select>
            productos por página
        </form>

        <div class="paginacion">
            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                <!-- La página activa se muestra como span, las demás como enlaces -->
                <?php if ($i == $pagina): ?>
                    <span class="pag-boton pag-activo"><?php echo $i; ?></span>
                <?php else: ?>
                    <a href="index.php?pagina=<?php echo $i; ?>&por_pagina=<?php echo $productosPorPagina; ?>" class="pag-boton"><?php echo $i; ?></a>
                <?php endif; ?>
            <?php endfor; ?>
        </div>

    </div>

</main>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function () {
    var slides = $('#slideshow img');
    var actual = 0;

    // Oculta todas las imágenes menos la primera
    slides.hide().first().show();

    // Cada 2 segundos pasa a la siguiente imagen con efecto fadeOut/fadeIn
    setInterval(function () {
        slides.eq(actual).fadeOut(500);
        actual = (actual + 1) % slides.length;
        slides.eq(actual).fadeIn(500);
    }, 2000);
});
</script>

<?php include_once 'includes/footer.php'; ?>