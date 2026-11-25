<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tarjetones', function (Blueprint $table) {
            $table->id('idtarjeton');
            $table->unsignedBigInteger('idinscripcion');
            $table->unsignedBigInteger('idopago')->unique();
            $table->char('estatus', 1)->default('1');
            $table->timestamps();

            $table->foreign('idinscripcion')->references('idinscripcion')->on('inscripciones')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('idopago')->references('idopago')->on('opagos')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tarjetones', function (Blueprint $table) {
            $table->dropForeign(['idinscripcion']);
            $table->dropForeign(['idopago']);
        });

        Schema::dropIfExists('tarjetones');
    }
};
