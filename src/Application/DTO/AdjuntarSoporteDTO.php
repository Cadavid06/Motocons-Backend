<?php

namespace App\Application\DTO;

readonly class AdjuntarSoporteDTO
{
    public function __construct(
        public string $ventaNumero,
        public int $requisitoId,
        public string $rutaArchivo
    ) {}
}
