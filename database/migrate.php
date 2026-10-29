<?php

use App\Infrastructure\Persistence\Pdo\Schema;

$kernel = require __DIR__ . '/../bootstrap.php';

$config = $kernel->config();
$fresh = in_array('--fresh', $argv ?? [], true);
$driver = (string) $config['db']['driver'];

Schema::migrate($kernel->db(), (string) $config['root'], $driver, $fresh);

echo ($fresh ? 'Base de datos reiniciada' : 'Migraciones ejecutadas')
    . " ({$driver}).\n";
