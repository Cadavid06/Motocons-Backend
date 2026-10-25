<?php

namespace App\Infrastructure\Adapters\Out\Persistence\Eloquent\Mappers;

use App\Domain\Model\Venta as VentaDomain;
use App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models\Venta as VentaEloquent;
use DateTimeImmutable;

class VentaMapper
{
    public static function toDomain(VentaEloquent $eloquent): VentaDomain
    {
        $fecha = $eloquent->fecha
            ? DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $eloquent->fecha->format('Y-m-d H:i:s'))
            : new DateTimeImmutable;

        $cierreFecha = $eloquent->cierre_fecha_inicio
            ? DateTimeImmutable::createFromFormat('Y-m-d', $eloquent->cierre_fecha_inicio->format('Y-m-d'))
            : null;

        $terminosFecha = $eloquent->aceptacion_terminos
            ? DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $eloquent->aceptacion_terminos->format('Y-m-d H:i:s'))
            : null;

        return new VentaDomain(
            numero: $eloquent->numero,
            fecha: $fecha,
            canal: $eloquent->canal,
            estado: $eloquent->estado,
            clienteTipoDoc: $eloquent->cliente_tipo_doc,
            clienteNumDoc: $eloquent->cliente_num_doc,
            vendedorTipoDoc: $eloquent->vendedor_tipo_doc,
            vendedorNumDoc: $eloquent->vendedor_num_doc,
            promocionId: $eloquent->promocion_id,
            cierreTipo: $eloquent->cierre_tipo,
            cierreFechaInicio: $cierreFecha ?: null,
            liquidacionId: $eloquent->liquidacion_id,
            comisionPorcentaje: $eloquent->comision_porcentaje !== null ? (float) $eloquent->comision_porcentaje : null,
            comisionValor: $eloquent->comision_valor !== null ? (float) $eloquent->comision_valor : null,
            aceptacionTerminos: $terminosFecha ?: null
        );
    }

    public static function toEloquent(VentaDomain $domain): VentaEloquent
    {
        $eloquent = VentaEloquent::find($domain->getNumero()) ?? new VentaEloquent;

        $eloquent->numero = $domain->getNumero();
        $eloquent->fecha = $domain->getFecha()->format('Y-m-d H:i:s');
        $eloquent->canal = $domain->getCanal();
        $eloquent->estado = $domain->getEstado();
        $eloquent->cliente_tipo_doc = $domain->getClienteTipoDoc();
        $eloquent->cliente_num_doc = $domain->getClienteNumDoc();
        $eloquent->vendedor_tipo_doc = $domain->getVendedorTipoDoc();
        $eloquent->vendedor_num_doc = $domain->getVendedorNumDoc();
        $eloquent->promocion_id = $domain->getPromocionId();
        $eloquent->cierre_tipo = $domain->getCierreTipo();
        $eloquent->cierre_fecha_inicio = $domain->getCierreFechaInicio()?->format('Y-m-d');
        $eloquent->liquidacion_id = $domain->getLiquidacionId();
        $eloquent->comision_porcentaje = $domain->getComisionPorcentaje();
        $eloquent->comision_valor = $domain->getComisionValor();
        $eloquent->aceptacion_terminos = $domain->getAceptacionTerminos()?->format('Y-m-d H:i:s');

        return $eloquent;
    }
}
