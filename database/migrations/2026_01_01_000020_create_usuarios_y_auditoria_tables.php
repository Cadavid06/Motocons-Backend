<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Módulo: Usuarios y Auditorías
     */
    public function up(): void
    {
        // 1. Tabla usuario
        Schema::create('usuario', function (Blueprint $table) {
            $table->string('username')->primary();
            $table->string('password_hash');
            $table->string('rol'); // 'Cliente', 'Vendedor', 'Administrador', 'Mecanico'
            $table->boolean('activo')->default(true);

            // Relación con cliente (opcional si es usuario empleado)
            $table->string('cliente_tipo_doc')->nullable();
            $table->string('cliente_num_doc')->nullable();

            // Relación con empleado (opcional si es usuario cliente)
            $table->string('empleado_tipo_doc')->nullable();
            $table->string('empleado_num_doc')->nullable();

            $table->unique(['cliente_tipo_doc', 'cliente_num_doc']);
            $table->unique(['empleado_tipo_doc', 'empleado_num_doc']);

            $table->foreign(['cliente_tipo_doc', 'cliente_num_doc'])
                ->references(['tipo_doc', 'num_doc'])
                ->on('cliente')
                ->onUpdate('cascade')
                ->onDelete('set null');

            $table->foreign(['empleado_tipo_doc', 'empleado_num_doc'])
                ->references(['tipo_doc', 'num_doc'])
                ->on('empleado')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });

        // 2. Tabla auditoria
        Schema::create('auditoria', function (Blueprint $table) {
            $table->id(); // bigint autonumérico
            $table->timestamp('fecha_hora')->useCurrent();
            $table->string('accion'); // 'INSERT', 'UPDATE', 'DELETE', 'LOGIN', etc.
            $table->string('entidad'); // 'cliente', 'usuario', 'producto', etc.
            $table->string('registro_id');
            $table->text('datos_antes')->nullable();
            $table->text('datos_despues')->nullable();
            $table->string('ip_origen')->nullable();
            $table->string('username')->nullable();

            $table->foreign('username')
                ->references('username')
                ->on('usuario')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auditoria');
        Schema::dropIfExists('usuario');
    }
};
