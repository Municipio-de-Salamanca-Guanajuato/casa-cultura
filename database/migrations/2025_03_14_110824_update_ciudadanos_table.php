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
        Schema::table('ciudadanos', function (Blueprint $table) {
            // Eliminar las columnas que ya no necesitas
            $table->dropColumn(['curp', 'apellidop', 'apellidom', 'direccion']);

            // Aumentar la longitud del campo 'name' a 200 caracteres
            $table->string('name', 300)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ciudadanos', function (Blueprint $table) {
            // Restaurar las columnas eliminadas
            $table->string('curp', 18)->nullable();
            $table->string('apellidop', 20)->nullable();
            $table->string('apellidom', 20)->nullable();
            $table->string('direccion', 100)->nullable();

            // Revertir el tamaño del campo 'name' a 70 caracteres
            $table->string('name', 70)->change();
        });
    }
};
