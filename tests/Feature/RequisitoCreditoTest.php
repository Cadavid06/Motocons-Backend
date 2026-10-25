<?php

namespace Tests\Feature;

use Database\Seeders\RequisitoCreditoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RequisitoCreditoTest extends TestCase
{
    use RefreshDatabase;

    public function test_puede_listar_todos_los_requisitos_de_credito(): void
    {
        $this->seed(RequisitoCreditoSeeder::class);

        $response = $this->getJson(route('creditos.requisitos.index'));

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'medio_pago', 'descripcion', 'obligatorio'],
                ],
            ]);

        $this->assertCount(6, $response->json('data'));
    }

    public function test_puede_filtrar_requisitos_por_medio_de_pago_brilla(): void
    {
        $this->seed(RequisitoCreditoSeeder::class);

        $response = $this->getJson(route('creditos.requisitos.index', ['medio_pago' => 'BRILLA']));

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $data = $response->json('data');
        $this->assertCount(3, $data);
        foreach ($data as $item) {
            $this->assertSame('BRILLA', $item['medio_pago']);
        }
    }

    public function test_puede_filtrar_requisitos_por_medio_de_pago_servicredito(): void
    {
        $this->seed(RequisitoCreditoSeeder::class);

        $response = $this->getJson(route('creditos.requisitos.index', ['medio_pago' => 'SERVICREDITO']));

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $data = $response->json('data');
        $this->assertCount(3, $data);
        foreach ($data as $item) {
            $this->assertSame('SERVICREDITO', $item['medio_pago']);
        }
    }
}
