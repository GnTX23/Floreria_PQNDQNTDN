<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/database/config.php";

echo "config cargado correctamente<br>";

$conn = conectarDB();

echo "Conexión a Railway EXITOSA";
