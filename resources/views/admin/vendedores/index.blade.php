@extends('layouts.admin')

@section('title', 'Admin · Fuerza de Ventas')

@section('content')

<div class="space-y-6">

    {{-- HEADER & ACTIONS --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <nav class="flex text-xs font-medium text-slate-500 mb-1 space-x-2">
                <span>Gestión</span>
                <span class="text-slate-300">/</span>
                <span class="text-slate-800">Vendedores</span>
            </nav>
            <h1 class="font-display font-bold text-xl text-slate-900 tracking-tight flex items-center gap-2">
                Fuerza de Ventas
                <span class="bg-slate-100 text-slate-600 text-[10px] px-2 py-0.5 rounded-full border border-slate-200 uppercase tracking-wide">
                    {{ $vendedores->count() }} Activos
                </span>
            </h1>
        </div>
        
        <div class="flex gap-2">
            <button onclick="openAssignModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 hover:text-indigo-600 transition-all shadow-sm">
                <i class="ri-ticket-line"></i>
                <span>Asignación Masiva</span>
            </button>
            <button onclick="openCreateModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-slate-800 transition-all shadow-sm">
                <i class="ri-user-add-line"></i>
                <span>Nuevo Vendedor</span>
            </button>
        </div>
    </div>

    {{-- METRICS SUMMARY --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Vendedores</p>
                <p class="text-2xl font-display font-bold text-slate-900 mt-1">{{ $vendedores->count() }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-slate-50 flex items-center justify-center text-slate-400">
                <i class="ri-group-line text-xl"></i>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Boletos en Calle</p>
                <p class="text-2xl font-display font-bold text-blue-600 mt-1">
                    {{ number_format($vendedores->sum('total_asignados')) }}
                </p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                <i class="ri-coupon-3-line text-xl"></i>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            @php
                $totalAsignados = $vendedores->sum('total_asignados');
                $totalVendidos = $vendedores->sum('total_vendidos');
                $efectividad = $totalAsignados > 0 ? ($totalVendidos / $totalAsignados) * 100 : 0;
            @endphp
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Efectividad Global</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <p class="text-2xl font-display font-bold text-emerald-600">{{ number_format($efectividad, 1) }}%</p>
                    <span class="text-xs text-slate-400 font-medium">de conversión</span>
                </div>
            </div>
            <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600">
                <i class="ri-bar-chart-grouped-line text-xl"></i>
            </div>
        </div>
    </div>

    {{-- ALERTAS DE ERROR --}}
    @if ($errors->any())
        <div class="bg-red-50 border border-red-100 rounded-lg p-4 flex items-start gap-3">
            <i class="ri-error-warning-fill text-red-500 mt-0.5"></i>
            <div>
                <h3 class="text-sm font-bold text-red-800">No se pudo procesar la solicitud</h3>
                <ul class="mt-1 text-xs text-red-700 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- MAIN TABLE --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        
        {{-- Toolbar --}}
        <div class="p-4 border-b border-slate-100 flex gap-4 bg-slate-50/50">
            <div class="relative flex-1 max-w-sm">
                <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" placeholder="Buscar vendedor por nombre o alias..." 
                       class="w-full pl-9 pr-4 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-slate-400 focus:ring-1 focus:ring-slate-400 transition-all text-slate-700">
            </div>
            <div class="flex items-center gap-2">
                 <select class="pl-3 pr-8 py-2 bg-white border border-slate-200 rounded-lg text-sm text-slate-600 focus:outline-none focus:border-slate-400 cursor-pointer">
                    <option>Ordenar por: Rendimiento</option>
                    <option>Ordenar por: Nombre</option>
                    <option>Ordenar por: Ventas</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
    <table class="w-full text-left text-sm whitespace-nowrap">
        <thead class="bg-slate-50 border-b border-slate-200">
            <tr>
                <th class="px-6 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">Vendedor</th>
                <th class="px-6 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">Rifas Activas</th>
                <th class="px-6 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 text-center">Métricas de Inventario</th>
                <th class="px-6 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">Eficacia</th>
                <th class="px-6 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 text-right">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
    @forelse($vendedores as $vendedor)
    @php
        // Generamos el mapa de estadísticas por rifa para este vendedor en específico
        $statsPorRifa = $vendedor->boletos->groupBy('rifa_id')->map(function($boletos) {
            return [
                'asignados' => $boletos->count(),
                'vendidos' => $boletos->where('estado', 'vendido')->count(),
                'pendientes' => $boletos->where('estado', 'disponible')->count(),
            ];
        });

        // Agregamos la opción "General" al mapa
        $statsPorRifa['general'] = [
            'asignados' => $vendedor->total_asignados,
            'vendidos' => $vendedor->total_vendidos,
            'pendientes' => $vendedor->total_pendientes,
        ];
    @endphp

    <tr class="group hover:bg-slate-50 transition-colors" 
        x-data="{ 
            selected: 'general',
            stats: {{ json_encode($statsPorRifa) }},
            get current() { return this.stats[this.selected] }
        }">
        
        {{-- Vendedor Info --}}
        <td class="px-6 py-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-xs">
                    {{ substr($vendedor->name, 0, 2) }}
                </div>
                <div>
                    <p class="font-bold text-slate-800">{{ $vendedor->name }}</p>
                    <p class="text-[10px] text-slate-400 uppercase tracking-tighter">{{ $vendedor->alias ?? 'Vendedor Externo' }}</p>
                </div>
            </div>
        </td>

        {{-- Selector de Rifas Activas --}}
        <td class="px-6 py-4">
            <div class="max-w-[200px]">
                <select x-model="selected" 
                        class="w-full pl-2 pr-8 py-1.5 bg-white border border-slate-200 rounded-md text-[11px] font-bold text-indigo-700 focus:outline-none focus:border-indigo-400 cursor-pointer shadow-sm">
                    <option value="general">📊 Resumen General</option>
                    @foreach($vendedor->boletos->pluck('rifa')->unique('id') as $rifa)
                        <option value="{{ $rifa->id }}">🎫 {{ $rifa->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </td>

        {{-- Métricas de Inventario REACTIVAS --}}
        <td class="px-6 py-4">
            <div class="flex items-center justify-center gap-3 text-center">
                {{-- Totales --}}
                <div class="px-3 py-1 bg-slate-50 rounded-lg border border-slate-100 min-w-[75px]">
                    <span class="block text-xs font-black text-slate-700" x-text="current.asignados.toLocaleString()"></span>
                    <span class="text-[9px] text-slate-400 uppercase font-bold tracking-tighter">Asignados</span>
                </div>
                {{-- Vendidos --}}
                <div class="px-3 py-1 bg-emerald-50 rounded-lg border border-emerald-100 min-w-[75px]">
                    <span class="block text-xs font-black text-emerald-700" x-text="current.vendidos.toLocaleString()"></span>
                    <span class="text-[9px] text-emerald-500 uppercase font-bold tracking-tighter">Vendidos</span>
                </div>
                {{-- Por Vender --}}
                <div class="px-3 py-1 bg-amber-50 rounded-lg border border-amber-100 min-w-[75px]">
                    <span class="block text-xs font-black text-amber-700" x-text="current.pendientes.toLocaleString()"></span>
                    <span class="text-[9px] text-amber-500 uppercase font-bold tracking-tighter">Por Vender</span>
                </div>
            </div>
        </td>

        {{-- Eficacia REACTIVA --}}
        <td class="px-6 py-4">
            <div class="w-32">
                <div class="flex justify-between text-[10px] mb-1">
                    <span class="font-bold text-slate-600" x-text="Math.round((current.vendidos / (current.asignados || 1)) * 100) + '%'"></span>
                    <span class="text-slate-400 italic">Eficacia</span>
                </div>
                <div class="h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full bg-slate-900 rounded-full transition-all duration-500" 
                         :style="'width: ' + ((current.vendidos / (current.asignados || 1)) * 100) + '%'"></div>
                </div>
            </div>
        </td>

        {{-- Acciones --}}
        <td class="px-6 py-4 text-right">
            <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                <button onclick='editarVendedor(@json($vendedor))' class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-white rounded-lg transition-all shadow-sm border border-transparent hover:border-slate-100">
                    <i class="ri-pencil-line"></i>
                </button>
                <form action="{{ route('admin.vendedores.destroy', $vendedor->id) }}" method="POST" onsubmit="return confirm('¿Eliminar vendedor?');" class="inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-white rounded-lg transition-all shadow-sm border border-transparent hover:border-slate-100">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </form>
            </div>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="5" class="px-6 py-20 text-center text-slate-400 italic bg-slate-50/50">
            No hay vendedores registrados en el sistema.
        </td>
    </tr>
    @endforelse
</tbody>
    </table>
</div>
    </div>
</div>

{{-- MODAL CREAR/EDITAR (Reutilizable logicamente o separado) --}}
{{-- Aquí pongo los dos separados para claridad, pero comparten estilos --}}

<div id="createModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-[2px] transition-opacity" onclick="closeCreateModal()"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4">
            <div id="createModalContent" class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:w-full sm:max-w-md border border-slate-200 scale-95 opacity-0">
                
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                    <h3 class="font-bold text-slate-900">Registrar Vendedor</h3>
                    <button onclick="closeCreateModal()" class="text-slate-400 hover:text-slate-600"><i class="ri-close-line text-xl"></i></button>
                </div>

                <form action="{{ route('admin.vendedores.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nombre Completo</label>
                        <input type="text" name="name" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:border-slate-400 focus:ring-1 focus:ring-slate-400 outline-none transition-all" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Alias / Referencia</label>
                        <input type="text" name="alias" placeholder="Ej. Tienda Centro" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:border-slate-400 focus:ring-1 focus:ring-slate-400 outline-none transition-all">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Teléfono</label>
                            <input type="text" name="telefono" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:border-slate-400 focus:ring-1 focus:ring-slate-400 outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Correo (Login)</label>
                            <input type="email" name="email" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:border-slate-400 focus:ring-1 focus:ring-slate-400 outline-none transition-all" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Contraseña</label>
                            <input type="password" name="password" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:border-slate-400 focus:ring-1 focus:ring-slate-400 outline-none transition-all" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Confirmar</label>
                            <input type="password" name="password_confirmation" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:border-slate-400 focus:ring-1 focus:ring-slate-400 outline-none transition-all" required>
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end gap-2 border-t border-slate-100 mt-2">
                        <button type="button" onclick="closeCreateModal()" class="px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-lg">Cancelar</button>
                        <button type="submit" class="px-4 py-2 text-sm font-bold text-white bg-slate-900 hover:bg-slate-800 rounded-lg">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="editModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-[2px] transition-opacity" onclick="closeEditModal()"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4">
            <div id="editModalContent" class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:w-full sm:max-w-md border border-slate-200 scale-95 opacity-0">
                
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-indigo-50">
                    <h3 class="font-bold text-indigo-900">Editar Vendedor</h3>
                    <button onclick="closeEditModal()" class="text-indigo-400 hover:text-indigo-600"><i class="ri-close-line text-xl"></i></button>
                </div>

                <form id="formEditar" method="POST" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')
                    
                    <input type="hidden" id="edit_id" name="id">

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nombre Completo</label>
                        <input type="text" id="edit_name" name="name" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400 outline-none transition-all" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Alias</label>
                        <input type="text" id="edit_alias" name="alias" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Teléfono</label>
                        <input type="text" id="edit_telefono" name="telefono" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Correo</label>
                        <input type="email" id="edit_email" name="email" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400 outline-none transition-all" required>
                    </div>
                    
                    <div class="border-t border-slate-100 pt-3">
                        <p class="text-[10px] text-slate-400 mb-2 uppercase tracking-wide">Cambiar Contraseña (Opcional)</p>
                        <div class="grid grid-cols-2 gap-4">
                            <input type="password" name="password" placeholder="Nueva" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:border-indigo-400 outline-none">
                            <input type="password" name="password_confirmation" placeholder="Confirmar" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:border-indigo-400 outline-none">
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end gap-2">
                        <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-lg">Cancelar</button>
                        <button type="submit" class="px-4 py-2 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- MODAL ASIGNAR BOLETOS --}}
<div id="assignModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-[2px] transition-opacity" onclick="closeAssignModal()"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4">
            <div id="assignModalContent" class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:w-full sm:max-w-md border border-slate-200 scale-95 opacity-0">
                
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-slate-900">Asignar Boletos</h3>
                        <p class="text-xs text-slate-500">Distribuye inventario a tu fuerza de ventas.</p>
                    </div>
                    <button onclick="closeAssignModal()" class="text-slate-400 hover:text-slate-600"><i class="ri-close-line text-xl"></i></button>
                </div>

                <form action="{{ route('admin.vendedores.asignar') }}" method="POST" class="p-6 space-y-5" x-data="{ mode: 'aleatorio' }">
                    @csrf
                    
                    {{-- Selección Básica --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Vendedor</label>
                            <select name="vendedor_id" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:border-slate-400 outline-none cursor-pointer">
                                @foreach($vendedores as $v)
                                    <option value="{{ $v->id }}">{{ $v->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Rifa</label>
                            <select name="rifa_id" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:border-slate-400 outline-none cursor-pointer">
                                @foreach($rifas as $r)
                                    <option value="{{ $r->id }}">{{ $r->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Tabs de Modo (Alpine) --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-2">Método de Asignación</label>
                        <div class="flex p-1 bg-slate-100 rounded-lg">
                            <button type="button" @click="mode = 'aleatorio'" 
                                :class="mode === 'aleatorio' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                                class="flex-1 py-1.5 text-xs font-bold rounded-md transition-all flex items-center justify-center gap-2">
                                <i class="ri-shuffle-line"></i> Aleatorio
                            </button>
                            <button type="button" @click="mode = 'rango'" 
                                :class="mode === 'rango' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                                class="flex-1 py-1.5 text-xs font-bold rounded-md transition-all flex items-center justify-center gap-2">
                                <i class="ri-sort-number-asc"></i> Por Rango
                            </button>
                            <input type="hidden" name="tipo_asignacion" x-model="mode">
                        </div>
                    </div>

                    {{-- Inputs Dinámicos --}}
                    <div x-show="mode === 'aleatorio'" x-transition:enter="transition ease-out duration-200">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Cantidad de Boletos</label>
                        <div class="relative">
                            <input type="number" name="cantidad" placeholder="Ej. 50" class="w-full pl-3 pr-10 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:border-slate-400 outline-none font-mono">
                            <span class="absolute right-3 top-2 text-xs text-slate-400 font-bold">UDS</span>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1">El sistema seleccionará boletos disponibles al azar.</p>
                    </div>

                    <div x-show="mode === 'rango'" x-transition:enter="transition ease-out duration-200" style="display: none;">
                        <div class="flex items-center gap-3">
                            <div class="flex-1">
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Del Folio</label>
                                <input type="text" name="folio_inicial" placeholder="001" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:border-slate-400 outline-none font-mono text-center">
                            </div>
                            <span class="text-slate-300 mt-5"><i class="ri-arrow-right-line"></i></span>
                            <div class="flex-1">
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Al Folio</label>
                                <input type="text" name="folio_final" placeholder="100" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:border-slate-400 outline-none font-mono text-center">
                            </div>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1">Se asignarán todos los boletos disponibles en este rango secuencial.</p>
                    </div>

                    <div class="pt-2 flex justify-end gap-2 border-t border-slate-100">
                        <button type="button" onclick="closeAssignModal()" class="px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-lg">Cancelar</button>
                        <button type="submit" class="px-4 py-2 text-sm font-bold text-white bg-slate-900 hover:bg-slate-800 rounded-lg shadow-sm">Confirmar Asignación</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Utilidad genérica para abrir/cerrar modales con animación
    function toggleModal(modalId, show) {
        const modal = document.getElementById(modalId);
        const content = document.getElementById(modalId + 'Content');
        
        if (show) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        } else {
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 200);
        }
    }

    // Wrappers para cada modal
    function openCreateModal() { toggleModal('createModal', true); }
    function closeCreateModal() { toggleModal('createModal', false); }
    
    function openAssignModal() { toggleModal('assignModal', true); }
    function closeAssignModal() { toggleModal('assignModal', false); }

    function openEditModal() { toggleModal('editModal', true); }
    function closeEditModal() { toggleModal('editModal', false); }

    // Lógica específica de Editar
    function editarVendedor(vendedor) {
        // Llenar datos
        document.getElementById('edit_id').value = vendedor.id;
        document.getElementById('edit_name').value = vendedor.name;
        document.getElementById('edit_email').value = vendedor.email;
        document.getElementById('edit_alias').value = vendedor.alias || '';
        document.getElementById('edit_telefono').value = vendedor.telefono || '';

        // Construir URL dinámicamente
        // Nota: Laravel genera rutas con un ID. Usamos un placeholder JS para reemplazarlo.
        let url = "{{ route('admin.vendedores.update', ':id') }}";
        url = url.replace(':id', vendedor.id);
        
        document.getElementById('formEditar').action = url;

        // Abrir Modal
        openEditModal();
    }
</script>

@endsection