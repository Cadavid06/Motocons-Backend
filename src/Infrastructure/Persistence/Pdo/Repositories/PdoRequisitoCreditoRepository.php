<?php

namespace App\Infrastructure\Persistence\Pdo\Repositories;

use App\Application\Ports\Out\RequisitoCreditoRepositoryPort;
use App\Domain\Model\RequisitoCredito;
use App\Infrastructure\Persistence\Pdo\Mappers\RequisitoCreditoMapper;
use PDO;

final class PdoRequisitoCreditoRepository implements RequisitoCreditoRepositoryPort
{
    public function __construct(private readonly PDO $pdo) {}

    /**
     * @return RequisitoCredito[]
     */
    public function listarTodos(): array
    {
        $statement = $this->pdo->query('SELECT * FROM requisito_credito ORDER BY id');

        return array_map(
            static fn (array $row): RequisitoCredito => RequisitoCreditoMapper::toDomain($row),
            $statement->fetchAll()
        );
    }

    /**
     * @return RequisitoCredito[]
     */
    public function buscarPorMedioPago(string $medioPago): array
    {
        $statement = $this->pdo->prepare(
            'SELECT * FROM requisito_credito WHERE UPPER(medio_pago) = ? ORDER BY id'
        );
        $statement->execute([strtoupper($medioPago)]);

        return array_map(
            static fn (array $row): RequisitoCredito => RequisitoCreditoMapper::toDomain($row),
            $statement->fetchAll()
        );
    }

    public function buscarPorId(int $id): ?RequisitoCredito
    {
        $statement = $this->pdo->prepare('SELECT * FROM requisito_credito WHERE id = ?');
        $statement->execute([$id]);
        $row = $statement->fetch();

        return is_array($row) ? RequisitoCreditoMapper::toDomain($row) : null;
    }

    public function guardar(RequisitoCredito $requisito): RequisitoCredito
    {
        $row = RequisitoCreditoMapper::toRow($requisito);

        if ($requisito->getId() !== null) {
            $assignments = implode(', ', array_map(
                static fn (string $column): string => "{$column} = :{$column}",
                array_keys($row)
            ));

            $statement = $this->pdo->prepare(
                "UPDATE requisito_credito SET {$assignments} WHERE id = :id"
            );
            $statement->execute($row + ['id' => $requisito->getId()]);

            return $this->buscarPorId($requisito->getId()) ?? $requisito;
        }

        $columns = implode(', ', array_keys($row));
        $placeholders = implode(', ', array_map(
            static fn (string $column): string => ":{$column}",
            array_keys($row)
        ));

        $statement = $this->pdo->prepare(
            "INSERT INTO requisito_credito ({$columns}) VALUES ({$placeholders})"
        );
        $statement->execute($row);

        $id = (int) $this->pdo->lastInsertId();

        return $id > 0 ? ($this->buscarPorId($id) ?? $requisito) : $requisito;
    }
}
