<?php

namespace App\Domain\Model;

class OrdenServicioProducto
{
    public function __construct(
        private string $ordenServicioNumero,
        private string $productoReferencia,
        private float $cantidad,
        private float $valorUnit
    ) {}

    public function getOrdenServicioNumero(): string { return $this->ordenServicioNumero; }
    public function getProductoReferencia(): string { return $this->productoReferencia; }
    public function getCantidad(): float { return $this->cantidad; }
    public function getValorUnit(): float { return $this->valorUnit; }

    public function subtotal(): float
    {
        return round($this->cantidad * $this->valorUnit, 2);
    }
}
