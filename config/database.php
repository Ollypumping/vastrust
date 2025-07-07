<?php
namespace config;
class Database {
    private static $conn;

    public static function connect() {
        if (!self::$conn) {
            // Load environment variables from .env file
            $env = parse_ini_file(__DIR__ . '/../.env');

            $db_host = $env['MYSQL_ADDON_HOST'];
            $db_name = $env['MYSQL_ADDON_DB'];
            $db_user = $env['MYSQL_ADDON_USER'];
            $db_pass = $env['MYSQL_ADDON_PASSWORD'];
            $db_port = isset($env['MYSQL_ADDON_PORT']) ? (int)$env['MYSQL_ADDON_PORT'] : 3306;

            $dsn = "mysql:host=$db_host;dbname=$db_name;port=$db_port;charset=utf8mb4";
            self::$conn = new \PDO(
                $dsn,
                $db_user,
                $db_pass
            );
            self::$conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
        return self::$conn;
    }
}