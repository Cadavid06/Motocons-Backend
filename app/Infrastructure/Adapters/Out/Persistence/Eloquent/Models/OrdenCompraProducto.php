<?php

namespace App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models;

use App\Shared\Traits\HasCompositePrimaryKey;
use Illuminate\Database\Eloquent\Model;

class OrdenCompraProducto extends Model
{
    use HasCompositePrimaryKey;

    protected $table = 'orden_compra_producto';

    protected $primaryKey = ['orden_compra_numero', 'producto_referencia'];

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'orden_compra_numero',
        'producto_referencia',
        'cantidad',
        'costo',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'costo' => 'decimal:2',
    ];

    public function ordenCompra()
    {
        return $this->belongsTo(OrdenCompra::class, 'orden_compra_numero', 'numero');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_referencia', 'referencia');
    }
}
