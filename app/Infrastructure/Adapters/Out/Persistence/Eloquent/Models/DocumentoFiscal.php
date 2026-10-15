<?php

namespace App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use App\Shared\Traits\HasCompositePrimaryKey;

class DocumentoFiscal extends Model
{
    use HasCompositePrimaryKey;

    protected $table = 'documento_fiscal';
    protected $primaryKey = ['prefijo', 'consecutivo'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'prefijo',
        'consecutivo',
        'tipo', // 'FACTURA_VENTA', 'FACTURA_TALLER', 'NOTA_CREDITO', 'NOTA_DEBITO'
        'fecha_hora_emision',
        'cufe_cude',
        'xml_firmado',
        'qr',
        'estado_dian', // 'EMITIDA', 'ENVIADA', 'AUTORIZADA', 'RECHAZADA'
        'forma_pago', // 'CONTADO', 'CREDITO'
        'cliente_tipo_doc',
        'cliente_num_doc',
        'venta_numero',
        'orden_servicio_numero',
        'numero_resolucion',
        'corrige_prefijo',
        'corrige_consecutivo',
    ];

    protected $casts = [
        'consecutivo'         => 'integer',
        'corrige_consecutivo' => 'integer',
        'fecha_hora_emision'  => 'datetime',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_tipo_doc', 'tipo_doc')
                    ->where('num_doc', $this->cliente_num_doc);
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_numero', 'numero');
    }

    public function ordenServicio()
    {
        return $this->belongsTo(OrdenServicio::class, 'orden_servicio_numero', 'numero');
    }

    public function resolucionDian()
    {
        return $this->belongsTo(ResolucionDian::class, 'numero_resolucion', 'numero_resolucion');
    }

    public function facturaCorregida()
    {
        return $this->belongsTo(DocumentoFiscal::class, 'corrige_prefijo', 'prefijo')
                    ->where('consecutivo', $this->corrige_consecutivo);
    }

    public function notasCorreccion()
    {
        return $this->hasMany(DocumentoFiscal::class, 'corrige_prefijo', 'prefijo')
                    ->where('corrige_consecutivo', $this->consecutivo);
    }

    public function detalles()
    {
        return $this->hasMany(DocumentoFiscalDetalle::class, 'prefijo', 'prefijo')
                    ->where('consecutivo', $this->consecutivo);
    }
}
