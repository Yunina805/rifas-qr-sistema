<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>@yield('title', 'Admin · Panel')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- Tailwind (temporal) -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Remixicon -->
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css" rel="stylesheet">

  @stack('head')
</head>

<body class="min-h-screen flex bg-gray-100">

  <!-- Sidebar -->
  <aside class="w-64 shrink-0 bg-white border-r flex flex-col">

    <!-- Logo -->
    <div class="px-6 py-5 border-b">
      <h1 class="text-lg font-bold text-gray-800">
        Admin Rifas
      </h1>
      <p class="text-xs text-gray-500">
        Panel de administración
      </p>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-6 space-y-1 text-sm">

      <a href="{{ route('admin.dashboard') }}"
         class="flex items-center gap-3 px-3 py-2 rounded-lg bg-blue-50 text-blue-600 font-medium">
        <i class="ri-dashboard-line"></i>
        Dashboard
      </a>

      <a href="{{ route('admin.rifas') }}"
         class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-100">
        <i class="ri-ticket-line"></i>
        Rifas
      </a>

      <a href="{{ route('admin.boletos') }}"
         class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-100">
        <i class="ri-qr-code-line"></i>
        Boletos
      </a>

      <a href="{{ route('admin.premios') }}"
         class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-100">
        <i class="ri-gift-line"></i>
        Premios
      </a>

      <a href="{{ route('admin.reportes') }}"
         class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-100">
        <i class="ri-bar-chart-line"></i>
        Reportes
      </a>

    </nav>

    <!-- Footer Sidebar -->
    <div class="px-6 py-4 border-t text-xs text-gray-400">
      © {{ date('Y') }} Rifas
    </div>
  </aside>

  <!-- Main Area -->
  <div class="flex-1 flex flex-col">

    <!-- Header -->
    <header class="bg-white border-b px-6 py-4 flex items-center justify-between">
      <div>
        <h2 class="text-sm font-semibold text-gray-800">
          @yield('context_title', 'Rifa en contexto')
        </h2>
        <p class="text-xs text-gray-500">
          @yield('context_subtitle', 'Gran Rifa Anual · Febrero 2026')
        </p>
      </div>

      <div class="flex items-center gap-3">
        <span class="text-sm text-gray-600">
          @yield('user_name', 'Admin')
        </span>
        <i class="ri-user-3-line text-gray-500"></i>

        <!-- Logout DESACTIVADO por ahora -->
        <i class="ri-logout-box-r-line text-gray-400 cursor-pointer"></i>
      </div>
    </header>

    <!-- Content -->
    <main class="flex-1 p-6 bg-gray-50">
      @yield('content')
    </main>

  </div>

  @stack('scripts')
</body>
</html>
