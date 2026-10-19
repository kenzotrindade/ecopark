<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    private function __construct() {}
    private function __clone() {}

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $configFile = __DIR__ . '/../../config.local.php';

            if (!is_file($configFile)) {
                throw new RuntimeException(
                    'Configuration BDD absente : créez backend/config.local.php.'
                );
            }

            $config = require $configFile;

            $host = $config['host'] ?? '127.0.0.1';
            $db = $config['database'] ?? 'ecopark';
            $user = $config['username'] ?? '';
            $pass = $config['password'] ?? '';
            $charset = $config['charset'] ?? 'utf8mb4';

            $dsn = "mysql:host={$host};dbname={$db};charset={$charset}";

            self::$instance = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }

        return self::$instance;
    }
}
