<?php

namespace App\Application\DTO;

readonly class AprobacionCreditoResponseDTO
{
    public function __construct(
        public string $ventaNumero,
        public string $estado,
        public string $aprobadoPor,
        public string $fecha,
        public string $mensaje
    ) {}

    /**
     * @return array{venta_numero: string, estado: string, aprobado_por: string, fecha: string, mensaje: string}
     */
    public function toArray(): array
    {
        return [
            'venta_numero' => $this->ventaNumero,
            'estado' => $this->estado,
            'aprobado_por' => $this->aprobadoPor,
            'fecha' => $this->fecha,
            'mensaje' => $this->mensaje,
        ];
    }
}
