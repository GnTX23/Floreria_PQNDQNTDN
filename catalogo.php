<?php
require_once "database/config.php";
verificarSesion();

$conn = conectarDB();

// Obtener flores
$sql = "SELECT f.*, c.nombre as categoria_nombre 
        FROM flores f 
        LEFT JOIN categorias c ON f.categoria_id = c.id 
        ORDER BY f.id";
$flores = $conn->query($sql);

// Obtener categorías
$sql_cat = "SELECT * FROM categorias";
$categorias = $conn->query($sql_cat);

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo - Floristería</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .categories {
            display: flex;
            gap: 15px;
            margin: 30px 0;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .category-btn {
            padding: 10px 20px;
            background: white;
            border: 2px solid #841F2B;
            color: #841F2B;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: bold;
        }
        
        .category-btn:hover,
        .category-btn.active {
            background: #841F2B;
            color: white;
        }
        
        .btn-agregar {
            width: 100%;
            padding: 12px;
            background: #841F2B;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s;
            margin-top: 10px;
        }
        
        .btn-agregar:hover {
            background: #661922;
        }
        
        .categoria-tag {
            display: inline-block;
            padding: 5px 10px;
            background: #f0f0f0;
            border-radius: 15px;
            font-size: 0.8em;
            color: #841F2B;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">Pa´que no digas que no te di nada</div>
        <nav class="nav">
            <a href="Home.php">Inicio</a>
            <a href="catalogo.php">Catálogo</a>
            <a href="script/carrito/carrito.php">Carrito 🛒</a>
            <a href="contacto.php">Rosita</a>
        </nav>
        <div style="display: flex; gap: 15px; align-items: center;">
            <span style="color: #333;">Hola, <?php echo htmlspecialchars($_SESSION['cliente_nombre']); ?></span>
            <a href="loguearse/logout.php" class="login-btn">Cerrar Sesión</a>
        </div>
    </header>
    
    <div class="catalogo">
        <h2>🌸 Nuestro Catálogo de Flores</h2>
        
        <div class="categories">
            <button class="category-btn active" onclick="filtrarCategoria('todas')">Todas</button>
            <?php 
            $categorias->data_seek(0);
            while($cat = $categorias->fetch_assoc()): 
            ?>
                <button class="category-btn" onclick="filtrarCategoria(<?php echo $cat['id']; ?>)">
                    <?php echo htmlspecialchars($cat['nombre']); ?>
                </button>
            <?php endwhile; ?>
        </div>
        
        <div id="contenedor-productos">
            <?php 
            $flores->data_seek(0);
            while($flor = $flores->fetch_assoc()): 
            ?>
                <div class="producto" data-categoria="<?php echo $flor['categoria_id']; ?>">
                    <img src="<?php echo htmlspecialchars($flor['imagen']); ?>" alt="<?php echo htmlspecialchars($flor['nombre']); ?>">
                    <span class="categoria-tag">
                        <?php echo htmlspecialchars($flor['categoria_nombre']); ?>
                    </span>
                    <p style="font-weight: bold; color: #841F2B;"><?php echo htmlspecialchars($flor['nombre']); ?></p>
                    <p style="color: #666; font-size: 13px;"><?php echo htmlspecialchars($flor['descripcion']); ?></p>
                    <p style="font-size: 18px; color: #bb1c2f; font-weight: bold;">$<?php echo number_format($flor['precio'], 2); ?> MXN</p>
                    <p style="color: #888; font-size: 0.9em;">Stock: <?php echo $flor['stock']; ?></p>
                    <button class="btn-agregar" onclick="agregarCarrito(<?php echo $flor['id']; ?>)">
                        Agregar al Carrito
                    </button>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
    
    <footer class="footer">
        <p>© 2025 Pa´que no digas que no te di nada.</p>
        <p>Hecho con 💖 para los amantes de las flores.</p>
    </footer>
    
    <script>
        function filtrarCategoria(catId) {
            const cards = document.querySelectorAll('.producto');
            const btns = document.querySelectorAll('.category-btn');
            
            btns.forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            cards.forEach(card => {
                if (catId === 'todas' || card.dataset.categoria == catId) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }
        
        function agregarCarrito(florId) {
            fetch('script/carrito/agregar_carrito.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'flor_id=' + florId
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    alert('¡Producto agregado al carrito!');
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al agregar producto');
            });
        }
    </script>
</body>
</html>