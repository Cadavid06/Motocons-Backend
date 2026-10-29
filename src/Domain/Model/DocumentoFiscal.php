<?php

namespace App\Domain\Model;

use DateTimeImmutable;

class DocumentoFiscal
{
    /** @var DocumentoFiscalDetalle[] */
    private array $detalles = [];

    public function __construct(
        private string $prefijo,
        private int $consecutivo,
        private string $tipo, // 'FACTURA_VENTA', 'FACTURA_TALLER', 'NOTA_CREDITO', 'NOTA_DEBITO'
        private DateTimeImmutable $fechaHoraEmision,
        private string $estadoDian, // 'EMITIDA', 'ENVIADA', 'AUTORIZADA', 'RECHAZADA'
        private string $formaPago, // 'CONTADO', 'CREDITO'
        private string $clienteTipoDoc,
        private string $clienteNumDoc,
        private ?string $cufeCude = null,
        private ?string $xmlFirmado = null,
        private ?string $qr = null,
        private ?string $ventaNumero = null,
        private ?string $ordenServicioNumero = null,
        private ?string $numeroResolucion = null,
        private ?string $corrigePrefijo = null,
        private ?int $corrigeConsecutivo = null
    ) {}

    public function getPrefijo(): string
    {
        return $this->prefijo;
    }

    public function getConsecutivo(): int
    {
        return $this->consecutivo;
    }

    public function getTipo(): string
    {
        return $this->tipo;
    }

    public function getFechaHoraEmision(): DateTimeImmutable
    {
        return $this->fechaHoraEmision;
    }

    public function getEstadoDian(): string
    {
        return $this->estadoDian;
    }

    public function getFormaPago(): string
    {
        return $this->formaPago;
    }

    public function getClienteTipoDoc(): string
    {
        return $this->clienteTipoDoc;
    }

    public function getClienteNumDoc(): string
    {
        return $this->clienteNumDoc;
    }

    public function getCufeCude(): ?string
    {
        return $this->cufeCude;
    }

    public function getXmlFirmado(): ?string
    {
        return $this->xmlFirmado;
    }

    public function getQr(): ?string
    {
        return $this->qr;
    }

    public function getVentaNumero(): ?string
    {
        return $this->ventaNumero;
    }

    public function getOrdenServicioNumero(): ?string
    {
        return $this->ordenServicioNumero;
    }

    public function getNumeroResolucion(): ?string
    {
        return $this->numeroResolucion;
    }

    public function getCorrigePrefijo(): ?string
    {
        return $this->corrigePrefijo;
    }

    public function getCorrigeConsecutivo(): ?int
    {
        return $this->corrigeConsecutivo;
    }

    public function getDetalles(): array
    {
        return $this->detalles;
    }

    public function agregarDetalle(DocumentoFiscalDetalle $detalle): void
    {
        $this->detalles[] = $detalle;
    }

    public function autorizarPorDian(string $cufe, string $xml, string $qr): void
    {
        $this->cufeCude = $cufe;
        $this->xmlFirmado = $xml;
        $this->qr = $qr;
        $this->estadoDian = 'AUTORIZADA';
    }

    public function rechazarPorDian(): void
    {
        $this->estadoDian = 'RECHAZADA';
    }

    public function calcularSubtotal(): float
    {
        $sub = 0.0;
        foreach ($this->detalles as $d) {
            $sub += $d->subtotalNeto();
        }

        return round($sub, 2);
    }

    public function calcularTotal(): float
    {
        $total = 0.0;
        foreach ($this->detalles as $d) {
            $total += $d->totalConImpuestos();
        }

        return round($total, 2);
    }
}
