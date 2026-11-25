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
        Schema::create('taller_horarios', function (Blueprint $table) {
            $table->id('idhorario');
            $table->unsignedBigInteger('idtaller');
            $table->unsignedBigInteger('idaula');
            $table->unsignedTinyInteger('dia'); // 1 = Lunes ... 6 = Sábado
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->char('estatus', 1)->default('1');
            $table->timestamps();

            // Para buscar choques por aula y día, y los horarios de un taller
            $table->index(['idaula', 'dia']);

            $table->foreign('idtaller')->references('idtaller')->on('talleres')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('idaula')->references('idaula')->on('aulas')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('taller_horarios', function (Blueprint $table) {
            $table->dropForeign(['idtaller']);
            $table->dropForeign(['idaula']);
        });

        Schema::dropIfExists('taller_horarios');
    }
};
