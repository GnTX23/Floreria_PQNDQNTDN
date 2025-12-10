<?php
require_once __DIR__ . "/database/config.php";

?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Pa'que no digas que no te di nada | Detalles que florecen</title>
  <link rel="stylesheet" href="style.css" />
</head>

<body>
  <!-- ==== HEADER ==== -->
  <header class="header">
    <div class="logo">Pa´que no digas que no te di nada</div>
    <nav class="nav">
      <a href="Home.php">Inicio</a>
      <?php if (isset($_SESSION['cliente_id'])): ?>
        <a href="catalogo.php">Catálogo</a>
        <a href="script/carrito/carrito.php">Carrito</a>
        <a href="contacto.php">Rosita</a>
      <?php else: ?>
        <a href="loguearse/login.php">Catálogo</a>
        <a href="loguearse/login.php">Carrito</a>
        <a href="loguearse/login.php">Rosita</a>
      <?php endif; ?>
    </nav>
    
    <?php if (isset($_SESSION['cliente_id'])): ?>
      <div style="display: flex; gap: 15px; align-items: center;">
        <span style="color: #333;">Hola, <?php echo htmlspecialchars($_SESSION['cliente_nombre']); ?></span>
        <a href="loguearse/logout.php" class="login-btn">Cerrar Sesión</a>
      </div>
    <?php else: ?>
      <a href="loguearse/login.php" class="login-btn">Iniciar sesión</a>
    <?php endif; ?>
  </header>

  <!-- ==== HERO ==== -->
  <section class="hero">
    <h1>Entrega flores con un clic</h1>
    <p>Haz sonreír a esa persona especial con un detalle floral a domicilio.</p>
    
    <?php if (isset($_SESSION['cliente_id'])): ?>
      <a href="catalogo.php" class="cta-btn" style="display: inline-block; text-decoration: none; margin-top: 20px;">
        Ver Catálogo Completo
      </a>
    <?php else: ?>
      <a href="registros/registro.php" class="cta-btn" style="display: inline-block; text-decoration: none; margin-top: 20px;">
        Crear Cuenta Gratis
      </a>
    <?php endif; ?>
  </section>

  <!-- ==== CATÁLOGO DESTACADO ==== -->
  <main id="catalogo" class="catalogo">
    <h1>Catálogo de Flores</h1>
    <p>Explora nuestra colección de flores y arreglos especiales para cada ocasión.</p>
    <h2>Flores destacadas</h2>

    <div id="contenedor-productos">
      <!-- Producto 1 -->
      <div class="producto">
        <img src="https://www.yaakunflores.com/uploads/arreglos/ramo-de-12-rosas-amarillas-con-3-girasoles-y-follajes-con-papel-coreano.jpg" alt="Flores amarillas">
        <p style="font-weight: bold; color: #841F2B;">Flores amarillas</p>
        <p style="color: #666; font-size: 13px;">12 rosas amarillas con 3 girasoles</p>
        <p style="font-size: 18px; color: #bb1c2f; font-weight: bold;">$850.00 MXN</p>
      </div>

      <!-- Producto 2 -->
      <div class="producto">
        <img src="https://www.yaakunflores.com/uploads/arreglos/ramo-de-20-girasoles-circular.jpg" alt="20 girasoles">
        <p style="font-weight: bold; color: #841F2B;">Ramo de 20 girasoles</p>
        <p style="color: #666; font-size: 13px;">Hermoso ramo circular</p>
        <p style="font-size: 18px; color: #bb1c2f; font-weight: bold;">$1,300.00 MXN</p>
      </div>

      <!-- Producto 3 -->
      <div class="producto">
        <img src="https://www.yaakunflores.com/uploads/arreglos/ramo-de-100-rosas-rojas-con-papel-coreano.jpg" alt="100 rosas rojas">
        <p style="font-weight: bold; color: #841F2B;">100 rosas rojas</p>
        <p style="color: #666; font-size: 13px;">Ramo espectacular</p>
        <p style="font-size: 18px; color: #bb1c2f; font-weight: bold;">$2,350.00 MXN</p>
      </div>

      <!-- Producto 4 -->
      <div class="producto">
        <img src="https://www.yaakunflores.com/uploads/arreglos/150-rosas-lilas-en-caja-circular-blanca-con-5-orquideas-blancas.jpg" alt="Rosas con orquídeas">
        <p style="font-weight: bold; color: #841F2B;">150 rosas lilas</p>
        <p style="color: #666; font-size: 13px;">Con 5 orquídeas blancas</p>
        <p style="font-size: 18px; color: #bb1c2f; font-weight: bold;">$6,600.00 MXN</p>
      </div>
    </div>

    <div style="text-align: center; margin-top: 40px;">
      <?php if (isset($_SESSION['cliente_id'])): ?>
        <p style="font-size: 1.1rem; color: #841F2B; margin-bottom: 15px;">
          ¡Explora nuestro catálogo completo!
        </p>
        <a href="catalogo.php" class="cta-btn" style="display: inline-block; text-decoration: none;">Ver Todos los Productos</a>
      <?php else: ?>
        <p style="font-size: 1.1rem; color: #841F2B; margin-bottom: 15px;">
          ¿Quieres ver todos los 30 productos?
        </p>
        <a href="registros/registro.php" class="cta-btn" style="display: inline-block; text-decoration: none;">Crear Cuenta Gratis</a>
        <p style="margin-top: 15px; color: #666;">
          ¿Ya tienes cuenta? <a href="loguerse/login.php" style="color: #841F2B; font-weight: bold;">Inicia sesión aquí</a>
        </p>
      <?php endif; ?>
    </div>
  </main>

  <!-- ==== FOOTER ==== -->
  <footer class="footer">
    <p>© 2025 Pa´que no digas que no te di nada.</p>
    <p>Hecho con 💖 para los amantes de las flores.</p>
  </footer>

</body>
</html>