<?php
require_once __DIR__ . '/../database/config.php';

verificarSesion(); // Solo usuarios logueados
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Rosita - Asistente Virtual</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: Arial, sans-serif;
      background: #f8f9fa;
      display: flex;
      flex-direction: column;
      height: 100vh;
      overflow: hidden;
    }
    header {
      background: #da727f;
      padding: 12px 20px;
      color: #fff;
      font-size: 1.2rem;
      font-weight: bold;
      text-align: center;
      letter-spacing: 0.5px;
    }
    iframe { flex: 1; width: 100%; height: 100%; border: none; }
  </style>
</head>
<body>
  <header>
    Chatea con Rosita 🌹<br>
    <small style="font-size: 0.8rem; opacity: 0.9;">
      Hola, <?php echo htmlspecialchars($_SESSION['cliente_nombre']); ?>
    </small>
  </header>

  <iframe
    src="https://www.stack-ai.com/chat/6662745bde3c3fa0075290fd-42CUiWgmuyNXSISTwKZk1s"
    allow="camera; microphone; autoplay">
  </iframe>
</body>
</html>