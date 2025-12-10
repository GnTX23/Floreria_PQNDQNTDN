<?php
require_once __DIR__ . '/../database/config.php';

$error = '';
$success = '';

// Mostrar mensaje de registro exitoso (enviado desde registro.php)
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = conectarDB();

    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT cliente_id, nombre, email, password FROM clientes WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $cliente = $resultado->fetch_assoc();

        if (password_verify($password, $cliente['password'])) {

            // Guardar sesión
            $_SESSION['cliente_id'] = $cliente['cliente_id'];
            $_SESSION['cliente_nombre'] = $cliente['nombre'];
            $_SESSION['email'] = $cliente['email'];

            // Redirección limpia y sin errores
            header("Location: ../catalogo.php");
            exit();


        } else {
            $error = "Contraseña incorrecta";
        }

    } else {
        $error = "Usuario no encontrado";
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Floristería</title>
  <link rel="stylesheet" href="../style.css" />
</head>
<body>

  <header class="header">
    <div class="logo">Pa´que no digas que no te di nada</div>
    <nav class="nav">
      <a href="../Home.php">Inicio</a>
      <a href="../loguearse/login.php">Rosita</a>
    </nav>
  </header>

  <div class="form-box">
    <h2>Iniciar sesión</h2>

    <?php if ($error): ?>
      <p style="color: #bb1c2f; text-align:center; background: #ffe6e6; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
        <?= htmlspecialchars($error) ?>
      </p>
    <?php endif; ?>

    <?php if ($success): ?>
      <p style="color: #27ae60; text-align:center; background: #e8f8f5; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
        <?= htmlspecialchars($success) ?>
      </p>
    <?php endif; ?>

    <form method="POST" action="">
      <input type="email" placeholder="Correo electrónico" name="email" required>
      <input type="password" placeholder="Contraseña" name="password" required>
      <button class="form-btn" type="submit" name="login">Acceder</button>
    </form>

    <p style="text-align:center; margin-top:12px;">¿No tienes cuenta? 
        <a href="../registros/registro.php">Regístrate</a>
    </p>
  </div>

  <footer class="footer">
    <p>© 2025 Pa´que no digas que no te di nada.</p>
    <p>Hecho con 💖 para los amantes de las flores.</p>
  </footer>

</body>
</html>
