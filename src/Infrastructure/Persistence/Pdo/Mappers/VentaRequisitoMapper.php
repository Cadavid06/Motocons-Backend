<?php

namespace App\Infrastructure\Persistence\Pdo\Mappers;

use App\Domain\Model\VentaRequisito as VentaRequisitoDomain;

final class VentaRequisitoMapper
{
    /**
     * @param  array<string, mixed>  $row
     */
    public static function toDomain(array $row): VentaRequisitoDomain
    {
        return new VentaRequisitoDomain(
            ventaNumero: (string) $row['venta_numero'],
            requisitoId: (int) $row['requisito_id'],
            estado: (string) $row['estado'],
            soporteAdjunto: self::nullableString($row['soporte_adjunto'] ?? null),
            validadoPor: self::nullableString($row['validado_por'] ?? null)
        );
    }

    /**
     * @return array<string, mixed>
     */
    public static function toRow(VentaRequisitoDomain $ventaRequisito): array
    {
        return [
            'venta_numero' => $ventaRequisito->getVentaNumero(),
            'requisito_id' => $ventaRequisito->getRequisitoId(),
            'estado' => $ventaRequisito->getEstado(),
            'soporte_adjunto' => $ventaRequisito->getSoporteAdjunto(),
            'validado_por' => $ventaRequisito->getValidadoPor(),
        ];
    }

    private static function nullableString(mixed $value): ?string
    {
        return $value === null ? null : (string) $value;
    }
}
