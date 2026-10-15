<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Módulo: Ventas y Motocicletas (Inventario individual por VIN)
     */
    public function up(): void
    {
        // 1. Tabla venta
        Schema::create('venta', function (Blueprint $table) {
            $table->string('numero')->primary();
            $table->timestamp('fecha')->useCurrent();
            $table->string('canal'); // 'PRESENCIAL', 'WEB'
            $table->string('estado'); // 'COTIZACION', 'EN_PROCESO_CREDITO', 'PAGADA', 'ENTREGADA', 'ANULADA'
            $table->timestamp('aceptacion_terminos')->nullable();

            // Cliente comprador
            $table->string('cliente_tipo_doc');
            $table->string('cliente_num_doc');

            // Vendedor asignado (en ventas presenciales)
            $table->string('vendedor_tipo_doc')->nullable();
            $table->string('vendedor_num_doc')->nullable();

            // Promoción aplicada (opcional)
            $table->unsignedBigInteger('promocion_id')->nullable();

            // Cierre financiero semanal
            $table->string('cierre_tipo')->nullable();
            $table->date('cierre_fecha_inicio')->nullable();

            // Liquidación de comisión del vendedor
            $table->unsignedBigInteger('liquidacion_id')->nullable();
            $table->decimal('comision_porcentaje', 5, 2)->nullable();
            $table->decimal('comision_valor', 14, 2)->nullable();

            $table->foreign(['cliente_tipo_doc', 'cliente_num_doc'])
                ->references(['tipo_doc', 'num_doc'])
                ->on('cliente')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign(['vendedor_tipo_doc', 'vendedor_num_doc'])
                ->references(['tipo_doc', 'num_doc'])
                ->on('empleado')
                ->onUpdate('cascade')
                ->onDelete('set null');

            $table->foreign('promocion_id')
                ->references('id')
                ->on('promocion')
                ->onUpdate('cascade')
                ->onDelete('set null');

            $table->foreign(['cierre_tipo', 'cierre_fecha_inicio'])
                ->references(['tipo', 'fecha_inicio'])
                ->on('cierre')
                ->onUpdate('cascade')
                ->onDelete('set null');

            $table->foreign('liquidacion_id')
                ->references('id')
                ->on('liquidacion_comision')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });

        // 2. Tabla motocicleta (Unidades físicas únicas)
        Schema::create('motocicleta', function (Blueprint $table) {
            $table->string('vin')->primary(); // Número de chasis único
            $table->string('numero_motor');
            $table->string('color');
            $table->string('placa')->nullable();
            $table->string('estado'); // 'DISPONIBLE', 'RESERVADA', 'VENDIDA', 'EN_MANTENIMIENTO'
            $table->date('garantia_hasta')->nullable();

            $table->string('modelo_referencia');
            $table->unsignedBigInteger('recepcion_id')->nullable();
            $table->string('venta_numero')->nullable();

            $table->decimal('precio_lista', 14, 2);
            $table->decimal('descuento', 14, 2)->default(0);

            $table->foreign('modelo_referencia')
                ->references('referencia')
                ->on('modelo_moto')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('recepcion_id')
                ->references('id')
                ->on('recepcion')
                ->onUpdate('cascade')
                ->onDelete('set null');

            $table->foreign('venta_numero')
                ->references('numero')
                ->on('venta')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });

        // 3. Tabla venta_requisito (Soportes como recibo de gas para Brilla, ServiCrédito)
        Schema::create('venta_requisito', function (Blueprint $table) {
            $table->string('venta_numero');
            $table->unsignedBigInteger('requisito_id');
            $table->string('estado')->default('PENDIENTE'); // 'PENDIENTE', 'VALIDADO', 'RECHAZADO'
            $table->string('soporte_adjunto')->nullable();
            $table->string('validado_por')->nullable(); // Username del vendedor o admin

            $table->primary(['venta_numero', 'requisito_id']);

            $table->foreign('venta_numero')
                ->references('numero')
                ->on('venta')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('requisito_id')
                ->references('id')
                ->on('requisito_credito')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venta_requisito');
        Schema::dropIfExists('motocicleta');
        Schema::dropIfExists('venta');
    }
};
