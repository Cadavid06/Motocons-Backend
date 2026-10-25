<?php

namespace App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models;

use App\Shared\Traits\HasCompositePrimaryKey;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasCompositePrimaryKey;

    protected $table = 'cliente';

    protected $primaryKey = ['tipo_doc', 'num_doc'];

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'tipo_doc',
        'num_doc',
        'nombre_razon_social',
        'email_facturacion',
        'telefono',
        'direccion',
        'municipio',
        'autorizacion_datos_fecha',
        'autorizacion_datos_version',
    ];

    protected $casts = [
        'autorizacion_datos_fecha' => 'datetime',
    ];

    public function usuario()
    {
        return $this->hasOne(Usuario::class, 'cliente_tipo_doc', 'tipo_doc')
            ->where('cliente_num_doc', $this->num_doc);
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'cliente_tipo_doc', 'tipo_doc')
            ->where('cliente_num_doc', $this->num_doc);
    }

    public function documentosFiscales()
    {
        return $this->hasMany(DocumentoFiscal::class, 'cliente_tipo_doc', 'tipo_doc')
            ->where('cliente_num_doc', $this->num_doc);
    }
}
