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
        Schema::create('opagos', function (Blueprint $table) {
            $table->id('idopago');
            $table->string('montototal', 20);
            $table->string('folio', 50);
            $table->dateTime('fechapago');
            $table->unsignedBigInteger('idinscripcion');
            $table->unsignedBigInteger('idrenta');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opagos');
    }
};
