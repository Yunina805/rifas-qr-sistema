<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Boleto extends Model
{
    use HasFactory;

    protected $fillable = [
        'rifa_id', 
        'folio', 
        'codigo_qr', 
        'es_ganador', 
        'premio', 
        'estado',
        // Datos de Venta
        'cliente_nombre', 
        'cliente_telefono', 
        'fecha_venta',
        'vendedor_id' 
    ];

    public function rifa()
    {
        return $this->belongsTo(Rifa::class);
    }

    
    public function vendedor()
    {
        // Ahora un boleto pertenece a un Usuario (que tiene el rol de vendedor)
        return $this->belongsTo(User::class, 'vendedor_id');
    }
}