<?php

namespace App\Application\Ports\Out;

use App\Domain\Model\Venta;

interface VentaRepositoryPort
{
    public function buscarPorNumero(string $numero): ?Venta;

    public function guardar(Venta $venta): Venta;

    public function generarSiguienteNumero(): string;
}
