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
        Schema::table('espacios', function (Blueprint $table) {
            // Renombrar el campo name a nameroom
            $table->renameColumn('name', 'nameroom');
            // Aumentar la longitud del campo nameroom a 300
            $table->string('nameroom', 300)->change();
            // Cambiar el tipo de campo cupo a smallint(5)
            $table->smallInteger('cupo')->unsigned()->change();
            // Cambiar el tipo de campo costo a decimal(10, 2)
            $table->decimal('costo', 10, 2)->change();
            // Eliminar las columnas 'descripcion'
            $table->dropColumn('descripcion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('espacios', function (Blueprint $table) {
            // Revertir el nombre del campo a name
            $table->renameColumn('nameroom', 'name');
            // Revertir el tamaño del campo name a 200 caracteres
            $table->string('name', 200)->change();
            // Revertir los campos a sus tipos originales
            $table->string('cupo', 30)->change();
            $table->string('costo', 15)->change();
            // Restaurar la columna 'descripcion'
            $table->string('descripcion', 250)->nullable();
        });
    }
};
