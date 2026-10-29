<?php

namespace App\Infrastructure\Persistence\Pdo\Mappers;

use App\Domain\Model\Venta as VentaDomain;
use DateTimeImmutable;

final class VentaMapper
{
    /**
     * @param  array<string, mixed>  $row
     */
    public static function toDomain(array $row): VentaDomain
    {
        $fecha = self::dateTime($row['fecha'] ?? null) ?? new DateTimeImmutable;

        return new VentaDomain(
            numero: (string) $row['numero'],
            fecha: $fecha,
            canal: (string) $row['canal'],
            estado: (string) $row['estado'],
            clienteTipoDoc: (string) $row['cliente_tipo_doc'],
            clienteNumDoc: (string) $row['cliente_num_doc'],
            vendedorTipoDoc: self::nullableString($row['vendedor_tipo_doc'] ?? null),
            vendedorNumDoc: self::nullableString($row['vendedor_num_doc'] ?? null),
            promocionId: self::nullableInt($row['promocion_id'] ?? null),
            cierreTipo: self::nullableString($row['cierre_tipo'] ?? null),
            cierreFechaInicio: self::date($row['cierre_fecha_inicio'] ?? null),
            liquidacionId: self::nullableInt($row['liquidacion_id'] ?? null),
            comisionPorcentaje: self::nullableFloat($row['comision_porcentaje'] ?? null),
            comisionValor: self::nullableFloat($row['comision_valor'] ?? null),
            aceptacionTerminos: self::dateTime($row['aceptacion_terminos'] ?? null)
        );
    }

    /**
     * @return array<string, mixed>
     */
    public static function toRow(VentaDomain $venta): array
    {
        return [
            'numero' => $venta->getNumero(),
            'fecha' => $venta->getFecha()->format('Y-m-d H:i:s'),
            'canal' => $venta->getCanal(),
            'estado' => $venta->getEstado(),
            'aceptacion_terminos' => $venta->getAceptacionTerminos()?->format('Y-m-d H:i:s'),
            'cliente_tipo_doc' => $venta->getClienteTipoDoc(),
            'cliente_num_doc' => $venta->getClienteNumDoc(),
            'vendedor_tipo_doc' => $venta->getVendedorTipoDoc(),
            'vendedor_num_doc' => $venta->getVendedorNumDoc(),
            'promocion_id' => $venta->getPromocionId(),
            'cierre_tipo' => $venta->getCierreTipo(),
            'cierre_fecha_inicio' => $venta->getCierreFechaInicio()?->format('Y-m-d'),
            'liquidacion_id' => $venta->getLiquidacionId(),
            'comision_porcentaje' => $venta->getComisionPorcentaje(),
            'comision_valor' => $venta->getComisionValor(),
        ];
    }

    private static function dateTime(mixed $value): ?DateTimeImmutable
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return new DateTimeImmutable((string) $value);
        } catch (\Throwable) {
            return null;
        }
    }

    private static function date(mixed $value): ?DateTimeImmutable
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return new DateTimeImmutable((string) $value);
        } catch (\Throwable) {
            return null;
        }
    }

    private static function nullableString(mixed $value): ?string
    {
        return $value === null ? null : (string) $value;
    }

    private static function nullableInt(mixed $value): ?int
    {
        return $value === null ? null : (int) $value;
    }

    private static function nullableFloat(mixed $value): ?float
    {
        return $value === null ? null : (float) $value;
    }
}
