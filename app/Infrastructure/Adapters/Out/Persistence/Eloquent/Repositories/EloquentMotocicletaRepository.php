<?php

namespace App\Infrastructure\Adapters\Out\Persistence\Eloquent\Repositories;

use App\Application\Ports\Out\MotocicletaRepositoryPort;
use App\Domain\Model\Motocicleta;
use App\Infrastructure\Adapters\Out\Persistence\Eloquent\Mappers\MotocicletaMapper;
use App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models\Motocicleta as MotocicletaEloquent;

class EloquentMotocicletaRepository implements MotocicletaRepositoryPort
{
    public function buscarPorVin(string $vin): ?Motocicleta
    {
        $model = MotocicletaEloquent::find($vin);

        return $model ? MotocicletaMapper::toDomain($model) : null;
    }

    public function guardar(Motocicleta $motocicleta): Motocicleta
    {
        $model = MotocicletaMapper::toEloquent($motocicleta);
        $model->save();

        return MotocicletaMapper::toDomain($model);
    }
}
