<?php
// Carga la cabecera y con ella inicia la sesión
include_once 'includes/header.php';

// Si el usuario no está logueado o no es de tipo usuario, lo manda al login
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'usuario') {
    header("Location: login.php?redirigido=true");
    exit();
}

// Carga la conexión a la base de datos
require_once 'includes/conexion.php';

$usuario = $_SESSION['usuario'];

// Si se pulsa el botón borrar, elimina ese producto del carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['borrar'])) {
    $sql = "DELETE FROM carrito WHERE id = :id AND usuario = :usuario";
    $stmt = $conexion->prepare($sql);
    $stmt->bindValue(':id',      $_POST['borrar'], PDO::PARAM_INT);
    $stmt->bindValue(':usuario', $usuario);
    $stmt->execute();
    header("Location: carrito.php");
    exit();
}

// Si se pulsa el botón pagar, procesa la compra
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pagar'])) {

    // Obtiene todos los productos del carrito del usuario
    $sql = "SELECT * FROM carrito WHERE usuario = :usuario";
    $stmt = $conexion->prepare($sql);
    $stmt->bindValue(':usuario', $usuario);
    $stmt->execute();
    $items_pagar = $stmt->fetchAll();

    $error_stock = false;

    // Comprueba que hay stock suficiente para cada producto antes de cobrar
    foreach ($items_pagar as $item) {
        $sql_check = "SELECT unidades FROM stock WHERE producto = :cod AND tienda = 1";
        $stmt_check = $conexion->prepare($sql_check);
        $stmt_check->bindValue(':cod', $item['producto']);
        $stmt_check->execute();
        $stock = $stmt_check->fetch();

        // Si no hay stock suficiente, marca el error y para el bucle
        if (!$stock || $stock['unidades'] < $item['unidades']) {
            $error_stock = true;
            break;
        }
    }

    // Si todos los productos tienen stock, realiza la compra
    if (!$error_stock) {

        foreach ($items_pagar as $item) {

            // Resta las unidades del stock
            $sql_update = "UPDATE stock SET unidades = unidades - :unidades WHERE producto = :cod AND tienda = 1";
            $stmt_update = $conexion->prepare($sql_update);
            $stmt_update->bindValue(':unidades', $item['unidades'], PDO::PARAM_INT);
            $stmt_update->bindValue(':cod',      $item['producto']);
            $stmt_update->execute();

            // Saca el nombre e imagen del producto por separado
            $sql_prod = "SELECT nombre_corto, pvp, imagen FROM producto WHERE cod = :cod";
            $stmt_prod = $conexion->prepare($sql_prod);
            $stmt_prod->bindValue(':cod', $item['producto']);
            $stmt_prod->execute();
            $producto = $stmt_prod->fetch();

            // Guarda el pedido con la imagen incluida
            $sql_pedido = "INSERT INTO pedido (usuario, producto, nombre_producto, unidades, pvp, imagen)
                           VALUES (:usuario, :producto, :nombre_producto, :unidades, :pvp, :imagen)";
            $stmt_pedido = $conexion->prepare($sql_pedido);
            $stmt_pedido->bindValue(':usuario',         $usuario);
            $stmt_pedido->bindValue(':producto',        $item['producto']);
            $stmt_pedido->bindValue(':nombre_producto', $producto['nombre_corto']);
            $stmt_pedido->bindValue(':unidades',        $item['unidades'], PDO::PARAM_INT);
            $stmt_pedido->bindValue(':pvp',             $producto['pvp']);
            $stmt_pedido->bindValue(':imagen',          $producto['imagen']);
            $stmt_pedido->execute();
        }

        // Vacía el carrito del usuario tras completar la compra
        $sql_vaciar = "DELETE FROM carrito WHERE usuario = :usuario";
        $stmt_vaciar = $conexion->prepare($sql_vaciar);
        $stmt_vaciar->bindValue(':usuario', $usuario);
        $stmt_vaciar->execute();

        $pagado = true;
    }
}

// Obtiene los productos del carrito
$sql = "SELECT * FROM carrito WHERE usuario = :usuario";
$stmt = $conexion->prepare($sql);
$stmt->bindValue(':usuario', $usuario);
$stmt->execute();
$items_carrito = $stmt->fetchAll();

// Para cada item del carrito saca los datos del producto por separado
$items = [];
$total = 0;
foreach ($items_carrito as $item) {
    $sql_prod = "SELECT nombre_corto, pvp, imagen FROM producto WHERE cod = :cod";
    $stmt_prod = $conexion->prepare($sql_prod);
    $stmt_prod->bindValue(':cod', $item['producto']);
    $stmt_prod->execute();
    $producto = $stmt_prod->fetch();

    // Junta los datos del carrito con los del producto
    $items[] = [
        'id'          => $item['id'],
        'unidades'    => $item['unidades'],
        'nombre_corto'=> $producto['nombre_corto'],
        'pvp'         => $producto['pvp'],
        'imagen'      => $producto['imagen']
    ];

    $total += $producto['pvp'] * $item['unidades'];
}
?>

<main>
    <div class="carrito-contenedor">
        <h1 class="carrito-titulo">Mi carrito</h1>

        <!-- Mensaje de éxito si la compra se ha realizado correctamente -->
        <?php if (isset($pagado) && $pagado): ?>
            <p class="contacto-ok">Compra realizada correctamente.</p>
        <?php endif; ?>

        <!-- Mensaje de error si algún producto no tiene stock suficiente -->
        <?php if (isset($error_stock) && $error_stock): ?>
            <p class="error">No hay suficiente stock para alguno de los productos.</p>
        <?php endif; ?>

        <?php if (count($items) === 0): ?>
            <p class="carrito-vacio">Tu carrito está vacío.</p>
        <?php else: ?>
            <div class="carrito-lista">
                <?php foreach ($items as $item): ?>
                    <div class="carrito-item">
                        <!-- Muestra la imagen del producto o una por defecto si no tiene -->
                        <?php if ($item['imagen']): ?>
                            <img src="static/img/<?php echo htmlspecialchars($item['imagen']); ?>" alt="">
                        <?php else: ?>
                            <img src="static/img/default.jpg" alt="">
                        <?php endif; ?>

                        <div class="carrito-info">
                            <p class="carrito-nombre"><?php echo htmlspecialchars($item['nombre_corto']); ?></p>
                            <p class="carrito-precio"><?php echo number_format($item['pvp'], 2, ',', '.'); ?>€ x <?php echo $item['unidades']; ?> ud</p>
                            <!-- Subtotal = precio unitario x unidades -->
                            <p class="carrito-subtotal">Subtotal: <?php echo number_format($item['pvp'] * $item['unidades'], 2, ',', '.'); ?>€</p>
                        </div>

                        <!-- Botón para eliminar este producto del carrito -->
                        <form method="post">
                            <input type="hidden" name="borrar" value="<?php echo $item['id']; ?>">
                            <button type="submit" class="boton-borrar">Borrar</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Barra inferior con el total y el botón de pagar -->
            <div class="carrito-total">
                <p>Total: <strong><?php echo number_format($total, 2, ',', '.'); ?>€</strong></p>
                <form method="post">
                    <input type="hidden" name="pagar" value="1">
                    <button type="submit" class="boton-pagar">Pagar</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include_once 'includes/footer.php'; ?>