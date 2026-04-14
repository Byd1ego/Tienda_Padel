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
    <title>Tienda - Borrar producto</title>
    <link rel="stylesheet" href="../resources/estilos.css">    
</head>
<body>

<div class="contenedor">
    <h1>🗑️ Borrar producto</h1>

    <?php
    require_once '../includes/conexion.php';

    if (!isset($_GET['cod'])) {
        die("Código de producto no especificado.");
    }

    $cod = $_GET['cod'];

    // Cargar producto
    $sql = "SELECT cod, nombre_corto FROM producto WHERE cod = :cod";
    $stmt = $conexion->prepare($sql);
    $stmt->bindValue(':cod', $cod);
    $stmt->execute();
    $producto = $stmt->fetch();

    if (!$producto) {
        die("Producto no encontrado.");
    }

    // Si se confirma el borrado
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $sql = "DELETE FROM producto WHERE cod = :cod";
        $stmt = $conexion->prepare($sql);
        $stmt->bindValue(':cod', $cod);

        try {
            $stmt->execute();
            header("Location: productos.php");
            exit();
        } catch (Exception $e) {
            echo "<p style='color:red'>Error al borrar el producto.</p>";
        }
    }
    ?>

    <div class="mensaje">
        ¿Estás seguro de que deseas borrar el producto <strong><?php echo htmlspecialchars($producto['nombre_corto']); ?></strong> (código: <?php echo htmlspecialchars($producto['cod']); ?>)?
    </div>

    <form method="post">
        <div class="form-botones">
            <a href="productos.php" class="btn btn-editar">Cancelar</a>
            <button type="submit" class="btn btn-borrar">Borrar</button>
        </div>
    </form>

</div>

</body>
</html>
