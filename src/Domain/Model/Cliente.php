<?php

namespace App\Domain\Model;

use DateTimeImmutable;

class Cliente
{
    public function __construct(
        private string $tipoDoc,
        private string $numDoc,
        private string $nombreRazonSocial,
        private ?string $emailFacturacion = null,
        private ?string $telefono = null,
        private ?string $direccion = null,
        private ?string $municipio = null,
        private ?DateTimeImmutable $autorizacionDatosFecha = null,
        private ?string $autorizacionDatosVersion = null
    ) {}

    public function getTipoDoc(): string
    {
        return $this->tipoDoc;
    }

    public function getNumDoc(): string
    {
        return $this->numDoc;
    }

    public function getNombreRazonSocial(): string
    {
        return $this->nombreRazonSocial;
    }

    public function getEmailFacturacion(): ?string
    {
        return $this->emailFacturacion;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function getDireccion(): ?string
    {
        return $this->direccion;
    }

    public function getMunicipio(): ?string
    {
        return $this->municipio;
    }

    public function getAutorizacionDatosFecha(): ?DateTimeImmutable
    {
        return $this->autorizacionDatosFecha;
    }

    public function getAutorizacionDatosVersion(): ?string
    {
        return $this->autorizacionDatosVersion;
    }

    public function tieneAutorizacionDatos(): bool
    {
        return $this->autorizacionDatosFecha !== null;
    }

    public function registrarAutorizacionDatos(string $version): void
    {
        $this->autorizacionDatosFecha = new DateTimeImmutable;
        $this->autorizacionDatosVersion = $version;
    }

    public function actualizarContacto(?string $telefono, ?string $direccion, ?string $email): void
    {
        if ($telefono !== null) {
            $this->telefono = $telefono;
        }
        if ($direccion !== null) {
            $this->direccion = $direccion;
        }
        if ($email !== null) {
            $this->emailFacturacion = $email;
        }
    }
}
