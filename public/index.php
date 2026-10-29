<?php

use App\Support\Request;

$kernel = require __DIR__ . '/../bootstrap.php';

$response = $kernel->handle(Request::capture());

$response->send();
