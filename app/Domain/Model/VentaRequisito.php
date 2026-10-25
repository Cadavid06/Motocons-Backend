<?php

namespace App\Domain\Model;

class VentaRequisito
{
    public function __construct(
        private string $ventaNumero,
        private int $requisitoId,
        private string $estado = 'PENDIENTE', // 'PENDIENTE', 'VALIDADO', 'RECHAZADO'
        private ?string $soporteAdjunto = null,
        private ?string $validadoPor = null
    ) {}

    public function getVentaNumero(): string
    {
        return $this->ventaNumero;
    }

    public function getRequisitoId(): int
    {
        return $this->requisitoId;
    }

    public function getEstado(): string
    {
        return $this->estado;
    }

    public function getSoporteAdjunto(): ?string
    {
        return $this->soporteAdjunto;
    }

    public function getValidadoPor(): ?string
    {
        return $this->validadoPor;
    }

    public function adjuntarSoporte(string $rutaArchivo): void
    {
        $this->soporteAdjunto = $rutaArchivo;
        $this->estado = 'PENDIENTE';
    }

    public function validar(string $validadorUsername): void
    {
        $this->estado = 'VALIDADO';
        $this->validadoPor = $validadorUsername;
    }

    public function rechazar(string $validadorUsername): void
    {
        $this->estado = 'RECHAZADO';
        $this->validadoPor = $validadorUsername;
    }

    public function estaValidado(): bool
    {
        return strcasecmp($this->estado, 'VALIDADO') === 0;
    }
}
