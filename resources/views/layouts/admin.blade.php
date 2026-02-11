<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <title>@yield('title', 'Admin Panel')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.1.0/fonts/remixicon.css" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        corporate: {
                            50: '#f8fafc',
                            100: '#f1f5f9',
                            200: '#e2e8f0',
                            300: '#cbd5e1',
                            400: '#94a3b8',
                            500: '#64748b',
                            600: '#475569',
                            800: '#1e293b',
                            900: '#0f172a',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        [x-cloak] { display: none !important; }
        *:focus-visible { outline: 2px solid #0f172a; outline-offset: 2px; }
    </style>

    @stack('head')
</head>

{{-- X-DATA PRINCIPAL: Controla el estado del sidebar móvil --}}
<body x-data="{ sidebarOpen: false }" class="bg-corporate-50 font-sans text-slate-600 antialiased h-screen flex overflow-hidden selection:bg-slate-900 selection:text-white">

    {{-- BACKDROP MÓVIL (Fondo oscuro) --}}
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-slate-900/80 z-40 lg:hidden" x-cloak></div>

    {{-- SIDEBAR --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-slate-200 flex flex-col transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-auto lg:flex shadow-2xl lg:shadow-none">
        
        {{-- Header Sidebar --}}
        <div class="h-16 flex items-center justify-between px-6 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded bg-slate-900 flex items-center justify-center text-white">
                    <i class="ri-command-fill text-lg"></i>
                </div>
                <div>
                    <h1 class="font-display font-bold text-slate-900 text-base tracking-tight">Rifas<span class="text-slate-400 font-medium">Costa</span></h1>
                </div>
            </div>
            {{-- Botón Cerrar (Solo Móvil) --}}
            <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-slate-600">
                <i class="ri-close-line text-2xl"></i>
            </button>
        </div>

        <nav class="flex-1 px-3 py-6 space-y-1 overflow-y-auto">

            {{-- ZONA EXCLUSIVA ADMIN --}}
            @if(Auth::check() && Auth::user()->role === 'admin')
                
                <div class="px-3 mb-2 mt-1">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">General</span>
                </div>

                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-all duration-200 group border border-transparent
                   {{ request()->routeIs('admin.dashboard') ? 'bg-slate-900 text-white shadow-md' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <i class="{{ request()->routeIs('admin.dashboard') ? 'ri-dashboard-fill' : 'ri-dashboard-line' }} text-lg opacity-90"></i>
                    <span>Dashboard</span>
                </a>

                <div class="px-3 mb-2 mt-6">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Gestión</span>
                </div>

                <a href="{{ route('admin.rifas') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-all duration-200 group border border-transparent
                   {{ request()->routeIs('admin.rifas*') ? 'bg-slate-900 text-white shadow-md' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <i class="{{ request()->routeIs('admin.rifas*') ? 'ri-coupon-3-fill' : 'ri-coupon-3-line' }} text-lg opacity-90"></i>
                    <span>Mis Rifas</span>
                </a>

                {{-- Sección Finanzas --}}
                <div class="px-3 mb-2 mt-6">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Finanzas</span>
                </div>

                <a href="{{ route('admin.vendedores.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-all duration-200 group border border-transparent
                   {{ request()->routeIs('admin.vendedores*') ? 'bg-slate-900 text-white shadow-md' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <i class="{{ request()->routeIs('admin.vendedores*') ? 'ri-user-star-fill' : 'ri-user-star-line' }} text-lg opacity-90"></i>
                    <span>Vendedores / Reportes</span>
                </a>

            @endif

            {{-- ZONA COMÚN --}}
            <div class="px-3 mb-2 mt-6">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Herramientas</span>
            </div>

            @php
                $rutaEscaner = (Auth::check() && Auth::user()->role === 'admin') ? route('admin.escaner.view') : route('app.escaner.view');
                $isEscanerActive = request()->routeIs('*.escaner*');
            @endphp

            <a href="{{ $rutaEscaner }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-all duration-200 group border border-transparent
               {{ $isEscanerActive ? 'bg-slate-900 text-white shadow-md' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <i class="{{ $isEscanerActive ? 'ri-qr-scan-2-fill' : 'ri-qr-scan-2-line' }} text-lg opacity-90"></i>
                <span>Escáner QR</span>
            </a>

        </nav>

        {{-- USER INFO BOTTOM --}}
        <div class="p-3 border-t border-slate-100">
            <button class="flex items-center gap-3 w-full p-2 rounded-md hover:bg-slate-50 transition-colors text-left group border border-transparent hover:border-slate-200">
                <div class="w-8 h-8 rounded bg-slate-200 flex items-center justify-center text-slate-600 font-bold text-xs uppercase shrink-0">
                    {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-700 truncate">
                        {{ Auth::user()->name ?? 'Usuario' }}
                    </p>
                    <p class="text-[11px] text-slate-400 truncate">{{ Auth::user()->email ?? '' }}</p>
                </div>
            </button>
        </div>
    </aside>

    {{-- MAIN CONTENT WRAPPER --}}
    <div class="flex-1 flex flex-col h-full overflow-hidden relative w-full">

        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 lg:px-6 z-10 sticky top-0">
            
            <div class="flex items-center gap-4">
                {{-- Botón Hamburguesa (Activa el sidebar) --}}
                <button @click="sidebarOpen = true" class="lg:hidden p-2 -ml-2 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-md transition-colors">
                    <i class="ri-menu-2-line text-xl"></i>
                </button>

                <div class="hidden md:block">
                    <nav class="flex text-xs font-medium text-slate-400 mb-0.5">
                        <ol class="flex items-center gap-2">
                            <li class="hover:text-slate-600 transition-colors"><a href="#">Panel</a></li>
                            <li class="text-slate-300">/</li>
                            <li class="text-slate-800">@yield('context_subtitle', 'Inicio')</li>
                        </ol>
                    </nav>
                </div>
            </div>

            {{-- USER DROPDOWN --}}
            <div class="flex items-center gap-3">
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" @click.outside="open = false" class="flex items-center gap-2 text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors px-2 py-1 rounded-md hover:bg-slate-50">
                        <span class="hidden sm:inline">Mi Cuenta</span> <div class="sm:hidden w-8 h-8 rounded bg-slate-100 flex items-center justify-center">
                             <i class="ri-user-line"></i>
                        </div>
                        <i class="ri-arrow-down-s-line text-slate-400 hidden sm:inline" :class="{'rotate-180': open}"></i>
                    </button>

                    <div x-show="open" 
                         x-cloak
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-slate-100 py-1 z-50 ring-1 ring-black ring-opacity-5">
                        
                        <div class="px-4 py-2 border-b border-slate-50">
                            <p class="text-xs text-slate-500">Conectado como</p>
                            <p class="text-sm font-medium text-slate-900 truncate">{{ Auth::user()->name ?? 'Usuario' }}</p>
                        </div>

                        <div class="border-t border-slate-100 my-1"></div>
                        
                        <form method="POST" action="{{ route('logout') }}"> 
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                Cerrar Sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto bg-corporate-50 p-4 lg:p-8 scroll-smooth w-full">
            <div class="max-w-7xl mx-auto w-full">
                
                {{-- Cabecera de Página Responsiva --}}
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-6 lg:mb-8">
                    <div>
                        <h2 class="font-display font-bold text-xl lg:text-2xl text-slate-900 tracking-tight">
                            @yield('context_title', 'Dashboard')
                        </h2>
                        <p class="text-slate-500 mt-1 text-xs lg:text-sm">
                            @yield('context_subtitle_desc', 'Resumen de actividad y métricas clave.')
                        </p>
                    </div>
                    <div class="w-full sm:w-auto">
                        @yield('action_button')
                    </div>
                </div>

                <div class="animate-fade-in-up w-full">
                    @yield('content')
                </div>
            </div>
        </main>

    </div>

    @stack('scripts')
</body>
</html>