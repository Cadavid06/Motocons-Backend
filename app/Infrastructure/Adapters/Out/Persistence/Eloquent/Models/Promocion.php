<?php

namespace App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Promocion extends Model
{
    protected $table = 'promocion';
    public $timestamps = false;

    protected $fillable = [
        'tipo', // 'PORCENTAJE', 'VALOR_FIJO'
        'valor',
        'fecha_inicio',
        'fecha_fin',
        'gama_objetivo',
        'propulsion_objetivo',
    ];

    protected $casts = [
        'valor'        => 'decimal:2',
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
    ];

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'promocion_id', 'id');
    }

    /**
     * Scope para promociones activas a la fecha actual.
     */
    public function scopeVigente(Builder $query): Builder
    {
        $hoy = now()->toDateString();
        return $query->where('fecha_inicio', '<=', $hoy)
                     ->where('fecha_fin', '>=', $hoy);
    }

    /**
     * Calcula el monto a descontar según el precio lista de la moto.
     */
    public function calcularDescuento(float $precioBase): float
    {
        if (strtoupper($this->tipo) === 'PORCENTAJE') {
            return round($precioBase * ($this->valor / 100), 2);
        }
        return min((float)$this->valor, $precioBase);
    }
}
