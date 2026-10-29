<?php

namespace App\Application\DTO;

use App\Domain\Model\VentaRequisito;

readonly class VentaRequisitoResponseDTO
{
    public function __construct(
        public string $ventaNumero,
        public int $requisitoId,
        public string $estado,
        public ?string $soporteAdjunto = null,
        public ?string $validadoPor = null
    ) {}

    public static function fromDomain(VentaRequisito $vr): self
    {
        return new self(
            ventaNumero: $vr->getVentaNumero(),
            requisitoId: $vr->getRequisitoId(),
            estado: $vr->getEstado(),
            soporteAdjunto: $vr->getSoporteAdjunto(),
            validadoPor: $vr->getValidadoPor()
        );
    }

    /**
     * @return array{venta_numero: string, requisito_id: int, estado: string, soporte_adjunto: ?string, validado_por: ?string}
     */
    public function toArray(): array
    {
        return [
            'venta_numero' => $this->ventaNumero,
            'requisito_id' => $this->requisitoId,
            'estado' => $this->estado,
            'soporte_adjunto' => $this->soporteAdjunto,
            'validado_por' => $this->validadoPor,
        ];
    }
}
