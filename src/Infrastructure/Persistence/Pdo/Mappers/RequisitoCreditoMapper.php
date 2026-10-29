<?php

namespace App\Infrastructure\Persistence\Pdo\Mappers;

use App\Domain\Model\RequisitoCredito as RequisitoCreditoDomain;

final class RequisitoCreditoMapper
{
    /**
     * @param  array<string, mixed>  $row
     */
    public static function toDomain(array $row): RequisitoCreditoDomain
    {
        return new RequisitoCreditoDomain(
            id: (int) $row['id'],
            medioPago: (string) $row['medio_pago'],
            descripcion: (string) $row['descripcion'],
            obligatorio: (bool) $row['obligatorio']
        );
    }

    /**
     * @return array<string, mixed>
     */
    public static function toRow(RequisitoCreditoDomain $requisito): array
    {
        return [
            'medio_pago' => $requisito->getMedioPago(),
            'descripcion' => $requisito->getDescripcion(),
            'obligatorio' => $requisito->isObligatorio(),
        ];
    }
}
