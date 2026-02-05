@extends('layouts.admin')

@section('title', 'Admin · Dashboard')

{{-- Opcional: si quieres cambiar el texto del header del layout --}}
@section('context_title', 'Rifa en contexto')
@section('context_subtitle', 'Gran Rifa Anual · Febrero 2026')
@section('user_name', 'Admin')

@section('content')

  <div class="p-8 space-y-8">

    <section>
      <div class="flex justify-between items-end mb-4">
        <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide">Mis Rifas Activas</h3>
        <a href="{{ route('admin.rifas') }}" class="text-sm text-blue-600 hover:underline">Ver todas -></a>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

        <div class="bg-white border-2 border-blue-500 rounded-2xl p-5 shadow-lg relative cursor-pointer transform transition hover:-translate-y-1 group">
          <div class="absolute top-4 right-4 text-blue-600 bg-blue-50 p-1.5 rounded-lg">
            <i class="ri-fire-line font-bold"></i>
          </div>

          <div class="flex items-center gap-3 mb-4">
            <div class="bg-blue-100 text-blue-700 p-2 rounded-lg group-hover:bg-blue-600 group-hover:text-white transition">
              <i class="ri-ticket-2-line text-xl"></i>
            </div>
            <div>
              <h4 class="font-bold text-gray-800">Gran Rifa Anual</h4>
              <p class="text-xs text-gray-500">Sede: Plaza Central</p>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-2 text-center border-t border-dashed border-gray-200 pt-3 mt-2">
            <div>
              <span class="block text-xs text-gray-400 uppercase font-bold">Boletos</span>
              <span class="block text-sm font-bold text-gray-800">1,200</span>
            </div>
            <div>
              <span class="block text-xs text-gray-400 uppercase font-bold">Ganancia</span>
              <span class="block text-sm font-bold text-green-600">$12,500</span>
            </div>
          </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-5 hover:shadow-md cursor-pointer transition opacity-80 hover:opacity-100">
          <div class="flex items-center gap-3 mb-4">
            <div class="bg-purple-100 text-purple-700 p-2 rounded-lg">
              <i class="ri-school-line text-xl"></i>
            </div>
            <div>
              <h4 class="font-bold text-gray-800">Rifa Escolar</h4>
              <p class="text-xs text-gray-500">Sede: Escuela #4</p>
            </div>
          </div>

          <div class="mb-2 flex justify-between text-xs font-medium">
            <span class="text-gray-600">Progreso</span>
            <span class="text-purple-600">45%</span>
          </div>

          <div class="w-full bg-gray-100 rounded-full h-1.5">
            <div class="bg-purple-600 h-1.5 rounded-full" style="width: 45%"></div>
          </div>
        </div>

        <div class="border-2 border-dashed border-gray-300 rounded-2xl p-5 flex flex-col items-center justify-center text-gray-400 hover:bg-white hover:border-blue-400 hover:text-blue-500 cursor-pointer transition group h-full min-h-[140px]"
             onclick="openModal()">
          <div class="bg-gray-100 p-3 rounded-full mb-2 group-hover:bg-blue-50 transition">
            <i class="ri-add-line text-2xl"></i>
          </div>
          <span class="text-sm font-medium">Crear Nueva Rifa</span>
        </div>

      </div>
    </section>

    <section class="grid grid-cols-1 lg:grid-cols-3 gap-8">

      <div class="lg:col-span-2 space-y-6">

        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
          <div class="flex justify-between items-center mb-4">
            <h4 class="font-bold text-gray-800 flex items-center gap-2">
              <i class="ri-coupon-3-fill text-gray-400"></i> Control de Boletos
            </h4>
            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded border border-gray-200">
              Tira Total: <strong>5,000</strong>
            </span>
          </div>

          <div class="relative pt-2 pb-6">
            <div class="flex mb-2 items-center justify-between text-xs font-bold uppercase tracking-wider">
              <div class="text-blue-600">
                Vendidos <span class="text-lg block">3,450</span>
              </div>
              <div class="text-right text-gray-400">
                Disponibles <span class="text-lg block">1,550</span>
              </div>
            </div>

            <div class="overflow-hidden h-4 mb-2 text-xs flex rounded-full bg-gray-100 shadow-inner">
              <div style="width: 69%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-600 relative overflow-hidden">
                <div class="absolute inset-0 bg-white/20 w-full h-full animate-[shimmer_2s_infinite]"></div>
              </div>
            </div>
          </div>
        </div>

        {{-- NOTA: aquí en tu HTML hay un bloque duplicado (otro “Control de Boletos”).
             Yo lo dejé tal cual para no alterar tu diseño. --}}
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
          <div class="flex justify-between items-center mb-4">
            <h4 class="font-bold text-gray-800 flex items-center gap-2">
              <i class="ri-coupon-3-fill text-gray-400"></i> Control de Boletos
            </h4>
            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded border border-gray-200">
              Tira Total: <strong>500</strong>
            </span>
          </div>

          <div class="relative pt-2 pb-6">
            <div class="flex mb-2 items-center justify-between text-xs font-bold uppercase tracking-wider">
              <div class="text-blue-600">
                Vendidos <span class="text-lg block">370</span>
              </div>
              <div class="text-right text-gray-400">
                Disponibles <span class="text-lg block">130</span>
              </div>
            </div>

            <div class="overflow-hidden h-4 mb-2 text-xs flex rounded-full bg-gray-100 shadow-inner">
              <div style="width: 74%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-600 relative overflow-hidden">
                <div class="absolute inset-0 bg-white/20 w-full h-full animate-[shimmer_2s_infinite]"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

          <div class="bg-white p-5 rounded-2xl border border-green-100 shadow-sm flex flex-col justify-between relative overflow-hidden">
            <div class="absolute top-0 right-0 w-16 h-16 bg-green-50 rounded-bl-full -mr-2 -mt-2 z-0"></div>
            <div class="relative z-10">
              <p class="text-xs font-bold text-gray-400 uppercase mb-1">Ingreso de Ventas</p>
              <h3 class="text-2xl font-black text-gray-800">$18,500</h3>
              <p class="text-[10px] text-green-600 mt-1 flex items-center gap-1">
                <i class="ri-arrow-up-circle-fill"></i> +12% vs Ayer
              </p>
            </div>
          </div>

          <div class="bg-white p-5 rounded-2xl border border-red-100 shadow-sm flex flex-col justify-between relative overflow-hidden">
            <div class="relative z-10">
              <p class="text-xs font-bold text-gray-400 uppercase mb-1">Costo de Ventas</p>
              <h3 class="text-2xl font-black text-red-500">-$1,850</h3>
              <p class="text-[10px] text-gray-400 mt-1">Imprenta, Publicidad</p>
            </div>
          </div>

          <div class="bg-white p-5 rounded-2xl border border-red-100 shadow-sm flex flex-col justify-between relative overflow-hidden">
            <div class="relative z-10">
              <p class="text-xs font-bold text-gray-400 uppercase mb-1">Bolsa de Premios</p>
              <h3 class="text-2xl font-black text-red-500">-$7,000</h3>
              <p class="text-[10px] text-gray-400 mt-1">Total a entregar</p>
            </div>
          </div>

          <div class="bg-gray-900 p-5 rounded-2xl shadow-xl flex flex-col justify-between relative overflow-hidden transform hover:scale-[1.02] transition duration-300">
            <div class="absolute top-0 right-0 w-24 h-24 bg-green-500 rounded-full filter blur-2xl opacity-20 -mr-5 -mt-5"></div>
            <div class="relative z-10">
              <p class="text-xs font-bold text-gray-400 uppercase mb-1 tracking-wider">Mi Ganancia Neta</p>
              <h3 class="text-3xl font-black text-white tracking-tight">$9,650</h3>
              <div class="mt-2 w-full bg-gray-700 h-1 rounded-full overflow-hidden">
                <div class="bg-green-500 h-full" style="width: 52%"></div>
              </div>
              <p class="text-[10px] text-gray-400 mt-1 text-right">52% Margen de Utilidad</p>
            </div>
          </div>

        </div>

      </div>

      <div class="space-y-6">

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm flex flex-col h-full max-h-[420px]">
          <div class="p-4 border-b border-gray-50 flex justify-between items-center">
            <h3 class="font-bold text-gray-800 text-sm">Últimos Movimientos</h3>
            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
          </div>

          <div class="flex-1 overflow-y-auto p-4 space-y-5 custom-scrollbar">

            <div class="flex gap-3 items-start relative pb-4 border-l border-gray-100 pl-4 last:border-0">
              <div class="absolute -left-[5px] top-0 w-2.5 h-2.5 rounded-full bg-blue-500 ring-4 ring-white"></div>
              <div>
                <p class="text-xs font-bold text-gray-800">Venta Registrada (+ $250)</p>
                <p class="text-[10px] text-gray-500">5 boletos • Hace 2 min</p>
              </div>
            </div>

            <div class="flex gap-3 items-start relative pb-4 border-l border-gray-100 pl-4 last:border-0">
              <div class="absolute -left-[5px] top-0 w-2.5 h-2.5 rounded-full bg-green-500 ring-4 ring-white"></div>
              <div>
                <p class="text-xs font-bold text-gray-800">Premio Entregado</p>
                <p class="text-[10px] text-gray-500">Boleto #1024 (Lote A) • Hace 15 min</p>
              </div>
            </div>

            <div class="flex gap-3 items-start relative pb-4 border-l border-gray-100 pl-4 last:border-0">
              <div class="absolute -left-[5px] top-0 w-2.5 h-2.5 rounded-full bg-gray-300 ring-4 ring-white"></div>
              <div>
                <p class="text-xs font-medium text-gray-600">Nueva Rifa Creada</p>
                <p class="text-[10px] text-gray-500">"Sorteo Día de la Madre" • Hace 1h</p>
              </div>
            </div>

            <div class="flex gap-3 items-start relative border-l border-transparent pl-4">
              <div class="absolute -left-[5px] top-0 w-2.5 h-2.5 rounded-full bg-red-400 ring-4 ring-white"></div>
              <div>
                <p class="text-xs font-bold text-gray-800">Pago Imprenta (- $500)</p>
                <p class="text-[10px] text-gray-500">Gasto Operativo • Hace 3h</p>
              </div>
            </div>

          </div>
        </div>

        <button class="w-full bg-white border border-gray-200 text-gray-600 py-3 rounded-xl text-sm font-bold shadow-sm hover:bg-gray-50 transition flex items-center justify-center gap-2">
          <i class="ri-download-cloud-2-line"></i> Descargar Reporte Excel
        </button>

      </div>

    </section>

  </div>

@endsection

@push('scripts')
<script>
  // Placeholder para que no truene por el onclick="openModal()"
  // Luego lo conectamos a un modal real o a una ruta de "Crear Rifa".
  function openModal() {
    window.location.href = "{{ route('admin.rifas') }}";
  }
</script>
@endpush
