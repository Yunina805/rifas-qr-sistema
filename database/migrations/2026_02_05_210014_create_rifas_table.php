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
    Schema::create('rifas', function (Blueprint $table) {
        $table->id();

        $table->string('nombre');
        $table->string('sede')->nullable();

        $table->dateTime('fecha_inicio')->nullable();
        $table->dateTime('fecha_fin')->nullable();

        $table->decimal('precio_boleto', 10, 2)->default(0);
        $table->decimal('costo_boleto', 10, 2)->default(0);

        $table->integer('total_boletos')->default(0);
        $table->integer('boletos_vendidos')->default(0);

        $table->enum('estado', ['borrador', 'activa', 'finalizada'])->default('borrador');

        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rifas');
    }
};
