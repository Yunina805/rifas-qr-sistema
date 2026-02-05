<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lote extends Model
{
    protected $fillable = [
        'rifa_id',
        'codigo',
        'folio_inicio',
        'folio_fin',
        'total_boletos',
        'estado',
        'asignado_a',
    ];

    public function rifa()
    {
        return $this->belongsTo(Rifa::class);
    }
}
