<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Módulo: Terceros (Cliente, Empleado, Proveedor)
     */
    public function up(): void
    {
        // 1. Tabla cliente
        Schema::create('cliente', function (Blueprint $table) {
            $table->string('tipo_doc');
            $table->string('num_doc');
            $table->string('nombre_razon_social');
            $table->string('email_facturacion')->nullable();
            $table->string('telefono')->nullable();
            $table->string('direccion')->nullable();
            $table->string('municipio')->nullable();
            $table->timestamp('autorizacion_datos_fecha')->nullable();
            $table->string('autorizacion_datos_version')->nullable();

            $table->primary(['tipo_doc', 'num_doc']);
        });

        // 2. Tabla empleado
        Schema::create('empleado', function (Blueprint $table) {
            $table->string('tipo_doc');
            $table->string('num_doc');
            $table->string('nombre');
            $table->string('tipo_vinculacion')->nullable();

            $table->primary(['tipo_doc', 'num_doc']);
        });

        // 3. Tabla proveedor
        Schema::create('proveedor', function (Blueprint $table) {
            $table->string('nit')->primary();
            $table->string('razon_social');
            $table->string('email_ordenes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proveedor');
        Schema::dropIfExists('empleado');
        Schema::dropIfExists('cliente');
    }
};
