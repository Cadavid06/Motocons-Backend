<?php

namespace App\Infrastructure\Persistence\Pdo\Repositories;

use App\Application\Ports\Out\VentaRepositoryPort;
use App\Domain\Model\Venta;
use App\Infrastructure\Persistence\Pdo\Mappers\VentaMapper;
use PDO;

final class PdoVentaRepository implements VentaRepositoryPort
{
    public function __construct(private readonly PDO $pdo) {}

    public function buscarPorNumero(string $numero): ?Venta
    {
        $statement = $this->pdo->prepare('SELECT * FROM venta WHERE numero = ?');
        $statement->execute([$numero]);
        $row = $statement->fetch();

        return is_array($row) ? VentaMapper::toDomain($row) : null;
    }

    public function guardar(Venta $venta): Venta
    {
        $row = VentaMapper::toRow($venta);

        if ($this->existe($venta->getNumero())) {
            $assignments = implode(', ', array_map(
                static fn (string $column): string => "{$column} = :{$column}",
                array_keys($row)
            ));

            $statement = $this->pdo->prepare(
                "UPDATE venta SET {$assignments} WHERE numero = :numero"
            );
        } else {
            $columns = implode(', ', array_keys($row));
            $placeholders = implode(', ', array_map(
                static fn (string $column): string => ":{$column}",
                array_keys($row)
            ));

            $statement = $this->pdo->prepare(
                "INSERT INTO venta ({$columns}) VALUES ({$placeholders})"
            );
        }

        $statement->execute($row);

        return $this->buscarPorNumero($venta->getNumero()) ?? $venta;
    }

    public function generarSiguienteNumero(): string
    {
        $prefijo = 'VTA-' . date('Ymd');

        $statement = $this->pdo->prepare(
            'SELECT numero FROM venta WHERE numero LIKE ? ORDER BY numero DESC LIMIT 1'
        );
        $statement->execute([$prefijo . '-%']);
        $ultimo = $statement->fetchColumn();

        if (! is_string($ultimo) || $ultimo === '') {
            return "{$prefijo}-0001";
        }

        $separator = strrpos($ultimo, '-');
        $secuencia = $separator === false ? 0 : (int) substr($ultimo, $separator + 1);

        return sprintf('%s-%04d', $prefijo, $secuencia + 1);
    }

    private function existe(string $numero): bool
    {
        $statement = $this->pdo->prepare('SELECT 1 FROM venta WHERE numero = ?');
        $statement->execute([$numero]);

        return $statement->fetchColumn() !== false;
    }
}
