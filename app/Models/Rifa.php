<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rifa extends Model
{
    protected $table = 'rifas';

    protected $fillable = [
        'nombre',
        'sede',
        'fecha_inicio',
        'fecha_fin',
        'precio_boleto',
        'costo_boleto',
        'total_boletos',
        'boletos_vendidos',
        'estado',
    ];

    /*
    |--------------------------------------------------------------------------
    | Estados de la rifa
    |--------------------------------------------------------------------------
    */

    public function activar()
    {
        $this->estado = 'activa';
        $this->save();
    }

    public function finalizar()
    {
        $this->estado = 'finalizada';
        $this->save();
    }

    public function lotes()
    {
        return $this->hasMany(Lote::class);
    }

}
