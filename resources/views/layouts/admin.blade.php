<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
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
                        // Definimos una paleta "Corporate" basada en Slate
                        corporate: {
                            50: '#f8fafc',
                            100: '#f1f5f9',
                            200: '#e2e8f0',
                            300: '#cbd5e1',
                            400: '#94a3b8',
                            500: '#64748b',
                            600: '#475569',
                            800: '#1e293b',
                            900: '#0f172a', // Color principal oscuro (Casi negro)
                        }
                    }
                }
            }
        }
    </script>

    <style>
        /* Scrollbar refinado y profesional */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        [x-cloak] { display: none !important; }
        
        /* Efecto sutil en focus */
        *:focus-visible { outline: 2px solid #0f172a; outline-offset: 2px; }
    </style>

    @stack('head')
</head>

<body class="bg-corporate-50 font-sans text-slate-600 antialiased h-screen flex overflow-hidden selection:bg-slate-900 selection:text-white">

    <aside class="w-72 bg-white border-r border-slate-200 flex flex-col z-30 hidden md:flex shadow-[2px_0_20px_-10px_rgba(0,0,0,0.05)]">
        
        <div class="h-16 flex items-center px-6 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded bg-slate-900 flex items-center justify-center text-white">
                    <i class="ri-command-fill text-lg"></i>
                </div>
                <div>
                    <h1 class="font-display font-bold text-slate-900 text-base tracking-tight">Rifas<span class="text-slate-400 font-medium">Admin</span></h1>
                </div>
            </div>
        </div>

        <nav class="flex-1 px-3 py-6 space-y-1 overflow-y-auto">

            {{-- SECCIÓN: PRINCIPAL --}}
            <div class="px-3 mb-2 mt-1">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">General</span>
            </div>

            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-all duration-200 group border border-transparent
               {{ request()->routeIs('admin.dashboard') 
                  ? 'bg-slate-900 text-white shadow-md shadow-slate-900/10' 
                  : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                
                <i class="{{ request()->routeIs('admin.dashboard') ? 'ri-dashboard-fill' : 'ri-dashboard-line' }} text-lg opacity-90"></i>
                <span>Dashboard</span>
            </a>

            {{-- SECCIÓN: OPERACIÓN --}}
            <div class="px-3 mb-2 mt-6">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Gestión</span>
            </div>

            <a href="{{ route('admin.rifas') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-all duration-200 group border border-transparent
               {{ request()->routeIs('admin.rifas*') 
                  ? 'bg-slate-900 text-white shadow-md shadow-slate-900/10' 
                  : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <i class="{{ request()->routeIs('admin.rifas*') ? 'ri-coupon-3-fill' : 'ri-coupon-3-line' }} text-lg opacity-90"></i>
                <span>Mis Rifas</span>
            </a>

            <a href="{{ route('admin.escaner.view') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-all duration-200 group border border-transparent
               {{ request()->routeIs('admin.escaner*') 
                  ? 'bg-slate-900 text-white shadow-md shadow-slate-900/10' 
                  : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <i class="{{ request()->routeIs('admin.escaner*') ? 'ri-qr-scan-2-fill' : 'ri-qr-scan-2-line' }} text-lg opacity-90"></i>
                <span>Escaner</span>
            </a>

            {{-- SECCIÓN: FINANZAS --}}
            <div class="px-3 mb-2 mt-6">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Finanzas</span>
            </div>

            <a href="{{ route('admin.vendedores.index') }}"
              class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-all duration-200 group border border-transparent
              {{ request()->routeIs('admin.vendedores*') 
                  ? 'bg-slate-900 text-white shadow-md shadow-slate-900/10' 
                  : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                
                <i class="{{ request()->routeIs('admin.vendedores*') ? 'ri-user-star-fill' : 'ri-user-star-line' }} text-lg opacity-90"></i>
                <span>Vendedores / Reportes</span>
            </a>

        </nav>

        <div class="p-3 border-t border-slate-100">
            <button class="flex items-center gap-3 w-full p-2 rounded-md hover:bg-slate-50 transition-colors text-left group border border-transparent hover:border-slate-200">
                <div class="w-8 h-8 rounded bg-slate-200 flex items-center justify-center text-slate-600 font-bold text-xs">
                    {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-700 truncate">
                        @yield('user_name', 'Administrador')
                    </p>
                    <p class="text-[11px] text-slate-400 truncate">admin@empresa.com</p>
                </div>
                <i class="ri-expand-up-down-line text-slate-400 text-xs"></i>
            </button>
        </div>
    </aside>

    <div class="flex-1 flex flex-col h-full overflow-hidden relative">

        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6 z-10 sticky top-0">
            
            <div class="flex items-center gap-4">
                <button class="md:hidden p-2 text-slate-400 hover:text-slate-600">
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

            <div class="flex-1 max-w-lg px-6">
                <div class="relative group">
                    <i class="ri-search-2-line absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-slate-800 transition-colors"></i>
                    <input type="text" 
                           placeholder="Buscar (Presiona '/')" 
                           class="w-full bg-slate-50 border border-slate-200 rounded-md py-1.5 pl-9 pr-4 text-sm focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition-all placeholder:text-slate-400 text-slate-700">
                </div>
            </div>

            <div class="flex items-center gap-3">
                
                <button class="p-2 text-slate-400 hover:text-slate-700 transition-colors rounded-md hover:bg-slate-50 relative">
                    <i class="ri-notification-line text-lg"></i>
                    <span class="absolute top-2 right-2.5 w-1.5 h-1.5 bg-orange-500 rounded-full border border-white"></span>
                </button>

                <div class="h-5 w-px bg-slate-200 mx-1"></div>

                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" @click.outside="open = false" class="flex items-center gap-2 text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors px-2 py-1 rounded-md hover:bg-slate-50">
                        <span>Mi Cuenta</span>
                        <i class="ri-arrow-down-s-line text-slate-400" :class="{'rotate-180': open}"></i>
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
                            <p class="text-sm font-medium text-slate-900 truncate">@yield('user_name', 'Admin')</p>
                        </div>

                        <a href="#" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-slate-900">
                            Configuración
                        </a>
                        <a href="#" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-slate-900">
                            Facturación
                        </a>
                        
                        <div class="border-t border-slate-100 my-1"></div>
                        
                        <form method="POST" action="#"> @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                Cerrar Sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto bg-corporate-50 p-6 md:p-8 scroll-smooth">
            <div class="max-w-7xl mx-auto">
                
                <div class="flex items-end justify-between mb-8">
                    <div>
                        <h2 class="font-display font-bold text-2xl text-slate-900 tracking-tight">
                            @yield('context_title', 'Dashboard')
                        </h2>
                        <p class="text-slate-500 mt-1 text-sm">
                            @yield('context_subtitle_desc', 'Resumen de actividad y métricas clave.')
                        </p>
                    </div>
                    <div>
                        @yield('action_button')
                    </div>
                </div>

                <div class="animate-fade-in-up">
                    @hasSection('content')
                        @yield('content')
                    @else
                        <div class="bg-white rounded-xl border border-slate-200 border-dashed p-12 text-center">
                            <div class="w-12 h-12 bg-slate-50 rounded-lg flex items-center justify-center mx-auto mb-3 text-slate-400">
                                <i class="ri-file-list-3-line text-xl"></i>
                            </div>
                            <h3 class="font-medium text-slate-900">Sin contenido</h3>
                            <p class="text-slate-500 text-sm mt-1">Usa la directiva @yield('content') para inyectar datos aquí.</p>
                        </div>
                    @endif
                </div>

            </div>
        </main>

    </div>

    @stack('scripts')
</body>
</html>