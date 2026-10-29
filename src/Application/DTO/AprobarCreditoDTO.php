<?php

namespace App\Application\DTO;

readonly class AprobarCreditoDTO
{
    public function __construct(
        public string $ventaNumero,
        public string $aprobadoPor
    ) {}
}
