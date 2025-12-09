<?php
require_once "database/config.php";
verificarSesion();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: pago.php");
    exit();
}

$conn = conectarDB();

// Obtener datos del formulario
$nombre_destinatario = $conn->real_escape_string($_POST['nombre_destinatario']);
$direccion = $conn->real_escape_string($_POST['direccion']);
$telefono = $conn->real_escape_string($_POST['telefono']);
$codigo_postal = $conn->real_escape_string($_POST['codigo_postal']);
$mensaje = $conn->real_escape_string($_POST['mensaje'] ?? '');
$metodo_pago = $conn->real_escape_string($_POST['metodo_pago']);

// Datos de tarjeta (si aplica) - NO se guardan por seguridad
$numero_tarjeta = isset($_POST['numero_tarjeta']) ? substr($_POST['numero_tarjeta'], -4) : null; // Solo últimos 4 dígitos

// Obtener carrito
$carrito = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];

if (empty($carrito)) {
    header("Location: script/carrito/carrito.php");
    exit();
}

// Calcular total
$total = 0;
$ids = implode(',', array_keys($carrito));
$sql = "SELECT * FROM flores WHERE id IN ($ids)";
$result = $conn->query($sql);

$productos = [];
while ($flor = $result->fetch_assoc()) {
    $cantidad = $carrito[$flor['id']];
    $subtotal = $flor['precio'] * $cantidad;
    $total += $subtotal;
    $productos[] = [
        'id' => $flor['id'],
        'nombre' => $flor['nombre'],
        'precio' => $flor['precio'],
        'cantidad' => $cantidad,
        'subtotal' => $subtotal
    ];
}

// Iniciar transacción
$conn->begin_transaction();

try {
    // 1. Insertar pedido
    $direccion_completa = $direccion . " | CP: " . $codigo_postal . " | Tel: " . $telefono;
    if ($mensaje) {
        $direccion_completa .= " | Mensaje: " . $mensaje;
    }
    
    $sql_pedido = "INSERT INTO pedidos (cliente_id, total, estado, direccion_envio) VALUES (?, ?, 'procesando', ?)";
    $stmt_pedido = $conn->prepare($sql_pedido);
    $stmt_pedido->bind_param("ids", $_SESSION['cliente_id'], $total, $direccion_completa);
    $stmt_pedido->execute();
    $pedido_id = $conn->insert_id;
    $stmt_pedido->close();
    
    // 2. Insertar detalles del pedido
    $sql_detalle = "INSERT INTO detalle_pedidos (pedido_id, flor_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)";
    $stmt_detalle = $conn->prepare($sql_detalle);
    
    foreach ($productos as $producto) {
        $stmt_detalle->bind_param("iiid", $pedido_id, $producto['id'], $producto['cantidad'], $producto['precio']);
        $stmt_detalle->execute();
        
        // 3. Actualizar stock
        $sql_stock = "UPDATE flores SET stock = stock - ? WHERE id = ?";
        $stmt_stock = $conn->prepare($sql_stock);
        $stmt_stock->bind_param("ii", $producto['cantidad'], $producto['id']);
        $stmt_stock->execute();
        $stmt_stock->close();
    }
    
    $stmt_detalle->close();
    
    // Confirmar transacción
    $conn->commit();
    
    // Limpiar carrito
    unset($_SESSION['carrito']);
    
    // Guardar datos para la confirmación
    $_SESSION['pedido_exitoso'] = [
        'pedido_id' => $pedido_id,
        'total' => $total,
        'metodo_pago' => $metodo_pago,
        'direccion' => $direccion,
        'productos' => $productos
    ];
    
    // Redirigir a confirmación
    header("Location: confirmacion_pedido.php");
    exit();
    
} catch (Exception $e) {
    // Revertir cambios si hay error
    $conn->rollback();
    $_SESSION['error_pago'] = "Error al procesar el pago: " . $e->getMessage();
    header("Location: pago.php");
    exit();
}

$conn->close();
?>