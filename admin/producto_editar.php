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
    <title>Tienda - Editar producto</title>
    <link rel="stylesheet" href="../static/css/estilos.css">
</head>
<body>

<div class="admin-contenedor">
    <h1 class="admin-titulo">✏️ Editar producto</h1>

    <?php
    require_once '../includes/conexion.php';
    require_once '../includes/funciones.php';

    if (!isset($_GET['cod'])) {
        die("Código de producto no especificado.");
    }

    $cod = $_GET['cod'];

    $sql = "SELECT * FROM producto WHERE cod = :cod";
    $stmt = $conexion->prepare($sql);
    $stmt->bindValue(':cod', $cod);
    $stmt->execute();
    $producto = $stmt->fetch();

    if (!$producto) {
        die("Producto no encontrado.");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Subida de imagen
        $imagen = $producto['imagen']; // mantiene la anterior por defecto
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
            $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
            $nombre_archivo = uniqid('pala_') . '.' . $extension;
            $destino = '../static/img/' . $nombre_archivo;
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $destino)) {
                $imagen = $nombre_archivo;
            }
        }

        $sql = "UPDATE producto
                SET nombre       = :nombre,
                    nombre_corto = :nombre_corto,
                    descripcion  = :descripcion,
                    marca        = :marca,
                    nivel        = :nivel,
                    forma        = :forma,
                    peso         = :peso,
                    pvp          = :pvp,
                    exclusiva    = :exclusiva,
                    imagen       = :imagen
                WHERE cod = :cod";

        $stmt = $conexion->prepare($sql);

        $stmt->bindValue(':cod',          $cod);
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
            echo "<p style='color:red'>Error al actualizar: " . $e->getMessage() . "</p>";
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
            <input type="text" value="<?php echo htmlspecialchars($producto['cod']); ?>" disabled>
        </div>

        <div class="form-grupo">
            <label>Nombre</label>
            <input type="text" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>">
        </div>

        <div class="form-grupo">
            <label>Nombre corto</label>
            <input type="text" name="nombre_corto" value="<?php echo htmlspecialchars($producto['nombre_corto']); ?>" required>
        </div>

        <div class="form-grupo">
            <label>Descripción</label>
            <textarea name="descripcion" rows="4"><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
        </div>

        <div class="form-grupo">
            <label>Marca</label>
            <input type="text" name="marca" value="<?php echo htmlspecialchars($producto['marca']); ?>">
        </div>

        <div class="form-grupo">
            <label>Nivel</label>
            <select name="nivel">
                <option value="principiante" <?php echo $producto['nivel'] === 'principiante' ? 'selected' : ''; ?>>Principiante</option>
                <option value="intermedio"   <?php echo $producto['nivel'] === 'intermedio'   ? 'selected' : ''; ?>>Intermedio</option>
                <option value="avanzado"     <?php echo $producto['nivel'] === 'avanzado'     ? 'selected' : ''; ?>>Avanzado</option>
            </select>
        </div>

        <div class="form-grupo">
            <label>Forma</label>
            <select name="forma">
                <option value="redonda"   <?php echo $producto['forma'] === 'redonda'   ? 'selected' : ''; ?>>Redonda</option>
                <option value="lagrima"   <?php echo $producto['forma'] === 'lagrima'   ? 'selected' : ''; ?>>Lágrima</option>
                <option value="diamante"  <?php echo $producto['forma'] === 'diamante'  ? 'selected' : ''; ?>>Diamante</option>
            </select>
        </div>

        <div class="form-grupo">
            <label>Peso (g)</label>
            <input type="number" name="peso" min="0" value="<?php echo htmlspecialchars($producto['peso']); ?>">
        </div>

        <div class="form-grupo">
            <label>Precio (€)</label>
            <input type="number" step="0.01" name="pvp" value="<?php echo htmlspecialchars($producto['pvp']); ?>" required>
        </div>

        <div class="form-grupo">
            <label>Exclusiva</label>
            <input type="checkbox" name="exclusiva" value="1" <?php echo $producto['exclusiva'] ? 'checked' : ''; ?>>
        </div>

        <div class="form-grupo">
            <label>Imagen actual</label>
            <?php if ($producto['imagen']): ?>
                <img src="../static/img/<?php echo htmlspecialchars($producto['imagen']); ?>" style="max-width:120px; border-radius:8px;">
            <?php else: ?>
                <p>Sin imagen</p>
            <?php endif; ?>
            <label>Cambiar imagen</label>
            <input type="file" name="imagen" accept="image/*">
        </div>

        <div class="form-botones">
            <a href="productos.php" class="boton-borrar">Cancelar</a>
            <button type="submit" class="boton-nuevo">Actualizar</button>
        </div>

    </form>
</div>

</body>
</html>