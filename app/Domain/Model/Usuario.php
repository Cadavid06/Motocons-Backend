<?php

namespace App\Domain\Model;

class Usuario
{
    public function __construct(
        private string $username,
        private string $passwordHash,
        private string $rol, // 'Cliente', 'Vendedor', 'Administrador', 'Mecanico'
        private bool $activo = true,
        private ?string $clienteTipoDoc = null,
        private ?string $clienteNumDoc = null,
        private ?string $empleadoTipoDoc = null,
        private ?string $empleadoNumDoc = null
    ) {}

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getRol(): string
    {
        return $this->rol;
    }

    public function isActivo(): bool
    {
        return $this->activo;
    }

    public function getClienteTipoDoc(): ?string
    {
        return $this->clienteTipoDoc;
    }

    public function getClienteNumDoc(): ?string
    {
        return $this->clienteNumDoc;
    }

    public function getEmpleadoTipoDoc(): ?string
    {
        return $this->empleadoTipoDoc;
    }

    public function getEmpleadoNumDoc(): ?string
    {
        return $this->empleadoNumDoc;
    }

    public function activar(): void
    {
        $this->activo = true;
    }

    public function desactivar(): void
    {
        $this->activo = false;
    }

    public function cambiarPassword(string $nuevoPasswordHash): void
    {
        $this->passwordHash = $nuevoPasswordHash;
    }

    public function esAdmin(): bool
    {
        return strcasecmp($this->rol, 'Administrador') === 0;
    }

    public function esVendedor(): bool
    {
        return strcasecmp($this->rol, 'Vendedor') === 0;
    }

    public function esMecanico(): bool
    {
        return strcasecmp($this->rol, 'Mecanico') === 0;
    }

    public function esCliente(): bool
    {
        return strcasecmp($this->rol, 'Cliente') === 0;
    }
}
