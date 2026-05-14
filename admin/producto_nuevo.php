<?php 
// Carga la cabecera del panel de administración
require_once '../includes/header_admin.php';

// Carga la conexión a la base de datos y funciones auxiliares
require_once '../includes/conexion.php';
require_once '../includes/funciones.php'; 
?>

<div class="admin-contenedor">
    <h1 class="admin-titulo">Nuevo producto</h1>

    <?php
    // Si el formulario ha sido enviado, procesa los datos
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Por defecto no hay imagen
        $imagen = null;

        // Si se ha subido una imagen sin errores, la guarda en el servidor
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

        // Inserta el nuevo producto en la base de datos
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
        $stmt->bindValue(':peso',         $_POST['peso'],         PDO::PARAM_INT);
        $stmt->bindValue(':pvp',          $_POST['pvp'],          PDO::PARAM_STR);
        $stmt->bindValue(':exclusiva',    isset($_POST['exclusiva']) ? 1 : 0, PDO::PARAM_INT);
        $stmt->bindValue(':imagen',       $imagen);

        try {
            $stmt->execute();

            // Inserta el stock inicial del producto en la tienda 1
            // Si no se especifica stock, se pone 0 por defecto
            $sql_stock = "INSERT INTO stock (producto, tienda, unidades) VALUES (:cod, 1, :unidades)";
            $stmt_stock = $conexion->prepare($sql_stock);
            $stmt_stock->bindValue(':cod',      $_POST['cod']);
            $stmt_stock->bindValue(':unidades', $_POST['unidades'] ?? 0, PDO::PARAM_INT);
            $stmt_stock->execute();

            // Redirige al listado tras guardar el nuevo producto
            header("Location: productos.php");
            exit();
        } catch (Exception $e) {
            echo "<p style='color:red'>Error al insertar el producto: " . $e->getMessage() . "</p>";
        }
    }
    ?>

    <!-- Formulario de creación, enctype necesario para poder subir imágenes -->
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
            <input type="number" name="pvp" required>
        </div>
        <div class="form-grupo">
            <label>Exclusiva</label>
            <!-- Si se marca, el producto se guardará como exclusivo -->
            <input type="checkbox" name="exclusiva" value="1">
        </div>
        <div class="form-grupo">
            <label>Stock inicial</label>
            <input type="number" name="unidades" min="0" value="0">
        </div>
        <div class="form-grupo">
            <label>Imagen</label>
            <!-- Solo acepta archivos de imagen, obligatorio -->
            <input type="file" name="imagen" accept="image/*" required>
        </div>
        <div class="form-botones">
            <a href="productos.php" class="boton-borrar">Cancelar</a>
            <button type="submit" class="boton-nuevo">Guardar</button>
        </div>
    </form>
</div>

<?php require_once '../includes/footer_admin.php'; ?>