<?php

namespace App\Http\Controllers;

use App\Models\Boleto;
use Illuminate\Http\Request;

class PublicRifaController extends Controller
{
    public function verificar($codigo)
    {
        // Buscamos el boleto por su código QR único
        $boleto = Boleto::where('codigo_qr', $codigo)->with('rifa')->first();

        // Si alguien inventa un código o escanea mal
        if (!$boleto) {
            abort(404, 'Boleto no encontrado en nuestros registros.');
        }

        // Retornamos la vista pública con los datos del boleto
        return view('public.resultado', compact('boleto'));
    }
}