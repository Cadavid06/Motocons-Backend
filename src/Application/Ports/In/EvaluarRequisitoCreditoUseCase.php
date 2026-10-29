<?php

namespace App\Application\Ports\In;

use App\Application\DTO\EvaluarRequisitoDTO;
use App\Application\DTO\VentaRequisitoResponseDTO;

interface EvaluarRequisitoCreditoUseCase
{
    public function execute(EvaluarRequisitoDTO $dto): VentaRequisitoResponseDTO;
}
