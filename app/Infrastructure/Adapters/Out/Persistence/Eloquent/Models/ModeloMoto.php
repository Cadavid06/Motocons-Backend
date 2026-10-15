<?php

namespace App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ModeloMoto extends Model
{
    protected $table = 'modelo_moto';
    protected $primaryKey = 'referencia';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'referencia',
        'marca',
        'linea',
        'anio',
        'gama', // 'baja', 'media', 'alta'
        'propulsion', // 'combustion', 'electrica'
        'cilindraje_cc',
        'ficha_tecnica',
    ];

    protected $casts = [
        'anio'          => 'integer',
        'cilindraje_cc' => 'integer',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'referencia', 'referencia');
    }

    public function motocicletas()
    {
        return $this->hasMany(Motocicleta::class, 'modelo_referencia', 'referencia');
    }

    /**
     * Scope para HU-01: Filtrar motos por gama, marca y propulsión combinables.
     */
    public function scopeFiltrar(Builder $query, array $filtros): Builder
    {
        if (!empty($filtros['marca'])) {
            $query->where('marca', 'ilike', '%' . $filtros['marca'] . '%');
        }

        if (!empty($filtros['gama'])) {
            $query->where('gama', strtolower($filtros['gama']));
        }

        if (!empty($filtros['propulsion'])) {
            $query->where('propulsion', strtolower($filtros['propulsion']));
        }

        return $query;
    }
}
