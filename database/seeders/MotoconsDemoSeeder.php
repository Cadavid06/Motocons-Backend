<?php

namespace Database\Seeders;

use App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models\Cliente;
use App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models\Empleado;
use App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models\ModeloMoto;
use App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models\Motocicleta;
use App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models\Producto;
use Illuminate\Database\Seeder;

class MotoconsDemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Clientes de prueba
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

        foreach ($clientes as $c) {
            Cliente::firstOrCreate(['tipo_doc' => $c['tipo_doc'], 'num_doc' => $c['num_doc']], $c);
        }

        // 2. Empleados de prueba (Vendedores / Asesores de Crédito)
        $empleados = [
            [
                'tipo_doc' => 'CC',
                'num_doc' => '78001122',
                'nombre' => 'María Fernández (Asesora Créditos)',
                'tipo_vinculacion' => 'PLANTA',
            ],
            [
                'tipo_doc' => 'CC',
                'num_doc' => '78003344',
                'nombre' => 'Juan Pérez (Vendedor Sala)',
                'tipo_vinculacion' => 'PLANTA',
            ],
        ];

        foreach ($empleados as $e) {
            Empleado::firstOrCreate(['tipo_doc' => $e['tipo_doc'], 'num_doc' => $e['num_doc']], $e);
        }

        // 3. Productos y Modelos de Motos
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

        foreach ($motos as $m) {
            Producto::firstOrCreate(
                ['referencia' => $m['ref']],
                [
                    'nombre' => $m['nombre'],
                    'tipo' => 'MOTO',
                    'precio_vigente' => $m['precio'],
                    'tarifa_iva' => 19.00,
                    'tarifa_inc' => 0.00,
                ]
            );

            ModeloMoto::firstOrCreate(
                ['referencia' => $m['ref']],
                [
                    'marca' => $m['marca'],
                    'linea' => $m['linea'],
                    'anio' => $m['anio'],
                    'gama' => $m['gama'],
                    'propulsion' => $m['propulsion'],
                    'cilindraje_cc' => $m['cc'],
                    'ficha_tecnica' => "Motor {$m['cc']}cc, {$m['propulsion']}, modelo {$m['anio']}.",
                ]
            );

            foreach ($m['unidades'] as $u) {
                Motocicleta::firstOrCreate(
                    ['vin' => $u['vin']],
                    [
                        'numero_motor' => $u['motor'],
                        'color' => $u['color'],
                        'estado' => 'DISPONIBLE',
                        'modelo_referencia' => $m['ref'],
                        'precio_lista' => $m['precio'],
                        'descuento' => $u['descuento'],
                    ]
                );
            }
        }
    }
}
