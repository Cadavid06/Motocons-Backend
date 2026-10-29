<?php

namespace App\Domain\Model;

class DocumentoFiscalDetalle
{
    public function __construct(
        private string $prefijo,
        private int $consecutivo,
        private string $productoReferencia,
        private float $cantidad,
        private float $valorUnit,
        private float $descuento = 0.0,
        private float $iva = 0.0,
        private float $inc = 0.0,
        private bool $esGratuita = false // Mano de obra de garantía en taller
    ) {}

    public function getPrefijo(): string
    {
        return $this->prefijo;
    }

    public function getConsecutivo(): int
    {
        return $this->consecutivo;
    }

    public function getProductoReferencia(): string
    {
        return $this->productoReferencia;
    }

    public function getCantidad(): float
    {
        return $this->cantidad;
    }

    public function getValorUnit(): float
    {
        return $this->valorUnit;
    }

    public function getDescuento(): float
    {
        return $this->descuento;
    }

    public function getIva(): float
    {
        return $this->iva;
    }

    public function getInc(): float
    {
        return $this->inc;
    }

    public function isEsGratuita(): bool
    {
        return $this->esGratuita;
    }

    public function subtotalNeto(): float
    {
        if ($this->esGratuita) {
            return 0.0;
        }

        return round(($this->cantidad * $this->valorUnit) - $this->descuento, 2);
    }

    public function totalConImpuestos(): float
    {
        if ($this->esGratuita) {
            return 0.0;
        }
        $base = $this->subtotalNeto();
        $valorIva = $base * ($this->iva / 100);
        $valorInc = $base * ($this->inc / 100);

        return round($base + $valorIva + $valorInc, 2);
    }
}
