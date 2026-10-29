<?php

namespace App\Domain\Model;

use DateTimeImmutable;

class Auditoria
{
    public function __construct(
        private ?int $id,
        private DateTimeImmutable $fechaHora,
        private string $accion, // 'INSERT', 'UPDATE', 'DELETE', etc.
        private string $entidad,
        private string $registroId,
        private ?string $datosAntes = null,
        private ?string $datosDespues = null,
        private ?string $ipOrigen = null,
        private ?string $username = null
    ) {}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFechaHora(): DateTimeImmutable
    {
        return $this->fechaHora;
    }

    public function getAccion(): string
    {
        return $this->accion;
    }

    public function getEntidad(): string
    {
        return $this->entidad;
    }

    public function getRegistroId(): string
    {
        return $this->registroId;
    }

    public function getDatosAntes(): ?string
    {
        return $this->datosAntes;
    }

    public function getDatosDespues(): ?string
    {
        return $this->datosDespues;
    }

    public function getIpOrigen(): ?string
    {
        return $this->ipOrigen;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public static function crear(
        string $accion,
        string $entidad,
        string $registroId,
        ?string $datosAntes = null,
        ?string $datosDespues = null,
        ?string $ip = null,
        ?string $username = null
    ): self {
        return new self(
            id: null,
            fechaHora: new DateTimeImmutable,
            accion: strtoupper($accion),
            entidad: strtolower($entidad),
            registroId: $registroId,
            datosAntes: $datosAntes,
            datosDespues: $datosDespues,
            ipOrigen: $ip,
            username: $username
        );
    }
}
