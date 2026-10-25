<?php

namespace Tests\Feature;

use App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models\Cliente;
use Database\Seeders\RequisitoCreditoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdjuntarSoporteRequisitoTest extends TestCase
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

    public function test_puede_adjuntar_soporte_exitosamente_a_un_requisito(): void
    {
        Storage::fake('public');

        $this->crearCliente('CC', '1065123456');

        // 1. Iniciar solicitud de crédito
        $solicitudResponse = $this->postJson(route('creditos.solicitudes.store'), [
            'cliente_tipo_doc' => 'CC',
            'cliente_num_doc' => '1065123456',
            'medio_pago' => 'BRILLA',
        ]);

        $ventaNumero = $solicitudResponse->json('data.venta_numero');
        $requisitoId = $solicitudResponse->json('data.requisitos.0.requisito_id');

        // 2. Adjuntar soporte (PDF del recibo de gas)
        $archivo = UploadedFile::fake()->create('recibo_gas.pdf', 500, 'application/pdf');

        $response = $this->postJson(
            route('creditos.solicitudes.requisitos.soporte', [
                'ventaNumero' => $ventaNumero,
                'requisitoId' => $requisitoId,
            ]),
            ['archivo' => $archivo]
        );

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Soporte adjuntado exitosamente.',
            ])
            ->assertJsonPath('data.estado', 'PENDIENTE')
            ->assertJsonPath('data.requisito_id', $requisitoId);

        $rutaGuardada = $response->json('data.soporte_adjunto');
        $this->assertNotEmpty($rutaGuardada);

        Storage::disk('public')->assertExists($rutaGuardada);

        $this->assertDatabaseHas('venta_requisito', [
            'venta_numero' => $ventaNumero,
            'requisito_id' => $requisitoId,
            'soporte_adjunto' => $rutaGuardada,
            'estado' => 'PENDIENTE',
        ]);
    }

    public function test_falla_si_archivo_tiene_extension_invalida(): void
    {
        Storage::fake('public');
        $this->crearCliente('CC', '1065123456');

        $solicitudResponse = $this->postJson(route('creditos.solicitudes.store'), [
            'cliente_tipo_doc' => 'CC',
            'cliente_num_doc' => '1065123456',
            'medio_pago' => 'BRILLA',
        ]);

        $ventaNumero = $solicitudResponse->json('data.venta_numero');
        $requisitoId = $solicitudResponse->json('data.requisitos.0.requisito_id');

        $archivoInvalido = UploadedFile::fake()->create('script.sh', 50, 'application/x-sh');

        $response = $this->postJson(
            route('creditos.solicitudes.requisitos.soporte', [
                'ventaNumero' => $ventaNumero,
                'requisitoId' => $requisitoId,
            ]),
            ['archivo' => $archivoInvalido]
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['archivo']);
    }

    public function test_falla_si_la_venta_o_requisito_no_existe(): void
    {
        Storage::fake('public');

        $archivo = UploadedFile::fake()->create('recibo_gas.pdf', 500, 'application/pdf');

        $response = $this->postJson(
            route('creditos.solicitudes.requisitos.soporte', [
                'ventaNumero' => 'VTA-INEXISTENTE',
                'requisitoId' => 9999,
            ]),
            ['archivo' => $archivo]
        );

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }
}
