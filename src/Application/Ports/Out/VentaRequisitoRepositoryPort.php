<?php

namespace App\Application\Ports\Out;

use App\Domain\Model\VentaRequisito;

interface VentaRequisitoRepositoryPort
{
    public function guardar(VentaRequisito $ventaRequisito): VentaRequisito;

    /**
     * @param  VentaRequisito[]  $ventaRequisitos
     */
    public function guardarMuchos(array $ventaRequisitos): void;

    /**
     * @return VentaRequisito[]
     */
    public function buscarPorVenta(string $ventaNumero): array;

    public function buscarPorVentaYRequisito(string $ventaNumero, int $requisitoId): ?VentaRequisito;
}
