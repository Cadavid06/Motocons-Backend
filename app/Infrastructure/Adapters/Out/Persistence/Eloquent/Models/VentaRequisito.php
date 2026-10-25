<?php

namespace App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models;

use App\Shared\Traits\HasCompositePrimaryKey;
use Illuminate\Database\Eloquent\Model;

class VentaRequisito extends Model
{
    use HasCompositePrimaryKey;

    protected $table = 'venta_requisito';

    protected $primaryKey = ['venta_numero', 'requisito_id'];

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'venta_numero',
        'requisito_id',
        'estado', // 'PENDIENTE', 'VALIDADO', 'RECHAZADO'
        'soporte_adjunto',
        'validado_por',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_numero', 'numero');
    }

    public function requisitoCredito()
    {
        return $this->belongsTo(RequisitoCredito::class, 'requisito_id', 'id');
    }
}
