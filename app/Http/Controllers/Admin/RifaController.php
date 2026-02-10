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
                    // Ignoramos filas vacías para evitar errores
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
                // Generar folio con ceros a la izquierda (ej: 001, 050, 500)
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

            // Insertar en lotes de 500 para optimizar memoria
            foreach (array_chunk($datosParaInsertar, 500) as $chunk) {
                Boleto::insert($chunk);
            }

            DB::commit();
            return redirect()->route('admin.rifas')->with('success', 'Rifa creada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['msg' => 'Error del sistema: ' . $e->getMessage()])->withInput();
        }
    }

    // --- MÉTODOS CRUD BÁSICOS ---

    public function edit(Rifa $rifa)
    {
        return response()->json($rifa);
    }

    public function update(Request $request, Rifa $rifa)
    {
        $request->validate([
            'nombre'        => 'required|string',
            'total_boletos' => 'required|integer',
            'precio_boleto' => 'required|numeric',
            'costo_boleto'  => 'required|numeric'
        ]);
        $rifa->update($request->all());
        return redirect()->route('admin.rifas')->with('success', 'Rifa actualizada correctamente.');
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

    public function lotes(Request $request, Rifa $rifa)
    {
        $query = $rifa->boletos();
        if ($request->has('search')) {
            $query->where('folio', 'like', '%' . $request->search . '%');
        }
        if ($request->has('estado') && $request->estado) {
            $query->where('estado', $request->estado);
        }
        $boletos = $query->paginate(50);
        return view('admin.lotes', compact('rifa', 'boletos'));
    }

    // ==========================================
    // ESCÁNER Y VALIDACIÓN (CORREGIDO)
    // ==========================================

    public function scanView()
    {
        return view('admin.scan');
    }

    public function validarBoleto(Request $request)
{
    // 1. Limpieza del código
    $rawCode = $request->input('codigo_qr');
    $codigo = basename($rawCode); 

    // MODIFICACIÓN 1: Cargamos también al 'vendedor'
    $boleto = Boleto::where('codigo_qr', $codigo)->with(['rifa', 'vendedor'])->first();

    if (!$boleto) {
        return response()->json(['success' => false, 'message' => 'Código no encontrado.']);
    }

    // MODIFICACIÓN 2: Preparamos el nombre del vendedor para usarlo en los mensajes
    $nombreVendedor = $boleto->vendedor ? $boleto->vendedor->nombre : 'Venta Directa / Oficina';

    $accion = $request->input('accion'); 

    // ----------------------------------------
    // ACCIÓN 1: VENTA RÁPIDA
    // ----------------------------------------
    if ($accion === 'vender') {
        if ($boleto->estado === 'disponible') {
            $boleto->update([
                'estado'       => 'vendido',
                'fecha_venta'  => now(),
                'cliente_nombre' => 'Venta por Escáner',
                // Nota: No tocamos vendedor_id, respetamos si ya estaba asignado
            ]);

            // Actualizar contador en Rifa (si usas esa columna)
            // $boleto->rifa()->increment('boletos_vendidos'); 

            return response()->json([
                'success' => true,
                'tipo'    => 'venta_exitosa',
                'mensaje' => "¡Venta Registrada!",
                'datos_extra' => "Vendedor: " . $nombreVendedor . " | Costo: $" . number_format($boleto->rifa->precio_boleto, 2),
                'boleto'  => $boleto,
                'vendedor'=> $nombreVendedor // Enviamos el dato al frontend
            ]);
        }

        if ($boleto->estado === 'vendido') {
            return response()->json([
                'success' => false,
                'tipo'    => 'ya_vendido',
                'message' => 'Este boleto YA fue vendido anteriormente por: ' . $nombreVendedor
            ]);
        }
    }

    // ----------------------------------------
    // ACCIÓN 2: ENTREGA DE PREMIO
    // ----------------------------------------
    if ($accion === 'entregar') {
        if ($boleto->es_ganador && $boleto->estado === 'vendido') {
            $boleto->update([
                'estado' => 'entregado',
            ]);

            return response()->json([
                'success' => true,
                'tipo'    => 'entrega_exitosa',
                'mensaje' => "¡Premio entregado correctamente!",
                'boleto'  => $boleto,
                'vendedor'=> $nombreVendedor // Dato útil para saber quién vendió el ganador
            ]);
        }

        if ($boleto->estado === 'entregado') {
            return response()->json([
                'success' => false,
                'tipo'    => 'ya_entregado',
                'message' => 'ALERTA: Este premio YA FUE COBRADO anteriormente.'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'No se puede entregar. El boleto no es ganador o no ha sido vendido.'
        ]);
    }

    // ----------------------------------------
    // RESPUESTA ESTÁNDAR (CONSULTA / ESCANEO)
    // ----------------------------------------
    return response()->json([
        'success' => true,
        'tipo'    => 'consulta',
        'boleto'  => [
            'folio'       => $boleto->folio,
            'estado'      => ucfirst($boleto->estado),
            'es_ganador'  => $boleto->es_ganador,
            'premio'      => $boleto->premio,
            'rifa_nombre' => $boleto->rifa->nombre,
            'precio'      => $boleto->rifa->precio_boleto,
            'vendedor'    => $nombreVendedor // <--- Aquí mostramos quién lo tiene asignado
        ]
    ]);
}
}