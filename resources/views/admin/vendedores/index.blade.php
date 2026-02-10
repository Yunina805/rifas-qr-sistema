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
                        <th class="px-6 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">Contacto</th>
                        <th class="px-6 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">Rendimiento</th>
                        <th class="px-6 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 text-center">Inventario</th>
                        <th class="px-6 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 text-right"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($vendedores as $vendedor)
                    <tr class="group hover:bg-slate-50 transition-colors">
                        {{-- Vendedor Info --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-600 font-bold text-xs">
                                    {{ substr($vendedor->nombre, 0, 2) }}
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800 group-hover:text-indigo-600 transition-colors">{{ $vendedor->nombre }}</p>
                                    <p class="text-xs text-slate-400">{{ $vendedor->alias ?? 'Sin alias' }}</p>
                                </div>
                            </div>
                        </td>

                        {{-- Contacto --}}
                        <td class="px-6 py-4">
                            @if($vendedor->telefono)
                                <div class="flex items-center gap-2 text-slate-600">
                                    <i class="ri-phone-line text-slate-400"></i>
                                    <span class="font-mono text-xs">{{ $vendedor->telefono }}</span>
                                </div>
                            @else
                                <span class="text-slate-300 text-xs italic">No registrado</span>
                            @endif
                        </td>

                        {{-- Rendimiento (Barra Progreso) --}}
                        <td class="px-6 py-4 w-64">
                            @php
                                $avance = $vendedor->total_asignados > 0 ? ($vendedor->total_vendidos / $vendedor->total_asignados) * 100 : 0;
                                $colorBarra = $avance >= 80 ? 'bg-emerald-500' : ($avance >= 40 ? 'bg-blue-500' : 'bg-amber-500');
                            @endphp
                            <div class="flex justify-between text-xs mb-1.5">
                                <span class="font-medium text-slate-700">{{ number_format($avance, 0) }}% Eficacia</span>
                            </div>
                            <div class="h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full {{ $colorBarra }} rounded-full" style="width: {{ $avance }}%"></div>
                            </div>
                        </td>

                        {{-- Inventario (Chips) --}}
                        <td class="px-6 py-4">
                            <div class="flex justify-center gap-2">
                                <div class="text-center px-3 py-1 rounded-lg bg-green-50 border border-green-100">
                                    <span class="block text-xs font-bold text-green-700">{{ $vendedor->total_vendidos }}</span>
                                    <span class="text-[10px] text-green-600 uppercase">Vend</span>
                                </div>
                                <div class="text-center px-3 py-1 rounded-lg bg-slate-50 border border-slate-200">
                                    <span class="block text-xs font-bold text-slate-700">{{ $vendedor->total_asignados }}</span>
                                    <span class="text-[10px] text-slate-500 uppercase">Asig</span>
                                </div>
                            </div>
                        </td>

                        {{-- Acciones --}}
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded transition-colors" title="Ver Detalle">
                                    <i class="ri-eye-line text-lg"></i>
                                </button>
                                <button class="p-1.5 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded transition-colors" title="Asignar Boletos">
                                    <i class="ri-ticket-line text-lg"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mb-3">
                                    <i class="ri-user-search-line text-xl text-slate-400"></i>
                                </div>
                                <p class="text-sm font-medium text-slate-900">No hay vendedores registrados</p>
                                <p class="text-xs text-slate-500 mt-1">Agrega tu primer vendedor para comenzar.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL CREAR VENDEDOR --}}
<div id="createModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-[2px] transition-opacity" onclick="closeCreateModal()"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4">
            <div id="createModalContent" class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:w-full sm:max-w-md border border-slate-200 scale-95 opacity-0">
                
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="font-bold text-slate-900">Registrar Vendedor</h3>
                    <button onclick="closeCreateModal()" class="text-slate-400 hover:text-slate-600"><i class="ri-close-line text-xl"></i></button>
                </div>

                <form action="{{ route('admin.vendedores.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nombre Completo</label>
                        <input type="text" name="nombre" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:border-slate-400 focus:ring-1 focus:ring-slate-400 outline-none transition-all" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Alias / Referencia</label>
                        <input type="text" name="alias" placeholder="Ej. Tienda Centro" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:border-slate-400 focus:ring-1 focus:ring-slate-400 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Teléfono</label>
                        <input type="text" name="telefono" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:border-slate-400 focus:ring-1 focus:ring-slate-400 outline-none transition-all">
                    </div>

                    <div class="pt-4 flex justify-end gap-2">
                        <button type="button" onclick="closeCreateModal()" class="px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-lg">Cancelar</button>
                        <button type="submit" class="px-4 py-2 text-sm font-bold text-white bg-slate-900 hover:bg-slate-800 rounded-lg">Guardar Vendedor</button>
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
                            <select name="vendedor_id" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:border-slate-400 outline-none">
                                @foreach($vendedores as $v)
                                    <option value="{{ $v->id }}">{{ $v->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Rifa</label>
                            <select name="rifa_id" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:border-slate-400 outline-none">
                                @foreach($rifas as $r)
                                    <option value="{{ $r->id }}">{{ $r->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Tabs de Modo --}}
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
                            <input type="number" name="cantidad" placeholder="Ej. 50" class="w-full pl-3 pr-10 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:border-slate-400 outline-none font-mono">
                            <span class="absolute right-3 top-2 text-xs text-slate-400 font-bold">UDS</span>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1">El sistema seleccionará boletos disponibles al azar.</p>
                    </div>

                    <div x-show="mode === 'rango'" x-transition:enter="transition ease-out duration-200" style="display: none;">
                        <div class="flex items-center gap-3">
                            <div class="flex-1">
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Del Folio</label>
                                <input type="text" name="folio_inicial" placeholder="001" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:border-slate-400 outline-none font-mono text-center">
                            </div>
                            <span class="text-slate-300 mt-5"><i class="ri-arrow-right-line"></i></span>
                            <div class="flex-1">
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Al Folio</label>
                                <input type="text" name="folio_final" placeholder="100" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:border-slate-400 outline-none font-mono text-center">
                            </div>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1">Se asignarán todos los boletos disponibles en este rango secuencial.</p>
                    </div>

                    <div class="pt-2 flex justify-end gap-2 border-t border-slate-100">
                        <button type="button" onclick="closeAssignModal()" class="px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-lg">Cancelar</button>
                        <button type="submit" class="px-4 py-2 text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm shadow-blue-200">Confirmar Asignación</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Scripts para Modales --}}
<script>
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

    function openCreateModal() { toggleModal('createModal', true); }
    function closeCreateModal() { toggleModal('createModal', false); }
    
    function openAssignModal() { toggleModal('assignModal', true); }
    function closeAssignModal() { toggleModal('assignModal', false); }
</script>

@endsection