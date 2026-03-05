<?php

class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        // Cargamos la configuración desde el archivo config.php
        $config = require __DIR__ . '/../config/config.php';
        $dbConfig = $config['db'];

        // DSN para MariaDB/MySQL
        $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['database']};charset=utf8mb4";
        
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lanza excepciones en errores
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Resultados como array asociativo
            PDO::ATTR_EMULATE_PREPARES   => false,                  // Seguridad extra contra Inyección SQL
        ];

        try {
            // Creamos la conexión PDO
            $this->connection = new PDO($dsn, $dbConfig['user'], $dbConfig['pass'], $options);
            echo "Conexión a la base de datos exitosa.";

        } catch (PDOException $e) {
            // Error controlado para no exponer credenciales en pantalla
            error_log($e->getMessage());
            die("Error crítico: No se pudo conectar a la base de datos.");
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }
}