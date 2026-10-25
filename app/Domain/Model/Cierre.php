<?php

namespace App\Domain\Model;

use DateTimeImmutable;

class Cierre
{
    public function __construct(
        private string $tipo, // 'SEMANAL_VENTAS', 'MENSUAL_PAGOS'
        private DateTimeImmutable $fechaInicio,
        private DateTimeImmutable $fechaFin,
        private DateTimeImmutable $fechaCierre
    ) {}

    public function getTipo(): string
    {
        return $this->tipo;
    }

    public function getFechaInicio(): DateTimeImmutable
    {
        return $this->fechaInicio;
    }

    public function getFechaFin(): DateTimeImmutable
    {
        return $this->fechaFin;
    }

    public function getFechaCierre(): DateTimeImmutable
    {
        return $this->fechaCierre;
    }

    public function esCierreSemanalVentas(): bool
    {
        return strcasecmp($this->tipo, 'SEMANAL_VENTAS') === 0;
    }

    public function esCierreMensualPagos(): bool
    {
        return strcasecmp($this->tipo, 'MENSUAL_PAGOS') === 0;
    }
}
