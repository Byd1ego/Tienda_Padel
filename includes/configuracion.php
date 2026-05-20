
<?php
//a los buenos dias
if ($_SERVER['HTTP_HOST'] === 'localhost') {
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'tienda_padel');
} else {
    define('DB_HOST', 'sql107.infinityfree.com');
    define('DB_USER', 'if0_41953172');
    define('DB_PASS', 'IOpyoTtBZO6JCSd');
    define('DB_NAME', 'if0_41953172_tienda_padel');
}

define('DB_CHARSET', 'utf8');
?>