<?php
namespace config;
class Database {
    private static $conn;

    public static function connect() {
        if (!self::$conn) {
            self::$conn = new \PDO(
                "mysql:host=localhost;dbname=vastrust;charset=utf8mb4", 
                "root", 
                ""
            );
            self::$conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
        return self::$conn;
    }
}