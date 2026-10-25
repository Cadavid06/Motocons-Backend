<?php

namespace App\Infrastructure\Adapters\Out\Persistence\Eloquent\Mappers;

use App\Domain\Model\VentaRequisito as VentaRequisitoDomain;
use App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models\VentaRequisito as VentaRequisitoEloquent;

class VentaRequisitoMapper
{
    public static function toDomain(VentaRequisitoEloquent $eloquent): VentaRequisitoDomain
    {
        return new VentaRequisitoDomain(
            ventaNumero: $eloquent->venta_numero,
            requisitoId: (int) $eloquent->requisito_id,
            estado: $eloquent->estado,
            soporteAdjunto: $eloquent->soporte_adjunto,
            validadoPor: $eloquent->validado_por
        );
    }

    public static function toEloquent(VentaRequisitoDomain $domain): VentaRequisitoEloquent
    {
        $eloquent = VentaRequisitoEloquent::where('venta_numero', $domain->getVentaNumero())
            ->where('requisito_id', $domain->getRequisitoId())
            ->first() ?? new VentaRequisitoEloquent;

        $eloquent->venta_numero = $domain->getVentaNumero();
        $eloquent->requisito_id = $domain->getRequisitoId();
        $eloquent->estado = $domain->getEstado();
        $eloquent->soporte_adjunto = $domain->getSoporteAdjunto();
        $eloquent->validado_por = $domain->getValidadoPor();

        return $eloquent;
    }
}
