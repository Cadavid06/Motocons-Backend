<?php

namespace App\Application\Ports\In;

use App\Application\DTO\IniciarSolicitudCreditoDTO;
use App\Application\DTO\SolicitudCreditoResponseDTO;

interface IniciarSolicitudCreditoUseCase
{
    public function execute(IniciarSolicitudCreditoDTO $dto): SolicitudCreditoResponseDTO;
}
