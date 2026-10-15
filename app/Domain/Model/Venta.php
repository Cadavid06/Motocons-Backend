<?php

namespace App\Domain\Model;

use DateTimeImmutable;
use App\Domain\Exceptions\VentaNoEditableException;

class Venta
{
    /** @var Motocicleta[] */
    private array $motocicletas = [];
    /** @var VentaRequisito[] */
    private array $requisitos = [];

    public function __construct(
        private string $numero,
        private DateTimeImmutable $fecha,
        private string $canal, // 'PRESENCIAL', 'WEB'
        private string $estado, // 'COTIZACION', 'EN_PROCESO_CREDITO', 'PAGADA', 'ENTREGADA', 'ANULADA'
        private string $clienteTipoDoc,
        private string $clienteNumDoc,
        private ?string $vendedorTipoDoc = null,
        private ?string $vendedorNumDoc = null,
        private ?int $promocionId = null,
        private ?string $cierreTipo = null,
        private ?DateTimeImmutable $cierreFechaInicio = null,
        private ?int $liquidacionId = null,
        private ?float $comisionPorcentaje = null,
        private ?float $comisionValor = null,
        private ?DateTimeImmutable $aceptacionTerminos = null
    ) {}

    public function getNumero(): string { return $this->numero; }
    public function getFecha(): DateTimeImmutable { return $this->fecha; }
    public function getCanal(): string { return $this->canal; }
    public function getEstado(): string { return $this->estado; }
    public function getClienteTipoDoc(): string { return $this->clienteTipoDoc; }
    public function getClienteNumDoc(): string { return $this->clienteNumDoc; }
    public function getVendedorTipoDoc(): ?string { return $this->vendedorTipoDoc; }
    public function getVendedorNumDoc(): ?string { return $this->vendedorNumDoc; }
    public function getPromocionId(): ?int { return $this->promocionId; }
    public function getCierreTipo(): ?string { return $this->cierreTipo; }
    public function getCierreFechaInicio(): ?DateTimeImmutable { return $this->cierreFechaInicio; }
    public function getLiquidacionId(): ?int { return $this->liquidacionId; }
    public function getComisionPorcentaje(): ?float { return $this->comisionPorcentaje; }
    public function getComisionValor(): ?float { return $this->comisionValor; }
    public function getAceptacionTerminos(): ?DateTimeImmutable { return $this->aceptacionTerminos; }

    public function esVentaWeb(): bool
    {
        return strcasecmp($this->canal, 'WEB') === 0;
    }

    public function esVentaPresencial(): bool
    {
        return strcasecmp($this->canal, 'PRESENCIAL') === 0;
    }

    public function puedeModificarse(): bool
    {
        return in_array(strtoupper($this->estado), ['COTIZACION', 'EN_PROCESO_CREDITO']);
    }

    public function registrarPago(): void
    {
        if (!$this->puedeModificarse()) {
            throw VentaNoEditableException::estadoNoPermiteCambios($this->numero, $this->estado);
        }
        $this->estado = 'PAGADA';
    }

    public function entregar(): void
    {
        $this->estado = 'ENTREGADA';
    }

    public function anular(): void
    {
        $this->estado = 'ANULADA';
    }

    public function calcularComision(float $porcentaje): void
    {
        $this->comisionPorcentaje = $porcentaje;
        $totalVenta = 0.0;
        foreach ($this->motocicletas as $moto) {
            $totalVenta += $moto->getPrecioFinal();
        }
        $this->comisionValor = round($totalVenta * ($porcentaje / 100), 2);
    }

    public function asociarMotocicleta(Motocicleta $moto): void
    {
        $this->motocicletas[] = $moto;
    }

    public function asociarRequisito(VentaRequisito $req): void
    {
        $this->requisitos[] = $req;
    }
}
