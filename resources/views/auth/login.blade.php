<x-guest-layout>
    <div class="w-full max-w-md mx-auto bg-white p-8 rounded-lg shadow-[0_4px_20px_-2px_rgba(0,0,0,0.1)] border border-gray-100">
        
        {{-- ENCABEZADO: Sobrio y Directo --}}
            <h2 class="text-2xl font-bold text-gray-900">RifasCosta</h2>
            <p class="text-sm text-gray-500 mt-1">Panel de Administración</p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            {{-- INPUT EMAIL: Estilo Outline (Bordeado) --}}
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Correo electrónico</label>
                <input id="email" 
                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-slate-900 focus:ring-slate-900 sm:text-sm py-2.5 px-3 transition duration-150 ease-in-out" 
                       type="email" 
                       name="email" 
                       :value="old('email')" 
                       required 
                       autofocus 
                       autocomplete="username" 
                       placeholder="nombre@empresa.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            {{-- INPUT PASSWORD --}}
            <div>
                <div class="flex justify-between items-center mb-1.5">
                    <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                </div>
                
                <input id="password" 
                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-slate-900 focus:ring-slate-900 sm:text-sm py-2.5 px-3 transition duration-150 ease-in-out"
                       type="password"
                       name="password"
                       required 
                       autocomplete="current-password" 
                       placeholder="••••••••" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            {{-- ACCIONES SECUNDARIAS --}}
            <div class="flex items-center justify-between">
                <label for="remember_me" class="inline-flex items-center cursor-pointer">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-slate-900 shadow-sm focus:ring-slate-900" name="remember">
                    <span class="ms-2 text-sm text-gray-600">Recordarme</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-sm text-slate-700 hover:text-slate-900 font-medium hover:underline" href="{{ route('password.request') }}">
                        ¿Recuperar contraseña?
                    </a>
                @endif
            </div>

            {{-- BOTÓN: Sólido y Autoritario --}}
            <button class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-md shadow-sm text-sm font-bold text-white bg-slate-900 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-900 transition-colors duration-200">
                Iniciar Sesión
            </button>
        </form>
        
        <div class="mt-6 text-center">
            <p class="text-xs text-gray-400">
                &copy; {{ date('Y') }} RifasCosta. Sistema Seguro.
            </p>
        </div>
    </div>
</x-guest-layout>