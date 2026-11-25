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
        Schema::table('talleres', function (Blueprint $table) {
            $table->smallInteger('cupo')->unsigned()->change(); // Cambiar cupo a SMALLINT sin signo
            $table->decimal('inscripcion', 10, 2)->change();
            $table->decimal('mensualidad', 10, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('talleres', function (Blueprint $table) {
            $table->string('cupo', 20)->change(); // Revertir cupo a varchar(20)
            $table->string('inscripcion', 10)->change();
            $table->string('mensualidad', 10)->change();
        });
    }
};
