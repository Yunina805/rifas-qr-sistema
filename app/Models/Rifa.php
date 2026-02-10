<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rifa extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'sede',
        'fecha_sorteo',
        'total_boletos',
        'precio_boleto',
        'costo_boleto',
        'estado',
        'boletos_vendidos'
    ];

    protected $casts = [
        'precio_boleto' => 'decimal:2',
        'costo_boleto'  => 'decimal:2',
        'total_boletos' => 'integer',
        'boletos_vendidos' => 'integer',
    ];

    /**
     * Relación Principal: Una Rifa tiene muchos Boletos.
     */
    public function boletos()
    {
        return $this->hasMany(Boleto::class);
    }

    public function lotes()
    {
        return $this->boletos();
    }
}