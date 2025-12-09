<?php
echo "<html><head>";
echo "<meta charset='UTF-8'>";
echo "<title>Test de Conexión - Floristería</title>";
echo "<style>
    body {
        font-family: Arial, sans-serif;
        max-width: 800px;
        margin: 50px auto;
        padding: 20px;
        background: #ffebb9;
    }
    .test-box {
        background: white;
        padding: 20px;
        border-radius: 10px;
        margin: 15px 0;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .success {
        border-left: 5px solid #27ae60;
        background: #e8f8f5;
    }
    .error {
        border-left: 5px solid #e74c3c;
        background: #fadbd8;
    }
    h1 {
        color: #841F2B;
        text-align: center;
    }
    h2 {
        color: #333;
        margin-top: 0;
    }
    .emoji {
        font-size: 2em;
        margin-right: 10px;
    }
</style>";
echo "</head><body>";

echo "<h1>🔍 Test de Conexión - Floristería</h1>";

// TEST 1: PHP
echo "<div class='test-box success'>";
echo "<h2><span class='emoji'>✅</span>TEST 1: PHP Funcionando</h2>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "</div>";

// TEST 2: MySQLi
echo "<div class='test-box " . (extension_loaded('mysqli') ? 'success' : 'error') . "'>";
echo "<h2><span class='emoji'>" . (extension_loaded('mysqli') ? '✅' : '❌') . "</span>TEST 2: Extensión MySQLi</h2>";
if (extension_loaded('mysqli')) {
    echo "<p>✅ MySQLi está disponible</p>";
} else {
    echo "<p>❌ ERROR: MySQLi NO disponible</p>";
}
echo "</div>";

// TEST 3: Conexión MySQL
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '12345';
$db_name = 'floristeria';

echo "<div class='test-box'>";
echo "<h2><span class='emoji'>🔌</span>TEST 3: Conexión a MySQL</h2>";

$conn = @new mysqli($db_host, $db_user, $db_pass);

if ($conn->connect_error) {
    echo "<div style='color: #e74c3c; font-weight: bold;'>";
    echo "❌ ERROR DE CONEXIÓN<br>";
    echo "Mensaje: " . $conn->connect_error;
    echo "</div>";
} else {
    echo "<p style='color: #27ae60; font-weight: bold;'>✅ CONEXIÓN A MYSQL EXITOSA</p>";
    
    // TEST 4: Base de datos
    echo "</div><div class='test-box'>";
    echo "<h2><span class='emoji'>🗄️</span>TEST 4: Base de Datos</h2>";
    
    $db_existe = $conn->select_db($db_name);
    
    if ($db_existe) {
        echo "<p style='color: #27ae60; font-weight: bold;'>✅ Base de datos 'floristeria' EXISTE</p>";
        
        // TEST 5: Tablas
        echo "</div><div class='test-box'>";
        echo "<h2><span class='emoji'>📋</span>TEST 5: Tablas</h2>";
        
        $result = $conn->query("SHOW TABLES");
        $tablas = [];
        while ($row = $result->fetch_array()) {
            $tablas[] = $row[0];
        }
        
        echo "<ul>";
        foreach ($tablas as $tabla) {
            echo "<li>✅ $tabla</li>";
        }
        echo "</ul>";
        
        // TEST 6: Datos
        echo "</div><div class='test-box success'>";
        echo "<h2><span class='emoji'>📊</span>TEST 6: Datos</h2>";
        
        // CAMBIO IMPORTANTE: Ahora usa la tabla 'flores' con columna 'id'
        $result_flores = $conn->query("SELECT COUNT(*) as total FROM flores");
        $total_flores = $result_flores->fetch_assoc()['total'];
        
        $result_cat = $conn->query("SELECT COUNT(*) as total FROM categorias");
        $total_categorias = $result_cat->fetch_assoc()['total'];
        
        $result_clientes = $conn->query("SELECT COUNT(*) as total FROM clientes");
        $total_clientes = $result_clientes->fetch_assoc()['total'];
        
        echo "<ul>";
        echo "<li><strong>Flores registradas:</strong> $total_flores</li>";
        echo "<li><strong>Categorías:</strong> $total_categorias</li>";
        echo "<li><strong>Clientes:</strong> $total_clientes</li>";
        echo "</ul>";
        
        if ($total_flores > 0) {
            echo "<p style='color: #27ae60; font-weight: bold;'>✅ ¡Hay flores en la BD!</p>";
            
            
            $result_list = $conn->query("SELECT nombre, precio FROM flores LIMIT 5");

            echo "<p><strong>Primeras 5 flores:</strong></p>";
            echo "<ol>";
            while ($flor = $result_list->fetch_assoc()) {
                echo "<li>" . htmlspecialchars($flor['nombre']) . " - $" . number_format($flor['precio'], 2) . " MXN</li>";
            }
            echo "</ol>";
        } else {
            echo "<div class='test-box error'>";
            echo "<p>❌ <strong>NO HAY FLORES</strong></p>";
            echo "<p>Ejecuta el script SQL que te proporcioné en phpMyAdmin</p>";
            echo "</div>";
        }
        
    } else {
        echo "<p style='color: #e74c3c; font-weight: bold;'>❌ La base de datos 'floristeria' NO EXISTE</p>";
        echo "<p>🔧 <strong>Solución:</strong></p>";
        echo "<ol>";
        echo "<li>Abre phpMyAdmin</li>";
        echo "<li>Crea una base de datos llamada 'floristeria'</li>";
        echo "<li>Ejecuta el script SQL completo</li>";
        echo "</ol>";
    }
    
    $conn->close();
}
echo "</div>";

// RESUMEN FINAL
echo "<div class='test-box' style='background: #da727f; color: white;'>";
echo "<h2 style='color: white;'>🎯 Siguiente Paso</h2>";
echo "<p><a href='registros/registro.php' style='color: #fff6e1; font-weight: bold; font-size: 1.2em;'>→ Ir a Registro</a></p>";
echo "<p><a href='loguerse/login.php' style='color: #fff6e1; font-weight: bold; font-size: 1.2em;'>→ Ir a Login</a></p>";
echo "<p><a href='Home.html' style='color: #fff6e1; font-weight: bold; font-size: 1.2em;'>→ Ir al Inicio</a></p>";
echo "</div>";

echo "</body></html>";
?>