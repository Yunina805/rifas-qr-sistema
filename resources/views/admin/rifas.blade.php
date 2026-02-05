@extends('layouts.admin')

@section('title', 'Admin · Gestión de Rifas')

@section('context_title', 'Gestión de Rifas')
@section('context_subtitle', 'Crea, edita y monitorea tus sorteos')

@push('head')
<style>
    /* Transiciones suaves para el modal */
    .step-content {
        display: none;
        animation: fadeIn 0.3s ease-in-out;
    }
    .step-content.active {
        display: block;
    }
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(5px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Ocultar flechas en inputs numéricos */
    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
</style>
@endpush

<!-- Modal Nueva / Editar Rifa -->
<div id="rifaModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">

    <form
        id="rifaForm"
        method="POST"
        action="{{ route('admin.rifas.store') }}"
        class="bg-white w-full max-w-xl rounded-2xl shadow-xl p-8"
    >
        @csrf
        <input type="hidden" name="_method" value="POST">
        <input type="hidden" name="rifa_id" id="rifa_id">

        <!-- Header -->
        <div class="flex justify-between items-center mb-4">
            <h3 id="rifaModalTitle" class="text-lg font-bold text-gray-800">
                Nueva Rifa
            </h3>
            <button
                type="button"
                onclick="closeRifaModal()"
                class="text-gray-400 hover:text-gray-600"
            >
                ✕
            </button>
        </div>

        <!-- Body -->
        <div class="space-y-4">

            <input
                id="rifa_nombre"
                type="text"
                name="nombre"
                placeholder="Nombre de la rifa"
                required
                class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500"
            >

            <input
                id="rifa_sede"
                type="text"
                name="sede"
                placeholder="Sede / Lugar"
                class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500"
            >

            <input
                id="rifa_total"
                type="number"
                name="total_boletos"
                placeholder="Total de boletos"
                min="0"
                required
                class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500"
            >

            <input
                id="rifa_precio"
                type="number"
                name="precio_boleto"
                placeholder="Precio por boleto"
                step="0.01"
                min="0"
                required
                class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500"
            >

            <input
                id="rifa_costo"
                type="number"
                name="costo_boleto"
                placeholder="Costo por boleto"
                step="0.01"
                min="0"
                required
                class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500"
            >

        </div>

        <!-- Footer -->
        <div class="mt-6 flex justify-end gap-2">
            <button
                type="button"
                onclick="closeRifaModal()"
                class="px-4 py-2 rounded-lg border text-gray-600 hover:bg-gray-50"
            >
                Cancelar
            </button>

            <button
                type="submit"
                class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700"
            >
                Guardar
            </button>
        </div>

    </form>
</div>




@section('content')

<div class="space-y-8">

    {{-- Header acción --}}
    <div class="flex justify-between items-center">
        <button
            type="button"
            onclick="openRifaModal()"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg"
        >
            + Nueva Rifa
        </button>
    </div>

    {{-- Buscador --}}
    <div class="rounded-2xl bg-white p-3 shadow-sm border border-slate-200">
        <input
            type="text"
            placeholder="Buscar rifa, folio o cliente..."
            class="w-full rounded-xl bg-slate-50 py-2.5 px-4 text-sm ring-1 ring-slate-200 focus:ring-2 focus:ring-blue-500"
        >
    </div>

    {{-- Tabla --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <table class="w-full text-left text-sm">

            <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                <tr>
                    <th class="px-6 py-4">Evento</th>
                    <th class="px-6 py-4 text-center">Estado</th>
                    <th class="px-6 py-4 text-center">Inventario</th>
                    <th class="px-6 py-4 text-center">Escaneados</th>
                    <th class="px-6 py-4 text-right"></th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100">

            @forelse ($rifas as $rifa)
                <tr class="hover:bg-slate-50 transition">

                    <td class="px-6 py-4 font-semibold">
                        {{ $rifa->nombre }}
                        <div class="text-xs text-slate-400">
                            {{ $rifa->sede ?? 'Sin sede' }}
                        </div>
                    </td>

                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 rounded-full text-xs font-bold
                            @if($rifa->estado === 'activa') bg-green-100 text-green-700
                            @elseif($rifa->estado === 'finalizada') bg-red-100 text-red-700
                            @else bg-gray-100 text-gray-600
                            @endif">
                            {{ ucfirst($rifa->estado) }}
                        </span>
                    </td>

                    <td class="px-6 py-4 text-center">
                        {{ $rifa->boletos_vendidos }} / {{ $rifa->total_boletos }}
                    </td>

                    <td class="px-6 py-4 text-center text-slate-500">
                        0
                    </td>

                    <td class="px-6 py-4 text-right space-x-3">

                        <button
                            onclick="editarRifa({{ $rifa->id }})"
                            class="text-blue-600 hover:underline text-sm"
                        >
                            Editar
                        </button>

                       <td class="px-6 py-4 text-right space-x-2">

                            <a href="{{ route('admin.rifas.lotes', $rifa) }}"
                            class="text-blue-600 hover:underline text-sm font-medium">
                                Lotes
                            </a>

                            @if($rifa->estado === 'borrador')
                                <form method="POST" action="{{ route('admin.rifas.activar', $rifa) }}" class="inline">
                                    @csrf
                                    <button class="text-green-600 hover:underline text-sm font-medium">
                                        Activar
                                    </button>
                                </form>
                            @endif

                        </td>

                        @if($rifa->estado === 'activa')
                            <form method="POST" action="{{ route('admin.rifas.finalizar', $rifa) }}" class="inline">
                                @csrf
                                <button class="text-red-600 hover:underline text-sm">
                                    Finalizar
                                </button>
                            </form>
                        @endif

                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-slate-400">
                        No hay rifas creadas aún
                    </td>
                </tr>
            @endforelse

            </tbody>

        </table>
    </div>

</div>

@endsection

<script>
    // ==========================================
    // 1. LÓGICA DEL MODAL PRINCIPAL (RIFA)
    // ==========================================
    
    function openRifaModal() {
        // 1. Reseteamos el formulario a su estado original
        resetForm();
        
        // 2. Calculamos totales (si aplica)
        calculateTotals(); 

        // 3. Mostramos el modal asegurando que sea FLEX para centrarse
        const modal = document.getElementById('rifaModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex'); 
    }

    function closeRifaModal() {
        const modal = document.getElementById('rifaModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function resetForm() {
        const form = document.getElementById('rifaForm');
        if(!form) return;

        form.reset();
        
        // Regresamos la acción a "Crear" (Store)
        form.action = "{{ route('admin.rifas.store') }}";
        form.querySelector('[name=_method]').value = 'POST';
        
        // Reseteamos títulos y botones
        document.getElementById('rifaModalTitle').innerText = 'Nueva Rifa';
        
        // Si usas el wizard, regresamos al paso 1
        currentStep = 1;
        showCurrentStep();
    }

    // ==========================================
    // 2. LÓGICA DE EDICIÓN (AJAX)
    // ==========================================

    function editarRifa(id) {
        fetch(`/admin/rifas/${id}/editar`)
            .then(res => res.json())
            .then(rifa => {
                // Llenamos los campos
                document.getElementById('rifa_id').value = rifa.id;
                document.getElementById('rifa_nombre').value = rifa.nombre;
                document.getElementById('rifa_sede').value = rifa.sede;
                document.getElementById('rifa_total').value = rifa.total_boletos;
                document.getElementById('rifa_precio').value = rifa.precio_boleto;
                document.getElementById('rifa_costo').value = rifa.costo_boleto;

                // Cambiamos el formulario para "Actualizar" (PUT)
                const form = document.getElementById('rifaForm');
                form.action = `/admin/rifas/${rifa.id}`;
                form.querySelector('[name=_method]').value = 'PUT';

                document.getElementById('rifaModalTitle').innerText = 'Editar Rifa';

                // Abrimos el modal manualmente (sin resetear el form de nuevo)
                const modal = document.getElementById('rifaModal');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                
                // Recalculamos los totales con los datos cargados
                calculateTotals();
            })
            .catch(error => console.error('Error al cargar rifa:', error));
    }

    // ==========================================
    // 3. UTILIDADES DE TABLA Y SCAN
    // ==========================================

    function toggleDetails(rowId) {
        const detailRow = document.getElementById(rowId + "-details");
        if (!detailRow) return;
        detailRow.classList.toggle("hidden");
    }

    function openScanModal() {
        const modal = document.getElementById("scan-modal");
        const content = document.getElementById("scan-modal-content");
        if(!modal) return;

        modal.classList.remove("hidden");
        // Pequeño timeout para permitir que la transición CSS se note
        setTimeout(() => {
            modal.classList.remove("opacity-0");
            content.classList.remove("scale-95");
            content.classList.add("scale-100");
        }, 10);
    }

    function closeScanModal() {
        const modal = document.getElementById("scan-modal");
        const content = document.getElementById("scan-modal-content");
        if(!modal) return;

        modal.classList.add("opacity-0");
        content.classList.remove("scale-100");
        content.classList.add("scale-95");

        setTimeout(() => {
            modal.classList.add("hidden");
        }, 300);
    }

    // ==========================================
    // 4. LÓGICA DEL WIZARD (PASOS) Y CALCULOS
    // ==========================================
    
    let currentStep = 1;
    const totalSteps = 4;
    let prizes = [];

    // Variables financieras
    let precioVenta = 0;
    let costoVenta = 0;
    let totalBoletos = 0;

    // Inicializar cálculos al cargar
    document.addEventListener("DOMContentLoaded", () => {
        calculateTotals();
    });

    function showCurrentStep() {
        for (let i = 1; i <= totalSteps; i++) {
            const stepEl = document.getElementById(`step-${i}`);
            const dotEl = document.getElementById(`dot-${i}`);
            
            if (stepEl) {
                if (i === currentStep) stepEl.classList.add("active");
                else stepEl.classList.remove("active");
            }
            
            if (dotEl) {
                if (i <= currentStep) {
                    dotEl.classList.remove("bg-gray-200");
                    dotEl.classList.add("bg-blue-600");
                } else {
                    dotEl.classList.remove("bg-blue-600");
                    dotEl.classList.add("bg-gray-200");
                }
            }
        }
        
        // Control de botones Prev/Next
        const btnPrev = document.getElementById("btn-prev");
        const btnNext = document.getElementById("btn-next");
        const btnFinish = document.getElementById("btn-finish");

        if(btnPrev) btnPrev.disabled = currentStep === 1;
        
        if (currentStep === totalSteps) {
            if(btnNext) btnNext.classList.add("hidden");
            if(btnFinish) btnFinish.classList.remove("hidden");
        } else {
            if(btnNext) btnNext.classList.remove("hidden");
            if(btnFinish) btnFinish.classList.add("hidden");
        }
    }

    function changeStep(direction) {
        // Validación básica del paso 1
        if (direction === 1 && currentStep === 1) {
            const nameInput = document.getElementById("rifa_nombre"); // Corregido ID
            if (nameInput && !nameInput.value.trim()) {
                return alert("Por favor, asigna un nombre a la rifa.");
            }
        }

        currentStep += direction;
        if(currentStep < 1) currentStep = 1;
        if(currentStep > totalSteps) currentStep = totalSteps;

        showCurrentStep();
    }

    function calculateTotals() {
        // Usamos los IDs correctos de tu formulario HTML actual
        const pv = document.getElementById("rifa_precio"); // precio venta
        const cv = document.getElementById("rifa_costo");  // costo venta
        const tb = document.getElementById("rifa_total");  // total boletos

        precioVenta = pv ? parseFloat(pv.value) || 0 : 0;
        costoVenta = cv ? parseFloat(cv.value) || 0 : 0;
        totalBoletos = tb ? parseInt(tb.value) || 0 : 0;

        const precioReal = precioVenta - costoVenta;
        const ingresosReales = totalBoletos * precioReal;

        let totalPremios = 0;
        prizes.forEach(p => totalPremios += p.amount * p.winners);

        // Actualizamos textos en pantalla (si existen esos elementos)
        updateMoney("precio-real", precioReal);
        updateMoney("ingreso-ventas", totalBoletos * precioVenta);
        updateMoney("ingresos-reales", ingresosReales);
        updateMoney("fin-income", ingresosReales);
        updateMoney("fin-expenses", totalPremios);
        updateMoney("fin-profit", ingresosReales - totalPremios);
    }

    function updateMoney(id, value) {
        const el = document.getElementById(id);
        if (el) el.innerText = formatMoney(value);
    }

    function formatMoney(amount) {
        return "$" + amount.toLocaleString("en-US", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
    }
</script>

@push('scripts')



@endpush
