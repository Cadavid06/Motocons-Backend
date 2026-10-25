<?php

namespace App\Domain\Model;

class Empleado
{
    public function __construct(
        private string $tipoDoc,
        private string $numDoc,
        private string $nombre,
        private ?string $tipoVinculacion = null // 'nomina', 'prestacion_servicios', etc.
    ) {}

    public function getTipoDoc(): string
    {
        return $this->tipoDoc;
    }

    public function getNumDoc(): string
    {
        return $this->numDoc;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getTipoVinculacion(): ?string
    {
        return $this->tipoVinculacion;
    }

    public function cambiarVinculacion(string $nuevaVinculacion): void
    {
        $this->tipoVinculacion = $nuevaVinculacion;
    }
}
