<?php

namespace App\Infrastructure\Persistence\Pdo\ReadModels;

use PDO;

final class CreditosReadRepository
{
    public function __construct(private readonly PDO $pdo) {}

    /**
     * @return array<int, array<string, mixed>>
     */
    public function clientes(): array
    {
        return $this->fetchAll('SELECT * FROM cliente ORDER BY nombre_razon_social');
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function empleados(): array
    {
        return $this->fetchAll('SELECT * FROM empleado ORDER BY nombre');
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function motocicletasDisponibles(): array
    {
        $sql = 'SELECT m.vin, m.numero_motor, m.color, m.placa, m.estado, m.garantia_hasta,
                       m.modelo_referencia, m.recepcion_id, m.venta_numero, m.precio_lista, m.descuento,
                       mm.marca, mm.linea, mm.anio, mm.gama, mm.propulsion, mm.cilindraje_cc
                FROM motocicleta m
                LEFT JOIN modelo_moto mm ON mm.referencia = m.modelo_referencia
                WHERE m.estado = ?
                ORDER BY m.modelo_referencia';

        $statement = $this->pdo->prepare($sql);
        $statement->execute(['DISPONIBLE']);

        $rows = $statement->fetchAll();

        return array_map(function (array $row): array {
            $row['precio_lista'] = (float) $row['precio_lista'];
            $row['descuento'] = (float) $row['descuento'];

            return $row;
        }, $rows);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function requisitosCatalogo(): array
    {
        $rows = $this->fetchAll('SELECT * FROM requisito_credito ORDER BY medio_pago, id');

        return array_map(static function (array $row): array {
            $row['id'] = (int) $row['id'];
            $row['obligatorio'] = self::toBool($row['obligatorio']);

            return $row;
        }, $rows);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function solicitudes(): array
    {
        $statement = $this->pdo->prepare(
            "SELECT * FROM venta
             WHERE estado IN ('COTIZACION', 'EN_PROCESO_CREDITO', 'PAGADA', 'ENTREGADA')
             ORDER BY fecha DESC"
        );
        $statement->execute();
        $ventas = $statement->fetchAll();

        return array_map(fn (array $venta): array => $this->hydrateVenta($venta), $ventas);
    }

    /**
     * @param  array<string, mixed>  $venta
     * @return array<string, mixed>
     */
    private function hydrateVenta(array $venta): array
    {
        $clienteStatement = $this->pdo->prepare(
            'SELECT * FROM cliente WHERE tipo_doc = ? AND num_doc = ?'
        );
        $clienteStatement->execute([$venta['cliente_tipo_doc'], $venta['cliente_num_doc']]);
        $cliente = $clienteStatement->fetch();

        $motocicletasStatement = $this->pdo->prepare(
            'SELECT m.*, mm.marca, mm.linea, mm.anio, mm.gama, mm.propulsion, mm.cilindraje_cc, mm.ficha_tecnica
             FROM motocicleta m
             LEFT JOIN modelo_moto mm ON mm.referencia = m.modelo_referencia
             WHERE m.venta_numero = ?
             ORDER BY m.vin'
        );
        $motocicletasStatement->execute([$venta['numero']]);
        $motocicletas = array_map(static function (array $row): array {
            $modelo = [
                'referencia' => $row['modelo_referencia'],
                'marca' => $row['marca'],
                'linea' => $row['linea'],
                'anio' => $row['anio'] !== null ? (int) $row['anio'] : null,
                'gama' => $row['gama'],
                'propulsion' => $row['propulsion'],
                'cilindraje_cc' => $row['cilindraje_cc'] !== null ? (int) $row['cilindraje_cc'] : null,
                'ficha_tecnica' => $row['ficha_tecnica'],
            ];

            return [
                'vin' => $row['vin'],
                'numero_motor' => $row['numero_motor'],
                'color' => $row['color'],
                'placa' => $row['placa'],
                'estado' => $row['estado'],
                'garantia_hasta' => $row['garantia_hasta'],
                'modelo_referencia' => $row['modelo_referencia'],
                'recepcion_id' => $row['recepcion_id'] !== null ? (int) $row['recepcion_id'] : null,
                'venta_numero' => $row['venta_numero'],
                'precio_lista' => (float) $row['precio_lista'],
                'descuento' => (float) $row['descuento'],
                'modelo_moto' => $modelo,
            ];
        }, $motocicletasStatement->fetchAll());

        $requisitosStatement = $this->pdo->prepare(
            'SELECT vr.*, rc.medio_pago, rc.descripcion, rc.obligatorio
             FROM venta_requisito vr
             LEFT JOIN requisito_credito rc ON rc.id = vr.requisito_id
             WHERE vr.venta_numero = ?
             ORDER BY vr.requisito_id'
        );
        $requisitosStatement->execute([$venta['numero']]);
        $requisitos = array_map(static function (array $row): array {
            return [
                'venta_numero' => $row['venta_numero'],
                'requisito_id' => (int) $row['requisito_id'],
                'estado' => $row['estado'],
                'soporte_adjunto' => $row['soporte_adjunto'],
                'validado_por' => $row['validado_por'],
                'requisito_credito' => [
                    'id' => (int) $row['requisito_id'],
                    'medio_pago' => $row['medio_pago'],
                    'descripcion' => $row['descripcion'],
                    'obligatorio' => self::toBool($row['obligatorio'] ?? false),
                ],
            ];
        }, $requisitosStatement->fetchAll());

        return [
            'numero' => $venta['numero'],
            'fecha' => $venta['fecha'],
            'canal' => $venta['canal'],
            'estado' => $venta['estado'],
            'aceptacion_terminos' => $venta['aceptacion_terminos'],
            'cliente_tipo_doc' => $venta['cliente_tipo_doc'],
            'cliente_num_doc' => $venta['cliente_num_doc'],
            'vendedor_tipo_doc' => $venta['vendedor_tipo_doc'],
            'vendedor_num_doc' => $venta['vendedor_num_doc'],
            'cliente' => is_array($cliente) ? $cliente : null,
            'motocicletas' => $motocicletas,
            'requisitos' => $requisitos,
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function fetchAll(string $sql): array
    {
        $statement = $this->pdo->query($sql);
        $rows = $statement->fetchAll();

        return is_array($rows) ? $rows : [];
    }

    private static function toBool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_int($value)) {
            return $value === 1;
        }

        return in_array(strtolower((string) $value), ['1', 't', 'true', 'y', 'yes'], true);
    }
}
