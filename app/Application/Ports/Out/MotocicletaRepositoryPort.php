<?php

namespace App\Application\Ports\Out;

use App\Domain\Model\Motocicleta;

interface MotocicletaRepositoryPort
{
    public function buscarPorVin(string $vin): ?Motocicleta;

    public function guardar(Motocicleta $motocicleta): Motocicleta;
}
