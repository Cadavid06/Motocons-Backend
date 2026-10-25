<?php

namespace App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models;

use App\Shared\Traits\HasCompositePrimaryKey;
use Illuminate\Database\Eloquent\Model;

class OrdenServicioProducto extends Model
{
    use HasCompositePrimaryKey;

    protected $table = 'orden_servicio_producto';

    protected $primaryKey = ['orden_servicio_numero', 'producto_referencia'];

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'orden_servicio_numero',
        'producto_referencia',
        'cantidad',
        'valor_unit',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'valor_unit' => 'decimal:2',
    ];

    public function ordenServicio()
    {
        return $this->belongsTo(OrdenServicio::class, 'orden_servicio_numero', 'numero');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_referencia', 'referencia');
    }
}
