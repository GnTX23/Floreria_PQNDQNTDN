<?php
require_once __DIR__ . '/../database/config.php';
verificarSesion();

$conn = conectarDB();

$carrito = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];
$productos_carrito = [];
$total = 0;

if (!empty($carrito)) {
    $ids = implode(',', array_keys($carrito));
    $sql = "SELECT * FROM flores WHERE id IN ($ids)";
    $result = $conn->query($sql);
    
    while ($flor = $result->fetch_assoc()) {
        $flor['cantidad'] = $carrito[$flor['id']];
        $flor['subtotal'] = $flor['precio'] * $flor['cantidad'];
        $total += $flor['subtotal'];
        $productos_carrito[] = $flor;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Carrito - Pa´que no digas que no te di nada</title>
  <link rel="stylesheet" href="../../style.css" />
</head>
<body>

  <header class="header">
    <div class="logo">Pa´que no digas que no te di nada</div>
    <nav class="nav">
      <a href="../../Home.php">Inicio</a>
      <a href="../../catalogo.php">Catálogo</a>
      <a href="carrito.php">Carrito 🛒</a>
      <a href="../../contacto.php">Rosita</a>
    </nav>
    <a href="../../loguearse/logout.php" class="login-btn">Cerrar Sesión</a>
  </header>

  <section class="catalogo">
    <h2>Mi Carrito de Compras 🛒</h2>

    <?php if (empty($productos_carrito)): ?>
      <div style="text-align: center; padding: 50px;">
        <p style="font-size: 1.2rem; color: #666;">Tu carrito está vacío</p>
        <a href="../../catalogo.php" class="cta-btn" style="display: inline-block; margin-top: 20px;">
          Ver Catálogo
        </a>
      </div>
    <?php else: ?>
      <div class="lista-carrito">
        <?php foreach ($productos_carrito as $item): ?>
          <div class="item-carrito">
            <img src="<?php echo htmlspecialchars($item['imagen']); ?>" alt="<?php echo htmlspecialchars($item['nombre']); ?>">
            <div style="flex: 1;">
              <h3 style="color: #841F2B; margin-bottom: 5px;"><?php echo htmlspecialchars($item['nombre']); ?></h3>
              <p style="color: #666; font-size: 14px;"><?php echo htmlspecialchars($item['descripcion']); ?></p>
              <p style="color: #bb1c2f; font-weight: bold; margin-top: 10px;">
                $<?php echo number_format($item['precio'], 2); ?> MXN x <?php echo $item['cantidad']; ?> 
                = $<?php echo number_format($item['subtotal'], 2); ?> MXN
              </p>
            </div>
            <div style="display: flex; flex-direction: column; gap: 10px;">
              <button class="cta-btn" onclick="cambiarCantidad(<?php echo $item['id']; ?>, 1)" style="padding: 0.3rem 0.8rem;">
                +
              </button>
              <button class="cta-btn" onclick="cambiarCantidad(<?php echo $item['id']; ?>, -1)" style="padding: 0.3rem 0.8rem;">
                -
              </button>
              <button class="eliminar-btn" onclick="eliminarProducto(<?php echo $item['id']; ?>)">
                Eliminar
              </button>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="total-container">
        <p style="font-size: 1.8rem; color: #841F2B; font-weight: bold;">
          Total: $<?php echo number_format($total, 2); ?> MXN
        </p>
        <a href="../../pago.php" class="cta-btn" style="display: inline-block; margin-top: 20px; text-decoration: none;">
          Proceder al Pago
        </a>
      </div>
    <?php endif; ?>
  </section>

  <footer class="footer">
    <p>© 2025 Pa´que no digas que no te di nada.</p>
    <p>Hecho con 💖 para los amantes de las flores.</p>
  </footer>

  <script>
    function cambiarCantidad(florId, cambio) {
      fetch('actualizar_carrito.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `flor_id=${florId}&cambio=${cambio}`
      })
      .then(() => location.reload());
    }

    function eliminarProducto(florId) {
      if (confirm('¿Eliminar este producto del carrito?')) {
        fetch('actualizar_carrito.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: `flor_id=${florId}&eliminar=1`
        })
        .then(() => location.reload());
      }
    }
  </script>

</body>
</html>