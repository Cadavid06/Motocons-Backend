<?php

namespace App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use App\Shared\Traits\HasCompositePrimaryKey;

class Empleado extends Model
{
    use HasCompositePrimaryKey;

    protected $table = 'empleado';
    protected $primaryKey = ['tipo_doc', 'num_doc'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'tipo_doc',
        'num_doc',
        'nombre',
        'tipo_vinculacion',
    ];

    public function usuario()
    {
        return $this->hasOne(Usuario::class, 'empleado_tipo_doc', 'tipo_doc')
                    ->where('empleado_num_doc', $this->num_doc);
    }

    public function ventasComoVendedor()
    {
        return $this->hasMany(Venta::class, 'vendedor_tipo_doc', 'tipo_doc')
                    ->where('vendedor_num_doc', $this->num_doc);
    }

    public function ordenesComoMecanico()
    {
        return $this->hasMany(OrdenServicio::class, 'mecanico_tipo_doc', 'tipo_doc')
                    ->where('mecanico_num_doc', $this->num_doc);
    }

    public function liquidacionesComision()
    {
        return $this->hasMany(LiquidacionComision::class, 'empleado_tipo_doc', 'tipo_doc')
                    ->where('empleado_num_doc', $this->num_doc);
    }
}
