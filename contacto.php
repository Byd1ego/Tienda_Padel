<?php
include_once 'includes/header.php';
?>

<main>
    <div class="contacto-contenedor">

        <h1 class="contacto-titulo">Contacto</h1>

        <div class="contacto-bloque">

            <!-- FORMULARIO -->
            <div class="contacto-formulario">
                <h2>Envíanos un mensaje</h2>

                <div id="mensaje-respuesta"></div>

                <form id="formulario-contacto" class="formulario">
                    <div class="form-grupo">
                        <label>Nombre</label>
                        <input type="text" id="nombre" placeholder="Tu nombre" required>
                    </div>
                    <div class="form-grupo">
                        <label>Email</label>
                        <input type="email" id="email" placeholder="tu@email.com" required>
                    </div>
                    <div class="form-grupo">
                        <label>Mensaje</label>
                        <textarea id="mensaje" rows="5" placeholder="Escribe tu mensaje..." required></textarea>
                    </div>
                    <div class="form-botones">
                        <button type="submit" class="boton-nuevo">Enviar</button>
                    </div>
                </form>
            </div>

            <!-- DATOS -->
            <div class="contacto-info">
                <h2>Dónde estamos</h2>
                <p>📍 Calle del Pádel, 10 - Crevillente</p>
                <p>📞 <a href="tel:+34600000000">+34 600 000 000</a></p>
                <p>✉️ <a href="mailto:contacto@padelzone.com">contacto@padelzone.com</a></p>
                <p>🕐 Lunes - Viernes: 9:00 - 20:00</p>
            </div>

        </div><!-- fin contacto-bloque -->

    </div><!-- fin contacto-contenedor -->
</main>

<script>
    document.getElementById('formulario-contacto').addEventListener('submit', function(e) {
        e.preventDefault();
        document.getElementById('mensaje-respuesta').innerHTML =
            "<p class='contacto-ok'>✅ Mensaje enviado correctamente.</p>";
        this.reset();
    });
</script>

<?php
include_once 'includes/footer.php';
?>