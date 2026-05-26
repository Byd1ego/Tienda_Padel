<?php
// Carga la cabecera y con ella inicia la sesión
include_once 'includes/header.php';
require_once 'includes/conexion.php';

// Si el usuario ha seleccionado cuántos productos ver, guarda su preferencia en una cookie de 30 días
if (isset($_GET['por_pagina'])) {
    $productosPorPagina = (int) $_GET['por_pagina'];
    setcookie('productos_por_pagina', $productosPorPagina, time() + 30 * 24 * 60 * 60, '/');
} else {
    // Si no hay preferencia en la URL, la lee de la cookie. Si tampoco existe, muestra 6 por defecto
    $productosPorPagina = isset($_COOKIE['productos_por_pagina']) ? (int) $_COOKIE['productos_por_pagina'] : 6;
}

// Calcula en qué producto empieza la página actual
$pagina = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
$inicio = ($pagina - 1) * $productosPorPagina;

// Cuenta el total de productos no exclusivos para calcular el número de páginas
$stmtTotal = $conexion->prepare("SELECT COUNT(*) AS total FROM producto WHERE exclusiva = FALSE");
$stmtTotal->execute();
$totalProductos = $stmtTotal->fetch()['total'];
$totalPaginas = ceil($totalProductos / $productosPorPagina);

// Obtiene solo los productos de la página actual usando LIMIT
$stmt = $conexion->prepare("SELECT cod_producto, nombre_corto, pvp, imagen FROM producto WHERE exclusiva = FALSE LIMIT :inicio, :por_pagina");
$stmt->bindValue(':inicio', $inicio, PDO::PARAM_INT);
$stmt->bindValue(':por_pagina', $productosPorPagina, PDO::PARAM_INT);
$stmt->execute();
$ofertas = $stmt->fetchAll();
?>
<?php if (isset($_GET['acceso_denegado'])): ?>
    <p class="alerta" style="text-align:center; margin: 10px 0;">
        No tienes permisos para acceder a esa página.
    </p>
<?php endif; ?>
<main>
    <div class="banner-completo">
        <div id="slideshow">
            <img src="static/img/coello.jpg" alt="" class="slide">
            <img src="static/img/Almejorprecio.jpg" alt="" class="slide">
            <img src="static/img/PROMOCION.jpg" alt="" class="slide">
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
    
    

    <div class="contenedorgrid">
        <?php foreach ($ofertas as $p): ?>
            <div class="card">
                <a href="producto.php?cod=<?php echo htmlspecialchars($p['cod_producto']); ?>">
                    <?php if ($p['imagen']): ?>
                        <img src="static/img/<?php echo htmlspecialchars($p['imagen']); ?>" alt="<?php echo htmlspecialchars($p['nombre_corto']); ?>">
                    <?php else: ?>
                        <img src="static/img/default.jpg" alt="Sin imagen">
                    <?php endif; ?>
                    <p><?php echo htmlspecialchars($p['nombre_corto']); ?> <br>
                        <?php echo number_format($p['pvp'], 2, ',', '.'); ?>€</p>
                </a>
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

    // Cogemos todas las imágenes del slideshow
    var slides = $('.slide');

    // Guardamos cuál es la imagen actual (empezamos por la primera)
    var actual = 0;

    // Ocultamos todas las imágenes menos la primera
    slides.hide();
    slides.eq(0).show();

    // Cada 3 segundos cambiamos de imagen
    setInterval(function () {

        // Ocultamos la imagen actual con efecto fadeOut
        slides.eq(actual).fadeOut(500);

        // Pasamos a la siguiente (si llegamos al final volvemos a la primera)
        if (actual < slides.length - 1) {
            actual = actual + 1;
        } else {
            actual = 0;
        }

        // Mostramos la nueva imagen con efecto fadeIn
        slides.eq(actual).fadeIn(500);

    }, 3000);

});
</script>

<?php include_once 'includes/footer.php'; ?>