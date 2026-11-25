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
        Schema::create('talleres', function (Blueprint $table) {
            $table->id('idtaller');
            $table->string('nametaller', 200);
            $table->string('maestro', 100);
            $table->date('fechainicio');
            $table->date('fechafin');
            $table->string('cupo', 20);
            $table->string('inscripcion', 10);
            $table->string('mensualidad', 10);
            $table->char('estatus', 1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tallers');
    }
};
