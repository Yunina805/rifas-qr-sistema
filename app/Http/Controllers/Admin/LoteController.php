<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rifa;
use App\Models\Lote;
use Illuminate\Http\Request;

class LoteController extends Controller
{
    public function index(Rifa $rifa)
    {
        $lotes = $rifa->lotes()->orderBy('id')->get();

        return view('admin.lotes', [
            'rifa' => $rifa,
            'lotes' => $lotes,
        ]);
    }

    public function store(Request $request, Rifa $rifa)
    {
        $request->validate([
            'codigo' => 'required|string',
            'folio_inicio' => 'required|integer',
            'folio_fin' => 'required|integer|gte:folio_inicio',
        ]);

        $total = ($request->folio_fin - $request->folio_inicio) + 1;

        Lote::create([
            'rifa_id' => $rifa->id,
            'codigo' => $request->codigo,
            'folio_inicio' => $request->folio_inicio,
            'folio_fin' => $request->folio_fin,
            'total_boletos' => $total,
            'estado' => 'almacen',
        ]);

        return redirect()->back();
    }
}
