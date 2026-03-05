<?php
require_once './database/database.php';

try {
    // Esto disparará el constructor y el mensaje de éxito
    $db = Database::getInstance()->getConnection();
    
    echo "<h1>Bienvenido a mi Backend</h1>";
    echo "Prueba";
    echo "<p>Si ves el log en la consola, todo está configurado correctamente.</p>";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}