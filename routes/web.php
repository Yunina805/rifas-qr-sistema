<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RifaController;
use App\Http\Controllers\Admin\LoteController;

/*
|--------------------------------------------------------------------------
| Rutas Admin (sin auth todavía)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::view('/dashboard', 'admin.dashboard')->name('dashboard');

    // Rifas
    Route::get('/rifas', [RifaController::class, 'index'])->name('rifas');
    Route::post('/rifas', [RifaController::class, 'store'])->name('rifas.store');

    Route::get('/rifas/{rifa}/editar', [RifaController::class, 'edit'])
        ->name('rifas.edit');

    Route::put('/rifas/{rifa}', [RifaController::class, 'update'])
        ->name('rifas.update');

    // Estados de rifa
    Route::post('/rifas/{rifa}/activar', [RifaController::class, 'activar'])
        ->name('rifas.activar');

    Route::post('/rifas/{rifa}/finalizar', [RifaController::class, 'finalizar'])
        ->name('rifas.finalizar');

    // Lotes (SIEMPRE dentro de una rifa)
    Route::get('/rifas/{rifa}/lotes', [LoteController::class, 'index'])
        ->name('rifas.lotes');

    Route::post('/rifas/{rifa}/lotes', [LoteController::class, 'store'])
        ->name('rifas.lotes.store');

    // Otras secciones (aún vistas simples)
    Route::view('/boletos', 'admin.boletos')->name('boletos');
    Route::view('/premios', 'admin.premios')->name('premios');
    Route::view('/reportes', 'admin.reportes')->name('reportes');
});

/*
|--------------------------------------------------------------------------
| Ruta raíz
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});
