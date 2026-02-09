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
    Schema::create('boletos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('rifa_id')->constrained()->onDelete('cascade');
        $table->string('folio'); // Ej: "0001", "0002"
        $table->string('codigo_qr')->unique(); // El hash único para el QR
        $table->boolean('es_ganador')->default(false);
        $table->decimal('premio', 10, 2)->default(0); // Cuánto ganó
        $table->string('estado')->default('disponible'); // disponible, apartado, vendido
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boletos');
    }
};
