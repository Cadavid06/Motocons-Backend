<?php

namespace App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

class Motocicleta extends Model
{
    protected $table = 'motocicleta';

    protected $primaryKey = 'vin';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'vin',
        'numero_motor',
        'color',
        'placa',
        'estado', // 'DISPONIBLE', 'RESERVADA', 'VENDIDA', 'EN_MANTENIMIENTO'
        'garantia_hasta',
        'modelo_referencia',
        'recepcion_id',
        'venta_numero',
        'precio_lista',
        'descuento',
    ];

    protected $casts = [
        'garantia_hasta' => 'date',
        'precio_lista' => 'decimal:2',
        'descuento' => 'decimal:2',
    ];

    public function modeloMoto()
    {
        return $this->belongsTo(ModeloMoto::class, 'modelo_referencia', 'referencia');
    }

    public function recepcion()
    {
        return $this->belongsTo(Recepcion::class, 'recepcion_id', 'id');
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_numero', 'numero');
    }

    public function ordenesServicio()
    {
        return $this->hasMany(OrdenServicio::class, 'motocicleta_vin', 'vin');
    }

    /**
     * Regla de negocio HU-08 / HU-10: Cuenta cuántas revisiones oficiales de garantía se han usado.
     */
    public function mantenimientosGarantiaUsados(): int
    {
        return $this->ordenesServicio()
            ->whereNotNull('nro_mant_garantia')
            ->count();
    }

    /**
     * Valida si todavía puede acceder a revisiones gratuitas de garantía (máximo 3).
     */
    public function tieneGarantiaDisponible(): bool
    {
        $vigentePorFecha = $this->garantia_hasta ? now()->toDateString() <= $this->garantia_hasta : true;

        return $vigentePorFecha && ($this->mantenimientosGarantiaUsados() < 3);
    }
}
