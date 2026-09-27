<?php
// config/Database.php

class Database {
    private static $host = "localhost";
    private static $dbName = "biblioteca_db";
    private static $username = "root";
    private static $password = "";
    private static $instance = null;

    // Construtor privado para evitar instanciação direta via 'new'
    private function __construct() {}

    public static function getConexao() {
        if (self::$instance === null) {
            try {
                self::$instance = new PDO(
                    "mysql:host=" . self::$host . ";dbname=" . self::$dbName . ";charset=utf8mb4",
                    self::$username,
                    self::$password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_PERSISTENT => false
                    ]
                );
            } catch (PDOException $exception) {
                die("Erro de Conexão com o Banco de Dados: " . $exception->getMessage());
            }
        }
        return self::$instance;
    }
}