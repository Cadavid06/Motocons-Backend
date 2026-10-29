<?php

namespace App\Infrastructure\Http\Controllers;

use App\Application\DTO\AdjuntarSoporteDTO;
use App\Application\DTO\AprobarCreditoDTO;
use App\Application\DTO\EvaluarRequisitoDTO;
use App\Application\DTO\IniciarSolicitudCreditoDTO;
use App\Application\Ports\In\AdjuntarSoporteRequisitoUseCase;
use App\Application\Ports\In\AprobarCreditoUseCase;
use App\Application\Ports\In\EvaluarRequisitoCreditoUseCase;
use App\Application\Ports\In\IniciarSolicitudCreditoUseCase;
use App\Domain\Exceptions\DomainException;
use App\Infrastructure\Http\Requests\AdjuntarSoporteRequest;
use App\Infrastructure\Http\Requests\AprobarCreditoRequest;
use App\Infrastructure\Http\Requests\EvaluarRequisitoRequest;
use App\Infrastructure\Http\Requests\IniciarSolicitudCreditoRequest;
use App\Support\Request;
use App\Support\Response;
use App\Support\Storage;
use DateTimeImmutable;

final class SolicitudCreditoController
{
    public function __construct(
        private readonly IniciarSolicitudCreditoUseCase $iniciarSolicitudCreditoUseCase,
        private readonly AdjuntarSoporteRequisitoUseCase $adjuntarSoporteRequisitoUseCase,
        private readonly EvaluarRequisitoCreditoUseCase $evaluarRequisitoCreditoUseCase,
        private readonly AprobarCreditoUseCase $aprobarCreditoUseCase,
        private readonly Storage $storage
    ) {}

    public function store(Request $request): Response
    {
        $validated = (new IniciarSolicitudCreditoRequest)->validate($request);

        $dto = new IniciarSolicitudCreditoDTO(
            clienteTipoDoc: strtoupper((string) $validated['cliente_tipo_doc']),
            clienteNumDoc: (string) $validated['cliente_num_doc'],
            medioPago: strtoupper((string) $validated['medio_pago']),
            ventaNumero: $validated['venta_numero'] ?? null,
            vinMoto: $validated['vin_moto'] ?? null,
            vendedorTipoDoc: isset($validated['vendedor_tipo_doc']) ? strtoupper((string) $validated['vendedor_tipo_doc']) : null,
            vendedorNumDoc: $validated['vendedor_num_doc'] ?? null,
            canal: isset($validated['canal']) ? strtoupper((string) $validated['canal']) : 'PRESENCIAL',
            aceptacionTerminos: isset($validated['aceptacion_terminos']) && $validated['aceptacion_terminos'] !== null
                ? new DateTimeImmutable((string) $validated['aceptacion_terminos'])
                : null
        );

        try {
            $solicitud = $this->iniciarSolicitudCreditoUseCase->execute($dto);

            return Response::json([
                'success' => true,
                'message' => 'Solicitud de crédito iniciada exitosamente',
                'data' => $solicitud->toArray(),
            ], 201);
        } catch (DomainException $e) {
            return Response::json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function adjuntarSoporte(string $ventaNumero, string $requisitoId, Request $request): Response
    {
        $validated = (new AdjuntarSoporteRequest)->validate($request);

        $ruta = $this->storage->storeUploadedFile($validated['archivo'], 'soportes_credito');

        try {
            $dto = new AdjuntarSoporteDTO(
                ventaNumero: $ventaNumero,
                requisitoId: (int) $requisitoId,
                rutaArchivo: $ruta
            );

            $resultado = $this->adjuntarSoporteRequisitoUseCase->execute($dto);

            return Response::json([
                'success' => true,
                'message' => 'Soporte adjuntado exitosamente.',
                'data' => $resultado->toArray(),
            ]);
        } catch (DomainException $e) {
            return Response::json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function evaluarRequisito(string $ventaNumero, string $requisitoId, Request $request): Response
    {
        $validated = (new EvaluarRequisitoRequest)->validate($request);

        try {
            $dto = new EvaluarRequisitoDTO(
                ventaNumero: $ventaNumero,
                requisitoId: (int) $requisitoId,
                accion: strtoupper((string) $validated['decision']),
                validadorUsername: (string) $validated['validador_username']
            );

            $resultado = $this->evaluarRequisitoCreditoUseCase->execute($dto);

            return Response::json([
                'success' => true,
                'message' => "Requisito evaluado exitosamente como {$resultado->estado}.",
                'data' => $resultado->toArray(),
            ]);
        } catch (DomainException $e) {
            return Response::json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function aprobar(string $ventaNumero, Request $request): Response
    {
        $validated = (new AprobarCreditoRequest)->validate($request);

        try {
            $dto = new AprobarCreditoDTO(
                ventaNumero: $ventaNumero,
                aprobadoPor: (string) $validated['aprobado_por']
            );

            $resultado = $this->aprobarCreditoUseCase->execute($dto);

            return Response::json([
                'success' => true,
                'message' => $resultado->mensaje,
                'data' => $resultado->toArray(),
            ]);
        } catch (DomainException $e) {
            return Response::json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
