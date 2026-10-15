<?php

namespace App\Domain\Model;

class OrdenCompraProducto
{
    public function __construct(
        private string $ordenCompraNumero,
        private string $productoReferencia,
        private int $cantidad,
        private float $costo
    ) {}

    public function getOrdenCompraNumero(): string { return $this->ordenCompraNumero; }
    public function getProductoReferencia(): string { return $this->productoReferencia; }
    public function getCantidad(): int { return $this->cantidad; }
    public function getCosto(): float { return $this->costo; }

    public function subtotal(): float
    {
        return round($this->cantidad * $this->costo, 2);
    }
}
