<?php

namespace App\Domain\Exceptions;

class GarantiaExcedidaException extends DomainException
{
    public static function limiteRevisionesAlcanzado(string $vin): self
    {
        return new self("La motocicleta con VIN {$vin} ya ha utilizado el máximo de 3 mantenimientos oficiales de garantía.");
    }

    public static function garantiaVencidaPorFecha(string $vin): self
    {
        return new self("La motocicleta con VIN {$vin} ya tiene su garantía vencida por fecha.");
    }
}
