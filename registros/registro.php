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

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Florería</title>
    <link rel="stylesheet" href="../style.css"> 
    <style>
        /* Estilos rápidos por si falla el CSS externo */
        body { font-family: sans-serif; display: flex; justify-content: center; padding-top: 50px; }
        .form-container { width: 100%; max-width: 400px; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        .error { color: red; background: #ffe6e6; padding: 10px; margin-bottom: 10px; border-radius: 4px; }
        input { width: 100%; padding: 10px; margin: 10px 0; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #28a745; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Crear Cuenta</h2>

    <?php if (!empty($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="registro.php" method="POST">
        <label>Nombre:</label>
        <input type="text" name="nombre" required>

        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Teléfono:</label>
        <input type="text" name="telefono" required>

        <label>Contraseña:</label>
        <input type="password" name="password" required>

        <button type="submit">Registrarse</button>
    </form>
    
    <p style="text-align: center; margin-top: 15px;">
        ¿Ya tienes cuenta? <a href="../loguearse/login.php">Inicia sesión aquí</a>
    </p>
</div>

</body>
</html>