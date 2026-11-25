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
        Schema::create('ciudadanos', function (Blueprint $table) {
            $table->id('idciudadano');
            $table->string('name', 70);
            $table->string('apellidop', 20);
            $table->string('apellidom', 20);
            $table->string('curp', 18);
            $table->string('telefono', 15);
            $table->string('direccion', 100);
            $table->char('estatus', length: 1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ciudadanos');
    }
};
