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
    // 1. Tabla de Vendedores
    Schema::create('vendedores', function (Blueprint $table) {
        $table->id();
        $table->string('nombre');
        $table->string('telefono')->nullable();
        $table->string('alias')->nullable(); // Ej: "El Primo", "Tienda Esquina"
        $table->timestamps();
    });

    // 2. Relación en Boletos (Un boleto puede estar asignado a un vendedor)
    Schema::table('boletos', function (Blueprint $table) {
        $table->foreignId('vendedor_id')->nullable()->constrained('vendedores')->onDelete('set null');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendedores_tables');
    }
};
