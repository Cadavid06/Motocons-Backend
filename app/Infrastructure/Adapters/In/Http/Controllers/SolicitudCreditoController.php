<?php

namespace App\Infrastructure\Adapters\In\Http\Controllers;

use App\Application\DTO\AdjuntarSoporteDTO;
use App\Application\DTO\AprobarCreditoDTO;
use App\Application\DTO\EvaluarRequisitoDTO;
use App\Application\DTO\IniciarSolicitudCreditoDTO;
use App\Application\Ports\In\AdjuntarSoporteRequisitoUseCase;
use App\Application\Ports\In\AprobarCreditoUseCase;
use App\Application\Ports\In\EvaluarRequisitoCreditoUseCase;
use App\Application\Ports\In\IniciarSolicitudCreditoUseCase;
use App\Domain\Exceptions\DomainException;
use App\Infrastructure\Adapters\In\Http\Requests\AdjuntarSoporteRequest;
use App\Infrastructure\Adapters\In\Http\Requests\AprobarCreditoRequest;
use App\Infrastructure\Adapters\In\Http\Requests\EvaluarRequisitoRequest;
use App\Infrastructure\Adapters\In\Http\Requests\IniciarSolicitudCreditoRequest;
use DateTimeImmutable;
use Illuminate\Http\JsonResponse;

class SolicitudCreditoController
{
    public function __construct(
        private IniciarSolicitudCreditoUseCase $iniciarSolicitudCreditoUseCase,
        private AdjuntarSoporteRequisitoUseCase $adjuntarSoporteRequisitoUseCase,
        private EvaluarRequisitoCreditoUseCase $evaluarRequisitoCreditoUseCase,
        private AprobarCreditoUseCase $aprobarCreditoUseCase
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

    public function adjuntarSoporte(
        string $ventaNumero,
        int $requisitoId,
        AdjuntarSoporteRequest $request
    ): JsonResponse {
        $archivo = $request->file('archivo');
        $ruta = $archivo->store('soportes_credito', 'public');

        try {
            $dto = new AdjuntarSoporteDTO(
                ventaNumero: $ventaNumero,
                requisitoId: $requisitoId,
                rutaArchivo: $ruta
            );

            $resultado = $this->adjuntarSoporteRequisitoUseCase->execute($dto);

            return response()->json([
                'success' => true,
                'message' => 'Soporte adjuntado exitosamente.',
                'data' => $resultado->toArray(),
            ], 200);
        } catch (DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function evaluarRequisito(
        string $ventaNumero,
        int $requisitoId,
        EvaluarRequisitoRequest $request
    ): JsonResponse {
        $validated = $request->validated();

        try {
            $dto = new EvaluarRequisitoDTO(
                ventaNumero: $ventaNumero,
                requisitoId: $requisitoId,
                accion: strtoupper($validated['decision']),
                validadorUsername: $validated['validador_username']
            );

            $resultado = $this->evaluarRequisitoCreditoUseCase->execute($dto);

            return response()->json([
                'success' => true,
                'message' => "Requisito evaluado exitosamente como {$resultado->estado}.",
                'data' => $resultado->toArray(),
            ], 200);
        } catch (DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function aprobar(
        string $ventaNumero,
        AprobarCreditoRequest $request
    ): JsonResponse {
        $validated = $request->validated();

        try {
            $dto = new AprobarCreditoDTO(
                ventaNumero: $ventaNumero,
                aprobadoPor: $validated['aprobado_por']
            );

            $resultado = $this->aprobarCreditoUseCase->execute($dto);

            return response()->json([
                'success' => true,
                'message' => $resultado->mensaje,
                'data' => $resultado->toArray(),
            ], 200);
        } catch (DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
