<?php
require_once __DIR__ . "/database/config.php";


verificarSesion();

$conn = conectarDB();

// Obtener productos del carrito
$carrito = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];
$productos_carrito = [];
$total = 0;

if (empty($carrito)) {
    header("Location: script/carrito/carrito.php");
    exit();
}

$ids = implode(',', array_keys($carrito));
$sql = "SELECT * FROM flores WHERE id IN ($ids)";
$result = $conn->query($sql);

while ($flor = $result->fetch_assoc()) {
    $flor['cantidad'] = $carrito[$flor['id']];
    $flor['subtotal'] = $flor['precio'] * $flor['cantidad'];
    $total += $flor['subtotal'];
    $productos_carrito[] = $flor;
}

// Obtener datos del cliente
$sql_cliente = "SELECT * FROM clientes WHERE cliente_id = ?";
$stmt = $conn->prepare($sql_cliente);
$stmt->bind_param("i", $_SESSION['cliente_id']);
$stmt->execute();
$cliente = $stmt->get_result()->fetch_assoc();
$stmt->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Procesar Pago | Pa'que no digas que no te di nada</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .pago-container {
      max-width: 1000px;
      margin: 2rem auto;
      padding: 0 20px;
      display: grid;
      grid-template-columns: 1fr 400px;
      gap: 30px;
    }
    
    .pago-form {
      background: white;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }
    
    .pago-resumen {
      background: #fff7f9;
      padding: 25px;
      border-radius: 15px;
      box-shadow: 0 3px 10px rgba(0,0,0,0.1);
      height: fit-content;
      position: sticky;
      top: 20px;
    }
    
    .form-section {
      margin-bottom: 30px;
    }
    
    .form-section h3 {
      color: #841F2B;
      margin-bottom: 15px;
      padding-bottom: 10px;
      border-bottom: 2px solid #da727f;
    }
    
    .form-group {
      margin-bottom: 15px;
    }
    
    .form-group label {
      display: block;
      color: #333;
      font-weight: 600;
      margin-bottom: 5px;
    }
    
    .form-group input,
    .form-group select,
    .form-group textarea {
      width: 100%;
      padding: 12px;
      border: 1px solid #ddd;
      border-radius: 8px;
      font-size: 14px;
      transition: border-color 0.3s;
    }
    
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
      outline: none;
      border-color: #841F2B;
    }
    
    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 15px;
    }
    
    .tarjeta-visual {
      background: linear-gradient(135deg, #841F2B 0%, #da727f 100%);
      padding: 25px;
      border-radius: 15px;
      color: white;
      margin-bottom: 20px;
      box-shadow: 0 8px 20px rgba(132, 31, 43, 0.3);
    }
    
    .tarjeta-numero {
      font-size: 22px;
      letter-spacing: 3px;
      margin: 20px 0;
      font-family: 'Courier New', monospace;
    }
    
    .tarjeta-info {
      display: flex;
      justify-content: space-between;
      font-size: 14px;
    }
    
    .metodo-pago {
      display: flex;
      gap: 15px;
      flex-wrap: wrap;
    }
    
    .metodo-option {
      flex: 1;
      min-width: 150px;
      padding: 15px;
      border: 2px solid #ddd;
      border-radius: 10px;
      cursor: pointer;
      text-align: center;
      transition: all 0.3s;
    }
    
    .metodo-option:hover {
      border-color: #841F2B;
      background: #fff7f9;
    }
    
    .metodo-option input[type="radio"] {
      display: none;
    }
    
    .metodo-option input[type="radio"]:checked + label {
      color: #841F2B;
      font-weight: bold;
    }
    
    .metodo-option.active {
      border-color: #841F2B;
      background: #fff7f9;
    }
    
    .item-resumen {
      display: flex;
      justify-content: space-between;
      padding: 10px 0;
      border-bottom: 1px solid #eee;
    }
    
    .total-final {
      display: flex;
      justify-content: space-between;
      padding: 20px 0;
      font-size: 1.5rem;
      font-weight: bold;
      color: #841F2B;
      border-top: 2px solid #841F2B;
      margin-top: 15px;
    }
    
    .btn-pagar {
      width: 100%;
      padding: 15px;
      background: #841F2B;
      color: white;
      border: none;
      border-radius: 10px;
      font-size: 18px;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.3s;
    }
    
    .btn-pagar:hover {
      background: #661922;
    }
    
    .error {
      color: #e74c3c;
      font-size: 13px;
      margin-top: 5px;
      display: none;
    }
    
    .error.visible {
      display: block;
    }
    
    input.invalid {
      border-color: #e74c3c;
    }
    
    @media (max-width: 768px) {
      .pago-container {
        grid-template-columns: 1fr;
      }
      
      .pago-resumen {
        position: static;
      }
    }
  </style>
</head>
<body>

  <header class="header">
    <div class="logo">Pa´que no digas que no te di nada</div>
    <nav class="nav">
      <a href="Home.php">Inicio</a>
      <a href="script/carrito/carrito.php">Carrito</a>
      <a href="contacto.php">Rosita</a>
    </nav>
    <a href="loguearse/logout.php" class="login-btn">Cerrar sesión</a>
  </header>

  <div class="pago-container">
    <!-- Formulario de Pago -->
    <div class="pago-form">
      <h2 style="color: #841F2B; margin-bottom: 25px;">💳 Finalizar Compra</h2>
      
      <form id="form-pago" action="procesar_pago.php" method="POST">
        
        <!-- Información de Entrega -->
        <div class="form-section">
          <h3>📦 Información de Entrega</h3>
          
          <div class="form-group">
            <label>Nombre completo del destinatario *</label>
            <input type="text" name="nombre_destinatario" id="nombre_destinatario" 
                   value="<?php echo htmlspecialchars($cliente['nombre']); ?>" required>
            <span class="error" id="error-nombre">El nombre debe tener al menos 3 caracteres</span>
          </div>
          
          <div class="form-group">
            <label>Dirección completa *</label>
            <textarea name="direccion" id="direccion" rows="3" required 
                      placeholder="Calle, número, colonia, ciudad"><?php echo htmlspecialchars($cliente['direccion']); ?></textarea>
            <span class="error" id="error-direccion">La dirección debe tener al menos 10 caracteres</span>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label>Teléfono de contacto *</label>
              <input type="tel" name="telefono" id="telefono" 
                     value="<?php echo htmlspecialchars($cliente['telefono']); ?>" 
                     maxlength="10" required>
              <span class="error" id="error-telefono">Debe tener 10 dígitos</span>
            </div>
            
            <div class="form-group">
              <label>Código Postal *</label>
              <input type="text" name="codigo_postal" id="codigo_postal" 
                     maxlength="5" required placeholder="20000">
              <span class="error" id="error-cp">5 dígitos requeridos</span>
            </div>
          </div>
          
          <div class="form-group">
            <label>Mensaje para la tarjeta (opcional)</label>
            <textarea name="mensaje" id="mensaje" rows="2" 
                      maxlength="200" placeholder="Escribe un mensaje especial..."></textarea>
          </div>
        </div>
        
        <!-- Método de Pago -->
        <div class="form-section">
          <h3>💳 Método de Pago</h3>
          
          <div class="metodo-pago">
            <div class="metodo-option active" onclick="seleccionarMetodo('tarjeta', this)">
              <input type="radio" name="metodo_pago" value="tarjeta" id="metodo-tarjeta" checked>
              <label for="metodo-tarjeta">
                <div style="font-size: 2rem;">💳</div>
                Tarjeta
              </label>
            </div>
            
            <div class="metodo-option" onclick="seleccionarMetodo('efectivo', this)">
              <input type="radio" name="metodo_pago" value="efectivo" id="metodo-efectivo">
              <label for="metodo-efectivo">
                <div style="font-size: 2rem;">💵</div>
                Efectivo
              </label>
            </div>
            
            <div class="metodo-option" onclick="seleccionarMetodo('transferencia', this)">
              <input type="radio" name="metodo_pago" value="transferencia" id="metodo-transferencia">
              <label for="metodo-transferencia">
                <div style="font-size: 2rem;">🏦</div>
                Transferencia
              </label>
            </div>
          </div>
        </div>
        
        <!-- Datos de Tarjeta -->
        <div class="form-section" id="seccion-tarjeta">
          <h3>💳 Datos de la Tarjeta</h3>
          
          <!-- Tarjeta Visual -->
          <div class="tarjeta-visual">
            <div style="text-align: right; font-size: 24px;">💳</div>
            <div class="tarjeta-numero" id="tarjeta-display">•••• •••• •••• ••••</div>
            <div class="tarjeta-info">
              <div>
                <div style="font-size: 10px; opacity: 0.8;">TITULAR</div>
                <div id="nombre-display"><?php echo strtoupper(htmlspecialchars($cliente['nombre'])); ?></div>
              </div>
              <div>
                <div style="font-size: 10px; opacity: 0.8;">VENCE</div>
                <div id="expira-display">MM/AA</div>
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <label>Número de tarjeta *</label>
            <input type="text" name="numero_tarjeta" id="numero_tarjeta" 
                   maxlength="19" placeholder="1234 5678 9012 3456" 
                   oninput="formatearTarjeta(this)">
            <span class="error" id="error-tarjeta">Número de tarjeta inválido</span>
          </div>
          
          <div class="form-group">
            <label>Nombre del titular (como aparece en la tarjeta) *</label>
            <input type="text" name="nombre_tarjeta" id="nombre_tarjeta" 
                   value="<?php echo strtoupper(htmlspecialchars($cliente['nombre'])); ?>" 
                   oninput="actualizarNombreDisplay(this)" style="text-transform: uppercase;">
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label>Fecha de vencimiento *</label>
              <input type="text" name="fecha_expiracion" id="fecha_expiracion" 
                     maxlength="5" placeholder="MM/AA" oninput="formatearFecha(this)">
              <span class="error" id="error-expira">Formato MM/AA requerido</span>
            </div>
            
            <div class="form-group">
              <label>CVV *</label>
              <input type="text" name="cvv" id="cvv" 
                     maxlength="3" placeholder="123">
              <span class="error" id="error-cvv">3 dígitos requeridos</span>
            </div>
          </div>
        </div>
        
        <button type="submit" class="btn-pagar">Confirmar Pago - $<?php echo number_format($total, 2); ?> MXN</button>
      </form>
    </div>
    
    <!-- Resumen del Pedido -->
    <div class="pago-resumen">
      <h3 style="color: #841F2B; margin-bottom: 20px;">📋 Resumen del Pedido</h3>
      
      <?php foreach ($productos_carrito as $item): ?>
        <div class="item-resumen">
          <div>
            <div style="font-weight: 600;"><?php echo htmlspecialchars($item['nombre']); ?></div>
            <div style="font-size: 13px; color: #666;">Cantidad: <?php echo $item['cantidad']; ?></div>
          </div>
          <div style="font-weight: 600; color: #841F2B;">
            $<?php echo number_format($item['subtotal'], 2); ?>
          </div>
        </div>
      <?php endforeach; ?>
      
      <div class="total-final">
        <span>Total:</span>
        <span>$<?php echo number_format($total, 2); ?> MXN</span>
      </div>
      
      <div style="margin-top: 20px; padding: 15px; background: #e8f8f5; border-radius: 8px; font-size: 13px;">
        <strong style="color: #27ae60;">✓ Envío incluido</strong><br>
        <span style="color: #666;">Entrega estimada: 1-3 días hábiles</span>
      </div>
    </div>
  </div>

  <footer class="footer">
    <p>© 2025 Pa´que no digas que no te di nada.</p>
    <p>Hecho con 💖 para los amantes de las flores.</p>
  </footer>

  <script>
    // Formatear número de tarjeta
    function formatearTarjeta(input) {
      let valor = input.value.replace(/\s/g, '');
      let formateado = valor.match(/.{1,4}/g)?.join(' ') || valor;
      input.value = formateado;
      
      // Actualizar display
      if (valor.length > 0) {
        let display = formateado.padEnd(19, '•').replace(/\s/g, ' ');
        document.getElementById('tarjeta-display').textContent = display;
      } else {
        document.getElementById('tarjeta-display').textContent = '•••• •••• •••• ••••';
      }
    }
    
    // Formatear fecha MM/AA
    function formatearFecha(input) {
      let valor = input.value.replace(/\D/g, '');
      if (valor.length >= 2) {
        valor = valor.substring(0, 2) + '/' + valor.substring(2, 4);
      }
      input.value = valor;
      document.getElementById('expira-display').textContent = valor || 'MM/AA';
    }
    
    // Actualizar nombre en tarjeta
    function actualizarNombreDisplay(input) {
      document.getElementById('nombre-display').textContent = input.value.toUpperCase() || 'NOMBRE';
    }
    
    // Seleccionar método de pago
    function seleccionarMetodo(metodo, elemento) {
      document.querySelectorAll('.metodo-option').forEach(el => el.classList.remove('active'));
      elemento.classList.add('active');
      document.getElementById('metodo-' + metodo).checked = true;
      
      // Mostrar/ocultar sección de tarjeta
      const seccionTarjeta = document.getElementById('seccion-tarjeta');
      if (metodo === 'tarjeta') {
        seccionTarjeta.style.display = 'block';
        document.querySelectorAll('#seccion-tarjeta input').forEach(input => {
          input.setAttribute('required', 'required');
        });
      } else {
        seccionTarjeta.style.display = 'none';
        document.querySelectorAll('#seccion-tarjeta input').forEach(input => {
          input.removeAttribute('required');
        });
      }
    }
    
    // Validación del formulario
    document.getElementById('form-pago').addEventListener('submit', function(e) {
      let valido = true;
      
      // Validar nombre
      const nombre = document.getElementById('nombre_destinatario');
      if (nombre.value.length < 3) {
        document.getElementById('error-nombre').classList.add('visible');
        nombre.classList.add('invalid');
        valido = false;
      }
      
      // Validar dirección
      const direccion = document.getElementById('direccion');
      if (direccion.value.length < 10) {
        document.getElementById('error-direccion').classList.add('visible');
        direccion.classList.add('invalid');
        valido = false;
      }
      
      // Validar teléfono
      const telefono = document.getElementById('telefono');
      if (telefono.value.length !== 10 || !/^\d+$/.test(telefono.value)) {
        document.getElementById('error-telefono').classList.add('visible');
        telefono.classList.add('invalid');
        valido = false;
      }
      
      // Validar si es pago con tarjeta
      if (document.getElementById('metodo-tarjeta').checked) {
        const numTarjeta = document.getElementById('numero_tarjeta');
        if (numTarjeta.value.replace(/\s/g, '').length !== 16) {
          document.getElementById('error-tarjeta').classList.add('visible');
          numTarjeta.classList.add('invalid');
          valido = false;
        }
        
        const cvv = document.getElementById('cvv');
        if (cvv.value.length !== 3 || !/^\d+$/.test(cvv.value)) {
          document.getElementById('error-cvv').classList.add('visible');
          cvv.classList.add('invalid');
          valido = false;
        }
      }
      
      if (!valido) {
        e.preventDefault();
        alert('Por favor corrige los errores en el formulario');
      }
    });
    
    // Limpiar errores al escribir
    document.querySelectorAll('input, textarea').forEach(input => {
      input.addEventListener('input', function() {
        this.classList.remove('invalid');
        const errorId = 'error-' + this.id.split('_')[this.id.split('_').length - 1];
        const errorElement = document.getElementById(errorId);
        if (errorElement) {
          errorElement.classList.remove('visible');
        }
      });
    });
  </script>

</body>
</html>