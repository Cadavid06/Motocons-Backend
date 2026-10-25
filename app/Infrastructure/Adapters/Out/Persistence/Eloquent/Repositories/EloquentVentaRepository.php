<?php

namespace App\Infrastructure\Adapters\Out\Persistence\Eloquent\Repositories;

use App\Application\Ports\Out\VentaRepositoryPort;
use App\Domain\Model\Venta;
use App\Infrastructure\Adapters\Out\Persistence\Eloquent\Mappers\VentaMapper;
use App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models\Venta as VentaEloquent;

class EloquentVentaRepository implements VentaRepositoryPort
{
    public function buscarPorNumero(string $numero): ?Venta
    {
        $model = VentaEloquent::find($numero);

        return $model ? VentaMapper::toDomain($model) : null;
    }

    public function guardar(Venta $venta): Venta
    {
        $model = VentaMapper::toEloquent($venta);
        $model->save();

        return VentaMapper::toDomain($model);
    }

    public function generarSiguienteNumero(): string
    {
        $prefijo = 'VTA-'.date('Ymd');
        $ultimo = VentaEloquent::where('numero', 'like', "{$prefijo}-%")
            ->orderByDesc('numero')
            ->value('numero');

        if (! $ultimo) {
            return "{$prefijo}-0001";
        }

        $secuencia = (int) substr($ultimo, strrpos($ultimo, '-') + 1);

        return sprintf('%s-%04d', $prefijo, $secuencia + 1);
    }
}
