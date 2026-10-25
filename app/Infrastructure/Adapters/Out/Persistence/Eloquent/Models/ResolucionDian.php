<?php

namespace App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

class ResolucionDian extends Model
{
    protected $table = 'resolucion_dian';

    protected $primaryKey = 'numero_resolucion';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'numero_resolucion',
        'prefijo',
        'rango_desde',
        'rango_hasta',
        'vigencia_desde',
        'vigencia_hasta',
    ];

    protected $casts = [
        'rango_desde' => 'integer',
        'rango_hasta' => 'integer',
        'vigencia_desde' => 'date',
        'vigencia_hasta' => 'date',
    ];

    public function documentosFiscales()
    {
        return $this->hasMany(DocumentoFiscal::class, 'numero_resolucion', 'numero_resolucion');
    }
}
