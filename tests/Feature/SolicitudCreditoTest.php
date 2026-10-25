<?php

namespace Tests\Feature;

use App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models\Cliente;
use App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models\ModeloMoto;
use App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models\Motocicleta;
use App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models\Producto;
use Database\Seeders\RequisitoCreditoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SolicitudCreditoTest extends TestCase
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

    private function crearMotocicleta(string $vin = 'VIN12345678901234'): Motocicleta
    {
        Producto::create([
            'referencia' => 'BOXER-CT100',
            'nombre' => 'Bajaj Boxer CT 100 ES',
            'tipo' => 'MOTO',
            'precio_vigente' => 5800000.00,
            'tarifa_iva' => 19.00,
            'tarifa_inc' => 0.00,
        ]);

        ModeloMoto::create([
            'referencia' => 'BOXER-CT100',
            'marca' => 'Bajaj',
            'linea' => 'Boxer CT 100',
            'anio' => 2026,
            'gama' => 'baja',
            'propulsion' => 'combustion',
            'cilindraje_cc' => 102,
        ]);

        return Motocicleta::create([
            'vin' => $vin,
            'numero_motor' => 'ENG987654321',
            'color' => 'Negro Nebulosa',
            'estado' => 'DISPONIBLE',
            'modelo_referencia' => 'BOXER-CT100',
            'precio_lista' => 5800000.00,
            'descuento' => 200000.00,
        ]);
    }

    public function test_iniciar_solicitud_credito_brilla_crea_venta_y_requisitos_en_pendiente(): void
    {
        $this->crearCliente('CC', '1065123456');

        $payload = [
            'cliente_tipo_doc' => 'CC',
            'cliente_num_doc' => '1065123456',
            'medio_pago' => 'BRILLA',
            'canal' => 'PRESENCIAL',
        ];

        $response = $this->postJson(route('creditos.solicitudes.store'), $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Solicitud de crédito iniciada exitosamente',
            ])
            ->assertJsonPath('data.estado', 'EN_PROCESO_CREDITO')
            ->assertJsonPath('data.medio_pago', 'BRILLA');

        $ventaNumero = $response->json('data.venta_numero');
        $this->assertNotEmpty($ventaNumero);

        $requisitos = $response->json('data.requisitos');
        $this->assertCount(3, $requisitos);
        foreach ($requisitos as $req) {
            $this->assertSame('PENDIENTE', $req['estado']);
            $this->assertSame('BRILLA', $req['medio_pago']);
        }

        $this->assertDatabaseHas('venta', [
            'numero' => $ventaNumero,
            'estado' => 'EN_PROCESO_CREDITO',
            'cliente_tipo_doc' => 'CC',
            'cliente_num_doc' => '1065123456',
        ]);

        $this->assertDatabaseCount('venta_requisito', 3);
    }

    public function test_iniciar_solicitud_credito_con_motocicleta_reserva_la_unidad(): void
    {
        $this->crearCliente('CC', '1065123456');
        $moto = $this->crearMotocicleta('VIN99988877766655');

        $payload = [
            'cliente_tipo_doc' => 'CC',
            'cliente_num_doc' => '1065123456',
            'medio_pago' => 'SERVICREDITO',
            'vin_moto' => $moto->vin,
        ];

        $response = $this->postJson(route('creditos.solicitudes.store'), $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.motocicleta.vin', $moto->vin);

        $ventaNumero = $response->json('data.venta_numero');

        $this->assertDatabaseHas('motocicleta', [
            'vin' => $moto->vin,
            'estado' => 'RESERVADA',
            'venta_numero' => $ventaNumero,
        ]);
    }

    public function test_validacion_falla_si_medio_de_pago_es_invalido(): void
    {
        $this->crearCliente('CC', '1065123456');

        $payload = [
            'cliente_tipo_doc' => 'CC',
            'cliente_num_doc' => '1065123456',
            'medio_pago' => 'MEDIO_INEXISTENTE',
        ];

        $response = $this->postJson(route('creditos.solicitudes.store'), $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['medio_pago']);
    }
}
