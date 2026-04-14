<?php
session_start();

// Si no hay usuario logeado o no es admin, redirige al login
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {    
    header("Location: ../login.php?redirigido=true");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tienda - Editar producto</title>
    <link rel="stylesheet" href="../resources/estilos.css">    
</head>
<body>

<div class="contenedor">
    <h1>✏️ Editar producto</h1>

    <?php    
    require_once '../includes/conexion.php';
    require_once '../includes/funciones.php';
    
    // Comprobar que llega el código por GET
    if (!isset($_GET['cod'])) {
        die("Código de producto no especificado.");
    }

    $cod = $_GET['cod'];

    // Cargar datos actuales del producto
    $sql = "SELECT * FROM producto WHERE cod = :cod";
    $stmt = $conexion->prepare($sql);
    $stmt->bindValue(':cod', $cod);
    $stmt->execute();
    $producto = $stmt->fetch();

    if (!$producto) {
        die("Producto no encontrado.");
    }

    // Procesar actualización
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $sql = "UPDATE producto
                SET nombre = :nombre,
                    nombre_corto = :nombre_corto,
                    descripcion = :descripcion,
                    PVP = :pvp,
                    familia = :familia
                WHERE cod = :cod";

        $stmt = $conexion->prepare($sql);

        $stmt->bindValue(':cod', $cod);
        $stmt->bindValue(':nombre', $_POST['nombre']);
        $stmt->bindValue(':nombre_corto', $_POST['nombre_corto']);
        $stmt->bindValue(':descripcion', $_POST['descripcion']);
        $stmt->bindValue(':pvp', $_POST['pvp'], PDO::PARAM_STR);
        $stmt->bindValue(':familia', $_POST['familia']);

        try {
            $stmt->execute();
            header("Location: productos.php");
            exit();
        } catch (Exception $e) {
            echo "<p style='color:red'>Error al actualizar el producto</p>";
        }
    }
    ?>

    <form method="post" class="formulario">

        <div class="form-grupo">
            <label>Código</label>
            <input type="text" name="cod" value="<?php echo htmlspecialchars($producto['cod']); ?>" disabled>
        </div>

        <div class="form-grupo">
            <label>Nombre</label>
            <input type="text" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>">
        </div>

        <div class="form-grupo">
            <label>Nombre corto</label>
            <input type="text" name="nombre_corto" value="<?php echo htmlspecialchars($producto['nombre_corto']); ?>" required>
        </div>

        <div class="form-grupo" style="flex: 1 1 100%;">
            <label>Descripción</label>
            <textarea name="descripcion" rows="4"><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
        </div>

        <div class="form-grupo">
            <label>Precio (€)</label>
            <input type="number" step="0.01" name="pvp" value="<?php echo htmlspecialchars($producto['PVP']); ?>" required>
        </div>

        <div class="form-grupo">
            <label>Familia</label>
            <?php
                echo generarSelect($conexion, 'producto', 'familia', 'familia', $producto['familia'], false);
            ?>
        </div>

        <div class="form-botones">
            <a href="productos.php" class="btn btn-borrar">Cancelar</a>
            <button type="submit" class="btn btn-editar">Actualizar</button>
        </div>

    </form>
</div>

</body>
</html>
