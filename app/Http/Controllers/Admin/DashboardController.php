<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rifa;
use App\Models\Boleto;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // ==========================================
        // 1. INDICADORES GENERALES
        // ==========================================
        $totalRifas = Rifa::count();
        $rifasActivas = Rifa::where('estado', 'activa')->count();

        // ==========================================
        // 2. CÁLCULOS FINANCIEROS (HISTÓRICO)
        // ==========================================
        
        // CORRECCIÓN: Usamos whereIn para mayor seguridad y limpieza
        // Cuenta boletos 'vendido' Y 'entregado'
        $boletosVendidos = Boleto::whereIn('estado', ['vendido', 'entregado'])->count();
            
        // Ingresos Totales: Sumamos el precio de la rifa asociado a cada boleto vendido
        $ingresosTotales = Boleto::whereIn('boletos.estado', ['vendido', 'entregado'])
            ->join('rifas', 'boletos.rifa_id', '=', 'rifas.id')
            ->sum('rifas.precio_boleto');

        // ==========================================
        // 3. MOVIMIENTOS DE HOY (KPI DIARIO)
        // ==========================================
        
        $ventasHoy = Boleto::whereIn('estado', ['vendido', 'entregado'])
            ->whereDate('fecha_venta', Carbon::today())
            ->count();
            
        $ingresosHoy = Boleto::whereIn('boletos.estado', ['vendido', 'entregado'])
            ->whereDate('boletos.fecha_venta', Carbon::today())
            ->join('rifas', 'boletos.rifa_id', '=', 'rifas.id')
            ->sum('rifas.precio_boleto');

        // ==========================================
        // 4. DATOS PARA LA TABLA DE PROGRESO
        // ==========================================
        
        // Obtenemos solo las activas y contamos sus ventas dinámicamente
        // Esto crea el atributo virtual 'vendidos' que usamos en la vista
        $rifas = Rifa::where('estado', 'activa')
            ->withCount(['boletos as vendidos' => function($query) {
                $query->whereIn('estado', ['vendido', 'entregado']);
            }])
            ->get();

        return view('admin.dashboard', compact(
            'totalRifas', 
            'rifasActivas', 
            'boletosVendidos', 
            'ingresosTotales', 
            'ventasHoy', 
            'ingresosHoy', 
            'rifas'
        ));
    }
}