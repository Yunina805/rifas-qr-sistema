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
        Schema::table('boletos', function (Blueprint $table) {
        $table->dropForeign(['vendedor_id']); // Elimina la relación vieja
        $table->foreign('vendedor_id')->references('id')->on('users'); // Crea la nueva
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
