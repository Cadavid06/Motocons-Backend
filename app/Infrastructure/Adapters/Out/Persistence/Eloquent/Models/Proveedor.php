<?php

namespace App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedor';
    protected $primaryKey = 'nit';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'nit',
        'razon_social',
        'email_ordenes',
    ];

    public function ordenesCompra()
    {
        return $this->hasMany(OrdenCompra::class, 'proveedor_nit', 'nit');
    }
}
