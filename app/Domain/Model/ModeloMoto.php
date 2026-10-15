<?php

namespace App\Domain\Model;

class ModeloMoto
{
    public function __construct(
        private string $referencia,
        private string $marca,
        private string $linea,
        private int $anio,
        private string $gama, // 'baja', 'media', 'alta'
        private string $propulsion, // 'combustion', 'electrica'
        private ?int $cilindrajeCc = null,
        private ?string $fichaTecnica = null
    ) {}

    public function getReferencia(): string { return $this->referencia; }
    public function getMarca(): string { return $this->marca; }
    public function getLinea(): string { return $this->linea; }
    public function getAnio(): int { return $this->anio; }
    public function getGama(): string { return $this->gama; }
    public function getPropulsion(): string { return $this->propulsion; }
    public function getCilindrajeCc(): ?int { return $this->cilindrajeCc; }
    public function getFichaTecnica(): ?string { return $this->fichaTecnica; }

    public function esElectrica(): bool
    {
        return strcasecmp($this->propulsion, 'electrica') === 0;
    }

    public function esCombustion(): bool
    {
        return strcasecmp($this->propulsion, 'combustion') === 0;
    }

    public function perteneceAGama(string $gamaBuscada): bool
    {
        return strcasecmp($this->gama, $gamaBuscada) === 0;
    }
}
