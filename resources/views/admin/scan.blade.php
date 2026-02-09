@extends('layouts.admin')

@section('title', 'Validar Boletos')

@section('content')

<div class="max-w-md mx-auto space-y-6">

    <div class="text-center">
        <h2 class="text-2xl font-bold text-slate-800">Validar Boleto</h2>
        <p class="text-slate-500 text-sm">Apunta la cámara al código QR del boleto</p>
    </div>

    <div class="bg-black rounded-3xl overflow-hidden shadow-2xl relative aspect-square">
        <div id="reader" class="w-full h-full object-cover"></div>
        
        <div class="absolute inset-0 border-2 border-white/30 m-8 rounded-xl pointer-events-none">
            <div class="absolute top-0 left-0 w-8 h-8 border-t-4 border-l-4 border-blue-500 rounded-tl-xl"></div>
            <div class="absolute top-0 right-0 w-8 h-8 border-t-4 border-r-4 border-blue-500 rounded-tr-xl"></div>
            <div class="absolute bottom-0 left-0 w-8 h-8 border-b-4 border-l-4 border-blue-500 rounded-bl-xl"></div>
            <div class="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 border-blue-500 rounded-br-xl"></div>
        </div>

        <div id="loading-msg" class="absolute inset-0 flex items-center justify-center text-white bg-black/80 z-10 hidden">
            <p class="animate-pulse font-bold">Verificando...</p>
        </div>
    </div>

    <div id="result-card" class="hidden bg-white rounded-2xl p-6 shadow-lg border border-slate-100 text-center transition-all">
        
        <div id="status-icon" class="w-16 h-16 mx-auto rounded-full flex items-center justify-center text-3xl mb-4">
            </div>

        <h3 id="status-title" class="text-xl font-bold text-slate-800 mb-1"></h3>
        <p id="status-desc" class="text-slate-500 text-sm mb-4"></p>

        <div class="bg-slate-50 rounded-xl p-4 text-left space-y-2 text-sm mb-4">
            <div class="flex justify-between">
                <span class="text-slate-500">Evento:</span>
                <span id="res-evento" class="font-bold text-slate-700"></span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Folio:</span>
                <span id="res-folio" class="font-bold text-slate-700"></span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Estado:</span>
                <span id="res-estado" class="font-bold"></span>
            </div>
        </div>

        <button onclick="reiniciarEscaner()" class="w-full py-3 bg-slate-800 text-white rounded-xl font-bold hover:bg-slate-900 transition">
            Escanear Otro
        </button>
    </div>

</div>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<audio id="scan-sound" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3"></audio>

<script>
    const html5QrCode = new Html5Qrcode("reader");
    let isScanning = true;

    // Configuración de la cámara
    const config = { fps: 10, qrbox: { width: 250, height: 250 } };

    // Iniciar escáner al cargar
    document.addEventListener('DOMContentLoaded', () => {
        iniciarCamara();
    });

    function iniciarCamara() {
        // Preferir cámara trasera ('environment')
        html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess)
        .catch(err => {
            console.error("Error al iniciar cámara", err);
            alert("No se pudo acceder a la cámara. Asegúrate de dar permisos.");
        });
    }

    function onScanSuccess(decodedText, decodedResult) {
        if (!isScanning) return;
        
        // 1. Detener escaneo temporalmente para no leer el mismo código 10 veces
        isScanning = false;
        html5QrCode.pause(); 
        
        // Reproducir sonido
        document.getElementById('scan-sound').play().catch(e => {});

        // Mostrar loading
        document.getElementById('loading-msg').classList.remove('hidden');

        // 2. Enviar al Backend
        fetch("{{ route('admin.escaner.validar') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ codigo_qr: decodedText })
        })
        .then(res => res.json())
        .then(data => {
            mostrarResultado(data);
        })
        .catch(err => {
            console.error(err);
            alert("Error de conexión");
            reiniciarEscaner();
        });
    }

    function mostrarResultado(data) {
        document.getElementById('loading-msg').classList.add('hidden');
        document.getElementById('result-card').classList.remove('hidden');
        
        const icon = document.getElementById('status-icon');
        const title = document.getElementById('status-title');
        const desc = document.getElementById('status-desc');

        if (data.success) {
            // Datos básicos
            document.getElementById('res-evento').innerText = data.boleto.rifa_nombre;
            document.getElementById('res-folio').innerText = '#' + data.boleto.folio;
            document.getElementById('res-estado').innerText = data.boleto.estado;

            // Lógica de Ganador
            if (data.boleto.es_ganador) {
                icon.className = "w-16 h-16 mx-auto rounded-full flex items-center justify-center text-3xl mb-4 bg-amber-100 text-amber-600 animate-bounce";
                icon.innerHTML = "🏆";
                title.innerText = "¡BOLETO GANADOR!";
                title.className = "text-xl font-bold text-amber-600 mb-1";
                desc.innerText = "Premio: $" + data.boleto.premio;
            } else {
                icon.className = "w-16 h-16 mx-auto rounded-full flex items-center justify-center text-3xl mb-4 bg-green-100 text-green-600";
                icon.innerHTML = "✅";
                title.innerText = "Boleto Válido";
                title.className = "text-xl font-bold text-green-600 mb-1";
                desc.innerText = "Este boleto es auténtico pero no tiene premio.";
            }
        } else {
            // Error o No encontrado
            icon.className = "w-16 h-16 mx-auto rounded-full flex items-center justify-center text-3xl mb-4 bg-red-100 text-red-600";
            icon.innerHTML = "❌";
            title.innerText = "Código Inválido";
            title.className = "text-xl font-bold text-red-600 mb-1";
            desc.innerText = "Este código no existe en el sistema.";
            
            document.getElementById('res-evento').innerText = "-";
            document.getElementById('res-folio').innerText = "-";
            document.getElementById('res-estado').innerText = "-";
        }
    }

    function reiniciarEscaner() {
        document.getElementById('result-card').classList.add('hidden');
        isScanning = true;
        html5QrCode.resume();
    }
</script>

@endsection