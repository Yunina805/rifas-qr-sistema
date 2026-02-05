<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    Schema::create('lotes', function (Blueprint $table) {
        $table->id();

        $table->foreignId('rifa_id')
            ->constrained()
            ->cascadeOnDelete();

        $table->string('codigo'); // Ej: Lote #001
        $table->integer('folio_inicio');
        $table->integer('folio_fin');

        $table->integer('total_boletos');

        $table->enum('estado', [
            'almacen',
            'asignado',
            'agotado'
        ])->default('almacen');

        $table->string('asignado_a')->nullable(); // Vendedor (luego será relación)

        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lotes');
    }
};
