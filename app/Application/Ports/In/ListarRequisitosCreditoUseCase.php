<?php

namespace App\Application\Ports\In;

use App\Application\DTO\RequisitoCreditoResponseDTO;

interface ListarRequisitosCreditoUseCase
{
    /**
     * @return RequisitoCreditoResponseDTO[]
     */
    public function execute(?string $medioPago = null): array;
}
