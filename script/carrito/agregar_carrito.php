<?php
require_once "../../database/config.php";
verificarSesion();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['flor_id'])) {
    $flor_id = intval($_POST['flor_id']);
    
    // Inicializar carrito en sesión si no existe
    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }
    
    // Agregar al carrito (o incrementar cantidad)
    if (isset($_SESSION['carrito'][$flor_id])) {
        $_SESSION['carrito'][$flor_id]++;
    } else {
        $_SESSION['carrito'][$flor_id] = 1;
    }
    
    echo json_encode(['success' => true, 'message' => 'Producto agregado']);
} else {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
}
?>