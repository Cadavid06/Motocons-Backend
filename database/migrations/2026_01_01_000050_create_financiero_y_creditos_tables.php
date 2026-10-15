<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Módulo: Financiero, Cierres y Requisitos de Crédito
     */
    public function up(): void
    {
        // 1. Tabla requisito_credito (Brilla, ServiCrédito, etc.)
        Schema::create('requisito_credito', function (Blueprint $table) {
            $table->id();
            $table->string('medio_pago'); // 'BRILLA', 'SERVICREDITO', etc.
            $table->string('descripcion');
            $table->boolean('obligatorio')->default(true);
        });

        // 2. Tabla resolucion_dian
        Schema::create('resolucion_dian', function (Blueprint $table) {
            $table->string('numero_resolucion')->primary();
            $table->string('prefijo')->nullable();
            $table->unsignedBigInteger('rango_desde');
            $table->unsignedBigInteger('rango_hasta');
            $table->date('vigencia_desde');
            $table->date('vigencia_hasta');
        });

        // 3. Tabla cierre (Cierres semanales de ventas y mensuales de pagos)
        Schema::create('cierre', function (Blueprint $table) {
            $table->string('tipo'); // 'SEMANAL_VENTAS', 'MENSUAL_PAGOS'
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->timestamp('fecha_cierre');

            $table->primary(['tipo', 'fecha_inicio']);
        });

        // 4. Tabla liquidacion_comision (Comisiones de vendedores por periodo)
        Schema::create('liquidacion_comision', function (Blueprint $table) {
            $table->id();
            $table->string('periodo'); // Ej: '2026-W41' o '2026-10'
            $table->string('estado'); // 'BORRADOR', 'APROBADA', 'PAGADA'
            $table->string('empleado_tipo_doc');
            $table->string('empleado_num_doc');

            $table->foreign(['empleado_tipo_doc', 'empleado_num_doc'])
                ->references(['tipo_doc', 'num_doc'])
                ->on('empleado')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('liquidacion_comision');
        Schema::dropIfExists('cierre');
        Schema::dropIfExists('resolucion_dian');
        Schema::dropIfExists('requisito_credito');
    }
};
