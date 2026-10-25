<?php

namespace Tests\Feature;

use App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models\Cliente;
use Database\Seeders\RequisitoCreditoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AprobacionCreditoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RequisitoCreditoSeeder::class);
    }

    private function crearCliente(string $tipoDoc = 'CC', string $numDoc = '1065123456'): Cliente
    {
        return Cliente::create([
            'tipo_doc' => $tipoDoc,
            'num_doc' => $numDoc,
            'nombre_razon_social' => 'Albeiro Gomez',
            'email_facturacion' => 'albeiro@example.com',
            'telefono' => '3001234567',
            'direccion' => 'Calle 10 # 5-20',
            'municipio' => 'Sahagún',
        ]);
    }

    public function test_puede_evaluar_requisito_como_validado_o_rechazado(): void
    {
        $this->crearCliente('CC', '1065123456');

        $solicitudResponse = $this->postJson(route('creditos.solicitudes.store'), [
            'cliente_tipo_doc' => 'CC',
            'cliente_num_doc' => '1065123456',
            'medio_pago' => 'BRILLA',
        ]);

        $ventaNumero = $solicitudResponse->json('data.venta_numero');
        $requisitoId1 = $solicitudResponse->json('data.requisitos.0.requisito_id');
        $requisitoId2 = $solicitudResponse->json('data.requisitos.1.requisito_id');

        // Evaluar requisito 1 como VALIDAR
        $evaluarResponse1 = $this->patchJson(
            route('creditos.solicitudes.requisitos.evaluar', [
                'ventaNumero' => $ventaNumero,
                'requisitoId' => $requisitoId1,
            ]),
            [
                'decision' => 'VALIDAR',
                'validador_username' => 'analista_ana',
            ]
        );

        $evaluarResponse1->assertStatus(200)
            ->assertJsonPath('data.estado', 'VALIDADO')
            ->assertJsonPath('data.validado_por', 'analista_ana');

        // Evaluar requisito 2 como RECHAZAR
        $evaluarResponse2 = $this->patchJson(
            route('creditos.solicitudes.requisitos.evaluar', [
                'ventaNumero' => $ventaNumero,
                'requisitoId' => $requisitoId2,
            ]),
            [
                'decision' => 'RECHAZAR',
                'validador_username' => 'analista_carlos',
            ]
        );

        $evaluarResponse2->assertStatus(200)
            ->assertJsonPath('data.estado', 'RECHAZADO')
            ->assertJsonPath('data.validado_por', 'analista_carlos');
    }

    public function test_aprobar_credito_brilla_falla_si_recibo_gas_no_esta_validado(): void
    {
        $this->crearCliente('CC', '1065123456');

        $solicitudResponse = $this->postJson(route('creditos.solicitudes.store'), [
            'cliente_tipo_doc' => 'CC',
            'cliente_num_doc' => '1065123456',
            'medio_pago' => 'BRILLA',
        ]);

        $ventaNumero = $solicitudResponse->json('data.venta_numero');

        // Intentar aprobar cuando los requisitos están en PENDIENTE
        $aprobarResponse = $this->postJson(
            route('creditos.solicitudes.aprobar', ['ventaNumero' => $ventaNumero]),
            ['aprobado_por' => 'gerente_credito']
        );

        $aprobarResponse->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);

        $mensaje = $aprobarResponse->json('message');
        $this->assertStringContainsString('recibo de gas', $mensaje);
    }

    public function test_aprobar_credito_exitosamente_cuando_todos_los_obligatorios_estan_validados(): void
    {
        $this->crearCliente('CC', '1065123456');

        $solicitudResponse = $this->postJson(route('creditos.solicitudes.store'), [
            'cliente_tipo_doc' => 'CC',
            'cliente_num_doc' => '1065123456',
            'medio_pago' => 'BRILLA',
        ]);

        $ventaNumero = $solicitudResponse->json('data.venta_numero');
        $requisitos = $solicitudResponse->json('data.requisitos');

        // Validar todos los requisitos obligatorios
        foreach ($requisitos as $req) {
            if ($req['obligatorio']) {
                $this->patchJson(
                    route('creditos.solicitudes.requisitos.evaluar', [
                        'ventaNumero' => $ventaNumero,
                        'requisitoId' => $req['requisito_id'],
                    ]),
                    [
                        'decision' => 'VALIDAR',
                        'validador_username' => 'analista_juan',
                    ]
                )->assertStatus(200);
            }
        }

        // Aprobar el crédito
        $aprobarResponse = $this->postJson(
            route('creditos.solicitudes.aprobar', ['ventaNumero' => $ventaNumero]),
            ['aprobado_por' => 'gerente_credito']
        );

        $aprobarResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonPath('data.estado', 'PAGADA')
            ->assertJsonPath('data.aprobado_por', 'gerente_credito');

        $this->assertDatabaseHas('venta', [
            'numero' => $ventaNumero,
            'estado' => 'PAGADA',
        ]);
    }
}
