<?php
// Configuración para Railway (producción)
define('DB_HOST', 'caboose.proxy.rlwy.net'); 
define('DB_PORT', '15501');                    
define('DB_USER', 'root');                     
define('DB_PASS', 'zrujEdYSgTqSeUDjWWFeAYbVfifgPJvT');  
define('DB_NAME', 'railway');                  
function conectarDB() {
    $conn = new mysqli(DB_HOST, DB_PORT, DB_USER, DB_PASS, DB_NAME); 
    
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");
    return $conn;
}

session_start();

function verificarSesion() {
    if (!isset($_SESSION['cliente_id'])) {
        $ruta_login = $_SERVER['REQUEST_URI'];
        
        if (strpos($ruta_login, '/loguearse/') !== false || strpos($ruta_login, '/registros/') !== false) {
            header("Location: ../loguearse/login.php");
        } elseif (strpos($ruta_login, '/script/carrito/') !== false) {
            header("Location: ../../loguearse/login.php");
        } else {
            header("Location: loguearse/login.php");
        }
        exit();
    }
}
?>