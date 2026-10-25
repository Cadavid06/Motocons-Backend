<?php

use App\Infrastructure\Adapters\In\Http\Controllers\Web\CreditosWebController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [CreditosWebController::class, 'index'])->name('home');
Route::get('/creditos', [CreditosWebController::class, 'index'])->name('creditos.web.index');
