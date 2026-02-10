@extends('layouts.admin')

@section('title', 'Terminal de Escaneo')

@section('content')

<div class="max-w-md mx-auto space-y-6">

    <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
        <div class="flex justify-between items-center mb-2">
            <h2 class="font-bold text-slate-800">Escáner QR</h2>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-slate-500 uppercase">Modo Venta</span>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="mode-switch" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                </label>
            </div>
        </div>
        <p id="mode-text" class="text-xs text-slate-500">
            Modo Consulta: Solo verifica el estado del boleto.
        </p>
    </div>

    <div class="bg-black rounded-3xl overflow-hidden shadow-2xl relative aspect-square group">
        <div id="reader" class="w-full h-full object-cover"></div>
        
        <div id="scan-frame" class="absolute inset-0 border-4 border-blue-500/50 m-8 rounded-xl pointer-events-none transition-colors duration-300"></div>

        <div id="sale-indicator" class="hidden absolute top-4 left-0 right-0 text-center">
            <span class="bg-green-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg animate-pulse">
                💲 MODO VENTA ACTIVO
            </span>
        </div>

        <div id="loading-msg" class="absolute inset-0 flex items-center justify-center text-white bg-black/80 z-10 hidden">
            <p class="font-bold text-lg animate-bounce">Procesando...</p>
        </div>
    </div>

    <div id="result-card" class="hidden bg-white rounded-2xl p-6 shadow-lg border-2 border-slate-100 text-center transition-all transform duration-300">
        
        <div id="status-icon" class="w-20 h-20 mx-auto rounded-full flex items-center justify-center text-4xl mb-4 shadow-sm"></div>

        <h3 id="status-title" class="text-2xl font-black text-slate-800 mb-1"></h3>
        <p id="status-desc" class="text-slate-500 text-sm mb-6"></p>

        <button onclick="reiniciarEscaner()" id="btn-continue" class="w-full py-4 bg-slate-800 text-white rounded-xl font-bold hover:bg-slate-900 transition shadow-lg text-lg">
            Siguiente Boleto &rarr;
        </button>
    </div>

</div>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<audio id="beep-ok" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3"></audio>
<audio id="beep-sale" src="https://assets.mixkit.co/active_storage/sfx/2019/2019-preview.mp3"></audio> <audio id="beep-error" src="https://assets.mixkit.co/active_storage/sfx/2572/2572-preview.mp3"></audio>

<script>
    const html5QrCode = new Html5Qrcode("reader");
    let isScanning = true;
    const modeSwitch = document.getElementById('mode-switch');
    const modeText = document.getElementById('mode-text');
    const scanFrame = document.getElementById('scan-frame');
    const saleIndicator = document.getElementById('sale-indicator');

    // 1. Control del Modo (Visual)
    modeSwitch.addEventListener('change', (e) => {
        if(e.target.checked) {
            modeText.innerText = "Modo Venta: Al escanear, el boleto se marcará como VENDIDO.";
            modeText.classList.add('text-green-600', 'font-bold');
            scanFrame.classList.replace('border-blue-500/50', 'border-green-500/80');
            saleIndicator.classList.remove('hidden');
        } else {
            modeText.innerText = "Modo Consulta: Solo verifica el estado del boleto.";
            modeText.classList.remove('text-green-600', 'font-bold');
            scanFrame.classList.replace('border-green-500/80', 'border-blue-500/50');
            saleIndicator.classList.add('hidden');
        }
    });

    // 2. Iniciar Cámara
    document.addEventListener('DOMContentLoaded', () => {
        html5QrCode.start({ facingMode: "environment" }, { fps: 10, qrbox: 250 }, onScanSuccess)
        .catch(err => alert("Error de cámara: " + err));
    });

    function onScanSuccess(decodedText, decodedResult) {
        if (!isScanning) return;
        isScanning = false;
        
        document.getElementById('loading-msg').classList.remove('hidden');

        // Determinar acción según el switch
        const accion = modeSwitch.checked ? 'vender' : 'consultar';

        fetch("{{ route('admin.escaner.validar') }}", {
            method: "POST",
            headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
            body: JSON.stringify({ codigo_qr: decodedText, accion: accion })
        })
        .then(res => res.json())
        .then(data => mostrarResultado(data))
        .catch(err => {
            console.error(err);
            mostrarError("Error de conexión");
        });
    }

    function mostrarResultado(data) {
        document.getElementById('loading-msg').classList.add('hidden');
        document.getElementById('result-card').classList.remove('hidden');
        
        const icon = document.getElementById('status-icon');
        const title = document.getElementById('status-title');
        const desc = document.getElementById('status-desc');
        const btn = document.getElementById('btn-continue');

        // RESETEAR CLASES
        icon.className = "w-20 h-20 mx-auto rounded-full flex items-center justify-center text-4xl mb-4 shadow-sm";
        document.getElementById('result-card').className = "bg-white rounded-2xl p-6 shadow-lg border-2 text-center transition-all transform duration-300";

        if (!data.success) {
            // ERROR (Ya vendido o no existe)
            document.getElementById('beep-error').play();
            icon.classList.add('bg-red-100', 'text-red-600');
            icon.innerHTML = "❌";
            document.getElementById('result-card').classList.add('border-red-500');
            title.innerText = "Error";
            desc.innerText = data.message || "Código inválido";
            btn.className = "w-full py-4 bg-red-600 text-white rounded-xl font-bold hover:bg-red-700 transition";
            return;
        }

        // ÉXITO - VENTA
        if (data.tipo === 'venta_exitosa') {
            document.getElementById('beep-sale').play();
            icon.classList.add('bg-green-100', 'text-green-600');
            icon.innerHTML = "💰";
            document.getElementById('result-card').classList.add('border-green-500');
            
            title.innerText = "¡Venta Registrada!";
            title.className = "text-2xl font-black text-green-600 mb-1";
            desc.innerText = `Folio #${data.boleto.folio} marcado como vendido.`;
            
            btn.className = "w-full py-4 bg-green-600 text-white rounded-xl font-bold hover:bg-green-700 transition";
            btn.innerText = "Siguiente Venta &rarr;";
            
            // Auto-cerrar en 2 segundos para agilizar ventas masivas (opcional)
            // setTimeout(reiniciarEscaner, 2000); 
        } 
        // ÉXITO - CONSULTA
        else {
            document.getElementById('beep-ok').play();
            if(data.boleto.es_ganador) {
                icon.classList.add('bg-yellow-100', 'text-yellow-600', 'animate-bounce');
                icon.innerHTML = "🏆";
                title.innerText = "¡TIENE PREMIO!";
                desc.innerText = "Premio: $" + data.boleto.premio;
            } else {
                icon.classList.add('bg-blue-50', 'text-blue-600');
                icon.innerHTML = "ℹ️";
                title.innerText = "Boleto #" + data.boleto.folio;
                desc.innerText = "Estado: " + data.boleto.estado;
            }
            btn.className = "w-full py-4 bg-slate-800 text-white rounded-xl font-bold hover:bg-slate-900 transition";
            btn.innerText = "Escanear Otro";
        }
    }

    function mostrarError(msg) {
        document.getElementById('loading-msg').classList.add('hidden');
        alert(msg);
        reiniciarEscaner();
    }

    function reiniciarEscaner() {
        document.getElementById('result-card').classList.add('hidden');
        isScanning = true;
    }
</script>

@endsection