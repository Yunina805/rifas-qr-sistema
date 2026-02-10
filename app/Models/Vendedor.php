<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendedor extends Model
{
    protected $table = 'vendedores';
    protected $fillable = ['nombre', 'telefono', 'alias'];

    // Relación: Un vendedor tiene muchos boletos asignados
    public function boletos()
    {
        return $this->hasMany(Boleto::class);
    }
}