@extends('layouts.admin')

@section('title', 'Admin · Lotes: ' . $rifa->nombre)

@section('content')

<div class="space-y-6">

    {{-- HEADER & BREADCRUMBS --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <nav class="flex text-xs font-medium text-slate-500 mb-1 space-x-2">
                <a href="{{ route('admin.rifas') }}" class="hover:text-slate-800 transition-colors">Rifas</a>
                <span class="text-slate-300">/</span>
                <span class="text-slate-800">{{ $rifa->nombre }}</span>
            </nav>
            <h1 class="font-display font-bold text-xl text-slate-900 tracking-tight flex items-center gap-3">
                Gestión de Boletos
                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[10px] font-bold uppercase tracking-wide border border-slate-200">
                    ID: {{ $rifa->id }}
                </span>
            </h1>
        </div>
{{-- PASO 1: VISTA DE LOTES (Botonera con Selector de Color) --}}

<div class="flex items-center gap-4" x-data="{ colorSeleccionado: '#d7ffc1' }">
    
    {{-- 1. Selector de Colores (Círculos) --}}
    <div class="flex items-center gap-2 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-200">
        <span class="text-[10px] font-bold text-slate-400 uppercase mr-1">Color:</span>
        
        {{-- Verde --}}
        <button @click="colorSeleccionado = '#d7ffc1'" 
                :class="colorSeleccionado === '#d7ffc1' ? 'ring-2 ring-slate-400 ring-offset-1' : ''"
                class="w-5 h-5 rounded-full border border-black/10 transition-transform hover:scale-110 shadow-sm" 
                style="background-color: #d7ffc1;"></button>
        
        {{-- Morado --}}
        <button @click="colorSeleccionado = '#e9c6fc'" 
                :class="colorSeleccionado === '#e9c6fc' ? 'ring-2 ring-slate-400 ring-offset-1' : ''"
                class="w-5 h-5 rounded-full border border-black/10 transition-transform hover:scale-110 shadow-sm" 
                style="background-color: #e9c6fc;"></button>
        
        {{-- Azul --}}
        <button @click="colorSeleccionado = '#d3effb'" 
                :class="colorSeleccionado === '#d3effb' ? 'ring-2 ring-slate-400 ring-offset-1' : ''"
                class="w-5 h-5 rounded-full border border-black/10 transition-transform hover:scale-110 shadow-sm" 
                style="background-color: #d3effb;"></button>

        {{-- EXTRA: Amarillo Pastel --}}
        <button @click="colorSeleccionado = '#fff5ba'" 
                :class="colorSeleccionado === '#fff5ba' ? 'ring-2 ring-slate-400 ring-offset-1' : ''"
                class="w-5 h-5 rounded-full border border-black/10 transition-transform hover:scale-110 shadow-sm" 
                style="background-color: #fff5ba;"></button>
    </div>

    {{-- 2. Botones de Acción --}}
    <div class="flex gap-2">
        {{-- Botón IMPRIMIR (Modificado para usar JS) --}}
        <button @click="window.open('{{ route('admin.rifas.imprimir', $rifa) }}?color=' + colorSeleccionado.replace('#', ''), '_blank')" 
           class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition-all shadow-sm cursor-pointer">
            <i class="ri-printer-line"></i>
            <span class="hidden sm:inline">Imprimir Listado</span>
        </button>

        {{-- Botón VOLVER (Intacto) --}}
        <a href="{{ route('admin.rifas') }}" 
           class="inline-flex items-center gap-2 px-3 py-2 bg-slate-900 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-slate-800 transition-all shadow-sm">
            <i class="ri-arrow-left-line"></i>
            <span>Volver</span>
        </a>
    </div>

</div>
    </div>

    {{-- KPI CARDS (Minimalistas) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        
        {{-- Total --}}
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Emisión</p>
                    <p class="text-2xl font-display font-bold text-slate-900 mt-1">{{ number_format($rifa->total_boletos) }}</p>
                </div>
                <div class="p-1.5 bg-slate-50 text-slate-500 rounded-md">
                    <i class="ri-coupon-3-line text-lg"></i>
                </div>
            </div>
            <div class="mt-2 text-xs text-slate-400">Boletos generados</div>
        </div>

        {{-- Vendidos --}}
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Vendidos</p>
                    <p class="text-2xl font-display font-bold text-slate-900 mt-1">{{ number_format($rifa->boletos()->where('estado', 'vendido')->count()) }}</p>
                </div>
                <div class="p-1.5 bg-indigo-50 text-indigo-600 rounded-md">
                    <i class="ri-ticket-line text-lg"></i>
                </div>
            </div>
            <div class="w-full bg-slate-100 h-1 mt-3 rounded-full overflow-hidden">
                <div class="bg-indigo-600 h-full rounded-full" style="width: {{ ($rifa->boletos()->where('estado', 'vendido')->count() / $rifa->total_boletos) * 100 }}%"></div>
            </div>
        </div>

        {{-- Premios --}}
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Ganadores</p>
                    <p class="text-2xl font-display font-bold text-slate-900 mt-1">{{ $rifa->boletos()->where('es_ganador', true)->count() }}</p>
                </div>
                <div class="p-1.5 bg-amber-50 text-amber-600 rounded-md">
                    <i class="ri-trophy-line text-lg"></i>
                </div>
            </div>
            <div class="mt-2 text-xs text-slate-400">Premios asignados</div>
        </div>

        {{-- Recaudado --}}
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Recaudación</p>
                    <p class="text-2xl font-display font-bold text-emerald-600 mt-1 tracking-tight">
                        ${{ number_format($rifa->boletos()->where('estado', 'vendido')->count() * $rifa->precio_boleto, 2) }}
                    </p>
                </div>
                <div class="p-1.5 bg-emerald-50 text-emerald-600 rounded-md">
                    <i class="ri-money-dollar-circle-line text-lg"></i>
                </div>
            </div>
            <div class="mt-2 text-xs text-slate-400">Estimado total</div>
        </div>
    </div>

    {{-- TOOLBAR & FILTROS --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-1.5 flex flex-col md:flex-row gap-2 items-center">
        
        <form method="GET" class="flex-1 w-full flex gap-2">
            {{-- Buscador Integrado --}}
            <div class="relative flex-1 group">
                <i class="ri-search-2-line absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-slate-600"></i>
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Buscar folio (001)..." 
                    class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-transparent hover:border-slate-200 focus:bg-white focus:border-slate-300 focus:ring-1 focus:ring-slate-300 rounded-lg text-sm transition-all outline-none font-medium">
            </div>

            {{-- Select Estado --}}
            <div class="relative w-40">
                <select name="estado" onchange="this.form.submit()" class="w-full pl-3 pr-8 py-2 bg-slate-50 border border-transparent hover:border-slate-200 focus:bg-white focus:border-slate-300 rounded-lg text-sm outline-none appearance-none font-medium text-slate-600 cursor-pointer">
                    <option value="">Estado: Todos</option>
                    <option value="ganadores" {{ request('estado') == 'ganadores' ? 'selected' : '' }}>Ganadores</option>
                    <option value="disponible" {{ request('estado') == 'disponible' ? 'selected' : '' }}>Disponibles</option>
                    <option value="vendido" {{ request('estado') == 'vendido' ? 'selected' : '' }}>Vendidos</option>
                </select>
                <i class="ri-arrow-down-s-line absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
            </div>
        </form>

        {{-- Botones Acción --}}
        @if(request()->has('search') || request()->has('estado'))
            <a href="{{ route('admin.rifas.lotes', $rifa) }}" class="px-3 py-2 text-xs font-medium text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg transition-colors border border-rose-100">
                Limpiar
            </a>
        @endif
        
        <div class="h-6 w-px bg-slate-200 mx-1 hidden md:block"></div>
        
        <button class="px-3 py-2 text-slate-500 hover:text-slate-700 hover:bg-slate-50 rounded-lg transition-colors" title="Exportar CSV">
            <i class="ri-download-line"></i>
        </button>
    </div>

    {{-- TABLA --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-slate-50/50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">Folio</th>
                        <th class="px-6 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 text-center">Estado</th>
                        <th class="px-6 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">Detalles Premio</th>
                        <th class="px-6 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 text-center">QR</th>
                        <th class="px-6 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($boletos as $boleto)
                    <tr class="group hover:bg-slate-50 transition-colors {{ $boleto->es_ganador ? 'bg-amber-50/30' : '' }}">
                        
                        {{-- Folio --}}
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-3">
                                <span class="font-mono font-bold text-slate-700 text-sm">#{{ $boleto->folio }}</span>
                                @if($boleto->es_ganador)
                                    <span class="w-2 h-2 rounded-full bg-amber-500" title="Ganador"></span>
                                @endif
                            </div>
                        </td>

                        {{-- Estado (Estilo Dot) --}}
                        <td class="px-6 py-3 text-center">
                            @php
                                $config = match($boleto->estado) {
                                    'vendido' => ['color' => 'bg-indigo-500', 'text' => 'text-indigo-700', 'bg_pill' => 'bg-indigo-50'],
                                    'reservado' => ['color' => 'bg-orange-500', 'text' => 'text-orange-700', 'bg_pill' => 'bg-orange-50'],
                                    default => ['color' => 'bg-slate-400', 'text' => 'text-slate-600', 'bg_pill' => 'bg-slate-100'],
                                };
                            @endphp
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $config['bg_pill'] }} {{ $config['text'] }} border border-transparent">
                                <span class="w-1.5 h-1.5 rounded-full {{ $config['color'] }}"></span>
                                {{ ucfirst($boleto->estado) }}
                            </div>
                        </td>

                        {{-- Premio --}}
                        <td class="px-6 py-3">
                            @if($boleto->es_ganador)
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded bg-amber-100 flex items-center justify-center text-amber-600 text-xs">
                                        <i class="ri-trophy-fill"></i>
                                    </div>
                                    <span class="font-mono font-medium text-slate-700">${{ number_format($boleto->premio, 0) }}</span>
                                </div>
                            @else
                                <span class="text-slate-300 text-xs">—</span>
                            @endif
                        </td>

                        {{-- QR Accion --}}
                        <td class="px-6 py-3 text-center">
                            <button onclick="verQRGrande('{{ $boleto->folio }}', '{{ $boleto->codigo_qr }}', '{{ $boleto->es_ganador ? 'SI' : 'NO' }}')" 
                                class="text-slate-400 hover:text-slate-700 transition-colors p-1" title="Ver QR">
                                <i class="ri-qr-code-line text-lg"></i>
                            </button>
                        </td>
{{-- Acciones --}}
<td class="px-6 py-3 text-right">
    {{-- Usamos onclick para inyectar el color dinámicamente --}}
    <button onclick="imprimirIndividual('{{ route('admin.boletos.imprimir', $boleto->id) }}')" 
            class="inline-flex items-center gap-1.5 text-slate-400 hover:text-indigo-600 transition-colors text-xs font-bold uppercase tracking-wider group cursor-pointer">
        <i class="ri-printer-line text-sm transition-transform group-hover:scale-110"></i>
        <span>Imprimir</span>
    </button>
</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-10 h-10 bg-slate-50 rounded-full flex items-center justify-center mb-3">
                                    <i class="ri-search-line text-slate-400"></i>
                                </div>
                                <p class="text-sm font-medium text-slate-900">No se encontraron boletos</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($boletos->hasPages())
        <div class="px-6 py-3 border-t border-slate-100 bg-slate-50/50">
            {{ $boletos->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

{{-- MODAL QR (Estilo Técnico) --}}
<div id="qrModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-[2px] transition-opacity opacity-0" id="qrModalBackdrop" onclick="cerrarQrModal()"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            
            <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all scale-95 opacity-0 sm:my-8 sm:w-full sm:max-w-sm border border-slate-200" id="qrModalContent">
                
                {{-- Header --}}
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-white">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Boleto Digital</p>
                        <h3 class="text-lg font-mono font-bold text-slate-900 mt-0.5">#<span id="modalFolio">0000</span></h3>
                    </div>
                    <button onclick="cerrarQrModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-md hover:bg-slate-50 transition-colors">
                        <i class="ri-close-line text-xl"></i>
                    </button>
                </div>

                {{-- Body --}}
                <div class="px-6 py-8 flex flex-col items-center">
                    
                    {{-- Badge Ganador --}}
                    <div id="modalGanadorBadge" class="hidden mb-6 w-full bg-amber-50 border border-amber-100 rounded-lg p-3 flex items-start gap-3">
                        <i class="ri-trophy-fill text-amber-500 mt-0.5"></i>
                        <div>
                            <p class="text-xs font-bold text-amber-700">Boleto Premiado</p>
                            <p class="text-[11px] text-amber-600/80">Este folio ha sido marcado como ganador.</p>
                        </div>
                    </div>

                    {{-- QR Container --}}
                    <div class="p-3 bg-white border border-slate-200 rounded-xl shadow-sm relative">
                         <div id="qrLoader" class="absolute inset-0 flex items-center justify-center bg-white z-10 rounded-xl">
                            <div class="w-6 h-6 border-2 border-slate-800 border-t-transparent rounded-full animate-spin"></div>
                        </div>
                        <div id="modalQrContent" class="opacity-0 transition-opacity duration-300"></div>
                    </div>

                    <p class="text-xs text-slate-400 mt-4 text-center max-w-[200px]">
                        Escanea para validar autenticidad en el sistema.
                    </p>
                </div>

                {{-- Footer --}}
                <div class="bg-slate-50 px-6 py-3 border-t border-slate-100 flex justify-center">
                    <button onclick="cerrarQrModal()" class="text-xs font-medium text-slate-500 hover:text-slate-800">Cerrar ventana</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

// --- NUEVA FUNCIÓN PARA BOLETO INDIVIDUAL ---
    function imprimirIndividual(urlBase) {
        // 1. Buscamos qué color está seleccionado arriba
        // (Si no encuentra el input, usa el verde por defecto)
        let inputColor = document.getElementById('colorGlobal');
        let color = inputColor ? inputColor.value.replace('#', '') : 'd7ffc1';

        // 2. Abrimos la URL agregando el ?color=...
        window.open(urlBase + '?color=' + color, '_blank');
    }

    function verQRGrande(folio, codigoQr, esGanador) {
        const modal = document.getElementById('qrModal');
        const backdrop = document.getElementById('qrModalBackdrop');
        const content = document.getElementById('qrModalContent');
        
        modal.classList.remove('hidden');
        
        // Animación Entrada
        requestAnimationFrame(() => {
            backdrop.classList.remove('opacity-0');
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        });

        // Set Data
        document.getElementById('modalFolio').innerText = folio;

        // Ganador Logic
        const badge = document.getElementById('modalGanadorBadge');
        esGanador === 'SI' ? badge.classList.remove('hidden') : badge.classList.add('hidden');

        // Generar QR
        const container = document.getElementById('modalQrContent');
        const loader = document.getElementById('qrLoader');
        
        container.innerHTML = '';
        container.classList.add('opacity-0');
        loader.classList.remove('hidden');
        
        const img = new Image();
        img.src = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${codigoQr}&margin=0`;
        img.className = "w-48 h-48"; // Sin bordes redondeados en el QR mismo para mejor lectura
        
        img.onload = function() {
            loader.classList.add('hidden');
            container.appendChild(img);
            requestAnimationFrame(() => container.classList.remove('opacity-0'));
        };
    }

    function cerrarQrModal() {
        const modal = document.getElementById('qrModal');
        const backdrop = document.getElementById('qrModalBackdrop');
        const content = document.getElementById('qrModalContent');

        backdrop.classList.add('opacity-0');
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');

        setTimeout(() => {
            modal.classList.add('hidden');
        }, 200);
    }
</script>

@endsection