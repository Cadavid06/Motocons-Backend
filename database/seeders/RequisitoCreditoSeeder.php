<?php

namespace Database\Seeders;

use App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models\RequisitoCredito;
use Illuminate\Database\Seeder;

class RequisitoCreditoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $requisitos = [
            // Requisitos para Crédito Brilla (Gases del Caribe / Surtigas)
            [
                'medio_pago' => 'BRILLA',
                'descripcion' => 'Recibo de gas del último mes cancelado y con cupo aprobado disponible',
                'obligatorio' => true,
            ],
            [
                'medio_pago' => 'BRILLA',
                'descripcion' => 'Cédula de ciudadanía original y fotocopia ampliada al 150% del titular',
                'obligatorio' => true,
            ],
            [
                'medio_pago' => 'BRILLA',
                'descripcion' => 'Dos referencias familiares o personales verificables con número telefónico fijo o móvil',
                'obligatorio' => true,
            ],

            // Requisitos para ServiCrédito
            [
                'medio_pago' => 'SERVICREDITO',
                'descripcion' => 'Cédula de ciudadanía original del solicitante',
                'obligatorio' => true,
            ],
            [
                'medio_pago' => 'SERVICREDITO',
                'descripcion' => 'Desprendibles de nómina de los dos últimos meses o extractos bancarios del último trimestre',
                'obligatorio' => true,
            ],
            [
                'medio_pago' => 'SERVICREDITO',
                'descripcion' => 'Certificado laboral vigente con antigüedad mínima de 6 meses (para empleados)',
                'obligatorio' => false,
            ],
        ];

        foreach ($requisitos as $req) {
            RequisitoCredito::firstOrCreate(
                [
                    'medio_pago' => $req['medio_pago'],
                    'descripcion' => $req['descripcion'],
                ],
                [
                    'obligatorio' => $req['obligatorio'],
                ]
            );
        }
    }
}
