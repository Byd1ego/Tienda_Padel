<?php 
// Carga la cabecera del panel de administración
require_once '../includes/header_admin.php';

// Carga la conexión a la base de datos y funciones auxiliares
require_once '../includes/conexion.php';
require_once '../includes/funciones.php'; 
?>

<div class="admin-contenedor">
    <h1 class="admin-titulo">Editar producto</h1>

    <?php
    // Si no llega el código por URL, detiene la ejecución
    if (!isset($_GET['cod'])) {
        die("Código de producto no especificado.");
    }

    $cod = $_GET['cod'];

    // Busca el producto en la base de datos por su código
    $sql = "SELECT * FROM producto WHERE cod = :cod";
    $stmt = $conexion->prepare($sql);
    $stmt->bindValue(':cod', $cod);
    $stmt->execute();
    $producto = $stmt->fetch();

    // Si no existe el producto, detiene la ejecución
    if (!$producto) {
        die("Producto no encontrado.");
    }

    // Consulta el stock actual del producto en la tienda 1
    $sql_stock = "SELECT unidades FROM stock WHERE producto = :cod AND tienda = 1";
    $stmt_stock = $conexion->prepare($sql_stock);
    $stmt_stock->bindValue(':cod', $cod);
    $stmt_stock->execute();
    $stock = $stmt_stock->fetch();

    // Si no hay stock registrado, se pone 0 por defecto
    $unidades = $stock ? $stock['unidades'] : 0;

    // Si el formulario ha sido enviado, procesa los datos
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Mantiene la imagen actual por defecto
        $imagen = $producto['imagen'];

        // Si se ha subido una nueva imagen sin errores, la guarda en el servidor
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
            $extension       = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
            $nombre_original = pathinfo($_FILES['imagen']['name'], PATHINFO_FILENAME);
            $nombre_archivo  = $nombre_original . '.' . $extension;
            $destino         = '../static/img/' . $nombre_archivo;

            // Solo mueve el archivo si no existe ya uno con el mismo nombre
            if (!file_exists($destino)) {
                move_uploaded_file($_FILES['imagen']['tmp_name'], $destino);
            }
            $imagen = $nombre_archivo;
        }

        // Actualiza todos los campos del producto en la base de datos
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
        $stmt->bindValue(':peso',         $_POST['peso'],         PDO::PARAM_INT);
        $stmt->bindValue(':pvp',          $_POST['pvp'],          PDO::PARAM_STR);
        $stmt->bindValue(':exclusiva',    isset($_POST['exclusiva']) ? 1 : 0, PDO::PARAM_INT);
        $stmt->bindValue(':imagen',       $imagen);

        try {
            $stmt->execute();

            // Si ya existía stock lo actualiza, si no existía lo inserta
            if ($stock) {
                $sql_stock = "UPDATE stock SET unidades = :unidades WHERE producto = :cod AND tienda = 1";
            } else {
                $sql_stock = "INSERT INTO stock (producto, tienda, unidades) VALUES (:cod, 1, :unidades)";
            }
            $stmt_stock = $conexion->prepare($sql_stock);
            $stmt_stock->bindValue(':cod',      $cod);
            $stmt_stock->bindValue(':unidades', $_POST['unidades'], PDO::PARAM_INT);
            $stmt_stock->execute();

            // Redirige al listado tras guardar los cambios
            header("Location: productos.php");
            exit();
        } catch (Exception $e) {
            echo "<p style='color:red'>Error al actualizar: " . $e->getMessage() . "</p>";
        }
    }
    ?>

    <!-- Formulario de edición, enctype necesario para poder subir imágenes -->
    <form method="post" class="formulario" enctype="multipart/form-data">
        <div class="form-grupo">
            <label>Código</label>
            <!-- Deshabilitado para que no se pueda cambiar el código -->
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
            <!-- Marca como seleccionada la opción que tiene el producto actualmente -->
            <select name="nivel">
                <option value="principiante" <?php echo $producto['nivel'] === 'principiante' ? 'selected' : ''; ?>>Principiante</option>
                <option value="intermedio"   <?php echo $producto['nivel'] === 'intermedio'   ? 'selected' : ''; ?>>Intermedio</option>
                <option value="avanzado"     <?php echo $producto['nivel'] === 'avanzado'     ? 'selected' : ''; ?>>Avanzado</option>
            </select>
        </div>
        <div class="form-grupo">
            <label>Forma</label>
            <select name="forma">
                <option value="redonda"  <?php echo $producto['forma'] === 'redonda'  ? 'selected' : ''; ?>>Redonda</option>
                <option value="lagrima"  <?php echo $producto['forma'] === 'lagrima'  ? 'selected' : ''; ?>>Lágrima</option>
                <option value="diamante" <?php echo $producto['forma'] === 'diamante' ? 'selected' : ''; ?>>Diamante</option>
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
            <!-- Marca el checkbox si el producto es exclusivo -->
            <input type="checkbox" name="exclusiva" value="1" <?php echo $producto['exclusiva'] ? 'checked' : ''; ?>>
        </div>
        <div class="form-grupo">
            <label>Stock</label>
            <input type="number" name="unidades" min="0" value="<?php echo $unidades; ?>">
        </div>
        <div class="form-grupo">
            <label>Imagen actual</label>
            <!-- Muestra la imagen actual del producto si tiene una -->
            <?php if ($producto['imagen']): ?>
                <img src="../static/img/<?php echo htmlspecialchars($producto['imagen']); ?>" style="max-width:120px; border-radius:8px;">
            <?php else: ?>
                <p>Sin imagen</p>
            <?php endif; ?>
            <label>Cambiar imagen</label>
            <!-- Campo opcional para subir una nueva imagen -->
            <input type="file" name="imagen" accept="image/*">
        </div>
        <div class="form-botones">
            <a href="productos.php" class="boton-borrar">Cancelar</a>
            <button type="submit" class="boton-nuevo">Actualizar</button>
        </div>
    </form>
</div>

<?php require_once '../includes/footer_admin.php'; ?>