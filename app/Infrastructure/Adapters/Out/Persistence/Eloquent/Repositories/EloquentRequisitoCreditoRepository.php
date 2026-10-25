<?php

namespace App\Infrastructure\Adapters\Out\Persistence\Eloquent\Repositories;

use App\Application\Ports\Out\RequisitoCreditoRepositoryPort;
use App\Domain\Model\RequisitoCredito;
use App\Infrastructure\Adapters\Out\Persistence\Eloquent\Mappers\RequisitoCreditoMapper;
use App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models\RequisitoCredito as RequisitoCreditoEloquent;

class EloquentRequisitoCreditoRepository implements RequisitoCreditoRepositoryPort
{
    /**
     * @return RequisitoCredito[]
     */
    public function listarTodos(): array
    {
        return RequisitoCreditoEloquent::query()
            ->orderBy('id')
            ->get()
            ->map(fn (RequisitoCreditoEloquent $model) => RequisitoCreditoMapper::toDomain($model))
            ->all();
    }

    /**
     * @return RequisitoCredito[]
     */
    public function buscarPorMedioPago(string $medioPago): array
    {
        return RequisitoCreditoEloquent::query()
            ->whereRaw('UPPER(medio_pago) = ?', [strtoupper($medioPago)])
            ->orderBy('id')
            ->get()
            ->map(fn (RequisitoCreditoEloquent $model) => RequisitoCreditoMapper::toDomain($model))
            ->all();
    }

    public function buscarPorId(int $id): ?RequisitoCredito
    {
        $model = RequisitoCreditoEloquent::query()->find($id);

        return $model ? RequisitoCreditoMapper::toDomain($model) : null;
    }

    public function guardar(RequisitoCredito $requisito): RequisitoCredito
    {
        $model = RequisitoCreditoMapper::toEloquent($requisito);
        $model->save();

        return RequisitoCreditoMapper::toDomain($model);
    }
}
