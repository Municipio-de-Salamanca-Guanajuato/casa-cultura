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
        Schema::table('rentas', function (Blueprint $table) {
            // Renombrar el campo fechainicio a fechaevento y cambiarlo a tipo date
            $table->renameColumn('fechainicio', 'fechaevento');
            $table->date('fechaevento')->change();
            // Eliminar el campo fechafin
            $table->dropColumn('fechafin');
            // Agregar los nuevos campos
            $table->string('horainicio', 8)->after('fechaevento'); // Tipo varchar(8) para HH:MM:SS
            $table->string('horafin', 8)->after('horainicio');    // Tipo varchar(8)
            $table->string('cantidadxrenta', 255)->nullable()->after('horafin');        // Tipo string
            // Reemplazar el campo idcxrenta por cxrenta (IDs separados por comas)
            $table->string('conceptosxrenta', 255)->nullable()->after('cantidadxrenta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rentas', function (Blueprint $table) {
            // Revertir los cambios
            $table->renameColumn('fechaevento', 'fechainicio');
            $table->dateTime('fechainicio')->change();
            // Volver a agregar el campo fechafin como datetime
            $table->dateTime('fechafin')->nullable();
            // Eliminar los nuevos campos
            $table->dropColumn(['horainicio', 'horafin', 'cantidadxrenta', 'conceptosxrenta']);
        });
    }
};
