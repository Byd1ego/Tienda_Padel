<?php
include_once 'includes/header.php';
require_once 'includes/conexion.php';

$ok = false;
$error = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre   = trim($_POST['nombre']   ?? '');
    $email    = trim($_POST['email']    ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $mensaje  = trim($_POST['mensaje']  ?? '');

    if ($nombre && $email && $telefono && $mensaje) {
        $sql = "INSERT INTO contacto (nombre, email, telefono, mensaje) VALUES (:nombre, :email, :telefono, :mensaje)";
        $stmt = $conexion->prepare($sql);
        $stmt->bindValue(':nombre',   $nombre);
        $stmt->bindValue(':email',    $email);
        $stmt->bindValue(':telefono', $telefono);
        $stmt->bindValue(':mensaje',  $mensaje);
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
    <div class="banner-grid">
        <div class="banner-imagen">
            <img src="static/img/contactanos.png" alt="Contáctanos">
        </div>
        <div class="banner-texto">
            <h1 class="banner-titulo">Envíanos un mensaje</h1>
            <p class="banner-desc">Cualquier problema o consulta, no dudes en contactarnos.</p>
        </div>
    </div>

    <div class="contacto-contenedor">
        <div class="contacto-bloque">

            <div class="contacto-formulario">
                <h2>Envíanos un mensaje</h2>

                <?php if ($ok): ?>
                    <p class="contacto-ok">Mensaje enviado correctamente.</p>
                <?php endif; ?>
                <?php if ($error): ?>
                    <p style="color:red">Error al enviar el mensaje.</p>
                <?php endif; ?>

                <form method="post" class="formulario" onsubmit="return validarTelefono()">
                    <div class="form-grupo">
                        <label>Nombre</label>
                        <input type="text" name="nombre" placeholder="Tu nombre" required>
                    </div>
                    <div class="form-grupo">
                        <label>Email</label>
                        <input type="email" name="email" placeholder="tu@email.com" required>
                    </div>
                    <div class="form-grupo">
                        <label>Teléfono</label>
                        <input type="text" name="telefono" id="telefono" placeholder="600000000" required>
                        <span id="telefono-error" style="color:red; font-size:0.85em; display:none;">El teléfono debe tener exactamente 9 dígitos y solo números.</span>
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
                <div id="tiempo"></div>
            </div>

        </div>
    </div>
    


</main>

<script>
function validarTelefono() {
    const telefono = document.getElementById('telefono').value;
    const error    = document.getElementById('telefono-error');
    const regex    = /^\d{9}$/;

    if (!regex.test(telefono)) {
        error.style.display = 'block';
        return false;
    }

    error.style.display = 'none';
    return true;
}
</script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
// Petición AJAX a la API del tiempo de Crevillente
$.ajax({
    url: 'https://api.open-meteo.com/v1/forecast?latitude=38.25&longitude=-0.81&current_weather=true',
    type: 'GET',
    success: function (datos) {
        // Muestra la temperatura actual en el div
        var temp = datos.current_weather.temperature;
        $('#tiempo').html('🌡️ Temperatura actual en Crevillente: <strong>' + temp + '°C</strong>');
    }
});
</script>

<?php include_once 'includes/footer.php'; ?>