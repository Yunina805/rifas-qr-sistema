<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado del Sorteo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-white rounded-3xl shadow-2xl overflow-hidden text-center relative">
        
        <div class="bg-slate-900 text-white p-6">
            <h1 class="text-xl font-bold uppercase tracking-wider">{{ $boleto->rifa->nombre }}</h1>
            <p class="text-slate-400 text-sm mt-1">Sede: {{ $boleto->rifa->sede }}</p>
        </div>

        <div class="p-8 space-y-6">
            
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase">Tu Boleto</p>
                <p class="text-4xl font-mono font-black text-gray-800 tracking-widest">#{{ $boleto->folio }}</p>
            </div>

            <hr class="border-gray-100">

            @if($boleto->es_ganador)
                <div class="py-4">
                    <div class="w-24 h-24 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center mx-auto text-5xl mb-4 animate-bounce">
                        🏆
                    </div>
                    <h2 class="text-3xl font-black text-yellow-500 mb-2">¡ERES GANADOR!</h2>
                    <p class="text-gray-600">Felicidades, tu boleto ha sido premiado con:</p>
                    <div class="mt-4 bg-yellow-50 border-2 border-yellow-200 rounded-xl p-4">
                        <span class="text-2xl font-bold text-slate-800">${{ number_format($boleto->premio, 2) }}</span>
                    </div>
                </div>
                
                <script>
                    // Lanzar confeti automáticamente
                    document.addEventListener("DOMContentLoaded", () => {
                        var duration = 3 * 1000;
                        var animationEnd = Date.now() + duration;
                        var defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 0 };

                        function randomInOut(min, max) { return Math.random() * (max - min) + min; }

                        var interval = setInterval(function() {
                            var timeLeft = animationEnd - Date.now();
                            if (timeLeft <= 0) return clearInterval(interval);
                            var particleCount = 50 * (timeLeft / duration);
                            confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInOut(0.1, 0.3), y: Math.random() - 0.2 } }));
                            confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInOut(0.7, 0.9), y: Math.random() - 0.2 } }));
                        }, 250);
                    });
                </script>

            @else
                <div class="py-4">
                    <div class="w-20 h-20 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mx-auto text-4xl mb-4 grayscale">
                        🍀
                    </div>
                    <h2 class="text-2xl font-bold text-gray-700 mb-2">Sigue Participando</h2>
                    <p class="text-gray-500 text-sm">
                        Lo sentimos, este folio no tiene premio asignado en este momento.
                        <br>¡Muchas gracias por tu apoyo!
                    </p>
                </div>
            @endif

            <div class="pt-4">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-bold 
                    {{ $boleto->estado == 'vendido' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                    Estado: {{ ucfirst($boleto->estado) }}
                </span>
            </div>

        </div>

        <div class="bg-gray-50 p-4 text-xs text-gray-400">
            Validado por Sistema Rifas QR &copy; {{ date('Y') }}
        </div>
    </div>

</body>
</html>