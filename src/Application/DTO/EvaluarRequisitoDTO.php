<?php

namespace App\Application\DTO;

readonly class EvaluarRequisitoDTO
{
    public function __construct(
        public string $ventaNumero,
        public int $requisitoId,
        public string $accion, // 'VALIDAR', 'RECHAZAR'
        public string $validadorUsername
    ) {}
}
