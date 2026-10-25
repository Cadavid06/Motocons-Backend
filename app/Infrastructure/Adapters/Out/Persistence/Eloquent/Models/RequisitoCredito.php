<?php

namespace App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

class RequisitoCredito extends Model
{
    protected $table = 'requisito_credito';

    public $timestamps = false;

    protected $fillable = [
        'medio_pago', // 'BRILLA', 'SERVICREDITO'
        'descripcion',
        'obligatorio',
    ];

    protected $casts = [
        'obligatorio' => 'boolean',
    ];

    public function ventasRequisitos()
    {
        return $this->hasMany(VentaRequisito::class, 'requisito_id', 'id');
    }
}
