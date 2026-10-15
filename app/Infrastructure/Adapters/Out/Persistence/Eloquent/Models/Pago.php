<?php

namespace App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table = 'pago';
    public $timestamps = false;

    protected $fillable = [
        'medio', // 'CONTADO', 'TARJETA', 'SERVICREDITO', 'BRILLA'
        'valor',
        'fecha',
        'referencia_autorizacion',
        'estado', // 'APROBADO', 'PENDIENTE', 'RECHAZADO'
        'venta_numero',
        'orden_servicio_numero',
        'cierre_tipo',
        'cierre_fecha_inicio',
    ];

    protected $casts = [
        'valor'               => 'decimal:2',
        'fecha'               => 'datetime',
        'cierre_fecha_inicio' => 'date',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_numero', 'numero');
    }

    public function ordenServicio()
    {
        return $this->belongsTo(OrdenServicio::class, 'orden_servicio_numero', 'numero');
    }
}
