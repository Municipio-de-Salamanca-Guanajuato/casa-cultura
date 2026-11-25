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
        Schema::table('alumnos', function (Blueprint $table) {
            // Cambiar la longitud del campo CURP a 21 caracteres
            $table->string('curp', 21)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alumnos', function (Blueprint $table) {
            // Revertir el campo CURP a su longitud original (18 caracteres)
            $table->string('curp', 18)->change();
        });
    }
};
