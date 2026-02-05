<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rifa;
use Illuminate\Http\Request;

class RifaController extends Controller
{
    public function index()
    {
        $rifas = Rifa::latest()->get();
        return view('admin.rifas', compact('rifas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'lugar' => 'required|string|max:255',
            'total_boletos' => 'required|integer|min:1',
        ]);

        Rifa::create($request->all());

        return redirect()->route('admin.rifas');
    }

    public function activar(Rifa $rifa)
    {
        $rifa->activar();
        return back();
    }

    public function finalizar(Rifa $rifa)
    {
        $rifa->finalizar();
        return back();
    }

    public function edit(Rifa $rifa)
{
    return response()->json($rifa);
}

public function update(Request $request, Rifa $rifa)
{
    $rifa->update([
        'nombre' => $request->nombre,
        'sede' => $request->sede,
        'total_boletos' => $request->total_boletos,
    ]);

    return redirect()->route('admin.rifas');
}

}
