<?php

namespace App\Infrastructure\Persistence\Pdo\Repositories;

use App\Application\Ports\Out\MotocicletaRepositoryPort;
use App\Domain\Model\Motocicleta;
use App\Infrastructure\Persistence\Pdo\Mappers\MotocicletaMapper;
use PDO;

final class PdoMotocicletaRepository implements MotocicletaRepositoryPort
{
    public function __construct(private readonly PDO $pdo) {}

    public function buscarPorVin(string $vin): ?Motocicleta
    {
        $statement = $this->pdo->prepare('SELECT * FROM motocicleta WHERE vin = ?');
        $statement->execute([$vin]);
        $row = $statement->fetch();

        return is_array($row) ? MotocicletaMapper::toDomain($row) : null;
    }

    public function guardar(Motocicleta $motocicleta): Motocicleta
    {
        $row = MotocicletaMapper::toRow($motocicleta);

        if ($this->existe($motocicleta->getVin())) {
            $assignments = implode(', ', array_map(
                static fn (string $column): string => "{$column} = :{$column}",
                array_keys($row)
            ));

            $statement = $this->pdo->prepare(
                "UPDATE motocicleta SET {$assignments} WHERE vin = :vin"
            );
        } else {
            $columns = implode(', ', array_keys($row));
            $placeholders = implode(', ', array_map(
                static fn (string $column): string => ":{$column}",
                array_keys($row)
            ));

            $statement = $this->pdo->prepare(
                "INSERT INTO motocicleta ({$columns}) VALUES ({$placeholders})"
            );
        }

        $statement->execute($row);

        return $this->buscarPorVin($motocicleta->getVin()) ?? $motocicleta;
    }

    private function existe(string $vin): bool
    {
        $statement = $this->pdo->prepare('SELECT 1 FROM motocicleta WHERE vin = ?');
        $statement->execute([$vin]);

        return $statement->fetchColumn() !== false;
    }
}
