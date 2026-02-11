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
    Schema::table('users', function (Blueprint $table) {
        $table->string('alias')->nullable()->after('name');     // Nombre de la tienda o apodo
        $table->string('telefono')->nullable()->after('email'); // Para WhatsApp
        $table->boolean('activo')->default(true)->after('role'); // Para bloquear acceso sin borrarlo
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
