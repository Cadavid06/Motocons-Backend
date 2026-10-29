<?php

namespace App\Support;

use PDO;
use RuntimeException;

final class Database
{
    public static function connect(array $config): PDO
    {
        $driver = strtolower((string) ($config['driver'] ?? 'sqlite'));

        if ($driver === 'sqlite') {
            $database = (string) $config['database'];

            if ($database !== ':memory:') {
                $directory = dirname($database);

                if (! is_dir($directory)) {
                    mkdir($directory, 0775, true);
                }
            }

            $dsn = 'sqlite:' . $database;
            $pdo = new PDO($dsn, null, null, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            $pdo->exec('PRAGMA foreign_keys = ON');

            return $pdo;
        }

        if (in_array($driver, ['pgsql', 'postgres', 'postgresql'], true)) {
            $dsn = sprintf(
                'pgsql:host=%s;port=%s;dbname=%s',
                $config['host'],
                $config['port'],
                $config['database']
            );

            if (! empty($config['sslmode'])) {
                $dsn .= ';sslmode=' . $config['sslmode'];
            }

            return new PDO($dsn, (string) $config['username'], (string) $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }

        throw new RuntimeException("Motor de base de datos no soportado: {$driver}");
    }
}
