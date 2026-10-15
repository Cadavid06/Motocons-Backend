<?php

namespace App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'producto';
    protected $primaryKey = 'referencia';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'referencia',
        'nombre',
        'tipo', // 'MOTO', 'REPUESTO', 'INSUMO', 'SERVICIO'
        'precio_vigente',
        'tarifa_iva',
        'tarifa_inc',
    ];

    protected $casts = [
        'precio_vigente' => 'decimal:2',
        'tarifa_iva'     => 'decimal:2',
        'tarifa_inc'     => 'decimal:2',
    ];

    public function modeloMoto()
    {
        return $this->hasOne(ModeloMoto::class, 'referencia', 'referencia');
    }

    public function ordenesCompraDetalles()
    {
        return $this->hasMany(OrdenCompraProducto::class, 'producto_referencia', 'referencia');
    }

    public function recepcionesDetalles()
    {
        return $this->hasMany(RecepcionProducto::class, 'producto_referencia', 'referencia');
    }

    public function ordenesServicioDetalles()
    {
        return $this->hasMany(OrdenServicioProducto::class, 'producto_referencia', 'referencia');
    }

    public function documentosFiscalesDetalles()
    {
        return $this->hasMany(DocumentoFiscalDetalle::class, 'producto_referencia', 'referencia');
    }

    public function esMoto(): bool
    {
        return strtoupper($this->tipo) === 'MOTO';
    }
}
