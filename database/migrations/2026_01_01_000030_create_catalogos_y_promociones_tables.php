<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Módulo: Catálogos y Promociones
     */
    public function up(): void
    {
        // 1. Tabla producto (Entidad general de inventario: motos, repuestos, insumos, servicios)
        Schema::create('producto', function (Blueprint $table) {
            $table->string('referencia')->primary();
            $table->string('nombre');
            $table->string('tipo'); // 'MOTO', 'REPUESTO', 'INSUMO', 'SERVICIO'
            $table->decimal('precio_vigente', 14, 2);
            $table->decimal('tarifa_iva', 5, 2)->default(0);
            $table->decimal('tarifa_inc', 5, 2)->default(0); // Impuesto al consumo
        });

        // 2. Tabla modelo_moto (Especialización de producto para motocicletas)
        Schema::create('modelo_moto', function (Blueprint $table) {
            $table->string('referencia')->primary();
            $table->string('marca');
            $table->string('linea');
            $table->integer('anio');
            $table->string('gama'); // 'baja', 'media', 'alta'
            $table->string('propulsion'); // 'combustion', 'electrica'
            $table->integer('cilindraje_cc')->nullable();
            $table->text('ficha_tecnica')->nullable();

            $table->foreign('referencia')
                ->references('referencia')
                ->on('producto')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        // 3. Tabla promocion (Descuentos y promociones periódicas)
        Schema::create('promocion', function (Blueprint $table) {
            $table->id();
            $table->string('tipo'); // 'PORCENTAJE', 'VALOR_FIJO', etc.
            $table->decimal('valor', 14, 2);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->string('gama_objetivo')->nullable(); // Opcional: aplica a gama específica
            $table->string('propulsion_objetivo')->nullable(); // Opcional: combustión o eléctrica
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modelo_moto');
        Schema::dropIfExists('producto');
        Schema::dropIfExists('promocion');
    }
};
