<?php

namespace App\Domain\Model;

use App\Domain\Exceptions\GarantiaExcedidaException;
use DateTimeImmutable;

class OrdenServicio
{
    /** @var OrdenServicioProducto[] */
    private array $detalles = [];

    public function __construct(
        private string $numero,
        private DateTimeImmutable $fechaIngreso,
        private int $kilometraje,
        private string $tipo, // 'GARANTIA', 'GENERAL', 'CORRECTIVO'
        private string $motocicletaVin,
        private ?int $nroMantGarantia = null, // 1, 2 o 3 si es garantía
        private ?string $diagnostico = null,
        private ?string $mecanicoTipoDoc = null,
        private ?string $mecanicoNumDoc = null
    ) {
        if ($this->nroMantGarantia !== null && $this->nroMantGarantia > 3) {
            throw GarantiaExcedidaException::limiteRevisionesAlcanzado($this->motocicletaVin);
        }
    }

    public function getNumero(): string
    {
        return $this->numero;
    }

    public function getFechaIngreso(): DateTimeImmutable
    {
        return $this->fechaIngreso;
    }

    public function getKilometraje(): int
    {
        return $this->kilometraje;
    }

    public function getTipo(): string
    {
        return $this->tipo;
    }

    public function getMotocicletaVin(): string
    {
        return $this->motocicletaVin;
    }

    public function getNroMantGarantia(): ?int
    {
        return $this->nroMantGarantia;
    }

    public function getDiagnostico(): ?string
    {
        return $this->diagnostico;
    }

    public function getMecanicoTipoDoc(): ?string
    {
        return $this->mecanicoTipoDoc;
    }

    public function getMecanicoNumDoc(): ?string
    {
        return $this->mecanicoNumDoc;
    }

    public function getDetalles(): array
    {
        return $this->detalles;
    }

    public function agregarDetalle(OrdenServicioProducto $detalle): void
    {
        $this->detalles[] = $detalle;
    }

    /**
     * Regla de negocio HU-09: Mano de obra es gratuita en las primeras 3 revisiones de garantía.
     */
    public function aplicaManoDeObraGratis(): bool
    {
        return $this->nroMantGarantia !== null && $this->nroMantGarantia <= 3;
    }

    public function calcularTotalRepuestosEInsumos(): float
    {
        $total = 0.0;
        foreach ($this->detalles as $detalle) {
            $total += $detalle->subtotal();
        }

        return round($total, 2);
    }
}
