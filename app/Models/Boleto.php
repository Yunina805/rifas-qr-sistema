<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Boleto extends Model
{
    protected $fillable = ['rifa_id', 'folio', 'codigo_qr', 'es_ganador', 'premio', 'estado','vendedor_id'];

    // Relación
    public function vendedor()
    {
        return $this->belongsTo(Vendedor::class);
    }
}

