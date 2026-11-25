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
        Schema::create('conceptosxrenta', function (Blueprint $table) {
            $table->id('idcxrenta');
            $table->string('namecxrenta', 300);   // Nombre del concepto
            $table->decimal('costo', 10, 2);     // Costo con 2 decimales
            $table->char('estatus', 1);          // Estatus activo/inactivo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conceptosxrenta');
    }
};
