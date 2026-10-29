<?php

namespace App\Infrastructure\Persistence\Pdo\Repositories;

use App\Application\Ports\Out\VentaRequisitoRepositoryPort;
use App\Domain\Model\VentaRequisito;
use App\Infrastructure\Persistence\Pdo\Mappers\VentaRequisitoMapper;
use PDO;

final class PdoVentaRequisitoRepository implements VentaRequisitoRepositoryPort
{
    public function __construct(private readonly PDO $pdo) {}

    public function guardar(VentaRequisito $ventaRequisito): VentaRequisito
    {
        $row = VentaRequisitoMapper::toRow($ventaRequisito);

        if ($this->existe($ventaRequisito->getVentaNumero(), $ventaRequisito->getRequisitoId())) {
            $statement = $this->pdo->prepare(
                'UPDATE venta_requisito
                 SET estado = :estado, soporte_adjunto = :soporte_adjunto, validado_por = :validado_por
                 WHERE venta_numero = :venta_numero AND requisito_id = :requisito_id'
            );
        } else {
            $statement = $this->pdo->prepare(
                'INSERT INTO venta_requisito (venta_numero, requisito_id, estado, soporte_adjunto, validado_por)
                 VALUES (:venta_numero, :requisito_id, :estado, :soporte_adjunto, :validado_por)'
            );
        }

        $statement->execute($row);

        return $this->buscarPorVentaYRequisito(
            $ventaRequisito->getVentaNumero(),
            $ventaRequisito->getRequisitoId()
        ) ?? $ventaRequisito;
    }

    /**
     * @param  VentaRequisito[]  $ventaRequisitos
     */
    public function guardarMuchos(array $ventaRequisitos): void
    {
        foreach ($ventaRequisitos as $ventaRequisito) {
            $this->guardar($ventaRequisito);
        }
    }

    /**
     * @return VentaRequisito[]
     */
    public function buscarPorVenta(string $ventaNumero): array
    {
        $statement = $this->pdo->prepare(
            'SELECT * FROM venta_requisito WHERE venta_numero = ? ORDER BY requisito_id'
        );
        $statement->execute([$ventaNumero]);

        return array_map(
            static fn (array $row): VentaRequisito => VentaRequisitoMapper::toDomain($row),
            $statement->fetchAll()
        );
    }

    public function buscarPorVentaYRequisito(string $ventaNumero, int $requisitoId): ?VentaRequisito
    {
        $statement = $this->pdo->prepare(
            'SELECT * FROM venta_requisito WHERE venta_numero = ? AND requisito_id = ?'
        );
        $statement->execute([$ventaNumero, $requisitoId]);
        $row = $statement->fetch();

        return is_array($row) ? VentaRequisitoMapper::toDomain($row) : null;
    }

    private function existe(string $ventaNumero, int $requisitoId): bool
    {
        $statement = $this->pdo->prepare(
            'SELECT 1 FROM venta_requisito WHERE venta_numero = ? AND requisito_id = ?'
        );
        $statement->execute([$ventaNumero, $requisitoId]);

        return $statement->fetchColumn() !== false;
    }
}
