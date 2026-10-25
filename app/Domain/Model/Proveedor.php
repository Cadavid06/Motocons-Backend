<?php

namespace App\Domain\Model;

class Proveedor
{
    public function __construct(
        private string $nit,
        private string $razonSocial,
        private ?string $emailOrdenes = null
    ) {}

    public function getNit(): string
    {
        return $this->nit;
    }

    public function getRazonSocial(): string
    {
        return $this->razonSocial;
    }

    public function getEmailOrdenes(): ?string
    {
        return $this->emailOrdenes;
    }

    public function actualizarEmailOrdenes(string $nuevoEmail): void
    {
        $this->emailOrdenes = $nuevoEmail;
    }
}
