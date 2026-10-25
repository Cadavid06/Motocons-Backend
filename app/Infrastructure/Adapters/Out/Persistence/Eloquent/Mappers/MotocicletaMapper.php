<?php

namespace App\Infrastructure\Adapters\Out\Persistence\Eloquent\Mappers;

use App\Domain\Model\Motocicleta as MotocicletaDomain;
use App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models\Motocicleta as MotocicletaEloquent;
use DateTimeImmutable;

class MotocicletaMapper
{
    public static function toDomain(MotocicletaEloquent $eloquent): MotocicletaDomain
    {
        $garantia = $eloquent->garantia_hasta
            ? DateTimeImmutable::createFromFormat('Y-m-d', $eloquent->garantia_hasta->format('Y-m-d'))
            : null;

        return new MotocicletaDomain(
            vin: $eloquent->vin,
            numeroMotor: $eloquent->numero_motor,
            color: $eloquent->color,
            estado: $eloquent->estado,
            modeloReferencia: $eloquent->modelo_referencia,
            precioLista: (float) $eloquent->precio_lista,
            descuento: (float) $eloquent->descuento,
            placa: $eloquent->placa,
            garantiaHasta: $garantia ?: null,
            recepcionId: $eloquent->recepcion_id !== null ? (int) $eloquent->recepcion_id : null,
            ventaNumero: $eloquent->venta_numero
        );
    }

    public static function toEloquent(MotocicletaDomain $domain): MotocicletaEloquent
    {
        $eloquent = MotocicletaEloquent::find($domain->getVin()) ?? new MotocicletaEloquent;

        $eloquent->vin = $domain->getVin();
        $eloquent->numero_motor = $domain->getNumeroMotor();
        $eloquent->color = $domain->getColor();
        $eloquent->estado = $domain->getEstado();
        $eloquent->modelo_referencia = $domain->getModeloReferencia();
        $eloquent->precio_lista = $domain->getPrecioLista();
        $eloquent->descuento = $domain->getDescuento();
        $eloquent->placa = $domain->getPlaca();
        $eloquent->garantia_hasta = $domain->getGarantiaHasta()?->format('Y-m-d');
        $eloquent->recepcion_id = $domain->getRecepcionId();
        $eloquent->venta_numero = $domain->getVentaNumero();

        return $eloquent;
    }
}
