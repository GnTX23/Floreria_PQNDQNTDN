<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuración Railway
define('DB_HOST', 'caboose.proxy.rlwy.net');
define('DB_USER', 'root');
define('DB_PASS', 'zrujEdYSgTqSeUDjWWFeAYbVifgPJvT');
define('DB_NAME', 'railway');
define('DB_PORT', 15501);

function conectarDB() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    $conn->set_charset("utf8mb4");
    return $conn;
}
?>
