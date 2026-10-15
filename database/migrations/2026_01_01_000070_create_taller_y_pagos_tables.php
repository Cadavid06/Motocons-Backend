<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Módulo: Taller (Servicio Posventa y Garantías) y Gestión de Pagos
     */
    public function up(): void
    {
        // 1. Tabla orden_servicio (Mantenimientos y revisiones oficiales)
        Schema::create('orden_servicio', function (Blueprint $table) {
            $table->string('numero')->primary();
            $table->timestamp('fecha_ingreso')->useCurrent();
            $table->integer('kilometraje');
            $table->string('tipo'); // 'GARANTIA', 'GENERAL', 'CORRECTIVO'
            $table->smallInteger('nro_mant_garantia')->nullable(); // 1, 2 o 3 (Garantía oficial)
            $table->text('diagnostico')->nullable();

            $table->string('motocicleta_vin');
            $table->string('mecanico_tipo_doc')->nullable();
            $table->string('mecanico_num_doc')->nullable();

            $table->foreign('motocicleta_vin')
                ->references('vin')
                ->on('motocicleta')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign(['mecanico_tipo_doc', 'mecanico_num_doc'])
                ->references(['tipo_doc', 'num_doc'])
                ->on('empleado')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });

        // 2. Tabla orden_servicio_producto (Repuestos e insumos cargados a la orden)
        Schema::create('orden_servicio_producto', function (Blueprint $table) {
            $table->string('orden_servicio_numero');
            $table->string('producto_referencia');
            $table->decimal('cantidad', 10, 2);
            $table->decimal('valor_unit', 14, 2);

            $table->primary(['orden_servicio_numero', 'producto_referencia']);

            $table->foreign('orden_servicio_numero')
                ->references('numero')
                ->on('orden_servicio')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('producto_referencia')
                ->references('referencia')
                ->on('producto')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });

        // 3. Tabla pago (Transacciones de pago para ventas u órdenes de taller)
        Schema::create('pago', function (Blueprint $table) {
            $table->id();
            $table->string('medio'); // 'CONTADO', 'TARJETA', 'SERVICREDITO', 'BRILLA'
            $table->decimal('valor', 14, 2);
            $table->timestamp('fecha')->useCurrent();
            $table->string('referencia_autorizacion')->nullable();
            $table->string('estado'); // 'APROBADO', 'PENDIENTE', 'RECHAZADO'

            $table->string('venta_numero')->nullable();
            $table->string('orden_servicio_numero')->nullable();

            // Cierre financiero mensual de pagos
            $table->string('cierre_tipo')->nullable();
            $table->date('cierre_fecha_inicio')->nullable();

            $table->foreign('venta_numero')
                ->references('numero')
                ->on('venta')
                ->onUpdate('cascade')
                ->onDelete('set null');

            $table->foreign('orden_servicio_numero')
                ->references('numero')
                ->on('orden_servicio')
                ->onUpdate('cascade')
                ->onDelete('set null');

            $table->foreign(['cierre_tipo', 'cierre_fecha_inicio'])
                ->references(['tipo', 'fecha_inicio'])
                ->on('cierre')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pago');
        Schema::dropIfExists('orden_servicio_producto');
        Schema::dropIfExists('orden_servicio');
    }
};
