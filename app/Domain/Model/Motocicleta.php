<?php

namespace App\Domain\Model;

use DateTimeImmutable;
use App\Domain\Exceptions\GarantiaExcedidaException;

class Motocicleta
{
    public function __construct(
        private string $vin,
        private string $numeroMotor,
        private string $color,
        private string $estado, // 'DISPONIBLE', 'RESERVADA', 'VENDIDA', 'EN_MANTENIMIENTO'
        private string $modeloReferencia,
        private float $precioLista,
        private float $descuento = 0.0,
        private ?string $placa = null,
        private ?DateTimeImmutable $garantiaHasta = null,
        private ?int $recepcionId = null,
        private ?string $ventaNumero = null
    ) {}

    public function getVin(): string { return $this->vin; }
    public function getNumeroMotor(): string { return $this->numeroMotor; }
    public function getColor(): string { return $this->color; }
    public function getEstado(): string { return $this->estado; }
    public function getModeloReferencia(): string { return $this->modeloReferencia; }
    public function getPrecioLista(): float { return $this->precioLista; }
    public function getDescuento(): float { return $this->descuento; }
    public function getPlaca(): ?string { return $this->placa; }
    public function getGarantiaHasta(): ?DateTimeImmutable { return $this->garantiaHasta; }
    public function getRecepcionId(): ?int { return $this->recepcionId; }
    public function getVentaNumero(): ?string { return $this->ventaNumero; }

    public function getPrecioFinal(): float
    {
        return max(0.0, round($this->precioLista - $this->descuento, 2));
    }

    public function aplicarDescuento(float $descuento): void
    {
        $this->descuento = min($this->precioLista, $descuento);
    }

    public function asignarPlaca(string $placa): void
    {
        $this->placa = strtoupper($placa);
    }

    public function marcarComoVendida(string $ventaNumero, int $mesesGarantia = 12): void
    {
        $this->estado = 'VENDIDA';
        $this->ventaNumero = $ventaNumero;
        $this->garantiaHasta = (new DateTimeImmutable())->modify("+{$mesesGarantia} months");
    }

    /**
     * Regla de negocio HU-08 / RF-05: Validación de hasta 3 revisiones de garantía.
     */
    public function validarAccesoAGarantia(int $revisionesUsadas): void
    {
        if ($revisionesUsadas >= 3) {
            throw GarantiaExcedidaException::limiteRevisionesAlcanzado($this->vin);
        }

        if ($this->garantiaHasta !== null && (new DateTimeImmutable()) > $this->garantiaHasta) {
            throw GarantiaExcedidaException::garantiaVencidaPorFecha($this->vin);
        }
    }
}
