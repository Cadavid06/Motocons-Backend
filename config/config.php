<?php

use App\Support\Env;

$root = dirname(__DIR__);

Env::load($root . '/.env');

$driver = (string) Env::get('DB_CONNECTION', 'sqlite');
$database = Env::get('DB_DATABASE');

if ($database === null || $database === '') {
    $database = $driver === 'sqlite'
        ? $root . '/database/database.sqlite'
        : 'motocons_db';
}

return [
    'root' => $root,

    'app' => [
        'name' => (string) Env::get('APP_NAME', 'MOTOCONS'),
        'env' => (string) Env::get('APP_ENV', 'local'),
        'debug' => filter_var(Env::get('APP_DEBUG', true), FILTER_VALIDATE_BOOL),
        'url' => (string) Env::get('APP_URL', 'http://localhost:8000'),
        'timezone' => (string) Env::get('APP_TIMEZONE', 'America/Bogota'),
        'csrf' => filter_var(Env::get('APP_CSRF', true), FILTER_VALIDATE_BOOL),
    ],

    'db' => [
        'driver' => $driver,
        'host' => (string) Env::get('DB_HOST', '127.0.0.1'),
        'port' => (string) Env::get('DB_PORT', '5432'),
        'database' => (string) $database,
        'username' => (string) Env::get('DB_USERNAME', 'postgres'),
        'password' => (string) Env::get('DB_PASSWORD', ''),
        'sslmode' => (string) Env::get('DB_SSLMODE', 'prefer'),
    ],

    'storage' => [
        'public' => (string) Env::get('STORAGE_PATH', $root . '/storage/app/public'),
    ],
];
