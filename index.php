<?php
include_once 'includes/header.php';
?>

    <main>
    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. A, beatae. Porro culpa minima recusandae beatae
        ipsam pariatur voluptatem repellat dicta esse tenetur qui at mollitia nostrum provident, minus tempora
        molestias!
        Et quibusdam, eius autem minima corrupti cupiditate sint laboriosam, possimus alias voluptates nulla
        asperiores vero, quaerat temporibus! Quo aspernatur repudiandae necessitatibus, temporibus repellat
        nesciunt sequi voluptatibus maxime modi, tempora esse!
        Laudantium vitae cupiditate porro rerum, sunt nostrum exercitationem. Delectus, recusandae nisi quas
        odio optio provident porro distinctio quaerat deleniti. Quas quidem dignissimos ex expedita, cum
        veritatis dolorum quisquam impedit quis?</p>

    <h2>Algunos de nuestros productos</h2>

    <?php
    require_once 'includes/conexion.php';

    $sql = "SELECT nombre_corto, pvp, imagen FROM producto WHERE oferta = FALSE";
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $ofertas = $stmt->fetchAll();
    ?>

    <div class="contenedorgrid">
        <?php foreach ($ofertas as $p): ?>
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

<?php
include_once 'includes/footer.php';
?>

