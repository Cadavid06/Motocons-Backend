<?php

namespace App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuario';
    protected $primaryKey = 'username';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'username',
        'password_hash',
        'rol',
        'activo',
        'cliente_tipo_doc',
        'cliente_num_doc',
        'empleado_tipo_doc',
        'empleado_num_doc',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * Sobrescribir para que Laravel Auth use password_hash en vez de password.
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_tipo_doc', 'tipo_doc')
                    ->where('num_doc', $this->cliente_num_doc);
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_tipo_doc', 'tipo_doc')
                    ->where('num_doc', $this->empleado_num_doc);
    }

    public function auditorias()
    {
        return $this->hasMany(Auditoria::class, 'username', 'username');
    }

    // Reglas de rol
    public function esAdmin(): bool
    {
        return strtolower($this->rol) === 'administrador';
    }

    public function esVendedor(): bool
    {
        return strtolower($this->rol) === 'vendedor';
    }

    public function esMecanico(): bool
    {
        return strtolower($this->rol) === 'mecanico';
    }

    public function esCliente(): bool
    {
        return strtolower($this->rol) === 'cliente';
    }
}
