<?php

require __DIR__ . '/src/Support/helpers.php';
require __DIR__ . '/src/Support/Autoloader.php';

App\Support\Autoloader::register(__DIR__ . '/src');

$config = require __DIR__ . '/config/config.php';

return new App\Kernel($config);
