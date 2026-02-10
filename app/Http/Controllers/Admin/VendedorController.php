<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendedor;
use App\Models\Rifa;
use App\Models\Boleto;
use Illuminate\Http\Request;

class VendedorController extends Controller
{
    // Ver lista de vendedores y su rendimiento
    public function index()
    {
        // Cargamos vendedores con el conteo de sus boletos y estado
        $vendedores = Vendedor::withCount([
            'boletos as total_asignados',
            'boletos as total_vendidos' => function ($query) {
                $query->where('estado', 'vendido');
            },
            'boletos as total_por_pagar' => function ($query) {
                $query->where('estado', 'vendido'); // Aquí podrías filtrar si ya te pagaron o no
            }
        ])->get();

        // Necesitamos las rifas para el modal de asignar
        $rifas = Rifa::where('estado', 'activa')->get();

        return view('admin.vendedores.index', compact('vendedores', 'rifas'));
    }

    // Guardar nuevo vendedor
    public function store(Request $request)
    {
        $request->validate(['nombre' => 'required']);
        Vendedor::create($request->all());
        return back()->with('success', 'Vendedor registrado.');
    }

    // Asignar boletos (MÁGICO)
    public function asignar(Request $request)
    {
        $request->validate([
            'vendedor_id' => 'required',
            'rifa_id' => 'required',
            'cantidad' => 'required|numeric|min:1',
            'tipo_asignacion' => 'required' // 'aleatorio' o 'rango'
        ]);

        $rifa = Rifa::find($request->rifa_id);
        
        // Buscamos boletos disponibles de esa rifa que NO tengan vendedor asignado
        $query = Boleto::where('rifa_id', $rifa->id)
                       ->where('estado', 'disponible')
                       ->whereNull('vendedor_id');

        if($request->tipo_asignacion == 'rango') {
            // Ejemplo: Asignar del 001 al 050
            $query->whereBetween('folio', [$request->folio_inicial, $request->folio_final]);
        } else {
            // Aleatorio: Toma X cantidad al azar
            $query->inRandomOrder()->limit($request->cantidad);
        }

        // Ejecutamos la actualización
        $afectados = $query->update(['vendedor_id' => $request->vendedor_id]);

        if ($afectados == 0) {
            return back()->with('error', 'No hay boletos disponibles en ese rango o cantidad.');
        }

        return back()->with('success', "Se asignaron $afectados boletos al vendedor correctamente.");
    }
}