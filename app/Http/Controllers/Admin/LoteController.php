<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rifa;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf as PDFFacade;

class LoteController extends Controller
{
    public function index(Request $request, Rifa $rifa)
    {
        // 1. Iniciamos la consulta usando la relación 'boletos'
        // Gracias al alias que pusimos en el Modelo, esto funciona perfecto.
        $query = $rifa->boletos();

        // 2. Filtro: Si el admin busca por número de folio
        if ($request->has('search') && $request->search != '') {
            $query->where('folio', 'like', '%' . $request->search . '%');
        }

        // 3. Filtro: Por estado (Ganadores, vendidos, disponibles)
        if ($request->has('estado') && $request->estado != '') {
            if($request->estado == 'ganadores') {
                $query->where('es_ganador', true);
            } else {
                $query->where('estado', $request->estado);
            }
        }

        // 4. Paginamos los resultados (50 por página)
        $boletos = $query->paginate(50);

        // 5. ENVIAMOS LA VARIABLE $boletos A LA VISTA (Esto es lo que faltaba)
        return view('admin.lotes', compact('rifa', 'boletos'));
    }

    public function imprimir(Rifa $rifa)
    {
        // Obtenemos todos los boletos
        // Usamos 'chunk' si fueran demasiados, pero para 1000 el PDF aguanta bien.
        // Aumentamos el tiempo de ejecución por si son muchos
        set_time_limit(300); 

        $boletos = $rifa->boletos()->get();

        // CAMBIA 'Pdf::loadView' POR 'PDFFacade::loadView'
        $pdf = PDFFacade::loadView('admin.pdf.boletos', compact('rifa', 'boletos'));
        
        return $pdf->stream();
    }
}