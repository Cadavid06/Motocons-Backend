<?php

namespace App\Infrastructure\Adapters\Out\Persistence\Eloquent\Repositories;

use App\Application\Ports\Out\VentaRequisitoRepositoryPort;
use App\Domain\Model\VentaRequisito;
use App\Infrastructure\Adapters\Out\Persistence\Eloquent\Mappers\VentaRequisitoMapper;
use App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models\VentaRequisito as VentaRequisitoEloquent;

class EloquentVentaRequisitoRepository implements VentaRequisitoRepositoryPort
{
    public function guardar(VentaRequisito $ventaRequisito): VentaRequisito
    {
        $model = VentaRequisitoMapper::toEloquent($ventaRequisito);
        $model->save();

        return VentaRequisitoMapper::toDomain($model);
    }

    /**
     * @param  VentaRequisito[]  $ventaRequisitos
     */
    public function guardarMuchos(array $ventaRequisitos): void
    {
        foreach ($ventaRequisitos as $req) {
            $this->guardar($req);
        }
    }

    /**
     * @return VentaRequisito[]
     */
    public function buscarPorVenta(string $ventaNumero): array
    {
        return VentaRequisitoEloquent::where('venta_numero', $ventaNumero)
            ->orderBy('requisito_id')
            ->get()
            ->map(fn (VentaRequisitoEloquent $m) => VentaRequisitoMapper::toDomain($m))
            ->all();
    }

    public function buscarPorVentaYRequisito(string $ventaNumero, int $requisitoId): ?VentaRequisito
    {
        $model = VentaRequisitoEloquent::where('venta_numero', $ventaNumero)
            ->where('requisito_id', $requisitoId)
            ->first();

        return $model ? VentaRequisitoMapper::toDomain($model) : null;
    }
}
