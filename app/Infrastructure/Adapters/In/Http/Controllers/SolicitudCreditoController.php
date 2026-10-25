<?php

namespace App\Infrastructure\Adapters\In\Http\Controllers;

use App\Application\DTO\IniciarSolicitudCreditoDTO;
use App\Application\Ports\In\IniciarSolicitudCreditoUseCase;
use App\Domain\Exceptions\DomainException;
use App\Infrastructure\Adapters\In\Http\Requests\IniciarSolicitudCreditoRequest;
use DateTimeImmutable;
use Illuminate\Http\JsonResponse;

class SolicitudCreditoController
{
    public function __construct(
        private IniciarSolicitudCreditoUseCase $iniciarSolicitudCreditoUseCase
    ) {}

    public function store(IniciarSolicitudCreditoRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $dto = new IniciarSolicitudCreditoDTO(
            clienteTipoDoc: strtoupper($validated['cliente_tipo_doc']),
            clienteNumDoc: $validated['cliente_num_doc'],
            medioPago: strtoupper($validated['medio_pago']),
            ventaNumero: $validated['venta_numero'] ?? null,
            vinMoto: $validated['vin_moto'] ?? null,
            vendedorTipoDoc: isset($validated['vendedor_tipo_doc']) ? strtoupper($validated['vendedor_tipo_doc']) : null,
            vendedorNumDoc: $validated['vendedor_num_doc'] ?? null,
            canal: isset($validated['canal']) ? strtoupper($validated['canal']) : 'PRESENCIAL',
            aceptacionTerminos: isset($validated['aceptacion_terminos']) ? new DateTimeImmutable($validated['aceptacion_terminos']) : null
        );

        try {
            $solicitud = $this->iniciarSolicitudCreditoUseCase->execute($dto);

            return response()->json([
                'success' => true,
                'message' => 'Solicitud de crédito iniciada exitosamente',
                'data' => $solicitud->toArray(),
            ], 201);
        } catch (DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
