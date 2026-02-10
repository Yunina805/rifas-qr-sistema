<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado de tu Boleto</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Poppins', sans-serif; }
        .animate-bounce-slow { animation: bounce 2s infinite; }
        .gradient-winner { background: linear-gradient(135deg, #FFD700 0%, #FDB931 100%); }
        .gradient-loser { background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%); }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4 {{ $boleto->es_ganador ? 'bg-slate-900' : 'bg-gray-100' }}">

    <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden relative transform transition-all duration-500 hover:scale-[1.01]">
        
        <div class="bg-slate-800 text-white p-4 text-center relative z-10">
            <p class="text-xs uppercase tracking-widest text-slate-400 font-bold">Sorteo Oficial</p>
            <h1 class="text-lg font-bold truncate">{{ $boleto->rifa->nombre }}</h1>
        </div>

        <div class="p-8 text-center relative">
            
            @if($boleto->es_ganador)
                
                <div class="mb-6 relative">
                    <div class="absolute inset-0 bg-yellow-400 rounded-full filter blur-xl opacity-50 animate-pulse"></div>
                    <i class="ri-trophy-fill text-8xl text-yellow-500 relative z-10 drop-shadow-lg animate-bounce-slow"></i>
                </div>

                <h2 class="text-3xl font-black text-slate-800 mb-2 uppercase">¡Felicidades!</h2>
                <p class="text-slate-500 mb-6">Tu boleto ha sido premiado.</p>

                <div class="gradient-winner p-6 rounded-2xl shadow-lg border-4 border-yellow-200 mb-8 transform rotate-1 hover:rotate-0 transition">
                    <p class="text-xs font-bold text-yellow-900 uppercase mb-1">Ganaste:</p>
                    <p class="text-2xl font-black text-yellow-900 leading-tight">
                        {{ $boleto->premio }}
                    </p>
                </div>

                <a href="https://wa.me/5211234567890?text=Hola,%20acabo%20de%20escanear%20mi%20boleto%20Folio:%20{{ $boleto->folio }}%20y%20dice%20que%20GANE%20{{ $boleto->premio }}." 
                   target="_blank"
                   class="block w-full bg-green-500 hover:bg-green-600 text-white font-bold py-4 rounded-xl shadow-lg shadow-green-200 transition transform active:scale-95 flex items-center justify-center gap-2">
                    <i class="ri-whatsapp-line text-xl"></i> Reclamar Premio
                </a>

                <p class="text-xs text-gray-400 mt-4">
                    *Presenta tu boleto físico para reclamar.
                </p>

            @else
                
                <div class="mb-6">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ri-emotion-sad-line text-5xl text-gray-400"></i>
                    </div>
                </div>

                <h2 class="text-2xl font-bold text-gray-700 mb-2">Sigue Participando</h2>
                <p class="text-gray-500 mb-8 text-sm px-4">
                    Este boleto no tiene premio instantáneo, ¡pero gracias por apoyar!
                </p>

                <div class="bg-gray-50 border border-gray-100 p-4 rounded-xl mb-6">
                    <p class="text-xs text-gray-400 uppercase font-bold mb-1">Tu Folio</p>
                    <p class="text-3xl font-black text-gray-300 tracking-widest">{{ $boleto->folio }}</p>
                </div>

                <a href="{{ route('admin.dashboard') }}" class="text-blue-500 text-sm font-bold hover:underline">
                    Ver más sorteos
                </a>

            @endif

        </div>

        <div class="bg-gray-50 p-3 text-center border-t border-gray-100">
            <p class="text-[10px] text-gray-400 flex justify-center items-center gap-1">
                <i class="ri-shield-check-fill text-green-500"></i> Verificado por RifasQR · {{ date('Y') }}
            </p>
        </div>

    </div>

    @if($boleto->es_ganador)
        <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
        <script>
            // Lanza confeti automáticamente al cargar si es ganador
            window.onload = function() {
                var duration = 3000; // 3 segundos
                var animationEnd = Date.now() + duration;
                var defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 0 };

                var interval = setInterval(function() {
                    var timeLeft = animationEnd - Date.now();

                    if (timeLeft <= 0) {
                        return clearInterval(interval);
                    }

                    var particleCount = 50 * (timeLeft / duration);
                    confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 } }));
                    confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 } }));
                }, 250);
            };

            function randomInRange(min, max) {
                return Math.random() * (max - min) + min;
            }
        </script>
    @endif

</body>
</html>