<?php

namespace App\Domain\Model;

use DateTimeImmutable;

class ResolucionDian
{
    public function __construct(
        private string $numeroResolucion,
        private ?string $prefijo,
        private int $rangoDesde,
        private int $rangoHasta,
        private DateTimeImmutable $vigenciaDesde,
        private DateTimeImmutable $vigenciaHasta
    ) {}

    public function getNumeroResolucion(): string { return $this->numeroResolucion; }
    public function getPrefijo(): ?string { return $this->prefijo; }
    public function getRangoDesde(): int { return $this->rangoDesde; }
    public function getRangoHasta(): int { return $this->rangoHasta; }
    public function getVigenciaDesde(): DateTimeImmutable { return $this->vigenciaDesde; }
    public function getVigenciaHasta(): DateTimeImmutable { return $this->vigenciaHasta; }

    public function esVigente(?DateTimeImmutable $fecha = null): bool
    {
        $fecha = $fecha ?? new DateTimeImmutable();
        return $fecha >= $this->vigenciaDesde && $fecha <= $this->vigenciaHasta;
    }

    public function consecutivoEstaEnRango(int $consecutivo): bool
    {
        return $consecutivo >= $this->rangoDesde && $consecutivo <= $this->rangoHasta;
    }
}
