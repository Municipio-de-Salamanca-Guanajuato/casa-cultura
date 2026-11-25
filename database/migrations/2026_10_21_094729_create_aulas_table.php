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
        Schema::create('aulas', function (Blueprint $table) {
            $table->id('idaula');
            $table->unsignedBigInteger('idedificio');
            $table->string('nombre', 100);
            $table->char('estatus', 1)->default('1');
            $table->timestamps();

            // El nombre del aula no se repite dentro del mismo edificio
            $table->unique(['idedificio', 'nombre']);

            $table->foreign('idedificio')->references('idedificio')->on('edificios')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aulas', function (Blueprint $table) {
            $table->dropForeign(['idedificio']);
        });

        Schema::dropIfExists('aulas');
    }
};
