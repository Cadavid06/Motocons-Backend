<?php

namespace App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use App\Shared\Traits\HasCompositePrimaryKey;

class DocumentoFiscalDetalle extends Model
{
    use HasCompositePrimaryKey;

    protected $table = 'documento_fiscal_detalle';
    protected $primaryKey = ['prefijo', 'consecutivo', 'producto_referencia'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'prefijo',
        'consecutivo',
        'producto_referencia',
        'cantidad',
        'valor_unit',
        'descuento',
        'iva',
        'inc',
        'es_gratuita',
    ];

    protected $casts = [
        'consecutivo' => 'integer',
        'cantidad'    => 'decimal:2',
        'valor_unit'  => 'decimal:2',
        'descuento'   => 'decimal:2',
        'iva'         => 'decimal:2',
        'inc'         => 'decimal:2',
        'es_gratuita' => 'boolean',
    ];

    public function documentoFiscal()
    {
        return $this->belongsTo(DocumentoFiscal::class, 'prefijo', 'prefijo')
                    ->where('consecutivo', $this->consecutivo);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_referencia', 'referencia');
    }

    public function subtotalNeto(): float
    {
        if ($this->es_gratuita) {
            return 0.0;
        }
        return round(($this->cantidad * $this->valor_unit) - $this->descuento, 2);
    }
}
