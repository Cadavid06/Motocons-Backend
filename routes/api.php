<?php

use App\Infrastructure\Adapters\In\Http\Controllers\RequisitoCreditoController;
use App\Infrastructure\Adapters\In\Http\Controllers\SolicitudCreditoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('creditos')->group(function () {
    // Catálogo de Requisitos
    Route::get('/requisitos', [RequisitoCreditoController::class, 'index'])->name('creditos.requisitos.index');

    // Solicitudes de Crédito
    Route::post('/solicitudes', [SolicitudCreditoController::class, 'store'])->name('creditos.solicitudes.store');
    Route::post('/solicitudes/{ventaNumero}/requisitos/{requisitoId}/soporte', [SolicitudCreditoController::class, 'adjuntarSoporte'])
        ->name('creditos.solicitudes.requisitos.soporte');
    Route::patch('/solicitudes/{ventaNumero}/requisitos/{requisitoId}/evaluar', [SolicitudCreditoController::class, 'evaluarRequisito'])
        ->name('creditos.solicitudes.requisitos.evaluar');
    Route::post('/solicitudes/{ventaNumero}/aprobar', [SolicitudCreditoController::class, 'aprobar'])
        ->name('creditos.solicitudes.aprobar');
});
