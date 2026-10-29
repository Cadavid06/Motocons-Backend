<?php

namespace App\Infrastructure\Persistence\Pdo\Mappers;

use App\Domain\Model\Motocicleta as MotocicletaDomain;
use DateTimeImmutable;

final class MotocicletaMapper
{
    /**
     * @param  array<string, mixed>  $row
     */
    public static function toDomain(array $row): MotocicletaDomain
    {
        return new MotocicletaDomain(
            vin: (string) $row['vin'],
            numeroMotor: (string) $row['numero_motor'],
            color: (string) $row['color'],
            estado: (string) $row['estado'],
            modeloReferencia: (string) $row['modelo_referencia'],
            precioLista: (float) $row['precio_lista'],
            descuento: (float) ($row['descuento'] ?? 0),
            placa: self::nullableString($row['placa'] ?? null),
            garantiaHasta: self::date($row['garantia_hasta'] ?? null),
            recepcionId: ($row['recepcion_id'] ?? null) !== null ? (int) $row['recepcion_id'] : null,
            ventaNumero: self::nullableString($row['venta_numero'] ?? null)
        );
    }

    /**
     * @return array<string, mixed>
     */
    public static function toRow(MotocicletaDomain $motocicleta): array
    {
        return [
            'vin' => $motocicleta->getVin(),
            'numero_motor' => $motocicleta->getNumeroMotor(),
            'color' => $motocicleta->getColor(),
            'placa' => $motocicleta->getPlaca(),
            'estado' => $motocicleta->getEstado(),
            'garantia_hasta' => $motocicleta->getGarantiaHasta()?->format('Y-m-d'),
            'modelo_referencia' => $motocicleta->getModeloReferencia(),
            'recepcion_id' => $motocicleta->getRecepcionId(),
            'venta_numero' => $motocicleta->getVentaNumero(),
            'precio_lista' => $motocicleta->getPrecioLista(),
            'descuento' => $motocicleta->getDescuento(),
        ];
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
}
