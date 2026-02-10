<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RifaController;
use App\Http\Controllers\Admin\LoteController;
use App\Http\Controllers\PublicRifaController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\VendedorController; // <--- AGREGADO

/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS
|--------------------------------------------------------------------------
*/

// Redirección de la raíz al admin
Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

// Verificación de Boletos (QR)
Route::get('/verificar/{codigo}', [PublicRifaController::class, 'verificar'])
    ->name('boleto.verificar');


/*
|--------------------------------------------------------------------------
| RUTAS ADMINISTRATIVAS
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {

    // ==========================================
    // DASHBOARD
    // ==========================================
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ==========================================
    // GESTIÓN DE RIFAS
    // ==========================================
    Route::get('/rifas', [RifaController::class, 'index'])->name('rifas');
    Route::post('/rifas', [RifaController::class, 'store'])->name('rifas.store');

    Route::get('/rifas/{rifa}/editar', [RifaController::class, 'edit'])->name('rifas.edit');
    Route::put('/rifas/{rifa}', [RifaController::class, 'update'])->name('rifas.update');

    // Estados de rifa
    Route::post('/rifas/{rifa}/activar', [RifaController::class, 'activar'])->name('rifas.activar');
    Route::post('/rifas/{rifa}/finalizar', [RifaController::class, 'finalizar'])->name('rifas.finalizar');

    // ==========================================
    // GESTIÓN DE LOTES (BOLETOS)
    // ==========================================
    Route::get('/rifas/{rifa}/lotes', [LoteController::class, 'index'])->name('rifas.lotes');
    Route::post('/rifas/{rifa}/lotes', [LoteController::class, 'store'])->name('rifas.lotes.store');

    // Imprimir PDF
    Route::get('/rifas/{rifa}/imprimir', [LoteController::class, 'imprimir'])->name('rifas.imprimir');

    // Venta Manual
    Route::post('/boletos/{boleto}/vender', [LoteController::class, 'vender'])->name('boletos.vender');
    Route::post('/boletos/{boleto}/liberar', [LoteController::class, 'liberar'])->name('boletos.liberar');

    // ==========================================
    // ESCÁNER ADMINISTRATIVO
    // ==========================================
    Route::get('/escaner', [RifaController::class, 'scanView'])->name('escaner.view');
    Route::post('/escaner/validar', [RifaController::class, 'validarBoleto'])->name('escaner.validar');

    // ==========================================
    // GESTIÓN DE VENDEDORES Y ASIGNACIONES (NUEVO)
    // ==========================================
    Route::get('/vendedores', [VendedorController::class, 'index'])->name('vendedores.index');
    Route::post('/vendedores', [VendedorController::class, 'store'])->name('vendedores.store');
    Route::post('/vendedores/asignar', [VendedorController::class, 'asignar'])->name('vendedores.asignar');

    // ==========================================
    // OTRAS VISTAS SIMPLES
    // ==========================================
    Route::view('/boletos', 'admin.boletos')->name('boletos');
    Route::view('/premios', 'admin.premios')->name('premios');
    
    // Esta la dejamos por si quieres un reporte global después, 
    // pero la acción principal estará en 'vendedores'
    Route::view('/reportes', 'admin.reportes')->name('reportes');

});