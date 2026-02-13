<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rifa;
use App\Models\Boleto; // Asegúrate de importar el modelo Boleto
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf as PDFFacade;

class LoteController extends Controller
{
    public function index(Request $request, Rifa $rifa)
    {
        $query = $rifa->boletos();

        if ($request->has('search') && $request->search != '') {
            $query->where('folio', 'like', '%' . $request->search . '%');
        }

        if ($request->has('estado') && $request->estado != '') {
            if($request->estado == 'ganadores') {
                $query->where('es_ganador', true);
            } else {
                $query->where('estado', $request->estado);
            }
        }

        $boletos = $query->paginate(50);

        return view('admin.lotes', compact('rifa', 'boletos'));
    }

    public function imprimir(Request $request, Rifa $rifa)
    {
        // Aumentamos el tiempo por si son muchos boletos
        set_time_limit(300); 

        // 1. CAPTURAR EL COLOR
        // Si la URL trae ?color=e9c6fc, le ponemos el # -> #e9c6fc
        // Si no trae nada, usamos tu verde (#d7ffc1) por defecto.
        $color = $request->has('color') ? '#' . $request->color : '#d7ffc1';

        $boletos = $rifa->boletos()->get();

        // 2. PASAR EL COLOR A LA VISTA
        // Agregamos 'color' al compact
        $pdf = PDFFacade::loadView('admin.pdf.boletos', compact('rifa', 'boletos', 'color'));

        return $pdf->stream("lote-{$rifa->nombre}.pdf");
    }

// 
public function imprimirIndividual(Request $request, Boleto $boleto)
{
    // 1. CAPTURAR EL COLOR
    $color = $request->has('color') ? '#' . $request->color : '#d7ffc1';

    // Cargamos los datos de la rifa
    $boleto->load('rifa');

    // 2. EL TRUCO MAESTRO:
    // Convertimos el boleto único en una Colección (una lista de 1)
    // Así la variable $boletos existirá en la vista
    $boletos = collect([$boleto]);

    // 3. PASAMOS 'boletos' (PLURAL) A LA VISTA
    $pdf = PDFFacade::loadView('admin.pdf.boleto_individual', compact('boletos', 'color'));
    
    return $pdf->stream("boleto-{$boleto->folio}.pdf");
}
}