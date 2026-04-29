<?php
    function generarSelect($conexion, $tabla, $columna, $nombreSelector, $valorSeleccionado = '', $mostrarTodas = true) {
        $html = "<select name='$nombreSelector'>\n";
        if ($mostrarTodas) {
            $html .= " <option value='TODAS'>TODAS</option>\n";
        }

        try {
            $sql = "SELECT DISTINCT $columna FROM $tabla ORDER BY $columna";
            $stmt = $conexion->prepare($sql);
            $stmt->execute();

            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $fila) {
                $opcion = htmlspecialchars($fila[$columna]);
                $selected = ($valorSeleccionado === $opcion) ? " selected" : "";
                $html .= " <option value='$opcion'$selected>$opcion</option>\n";
            }
        } catch (Exception $e) {
            $html .= "<option value=''>Error al cargar opciones</option>\n";
        }

        $html .= "</select>\n";
        return $html;
    }
?>