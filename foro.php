<?php
include_once 'includes/header.php';
require_once 'includes/conexion.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php?redirigido=true");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nick       = trim($_POST['nick'] ?? '');
    $comentario = trim($_POST['comentario'] ?? '');

    if ($nick && $comentario) {
        $sql = "INSERT INTO foro (nick, comentario) VALUES (:nick, :comentario)";
        $stmt = $conexion->prepare($sql);
        $stmt->bindValue(':nick',       $nick);
        $stmt->bindValue(':comentario', $comentario);
        $stmt->execute();
    }
}

$sql  = "SELECT nick, comentario, fecha FROM foro ORDER BY fecha DESC";
$stmt = $conexion->prepare($sql);
$stmt->execute();
$comentarios = $stmt->fetchAll();
?>

<main>
    <div id="bienvenida">
        <strong>¡Bienvenido al foro!</strong> Aquí puedes compartir tu opinión sobre nuestras palas.
        <span id="cerrar-bienvenida">✕</span>
    </div>

    <h2>Foro</h2>

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
                    <button type="submit" class="boton-nuevo">💬 Publicar</button>
                </div>
            </form>
        </div>

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
    $(document).ready(function () {
        $("#bienvenida").slideDown(1000);
        $("#cerrar-bienvenida").click(function () {
            $("#bienvenida").slideUp();
        });
    });
</script>

<?php include_once 'includes/footer.php'; ?>