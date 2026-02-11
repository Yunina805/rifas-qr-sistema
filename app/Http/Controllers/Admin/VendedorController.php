<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Rifa;
use App\Models\Boleto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class VendedorController extends Controller
{
public function index()
{
    $vendedores = User::where('role', 'vendedor')
        // Cargamos los boletos y su rifa asociada
        ->with(['boletos.rifa']) 
        ->withCount([
            'boletos as total_asignados',
            'boletos as total_vendidos' => function ($query) {
                $query->where('estado', 'vendido');
            },
            'boletos as total_pendientes' => function ($query) {
                $query->where('estado', 'disponible');
            }
        ])
        ->latest()
        ->get();

    $rifas = Rifa::where('estado', 'activa')->get();

    return view('admin.vendedores.index', compact('vendedores', 'rifas'));
}

    // --- CREAR ---
    public function store(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'alias'    => ['nullable', 'string', 'max:100'], // Nuevo
            'telefono' => ['nullable', 'string', 'max:20'],  // Nuevo
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name'     => $request->name,
            'alias'    => $request->alias,
            'telefono' => $request->telefono,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'vendedor',
            'activo'   => true,
        ]);

        return back()->with('success', 'Vendedor registrado correctamente.');
    }

    // --- EDITAR ---
    public function update(Request $request, User $user) // Recibimos el ID pero Laravel busca el User
    {
        // Validación un poco diferente (el email unique debe ignorar al usuario actual)
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'alias'    => ['nullable', 'string', 'max:100'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'email'    => ['required', 'email', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()], // Nullable: solo si quiere cambiarla
        ]);

        $data = [
            'name'     => $request->name,
            'alias'    => $request->alias,
            'telefono' => $request->telefono,
            'email'    => $request->email,
        ];

        // Solo actualizamos password si escribieron algo
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return back()->with('success', 'Datos del vendedor actualizados.');
    }

    // --- ELIMINAR ---
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->role !== 'vendedor') {
            return back()->with('error', 'No puedes eliminar administradores desde aquí.');
        }

        // Opcional: Verificar si tiene ventas antes de borrar
        if ($user->boletos()->where('estado', 'vendido')->exists()) {
            return back()->with('error', 'No se puede eliminar: Este vendedor ya tiene boletos vendidos.');
        }

        // Desvincular boletos no vendidos antes de borrar
        Boleto::where('vendedor_id', $user->id)->where('estado', '!=', 'vendido')->update(['vendedor_id' => null]);

        $user->delete();

        return back()->with('success', 'Vendedor eliminado del sistema.');
    }

    // --- ASIGNAR (Se mantiene igual) ---
    public function asignar(Request $request)
    {
        $request->validate([
            'vendedor_id' => 'required|exists:users,id',
            'rifa_id'     => 'required|exists:rifas,id',
            'cantidad'    => 'required|numeric|min:1',
            'tipo_asignacion' => 'required'
        ]);

        $query = Boleto::where('rifa_id', $request->rifa_id)
                       ->where('estado', 'disponible')
                       ->whereNull('vendedor_id');

        if($request->tipo_asignacion == 'rango') {
            $query->whereBetween('folio', [$request->folio_inicial, $request->folio_final]);
        } else {
            $query->inRandomOrder()->limit($request->cantidad);
        }

        $afectados = $query->update(['vendedor_id' => $request->vendedor_id]);

        if ($afectados == 0) {
            return back()->with('error', 'No hay boletos disponibles con esos criterios.');
        }

        return back()->with('success', "Se asignaron $afectados boletos correctamente.");
    }
}