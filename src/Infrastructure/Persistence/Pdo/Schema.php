<?php

namespace App\Infrastructure\Persistence\Pdo;

use PDO;
use RuntimeException;

final class Schema
{
    private const TABLES = [
        'documento_fiscal_detalle',
        'documento_fiscal',
        'pago',
        'orden_servicio_producto',
        'orden_servicio',
        'venta_requisito',
        'motocicleta',
        'venta',
        'liquidacion_comision',
        'cierre',
        'resolucion_dian',
        'requisito_credito',
        'recepcion_producto',
        'recepcion',
        'orden_compra_producto',
        'orden_compra',
        'promocion',
        'modelo_moto',
        'producto',
        'auditoria',
        'usuario',
        'proveedor',
        'empleado',
        'cliente',
    ];

    public static function migrate(PDO $pdo, string $root, string $driver, bool $fresh = false): void
    {
        $driver = self::normalizeDriver($driver);
        $file = $root . '/database/schema.' . $driver . '.sql';

        if (! is_file($file)) {
            throw new RuntimeException("No existe el esquema para el motor {$driver}: {$file}");
        }

        if ($fresh) {
            $cascade = $driver === 'pgsql' ? ' CASCADE' : '';

            foreach (self::TABLES as $table) {
                $pdo->exec("DROP TABLE IF EXISTS {$table}{$cascade}");
            }
        }

        $sql = (string) file_get_contents($file);
        $statements = explode(';', $sql);

        foreach ($statements as $statement) {
            $statement = preg_replace('/^\s*--.*$/m', '', $statement) ?? '';
            $statement = trim($statement);

            if ($statement === '') {
                continue;
            }

            $pdo->exec($statement);
        }
    }

    public static function normalizeDriver(string $driver): string
    {
        $driver = strtolower($driver);

        return in_array($driver, ['postgres', 'postgresql'], true) ? 'pgsql' : $driver;
    }
}
