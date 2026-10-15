<?php

namespace App\Domain\Model;

use DateTimeImmutable;

class Promocion
{
    public function __construct(
        private ?int $id,
        private string $tipo, // 'PORCENTAJE', 'VALOR_FIJO'
        private float $valor,
        private DateTimeImmutable $fechaInicio,
        private DateTimeImmutable $fechaFin,
        private ?string $gamaObjetivo = null,
        private ?string $propulsionObjetivo = null
    ) {}

    public function getId(): ?int { return $this->id; }
    public function getTipo(): string { return $this->tipo; }
    public function getValor(): float { return $this->valor; }
    public function getFechaInicio(): DateTimeImmutable { return $this->fechaInicio; }
    public function getFechaFin(): DateTimeImmutable { return $this->fechaFin; }
    public function getGamaObjetivo(): ?string { return $this->gamaObjetivo; }
    public function getPropulsionObjetivo(): ?string { return $this->propulsionObjetivo; }

    public function esVigenteEnFecha(?DateTimeImmutable $fecha = null): bool
    {
        $fecha = $fecha ?? new DateTimeImmutable();
        return $fecha >= $this->fechaInicio && $fecha <= $this->fechaFin;
    }

    public function aplicaAModelo(ModeloMoto $modelo): bool
    {
        if ($this->gamaObjetivo !== null && strcasecmp($this->gamaObjetivo, $modelo->getGama()) !== 0) {
            return false;
        }

        if ($this->propulsionObjetivo !== null && strcasecmp($this->propulsionObjetivo, $modelo->getPropulsion()) !== 0) {
            return false;
        }

        return true;
    }

    public function calcularDescuento(float $precioBase): float
    {
        if (strcasecmp($this->tipo, 'PORCENTAJE') === 0) {
            return round($precioBase * ($this->valor / 100), 2);
        }
        return min($this->valor, $precioBase);
    }
}
