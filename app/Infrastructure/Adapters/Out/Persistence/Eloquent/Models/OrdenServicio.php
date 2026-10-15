<?php

namespace App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenServicio extends Model
{
    protected $table = 'orden_servicio';
    protected $primaryKey = 'numero';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'numero',
        'fecha_ingreso',
        'kilometraje',
        'tipo', // 'GARANTIA', 'GENERAL', 'CORRECTIVO'
        'nro_mant_garantia', // 1, 2 o 3
        'diagnostico',
        'motocicleta_vin',
        'mecanico_tipo_doc',
        'mecanico_num_doc',
    ];

    protected $casts = [
        'fecha_ingreso'     => 'datetime',
        'kilometraje'       => 'integer',
        'nro_mant_garantia' => 'integer',
    ];

    public function motocicleta()
    {
        return $this->belongsTo(Motocicleta::class, 'motocicleta_vin', 'vin');
    }

    public function mecanico()
    {
        return $this->belongsTo(Empleado::class, 'mecanico_tipo_doc', 'tipo_doc')
                    ->where('num_doc', $this->mecanico_num_doc);
    }

    public function productosDetalles()
    {
        return $this->hasMany(OrdenServicioProducto::class, 'orden_servicio_numero', 'numero');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'orden_servicio_numero', 'numero');
    }

    public function documentosFiscales()
    {
        return $this->hasMany(DocumentoFiscal::class, 'orden_servicio_numero', 'numero');
    }

    /**
     * Regla de negocio HU-09: Determina si la mano de obra debe ser gratuita.
     */
    public function esGarantiaOficialGratuita(): bool
    {
        return $this->nro_mant_garantia !== null && $this->nro_mant_garantia <= 3;
    }
}
