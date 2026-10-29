<?php

namespace App\Infrastructure\Http\Controllers;

use App\Application\Ports\In\ListarRequisitosCreditoUseCase;
use App\Support\Request;
use App\Support\Response;

final class RequisitoCreditoController
{
    public function __construct(
        private readonly ListarRequisitosCreditoUseCase $listarRequisitosCreditoUseCase
    ) {}

    public function index(Request $request): Response
    {
        $medioPago = $request->query('medio_pago');
        $requisitos = $this->listarRequisitosCreditoUseCase->execute(
            is_string($medioPago) ? $medioPago : null
        );

        return Response::json([
            'success' => true,
            'data' => array_map(static fn ($r) => $r->toArray(), $requisitos),
        ]);
    }
}
