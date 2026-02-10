@extends('layouts.admin')

@section('title', 'Admin · Dashboard')

{{-- Header Dinámico --}}
@section('context_title', 'Vista General')
@section('context_subtitle', 'Métricas y rendimiento · ' . date('F Y'))
@section('user_name', Auth::user()->name ?? 'Admin')

{{-- Botón de Acción Principal en el Header (Opcional) --}}
@section('action_button')
    <a href="{{ route('admin.rifas') }}" class="inline-flex items-center justify-center gap-2 bg-slate-900 hover:bg-slate-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors shadow-sm ring-1 ring-slate-900/5">
        <i class="ri-add-line"></i> Nueva Rifa
    </a>
@endsection

@section('content')

<div class="space-y-6">

    {{-- SECCIÓN 1: KPI CARDS (Métricas Globales) --}}
    {{-- Diseño: Tarjetas limpias con iconos sutiles y tipografía técnica --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm relative overflow-hidden group hover:border-slate-300 transition-colors">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Ingresos Hoy</p>
                    <h3 class="text-2xl font-display font-bold text-slate-900 mt-1">${{ number_format($ingresosHoy, 2) }}</h3>
                </div>
                <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg">
                    <i class="ri-money-dollar-circle-line text-lg"></i>
                </div>
            </div>
            <div class="flex items-center gap-1 text-xs font-medium text-emerald-600">
                <i class="ri-arrow-up-line"></i>
                <span>{{ $ventasHoy }} boletos vendidos</span>
                <span class="text-slate-400 font-normal ml-1">vs ayer</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm relative overflow-hidden hover:border-slate-300 transition-colors">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Volumen Total</p>
                    <h3 class="text-2xl font-display font-bold text-slate-900 mt-1">{{ number_format($boletosVendidos) }}</h3>
                </div>
                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                    <i class="ri-ticket-line text-lg"></i>
                </div>
            </div>
            <p class="text-xs text-slate-400">Boletos vendidos históricamente</p>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm relative overflow-hidden hover:border-slate-300 transition-colors">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">En Curso</p>
                    <h3 class="text-2xl font-display font-bold text-slate-900 mt-1">{{ $rifasActivas }}</h3>
                </div>
                <div class="p-2 bg-purple-50 text-purple-600 rounded-lg">
                    <i class="ri-calendar-event-line text-lg"></i>
                </div>
            </div>
            <p class="text-xs text-slate-400">Sorteos activos actualmente</p>
        </div>

        <div class="bg-slate-900 p-5 rounded-xl shadow-md relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-slate-800 rounded-full blur-3xl opacity-50 -mr-10 -mt-10 pointer-events-none"></div>
            
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Mi Ganancia</p>
                        <h3 class="text-2xl font-display font-bold text-white mt-1">${{ number_format($ingresosTotales, 2) }}</h3>
                    </div>
                    <div class="p-2 bg-slate-800 text-slate-300 rounded-lg">
                        <i class="ri-bank-card-line text-lg"></i>
                    </div>
                </div>
                <div class="w-full bg-slate-800 h-1.5 rounded-full overflow-hidden mt-2">
                    <div class="bg-emerald-500 h-full rounded-full" style="width: 100%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- SECCIÓN 2: RIFAS ACTIVAS (Columna Ancha) --}}
        <div class="lg:col-span-2 space-y-6">
            
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <i class="ri-fire-fill text-orange-500"></i> Prioridad Alta
                </h3>
                <a href="{{ route('admin.rifas') }}" class="text-xs font-medium text-slate-500 hover:text-slate-900 transition-colors">Ver todo</a>
            </div>

            @forelse($rifas as $rifa)
                @php
                    $porcentaje = $rifa->total_boletos > 0 ? ($rifa->vendidos / $rifa->total_boletos) * 100 : 0;
                @endphp
                
                <div onclick="window.location='{{ route('admin.rifas.lotes', $rifa->id) }}'" 
                     class="group bg-white rounded-xl border border-slate-200 p-5 hover:border-slate-300 hover:shadow-md transition-all cursor-pointer relative">
                    
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-500 group-hover:bg-slate-900 group-hover:text-white transition-colors">
                                <i class="ri-coupon-3-line text-lg"></i>
                            </div>
                            <div>
                                <h4 class="font-display font-bold text-slate-800 text-base leading-tight group-hover:text-indigo-600 transition-colors">{{ $rifa->nombre }}</h4>
                                <p class="text-xs text-slate-400 mt-0.5 flex items-center gap-1">
                                    <i class="ri-map-pin-line"></i> {{ $rifa->sede ?? 'Sede Virtual' }}
                                </p>
                            </div>
                        </div>
                        
                        <div class="text-right">
                             <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span> Activa
                            </span>
                        </div>
                    </div>

                    <div class="bg-slate-50 rounded-lg p-3 border border-slate-100">
                        <div class="flex justify-between items-end mb-2 text-xs">
                            <div>
                                <span class="text-slate-500 font-medium">Progreso de Venta</span>
                            </div>
                            <div class="text-right">
                                <span class="font-bold text-slate-900">{{ number_format($porcentaje, 1) }}%</span>
                                <span class="text-slate-400"> completado</span>
                            </div>
                        </div>
                        
                        <div class="h-2 w-full bg-slate-200 rounded-full overflow-hidden">
                            <div class="h-full bg-slate-900 rounded-full transition-all duration-1000 ease-out" style="width: {{ $porcentaje }}%"></div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mt-3 pt-3 border-t border-slate-200/50">
                            <div>
                                <p class="text-[10px] text-slate-400 uppercase font-bold">Vendidos</p>
                                <p class="text-sm font-semibold text-slate-700">{{ number_format($rifa->vendidos) }} <span class="text-slate-400 font-normal">/ {{ number_format($rifa->total_boletos) }}</span></p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] text-slate-400 uppercase font-bold">Recaudado</p>
                                <p class="text-sm font-semibold text-slate-900">${{ number_format($rifa->vendidos * $rifa->precio_boleto, 0) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl border border-dashed border-slate-300 p-8 text-center">
                    <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-400">
                        <i class="ri-coupon-line text-xl"></i>
                    </div>
                    <h3 class="text-sm font-medium text-slate-900">No hay rifas activas</h3>
                    <p class="text-xs text-slate-500 mt-1 mb-4">Comienza creando tu primer evento para ver métricas.</p>
                    <a href="{{ route('admin.rifas') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700 hover:underline">Crear rifa ahora &rarr;</a>
                </div>
            @endforelse

            @if($rifas->count() > 0)
                <button onclick="window.location='{{ route('admin.rifas') }}'" class="w-full py-3 rounded-xl border border-dashed border-slate-300 text-slate-500 text-sm font-medium hover:border-slate-400 hover:bg-slate-50 hover:text-slate-700 transition-all flex items-center justify-center gap-2">
                    <i class="ri-add-circle-line"></i> Crear Nueva Rifa
                </button>
            @endif

        </div>

        {{-- SECCIÓN 3: SIDEBAR WIDGETS (Actividad) --}}
        <div class="space-y-6">

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm flex flex-col h-full max-h-[500px]">
        
        {{-- HEADER --}}
        <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 rounded-t-xl">
            <h3 class="font-bold text-slate-700 text-xs uppercase tracking-wide">Actividad Reciente</h3>
            <div class="flex items-center gap-1.5">
                <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                <span class="text-[10px] text-slate-400 font-medium">En vivo</span>
            </div>
        </div>

        {{-- LISTA DINÁMICA --}}
        <div class="flex-1 overflow-y-auto p-5 space-y-0 custom-scrollbar">
            
            @forelse($actividadReciente as $actividad)
                
                {{-- Logica de Colores e Iconos según el tipo de actividad --}}
                @php
                    $esVenta = $actividad->estado === 'vendido';
                    $esEntrega = $actividad->estado === 'entregado';
                    
                    $colorDot = $esEntrega ? 'bg-amber-500' : 'bg-indigo-500';
                    $icono = $esEntrega ? 'ri-trophy-fill' : 'ri-ticket-fill';
                    $textoColor = $esEntrega ? 'text-amber-700' : 'text-slate-800';
                    $titulo = $esEntrega ? '¡Premio Entregado!' : 'Nueva Venta';
                @endphp

                <div class="relative pl-6 border-l border-slate-200 pb-6 last:pb-0 last:border-transparent group">
                    
                    {{-- Punto de la línea de tiempo --}}
                    <div class="absolute -left-[5px] top-1 w-2.5 h-2.5 rounded-full {{ $colorDot }} ring-4 ring-white group-hover:scale-125 transition-transform"></div>
                    
                    <div>
                        {{-- Tiempo relativo (Ej: Hace 5 min) --}}
                        <p class="text-[10px] text-slate-400 mb-0.5 uppercase font-bold tracking-wider">
                            {{ $actividad->updated_at->diffForHumans() }}
                        </p>
                        
                        {{-- Título de la acción --}}
                        <p class="text-sm font-bold {{ $textoColor }} flex items-center gap-1.5">
                            <i class="{{ $icono }}"></i> {{ $titulo }}
                        </p>
                        
                        {{-- Detalles --}}
                        <div class="text-xs text-slate-500 mt-1 bg-slate-50 p-2 rounded border border-slate-100">
                            <p>
                                <span class="font-semibold text-slate-700">Folio #{{ $actividad->folio }}</span> 
                                en <span class="italic">{{ $actividad->rifa->nombre }}</span>
                            </p>
                            
                            @if($actividad->vendedor)
                                <p class="mt-1 flex items-center gap-1 text-slate-400">
                                    <i class="ri-user-star-line"></i> Vendedor: {{ $actividad->vendedor->nombre }}
                                </p>
                            @else
                                <p class="mt-1 flex items-center gap-1 text-slate-400">
                                    <i class="ri-store-line"></i> Venta Directa / Oficina
                                </p>
                            @endif

                            @if($esVenta)
                                <p class="mt-1 font-mono text-emerald-600 font-bold">+ ${{ number_format($actividad->rifa->precio_boleto, 2) }}</p>
                            @endif
                        </div>
                    </div>
                </div>

            @empty
                
                {{-- ESTADO VACÍO (Si no hay nada aún) --}}
                <div class="relative pl-6 border-l border-slate-100 opacity-60">
                    <div class="absolute -left-[5px] top-1 w-2.5 h-2.5 rounded-full bg-slate-300 ring-4 ring-white"></div>
                    <div class="bg-slate-50 p-4 rounded-lg border border-slate-100 text-center">
                        <i class="ri-time-line text-2xl text-slate-300 mb-2 block"></i>
                        <p class="text-xs text-slate-400 italic">
                            El sistema está esperando las primeras transacciones.
                        </p>
                    </div>
                </div>

            @endforelse

        </div>
    </div>
</div>

    </div>
</div>

@endsection