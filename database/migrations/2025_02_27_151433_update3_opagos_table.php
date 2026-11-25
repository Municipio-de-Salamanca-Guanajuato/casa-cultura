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
        Schema::table('opagos', function (Blueprint $table) {
            $table->string('rfc', 13)->nullable()->after('fechapago'); // Agregar el campo después de 'fechapago'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('opagos', function (Blueprint $table) {
            $table->dropColumn('rfc'); // Eliminar el campo en caso de rollback
        });
    }
};
