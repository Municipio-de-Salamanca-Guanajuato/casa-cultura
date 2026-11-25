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
        Schema::table('conceptos', function (Blueprint $table) {
            $table->decimal('costo', 10, 2)->after('concepto')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conceptos', function (Blueprint $table) {
            $table->dropColumn('costo');
        });
    }
};
