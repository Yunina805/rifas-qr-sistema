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
<div id="rifaModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4 transition-opacity">
    <div class="w-full max-w-lg transform transition-all"> 
        <form
            id="rifaForm"
            method="POST"
            action="{{ route('admin.rifas.store') }}"
            class="bg-white rounded-xl shadow-2xl overflow-hidden"
        >
            @csrf
            <input type="hidden" name="_method" value="">
            <input type="hidden" name="rifa_id" id="rifa_id">

            <div class="flex justify-between items-center px-6 py-4 border-b bg-gray-50">
                <h3 id="rifaModalTitle" class="text-lg font-bold text-gray-800">
                    Nueva Rifa
                </h3>
                <button type="button" onclick="closeRifaModal()" class="text-gray-400 hover:text-red-500 transition-colors text-2xl leading-none">
                    &times;
                </button>
            </div>

            <div class="p-6 space-y-4">
                
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">Nombre del Evento</label>
                    <input id="rifa_nombre" type="text" name="nombre" placeholder="Ej. Gran Rifa Navideña" required
                        class="w-full text-sm border-gray-300 rounded-lg px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                </div>
                
                <div>
                     <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">Sede / Lugar</label>
                    <input id="rifa_sede" type="text" name="sede" placeholder="Ej. Plaza Principal"
                        class="w-full text-sm border-gray-300 rounded-lg px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">Boletos</label>
                        <input id="rifa_total" type="number" name="total_boletos" placeholder="1000" min="1" required
                            class="w-full text-sm border-gray-300 rounded-lg px-3 py-1.5 focus:ring-1 focus:ring-blue-500 shadow-sm">
                    </div>
                    <div>
                         <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">Venta ($)</label>
                        <input id="rifa_precio" type="number" name="precio_boleto" placeholder="0.00" step="0.01" required
                            class="w-full text-sm border-gray-300 rounded-lg px-3 py-1.5 focus:ring-1 focus:ring-blue-500 shadow-sm">
                    </div>
                    <div>
                         <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">Costo ($)</label>
                        <input id="rifa_costo" type="number" name="costo_boleto" placeholder="0.00" step="0.01" required
                            class="w-full text-sm border-gray-300 rounded-lg px-3 py-1.5 focus:ring-1 focus:ring-blue-500 shadow-sm">
                    </div>
                </div>

                <div class="pt-2">
                    <div class="flex justify-between items-center mb-2">
                        <h4 class="text-sm font-bold text-gray-700">Premios</h4>
                        <button type="button" onclick="agregarFilaPremio()" class="text-xs text-blue-600 hover:text-blue-800 font-semibold flex items-center gap-1">
                            <span class="text-lg">+</span> Agregar
                        </button>
                    </div>
                    
                    <div class="bg-slate-50 rounded-lg p-3 border border-slate-200">
                        <div class="grid grid-cols-12 gap-2 text-[10px] font-bold text-gray-500 uppercase mb-1">
                            <div class="col-span-3">Cant.</div>
                            <div class="col-span-4">Monto ($)</div>
                            <div class="col-span-4">Descripción</div>
                            <div class="col-span-1"></div>
                        </div>
                        
                        <div id="contenedor-premios" class="space-y-2 max-h-32 overflow-y-auto pr-1">
                            </div>
                    </div>
                    <p class="mt-2 text-[10px] text-gray-400 text-center">
                        Los boletos no premiados serán perdedores automáticamente.
                    </p>
                </div>

            </div>

            <div class="px-6 py-4 bg-gray-50 border-t flex justify-end gap-3">
                <button type="button" onclick="closeRifaModal()" class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition-colors">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700 shadow-sm transition-colors">
                    Guardar Rifa
                </button>
            </div>
        </form>
    </div>
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
    document.addEventListener("DOMContentLoaded", () => {
        // ==========================================
        // 0. AUTO-RECUPERACIÓN DE ERRORES Y DATOS (MAGIA)
        // ==========================================
        // Capturamos los datos que Laravel manda tras un error
        const errores = @json($errors->all());
        const datosPrevios = @json(session()->getOldInput());

        // Si hay datos previos (significa que hubo un error y recargó)
        if (Object.keys(datosPrevios).length > 0) {
            
            // 1. Abrimos el modal automáticamente
            const modal = document.getElementById('rifaModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            // 2. Rellenamos los campos con lo que el usuario había escrito
            if(datosPrevios.nombre) document.getElementById('rifa_nombre').value = datosPrevios.nombre;
            if(datosPrevios.sede) document.getElementById('rifa_sede').value = datosPrevios.sede;
            if(datosPrevios.total_boletos) document.getElementById('rifa_total').value = datosPrevios.total_boletos;
            if(datosPrevios.precio_boleto) document.getElementById('rifa_precio').value = datosPrevios.precio_boleto;
            if(datosPrevios.costo_boleto) document.getElementById('rifa_costo').value = datosPrevios.costo_boleto;
            
            // 3. Inyectamos la alerta de error dinámicamente (ya que no está en el HTML)
            if (errores.length > 0) {
                const formBody = document.querySelector('#rifaForm .p-6'); // Buscamos el cuerpo del form
                if (formBody) {
                    let htmlErrores = `
                        <div id="error-alert" class="bg-red-50 border-l-4 border-red-500 p-4 mb-4 rounded shadow-sm animate-pulse">
                            <div class="flex">
                                <div class="ml-3">
                                    <h3 class="text-sm font-bold text-red-800">No pudimos guardar la rifa</h3>
                                    <ul class="mt-1 list-disc list-inside text-sm text-red-700">
                                        ${errores.map(e => `<li>${e}</li>`).join('')}
                                    </ul>
                                </div>
                            </div>
                        </div>`;
                    // Insertamos el error al principio del formulario
                    formBody.insertAdjacentHTML('afterbegin', htmlErrores);
                }
            }
            
            // Recalculamos los totales con los datos recuperados
            calculateTotals();
        }

        // ==========================================
        // INICIALIZACIÓN NORMAL
        // ==========================================
        calculateTotals();
        showCurrentStep();

        // Listeners para recálculo en tiempo real
        const inputsCalculo = ['rifa_precio', 'rifa_costo', 'rifa_total'];
        inputsCalculo.forEach(id => {
            const el = document.getElementById(id);
            if(el) el.addEventListener('input', calculateTotals);
        });
    });

    // ==========================================
    // 1. VARIABLES GLOBALES
    // ==========================================
    let premioIndex = 0;
    let currentStep = 1;
    const totalSteps = 4;

    // ==========================================
    // 2. GESTIÓN DE PREMIOS (CORREGIDO)
    // ==========================================
    function agregarFilaPremio() {
        const contenedor = document.getElementById('contenedor-premios');
        const rowId = `premio-row-${premioIndex}`;
        
        // CAMBIO IMPORTANTE: Quité el 'required' de los inputs generados.
        // Esto permite enviar el formulario aunque la fila esté vacía (el controlador la ignorará).
        const html = `
            <div id="${rowId}" class="grid grid-cols-12 gap-2 items-center mb-2 prize-row transition-all duration-300">
                <div class="col-span-3">
                    <input type="number" name="premios[${premioIndex}][cantidad]" min="1" placeholder="1"
                        oninput="calculateTotals()"
                        class="prize-qty w-full text-sm border rounded px-2 py-1 focus:ring-1 focus:ring-blue-500">
                </div>
                <div class="col-span-4">
                    <input type="number" name="premios[${premioIndex}][monto]" min="0" step="0.01" placeholder="$"
                        oninput="calculateTotals()"
                        class="prize-amount w-full text-sm border rounded px-2 py-1 focus:ring-1 focus:ring-blue-500">
                </div>
                <div class="col-span-4">
                    <input type="text" name="premios[${premioIndex}][descripcion]" placeholder="Descripción"
                        class="w-full text-sm border rounded px-2 py-1 focus:ring-1 focus:ring-blue-500">
                </div>
                <div class="col-span-1 text-center">
                    <button type="button" onclick="eliminarFila('${rowId}')" class="text-red-400 hover:text-red-600 font-bold transition-colors">
                        &times;
                    </button>
                </div>
            </div>
        `;        
        
        contenedor.insertAdjacentHTML('beforeend', html);
        premioIndex++;
        calculateTotals();
    }

    function eliminarFila(rowId) {
        const fila = document.getElementById(rowId);
        if(fila) {
            fila.remove();
            calculateTotals();
        }
    }

    // ==========================================
    // 3. LÓGICA DEL MODAL PRINCIPAL
    // ==========================================
    function openRifaModal() {
        // Limpiamos alertas de error previas si existen
        const errorAlert = document.getElementById('error-alert');
        if(errorAlert) errorAlert.remove();

        resetForm(); 
        const modal = document.getElementById('rifaModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        calculateTotals();
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
        form.action = "{{ route('admin.rifas.store') }}";
        
        const methodField = form.querySelector('[name=_method]');
        if(methodField) methodField.value = '';
        
        document.getElementById('rifaModalTitle').innerText = 'Nueva Rifa';
        
        currentStep = 1;
        showCurrentStep();

        // Reiniciar Premios: Agregamos una fila vacía PERO como ya no es 'required', no bloqueará nada
        document.getElementById('contenedor-premios').innerHTML = '';
        premioIndex = 0;
        agregarFilaPremio(); 
    }

    // ==========================================
    // 4. EDICIÓN (AJAX)
    // ==========================================
    function editarRifa(id) {
        // Limpiamos errores previos
        const errorAlert = document.getElementById('error-alert');
        if(errorAlert) errorAlert.remove();

        fetch(`/admin/rifas/${id}/editar`)
            .then(res => res.json())
            .then(rifa => {
                const modal = document.getElementById('rifaModal');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.getElementById('rifaModalTitle').innerText = 'Editar Rifa';

                document.getElementById('rifa_id').value = rifa.id;
                document.getElementById('rifa_nombre').value = rifa.nombre;
                document.getElementById('rifa_sede').value = rifa.sede;
                document.getElementById('rifa_total').value = rifa.total_boletos;
                document.getElementById('rifa_precio').value = rifa.precio_boleto;
                document.getElementById('rifa_costo').value = rifa.costo_boleto;

                const form = document.getElementById('rifaForm');
                form.action = `/admin/rifas/${rifa.id}`;
                
                let methodField = form.querySelector('[name=_method]');
                if(!methodField) {
                    methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    form.appendChild(methodField);
                }
                methodField.value = 'PUT';

                currentStep = 1;
                showCurrentStep();

                document.getElementById('contenedor-premios').innerHTML = '';
                premioIndex = 0;
                
                // Si el backend devolviera premios, aquí los cargaríamos
                // Por ahora ponemos una vacía
                if (!rifa.premios || rifa.premios.length === 0) {
                    agregarFilaPremio();
                }

                calculateTotals();
            })
            .catch(error => console.error('Error al cargar rifa:', error));
    }

    // ==========================================
    // 5. WIZARD (PASOS)
    // ==========================================
    function showCurrentStep() {
        for (let i = 1; i <= totalSteps; i++) {
            const stepEl = document.getElementById(`step-${i}`);
            // ... (resto de tu lógica de wizard original se mantiene igual si la tienes en HTML) ...
            // Como tu HTML actual no parece tener divs con id="step-1", etc., 
            // esta función es segura de dejar pero quizás no haga nada visual en tu modal actual
            // ya que tu modal es de una sola página.
        }
    }

    function changeStep(direction) {
        // Lógica placeholder por si decides usar wizard en el futuro
        currentStep += direction;
    }

    // ==========================================
    // 6. CÁLCULOS FINANCIEROS
    // ==========================================
    function calculateTotals() {
        const pv = parseFloat(document.getElementById("rifa_precio")?.value) || 0;
        const cv = parseFloat(document.getElementById("rifa_costo")?.value) || 0;
        const tb = parseInt(document.getElementById("rifa_total")?.value) || 0;

        const precioReal = pv - cv;
        const ingresosBrutos = tb * pv;
        const ingresosReales = tb * precioReal;

        let totalPremios = 0;
        const filas = document.querySelectorAll('#contenedor-premios .prize-row');
        
        filas.forEach(row => {
            const qtyInput = row.querySelector('.prize-qty');
            const amtInput = row.querySelector('.prize-amount');
            
            const cantidad = parseFloat(qtyInput?.value) || 0;
            const monto = parseFloat(amtInput?.value) || 0;
            
            totalPremios += (cantidad * monto);
        });

        // Solo actualizamos si existen los elementos en el DOM (para evitar errores)
        updateMoney("precio-real", precioReal);
        updateMoney("ingreso-ventas", ingresosBrutos);
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

    // ==========================================
    // 7. UTILIDADES
    // ==========================================
    function toggleDetails(rowId) {
        const detailRow = document.getElementById(rowId + "-details");
        if (detailRow) detailRow.classList.toggle("hidden");
    }
</script>

@push('scripts')



@endpush
