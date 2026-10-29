<?php

use App\Infrastructure\Persistence\Pdo\DemoSeeder;

$kernel = require __DIR__ . '/../bootstrap.php';

DemoSeeder::run($kernel->db());

echo "Datos de demostración cargados.\n";
