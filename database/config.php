<?php
function conectarDB() {
    // 1. Intentamos leer las variables EXACTAS que tienes en Railway
    // Prioridad 1: Las variables MYSQL... que se ven en tu foto
    $host = getenv('MYSQLHOST');
    $user = getenv('MYSQLUSER');
    $pass = getenv('MYSQLPASSWORD');
    $port = getenv('MYSQLPORT');
    $db   = getenv('MYSQLDATABASE');

    // Prioridad 2: Si por alguna razón fallan, intentamos con DB_... (que también vi en tu foto)
    if (empty($host)) $host = getenv('DB_HOST');
    if (empty($user)) $user = getenv('DB_USER');
    if (empty($pass)) $pass = getenv('DB_PASSWORD');
    if (empty($port)) $port = getenv('DB_PORT');
    if (empty($db))   $db   = getenv('DB_NAME');

    // 2. MODO LOCAL (XAMPP)
    // Si no estamos en Railway (host vacío), usamos la config de tu PC
    if (empty($host)) {
        $host = 'localhost';
        $user = 'root';
        $pass = ''; 
        $db   = 'nombre_de_tu_base_de_datos'; // <--- CAMBIA ESTO POR TU DB LOCAL
        $port = 3306;
    }

    // 3. DIAGNÓSTICO DE ERRORES (Esto te dirá qué pasa si falla)
    if ($host !== 'localhost') {
        if (empty($host) || empty($user) || empty($db)) {
            // Esto imprimirá una tabla en pantalla con lo que está leyendo PHP
            echo "<h3>Error de Variables de Entorno en Railway</h3>";
            echo "PHP no está recibiendo los datos. Estado actual:<br>";
            echo "<pre>";
            echo "Host (MYSQLHOST): [" . getenv('MYSQLHOST') . "]<br>";
            echo "User (MYSQLUSER): [" . getenv('MYSQLUSER') . "]<br>";
            echo "Port (MYSQLPORT): [" . getenv('MYSQLPORT') . "]<br>";
            echo "</pre>";
            die("Deteniendo ejecución para proteger credenciales.");
        }
    }

    // 4. Conectar
    // El @ oculta el error técnico feo, nosotros lo manejamos abajo
    $conn = @new mysqli($host, $user, $pass, $db, $port);

    if ($conn->connect_error) {
        die("<h1>Error de Conexión a la Base de Datos</h1>
             <p><strong>Mensaje:</strong> " . $conn->connect_error . "</p>
             <p><strong>Intentando conectar a:</strong> $host</p>
             <p><strong>Usuario:</strong> $user</p>
             <hr>
             <p>Verifica que las variables en Railway coincidan con estos valores.</p>");
    }

    $conn->set_charset("utf8");
    return $conn;

    // --- FUNCIONES DE SESIÓN ---

function verificarSesion() {
    // Si la sesión no está iniciada, la iniciamos
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Si el usuario NO tiene un ID en la sesión, lo mandamos al login
    // Ajusta la ruta "../loguearse/login.php" si catalogo.php está en otra carpeta,
    // pero generalmente para archivos en la raíz suele ser "loguearse/login.php".
    if (!isset($_SESSION['cliente_id'])) {
        // Detectamos dónde estamos para saber cómo regresar al login
        // Si estamos en la raíz (ej. catalogo.php)
        if (file_exists('loguearse/login.php')) {
            header("Location: loguearse/login.php");
        } 
        // Si estamos en una subcarpeta (ej. scripts/carrito.php)
        else {
            header("Location: ../loguearse/login.php");
        }
        exit();
    }
}
}
?>