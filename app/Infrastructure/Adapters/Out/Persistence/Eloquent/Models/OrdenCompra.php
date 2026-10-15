<?php

namespace App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenCompra extends Model
{
    protected $table = 'orden_compra';
    protected $primaryKey = 'numero';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'numero',
        'fecha',
        'estado',
        'proveedor_nit',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_nit', 'nit');
    }

    public function productosDetalles()
    {
        return $this->hasMany(OrdenCompraProducto::class, 'orden_compra_numero', 'numero');
    }

    public function recepciones()
    {
        return $this->hasMany(Recepcion::class, 'orden_compra_numero', 'numero');
    }
}
