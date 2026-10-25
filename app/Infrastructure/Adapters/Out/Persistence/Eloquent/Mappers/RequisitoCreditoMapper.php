<?php

namespace App\Infrastructure\Adapters\Out\Persistence\Eloquent\Mappers;

use App\Domain\Model\RequisitoCredito as RequisitoCreditoDomain;
use App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models\RequisitoCredito as RequisitoCreditoEloquent;

class RequisitoCreditoMapper
{
    public static function toDomain(RequisitoCreditoEloquent $eloquent): RequisitoCreditoDomain
    {
        return new RequisitoCreditoDomain(
            id: $eloquent->id,
            medioPago: $eloquent->medio_pago,
            descripcion: $eloquent->descripcion,
            obligatorio: (bool) $eloquent->obligatorio
        );
    }

    public static function toEloquent(RequisitoCreditoDomain $domain): RequisitoCreditoEloquent
    {
        $eloquent = new RequisitoCreditoEloquent;
        if ($domain->getId() !== null) {
            $eloquent->id = $domain->getId();
            $eloquent->exists = true;
        }
        $eloquent->medio_pago = $domain->getMedioPago();
        $eloquent->descripcion = $domain->getDescripcion();
        $eloquent->obligatorio = $domain->isObligatorio();

        return $eloquent;
    }
}
