<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '12345'); 
define('DB_NAME', 'floristeria');

function conectarDB() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");
    return $conn;
}

session_start();

function verificarSesion() {
    if (!isset($_SESSION['cliente_id'])) {
        header("Location: /codigo/loguearse/login.php");
        exit();
    }
}
?>