<?php

namespace App\Domain\Model;

class RecepcionProducto
{
    public function __construct(
        private int $recepcionId,
        private string $productoReferencia,
        private int $cantidadRecibida
    ) {}

    public function getRecepcionId(): int { return $this->recepcionId; }
    public function getProductoReferencia(): string { return $this->productoReferencia; }
    public function getCantidadRecibida(): int { return $this->cantidadRecibida; }
}
