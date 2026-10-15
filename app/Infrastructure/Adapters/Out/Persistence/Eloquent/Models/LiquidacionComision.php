<?php

namespace App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

class LiquidacionComision extends Model
{
    protected $table = 'liquidacion_comision';
    public $timestamps = false;

    protected $fillable = [
        'periodo',
        'estado', // 'BORRADOR', 'APROBADA', 'PAGADA'
        'empleado_tipo_doc',
        'empleado_num_doc',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_tipo_doc', 'tipo_doc')
                    ->where('num_doc', $this->empleado_num_doc);
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'liquidacion_id', 'id');
    }
}
