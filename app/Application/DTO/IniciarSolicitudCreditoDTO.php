<?php

namespace App\Application\DTO;

use DateTimeImmutable;

readonly class IniciarSolicitudCreditoDTO
{
    public function __construct(
        public string $clienteTipoDoc,
        public string $clienteNumDoc,
        public string $medioPago, // 'BRILLA', 'SERVICREDITO'
        public ?string $ventaNumero = null,
        public ?string $vinMoto = null,
        public ?string $vendedorTipoDoc = null,
        public ?string $vendedorNumDoc = null,
        public string $canal = 'PRESENCIAL',
        public ?DateTimeImmutable $aceptacionTerminos = null
    ) {}
}
