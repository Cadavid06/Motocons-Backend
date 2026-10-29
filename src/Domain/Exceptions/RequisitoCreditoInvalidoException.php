<?php

namespace App\Domain\Exceptions;

class RequisitoCreditoInvalidoException extends DomainException
{
    public static function reciboGasObligatorio(string $ventaNumero): self
    {
        return new self("Para compras con Crédito Brilla en la venta {$ventaNumero}, es obligatorio adjuntar y validar el recibo de gas.");
    }
}
