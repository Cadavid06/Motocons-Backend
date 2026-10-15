<?php

namespace App\Domain\Model;

use DateTimeImmutable;

class Recepcion
{
    /** @var RecepcionProducto[] */
    private array $detalles = [];

    public function __construct(
        private ?int $id,
        private DateTimeImmutable $fecha,
        private ?string $cufeFacturaProveedor,
        private string $ordenCompraNumero
    ) {}

    public function getId(): ?int { return $this->id; }
    public function getFecha(): DateTimeImmutable { return $this->fecha; }
    public function getCufeFacturaProveedor(): ?string { return $this->cufeFacturaProveedor; }
    public function getOrdenCompraNumero(): string { return $this->ordenCompraNumero; }
    public function getDetalles(): array { return $this->detalles; }

    public function agregarDetalle(RecepcionProducto $detalle): void
    {
        $this->detalles[] = $detalle;
    }
}
