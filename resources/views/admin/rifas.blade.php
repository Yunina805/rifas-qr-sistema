@extends('layouts.admin')

@section('title', 'Admin · Gestión de Rifas')

@section('context_title', 'Rifas')
@section('context_subtitle', 'Listado y configuración de eventos')

@push('head')
<style>
    /* Animación sutil para el modal */
    #rifaModal { transition: opacity 0.2s ease-in-out; }
    #rifaModal.hidden { opacity: 0; pointer-events: none; }
    #rifaModal:not(.hidden) { opacity: 1; pointer-events: auto; }
    
    #rifaModalContent { transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1); transform: scale(0.95); opacity: 0; }
    #rifaModal:not(.hidden) #rifaModalContent { transform: scale(1); opacity: 1; }

    /* Eliminar flechas de number inputs */
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
</style>
@endpush

@section('content')

<div class="space-y-6">

    {{-- HEADER DE LA SECCIÓN --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <span>Gestión</span>
                <i class="ri-arrow-right-s-line"></i>
                <span class="font-medium text-slate-700">Eventos</span>
            </div>
            <h1 class="font-display font-bold text-xl text-slate-900 tracking-tight">Rifas y Sorteos</h1>
        </div>
        
        <div class="flex gap-3">
            <button class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors shadow-sm">
                <i class="ri-filter-3-line mr-1"></i> Filtros
            </button>

            <button onclick="openRifaModal()" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-sm font-medium transition-all shadow-sm flex items-center gap-2">
                <i class="ri-add-line"></i>
                <span>Crear Rifa</span>
            </button>
        </div>
    </div>

    {{-- TABLA DE DATOS --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        
        {{-- Toolbar de la tabla --}}
        <div class="p-4 border-b border-slate-100 flex gap-4">
            <div class="relative flex-1 max-w-sm">
                <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" 
                       placeholder="Buscar por nombre, sede..." 
                       class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-slate-400 focus:ring-1 focus:ring-slate-400 transition-all placeholder:text-slate-400 text-slate-700">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-200">
                        <th class="px-6 py-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Detalle del Evento</th>
                        <th class="px-6 py-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Progreso</th>
                        <th class="px-6 py-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider text-right">Métricas</th>
                        <th class="px-6 py-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider text-right"></th>
                    </tr>
                </thead>
            <tbody class="divide-y divide-slate-100">
            @forelse ($rifas as $rifa)
                <tr class="group hover:bg-slate-50/80 transition-colors">
                    
                    {{-- 1. DETALLE DEL EVENTO (+ FECHA SORTEO) --}}
                    <td class="px-6 py-4">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded bg-slate-100 flex items-center justify-center text-slate-500 border border-slate-200 mt-0.5">
                                <span class="font-bold text-xs">#{{ $rifa->id }}</span>
                            </div>
                            <div>
                                <p class="font-bold text-slate-800 group-hover:text-indigo-600 transition-colors">{{ $rifa->nombre }}</p>
                                
                                {{-- Ubicación y Precio --}}
                                <div class="flex items-center gap-2 mt-1">
                                    <p class="text-xs text-slate-500 flex items-center gap-1">
                                        <i class="ri-map-pin-line text-slate-400"></i> {{ $rifa->sede ?? 'Virtual' }}
                                    </p>
                                    <span class="text-slate-300">•</span>
                                    <p class="text-xs text-slate-500 font-mono">${{ number_format($rifa->precio_boleto, 2) }}</p>
                                </div>

                                {{-- CORREGIDO: Muestra la Fecha del Sorteo --}}
                                <p class="text-[10px] text-slate-400 mt-1 flex items-center gap-1">
                                    <i class="ri-calendar-event-line"></i> 
                                    @if($rifa->fecha_sorteo)
                                        {{-- Usamos Carbon::parse por si el modelo no tiene el cast --}}
                                        Sorteo: {{ \Carbon\Carbon::parse($rifa->fecha_sorteo)->format('d/m/Y h:i A') }}
                                    @else
                                        Sorteo: <span class="italic text-slate-300">Por definir</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </td>

                    {{-- 2. ESTADO --}}
                    <td class="px-6 py-4">
                        @php
                            $statusConfig = match($rifa->estado) {
                                'activa' => ['bg' => 'bg-emerald-500', 'text' => 'text-emerald-700', 'label' => 'Activa', 'border' => 'border-emerald-200', 'bg_pill' => 'bg-emerald-50'],
                                'finalizada' => ['bg' => 'bg-slate-500', 'text' => 'text-slate-600', 'label' => 'Finalizada', 'border' => 'border-slate-200', 'bg_pill' => 'bg-slate-50'],
                                default => ['bg' => 'bg-amber-500', 'text' => 'text-amber-700', 'label' => 'Borrador', 'border' => 'border-amber-200', 'bg_pill' => 'bg-amber-50'],
                            };
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border {{ $statusConfig['border'] }} {{ $statusConfig['bg_pill'] }} {{ $statusConfig['text'] }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $statusConfig['bg'] }}"></span>
                            {{ $statusConfig['label'] }}
                        </span>
                    </td>

                    {{-- 3. PROGRESO (CÁLCULO REAL) --}}
                    <td class="px-6 py-4 w-48">
                        @php
                            $vendidosReales = $rifa->boletos()->where('estado', 'vendido')->count();
                            $percent = $rifa->total_boletos > 0 ? ($vendidosReales / $rifa->total_boletos) * 100 : 0;
                        @endphp

                        <div class="flex justify-between text-xs mb-1.5">
                            <span class="font-medium text-slate-700">{{ number_format($vendidosReales) }} <span class="text-slate-400 font-normal">/ {{ number_format($rifa->total_boletos) }}</span></span>
                            <span class="text-slate-500">{{ round($percent) }}%</span>
                        </div>
                        <div class="h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-slate-900 rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                        </div>
                    </td>

                    {{-- 4. MÉTRICAS (DINERO REAL) --}}
                    <td class="px-6 py-4 text-right">
                        <p class="text-xs font-bold text-slate-700">
                            ${{ number_format($vendidosReales * $rifa->precio_boleto) }}
                        </p>
                        <p class="text-[10px] text-slate-400 uppercase tracking-wide">Recaudado</p>
                    </td>

                    {{-- 5. ACCIONES (+ BOTÓN FINALIZAR) --}}
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            
                            {{-- Editar --}}
                            <button onclick="editarRifa({{ $rifa->id }})" class="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-md transition-colors" title="Editar">
                                <i class="ri-settings-3-line text-lg"></i>
                            </button>
                            
                            {{-- Ver Boletos --}}
                            <a href="{{ route('admin.rifas.lotes', $rifa) }}" class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-md transition-colors" title="Ver Boletos">
                                <i class="ri-coupon-line text-lg"></i>
                            </a>

                            {{-- Botón Finalizar Rifa --}}
                            @if($rifa->estado === 'activa')
                                <form action="{{ route('admin.rifas.finalizar', $rifa) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas FINALIZAR esta rifa? Ya no se podrán vender más boletos.');" class="inline">
                                    @csrf
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-md transition-colors" title="Finalizar Rifa">
                                        <i class="ri-stop-circle-line text-lg"></i>
                                    </button>
                                </form>
                            @endif

                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mb-3">
                                <i class="ri-inbox-line text-xl text-slate-400"></i>
                            </div>
                            <p class="text-sm font-medium text-slate-900">No hay rifas registradas</p>
                            <p class="text-xs text-slate-500 mt-1">Crea una nueva rifa para comenzar a vender.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
            </table>
        </div>
        
        {{-- Paginación (Placeholder) --}}
        <div class="px-6 py-3 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between text-xs text-slate-500">
            <span>Mostrando {{ $rifas->count() }} resultados</span>
            <div class="flex gap-2">
                <button class="hover:text-slate-800 disabled:opacity-50" disabled>Anterior</button>
                <button class="hover:text-slate-800 disabled:opacity-50" disabled>Siguiente</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL REFINADO --}}
<div id="rifaModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-[2px] transition-opacity" onclick="closeRifaModal()"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            
            {{-- Content --}}
            <div id="rifaModalContent" class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200">
                
                <form id="rifaForm" method="POST" action="{{ route('admin.rifas.store') }}">
                    @csrf
                    <input type="hidden" name="_method" value="">
                    <input type="hidden" name="rifa_id" id="rifa_id">

                    {{-- Header Limpio --}}
                    <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-white">
                        <div>
                            <h3 id="rifaModalTitle" class="text-base font-bold text-slate-900 leading-none">Nueva Rifa</h3>
                            <p class="text-xs text-slate-500 mt-1">Ingresa los detalles básicos del sorteo.</p>
                        </div>
                        <button type="button" onclick="closeRifaModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-md hover:bg-slate-100 transition-colors">
                            <i class="ri-close-line text-xl"></i>
                        </button>
                    </div>

                    {{-- Body --}}
                    <div class="p-6 space-y-5">
                        
                        {{-- Grupo 1: Info General --}}
                        <div class="space-y-4">
                            
                            {{-- Nombre --}}
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nombre del Evento</label>
                                <input type="text" name="nombre" id="rifa_nombre" placeholder="Ej. Gran Sorteo Anual" required
                                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-slate-400 focus:ring-1 focus:ring-slate-400 focus:bg-white transition-all text-slate-800 placeholder:text-slate-400">
                            </div>

                            {{-- Grid para Sede y Fecha --}}
                            <div class="grid grid-cols-2 gap-4">
                                
                                {{-- Sede --}}
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Sede / Ubicación</label>
                                    <div class="relative">
                                        <i class="ri-map-pin-line absolute left-3 top-2 text-slate-400"></i>
                                        <input type="text" name="sede" id="rifa_sede" placeholder="Ej. Auditorio"
                                            class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-slate-400 focus:ring-1 focus:ring-slate-400 focus:bg-white transition-all text-slate-800 placeholder:text-slate-400">
                                    </div>
                                </div>

                                {{-- NUEVO: Fecha y Hora del Sorteo --}}
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Fecha del Sorteo</label>
                                    <div class="relative">
                                        <i class="ri-calendar-event-line absolute left-3 top-2 text-slate-400"></i>
                                        <input type="datetime-local" name="fecha_sorteo" id="rifa_fecha" required
                                            class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-slate-400 focus:ring-1 focus:ring-slate-400 focus:bg-white transition-all text-slate-800 placeholder:text-slate-400 font-medium">
                                    </div>
                                </div>

                            </div>
                        </div>

                        <hr class="border-slate-100">

                        {{-- Grupo 2: Datos Económicos --}}
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Boletos Totales</label>
                                <input type="number" name="total_boletos" id="rifa_total" placeholder="1000" min="1" required
                                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-slate-400 focus:ring-1 focus:ring-slate-400 focus:bg-white transition-all font-mono text-slate-800">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Precio Venta</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-slate-400 text-xs">$</span>
                                    <input type="number" name="precio_boleto" id="rifa_precio" placeholder="0.00" step="0.01" required
                                           class="w-full pl-6 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-slate-400 focus:ring-1 focus:ring-slate-400 focus:bg-white transition-all font-mono text-slate-800">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Costo Unit.</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-slate-400 text-xs">$</span>
                                    <input type="number" name="costo_boleto" id="rifa_costo" placeholder="0.00" step="0.01" required
                                           class="w-full pl-6 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-slate-400 focus:ring-1 focus:ring-slate-400 focus:bg-white transition-all font-mono text-slate-800">
                                </div>
                            </div>
                        </div>

                        {{-- Sección Premios (Lista simple) --}}
                        <div class="bg-slate-50 rounded-lg border border-slate-200 p-4">
                            <div class="flex justify-between items-center mb-3">
                                <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wide">Premios</h4>
                                <button type="button" onclick="agregarFilaPremio()" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1 hover:underline">
                                    <i class="ri-add-line"></i> Agregar
                                </button>
                            </div>
                            
                            {{-- Contenedor de premios --}}
                            <div id="contenedor-premios" class="space-y-2 max-h-32 overflow-y-auto custom-scrollbar">
                                <div id="empty-prizes-msg" class="text-center py-2 border border-dashed border-slate-200 rounded-md">
                                    <p class="text-xs text-slate-400">Sin premios configurados</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3 rounded-b-xl">
                        <button type="button" onclick="closeRifaModal()" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800 hover:bg-white hover:shadow-sm border border-transparent hover:border-slate-200 rounded-lg transition-all">
                            Cancelar
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-bold text-white bg-slate-900 hover:bg-slate-800 rounded-lg shadow-sm transition-all flex items-center gap-2">
                            <span>Guardar Cambios</span>
                            <i class="ri-arrow-right-line"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

<script>
    document.addEventListener("DOMContentLoaded", () => {
        // ==========================================
        // 0. AUTO-RECUPERACIÓN DE ERRORES (Laravel)
        // ==========================================
        const errores = @json($errors->all());
        const datosPrevios = @json(session()->getOldInput());

        // Si hay datos previos (hubo error de validación)
        if (Object.keys(datosPrevios).length > 0) {
            openRifaModal();

            // Restaurar valores básicos
            const map = {
                'nombre': 'rifa_nombre',
                'sede': 'rifa_sede',
                'fecha_sorteo': 'rifa_fecha', // <--- AGREGADO: Para recuperar la fecha
                'total_boletos': 'rifa_total',
                'precio_boleto': 'rifa_precio',
                'costo_boleto': 'rifa_costo'
            };

            for (const [key, id] of Object.entries(map)) {
                if (datosPrevios[key]) {
                    const input = document.getElementById(id);
                    if(input) input.value = datosPrevios[key];
                }
            }

            // Mostrar Errores de forma elegante
            if (errores.length > 0) {
                const modalBody = document.querySelector('#rifaForm .space-y-5'); // Buscar el contenedor principal
                if (modalBody) {
                    let htmlErrores = `
                        <div id="error-alert" class="bg-red-50 border border-red-100 rounded-lg p-3 mb-4 animate-fade-in-down">
                            <div class="flex items-start gap-3">
                                <i class="ri-error-warning-fill text-red-500 mt-0.5"></i>
                                <div>
                                    <h3 class="text-xs font-bold text-red-700 uppercase">Atención</h3>
                                    <ul class="mt-1 text-xs text-red-600 list-disc list-inside">
                                        ${errores.map(e => `<li>${e}</li>`).join('')}
                                    </ul>
                                </div>
                            </div>
                        </div>`;
                    modalBody.insertAdjacentHTML('afterbegin', htmlErrores);
                    modalBody.scrollTop = 0;
                }
            }
        }
    });

    // ==========================================
    // 1. GESTIÓN DEL MODAL (Animaciones Suaves)
    // ==========================================
    function openRifaModal() {
        const modal = document.getElementById('rifaModal');
        // Quitar alerta de errores previos si existe
        const errorAlert = document.getElementById('error-alert');
        if(errorAlert) errorAlert.remove();

        modal.classList.remove('hidden');
        
        // Pequeño delay para permitir que el navegador renderice antes de animar
        setTimeout(() => {
            const content = document.getElementById('rifaModalContent');
            content.style.transform = 'scale(1)';
            content.style.opacity = '1';
        }, 10);
    }

    function closeRifaModal() {
        const modal = document.getElementById('rifaModal');
        const content = document.getElementById('rifaModalContent');
        
        // Animación de salida
        content.style.transform = 'scale(0.95)';
        content.style.opacity = '0';
        
        setTimeout(() => {
            modal.classList.add('hidden');
            resetForm(); // Limpiar formulario al cerrar completamente
        }, 200); // 200ms coincide con el CSS transition
    }

    function resetForm() {
        const form = document.getElementById('rifaForm');
        if(!form) return;
        
        form.reset();
        form.action = "{{ route('admin.rifas.store') }}"; // Ruta por defecto (Crear)
        
        // Limpiar método oculto (para que sea POST por defecto)
        const methodField = form.querySelector('input[name="_method"]');
        if(methodField) methodField.value = '';

        // Título original
        document.getElementById('rifaModalTitle').innerText = 'Nueva Rifa';
        
        // Reiniciar premios
        document.getElementById('contenedor-premios').innerHTML = 
            '<div id="empty-prizes-msg" class="text-center py-2 border border-dashed border-slate-200 rounded-md"><p class="text-xs text-slate-400">Sin premios configurados</p></div>';
    }

    // ==========================================
    // 2. GESTIÓN DE PREMIOS (CORREGIDO)
    // ==========================================
    function agregarFilaPremio(data = null) {
        const contenedor = document.getElementById('contenedor-premios');
        const emptyMsg = document.getElementById('empty-prizes-msg');
        if(emptyMsg) emptyMsg.style.display = 'none';

        // Generamos un ID único para que Laravel agrupe los datos
        const index = Date.now() + Math.floor(Math.random() * 1000);

        // Valores por defecto
        const cantidad = data ? data.cantidad : '';
        const valor = data ? (data.valor || data.monto) : ''; 
        const desc = data ? data.descripcion : '';

        // Creamos la fila
        const row = document.createElement('div');
        row.className = 'grid grid-cols-12 gap-2 items-center animate-fade-in-up mb-2';
        
        row.innerHTML = `
            <div class="col-span-2">
                <input type="number" name="premios[${index}][cantidad]" value="${cantidad}" placeholder="1" 
                    class="w-full bg-white border border-slate-200 rounded text-xs px-2 py-1.5 focus:border-slate-400 focus:ring-1 focus:ring-slate-400 outline-none transition-all text-center">
            </div>
            <div class="col-span-3 relative">
                <span class="absolute left-2 top-1.5 text-xs text-slate-400">$</span>
                <input type="number" name="premios[${index}][monto]" value="${valor}" placeholder="0.00" 
                    class="w-full bg-white border border-slate-200 rounded text-xs pl-5 pr-2 py-1.5 focus:border-slate-400 focus:ring-1 focus:ring-slate-400 outline-none transition-all">
            </div>
            <div class="col-span-6">
                <input type="text" name="premios[${index}][descripcion]" value="${desc}" placeholder="Descripción (Opcional)" 
                    class="w-full bg-white border border-slate-200 rounded text-xs px-2 py-1.5 focus:border-slate-400 focus:ring-1 focus:ring-slate-400 outline-none transition-all">
            </div>
            <div class="col-span-1 text-center">
                <button type="button" onclick="eliminarFila(this)" 
                    class="text-slate-400 hover:text-red-500 hover:bg-red-50 p-1 rounded transition-colors">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </div>
        `;
        contenedor.appendChild(row);
    }

    function eliminarFila(btn) {
        btn.closest('.grid').remove();
        const contenedor = document.getElementById('contenedor-premios');
        // Si no quedan hijos (o solo quedan mensajes ocultos), mostrar mensaje vacío
        if(contenedor.querySelectorAll('.grid').length === 0) {
            const emptyMsg = document.getElementById('empty-prizes-msg');
            if(emptyMsg) emptyMsg.style.display = 'block';
        }
    }

    // ==========================================
    // 3. EDICIÓN (AJAX)
    // ==========================================
    async function editarRifa(id) {
        try {
            // UI Feedback visual inmediato
            document.body.style.cursor = 'wait';
            
            const response = await fetch(`/admin/rifas/${id}/editar`);
            if (!response.ok) throw new Error('Error de red');
            
            const rifa = await response.json();

            // 1. Abrir Modal
            openRifaModal();
            document.getElementById('rifaModalTitle').innerText = 'Editar Rifa #' + rifa.id;

            // 2. Llenar Campos
            document.getElementById('rifa_id').value = rifa.id;
            document.getElementById('rifa_nombre').value = rifa.nombre;
            document.getElementById('rifa_sede').value = rifa.sede;

            // --- LÓGICA DE FECHA AGREGADA ---
            // Convertimos "2026-02-10 12:00:00" a "2026-02-10T12:00" para el input datetime-local
            if(rifa.fecha_sorteo) {
                document.getElementById('rifa_fecha').value = rifa.fecha_sorteo.replace(' ', 'T').substring(0, 16);
            } else {
                document.getElementById('rifa_fecha').value = '';
            }

            document.getElementById('rifa_total').value = rifa.total_boletos;
            document.getElementById('rifa_precio').value = rifa.precio_boleto;
            document.getElementById('rifa_costo').value = rifa.costo_boleto;

            // 3. Configurar Formulario para PUT
            const form = document.getElementById('rifaForm');
            form.action = `/admin/rifas/${rifa.id}`;
            let methodInput = form.querySelector('input[name="_method"]');
            if (!methodInput) {
                methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                form.appendChild(methodInput);
            }
            methodInput.value = 'PUT';

            // 4. Llenar Premios
            const contenedor = document.getElementById('contenedor-premios');
            // Limpiar filas existentes (excepto el mensaje de vacío)
            contenedor.querySelectorAll('.grid').forEach(e => e.remove());
            
            if (rifa.premios && rifa.premios.length > 0) {
                rifa.premios.forEach(premio => agregarFilaPremio(premio));
            } else {
                document.getElementById('empty-prizes-msg').style.display = 'block';
            }

        } catch (error) {
            console.error(error);
            alert('No se pudo cargar la información de la rifa.');
        } finally {
            document.body.style.cursor = 'default';
        }
    }
</script>

@push('scripts')



@endpush
