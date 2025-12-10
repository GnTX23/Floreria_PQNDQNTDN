<?php
function conectarDB() {
    // Intentamos obtener las variables de entorno de Railway
    // Si no existen (porque estás en local), usamos valores por defecto (localhost)
    
    $host = getenv('MYSQLHOST');
    $user = getenv('MYSQLUSER');
    $pass = getenv('MYSQLPASSWORD');
    $db   = getenv('MYSQLDATABASE');
    $port = getenv('MYSQLPORT');


    // Crear la conexión
    $conn = new mysqli($host, $user, $pass, $db, $port);

    // Verificar si hubo error
    if ($conn->connect_error) {
        // En producción (Railway) no queremos mostrar el error técnico exacto al usuario, 
        // pero para depurar ahora mismo, dejémoslo así:
        die("Error de conexión (Fatal): " . $conn->connect_error);
    }

    // Configurar charset a utf8 para que salgan bien las ñ y acentos
    $conn->set_charset("utf8");

    return $conn;
}
?>