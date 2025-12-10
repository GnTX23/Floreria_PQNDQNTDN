<?php
// 1. INICIAR SESIÓN (Obligatorio para usar $_SESSION)
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../database/config.php';

$error = '';
$success = '';

// 2. LOGICA PHP (Solo se ejecuta si se envió el formulario)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $conn = conectarDB();

    // Validar que los campos existan antes de usarlos
    $nombre = isset($_POST['nombre']) ? $conn->real_escape_string($_POST['nombre']) : '';
    $email = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : '';
    $password_raw = isset($_POST['password']) ? $_POST['password'] : '';
    $telefono = isset($_POST['telefono']) ? $conn->real_escape_string($_POST['telefono']) : '';
    $direccion = ''; // Dejar vacío o agregar campo en el form

    // Verificar correo existente
    $stmt_check = $conn->prepare("SELECT cliente_id FROM clientes WHERE email = ?");
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        $error = "Este email ya está registrado";
    } else {
        // Registrar
        $password = password_hash($password_raw, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare(
            "INSERT INTO clientes (nombre, email, password, telefono, direccion) 
            VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("sssss", $nombre, $email, $password, $telefono, $direccion);

        if ($stmt->execute()) {
            $_SESSION['success'] = "¡Registro exitoso! Inicia sesión.";
            header("Location: ../loguearse/login.php");
            exit(); 
        } else {
            $error = "Error al registrar: " . $conn->error;
        }
        $stmt->close();
    }

    $stmt_check->close();
    $conn->close();
}
?>

