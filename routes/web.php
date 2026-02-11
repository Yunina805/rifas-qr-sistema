<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\RifaController;
use App\Http\Controllers\Admin\LoteController;
use App\Http\Controllers\PublicRifaController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\VendedorController;

/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS (Sin Login)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Verificación de Boletos (QR Público)
Route::get('/verificar/{codigo}', [PublicRifaController::class, 'verificar'])
    ->name('boleto.verificar');

/*
|--------------------------------------------------------------------------
| RUTAS PROTEGIDAS (Requieren Login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // ==========================================
    // ZONA ADMINISTRADOR (Role: Admin)
    // ==========================================
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Gestión de Rifas
        Route::get('/rifas', [RifaController::class, 'index'])->name('rifas');
        Route::post('/rifas', [RifaController::class, 'store'])->name('rifas.store');
        Route::get('/rifas/{rifa}/editar', [RifaController::class, 'edit'])->name('rifas.edit');
        Route::put('/rifas/{rifa}', [RifaController::class, 'update'])->name('rifas.update');
        Route::post('/rifas/{rifa}/activar', [RifaController::class, 'activar'])->name('rifas.activar');
        Route::post('/rifas/{rifa}/finalizar', [RifaController::class, 'finalizar'])->name('rifas.finalizar');

        // Gestión de Lotes (Boletos)
        Route::get('/rifas/{rifa}/lotes', [LoteController::class, 'index'])->name('rifas.lotes');
        Route::post('/rifas/{rifa}/lotes', [LoteController::class, 'store'])->name('rifas.lotes.store');
        Route::get('/rifas/{rifa}/imprimir', [LoteController::class, 'imprimir'])->name('rifas.imprimir');
        Route::post('/boletos/{boleto}/vender', [LoteController::class, 'vender'])->name('boletos.vender');
        Route::post('/boletos/{boleto}/liberar', [LoteController::class, 'liberar'])->name('boletos.liberar');

        // Gestión de Vendedores (AQUÍ FALTABAN RUTAS)
        Route::get('/vendedores', [VendedorController::class, 'index'])->name('vendedores.index');
        Route::post('/vendedores', [VendedorController::class, 'store'])->name('vendedores.store');
        Route::put('/vendedores/{user}', [VendedorController::class, 'update'])->name('vendedores.update'); // <--- AGREGAR ESTA (Para editar)
        Route::delete('/vendedores/{id}', [VendedorController::class, 'destroy'])->name('vendedores.destroy'); // <--- AGREGAR ESTA (Para borrar)
        Route::post('/vendedores/asignar', [VendedorController::class, 'asignar'])->name('vendedores.asignar');

        // Otras Vistas
        Route::view('/boletos', 'admin.boletos')->name('boletos');
        Route::view('/premios', 'admin.premios')->name('premios');
        Route::view('/reportes', 'admin.reportes')->name('reportes');
        Route::get('/boletos/{boleto}/imprimir', [LoteController::class, 'imprimirIndividual'])->name('boletos.imprimir');

        // Escáner Admin
        Route::get('/escaner', [RifaController::class, 'scanView'])->name('escaner.view');
        Route::post('/escaner/validar', [RifaController::class, 'validarBoleto'])->name('escaner.validar');
    });

    // ==========================================
    // ZONA VENDEDOR (Role: Vendedor)
    // ==========================================
    Route::middleware('role:vendedor')->prefix('app')->name('app.')->group(function () {
        
        // Escáner para el Vendedor
        Route::get('/escaner', [RifaController::class, 'scanView'])->name('escaner.view');
        Route::post('/escaner/validar', [RifaController::class, 'validarBoleto'])->name('escaner.validar');
    });

    // Perfil de Usuario (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';