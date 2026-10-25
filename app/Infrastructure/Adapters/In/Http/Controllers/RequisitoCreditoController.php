<?php

namespace App\Infrastructure\Adapters\In\Http\Controllers;

use App\Application\Ports\In\ListarRequisitosCreditoUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RequisitoCreditoController
{
    public function __construct(
        private ListarRequisitosCreditoUseCase $listarRequisitosCreditoUseCase
    ) {}

    public function index(Request $request): JsonResponse
    {
        $medioPago = $request->query('medio_pago');
        $requisitos = $this->listarRequisitosCreditoUseCase->execute(
            is_string($medioPago) ? $medioPago : null
        );

        return response()->json([
            'success' => true,
            'data' => array_map(fn ($r) => $r->toArray(), $requisitos),
        ]);
    }
}
