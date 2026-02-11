<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
    Schema::table('boletos', function (Blueprint $table) {
        // 1. Eliminamos la clave foránea antigua que apunta a 'vendedores'
        // El nombre del constraint lo saqué de tu error: 'boletos_vendedor_id_foreign'
        $table->dropForeign(['vendedor_id']);

        // 2. Creamos la nueva relación que apunte a la tabla 'users'
        $table->foreign('vendedor_id')
              ->references('id')
              ->on('users')
              ->onDelete('set null'); 
    });
}

public function down()
{
    Schema::table('boletos', function (Blueprint $table) {
        $table->dropForeign(['vendedor_id']);
        // Revertir a como estaba antes por si acaso
        $table->foreign('vendedor_id')->references('id')->on('vendedores');
    });
}
};
