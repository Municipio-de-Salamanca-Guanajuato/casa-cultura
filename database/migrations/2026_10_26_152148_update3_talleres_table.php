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
        Schema::table('talleres', function (Blueprint $table) {
            // Maestro del catálogo (los talleres actuales se quedan en null)
            $table->unsignedBigInteger('idmaestro')->nullable()->after('maestro');
            // El texto viejo del maestro pasa a opcional
            $table->string('maestro', 100)->nullable()->change();

            // Rango de edad (null = sin límite)
            $table->unsignedTinyInteger('edad_min')->nullable()->after('mensualidad');
            $table->unsignedTinyInteger('edad_max')->nullable()->after('edad_min');

            $table->foreign('idmaestro')->references('idmaestro')->on('maestros')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('talleres', function (Blueprint $table) {
            $table->dropForeign(['idmaestro']);
            $table->dropColumn(['idmaestro', 'edad_min', 'edad_max']);
            $table->string('maestro', 100)->nullable(false)->change();
        });
    }
};
