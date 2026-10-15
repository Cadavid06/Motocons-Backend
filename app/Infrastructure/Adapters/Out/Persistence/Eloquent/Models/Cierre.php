<?php

namespace App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use App\Shared\Traits\HasCompositePrimaryKey;

class Cierre extends Model
{
    use HasCompositePrimaryKey;

    protected $table = 'cierre';
    protected $primaryKey = ['tipo', 'fecha_inicio'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'tipo', // 'SEMANAL_VENTAS', 'MENSUAL_PAGOS'
        'fecha_inicio',
        'fecha_fin',
        'fecha_cierre',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
        'fecha_cierre' => 'datetime',
    ];

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'cierre_tipo', 'tipo')
                    ->where('cierre_fecha_inicio', $this->fecha_inicio);
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'cierre_tipo', 'tipo')
                    ->where('cierre_fecha_inicio', $this->fecha_inicio);
    }
}
