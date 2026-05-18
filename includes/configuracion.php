<?php

    if ($_SERVER['HTTP_HOST'] === 'localhost') {
    // Configuración local
    $host = 'localhost';
    $usuario = 'root';
    $password = '';
    $bd = 'tienda_padel';
} else {
    // Configuración InfinityFree
    $host = 'sql208.infinityfree.com'; // el host que te dio InfinityFree
    $usuario = 'if0_41953172';
    $password = 'tu_password_bd';
    $bd = 'tu_nombre_bd';
}

?>