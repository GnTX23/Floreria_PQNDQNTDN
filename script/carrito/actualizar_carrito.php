<?php
require_once __DIR__ . "/../../database/config.php";


verificarSesion();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['flor_id'])) {
    $flor_id = intval($_POST['flor_id']);
    
    if (isset($_POST['eliminar'])) {
        unset($_SESSION['carrito'][$flor_id]);
    } elseif (isset($_POST['cambio'])) {
        $cambio = intval($_POST['cambio']);
        if (isset($_SESSION['carrito'][$flor_id])) {
            $_SESSION['carrito'][$flor_id] += $cambio;
            if ($_SESSION['carrito'][$flor_id] <= 0) {
                unset($_SESSION['carrito'][$flor_id]);
            }
        }
    }
    
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>