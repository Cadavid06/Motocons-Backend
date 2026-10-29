<?php

namespace App\Application\Services;

use App\Application\DTO\EvaluarRequisitoDTO;
use App\Application\DTO\VentaRequisitoResponseDTO;
use App\Application\Ports\In\EvaluarRequisitoCreditoUseCase;
use App\Application\Ports\Out\VentaRepositoryPort;
use App\Application\Ports\Out\VentaRequisitoRepositoryPort;
use App\Domain\Exceptions\DomainException;
use App\Domain\Exceptions\VentaNoEditableException;

class EvaluarRequisitoCreditoService implements EvaluarRequisitoCreditoUseCase
{
    public function __construct(
        private VentaRepositoryPort $ventaRepository,
        private VentaRequisitoRepositoryPort $ventaRequisitoRepository
    ) {}

    public function execute(EvaluarRequisitoDTO $dto): VentaRequisitoResponseDTO
    {
        // 1. Validar que la venta exista
        $venta = $this->ventaRepository->buscarPorNumero($dto->ventaNumero);
        if (! $venta) {
            throw new DomainException("La venta con número {$dto->ventaNumero} no existe.");
        }

        // 2. Validar que la venta esté en proceso
        if (! $venta->puedeModificarse()) {
            throw VentaNoEditableException::estadoNoPermiteCambios($venta->getNumero(), $venta->getEstado());
        }

        // 3. Buscar el requisito
        $ventaRequisito = $this->ventaRequisitoRepository->buscarPorVentaYRequisito(
            $dto->ventaNumero,
            $dto->requisitoId
        );

        if (! $ventaRequisito) {
            throw new DomainException("El requisito con ID {$dto->requisitoId} no está asociado a la venta {$dto->ventaNumero}.");
        }

        // 4. Aplicar acción
        $accion = strtoupper(trim($dto->accion));
        if ($accion === 'VALIDAR' || $accion === 'VALIDADO') {
            $ventaRequisito->validar($dto->validadorUsername);
        } elseif ($accion === 'RECHAZAR' || $accion === 'RECHAZADO') {
            $ventaRequisito->rechazar($dto->validadorUsername);
        } else {
            throw new DomainException("Acción de evaluación no válida: {$dto->accion}. Debe ser VALIDAR o RECHAZAR.");
        }

        // 5. Persistir
        $ventaRequisito = $this->ventaRequisitoRepository->guardar($ventaRequisito);

        return VentaRequisitoResponseDTO::fromDomain($ventaRequisito);
    }
}
