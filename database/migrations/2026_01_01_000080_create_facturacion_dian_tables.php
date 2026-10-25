<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Módulo: Facturación Electrónica y Documentos Fiscales DIAN
     */
    public function up(): void
    {
        // 1. Tabla documento_fiscal (Facturas electrónicas y notas crédito/débito)
        Schema::create('documento_fiscal', function (Blueprint $table) {
            $table->string('prefijo');
            $table->unsignedBigInteger('consecutivo');
            $table->string('tipo'); // 'FACTURA_VENTA', 'FACTURA_TALLER', 'NOTA_CREDITO', 'NOTA_DEBITO'
            $table->timestamp('fecha_hora_emision')->useCurrent();
            $table->string('cufe_cude')->nullable()->unique();
            $table->text('xml_firmado')->nullable();
            $table->text('qr')->nullable();
            $table->string('estado_dian'); // 'EMITIDA', 'ENVIADA', 'AUTORIZADA', 'RECHAZADA'
            $table->string('forma_pago'); // 'CONTADO', 'CREDITO'

            // Cliente receptor de la factura
            $table->string('cliente_tipo_doc');
            $table->string('cliente_num_doc');

            // Origen de la factura (o viene de una venta o de una orden de taller)
            $table->string('venta_numero')->nullable();
            $table->string('orden_servicio_numero')->nullable();

            // Resolución DIAN vigente
            $table->string('numero_resolucion')->nullable();

            // Referencia si es una Nota que corrige una factura previa
            $table->string('corrige_prefijo')->nullable();
            $table->unsignedBigInteger('corrige_consecutivo')->nullable();

            $table->primary(['prefijo', 'consecutivo']);

            $table->foreign(['cliente_tipo_doc', 'cliente_num_doc'])
                ->references(['tipo_doc', 'num_doc'])
                ->on('cliente')
                ->onUpdate('cascade')
                ->onDelete('restrict');

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

            $table->foreign('numero_resolucion')
                ->references('numero_resolucion')
                ->on('resolucion_dian')
                ->onUpdate('cascade')
                ->onDelete('set null');

            $table->foreign(['corrige_prefijo', 'corrige_consecutivo'])
                ->references(['prefijo', 'consecutivo'])
                ->on('documento_fiscal')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });

        // 2. Tabla documento_fiscal_detalle (Ítems facturados)
        Schema::create('documento_fiscal_detalle', function (Blueprint $table) {
            $table->string('prefijo');
            $table->unsignedBigInteger('consecutivo');
            $table->string('producto_referencia');

            $table->decimal('cantidad', 10, 2);
            $table->decimal('valor_unit', 14, 2);
            $table->decimal('descuento', 14, 2)->default(0);
            $table->decimal('iva', 14, 2)->default(0);
            $table->decimal('inc', 14, 2)->default(0); // Impuesto al consumo
            $table->boolean('es_gratuita')->default(false); // Ej: Mano de obra de garantía en taller

            $table->primary(['prefijo', 'consecutivo', 'producto_referencia']);

            $table->foreign(['prefijo', 'consecutivo'])
                ->references(['prefijo', 'consecutivo'])
                ->on('documento_fiscal')
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
        Schema::dropIfExists('documento_fiscal_detalle');
        Schema::dropIfExists('documento_fiscal');
    }
};
