<?php

namespace App\Infrastructure\Persistence\Pdo;

use PDO;

final class DemoSeeder
{
    public static function run(PDO $pdo): void
    {
        self::seedRequisitos($pdo);
        self::seedClientes($pdo);
        self::seedEmpleados($pdo);
        self::seedCatalogoMotos($pdo);
    }

    private static function seedRequisitos(PDO $pdo): void
    {
        $requisitos = [
            ['medio_pago' => 'BRILLA', 'descripcion' => 'Recibo de gas del último mes cancelado y con cupo aprobado disponible', 'obligatorio' => 1],
            ['medio_pago' => 'BRILLA', 'descripcion' => 'Cédula de ciudadanía original y fotocopia ampliada al 150% del titular', 'obligatorio' => 1],
            ['medio_pago' => 'BRILLA', 'descripcion' => 'Dos referencias familiares o personales verificables con número telefónico fijo o móvil', 'obligatorio' => 1],
            ['medio_pago' => 'SERVICREDITO', 'descripcion' => 'Cédula de ciudadanía original del solicitante', 'obligatorio' => 1],
            ['medio_pago' => 'SERVICREDITO', 'descripcion' => 'Desprendibles de nómina de los dos últimos meses o extractos bancarios del último trimestre', 'obligatorio' => 1],
            ['medio_pago' => 'SERVICREDITO', 'descripcion' => 'Certificado laboral vigente con antigüedad mínima de 6 meses (para empleados)', 'obligatorio' => 0],
        ];

        foreach ($requisitos as $requisito) {
            self::firstOrCreate(
                $pdo,
                'requisito_credito',
                ['medio_pago' => $requisito['medio_pago'], 'descripcion' => $requisito['descripcion']],
                ['obligatorio' => $requisito['obligatorio']]
            );
        }
    }

    private static function seedClientes(PDO $pdo): void
    {
        $clientes = [
            [
                'tipo_doc' => 'CC',
                'num_doc' => '1065123456',
                'nombre_razon_social' => 'Albeiro Gómez',
                'email_facturacion' => 'albeiro@motocons.test',
                'telefono' => '3015551234',
                'direccion' => 'Calle 10 # 5-20',
                'municipio' => 'Sahagún',
            ],
            [
                'tipo_doc' => 'CC',
                'num_doc' => '1065987654',
                'nombre_razon_social' => 'Carlos Ricardo',
                'email_facturacion' => 'carlos@motocons.test',
                'telefono' => '3104445678',
                'direccion' => 'Carrera 14 # 18-35',
                'municipio' => 'Montería',
            ],
        ];

        foreach ($clientes as $cliente) {
            self::firstOrCreate(
                $pdo,
                'cliente',
                ['tipo_doc' => $cliente['tipo_doc'], 'num_doc' => $cliente['num_doc']],
                array_diff_key($cliente, ['tipo_doc' => true, 'num_doc' => true])
            );
        }
    }

    private static function seedEmpleados(PDO $pdo): void
    {
        $empleados = [
            ['tipo_doc' => 'CC', 'num_doc' => '78001122', 'nombre' => 'María Fernández (Asesora Créditos)', 'tipo_vinculacion' => 'PLANTA'],
            ['tipo_doc' => 'CC', 'num_doc' => '78003344', 'nombre' => 'Juan Pérez (Vendedor Sala)', 'tipo_vinculacion' => 'PLANTA'],
        ];

        foreach ($empleados as $empleado) {
            self::firstOrCreate(
                $pdo,
                'empleado',
                ['tipo_doc' => $empleado['tipo_doc'], 'num_doc' => $empleado['num_doc']],
                array_diff_key($empleado, ['tipo_doc' => true, 'num_doc' => true])
            );
        }
    }

    private static function seedCatalogoMotos(PDO $pdo): void
    {
        $motos = [
            [
                'ref' => 'BOXER-CT100',
                'nombre' => 'Bajaj Boxer CT 100 ES',
                'precio' => 5850000.00,
                'marca' => 'Bajaj',
                'linea' => 'Boxer CT 100',
                'anio' => 2026,
                'gama' => 'baja',
                'propulsion' => 'combustion',
                'cc' => 102,
                'unidades' => [
                    ['vin' => '9F2BOX100T2026001', 'motor' => 'BX100E0001', 'color' => 'Negro Nebulosa', 'descuento' => 150000.00],
                    ['vin' => '9F2BOX100T2026002', 'motor' => 'BX100E0002', 'color' => 'Rojo Racing', 'descuento' => 100000.00],
                ],
            ],
            [
                'ref' => 'GIXXER-150',
                'nombre' => 'Suzuki Gixxer FI 150 ABS',
                'precio' => 10500000.00,
                'marca' => 'Suzuki',
                'linea' => 'Gixxer FI',
                'anio' => 2026,
                'gama' => 'media',
                'propulsion' => 'combustion',
                'cc' => 155,
                'unidades' => [
                    ['vin' => '9F2GIXX15T2026010', 'motor' => 'GX150E0010', 'color' => 'Azul Metálico', 'descuento' => 300000.00],
                    ['vin' => '9F2GIXX15T2026011', 'motor' => 'GX150E0011', 'color' => 'Negro Mate', 'descuento' => 250000.00],
                ],
            ],
            [
                'ref' => 'FZ25-ABS',
                'nombre' => 'Yamaha FZ-25 ABS Connected',
                'precio' => 15400000.00,
                'marca' => 'Yamaha',
                'linea' => 'FZ-25',
                'anio' => 2026,
                'gama' => 'alta',
                'propulsion' => 'combustion',
                'cc' => 249,
                'unidades' => [
                    ['vin' => '9F2FZ250T2026020', 'motor' => 'FZ250E0020', 'color' => 'Gris Mate Cyan', 'descuento' => 400000.00],
                ],
            ],
        ];

        foreach ($motos as $moto) {
            self::firstOrCreate(
                $pdo,
                'producto',
                ['referencia' => $moto['ref']],
                [
                    'nombre' => $moto['nombre'],
                    'tipo' => 'MOTO',
                    'precio_vigente' => $moto['precio'],
                    'tarifa_iva' => 19.00,
                    'tarifa_inc' => 0.00,
                ]
            );

            self::firstOrCreate(
                $pdo,
                'modelo_moto',
                ['referencia' => $moto['ref']],
                [
                    'marca' => $moto['marca'],
                    'linea' => $moto['linea'],
                    'anio' => $moto['anio'],
                    'gama' => $moto['gama'],
                    'propulsion' => $moto['propulsion'],
                    'cilindraje_cc' => $moto['cc'],
                    'ficha_tecnica' => "Motor {$moto['cc']}cc, {$moto['propulsion']}, modelo {$moto['anio']}.",
                ]
            );

            foreach ($moto['unidades'] as $unidad) {
                self::firstOrCreate(
                    $pdo,
                    'motocicleta',
                    ['vin' => $unidad['vin']],
                    [
                        'numero_motor' => $unidad['motor'],
                        'color' => $unidad['color'],
                        'estado' => 'DISPONIBLE',
                        'modelo_referencia' => $moto['ref'],
                        'precio_lista' => $moto['precio'],
                        'descuento' => $unidad['descuento'],
                    ]
                );
            }
        }
    }

    /**
     * @param  array<string, mixed>  $criteria
     * @param  array<string, mixed>  $values
     */
    private static function firstOrCreate(PDO $pdo, string $table, array $criteria, array $values): void
    {
        $where = implode(' AND ', array_map(
            static fn (string $column): string => "{$column} = :where_{$column}",
            array_keys($criteria)
        ));

        $whereParams = [];

        foreach ($criteria as $column => $value) {
            $whereParams['where_' . $column] = $value;
        }

        $statement = $pdo->prepare("SELECT 1 FROM {$table} WHERE {$where}");
        $statement->execute($whereParams);

        if ($statement->fetchColumn() !== false) {
            return;
        }

        $data = array_merge($criteria, $values);
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_map(
            static fn (string $column): string => ":{$column}",
            array_keys($data)
        ));

        $pdo->prepare("INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})")->execute($data);
    }
}
