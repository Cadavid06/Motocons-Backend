<?php

namespace App\Domain\Model;

class RequisitoCredito
{
    public function __construct(
        private ?int $id,
        private string $medioPago, // 'BRILLA', 'SERVICREDITO'
        private string $descripcion,
        private bool $obligatorio = true
    ) {}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMedioPago(): string
    {
        return $this->medioPago;
    }

    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

    public function isObligatorio(): bool
    {
        return $this->obligatorio;
    }

    public function esParaBrilla(): bool
    {
        return strcasecmp($this->medioPago, 'BRILLA') === 0;
    }

    public function esParaServiCredito(): bool
    {
        return strcasecmp($this->medioPago, 'SERVICREDITO') === 0;
    }
}
