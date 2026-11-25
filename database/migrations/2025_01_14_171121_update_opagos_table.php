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
            $table->decimal('montototal', 10, 2)->change(); // Cambiar el tipo de dato
            $table->char('mespago', 2)->nullable()->after('idopago');
            $table->char('estatuspago', 1)->after('mespago');
            $table->date('fechalimite')->nullable()->after('estatuspago'); 
            $table->unsignedBigInteger('idgeneral')->after('idrenta');
            $table->unsignedBigInteger('idconcepto')->after('idgeneral');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('opagos', function (Blueprint $table) {
            $table->string('montototal', 20)->change(); // Revertir el cambio
            $table->dropColumn('mespago');
            $table->dropColumn('estatuspago');
            $table->dropColumn('fechalimite');
            $table->dropColumn('idgeneral');
            $table->dropColumn('idconcepto');
        });
    }
};
