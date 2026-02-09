<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rifa;
use App\Models\Boleto;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class RifaController extends Controller
{
    public function index()
    {
        $rifas = Rifa::latest()->get();
        return view('admin.rifas', compact('rifas'));
    }

    public function store(Request $request)
    {
        // 1. VALIDACIÓN
        $request->validate([
            'nombre'        => 'required|string',
            'total_boletos' => 'required|integer|min:1',
            'precio_boleto' => 'required|numeric',
            'premios'       => 'nullable|array',
            // CAMBIO: Usamos 'nullable' para que NO falle si la fila de premios va vacía
            'premios.*.cantidad' => 'nullable|integer|min:1',
            'premios.*.monto'    => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // 2. Crear la Rifa
            $rifa = Rifa::create([
                'nombre'        => $request->nombre,
                'sede'          => $request->sede,
                'total_boletos' => $request->total_boletos,
                'precio_boleto' => $request->precio_boleto,
                'costo_boleto'  => $request->costo_boleto,
                'estado'        => 'activa', 
            ]);

            // 3. Lógica de Boletos
            $poolDePremios = [];
            $totalBoletos = intval($request->total_boletos);
            $boletosAsignados = 0;

            // Procesar Premios
            if ($request->has('premios')) {
                foreach ($request->premios as $premioData) {
                    // CAMBIO: Ignoramos filas vacías para evitar errores
                    if (empty($premioData['cantidad']) || empty($premioData['monto'])) {
                        continue; 
                    }

                    $cantidad = intval($premioData['cantidad']);
                    $monto    = floatval($premioData['monto']);
                    
                    for ($i = 0; $i < $cantidad; $i++) {
                        if ($boletosAsignados < $totalBoletos) {
                            $poolDePremios[] = ['es_ganador' => true, 'premio' => $monto];
                            $boletosAsignados++;
                        }
                    }
                }
            }

            // Rellenar Perdedores
            $restantes = $totalBoletos - $boletosAsignados;
            for ($i = 0; $i < $restantes; $i++) {
                $poolDePremios[] = ['es_ganador' => false, 'premio' => 0];
            }

            shuffle($poolDePremios);

            // Generar Datos Masivos
            $datosParaInsertar = [];
            foreach ($poolDePremios as $index => $configuracion) {
                $folio = str_pad($index + 1, strlen((string)$totalBoletos), '0', STR_PAD_LEFT);
                $datosParaInsertar[] = [
                    'rifa_id'     => $rifa->id,
                    'folio'       => $folio,
                    'codigo_qr'   => Str::uuid()->toString(),
                    'es_ganador'  => $configuracion['es_ganador'],
                    'premio'      => $configuracion['premio'],
                    'estado'      => 'disponible',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];
            }

            foreach (array_chunk($datosParaInsertar, 500) as $chunk) {
                Boleto::insert($chunk);
            }

            DB::commit();
            return redirect()->route('admin.rifas')->with('success', 'Rifa creada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            // IMPORTANTE: Esto te mostrará el error real en la pantalla
            return back()->withErrors(['msg' => 'Error del sistema: ' . $e->getMessage()])->withInput();
        }
    }

    // ... (El resto de tus métodos index, edit, update, lotes se quedan igual)
    // Solo asegúrate de incluir edit, update, activar, finalizar y lotes aquí abajo.
    public function edit(Rifa $rifa) { return response()->json($rifa); }
    
    public function update(Request $request, Rifa $rifa) {
        $request->validate([
            'nombre' => 'required|string', 'total_boletos' => 'required|integer', 
            'precio_boleto' => 'required|numeric', 'costo_boleto' => 'required|numeric'
        ]);
        $rifa->update($request->all());
        return redirect()->route('admin.rifas')->with('success', 'Actualizada');
    }
    
    public function activar(Rifa $rifa) { $rifa->update(['estado'=>'activa']); return back(); }
    public function finalizar(Rifa $rifa) { $rifa->update(['estado'=>'finalizada']); return back(); }
    
    public function lotes(Request $request, Rifa $rifa) {
        $query = $rifa->boletos();
        if ($request->has('search')) $query->where('folio', 'like', '%'.$request->search.'%');
        if ($request->has('estado') && $request->estado) $query->where('estado', $request->estado);
        $boletos = $query->paginate(50);
        return view('admin.lotes', compact('rifa', 'boletos'));
    }

    // ==========================================
    // ESCÁNER Y VALIDACIÓN
    // ==========================================
    
    // 1. Mostrar la vista de la cámara
    public function scanView()
    {
        return view('admin.scan');
    }

    // 2. Procesar el código leído (AJAX)
    public function validarBoleto(Request $request)
    {
        $codigo = $request->input('codigo_qr');

        // Buscar el boleto por su código único (UUID)
        $boleto = Boleto::where('codigo_qr', $codigo)->with('rifa')->first();

        if (!$boleto) {
            return response()->json([
                'success' => false,
                'message' => 'Código no encontrado en el sistema.'
            ]);
        }

        // Si existe, preparamos la respuesta
        return response()->json([
            'success' => true,
            'boleto' => [
                'folio' => $boleto->folio,
                'estado' => ucfirst($boleto->estado),
                'es_ganador' => $boleto->es_ganador,
                'premio' => $boleto->premio,
                'rifa_nombre' => $boleto->rifa->nombre,
                'created_at' => $boleto->created_at->format('d/m/Y')
            ]
        ]);
    }
}