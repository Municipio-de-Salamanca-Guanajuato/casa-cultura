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
        Schema::create('rentas', function (Blueprint $table) {
            $table->id('idrenta');
            $table->dateTime('fechainicio');
            $table->dateTime('fechafin');
            $table->char('estatus', length: 1);
            $table->unsignedBigInteger('idciudadano');
            $table->unsignedBigInteger('idespacio');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rentas');
    }
};
