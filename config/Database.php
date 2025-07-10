<?php
class Database
{
    private static $instance = null;

    private function __construct() {}
    public function __clone() {}

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $dsn = 'mysql:host=127.0.0.1;port=3308;dbname=contacts_db;charset=utf8mb4';
            $user = 'root';
            $pass = 'Manchas12345.';
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];
            self::$instance = new PDO($dsn, $user, $pass, $options);
        }
        return self::$instance;
    }
}
