<?php

namespace App\Application\Services;

use App\Application\DTO\AdjuntarSoporteDTO;
use App\Application\DTO\VentaRequisitoResponseDTO;
use App\Application\Ports\In\AdjuntarSoporteRequisitoUseCase;
use App\Application\Ports\Out\VentaRepositoryPort;
use App\Application\Ports\Out\VentaRequisitoRepositoryPort;
use App\Domain\Exceptions\DomainException;
use App\Domain\Exceptions\VentaNoEditableException;

class AdjuntarSoporteRequisitoService implements AdjuntarSoporteRequisitoUseCase
{
    public function __construct(
        private VentaRepositoryPort $ventaRepository,
        private VentaRequisitoRepositoryPort $ventaRequisitoRepository
    ) {}

    public function execute(AdjuntarSoporteDTO $dto): VentaRequisitoResponseDTO
    {
        // 1. Validar que la venta exista
        $venta = $this->ventaRepository->buscarPorNumero($dto->ventaNumero);
        if (! $venta) {
            throw new DomainException("La venta con número {$dto->ventaNumero} no existe.");
        }

        // 2. Validar que la venta esté en un estado modificable
        if (! $venta->puedeModificarse()) {
            throw VentaNoEditableException::estadoNoPermiteCambios($venta->getNumero(), $venta->getEstado());
        }

        // 3. Buscar el requisito asociado a la venta
        $ventaRequisito = $this->ventaRequisitoRepository->buscarPorVentaYRequisito(
            $dto->ventaNumero,
            $dto->requisitoId
        );

        if (! $ventaRequisito) {
            throw new DomainException("El requisito con ID {$dto->requisitoId} no está asociado a la venta {$dto->ventaNumero}.");
        }

        // 4. Adjuntar el soporte (actualiza ruta y vuelve el estado a PENDIENTE)
        $ventaRequisito->adjuntarSoporte($dto->rutaArchivo);

        // 5. Persistir cambios
        $ventaRequisito = $this->ventaRequisitoRepository->guardar($ventaRequisito);

        return VentaRequisitoResponseDTO::fromDomain($ventaRequisito);
    }
}
