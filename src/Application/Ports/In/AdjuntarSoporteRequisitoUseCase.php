<?php

namespace App\Application\Ports\In;

use App\Application\DTO\AdjuntarSoporteDTO;
use App\Application\DTO\VentaRequisitoResponseDTO;

interface AdjuntarSoporteRequisitoUseCase
{
    public function execute(AdjuntarSoporteDTO $dto): VentaRequisitoResponseDTO;
}
