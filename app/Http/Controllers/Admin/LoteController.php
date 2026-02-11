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

    // Imprimir todo el lote de la rifa
    public function imprimir(Rifa $rifa)
    {
        set_time_limit(300); 
        $boletos = $rifa->boletos()->get();
        $pdf = PDFFacade::loadView('admin.pdf.boletos', compact('rifa', 'boletos'));
        return $pdf->stream("lote-{$rifa->nombre}.pdf");
    }

    // NUEVA FUNCIÓN: Imprimir solo un boleto específico
    public function imprimirIndividual(Boleto $boleto)
    {
        // Cargamos la relación de la rifa para mostrar el nombre y fecha en el PDF
        $boleto->load('rifa');

        // Generamos el PDF. Te sugiero crear una vista 'boleto_individual.blade.php' 
        // optimizada para una sola hoja o formato pequeño.
        $pdf = PDFFacade::loadView('admin.pdf.boleto_individual', compact('boleto'));
        
        // Retornamos el stream para que se abra en el navegador con un nombre de archivo claro
        return $pdf->stream("boleto-{$boleto->folio}.pdf");
    }
}