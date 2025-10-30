<?php

namespace App\Models;

use PDO;
use PDOException;

class Database 
{
    private static $pdo;

    public static function getInstance() 
    {
        if (self::$pdo === null) {
            $host = getenv('MYSQL_HOST');
            $db   = getenv('MYSQL_DATABASE');
            $user = getenv('MYSQL_USER');
            $pass = getenv('MYSQL_PASSWORD');
            $charset = getenv('MYSQL_CHARSET');

            $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];

            try {
                self::$pdo = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                // Błąd połączenia z bazą danych
                die("Błąd połączenia z bazą: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}