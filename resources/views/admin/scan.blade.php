@extends('layouts.admin')

@section('title', 'Escáner QR')

@section('content')
<div class="p-4 max-w-lg mx-auto">
    
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold text-slate-800">Escáner de Boletos</h1>
        <p class="text-sm text-slate-500">Apunta la cámara al código QR.</p>
    </div>

    {{-- CONTENEDOR DE CÁMARA --}}
    <div class="bg-black rounded-2xl overflow-hidden shadow-2xl relative border-4 border-slate-800">
        <div id="reader" class="w-full h-[350px] bg-black"></div>
        {{-- Guía visual --}}
        <div class="absolute inset-0 border-2 border-white/20 pointer-events-none"></div>
        <div class="absolute top-1/2 left-0 right-0 h-0.5 bg-red-500 shadow-[0_0_15px_rgba(239,68,68,0.8)] animate-pulse"></div>
    </div>

    {{-- BOTÓN REINTENTAR (Aparece si hay error) --}}
    <div id="retry-area" class="hidden mt-4 text-center">
        <button onclick="resetScanner()" class="bg-slate-800 text-white px-6 py-2 rounded-full font-bold shadow-md">
            <i class="ri-refresh-line"></i> Escanear otro
        </button>
    </div>

    {{-- TARJETA DE RESULTADOS --}}
    <div id="resultado" class="hidden mt-6 p-5 rounded-2xl border bg-white shadow-xl transition-all animate-fade-in-up">
        
        {{-- Info básica (Siempre visible al detectar) --}}
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center text-slate-800 border-2 border-slate-200">
                <i class="ri-coupon-3-fill text-2xl"></i>
            </div>
            <div>
                <h3 class="text-2xl font-black text-slate-900 leading-tight" id="resFolio">#0000</h3>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest" id="resRifa">Nombre de la Rifa</p>
            </div>
        </div>

        <div id="resDetalleBasico" class="space-y-2 text-sm border-t border-slate-100 pt-3">
            <p class="flex justify-between">
                <span class="text-slate-400 font-medium">Estado:</span> 
                <span id="resEstado" class="font-bold px-2 py-0.5 rounded-md">---</span>
            </p>
            <p class="flex justify-between">
                <span class="text-slate-400 font-medium">Precio:</span> 
                <span id="resPrecio" class="font-bold text-slate-800">---</span>
            </p>
        </div>

        {{-- PASO 1: Botón Validar --}}
        <div id="areaValidar" class="mt-6">
            <button onclick="revelarEstatus()" class="w-full bg-slate-900 text-white py-4 rounded-xl font-black text-lg shadow-lg hover:bg-slate-800 transition-all flex items-center justify-center gap-2 transform active:scale-95">
                <i class="ri-shield-check-line text-xl"></i>
                VALIDAR PREMIO
            </button>
        </div>

        {{-- PASO 2: Info Ganador (Oculta al inicio) --}}
        <div id="areaGanador" class="hidden mt-4 pt-4 border-t-2 border-dashed border-slate-100">
            <div id="bannerGanador" class="p-4 rounded-xl flex items-center gap-3 mb-4">
                <div id="iconGanador" class="p-2 rounded-full"></div>
                <div>
                    <p id="msgGanador" class="font-black text-sm uppercase"></p>
                    <p id="subMsgGanador" class="text-xs"></p>
                </div>
            </div>

            {{-- Botones Finales (Vender/Entregar) --}}
            <div id="accionesFinales" class="grid grid-cols-1 gap-3">
                <button id="btnVender" class="hidden w-full bg-emerald-600 text-white py-3 rounded-xl font-bold shadow-lg hover:bg-emerald-700 transition-all">
                    ✅ CONFIRMAR VENTA
                </button>
                <button id="btnEntregar" class="hidden w-full bg-indigo-600 text-white py-3 rounded-xl font-bold shadow-lg hover:bg-indigo-700 transition-all">
                    🏆 FELICIDADES
                </button>
                <button onclick="resetScanner()" class="w-full bg-slate-100 text-slate-600 py-2 rounded-xl font-bold text-xs mt-2 hover:bg-slate-200">
                    ESCANEAR SIGUIENTE
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Librería QR --}}
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

{{-- Sonidos --}}
<audio id="beep-ok" src="https://assets.mixkit.co/active_storage/sfx/2578/2578-preview.mp3"></audio>
<audio id="beep-error" src="https://assets.mixkit.co/active_storage/sfx/2572/2572-preview.mp3"></audio>

<script>
    const html5QrCode = new Html5Qrcode("reader");
    let isScanning = true;
    let scanData = null; 
    let currentCode = null;

    // Ruta dinámica según el rol para evitar errores de permisos
    const rutaValidar = "{{ Auth::user()->role === 'admin' ? route('admin.escaner.validar') : route('app.escaner.validar') }}";

    function onScanSuccess(decodedText) {
        if (!isScanning) return;
        
        isScanning = false;
        currentCode = decodedText;
        
        // Feedback visual de carga
        document.getElementById('reader').style.opacity = "0.5";

        // Paso 1: Consulta inicial al servidor
        fetch(rutaValidar, {
            method: "POST",
            headers: { 
                "Content-Type": "application/json", 
                "X-CSRF-TOKEN": "{{ csrf_token() }}" 
            },
            body: JSON.stringify({ codigo_qr: decodedText })
        })
        .then(res => {
            if (!res.ok) throw new Error("Error en la respuesta del servidor");
            return res.json();
        })
        .then(data => {
            if(data.success) {
                document.getElementById('beep-ok').play();
                prepararVistaPrevia(data);
            } else {
                mostrarError(data.message);
            }
        })
        .catch(err => {
            console.error(err);
            mostrarError("Error de conexión o permisos insuficientes.");
        });
    }

    function prepararVistaPrevia(data) {
        scanData = data; 
        const div = document.getElementById('resultado');
        const areaValidar = document.getElementById('areaValidar');
        const areaGanador = document.getElementById('areaGanador');
        const retry = document.getElementById('retry-area');

        div.classList.remove('hidden');
        retry.classList.remove('hidden');

        // Limpiamos estados visuales previos
        areaValidar.classList.add('hidden');
        areaGanador.classList.add('hidden');

        // Info básica siempre visible
        document.getElementById('resFolio').innerText = "Folio: " + data.boleto.folio;
        document.getElementById('resRifa').innerText = data.boleto.rifa_nombre;

        // --- LÓGICA DE SEGURIDAD POR ESTADO ---
        if (data.status_type === 'utilizado') {
            mostrarBannerSeguridad('BOLETO YA VENDIDO', 'Vendido anteriormente.', 'orange', 'ri-history-line');
        } 
        else if (data.status_type === 'invalido') {
            mostrarBannerSeguridad('BOLETO INVÁLIDO', 'Este código ha sido anulado.', 'red', 'ri-close-circle-line');
        } 
        else {
            // Caso Disponible: Mostrar botón para validar y vender
            areaValidar.classList.remove('hidden');
            document.getElementById('resPrecio').innerText = "$" + data.boleto.precio;
            const estado = document.getElementById('resEstado');
            estado.innerText = "DISPONIBLE";
            estado.className = "font-bold px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-700";
        }
    }

    // Paso 2: Ejecuta la venta y revela el premio al hacer clic
    async function revelarEstatus() {
        const btn = document.querySelector('#areaValidar button');
        btn.disabled = true;
        btn.innerHTML = '<i class="ri-loader-4-line animate-spin"></i> PROCESANDO...';

        const response = await fetch(rutaValidar, {
            method: "POST",
            headers: { 
                "Content-Type": "application/json", 
                "X-CSRF-TOKEN": "{{ csrf_token() }}" 
            },
            body: JSON.stringify({ 
                codigo_qr: currentCode, 
                confirmar_venta: true // Dispara el cambio a "Vendido" en el controlador
            })
        });
        
        const data = await response.json();
        
        if(data.success && data.status_type === 'revelacion') {
            scanData = data; // Actualizamos con los datos del premio
            mostrarResultadoFinal(data.boleto);
        } else {
            alert(data.message || "Error al validar el premio.");
            resetScanner();
        }
    }

    function mostrarResultadoFinal(boleto) {
        document.getElementById('areaValidar').classList.add('hidden');
        const areaGanador = document.getElementById('areaGanador');
        const banner = document.getElementById('bannerGanador');
        const iconDiv = document.getElementById('iconGanador');
        const msg = document.getElementById('msgGanador');
        const subMsg = document.getElementById('subMsgGanador');

        areaGanador.classList.remove('hidden');
        
        // Actualizar estado a VENDIDO visualmente
        const estadoLabel = document.getElementById('resEstado');
        estadoLabel.innerText = "VENDIDO";
        estadoLabel.className = "font-bold px-2 py-0.5 rounded-md bg-blue-100 text-blue-700";

        // Lógica de Ganador / No Ganador
        if (boleto.es_ganador) {
            banner.className = "p-4 rounded-xl flex items-center gap-3 mb-4 bg-amber-50 border-2 border-amber-200 animate-bounce";
            iconDiv.className = "p-2 rounded-full bg-amber-100 text-amber-600";
            iconDiv.innerHTML = '<i class="ri-trophy-fill text-2xl"></i>';
            msg.innerText = "¡GANADOR!";
            msg.className = "font-black text-sm uppercase text-amber-700";
            subMsg.innerText = "Premio: $" + boleto.premio;
            document.getElementById('btnEntregar').classList.remove('hidden');
        } else {
            banner.className = "p-4 rounded-xl flex items-center gap-3 mb-4 bg-slate-50 border border-slate-200";
            iconDiv.className = "p-2 rounded-full bg-slate-200 text-slate-500";
            iconDiv.innerHTML = '<i class="ri-emotion-normal-line text-2xl"></i>';
            msg.innerText = "Sin Premio";
            msg.className = "font-black text-sm uppercase text-slate-600";
            subMsg.innerText = "Sigue participando.";
            document.getElementById('btnEntregar').classList.add('hidden');
        }
    }

    function mostrarBannerSeguridad(titulo, sub, color, icono) {
        const areaGanador = document.getElementById('areaGanador');
        const banner = document.getElementById('bannerGanador');
        const iconDiv = document.getElementById('iconGanador');
        const msg = document.getElementById('msgGanador');
        const subMsg = document.getElementById('subMsgGanador');

        areaGanador.classList.remove('hidden');
        banner.className = `p-4 rounded-xl flex items-center gap-3 mb-4 bg-${color}-50 border border-${color}-200`;
        iconDiv.className = `p-2 rounded-full bg-${color}-100 text-${color}-600`;
        iconDiv.innerHTML = `<i class="${icono} text-2xl"></i>`;
        msg.innerText = titulo;
        msg.className = `font-black text-sm uppercase text-${color}-700`;
        subMsg.innerText = sub;
        
        document.getElementById('btnEntregar').classList.add('hidden');
        document.getElementById('btnVender').classList.add('hidden');
    }

    function resetScanner() {
        document.getElementById('resultado').classList.add('hidden');
        document.getElementById('retry-area').classList.add('hidden');
        document.getElementById('reader').style.opacity = "1";
        const btnValidar = document.querySelector('#areaValidar button');
        if(btnValidar) {
            btnValidar.disabled = false;
            btnValidar.innerHTML = '<i class="ri-shield-check-line text-xl"></i> VALIDAR PREMIO';
        }
        isScanning = true;
        scanData = null;
        currentCode = null;
    }

    function startCamera() {
        const config = { fps: 15, qrbox: { width: 250, height: 250 }, aspectRatio: 1.0 };
        html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess)
        .catch(err => html5QrCode.start({ facingMode: "user" }, config, onScanSuccess));
    }

    document.addEventListener("DOMContentLoaded", startCamera);
</script>

<style>
    .animate-fade-in-up {
        animation: fadeInUp 0.3s ease-out;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection