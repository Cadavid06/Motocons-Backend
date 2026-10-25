<?php

namespace App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

class Auditoria extends Model
{
    protected $table = 'auditoria';

    public $timestamps = false;

    protected $fillable = [
        'fecha_hora',
        'accion',
        'entidad',
        'registro_id',
        'datos_antes',
        'datos_despues',
        'ip_origen',
        'username',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'username', 'username');
    }

    /**
     * Helper para registrar auditoría de forma atómica.
     */
    public static function registrar(
        string $accion,
        string $entidad,
        string $registroId,
        $datosAntes = null,
        $datosDespues = null,
        ?string $ip = null,
        ?string $username = null
    ): self {
        return self::create([
            'fecha_hora' => now(),
            'accion' => strtoupper($accion),
            'entidad' => strtolower($entidad),
            'registro_id' => $registroId,
            'datos_antes' => is_array($datosAntes) ? json_encode($datosAntes) : $datosAntes,
            'datos_despues' => is_array($datosDespues) ? json_encode($datosDespues) : $datosDespues,
            'ip_origen' => $ip ?? request()->ip(),
            'username' => $username ?? auth()->user()?->username,
        ]);
    }
}
