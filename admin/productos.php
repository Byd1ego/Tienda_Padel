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
    <title>Tienda - Administración de productos</title>
    <link rel="stylesheet" href="../resources/estilos.css">
</head>

<body>

<div class="admin-contenedor">
    <h1 class="admin-titulo">🛒 Administración de productos</h1>

    <?php
        require_once '../includes/conexion.php';
        require_once '../includes/funciones.php';
    ?>

    <div class="admin-barra">
        <div class="admin-botones">
            <a href="../index.php" class="boton-tienda">Tienda</a>
            <a href="producto_nuevo.php" class="boton-nuevo">➕ Nuevo producto</a>
            <a href="../logout.php" class="boton-cerrar">Cerrar sesión</a>
        </div>
    </div>

    <?php
        $sql = "SELECT cod, nombre_corto, descripcion, marca, nivel, forma, peso, pvp, oferta 
                FROM producto";

        $stmt = $conexion->prepare($sql);
        $stmt->execute();
        $productos = $stmt->fetchAll();

        if (count($productos) > 0) {
            echo "<table class='tabla-admin'>";
            echo "<tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Marca</th>
                    <th>Nivel</th>
                    <th>Forma</th>
                    <th>Peso</th>
                    <th>PVP</th>
                    <th>Oferta</th>
                    <th>Acciones</th>
                  </tr>";

            foreach ($productos as $p) {
                echo "<tr>";
                echo "<td>" . $p['cod'] . "</td>";
                echo "<td>" . $p['nombre_corto'] . "</td>";
                echo "<td>" . $p['descripcion'] . "</td>";
                echo "<td>" . $p['marca'] . "</td>";
                echo "<td>" . $p['nivel'] . "</td>";
                echo "<td>" . $p['forma'] . "</td>";
                echo "<td>" . $p['peso'] . " g</td>";
                echo "<td class='precio-admin'>" . number_format($p['pvp'], 2) . " €</td>";
                echo "<td>" . ($p['oferta'] ? 'Sí' : 'No') . "</td>";
                echo "<td class='acciones-admin'>
                        <a class='boton-editar' href='producto_editar.php?cod=" . $p['cod'] . "'>Editar</a><br>
                        <a class='boton-borrar' href='producto_borrar.php?cod=" . $p['cod'] . "'>Borrar</a>
                      </td>";
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "<p>No hay productos para mostrar.</p>";
        }
    ?>
</div>

</body>
</html>