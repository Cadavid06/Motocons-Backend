<?php

namespace App\Domain\Model;

use DateTimeImmutable;

class Pago
{
    public function __construct(
        private ?int $id,
        private string $medio, // 'CONTADO', 'TARJETA', 'SERVICREDITO', 'BRILLA'
        private float $valor,
        private DateTimeImmutable $fecha,
        private string $estado, // 'APROBADO', 'PENDIENTE', 'RECHAZADO'
        private ?string $referenciaAutorizacion = null,
        private ?string $ventaNumero = null,
        private ?string $ordenServicioNumero = null,
        private ?string $cierreTipo = null,
        private ?DateTimeImmutable $cierreFechaInicio = null
    ) {}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMedio(): string
    {
        return $this->medio;
    }

    public function getValor(): float
    {
        return $this->valor;
    }

    public function getFecha(): DateTimeImmutable
    {
        return $this->fecha;
    }

    public function getEstado(): string
    {
        return $this->estado;
    }

    public function getReferenciaAutorizacion(): ?string
    {
        return $this->referenciaAutorizacion;
    }

    public function getVentaNumero(): ?string
    {
        return $this->ventaNumero;
    }

    public function getOrdenServicioNumero(): ?string
    {
        return $this->ordenServicioNumero;
    }

    public function getCierreTipo(): ?string
    {
        return $this->cierreTipo;
    }

    public function getCierreFechaInicio(): ?DateTimeImmutable
    {
        return $this->cierreFechaInicio;
    }

    public function estaAprobado(): bool
    {
        return strcasecmp($this->estado, 'APROBADO') === 0;
    }

    public function aprobar(?string $referencia = null): void
    {
        $this->estado = 'APROBADO';
        if ($referencia !== null) {
            $this->referenciaAutorizacion = $referencia;
        }
    }

    public function rechazar(): void
    {
        $this->estado = 'RECHAZADO';
    }

    public function esCreditoBrilla(): bool
    {
        return strcasecmp($this->medio, 'BRILLA') === 0;
    }

    public function esServiCredito(): bool
    {
        return strcasecmp($this->medio, 'SERVICREDITO') === 0;
    }
}
