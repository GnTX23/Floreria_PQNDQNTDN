<?php
// --- LÓGICA PHP (NO TOCAR) ---
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Ajusta la ruta si es necesario
require_once __DIR__ . '/../database/config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = conectarDB();

    $nombre = isset($_POST['nombre']) ? $conn->real_escape_string($_POST['nombre']) : '';
    $email = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : '';
    $password_raw = isset($_POST['password']) ? $_POST['password'] : '';
    $telefono = isset($_POST['telefono']) ? $conn->real_escape_string($_POST['telefono']) : '';
    $direccion = ''; 

    $stmt_check = $conn->prepare("SELECT cliente_id FROM clientes WHERE email = ?");
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        $error = "Este email ya está registrado";
    } else {
        $password = password_hash($password_raw, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO clientes (nombre, email, password, telefono, direccion) VALUES (?, ?, ?, ?, ?)");
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
// --- FIN LÓGICA PHP ---
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Crear Cuenta | Pa'que no digas que no te di nada</title>
  <link rel="stylesheet" href="../style.css" />
  
  <style>
    /* Estilos específicos para centrar el formulario manteniendo el diseño */
    .registro-section {
        background-color: #f9f9f9; /* Fondo suave */
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
    }
    .form-card {
        background: white;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        width: 100%;
        max-width: 450px;
        text-align: center;
    }
    .form-card h2 {
        color: #841F2B; /* Tu color rojo vino */
        margin-bottom: 20px;
    }
    .input-group {
        margin-bottom: 15px;
        text-align: left;
    }
    .input-group label {
        display: block;
        margin-bottom: 5px;
        color: #333;
        font-weight: bold;
    }
    .input-group input {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-sizing: border-box; /* Para que el padding no rompa el ancho */
    }
    .btn-submit {
        background-color: #bb1c2f; /* Tu color rojo brillante */
        color: white;
        padding: 15px;
        width: 100%;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: background 0.3s;
        margin-top: 10px;
    }
    .btn-submit:hover {
        background-color: #841F2B;
    }
    .error-msg {
        background-color: #ffe6e6;
        color: #d8000c;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 15px;
        border: 1px solid #d8000c;
    }
  </style>
</head>

<body>
  <header class="header">
    <div class="logo">Pa´que no digas que no te di nada</div>
    <nav class="nav">
      <a href="../Home.php">Inicio</a>
      <a href="../loguearse/login.php">Catálogo</a>
      <a href="../loguearse/login.php">Carrito</a>
      <a href="../loguearse/login.php">Rosita</a>
    </nav>
    
    <a href="../loguearse/login.php" class="login-btn">Iniciar sesión</a>
  </header>

  <section class="registro-section">
      <div class="form-card">
          <h2>Únete a nosotros</h2>
          <p style="color: #666; margin-bottom: 25px;">Crea tu cuenta para enviar detalles inolvidables.</p>

          <?php if (!empty($error)): ?>
              <div class="error-msg"><?php echo $error; ?></div>
          <?php endif; ?>

          <form action="registro.php" method="POST">
              <div class="input-group">
                  <label for="nombre">Nombre completo</label>
                  <input type="text" id="nombre" name="nombre" placeholder="Ej. Juan Pérez" required>
              </div>

              <div class="input-group">
                  <label for="email">Correo electrónico</label>
                  <input type="email" id="email" name="email" placeholder="correo@ejemplo.com" required>
              </div>

              <div class="input-group">
                  <label for="telefono">Teléfono</label>
                  <input type="text" id="telefono" name="telefono" placeholder="Ej. 55 1234 5678" required>
              </div>

              <div class="input-group">
                  <label for="password">Contraseña</label>
                  <input type="password" id="password" name="password" placeholder="********" required>
              </div>

              <button type="submit" class="btn-submit">Crear Cuenta Gratis</button>
          </form>

          <p style="margin-top: 20px; font-size: 14px;">
              ¿Ya tienes cuenta? <a href="../loguearse/login.php" style="color: #841F2B; font-weight: bold;">Inicia sesión aquí</a>
          </p>
      </div>
  </section>

  <footer class="footer">
    <p>© 2025 Pa´que no digas que no te di nada.</p>
    <p>Hecho con 💖 para los amantes de las flores.</p>
  </footer>

</body>
</html>