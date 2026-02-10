@extends('layouts.admin')

@section('title', 'Vendedores')

@section('content')
<div class="p-8">
    
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Fuerza de Ventas</h1>
            <p class="text-sm text-gray-500">Gestiona quién vende tus boletos.</p>
        </div>
        <div class="flex gap-2">
            <button onclick="document.getElementById('modalAsignar').showModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-blue-700 transition">
                <i class="ri-ticket-line"></i> Asignar Boletos
            </button>
            <button onclick="document.getElementById('modalCrear').showModal()" class="bg-slate-800 text-white px-4 py-2 rounded-lg font-bold hover:bg-slate-900 transition">
                + Nuevo Vendedor
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($vendedores as $vendedor)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 relative overflow-hidden group">
                
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-lg">
                            {{ substr($vendedor->nombre, 0, 1) }}
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800">{{ $vendedor->nombre }}</h3>
                            <p class="text-xs text-gray-400">{{ $vendedor->alias ?? 'Sin alias' }}</p>
                        </div>
                    </div>
                    <span class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded font-bold">
                        {{ $vendedor->total_asignados }} Asignados
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-2 text-center text-xs mb-4">
                    <div class="bg-green-50 rounded-lg p-2">
                        <span class="block text-green-600 font-bold text-lg">{{ $vendedor->total_vendidos }}</span>
                        <span class="text-green-800/60 font-semibold">Vendidos</span>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-2">
                        <span class="block text-blue-600 font-bold text-lg">{{ $vendedor->total_asignados - $vendedor->total_vendidos }}</span>
                        <span class="text-blue-800/60 font-semibold">Pendientes</span>
                    </div>
                </div>

                @php
                    $avance = $vendedor->total_asignados > 0 ? ($vendedor->total_vendidos / $vendedor->total_asignados) * 100 : 0;
                @endphp
                <div class="w-full bg-gray-100 rounded-full h-1.5 mb-4">
                    <div class="bg-green-500 h-1.5 rounded-full" style="width: {{ $avance }}%"></div>
                </div>

                <div class="flex justify-between items-center border-t border-gray-50 pt-3">
                    <a href="#" class="text-blue-600 text-xs font-bold hover:underline">Ver Detalle -></a>
                    {{-- <span class="text-xs font-bold text-gray-400">Debe: $???</span> --}}
                </div>
            </div>
        @endforeach
    </div>
</div>

<dialog id="modalCrear" class="modal rounded-2xl p-0 backdrop:bg-black/50">
    <div class="bg-white w-full max-w-md p-6">
        <h3 class="font-bold text-lg mb-4">Registrar Vendedor</h3>
        <form action="{{ route('admin.vendedores.store') }}" method="POST">
            @csrf
            <div class="space-y-3">
                <input type="text" name="nombre" placeholder="Nombre completo" class="w-full border-gray-300 rounded-lg" required>
                <input type="text" name="alias" placeholder="Alias (Ej. Tienda Centro)" class="w-full border-gray-300 rounded-lg">
                <input type="text" name="telefono" placeholder="Teléfono" class="w-full border-gray-300 rounded-lg">
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modalCrear').close()" class="px-4 py-2 text-gray-500 hover:bg-gray-100 rounded-lg">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-lg font-bold">Guardar</button>
            </div>
        </form>
    </div>
</dialog>

<dialog id="modalAsignar" class="modal rounded-2xl p-0 backdrop:bg-black/50">
    <div class="bg-white w-full max-w-md p-6">
        <h3 class="font-bold text-lg mb-4 text-blue-600">Asignar Boletos</h3>
        <form action="{{ route('admin.vendedores.asignar') }}" method="POST">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase">Vendedor</label>
                    <select name="vendedor_id" class="w-full border-gray-300 rounded-lg" required>
                        @foreach($vendedores as $v)
                            <option value="{{ $v->id }}">{{ $v->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase">Rifa</label>
                    <select name="rifa_id" class="w-full border-gray-300 rounded-lg" required>
                        @foreach($rifas as $r)
                            <option value="{{ $r->id }}">{{ $r->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div x-data="{ tipo: 'aleatorio' }">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Método</label>
                    <div class="flex gap-4 mb-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="tipo_asignacion" value="aleatorio" x-model="tipo" class="text-blue-600"> Aleatorio
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="tipo_asignacion" value="rango" x-model="tipo" class="text-blue-600"> Por Rango
                        </label>
                    </div>

                    <div x-show="tipo == 'aleatorio'">
                        <input type="number" name="cantidad" placeholder="¿Cuántos boletos?" class="w-full border-gray-300 rounded-lg">
                    </div>

                    <div x-show="tipo == 'rango'" class="flex gap-2">
                        <input type="text" name="folio_inicial" placeholder="Del folio..." class="w-1/2 border-gray-300 rounded-lg">
                        <input type="text" name="folio_final" placeholder="Al folio..." class="w-1/2 border-gray-300 rounded-lg">
                        <input type="hidden" name="cantidad" value="1"> </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modalAsignar').close()" class="px-4 py-2 text-gray-500 hover:bg-gray-100 rounded-lg">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-bold">Asignar</button>
            </div>
        </form>
    </div>
</dialog>

<script src="//unpkg.com/alpinejs" defer></script>
@endsection