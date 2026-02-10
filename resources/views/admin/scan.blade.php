@extends('layouts.admin')

@section('title', 'Escáner QR')

@section('content')
<div class="p-6 max-w-lg mx-auto">
    
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold text-slate-800">Escáner de Boletos</h1>
        <p class="text-sm text-slate-500">Apunta la cámara al código QR del boleto.</p>
    </div>

    <div class="bg-black rounded-2xl overflow-hidden shadow-2xl relative">
        <div id="reader" class="w-full h-[400px] bg-black"></div>
        
        <div class="absolute inset-0 border-2 border-red-500/50 pointer-events-none"></div>
        <div class="absolute top-1/2 left-0 right-0 h-0.5 bg-red-600 shadow-[0_0_10px_rgba(255,0,0,0.8)] animate-pulse"></div>
    </div>

    <div id="resultado" class="hidden mt-6 p-4 rounded-xl border-l-4 shadow-sm transition-all">
        <h3 id="resTitulo" class="font-bold text-lg"></h3>
        <p id="resMensaje" class="text-sm"></p>
        <div id="resDetalle" class="mt-2 text-xs bg-white/50 p-2 rounded"></div>
        
        <div id="acciones" class="mt-4 flex gap-2 hidden">
            <button id="btnVender" class="flex-1 bg-green-600 text-white py-2 rounded font-bold shadow hover:bg-green-700">
                ✅ Confirmar Venta
            </button>
            <button id="btnEntregar" class="flex-1 bg-blue-600 text-white py-2 rounded font-bold shadow hover:bg-blue-700">
                🏆 Entregar Premio
            </button>
        </div>
    </div>

</div>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<audio id="beep-ok" src="https://assets.mixkit.co/active_storage/sfx/2578/2578-preview.mp3"></audio>
<audio id="beep-error" src="https://assets.mixkit.co/active_storage/sfx/2572/2572-preview.mp3"></audio>

<script>
    // Configuración del Escáner
    const html5QrCode = new Html5Qrcode("reader");
    let isScanning = true; // Para evitar lecturas dobles muy rápidas
    let lastCode = ""; 

    function onScanSuccess(decodedText, decodedResult) {
        // --- ZONA DE PRUEBAS (Debug) ---
        console.log("👁️ ¡OJO! La cámara detectó este texto:", decodedText);
        // -------------------------------

        if (!isScanning || decodedText === lastCode) {
            console.log("...Lectura repetida ignorada...");
            return;
        }
        
        // Pausamos brevemente para no saturar
        lastCode = decodedText;
        console.log("🚀 Enviando al backend..."); // Aviso de envío
        
        // Enviamos al Backend
        fetch("{{ route('admin.escaner.validar') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ codigo_qr: decodedText })
        })
        .then(response => {
            // Si el servidor falla, queremos saber por qué
            if (!response.ok) {
                throw new Error("Error del Servidor: " + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log("✅ Respuesta recibida:", data); // Ver qué respondió Laravel
            mostrarResultado(data, decodedText);
        })
        .catch(error => {
            console.error('❌ Error grave:', error);
            alert("Error de conexión: " + error.message);
        });
    }

    function mostrarResultado(data, codigoQr) {
        const div = document.getElementById('resultado');
        const titulo = document.getElementById('resTitulo');
        const mensaje = document.getElementById('resMensaje');
        const detalle = document.getElementById('resDetalle');
        const acciones = document.getElementById('acciones');
        const btnVender = document.getElementById('btnVender');
        const btnEntregar = document.getElementById('btnEntregar');

        // Limpieza de clases previas
        div.classList.remove('hidden', 'bg-green-50', 'border-green-500', 'bg-yellow-50', 'border-yellow-500', 'bg-red-50', 'border-red-500', 'bg-blue-50', 'border-blue-500', 'bg-slate-50', 'border-slate-400', 'bg-amber-50', 'border-amber-500', 'text-green-800', 'text-yellow-800', 'text-red-800', 'text-blue-900', 'text-slate-700', 'text-amber-900');
        acciones.classList.add('hidden');
        
        // Reproducir sonido
        if(data.success) {
            document.getElementById('beep-ok').play();
        } else {
            document.getElementById('beep-error').play();
        }

        // --- CASO 1: CONSULTA (Cuando escaneas por primera vez) ---
        if (data.tipo === 'consulta') {
            
            titulo.innerText = "Boleto Detectado";
            mensaje.innerText = "Folio: " + data.boleto.folio;
            
            let htmlEstadoBoleto = '';

            // ---------------------------------------------------------
            // OPCIÓN A: ES GANADOR (Dorado 🏆)
            // ---------------------------------------------------------
            if (data.boleto.es_ganador == 1 || data.boleto.es_ganador === true) {
                div.classList.add('bg-amber-50', 'border-amber-500', 'text-amber-900');
                
                htmlEstadoBoleto = `
                    <div class="mt-3 mb-2 p-3 bg-white/80 rounded-lg border border-amber-200 flex items-center gap-3 shadow-sm animate-pulse">
                        <div class="bg-amber-100 p-2 rounded-full text-amber-600">
                            <i class="ri-trophy-fill text-2xl"></i>
                        </div>
                        <div>
                            <p class="font-bold text-sm text-amber-700">¡ES UN BOLETO GANADOR!</p>
                            <p class="text-xs font-mono text-amber-600 font-bold">Premio: $${data.boleto.premio}</p>
                        </div>
                    </div>
                `;
            } 
            // ---------------------------------------------------------
            // OPCIÓN B: NO ES GANADOR (Gris 🎫)
            // ---------------------------------------------------------
            else {
                div.classList.add('bg-slate-50', 'border-slate-400', 'text-slate-700');
                
                htmlEstadoBoleto = `
                    <div class="mt-3 mb-2 p-3 bg-white/60 rounded-lg border border-slate-200 flex items-center gap-3 shadow-sm">
                        <div class="bg-slate-200 p-2 rounded-full text-slate-500">
                            <i class="ri-emotion-normal-line text-2xl"></i>
                        </div>
                        <div>
                            <p class="font-bold text-sm text-slate-600">Boleto No Premiado</p>
                            <p class="text-xs text-slate-400">Suerte para la próxima.</p>
                        </div>
                    </div>
                `;
            }

            // Construimos el detalle completo
            let htmlDetalle = `
                ${htmlEstadoBoleto}
                <div class="grid grid-cols-2 gap-y-1 gap-x-4 text-xs mt-2 border-t border-black/5 pt-2">
                    <div><strong>Estado:</strong> <span class="uppercase font-bold">${data.boleto.estado}</span></div>
                    <div><strong>Costo:</strong> $${data.boleto.precio}</div>
                    <div class="col-span-2 mt-1">
                        <strong>Vendedor Asignado:</strong><br>
                        ${data.boleto.vendedor}
                    </div>
                </div>
            `;
            detalle.innerHTML = htmlDetalle;

            // Botones de Acción
            acciones.classList.remove('hidden');
            
            // Lógica de Botones (Vender o Entregar)
            if(data.boleto.estado === 'Disponible') {
                btnVender.style.display = 'block';
                // Al hacer clic, enviamos la acción 'vender'
                btnVender.onclick = () => procesarAccion(codigoQr, 'vender');
                btnEntregar.style.display = 'none';

            } else if (data.boleto.estado === 'Vendido' && (data.boleto.es_ganador == 1 || data.boleto.es_ganador === true)) {
                // Solo si es GANADOR y VENDIDO se puede entregar premio
                btnVender.style.display = 'none';
                btnEntregar.style.display = 'block';
                btnEntregar.onclick = () => procesarAccion(codigoQr, 'entregar');

            } else {
                // Si ya está vendido y no gana nada, no hay botones
                acciones.classList.add('hidden'); 
            }

        // --- CASO 2: ÉXITO EN ACCIÓN (Ya se vendió o entregó) ---
        } else if (data.success) {
            div.classList.add('bg-green-50', 'border-green-500', 'text-green-800');
            titulo.innerText = "¡Operación Exitosa!";
            mensaje.innerText = data.mensaje;
            detalle.innerHTML = data.datos_extra || ''; 
            setTimeout(() => { lastCode = ""; }, 2500); 

        // --- CASO 3: ERROR (Boleto no existe o error del sistema) ---
        } else {
            div.classList.add('bg-red-50', 'border-red-500', 'text-red-800');
            titulo.innerText = "Error";
            mensaje.innerText = data.message;
            detalle.innerHTML = "";
            setTimeout(() => { lastCode = ""; }, 2500);
        }
    }

    // Función para confirmar Venta o Entrega
    function procesarAccion(codigo, accion) {
        fetch("{{ route('admin.escaner.validar') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ codigo_qr: codigo, accion: accion })
        })
        .then(response => response.json())
        .then(data => {
            mostrarResultado(data, codigo);
        });
    }

    // Iniciar cámara
    html5QrCode.start(
        { facingMode: "environment" }, 
        { fps: 10, qrbox: { width: 250, height: 250 } },
        onScanSuccess
    ).catch(err => {
        console.log("Error iniciando cámara", err);
        alert("No se pudo iniciar la cámara. Asegúrate de dar permisos.");
    });

</script>
@endsection