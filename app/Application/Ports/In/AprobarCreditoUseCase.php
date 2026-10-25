<?php

namespace App\Application\Ports\In;

use App\Application\DTO\AprobacionCreditoResponseDTO;
use App\Application\DTO\AprobarCreditoDTO;

interface AprobarCreditoUseCase
{
    public function execute(AprobarCreditoDTO $dto): AprobacionCreditoResponseDTO;
}
