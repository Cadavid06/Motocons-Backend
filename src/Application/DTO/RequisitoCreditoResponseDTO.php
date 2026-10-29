<?php

namespace App\Application\DTO;

use App\Domain\Model\RequisitoCredito;

readonly class RequisitoCreditoResponseDTO
{
    public function __construct(
        public ?int $id,
        public string $medioPago,
        public string $descripcion,
        public bool $obligatorio
    ) {}

    public static function fromDomain(RequisitoCredito $requisito): self
    {
        return new self(
            id: $requisito->getId(),
            medioPago: $requisito->getMedioPago(),
            descripcion: $requisito->getDescripcion(),
            obligatorio: $requisito->isObligatorio()
        );
    }

    /**
     * @return array{id: ?int, medio_pago: string, descripcion: string, obligatorio: bool}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'medio_pago' => $this->medioPago,
            'descripcion' => $this->descripcion,
            'obligatorio' => $this->obligatorio,
        ];
    }
}
