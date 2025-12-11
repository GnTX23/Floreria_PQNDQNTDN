<?php
require_once __DIR__ . "/database/config.php";

verificarSesion();

if (!isset($_SESSION['pedido_exitoso'])) {
    header("Location: Home.php");
    exit();
}

$pedido = $_SESSION['pedido_exitoso'];
unset($_SESSION['pedido_exitoso']); // Limpiar después de mostrar
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pedido Confirmado | Pa'que no digas que no te di nada</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .confirmacion-container {
      max-width: 700px;
      margin: 3rem auto;
      padding: 0 20px;
    }
    
    .success-box {
      background: white;
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.1);
      text-align: center;
    }
    
    .success-icon {
      width: 80px;
      height: 80px;
      background: #27ae60;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 25px;
      font-size: 40px;
      color: white;
      animation: scaleIn 0.5s ease;
    }
    
    @keyframes scaleIn {
      from { transform: scale(0); }
      to { transform: scale(1); }
    }
    
    .pedido-numero {
      background: #fff7f9;
      padding: 20px;
      border-radius: 10px;
      margin: 25px 0;
      border-left: 4px solid #841F2B;
    }
    
    .detalle-pedido {
      text-align: left;
      margin: 30px 0;
      padding: 20px;
      background: #f8f9fa;
      border-radius: 10px;
    }
    
    .producto-item {
      display: flex;
      justify-content: space-between;
      padding: 10px 0;
      border-bottom: 1px solid #ddd;
    }
    
    .total-pedido {
      display: flex;
      justify-content: space-between;
      padding: 20px 0;
      font-size: 1.3rem;
      font-weight: bold;
      color: #841F2B;
      border-top: 2px solid #841F2B;
      margin-top: 15px;
    }
    
    .acciones {
      display: flex;
      gap: 15px;
      margin-top: 30px;
      flex-wrap: wrap;
      justify-content: center;
    }
    
    .btn-accion {
      padding: 12px 30px;
      border-radius: 10px;
      text-decoration: none;
      font-weight: bold;
      transition: all 0.3s;
    }
    
    .btn-primario {
      background: #841F2B;
      color: white;
    }
    
    .btn-primario:hover {
      background: #661922;
    }
    
    .btn-secundario {
      background: white;
      color: #841F2B;
      border: 2px solid #841F2B;
    }
    
    .btn-secundario:hover {
      background: #fff7f9;
    }
  </style>
</head>
<body>

  <header class="header">
    <div class="logo">Pa´que no digas que no te di nada</div>
    <nav class="nav">
      <a href="Home.php">Inicio</a>
      <a href="catalogo.php">Catálogo</a>
      <a href="contacto.php">Rosita</a>
    </nav>
    <a href="loguearse/logout.php" class="login-btn">Cerrar sesión</a>
  </header>

  <div class="confirmacion-container">
    <div class="success-box">
      <div class="success-icon">✓</div>
      
      <h1 style="color: #27ae60; margin-bottom: 15px;">¡Pedido Confirmado!</h1>
      <p style="color: #666; font-size: 1.1rem;">Tu compra se ha procesado exitosamente</p>
      
      <div class="pedido-numero">
        <div style="color: #666; font-size: 14px; margin-bottom: 5px;">Número de pedido</div>
        <div style="color: #841F2B; font-size: 24px; font-weight: bold;">#<?php echo str_pad($pedido['pedido_id'], 6, '0', STR_PAD_LEFT); ?></div>
      </div>
      
      <div class="detalle-pedido">
        <h3 style="color: #841F2B; margin-bottom: 15px;">Detalle del Pedido</h3>
        
        <?php foreach ($pedido['productos'] as $producto): ?>
          <div class="producto-item">
            <div>
              <div style="font-weight: 600;"><?php echo htmlspecialchars($producto['nombre']); ?></div>
              <div style="font-size: 13px; color: #666;">
                <?php echo $producto['cantidad']; ?> x $<?php echo number_format($producto['precio'], 2); ?>
              </div>
            </div>
            <div style="font-weight: 600; color: #841F2B;">
              $<?php echo number_format($producto['subtotal'], 2); ?>
            </div>
          </div>
        <?php endforeach; ?>
        
        <div class="total-pedido">
          <span>Total Pagado:</span>
          <span>$<?php echo number_format($pedido['total'], 2); ?> MXN</span>
        </div>
      </div>
      
      <div style="background: #e8f8f5; padding: 20px; border-radius: 10px; margin: 25px 0;">
        <div style="color: #27ae60; font-weight: bold; margin-bottom: 10px;">📦 Información de Envío</div>
        <div style="color: #666; font-size: 14px;">
          <strong>Dirección:</strong><br>
          <?php echo nl2br(htmlspecialchars($pedido['direccion'])); ?>
        </div>
        <div style="color: #666; font-size: 14px; margin-top: 10px;">
          <strong>Tiempo estimado:</strong> 1-3 días hábiles
        </div>
      </div>
      
      <div class="acciones">
        <a href="catalogo.php" class="btn-accion btn-primario">Seguir Comprando</a>
    </div>
  </div>

  <footer class="footer">
    <p>© 2025 Pa´que no digas que no te di nada.</p>
    <p>Hecho con 💖 para los amantes de las flores.</p>
  </footer>

</body>
</html>