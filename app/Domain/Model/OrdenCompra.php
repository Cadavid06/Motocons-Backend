<?php

namespace App\Domain\Model;

use DateTimeImmutable;

class OrdenCompra
{
    /** @var OrdenCompraProducto[] */
    private array $detalles = [];

    public function __construct(
        private string $numero,
        private DateTimeImmutable $fecha,
        private string $estado, // 'PENDIENTE', 'RECIBIDA', 'CANCELADA'
        private string $proveedorNit
    ) {}

    public function getNumero(): string { return $this->numero; }
    public function getFecha(): DateTimeImmutable { return $this->fecha; }
    public function getEstado(): string { return $this->estado; }
    public function getProveedorNit(): string { return $this->proveedorNit; }
    public function getDetalles(): array { return $this->detalles; }

    public function agregarDetalle(OrdenCompraProducto $detalle): void
    {
        $this->detalles[] = $detalle;
    }

    public function marcarComoRecibida(): void
    {
        $this->estado = 'RECIBIDA';
    }

    public function cancelar(): void
    {
        $this->estado = 'CANCELADA';
    }

    public function calcularCostoTotal(): float
    {
        $total = 0.0;
        foreach ($this->detalles as $detalle) {
            $total += $detalle->subtotal();
        }
        return round($total, 2);
    }
}
