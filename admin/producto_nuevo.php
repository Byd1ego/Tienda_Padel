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
    <title>Tienda - Nuevo producto</title>
    <link rel="stylesheet" href="../resources/estilos.css">
</head>
<body>

<div class="contenedor">
    <h1>➕ Nuevo producto</h1>

    <?php
    require_once '../includes/conexion.php';
    require_once '../includes/funciones.php';

    // Insertar producto
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $sql = "INSERT INTO producto (cod, nombre, nombre_corto, descripcion, PVP, familia)
                VALUES (:cod, :nombre, :nombre_corto, :descripcion, :pvp, :familia)";

        $stmt = $conexion->prepare($sql);

        $stmt->bindValue(':cod', $_POST['cod']);
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
            echo "<p style='color:red'>Error al insertar el producto</p>";
        }
    }    
    ?>

    <form method="post" class="formulario">

        <div class="form-grupo">
            <label>Código</label>
            <input type="text" name="cod" required>
        </div>

        <div class="form-grupo">
            <label>Nombre</label>
            <input type="text" name="nombre">
        </div>

        <div class="form-grupo">
            <label>Nombre corto</label>
            <input type="text" name="nombre_corto" required>
        </div>

        <div class="form-grupo">
            <label>Descripción</label>
            <textarea name="descripcion" rows="4"></textarea>
        </div>

        <div class="form-grupo">
            <label>Precio (€)</label>
            <input type="number" step="0.01" name="pvp" required>
        </div>

        <div class="form-grupo">
            <label>Familia</label>
            <?php                
                echo generarSelect($conexion, 'producto', 'familia', 'familia', '', false);
            ?>
        </div>

        <div class="form-botones">
            <a href="productos.php" class="btn btn-borrar">Cancelar</a>
            <button type="submit" class="btn btn-nuevo">Guardar</button>
        </div>

    </form>
</div>

</body>
</html>
