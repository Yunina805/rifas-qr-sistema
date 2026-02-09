@extends('layouts.admin')

@section('title', 'Admin · Gestión de Lotes')

@section('context_title')
    Lote: {{ $rifa->nombre }}
@endsection

@section('context_subtitle', 'Administra los boletos generados para este sorteo')

@section('content')

<div class="space-y-6">

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs text-slate-500 uppercase font-bold">Total Boletos</p>
            <p class="text-2xl font-bold text-slate-800">{{ $rifa->total_boletos }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs text-slate-500 uppercase font-bold">Vendidos</p>
            <p class="text-2xl font-bold text-blue-600">{{ $rifa->boletos()->where('estado', 'vendido')->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs text-slate-500 uppercase font-bold">Premios en Juego</p>
            <p class="text-2xl font-bold text-purple-600">
                {{ $rifa->boletos()->where('es_ganador', true)->count() }}
            </p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs text-slate-500 uppercase font-bold">Recaudado (Estimado)</p>
            <p class="text-2xl font-bold text-green-600">
                ${{ number_format($rifa->boletos()->where('estado', 'vendido')->count() * $rifa->precio_boleto, 2) }}
            </p>
        </div>
    </div>

    <div class="flex flex-col md:flex-row justify-between items-center gap-4 bg-white p-3 rounded-xl border border-slate-200 shadow-sm">
        <form method="GET" class="flex gap-2 w-full md:max-w-xl">
            <input type="text" name="search" value="{{ request('search') }}" 
                placeholder="Buscar por folio (ej. 0001)..." 
                class="w-full rounded-lg border-slate-200 text-sm focus:ring-blue-500">
            
            <select name="estado" class="rounded-lg border-slate-200 text-sm focus:ring-blue-500" onchange="this.form.submit()">
                <option value="">Todos los estados</option>
                <option value="ganadores" {{ request('estado') == 'ganadores' ? 'selected' : '' }}>🏆 Solo Ganadores</option>
                <option value="disponible" {{ request('estado') == 'disponible' ? 'selected' : '' }}>Disponibles</option>
                <option value="vendido" {{ request('estado') == 'vendido' ? 'selected' : '' }}>Vendidos</option>
            </select>

            <button type="submit" class="bg-slate-100 text-slate-600 px-4 py-2 rounded-lg hover:bg-slate-200 font-medium">
                Filtrar
            </button>
            
            @if(request()->has('search') || request()->has('estado'))
                <a href="{{ route('admin.rifas.lotes', $rifa) }}" class="flex items-center justify-center bg-red-50 text-red-600 px-3 rounded-lg hover:bg-red-100" title="Limpiar Filtros">
                    &times;
                </a>
            @endif
        </form>
        
        <div class="flex gap-2">
            <button class="flex items-center gap-2 bg-slate-800 text-white px-4 py-2 rounded-lg hover:bg-slate-900 text-sm font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Imprimir Todo
            </button>
            
            <a href="{{ route('admin.rifas') }}" class="text-slate-500 hover:text-slate-800 px-4 py-2 text-sm font-medium">
                &larr; Volver
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500 font-semibold">
                    <tr>
                        <th class="px-6 py-4">Folio</th>
                        <th class="px-6 py-4 text-center">QR Preview</th>
                        <th class="px-6 py-4 text-center">Estado</th>
                        <th class="px-6 py-4 text-center">Premio Asignado</th>
                        <th class="px-6 py-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($boletos as $boleto)
                    <tr class="hover:bg-slate-50 transition {{ $boleto->es_ganador ? 'bg-amber-50/30' : '' }}">
                        
                        <td class="px-6 py-3">
                            <span class="font-mono font-bold text-lg text-slate-700">#{{ $boleto->folio }}</span>
                            @if($boleto->es_ganador)
                                <div class="text-[10px] text-amber-600 font-bold uppercase tracking-wider">Premio oculto</div>
                            @endif
                        </td>

                        <td class="px-6 py-3 text-center">
                            <div class="inline-block p-1 bg-white border rounded cursor-pointer hover:scale-110 transition-transform"
                                onclick="verQRGrande('{{ $boleto->folio }}', '{{ $boleto->codigo_qr }}', '{{ $boleto->es_ganador ? 'SI' : 'NO' }}')">
                                {{ QrCode::size(40)->generate($boleto->codigo_qr) }}
                            </div>
                        </td>

                        <td class="px-6 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-bold
                                @if($boleto->estado == 'disponible') bg-slate-100 text-slate-600
                                @elseif($boleto->estado == 'vendido') bg-green-100 text-green-700
                                @else bg-orange-100 text-orange-700 @endif">
                                {{ ucfirst($boleto->estado) }}
                            </span>
                        </td>

                        <td class="px-6 py-3 text-center">
                            @if($boleto->es_ganador)
                                <span class="inline-flex items-center gap-1 text-xs font-bold text-amber-600 bg-amber-100 px-2 py-1 rounded-full border border-amber-200">
                                    🏆 ${{ number_format($boleto->premio, 0) }}
                                </span>
                            @else
                                <span class="text-slate-300">-</span>
                            @endif
                        </td>

                        <td class="px-6 py-3 text-right">
                            <button onclick="verQRGrande('{{ $boleto->folio }}', '{{ $boleto->codigo_qr }}', '{{ $boleto->es_ganador ? 'SI' : 'NO' }}')" 
                                class="text-blue-600 hover:underline text-xs font-medium border border-blue-200 px-3 py-1 rounded hover:bg-blue-50">
                                🔍 Ver / Escanear
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-slate-400">
                            No se encontraron boletos con estos filtros.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $boletos->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<div id="qrModal" class="fixed inset-0 bg-black/80 hidden items-center justify-center z-50 backdrop-blur-sm transition-opacity" onclick="cerrarQrModal()">
    <div class="bg-white rounded-2xl p-8 max-w-sm w-full text-center shadow-2xl transform transition-all scale-100" onclick="event.stopPropagation()">
        
        <div class="mb-4">
            <h3 class="text-2xl font-bold text-gray-800">Boleto #<span id="modalFolio"></span></h3>
            <p class="text-sm text-gray-500">Escanea para validar</p>
        </div>

        <div id="modalQrContent" class="flex justify-center mb-6 p-4 border-2 border-dashed border-gray-200 rounded-xl">
            </div>

        <div id="modalGanadorBadge" class="hidden mb-4 bg-amber-100 text-amber-700 px-4 py-2 rounded-lg font-bold animate-pulse">
            🏆 ¡Este boleto tiene premio!
        </div>

        <button onclick="cerrarQrModal()" class="w-full bg-slate-800 text-white py-3 rounded-xl font-bold hover:bg-slate-900 transition">
            Cerrar
        </button>
    </div>
</div>

<script>
    function verQRGrande(folio, codigoQr, esGanador) {
        // 1. Llenar datos
        document.getElementById('modalFolio').innerText = folio;
        
        // 2. Mostrar alerta visual si es ganador (Solo para admin)
        const badge = document.getElementById('modalGanadorBadge');
        if(esGanador === 'SI') badge.classList.remove('hidden');
        else badge.classList.add('hidden');

        // 3. Generar el QR grande en el modal usando una librería JS simple o HTML
        // Como ya tenemos la librería de Laravel, lo más fácil es clonar el SVG pequeño y hacerlo grande,
        // PERO para mejor calidad, usaremos una API de QR rápida o simplemente le pediremos al backend (si quisiéramos).
        // TRUCO: Laravel ya generó el SVG en el listado, pero es pequeño. 
        // Para no complicarnos con AJAX, usaremos una librería JS ligera o un servicio de imagen para el modal.
        // O MEJOR AÚN: Inyectamos un contenedor vacío y usamos una librería JS pura para redibujarlo nítido.
        
        const container = document.getElementById('modalQrContent');
        container.innerHTML = ''; // Limpiar
        
        // Generamos el QR usando una API pública rápida para el preview del modal (o qrcode.js si lo instalas)
        // Para este ejemplo rápido usaremos la API de Google Charts o similar, OJO: En producción usa qrcode.js
        // Pero para que funcione YA sin instalar JS extra:
        
        // Opción segura: Usar el SVG que ya tenemos en la fila.
        // Vamos a buscar el SVG de la fila correspondiente. Pero eso es complejo.
        
        // Solución Robusta: Usar una librería JS CDN ligera para dibujar el QR al instante
        const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${codigoQr}`;
        const img = document.createElement('img');
        img.src = qrUrl;
        img.className = "w-64 h-64 rounded-lg";
        container.appendChild(img);

        // 4. Abrir Modal
        const modal = document.getElementById('qrModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function cerrarQrModal() {
        const modal = document.getElementById('qrModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
</script>

@endsection