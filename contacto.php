<?php
include_once 'includes/header.php';
require_once 'includes/conexion.php';

$ok = false;
$error = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre  = trim($_POST['nombre'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $mensaje = trim($_POST['mensaje'] ?? '');

    if ($nombre && $email && $mensaje) {
        $sql = "INSERT INTO contacto (nombre, email, mensaje) VALUES (:nombre, :email, :mensaje)";
        $stmt = $conexion->prepare($sql);
        $stmt->bindValue(':nombre',  $nombre);
        $stmt->bindValue(':email',   $email);
        $stmt->bindValue(':mensaje', $mensaje);
        try {
            $stmt->execute();
            $ok = true;
        } catch (Exception $e) {
            $error = true;
        }
    }
}
?>

<main>
    <div class="contacto-contenedor">

        <h1 class="contacto-titulo">Contacto</h1>

        <div class="contacto-bloque">

            <div class="contacto-formulario">
                <h2>Envíanos un mensaje</h2>

                <?php if ($ok): ?>
                    <p class="contacto-ok">✅ Mensaje enviado correctamente.</p>
                <?php endif; ?>
                <?php if ($error): ?>
                    <p style="color:red">❌ Error al enviar el mensaje.</p>
                <?php endif; ?>

                <form method="post" class="formulario">
                    <div class="form-grupo">
                        <label>Nombre</label>
                        <input type="text" name="nombre" placeholder="Tu nombre" required>
                    </div>
                    <div class="form-grupo">
                        <label>Email</label>
                        <input type="email" name="email" placeholder="tu@email.com" required>
                    </div>
                    <div class="form-grupo">
                        <label>Mensaje</label>
                        <textarea name="mensaje" rows="5" placeholder="Escribe tu mensaje..." required></textarea>
                    </div>
                    <div class="form-botones">
                        <button type="submit" class="boton-nuevo">Enviar</button>
                    </div>
                </form>
            </div>

            <div class="contacto-info">
                <h2>Dónde estamos</h2>
                <p>📍 Calle del Pádel, 10 - Crevillente</p>
                <p>📞 <a href="tel:+34600000000">+34 666 666 666 | 999 999 999</a></p>
                <p>✉️ <a href="mailto:padelzone@gmail.com">padelzone@gmail.com</a></p>
                <p>🕐 Lunes - Viernes: 9:00 - 20:00</p>
            </div>

        </div>
    </div>
</main>

<?php include_once 'includes/footer.php'; ?>