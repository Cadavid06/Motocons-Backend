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
});
