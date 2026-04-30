<?php
session_start();

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
    <link rel="stylesheet" href="../static/css/estilos.css">
</head>
<body>

<div class="admin-contenedor">
    <h1 class="admin-titulo">➕ Nuevo producto</h1>

    <?php
    require_once '../includes/conexion.php';
    require_once '../includes/funciones.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Subida de imagen
        $imagen = null;
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
            $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
            $nombre_archivo = uniqid('pala_') . '.' . $extension;
            $destino = '../static/img/' . $nombre_archivo;
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $destino)) {
                $imagen = $nombre_archivo;
            }
        }

        $sql = "INSERT INTO producto (cod, nombre, nombre_corto, descripcion, marca, nivel, forma, peso, pvp, exclusiva, imagen)
                VALUES (:cod, :nombre, :nombre_corto, :descripcion, :marca, :nivel, :forma, :peso, :pvp, :exclusiva, :imagen)";

        $stmt = $conexion->prepare($sql);

        $stmt->bindValue(':cod',          $_POST['cod']);
        $stmt->bindValue(':nombre',       $_POST['nombre']);
        $stmt->bindValue(':nombre_corto', $_POST['nombre_corto']);
        $stmt->bindValue(':descripcion',  $_POST['descripcion']);
        $stmt->bindValue(':marca',        $_POST['marca']);
        $stmt->bindValue(':nivel',        $_POST['nivel']);
        $stmt->bindValue(':forma',        $_POST['forma']);
        $stmt->bindValue(':peso',         $_POST['peso'], PDO::PARAM_INT);
        $stmt->bindValue(':pvp',          $_POST['pvp'], PDO::PARAM_STR);
        $stmt->bindValue(':exclusiva',    isset($_POST['exclusiva']) ? 1 : 0, PDO::PARAM_INT);
        $stmt->bindValue(':imagen',       $imagen);

        try {
            $stmt->execute();
            header("Location: productos.php");
            exit();
        } catch (Exception $e) {
            echo "<p style='color:red'>Error al insertar el producto: " . $e->getMessage() . "</p>";
        }
    }
    ?>

    <div class="admin-barra">
        <div class="admin-botones">
            <a href="../index.php" class="boton-tienda">Tienda</a>
            <a href="productos.php" class="boton-nuevo">← Volver a productos</a>
            <a href="../logout.php" class="boton-cerrar">Cerrar sesión</a>
        </div>
    </div>

    <form method="post" class="formulario" enctype="multipart/form-data">

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
            <label>Marca</label>
            <input type="text" name="marca">
        </div>

        <div class="form-grupo">
            <label>Nivel</label>
            <select name="nivel">
                <option value="principiante">Principiante</option>
                <option value="intermedio">Intermedio</option>
                <option value="avanzado">Avanzado</option>
            </select>
        </div>

        <div class="form-grupo">
            <label>Forma</label>
            <select name="forma">
                <option value="redonda">Redonda</option>
                <option value="lagrima">Lágrima</option>
                <option value="diamante">Diamante</option>
            </select>
        </div>

        <div class="form-grupo">
            <label>Peso (g)</label>
            <input type="number" name="peso" min="0">
        </div>

        <div class="form-grupo">
            <label>Precio (€)</label>
            <input type="number" step="0.01" name="pvp" required>
        </div>

        <div class="form-grupo">
            <label>Exclusiva</label>
            <input type="checkbox" name="exclusiva" value="1">
        </div>

        <div class="form-grupo">
            <label>Imagen</label>
            <input type="file" name="imagen" accept="image/*" required>
        </div>

        <div class="form-botones">
            <a href="productos.php" class="boton-borrar">Cancelar</a>
            <button type="submit" class="boton-nuevo">Guardar</button>
        </div>

    </form>
</div>

</body>
</html>
