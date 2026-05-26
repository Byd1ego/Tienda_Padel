<?php
// Carga la cabecera y con ella inicia la sesión
include_once 'static/header.php';
require_once 'static/conexion.php';

// Solo los usuarios logueados pueden acceder al foro
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php?acceso_denegado=1");
    exit();
}

// Si se ha enviado un comentario, lo guarda en la base de datos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nick       = trim($_POST['nick']       ?? '');
    $comentario = trim($_POST['comentario'] ?? '');

    // Solo inserta si los dos campos tienen contenido
    if ($nick && $comentario) {
        $sql = "INSERT INTO foro (nick, comentario) VALUES (:nick, :comentario)";
        $stmt = $conexion->prepare($sql);
        $stmt->bindValue(':nick',       $nick);
        $stmt->bindValue(':comentario', $comentario);
        $stmt->execute();
    }
}

// Obtiene todos los comentarios ordenados del más reciente al más antiguo
$sql  = "SELECT nick, comentario, fecha FROM foro ORDER BY fecha DESC";
$stmt = $conexion->prepare($sql);
$stmt->execute();
$comentarios = $stmt->fetchAll();
?>

<main>
    <!-- Mensaje de bienvenida oculto, se muestra con jQuery slideDown al cargar -->
    <div id="bienvenida">
        <strong>¡Bienvenido al foro!</strong> Aquí puedes compartir tu opinión sobre nuestras palas.
        <span id="cerrar-bienvenida">✕</span>
    </div>

    <h2>Foro</h2>

    <!-- La fecha y hora se escriben aquí mediante JavaScript -->
    <p id="fecha-actual" style="text-align:center; color:#888; margin-bottom:16px; font-size:0.9rem;"></p>

    <div class="foro-contenedor">

        <div class="foro-formulario">
            <form method="post">
                <div class="form-grupo">
                    <label for="nick">Nick</label>
                    <input type="text" name="nick" id="nick" placeholder="Tu nombre..." required>
                </div>
                <div class="form-grupo">
                    <label for="comentario">Comentario</label>
                    <textarea name="comentario" id="comentario" rows="4" placeholder="Escribe tu comentario..." required></textarea>
                </div>
                <div class="form-botones">
                    <button type="submit" class="boton-nuevo">Publicar</button>
                </div>
            </form>
        </div>

        <!-- Lista de comentarios existentes -->
        <div id="foro">
            <?php foreach ($comentarios as $c): ?>
                <div class="comentario">
                    <strong><?php echo htmlspecialchars($c['nick']); ?></strong>
                    <span class="comentario-fecha">(<?php echo $c['fecha']; ?>)</span>
                    <p><?php echo htmlspecialchars($c['comentario']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
</main>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    // Espera a que la página cargue completamente antes de ejecutar el código
    $(document).ready(function () {

        // Muestra el mensaje de bienvenida con efecto deslizante hacia abajo
        $("#bienvenida").slideDown(1000);

        // Al pulsar la X, oculta el mensaje con efecto deslizante hacia arriba
        $("#cerrar-bienvenida").click(function () {
            $("#bienvenida").slideUp();
        });

        // Obtiene la fecha y hora actual con el objeto Date de JavaScript
        var ahora = new Date();
        var fecha = ahora.toLocaleDateString('es-ES');
        var hora  = ahora.toLocaleTimeString('es-ES');

        // Escribe la fecha y hora en el elemento del DOM
        document.getElementById('fecha-actual').textContent =  fecha + ' — ' + hora;

    });
</script>

<?php include_once 'includes/footer.php'; ?>