<?php
// Carga la cabecera y con ella inicia la sesión
include_once 'static/header.php';

// Carga la conexión a la base de datos
require_once 'static/conexion.php';

// Variables para controlar si el mensaje se envió bien o hubo error
$ok    = false;
$error = false;

// Si el formulario ha sido enviado, procesa los datos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Recoge y limpia los datos del formulario
    $nombre   = trim($_POST['nombre']   ?? '');
    $email    = trim($_POST['email']    ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $mensaje  = trim($_POST['mensaje']  ?? '');

    // Solo inserta si todos los campos tienen valor
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
    <div class="banner-completo">
    <div class="banner-imagen-estatica">
        <img src="includes/img/contactanos.jpg" alt="Contáctanos">
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

                <!-- Mensaje verde si el formulario se envió correctamente -->
                <?php if ($ok): ?>
                    <p class="contacto-ok">Mensaje enviado correctamente.</p>
                <?php endif; ?>

                <!-- Mensaje rojo si hubo un error al guardar en la base de datos -->
                <?php if ($error): ?>
                    <p style="color:red">Error al enviar el mensaje.</p>
                <?php endif; ?>

                <!-- onsubmit llama a validarTelefono() antes de enviar el formulario -->
                <form method="post" class="formulario" onsubmit="return validarTelefono()">
                    <div class="form-grupo">
                        <label>Nombre</label>
                        <input type="text" name="nombre" placeholder="Tu nombre" required>
                    </div>
                    <div class="form-grupo">
                        <label>Email</label>
                        <!-- type="email" valida el formato de email automáticamente -->
                        <input type="email" name="email" placeholder="tu@email.com" required>
                    </div>
                    <div class="form-grupo">
                        <label>Teléfono</label>
                        <input type="text" name="telefono" id="telefono" placeholder="600000000" required>
                        <!-- Mensaje de error oculto que se muestra si el teléfono no es válido -->
                        <span id="telefono-error" style="color:red; font-size:0.85em; display:none;">
                            El teléfono debe tener exactamente 9 dígitos y solo números.
                        </span>
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
                <p> Calle del Pádel, 10 - Crevillente</p>
                <p>+34 666 666 666 | 999 999 999</p>
                <p>padelzone@gmail.com</p>
                <p> Lunes - Viernes: 9:00 - 20:00</p>
                <!-- Aquí aparece la temperatura cargada por AJAX -->
                <div id="tiempo"></div>
            </div>

        </div>
    </div>

</main>

<script>
// Valida que el teléfono tenga exactamente 9 dígitos antes de enviar el formulario
function validarTelefono() {
    const telefono = document.getElementById('telefono').value;
    const error    = document.getElementById('telefono-error');

    // La regex comprueba que sean exactamente 9 números, sin letras ni espacios
    const regex = /^\d{9}$/;

    if (!regex.test(telefono)) {
        // Muestra el mensaje de error y cancela el envío
        error.style.display = 'block';
        return false;
    }

    // Oculta el error y permite el envío
    error.style.display = 'none';
    return true;
}
</script>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
// Pido el tiempo de Crevillente a la API gratuita de Open-Meteo
$.get('https://api.open-meteo.com/v1/forecast?latitude=38.25&longitude=-0.81&current_weather=true', function(datos) {
    // Cuando llegan los datos muestro la temperatura en el div #tiempo
    $('#tiempo').html('Temperatura actual en Crevillente: <strong>' + datos.current_weather.temperature + '°C</strong>');
});
</script>

<?php include_once 'static/footer.php'; ?>