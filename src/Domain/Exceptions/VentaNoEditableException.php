<?php

namespace App\Domain\Exceptions;

class VentaNoEditableException extends DomainException
{
    public static function estadoNoPermiteCambios(string $numero, string $estado): self
    {
        return new self("La venta {$numero} se encuentra en estado '{$estado}' y no puede ser modificada.");
    }
}
