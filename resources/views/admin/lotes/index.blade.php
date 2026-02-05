@extends('admin.layouts.app')

@section('title', 'Lotes · ' . $rifa->nombre)

@section('content')

<div class="space-y-6">

    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-xl font-bold text-slate-800">
                Lotes – {{ $rifa->nombre }}
            </h1>
            <p class="text-sm text-slate-500">
                Total boletos: {{ $rifa->total_boletos }}
            </p>
        </div>

        <button
            onclick="document.getElementById('modalLote').classList.remove('hidden')"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
            + Nuevo Lote
        </button>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 uppercase text-xs">
                <tr>
                    <th class="px-6 py-3">Lote</th>
                    <th class="px-6 py-3">Folios</th>
                    <th class="px-6 py-3 text-center">Cantidad</th>
                    <th class="px-6 py-3 text-center">Estado</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @forelse($lotes as $lote)
                <tr>
                    <td class="px-6 py-4 font-medium">{{ $lote->nombre }}</td>
                    <td class="px-6 py-4">
                        {{ $lote->folio_inicio }} – {{ $lote->folio_fin }}
                    </td>
                    <td class="px-6 py-4 text-center">{{ $lote->cantidad }}</td>
                    <td class="px-6 py-4 text-center text-slate-500">
                        {{ ucfirst($lote->estado) }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-6 text-center text-slate-400">
                        No hay lotes creados
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL --}}
<div id="modalLote" class="fixed inset-0 bg-black/40 hidden flex items-center justify-center z-50">
    <form method="POST"
          action="{{ route('admin.rifas.lotes.store', $rifa) }}"
          class="bg-white p-6 rounded-xl w-full max-w-md space-y-4">
        @csrf

        <h3 class="text-lg font-bold">Nuevo Lote</h3>

        <input name="nombre" placeholder="Nombre del lote" required
            class="w-full border rounded-lg px-3 py-2">

        <input name="folio_inicio" type="number" placeholder="Folio inicio" required
            class="w-full border rounded-lg px-3 py-2">

        <input name="folio_fin" type="number" placeholder="Folio fin" required
            class="w-full border rounded-lg px-3 py-2">

        <div class="flex justify-end gap-2">
            <button type="button"
                onclick="document.getElementById('modalLote').classList.add('hidden')"
                class="px-4 py-2 border rounded-lg">
                Cancelar
            </button>

            <button class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                Crear
            </button>
        </div>
    </form>
</div>

@endsection
