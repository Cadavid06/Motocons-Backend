<?php

namespace App\Application\Services;

use App\Application\DTO\RequisitoCreditoResponseDTO;
use App\Application\Ports\In\ListarRequisitosCreditoUseCase;
use App\Application\Ports\Out\RequisitoCreditoRepositoryPort;

class ListarRequisitosCreditoService implements ListarRequisitosCreditoUseCase
{
    public function __construct(
        private RequisitoCreditoRepositoryPort $requisitoRepository
    ) {}

    /**
     * @return RequisitoCreditoResponseDTO[]
     */
    public function execute(?string $medioPago = null): array
    {
        $requisitos = ($medioPago !== null && trim($medioPago) !== '')
            ? $this->requisitoRepository->buscarPorMedioPago(strtoupper(trim($medioPago)))
            : $this->requisitoRepository->listarTodos();

        return array_map(
            fn ($req) => RequisitoCreditoResponseDTO::fromDomain($req),
            $requisitos
        );
    }
}
