<?php

namespace App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

class Recepcion extends Model
{
    protected $table = 'recepcion';

    public $timestamps = false;

    protected $fillable = [
        'fecha',
        'cufe_factura_proveedor',
        'orden_compra_numero',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function ordenCompra()
    {
        return $this->belongsTo(OrdenCompra::class, 'orden_compra_numero', 'numero');
    }

    public function productosDetalles()
    {
        return $this->hasMany(RecepcionProducto::class, 'recepcion_id', 'id');
    }

    public function motocicletas()
    {
        return $this->hasMany(Motocicleta::class, 'recepcion_id', 'id');
    }
}
