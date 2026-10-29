<?php

namespace App\Application\DTO;

readonly class SolicitudCreditoResponseDTO
{
    /**
     * @param  array<int, array{requisito_id: int, medio_pago: string, descripcion: string, obligatorio: bool, estado: string, soporte_adjunto: ?string, validado_por: ?string}>  $requisitos
     * @param  array{vin: string, modelo: string, color: string, precio_lista: float, descuento: float, precio_final: float}|null  $motocicleta
     */
    public function __construct(
        public string $ventaNumero,
        public string $fecha,
        public string $canal,
        public string $estado,
        public string $clienteTipoDoc,
        public string $clienteNumDoc,
        public string $medioPago,
        public array $requisitos,
        public ?array $motocicleta = null
    ) {}

    /**
     * @return array{venta_numero: string, fecha: string, canal: string, estado: string, cliente: array{tipo_doc: string, num_doc: string}, medio_pago: string, motocicleta: ?array{vin: string, modelo: string, color: string, precio_lista: float, descuento: float, precio_final: float}, requisitos: array<int, array{requisito_id: int, medio_pago: string, descripcion: string, obligatorio: bool, estado: string, soporte_adjunto: ?string, validado_por: ?string}>}
     */
    public function toArray(): array
    {
        return [
            'venta_numero' => $this->ventaNumero,
            'fecha' => $this->fecha,
            'canal' => $this->canal,
            'estado' => $this->estado,
            'cliente' => [
                'tipo_doc' => $this->clienteTipoDoc,
                'num_doc' => $this->clienteNumDoc,
            ],
            'medio_pago' => $this->medioPago,
            'motocicleta' => $this->motocicleta,
            'requisitos' => $this->requisitos,
        ];
    }
}
