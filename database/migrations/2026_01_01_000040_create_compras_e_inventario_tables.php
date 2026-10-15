<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Módulo: Control de Compras e Inventario
     */
    public function up(): void
    {
        // 1. Tabla orden_compra
        Schema::create('orden_compra', function (Blueprint $table) {
            $table->string('numero')->primary();
            $table->date('fecha');
            $table->string('estado'); // 'PENDIENTE', 'RECIBIDA', 'CANCELADA', etc.
            $table->string('proveedor_nit');

            $table->foreign('proveedor_nit')
                ->references('nit')
                ->on('proveedor')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });

        // 2. Tabla orden_compra_producto (Detalle de orden de compra)
        Schema::create('orden_compra_producto', function (Blueprint $table) {
            $table->string('orden_compra_numero');
            $table->string('producto_referencia');
            $table->integer('cantidad');
            $table->decimal('costo', 14, 2);

            $table->primary(['orden_compra_numero', 'producto_referencia']);

            $table->foreign('orden_compra_numero')
                ->references('numero')
                ->on('orden_compra')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('producto_referencia')
                ->references('referencia')
                ->on('producto')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });

        // 3. Tabla recepcion
        Schema::create('recepcion', function (Blueprint $table) {
            $table->id(); // bigint autonumérico
            $table->date('fecha');
            $table->string('cufe_factura_proveedor')->nullable();
            $table->string('orden_compra_numero');

            $table->foreign('orden_compra_numero')
                ->references('numero')
                ->on('orden_compra')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });

        // 4. Tabla recepcion_producto (Detalle de recepción física)
        Schema::create('recepcion_producto', function (Blueprint $table) {
            $table->unsignedBigInteger('recepcion_id');
            $table->string('producto_referencia');
            $table->integer('cantidad_recibida');

            $table->primary(['recepcion_id', 'producto_referencia']);

            $table->foreign('recepcion_id')
                ->references('id')
                ->on('recepcion')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('producto_referencia')
                ->references('referencia')
                ->on('producto')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recepcion_producto');
        Schema::dropIfExists('recepcion');
        Schema::dropIfExists('orden_compra_producto');
        Schema::dropIfExists('orden_compra');
    }
};
