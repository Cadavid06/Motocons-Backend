<?php

namespace App\Domain\Model;

class LiquidacionComision
{
    public function __construct(
        private ?int $id,
        private string $periodo,
        private string $estado, // 'BORRADOR', 'APROBADA', 'PAGADA'
        private string $empleadoTipoDoc,
        private string $empleadoNumDoc
    ) {}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPeriodo(): string
    {
        return $this->periodo;
    }

    public function getEstado(): string
    {
        return $this->estado;
    }

    public function getEmpleadoTipoDoc(): string
    {
        return $this->empleadoTipoDoc;
    }

    public function getEmpleadoNumDoc(): string
    {
        return $this->empleadoNumDoc;
    }

    public function aprobar(): void
    {
        $this->estado = 'APROBADA';
    }

    public function pagar(): void
    {
        $this->estado = 'PAGADA';
    }

    public function estaAprobada(): bool
    {
        return in_array(strtoupper($this->estado), ['APROBADA', 'PAGADA']);
    }
}
