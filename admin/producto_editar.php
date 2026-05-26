<?php 
// Carga la cabecera, la conexión y las funciones del panel de administración
require_once '../static/header_admin.php';
require_once '../static/conexion.php';
require_once '../static/funciones.php';

// Variable para guardar mensajes de error
$error = '';
?>

<div class="admin-contenedor">
    <h1 class="admin-titulo">Editar producto</h1>

    <?php
    // Comprueba que se ha pasado un código de producto por URL
    if (!isset($_GET['cod'])) {
        die("Código de producto no especificado.");
    }

    $cod = $_GET['cod'];

    // Busca el producto en la base de datos por su código
    $sql = "SELECT * FROM producto WHERE cod_producto = :cod";
    $stmt = $conexion->prepare($sql);
    $stmt->bindValue(':cod', $cod);
    $stmt->execute();
    $producto = $stmt->fetch();

    // Si no existe el producto, para la ejecución
    if (!$producto) {
        die("Producto no encontrado.");
    }

    // Obtiene el stock actual del producto en la tienda 1
    $sql_stock = "SELECT unidades FROM stock WHERE cod_producto = :cod AND cod_tienda = 1";
    $stmt_stock = $conexion->prepare($sql_stock);
    $stmt_stock->bindValue(':cod', $cod);
    $stmt_stock->execute();
    $stock    = $stmt_stock->fetch();
    $unidades = $stock ? $stock['unidades'] : 0;

    // Si el formulario ha sido enviado, procesa los datos
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Comprueba que el nombre corto no esté ya en uso por otro producto distinto
        $sqlCheck = "SELECT cod_producto FROM producto WHERE nombre_corto = :nombre_corto AND cod_producto != :cod";
        $stmtCheck = $conexion->prepare($sqlCheck);
        $stmtCheck->bindValue(':nombre_corto', $_POST['nombre_corto']);
        $stmtCheck->bindValue(':cod',          $cod);
        $stmtCheck->execute();

        if ($stmtCheck->fetch()) {
            // Si ya existe, guarda el mensaje de error y no guarda nada
            $error = "Ya existe otro producto con ese nombre corto.";
        } else {

            // Mantiene la imagen actual por defecto
            $imagen = $producto['imagen'];

            // Si se ha subido una nueva imagen la procesa y la guarda
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
                $extension       = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
                $nombre_original = pathinfo($_FILES['imagen']['name'], PATHINFO_FILENAME);
                $nombre_archivo  = $nombre_original . '.' . $extension;
                $destino         = '../includes/img/' . $nombre_archivo;
                // Solo mueve el archivo si no existe ya uno con ese nombre
                if (!file_exists($destino)) {
                    move_uploaded_file($_FILES['imagen']['tmp_name'], $destino);
                }
                $imagen = $nombre_archivo;
            }

            // Actualiza los datos del producto en la base de datos
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
                    WHERE cod_producto = :cod";
            $stmt = $conexion->prepare($sql);
            $stmt->bindValue(':cod',          $cod);
            $stmt->bindValue(':nombre',       $_POST['nombre']);
            $stmt->bindValue(':nombre_corto', $_POST['nombre_corto']);
            $stmt->bindValue(':descripcion',  $_POST['descripcion']);
            $stmt->bindValue(':marca',        $_POST['marca']);
            $stmt->bindValue(':nivel',        $_POST['nivel']);
            $stmt->bindValue(':forma',        $_POST['forma']);
            $stmt->bindValue(':peso',         $_POST['peso'],  PDO::PARAM_INT);
            $stmt->bindValue(':pvp',          $_POST['pvp'],   PDO::PARAM_STR);
            $stmt->bindValue(':exclusiva',    isset($_POST['exclusiva']) ? 1 : 0, PDO::PARAM_INT);
            $stmt->bindValue(':imagen',       $imagen);

            try {
                $stmt->execute();

                // Si ya tiene stock lo actualiza, si no lo inserta
                if ($stock) {
                    $sql_stock = "UPDATE stock SET unidades = :unidades WHERE cod_producto = :cod AND cod_tienda = 1";
                } else {
                    $sql_stock = "INSERT INTO stock (cod_producto, cod_tienda, unidades) VALUES (:cod, 1, :unidades)";
                }
                $stmt_stock = $conexion->prepare($sql_stock);
                $stmt_stock->bindValue(':cod',      $cod);
                $stmt_stock->bindValue(':unidades', $_POST['unidades'], PDO::PARAM_INT);
                $stmt_stock->execute();

                // Si todo va bien redirige a la lista de productos
                header("Location: productos.php");
                exit();
            } catch (Exception $e) {
                // Si hay un error de base de datos lo muestra
                $error = "Error al actualizar: " . $e->getMessage();
            }
        }
    }
    ?>

    <!-- Muestra el error si existe -->
    <?php if ($error): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="post" class="formulario" enctype="multipart/form-data">
        <div class="form-grupo">
            <label>Código</label>
            <!-- El código está desactivado porque no se puede cambiar -->
            <input type="text" value="<?php echo htmlspecialchars($producto['cod_producto']); ?>" disabled>
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
            <!-- Marca como seleccionado el nivel actual del producto -->
            <select name="nivel">
                <option value="principiante" <?php echo $producto['nivel'] === 'principiante' ? 'selected' : ''; ?>>Principiante</option>
                <option value="intermedio"   <?php echo $producto['nivel'] === 'intermedio'   ? 'selected' : ''; ?>>Intermedio</option>
                <option value="avanzado"     <?php echo $producto['nivel'] === 'avanzado'     ? 'selected' : ''; ?>>Avanzado</option>
            </select>
        </div>
        <div class="form-grupo">
            <label>Forma</label>
            <!-- Marca como seleccionada la forma actual del producto -->
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
            <!-- Muestra la imagen actual si existe -->
            <?php if ($producto['imagen']): ?>
                <img src="../includes/img/<?php echo htmlspecialchars($producto['imagen']); ?>" style="max-width:120px; border-radius:8px;">
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

<?php require_once '../static/footer_admin.php'; ?>