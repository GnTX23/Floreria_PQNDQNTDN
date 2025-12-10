<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once "../database/config.php";

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $conn = conectarDB();

    $nombre = $conn->real_escape_string($_POST['nombre']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $telefono = $conn->real_escape_string($_POST['telefono']);
    $direccion = '';

    // Verificar correo existente
    $stmt_check = $conn->prepare("SELECT cliente_id FROM clientes WHERE email = ?");
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        $error = "Este email ya está registrado";
    } else {

        // Registrar
        $stmt = $conn->prepare(
            "INSERT INTO clientes (nombre, email, password, telefono, direccion)
            VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("sssss", $nombre, $email, $password, $telefono, $direccion);

        if ($stmt->execute()) {
            // Guardar el mensaje en sesión para evitar usar header + echo
            $_SESSION['success'] = "¡Registro exitoso! Inicia sesión para continuar.";
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
