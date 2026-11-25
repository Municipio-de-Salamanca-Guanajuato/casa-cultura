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
            // Renombrar 'folio' a 'foliocajas'
            $table->renameColumn('folio', 'foliocajas');

            // Agregar el campo 'foliocc' después de 'montototal'
            $table->string('foliocc', 50)->after('montototal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('opagos', function (Blueprint $table) {
            // Revertir los cambios
            $table->renameColumn('foliocajas', 'folio');
            $table->dropColumn('foliocc');
        });
    }
};
