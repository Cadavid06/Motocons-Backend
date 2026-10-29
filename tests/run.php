<?php

use App\Infrastructure\Persistence\Pdo\DemoSeeder;
use App\Infrastructure\Persistence\Pdo\Schema;
use App\Kernel;
use App\Support\Autoloader;
use App\Support\Request;
use App\Support\Response;

require __DIR__ . '/../src/Support/helpers.php';
require __DIR__ . '/../src/Support/Autoloader.php';

Autoloader::register(__DIR__ . '/../src');

$passed = 0;
$failed = 0;
$failures = [];

/**
 * @return array{0: Kernel, 1: array<string, mixed>, 2: string}
 */
function freshApp(): array
{
    $base = sys_get_temp_dir() . '/motocons_puro_' . bin2hex(random_bytes(8));
    mkdir($base, 0775, true);
    mkdir($base . '/storage', 0775, true);

    $env = [
        'DB_CONNECTION' => 'sqlite',
        'DB_DATABASE' => $base . '/test.sqlite',
        'STORAGE_PATH' => $base . '/storage',
        'APP_CSRF' => 'false',
        'APP_ENV' => 'testing',
        'APP_DEBUG' => 'true',
    ];

    foreach ($env as $key => $value) {
        putenv("{$key}={$value}");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }

    $config = require __DIR__ . '/../config/config.php';
    $kernel = new Kernel($config);

    Schema::migrate($kernel->db(), (string) $config['root'], (string) $config['db']['driver'], true);
    DemoSeeder::run($kernel->db());

    return [$kernel, $config, $base];
}

function removeDirectory(string $directory): void
{
    if (! is_dir($directory)) {
        return;
    }

    $items = scandir($directory) ?: [];

    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }

        $path = $directory . '/' . $item;

        if (is_dir($path)) {
            removeDirectory($path);
        } else {
            @unlink($path);
        }
    }

    @rmdir($directory);
}

function call(
    Kernel $kernel,
    string $method,
    string $path,
    array $body = [],
    array $files = [],
    array $query = [],
    array $headers = []
): Response {
    $headers += ['accept' => 'application/json'];

    return $kernel->handle(new Request(
        method: strtoupper($method),
        path: $path,
        query: $query,
        body: $body,
        files: $files,
        headers: $headers
    ));
}

/**
 * @return array<string, mixed>
 */
function jsonBody(Response $response): array
{
    $decoded = json_decode($response->body, true);

    return is_array($decoded) ? $decoded : [];
}

function dataGet(array $data, string $path, mixed $default = null): mixed
{
    foreach (explode('.', $path) as $segment) {
        if (! is_array($data) || ! array_key_exists($segment, $data)) {
            return $default;
        }

        $data = $data[$segment];
    }

    return $data;
}

function assertTrue(bool $condition, string $message): void
{
    if (! $condition) {
        throw new RuntimeException($message);
    }
}

function assertSame(mixed $expected, mixed $actual, string $message): void
{
    if ($expected !== $actual) {
        throw new RuntimeException(
            $message . ' (esperado: ' . var_export($expected, true) . ', obtenido: ' . var_export($actual, true) . ')'
        );
    }
}

function assertStatus(Response $response, int $status): void
{
    assertSame($status, $response->status, "Se esperaba HTTP {$status}. Cuerpo: " . substr($response->body, 0, 500));
}

function assertContains(string $needle, string $haystack, string $message): void
{
    assertTrue(str_contains($haystack, $needle), $message . " (no se encontró '{$needle}')");
}

function uploadFile(string $name, string $contents): array
{
    $tmp = tempnam(sys_get_temp_dir(), 'motocons_upload_');

    if ($tmp === false) {
        throw new RuntimeException('No fue posible crear el archivo temporal de prueba.');
    }

    file_put_contents($tmp, $contents);

    return [
        'name' => $name,
        'type' => 'application/octet-stream',
        'tmp_name' => $tmp,
        'error' => UPLOAD_ERR_OK,
        'size' => strlen($contents),
    ];
}

function test(string $name, callable $callback): void
{
    global $passed, $failed, $failures;

    try {
        $callback();
        $passed++;
        echo "  [OK] {$name}\n";
    } catch (Throwable $e) {
        $failed++;
        $failures[] = [$name, $e->getMessage()];
        echo "  [FALLO] {$name}\n         {$e->getMessage()}\n";
    }
}

echo "MOTOCONS PHP puro - Suite de pruebas\n\n";

test('Lista todos los requisitos de crédito', function (): void {
    [$kernel, , $base] = freshApp();

    try {
        $response = call($kernel, 'GET', '/api/creditos/requisitos');
        $body = jsonBody($response);

        assertStatus($response, 200);
        assertSame(true, dataGet($body, 'success'), 'success debe ser true');
        assertSame(6, count(dataGet($body, 'data', [])), 'Deben existir 6 requisitos');

        $first = dataGet($body, 'data.0', []);
        foreach (['id', 'medio_pago', 'descripcion', 'obligatorio'] as $key) {
            assertTrue(array_key_exists($key, $first), "El requisito debe incluir la clave {$key}");
        }
    } finally {
        removeDirectory($base);
    }
});

test('Filtra requisitos por medio de pago BRILLA', function (): void {
    [$kernel, , $base] = freshApp();

    try {
        $response = call($kernel, 'GET', '/api/creditos/requisitos', [], [], ['medio_pago' => 'BRILLA']);
        $body = jsonBody($response);
        $data = dataGet($body, 'data', []);

        assertStatus($response, 200);
        assertSame(3, count($data), 'Deben existir 3 requisitos Brilla');

        foreach ($data as $item) {
            assertSame('BRILLA', $item['medio_pago'], 'Todos los requisitos deben ser BRILLA');
        }
    } finally {
        removeDirectory($base);
    }
});

test('Filtra requisitos por medio de pago SERVICREDITO', function (): void {
    [$kernel, , $base] = freshApp();

    try {
        $response = call($kernel, 'GET', '/api/creditos/requisitos', [], [], ['medio_pago' => 'SERVICREDITO']);
        $body = jsonBody($response);
        $data = dataGet($body, 'data', []);

        assertStatus($response, 200);
        assertSame(3, count($data), 'Deben existir 3 requisitos ServiCrédito');

        foreach ($data as $item) {
            assertSame('SERVICREDITO', $item['medio_pago'], 'Todos los requisitos deben ser SERVICREDITO');
        }
    } finally {
        removeDirectory($base);
    }
});

test('Inicia solicitud Brilla y crea venta con requisitos pendientes', function (): void {
    [$kernel, , $base] = freshApp();

    try {
        $response = call($kernel, 'POST', '/api/creditos/solicitudes', [
            'cliente_tipo_doc' => 'CC',
            'cliente_num_doc' => '1065123456',
            'medio_pago' => 'BRILLA',
            'canal' => 'PRESENCIAL',
        ]);
        $body = jsonBody($response);

        assertStatus($response, 201);
        assertSame(true, dataGet($body, 'success'), 'success debe ser true');
        assertSame('Solicitud de crédito iniciada exitosamente', dataGet($body, 'message'), 'Mensaje inesperado');
        assertSame('EN_PROCESO_CREDITO', dataGet($body, 'data.estado'), 'Estado inesperado');
        assertSame('BRILLA', dataGet($body, 'data.medio_pago'), 'Medio de pago inesperado');

        $ventaNumero = (string) dataGet($body, 'data.venta_numero', '');
        assertTrue($ventaNumero !== '', 'La venta debe tener número');

        $requisitos = dataGet($body, 'data.requisitos', []);
        assertSame(3, count($requisitos), 'Deben crearse 3 requisitos');

        foreach ($requisitos as $requisito) {
            assertSame('PENDIENTE', $requisito['estado'], 'El requisito debe iniciar PENDIENTE');
            assertSame('BRILLA', $requisito['medio_pago'], 'El requisito debe ser BRILLA');
        }

        $pdo = $kernel->db();
        $statement = $pdo->prepare('SELECT * FROM venta WHERE numero = ?');
        $statement->execute([$ventaNumero]);
        $venta = $statement->fetch();

        assertTrue(is_array($venta), 'La venta debe existir en la base de datos');
        assertSame('EN_PROCESO_CREDITO', $venta['estado'], 'Estado de venta en BD inesperado');
        assertSame('CC', $venta['cliente_tipo_doc'], 'Tipo de documento en BD inesperado');
        assertSame('1065123456', $venta['cliente_num_doc'], 'Número de documento en BD inesperado');

        assertSame(3, (int) $pdo->query('SELECT COUNT(*) FROM venta_requisito')->fetchColumn(), 'Deben existir 3 venta_requisito');
    } finally {
        removeDirectory($base);
    }
});

test('Inicia solicitud con motocicleta y reserva la unidad', function (): void {
    [$kernel, , $base] = freshApp();

    try {
        $vin = '9F2BOX100T2026001';

        $response = call($kernel, 'POST', '/api/creditos/solicitudes', [
            'cliente_tipo_doc' => 'CC',
            'cliente_num_doc' => '1065123456',
            'medio_pago' => 'SERVICREDITO',
            'vin_moto' => $vin,
        ]);
        $body = jsonBody($response);

        assertStatus($response, 201);
        assertSame($vin, dataGet($body, 'data.motocicleta.vin'), 'El VIN debe coincidir');

        $ventaNumero = (string) dataGet($body, 'data.venta_numero');
        $pdo = $kernel->db();
        $statement = $pdo->prepare('SELECT estado, venta_numero FROM motocicleta WHERE vin = ?');
        $statement->execute([$vin]);
        $moto = $statement->fetch();

        assertTrue(is_array($moto), 'La motocicleta debe existir');
        assertSame('RESERVADA', $moto['estado'], 'La motocicleta debe quedar RESERVADA');
        assertSame($ventaNumero, $moto['venta_numero'], 'La motocicleta debe quedar asociada a la venta');
    } finally {
        removeDirectory($base);
    }
});

test('Rechaza medio de pago inválido con errores de validación', function (): void {
    [$kernel, , $base] = freshApp();

    try {
        $response = call($kernel, 'POST', '/api/creditos/solicitudes', [
            'cliente_tipo_doc' => 'CC',
            'cliente_num_doc' => '1065123456',
            'medio_pago' => 'MEDIO_INEXISTENTE',
        ]);
        $body = jsonBody($response);

        assertStatus($response, 422);
        assertTrue(isset($body['errors']['medio_pago']), 'Debe existir un error de validación para medio_pago');
    } finally {
        removeDirectory($base);
    }
});

test('Adjunta soporte a un requisito y lo persiste', function (): void {
    [$kernel, , $base] = freshApp();

    try {
        $solicitud = jsonBody(call($kernel, 'POST', '/api/creditos/solicitudes', [
            'cliente_tipo_doc' => 'CC',
            'cliente_num_doc' => '1065123456',
            'medio_pago' => 'BRILLA',
        ]));

        $ventaNumero = (string) dataGet($solicitud, 'data.venta_numero');
        $requisitoId = (int) dataGet($solicitud, 'data.requisitos.0.requisito_id');

        $file = uploadFile('recibo_gas.pdf', "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\ntrailer\n<<>>\n%%EOF\n");

        $response = call(
            $kernel,
            'POST',
            "/api/creditos/solicitudes/{$ventaNumero}/requisitos/{$requisitoId}/soporte",
            [],
            ['archivo' => $file],
            [],
            ['accept' => 'application/json']
        );
        $body = jsonBody($response);

        assertStatus($response, 200);
        assertSame(true, dataGet($body, 'success'), 'success debe ser true');
        assertSame('Soporte adjuntado exitosamente.', dataGet($body, 'message'), 'Mensaje inesperado');
        assertSame('PENDIENTE', dataGet($body, 'data.estado'), 'El estado debe quedar PENDIENTE');
        assertSame($requisitoId, dataGet($body, 'data.requisito_id'), 'El requisito debe coincidir');

        $ruta = (string) dataGet($body, 'data.soporte_adjunto', '');
        assertTrue($ruta !== '', 'Debe devolverse la ruta del soporte');
        assertTrue(is_file($base . '/storage/' . $ruta), 'El archivo debe existir en el almacenamiento');

        $pdo = $kernel->db();
        $statement = $pdo->prepare('SELECT * FROM venta_requisito WHERE venta_numero = ? AND requisito_id = ?');
        $statement->execute([$ventaNumero, $requisitoId]);
        $row = $statement->fetch();

        assertTrue(is_array($row), 'La fila venta_requisito debe existir');
        assertSame($ruta, $row['soporte_adjunto'], 'La ruta debe persistirse');
        assertSame('PENDIENTE', $row['estado'], 'El estado persistido debe ser PENDIENTE');
    } finally {
        removeDirectory($base);
    }
});

test('Rechaza archivo con extensión inválida', function (): void {
    [$kernel, , $base] = freshApp();

    try {
        $solicitud = jsonBody(call($kernel, 'POST', '/api/creditos/solicitudes', [
            'cliente_tipo_doc' => 'CC',
            'cliente_num_doc' => '1065123456',
            'medio_pago' => 'BRILLA',
        ]));

        $ventaNumero = (string) dataGet($solicitud, 'data.venta_numero');
        $requisitoId = (int) dataGet($solicitud, 'data.requisitos.0.requisito_id');

        $file = uploadFile('script.sh', "#!/bin/sh\necho hola\n");

        $response = call(
            $kernel,
            'POST',
            "/api/creditos/solicitudes/{$ventaNumero}/requisitos/{$requisitoId}/soporte",
            [],
            ['archivo' => $file]
        );
        $body = jsonBody($response);

        assertStatus($response, 422);
        assertTrue(isset($body['errors']['archivo']), 'Debe existir un error de validación para archivo');
    } finally {
        removeDirectory($base);
    }
});

test('Falla al adjuntar soporte si la venta o requisito no existe', function (): void {
    [$kernel, , $base] = freshApp();

    try {
        $file = uploadFile('recibo_gas.pdf', "%PDF-1.4\n%%EOF\n");

        $response = call(
            $kernel,
            'POST',
            '/api/creditos/solicitudes/VTA-INEXISTENTE/requisitos/9999/soporte',
            [],
            ['archivo' => $file]
        );
        $body = jsonBody($response);

        assertStatus($response, 422);
        assertSame(false, dataGet($body, 'success'), 'success debe ser false');
    } finally {
        removeDirectory($base);
    }
});

test('Evalúa requisitos como VALIDADO y RECHAZADO', function (): void {
    [$kernel, , $base] = freshApp();

    try {
        $solicitud = jsonBody(call($kernel, 'POST', '/api/creditos/solicitudes', [
            'cliente_tipo_doc' => 'CC',
            'cliente_num_doc' => '1065123456',
            'medio_pago' => 'BRILLA',
        ]));

        $ventaNumero = (string) dataGet($solicitud, 'data.venta_numero');
        $requisito1 = (int) dataGet($solicitud, 'data.requisitos.0.requisito_id');
        $requisito2 = (int) dataGet($solicitud, 'data.requisitos.1.requisito_id');

        $validar = call($kernel, 'PATCH', "/api/creditos/solicitudes/{$ventaNumero}/requisitos/{$requisito1}/evaluar", [
            'decision' => 'VALIDAR',
            'validador_username' => 'analista_ana',
        ]);
        $validarBody = jsonBody($validar);

        assertStatus($validar, 200);
        assertSame('VALIDADO', dataGet($validarBody, 'data.estado'), 'Estado esperado VALIDADO');
        assertSame('analista_ana', dataGet($validarBody, 'data.validado_por'), 'Validador inesperado');

        $rechazar = call($kernel, 'PATCH', "/api/creditos/solicitudes/{$ventaNumero}/requisitos/{$requisito2}/evaluar", [
            'decision' => 'RECHAZAR',
            'validador_username' => 'analista_carlos',
        ]);
        $rechazarBody = jsonBody($rechazar);

        assertStatus($rechazar, 200);
        assertSame('RECHAZADO', dataGet($rechazarBody, 'data.estado'), 'Estado esperado RECHAZADO');
        assertSame('analista_carlos', dataGet($rechazarBody, 'data.validado_por'), 'Validador inesperado');
    } finally {
        removeDirectory($base);
    }
});

test('No aprueba crédito Brilla si el recibo de gas no está validado', function (): void {
    [$kernel, , $base] = freshApp();

    try {
        $solicitud = jsonBody(call($kernel, 'POST', '/api/creditos/solicitudes', [
            'cliente_tipo_doc' => 'CC',
            'cliente_num_doc' => '1065123456',
            'medio_pago' => 'BRILLA',
        ]));

        $ventaNumero = (string) dataGet($solicitud, 'data.venta_numero');

        $response = call($kernel, 'POST', "/api/creditos/solicitudes/{$ventaNumero}/aprobar", [
            'aprobado_por' => 'gerente_credito',
        ]);
        $body = jsonBody($response);

        assertStatus($response, 422);
        assertSame(false, dataGet($body, 'success'), 'success debe ser false');
        assertContains('recibo de gas', strtolower((string) dataGet($body, 'message', '')), 'El mensaje debe mencionar el recibo de gas');
    } finally {
        removeDirectory($base);
    }
});

test('Aprueba crédito cuando todos los obligatorios están validados', function (): void {
    [$kernel, , $base] = freshApp();

    try {
        $solicitud = jsonBody(call($kernel, 'POST', '/api/creditos/solicitudes', [
            'cliente_tipo_doc' => 'CC',
            'cliente_num_doc' => '1065123456',
            'medio_pago' => 'BRILLA',
        ]));

        $ventaNumero = (string) dataGet($solicitud, 'data.venta_numero');
        $requisitos = dataGet($solicitud, 'data.requisitos', []);

        foreach ($requisitos as $requisito) {
            if (! $requisito['obligatorio']) {
                continue;
            }

            $response = call(
                $kernel,
                'PATCH',
                "/api/creditos/solicitudes/{$ventaNumero}/requisitos/{$requisito['requisito_id']}/evaluar",
                [
                    'decision' => 'VALIDAR',
                    'validador_username' => 'analista_juan',
                ]
            );

            assertStatus($response, 200);
        }

        $response = call($kernel, 'POST', "/api/creditos/solicitudes/{$ventaNumero}/aprobar", [
            'aprobado_por' => 'gerente_credito',
        ]);
        $body = jsonBody($response);

        assertStatus($response, 200);
        assertSame(true, dataGet($body, 'success'), 'success debe ser true');
        assertSame('PAGADA', dataGet($body, 'data.estado'), 'El estado debe ser PAGADA');
        assertSame('gerente_credito', dataGet($body, 'data.aprobado_por'), 'Aprobador inesperado');

        $pdo = $kernel->db();
        $statement = $pdo->prepare('SELECT estado FROM venta WHERE numero = ?');
        $statement->execute([$ventaNumero]);

        assertSame('PAGADA', $statement->fetchColumn(), 'La venta debe quedar PAGADA en BD');
    } finally {
        removeDirectory($base);
    }
});

test('La interfaz web de créditos carga con los datos de demostración', function (): void {
    [$kernel, , $base] = freshApp();

    try {
        $response = call($kernel, 'GET', '/', [], [], [], ['accept' => 'text/html']);

        assertStatus($response, 200);
        assertContains('Gestión de Créditos', $response->body, 'La página debe mostrar el título del módulo');
        assertContains('Crédito Brilla', $response->body, 'La página debe mostrar Crédito Brilla');
        assertContains('ServiCrédito', $response->body, 'La página debe mostrar ServiCrédito');
        assertContains('Albeiro Gómez', $response->body, 'La página debe mostrar un cliente de demostración');
    } finally {
        removeDirectory($base);
    }
});

test('La ruta alternativa /creditos responde correctamente', function (): void {
    [$kernel, , $base] = freshApp();

    try {
        $response = call($kernel, 'GET', '/creditos', [], [], [], ['accept' => 'text/html']);

        assertStatus($response, 200);
        assertContains('MOTOCONS', $response->body, 'La página debe contener la marca MOTOCONS');
    } finally {
        removeDirectory($base);
    }
});

test('Expone el endpoint de salud y responde 404 en rutas inexistentes', function (): void {
    [$kernel, , $base] = freshApp();

    try {
        $health = call($kernel, 'GET', '/up');
        assertStatus($health, 200);
        assertSame('ok', dataGet(jsonBody($health), 'status'), 'El estado de salud debe ser ok');

        $notFound = call($kernel, 'GET', '/api/no-existe');
        assertStatus($notFound, 404);
    } finally {
        removeDirectory($base);
    }
});

echo "\nResultado: {$passed} correctas, {$failed} fallidas.\n";

if ($failed > 0) {
    echo "\nDetalle de fallos:\n";

    foreach ($failures as [$name, $message]) {
        echo " - {$name}: {$message}\n";
    }

    exit(1);
}

exit(0);
