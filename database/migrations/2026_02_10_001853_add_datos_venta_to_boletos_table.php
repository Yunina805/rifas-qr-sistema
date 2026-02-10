<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('boletos', function (Blueprint $table) {
            // Agregamos columnas para saber a quién se vendió y cuándo
            $table->string('cliente_nombre')->nullable()->after('estado');
            $table->string('cliente_telefono')->nullable()->after('cliente_nombre');
            $table->timestamp('fecha_venta')->nullable()->after('cliente_telefono');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('boletos', function (Blueprint $table) {
            //
        });
    }
};
