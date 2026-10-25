<?php

namespace Tests\Feature;

use Database\Seeders\MotoconsDemoSeeder;
use Database\Seeders\RequisitoCreditoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreditosWebTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([
            RequisitoCreditoSeeder::class,
            MotoconsDemoSeeder::class,
        ]);
    }

    public function test_pagina_principal_de_creditos_carga_satisfactoriamente(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk()
            ->assertViewIs('creditos.index')
            ->assertSee('Gestión de Créditos')
            ->assertSee('Crédito Brilla')
            ->assertSee('ServiCrédito')
            ->assertSee('Albeiro Gómez');
    }

    public function test_ruta_alternativa_creditos_responde_correctamente(): void
    {
        $response = $this->get(route('creditos.web.index'));

        $response->assertOk()
            ->assertSee('MOTOCONS');
    }
}
