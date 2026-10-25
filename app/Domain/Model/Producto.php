<?php

namespace App\Domain\Model;

class Producto
{
    public function __construct(
        private string $referencia,
        private string $nombre,
        private string $tipo, // 'MOTO', 'REPUESTO', 'INSUMO', 'SERVICIO'
        private float $precioVigente,
        private float $tarifaIva = 0.0,
        private float $tarifaInc = 0.0
    ) {}

    public function getReferencia(): string
    {
        return $this->referencia;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getTipo(): string
    {
        return $this->tipo;
    }

    public function getPrecioVigente(): float
    {
        return $this->precioVigente;
    }

    public function getTarifaIva(): float
    {
        return $this->tarifaIva;
    }

    public function getTarifaInc(): float
    {
        return $this->tarifaInc;
    }

    public function esMoto(): bool
    {
        return strcasecmp($this->tipo, 'MOTO') === 0;
    }

    public function esServicio(): bool
    {
        return strcasecmp($this->tipo, 'SERVICIO') === 0;
    }

    public function esRepuestoOInsumo(): bool
    {
        return in_array(strtoupper($this->tipo), ['REPUESTO', 'INSUMO']);
    }

    public function calcularPrecioTotal(): float
    {
        $iva = $this->precioVigente * ($this->tarifaIva / 100);
        $inc = $this->precioVigente * ($this->tarifaInc / 100);

        return round($this->precioVigente + $iva + $inc, 2);
    }

    public function actualizarPrecio(float $nuevoPrecio): void
    {
        $this->precioVigente = $nuevoPrecio;
    }
}
