
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once "../database/config.php";

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = conectarDB();
    
    if (isset($_POST['registro'])) {
        $nombre = $conn->real_escape_string($_POST['nombre']);
        $email = $conn->real_escape_string($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $telefono = $conn->real_escape_string($_POST['telefono']);
        $direccion = '';
        
        // Verificar si el email ya existe
        $sql_check = "SELECT cliente_id FROM clientes WHERE email = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        
        if ($result_check->num_rows > 0) {
            $error = "Este email ya está registrado";
        } else {
            $sql = "INSERT INTO clientes (nombre, email, password, telefono, direccion) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $nombre, $email, $password, $telefono, $direccion);
            
            if ($stmt->execute()) {
                $success = "¡Registro exitoso! Redirigiendo al login...";
                header("refresh:2;url=/codigo/loguearse/login.php");
            } else {
                $error = "Error al registrar: " . $conn->error;
            }
            $stmt->close();
        }
        $stmt_check->close();
    }
    
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro - Floristería</title>
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
    <h2>Crear cuenta</h2>

    <?php if ($error): ?>
      <p style="color: #bb1c2f; text-align:center; background: #ffe6e6; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
        <?php echo htmlspecialchars($error); ?>
      </p>
    <?php endif; ?>

    <?php if ($success): ?>
      <p style="color: #27ae60; text-align:center; background: #e8f8f5; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
        <?php echo htmlspecialchars($success); ?>
      </p>
    <?php endif; ?>

    <form method="POST" action="">
      <input type="text" placeholder="Nombre completo" name="nombre" required>
      <input type="email" placeholder="Correo electrónico" name="email" required>
      <input type="password" placeholder="Contraseña (mínimo 6 caracteres)" name="password" required minlength="6">
      <input type="tel" placeholder="Número telefónico" name="telefono">
      
      <button class="form-btn" type="submit" name="registro">Registrarme</button>
    </form>

    <p style="text-align:center; margin-top:12px;">¿Ya tienes cuenta? <a href="../loguerse/login.php">Inicia sesión</a></p>
  </div>

  <footer class="footer">
    <p>© 2025 Pa´que no digas que no te di nada.</p>
    <p>Hecho con 💖 para los amantes de las flores.</p>
  </footer>

</body>
</html>