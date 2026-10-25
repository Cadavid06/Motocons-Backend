<?php

namespace App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table = 'venta';

    protected $primaryKey = 'numero';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'numero',
        'fecha',
        'canal', // 'PRESENCIAL', 'WEB'
        'estado', // 'COTIZACION', 'EN_PROCESO_CREDITO', 'PAGADA', 'ENTREGADA', 'ANULADA'
        'aceptacion_terminos',
        'cliente_tipo_doc',
        'cliente_num_doc',
        'vendedor_tipo_doc',
        'vendedor_num_doc',
        'promocion_id',
        'cierre_tipo',
        'cierre_fecha_inicio',
        'liquidacion_id',
        'comision_porcentaje',
        'comision_valor',
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'aceptacion_terminos' => 'datetime',
        'comision_porcentaje' => 'decimal:2',
        'comision_valor' => 'decimal:2',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_tipo_doc', 'tipo_doc')
            ->where('num_doc', $this->cliente_num_doc);
    }

    public function vendedor()
    {
        return $this->belongsTo(Empleado::class, 'vendedor_tipo_doc', 'tipo_doc')
            ->where('num_doc', $this->vendedor_num_doc);
    }

    public function promocion()
    {
        return $this->belongsTo(Promocion::class, 'promocion_id', 'id');
    }

    public function liquidacion()
    {
        return $this->belongsTo(LiquidacionComision::class, 'liquidacion_id', 'id');
    }

    public function motocicletas()
    {
        return $this->hasMany(Motocicleta::class, 'venta_numero', 'numero');
    }

    public function requisitos()
    {
        return $this->hasMany(VentaRequisito::class, 'venta_numero', 'numero');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'venta_numero', 'numero');
    }

    public function documentosFiscales()
    {
        return $this->hasMany(DocumentoFiscal::class, 'venta_numero', 'numero');
    }
}
