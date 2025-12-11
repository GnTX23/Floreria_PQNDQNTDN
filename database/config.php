<?php
// 1. FUNCION PARA CONECTAR A LA BASE DE DATOS
function conectarDB() {
    // Intentamos leer las variables de Railway
    $host = getenv('MYSQLHOST');
    $user = getenv('MYSQLUSER');
    $pass = getenv('MYSQLPASSWORD');
    $port = getenv('MYSQLPORT');
    $db   = getenv('MYSQLDATABASE');

    // Respaldo para variables alternativas (por si acaso)
    if (empty($host)) $host = getenv('DB_HOST');
    if (empty($user)) $user = getenv('DB_USER');
    if (empty($pass)) $pass = getenv('DB_PASSWORD');
    if (empty($port)) $port = getenv('DB_PORT');
    if (empty($db))   $db   = getenv('DB_NAME');

    
    $conn = @new mysqli($host, $user, $pass, $db, $port);

    if ($conn->connect_error) {
        die("<h1>Error de Conexión</h1><p>" . $conn->connect_error . "</p>");
    }

    $conn->set_charset("utf8");
    return $conn;
}

// 2. FUNCION PARA PROTEGER PÁGINAS (La que te falta)
function verificarSesion() {
    // Iniciar sesión si no está iniciada
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Si el usuario NO está logueado, lo mandamos al login
    if (!isset($_SESSION['cliente_id'])) {
        // Detectar si estamos en una subcarpeta o en la raíz para saber cómo volver
        if (file_exists('loguearse/login.php')) {
            // Estamos en la raíz (ej. catalogo.php)
            header("Location: loguearse/login.php");
        } else {
            // Estamos en una carpeta (ej. script/carrito.php)
            header("Location: ../loguearse/login.php");
        }
        exit();
    }
}
?>