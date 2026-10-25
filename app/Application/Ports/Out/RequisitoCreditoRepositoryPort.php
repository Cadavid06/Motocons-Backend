<?php

namespace App\Application\Ports\Out;

use App\Domain\Model\RequisitoCredito;

interface RequisitoCreditoRepositoryPort
{
    /**
     * @return RequisitoCredito[]
     */
    public function listarTodos(): array;

    /**
     * @return RequisitoCredito[]
     */
    public function buscarPorMedioPago(string $medioPago): array;

    public function buscarPorId(int $id): ?RequisitoCredito;

    public function guardar(RequisitoCredito $requisito): RequisitoCredito;
}
