<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // 1. Si no está logueado, al login
        if (! $request->user()) {
            return redirect()->route('login');
        }

        // 2. Si es Admin, pase usted (Llave Maestra)
        if ($request->user()->role === 'admin') {
            return $next($request);
        }

        // 3. Si tiene el rol correcto (ej. vendedor), pase
        if ($request->user()->role === $role) {
            return $next($request);
        }
        
        abort(403, 'ACCESO DENEGADO: No tienes permiso para ver esta sección.');
    }
}