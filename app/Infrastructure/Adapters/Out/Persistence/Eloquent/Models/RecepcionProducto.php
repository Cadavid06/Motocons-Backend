<?php

namespace App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use App\Shared\Traits\HasCompositePrimaryKey;

class RecepcionProducto extends Model
{
    use HasCompositePrimaryKey;

    protected $table = 'recepcion_producto';
    protected $primaryKey = ['recepcion_id', 'producto_referencia'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'recepcion_id',
        'producto_referencia',
        'cantidad_recibida',
    ];

    protected $casts = [
        'recepcion_id'      => 'integer',
        'cantidad_recibida' => 'integer',
    ];

    public function recepcion()
    {
        return $this->belongsTo(Recepcion::class, 'recepcion_id', 'id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_referencia', 'referencia');
    }
}
