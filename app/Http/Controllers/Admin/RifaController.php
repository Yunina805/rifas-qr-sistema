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

    // ==========================================
    // CREAR RIFA (CON FECHA Y PREMIOS)
    // ==========================================
    public function store(Request $request)
    {
        // 1. VALIDACIÓN
        $request->validate([
            'nombre'        => 'required|string',
            'sede'          => 'nullable|string',
            'fecha_sorteo'  => 'nullable|date', // <--- NUEVO: Validamos la fecha
            'total_boletos' => 'required|integer|min:1',
            'precio_boleto' => 'required|numeric',
            'costo_boleto'  => 'required|numeric',
            'premios'       => 'nullable|array',
            // Estas reglas validan el array que manda el JS
            'premios.*.cantidad' => 'nullable|integer|min:1',
            'premios.*.monto'    => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // 2. Crear la Rifa
            $rifa = Rifa::create([
                'nombre'        => $request->nombre,
                'sede'          => $request->sede,
                'fecha_sorteo'  => $request->fecha_sorteo, // <--- NUEVO: Guardamos la fecha
                'total_boletos' => $request->total_boletos,
                'precio_boleto' => $request->precio_boleto,
                'costo_boleto'  => $request->costo_boleto,
                'estado'        => 'activa',
            ]);

            // 3. Lógica de Boletos
            $poolDePremios = [];
            $totalBoletos = intval($request->total_boletos);
            $boletosAsignados = 0;

            // Procesar Premios (Si vienen del formulario)
            if ($request->has('premios') && is_array($request->premios)) {
                foreach ($request->premios as $premioData) {
                    
                    // Saltamos filas vacías o mal formadas
                    if (empty($premioData['cantidad']) || empty($premioData['monto'])) {
                        continue;
                    }

                    $cantidad = intval($premioData['cantidad']);
                    $monto    = floatval($premioData['monto']);

                    for ($i = 0; $i < $cantidad; $i++) {
                        if ($boletosAsignados < $totalBoletos) {
                            // Marcamos como ganador (true)
                            $poolDePremios[] = ['es_ganador' => true, 'premio' => $monto];
                            $boletosAsignados++;
                        }
                    }
                }
            }

            // Rellenar el resto con Perdedores
            $restantes = $totalBoletos - $boletosAsignados;
            for ($i = 0; $i < $restantes; $i++) {
                $poolDePremios[] = ['es_ganador' => false, 'premio' => 0];
            }

            // Mezclar aleatoriamente para que los premios queden en cualquier folio
            shuffle($poolDePremios);

            // Generar Datos Masivos para Insertar
            $datosParaInsertar = [];
            $now = now(); 

            foreach ($poolDePremios as $index => $configuracion) {
                // Generar folio con ceros a la izquierda (ej: 001, 010, 100)
                $folio = str_pad($index + 1, strlen((string)$totalBoletos), '0', STR_PAD_LEFT);
                
                $datosParaInsertar[] = [
                    'rifa_id'     => $rifa->id,
                    'folio'       => $folio,
                    'codigo_qr'   => Str::uuid()->toString(),
                    
                    // --- CORRECCIÓN CRÍTICA: Forzamos 1 o 0 para la BD ---
                    'es_ganador'  => $configuracion['es_ganador'] ? 1 : 0, 
                    'premio'      => $configuracion['premio'],
                    
                    'estado'      => 'disponible',
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];
            }

            // Insertar en lotes de 500 (Bulk Insert) para rapidez
            foreach (array_chunk($datosParaInsertar, 500) as $chunk) {
                Boleto::insert($chunk);
            }

            DB::commit();
            return redirect()->route('admin.rifas')->with('success', 'Rifa creada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['msg' => 'Error al generar boletos: ' . $e->getMessage()])->withInput();
        }
    }

    // ==========================================
    // ACTUALIZAR RIFA
    // ==========================================
    public function update(Request $request, Rifa $rifa)
    {
        $request->validate([
            'nombre'        => 'required|string',
            'sede'          => 'nullable|string',
            'fecha_sorteo'  => 'nullable|date', // <--- NUEVO: Validar fecha al editar
            'total_boletos' => 'required|integer',
            'precio_boleto' => 'required|numeric',
            'costo_boleto'  => 'required|numeric'
        ]);

        // Actualizamos todos los campos autorizados en $fillable
        $rifa->update($request->all());
        
        return redirect()->route('admin.rifas')->with('success', 'Rifa actualizada correctamente.');
    }

    // --- MÉTODOS DE ESTADO ---

    public function edit(Rifa $rifa)
    {
        return response()->json($rifa);
    }

    public function activar(Rifa $rifa)
    {
        $rifa->update(['estado' => 'activa']);
        return back();
    }

    public function finalizar(Rifa $rifa)
    {
        $rifa->update(['estado' => 'finalizada']);
        return back();
    }

    // --- LISTADO DE BOLETOS (LOTES) ---

    public function lotes(Request $request, Rifa $rifa)
    {
        $query = $rifa->boletos();
        
        // Filtro por Folio
        if ($request->has('search') && $request->search != '') {
            $query->where('folio', 'like', '%' . $request->search . '%');
        }
        
        // Filtro por Estado (Ganador, Vendido, Disponible)
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

    // ==========================================
    // ESCÁNER Y VALIDACIÓN (CON VENDEDORES)
    // ==========================================

    public function scanView()
    {
        return view('admin.scan');
    }

    public function validarBoleto(Request $request)
    {
        $codigo = $request->codigo_qr;
        $boleto = Boleto::where('codigo_qr', $codigo)->first();

        if (!$boleto) {
            return response()->json(['success' => false, 'message' => 'Boleto no encontrado']);
        }

        $estadoActual = strtolower($boleto->estado);

        // BLOQUEO 1: Si ya fue vendido/utilizado
        if ($estadoActual === 'vendido') {
            return response()->json([
                'success' => true,
                'status_type' => 'utilizado',
                'mensaje' => 'Este boleto ya fue procesado y vendido.',
                'boleto' => [
                    'folio' => $boleto->folio,
                    'rifa_nombre' => $boleto->rifa->nombre,
                    'vendedor' => $boleto->vendedor->name ?? 'N/A'
                ]
            ]);
        }

        // BLOQUEO 2: Si es inválido (Anulado)
        if ($estadoActual === 'invalido') {
            return response()->json([
                'success' => true,
                'status_type' => 'invalido',
                'mensaje' => 'ATENCIÓN: Boleto anulado o no válido.',
                'boleto' => ['folio' => $boleto->folio]
            ]);
        }

        // PASO 2: ACCIÓN DE VALIDAR (Aquí es donde pasa a VENDIDO)
        if ($request->has('confirmar_venta')) {
            $boleto->update([
                'estado' => 'vendido',
                'vendedor_id' => auth()->id(),
                'fecha_venta' => now()
            ]);

            return response()->json([
                'success' => true,
                'status_type' => 'revelacion',
                'mensaje' => '¡Venta registrada con éxito!',
                'boleto' => [
                    'folio' => $boleto->folio,
                    'rifa_nombre' => $boleto->rifa->nombre,
                    'es_ganador' => (bool)$boleto->es_ganador,
                    'premio' => $boleto->premio
                ]
            ]);
        }

        // PASO 1: CONSULTA INICIAL (Disponible)
        return response()->json([
            'success' => true,
            'status_type' => 'disponible',
            'boleto' => [
                'folio' => $boleto->folio,
                'rifa_nombre' => $boleto->rifa->nombre,
                'precio' => $boleto->rifa->precio_boleto,
                'estado' => 'DISPONIBLE'
            ]
        ]);
    }

    
}