<?php

use App\Exports\PaseListaExport;
use App\Exports\ReporteGeneralAlumnosExport;
use App\Exports\ReporteRentasExport;
use App\Http\Controllers\AdminOpagoController;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\AulaController;
use App\Http\Controllers\CiudadanoController;
use App\Http\Controllers\ConceptoController;
use App\Http\Controllers\ConceptoXRentaController;
use App\Http\Controllers\EdificioController;
use App\Http\Controllers\EspacioController;
use App\Http\Controllers\HistoricoController;
use App\Http\Controllers\InscripcionController;
use App\Http\Controllers\MaestroController;
use App\Http\Controllers\OpagoController;
use App\Http\Controllers\RentaController;
use App\Http\Controllers\TallerController;
use App\Http\Controllers\TarjetonController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes(
    [
        'register' => false, // Registration Routes...
        // Sin recuperación por correo: la contraseña se cambia dentro del sistema o la restablece el administrador
        'reset' => false, // Password Reset Routes...
        /*'verify' => false, // Email Verification Routes...*/
    ]
);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::controller(UserController::class)->group(function () {
    Route::post('/user/updatepassword', 'actUser')->name('actUser')->middleware('auth');
});

Route::get('/exportar-reporte-general', function () {
    $fecha = date('Y-m-d');
    return Excel::download(new ReporteGeneralAlumnosExport, "ReporteGeneral-{$fecha}.xlsx");
})->middleware('auth');

Route::get('/exportar-pase-lista/{idtaller}/{nametaller}', function ($idtaller, $nametaller) {
    $fecha = date('Y-m-d'); // Obtener la fecha actual en formato YYYY-MM-DD
    return Excel::download(new PaseListaExport($idtaller, $nametaller), "PaseLista-{$nametaller}-({$fecha}).xlsx");
})->middleware('auth');

// Route::get('/exportar-reporte-rentas', function () {
//     $fecha = date('Y-m-d'); // Ejemplo: 2025-04-11
//     return Excel::download(new ReporteRentasExport, "ReporteRentas-({$fecha}).xlsx");
// });

// Todas las consultas /ajaxConsulta requieren sesión iniciada
Route::controller(AjaxController::class)->middleware('auth')->group(function () {
    // Alumnos
    // Route::get('/ajaxConsulta/Alumnos', 'tblalumnos')->name('tblalumnos');
    // Route::get('/ajaxConsulta/verificarcurp/alumno', 'ajaxConsultaCurp')->name('ajaxConsultaCurp');
    Route::get('/ajaxConsulta/verificarcurp/agregar', 'ajaxConsultaCurpAgregar')->name('ajaxConsultaCurpAgregar');
    Route::get('/ajaxConsulta/verificarcurp/editar', 'ajaxConsultaCurpEditar')->name('ajaxConsultaCurpEditar');
    Route::get('/ajaxConsulta/Alumnos/{estatus}', 'ajaxConsultaAlumnos')->name('ajaxConsultaAlumnos');
    Route::get('/ajaxConsulta/edit/{idalumno}/alumno', 'ajaxEditAlumno')->name('ajaxEditAlumno');
    // Talleres
    // Route::get('/ajaxConsulta/Talleres', 'tbltalleres')->name('tbltalleres');
    Route::get('/ajaxConsulta/verificartaller', 'ajaxConsultaTallerAgregar')->name('ajaxConsultaTallerAgregar');
    Route::get('/ajaxConsulta/verificartaller/{idtaller}', 'ajaxConsultaTallerEditar')->name('ajaxConsultaTallerEditar');
    Route::get('/ajaxConsulta/Talleres/{estatus}', 'ajaxConsultaTalleres')->name('ajaxConsultaTalleres');
    Route::get('/ajaxConsulta/edit/{idtaller}/taller', 'ajaxEditTaller')->name('ajaxEditTaller');
    // Inscripciones
    Route::get('/ajaxConsulta/Inscripciones/mostrar', 'ajaxReadInscripcion')->name('ajaxReadInscripcion');
    Route::get('/ajaxConsulta/Inscripciones', 'tblinscripcion')->name('tblinscripcion');
    Route::get('/ajaxConsulta/Inscripciones/{estatus}', 'ajaxConsultaInscripciones')->name('ajaxConsultaInscripciones');
    Route::get('/ajaxConsulta/edit/{idinscripcion}/inscripcion', 'ajaxEditInscripcion')->name('ajaxEditInscripcion');
    // Admin Ordenes de Pago
    Route::get('/ajaxConsulta/AdminOpagos', 'tbladminopagos')->name('tbladminopagos');
    Route::get('/ajaxConsulta/edit/{idgeneral}/adminopago', 'ajaxEditAdminOp')->name('ajaxEditAdminOp');

    // Conceptos
    // Route::get('/ajaxConsulta/Conceptos', 'tblconceptos')->name('tblconceptos');
    Route::get('/ajaxConsulta/Conceptos/{estatus}', 'ajaxConsultaConcepto')->name('ajaxConsultaConcepto');
    Route::get('/ajaxConsulta/edit/{idconcepto}/concepto', 'ajaxEditConcepto')->name('ajaxEditConcepto');

    // Orden de Pago Alumno Inscripcion
    Route::get('/ajaxConsulta/OpagosInscripciones', 'tblopinsc')->name('tblopinsc');
    Route::get('/ajaxConsulta/{idopago}/opagoalumnoinsc', 'ajaxShowOpagoAlumnoInsc')->name('ajaxShowOpagoAlumnoInsc');
    // Orden de Pago Alumno Mensualidad
    Route::get('/ajaxConsulta/OpagosMensualidades', 'tblopmens')->name('tblopmens');
    Route::get('/ajaxConsulta/{idinscripcion}/opagoalumnomens', 'ajaxShowOpagoAlumnoMens')->name('ajaxShowOpagoAlumnoMens');

    // Orden de Pago Reposición de Tarjetón
    Route::get('/ajaxConsulta/Tarjeton/mostrar', 'ajaxReadTarjeton')->name('ajaxReadTarjeton');
    Route::get('/ajaxConsulta/OpagosRepoTarjeton', 'tbloprepotarjeton')->name('tbloprepotarjeton');
    Route::get('/ajaxConsulta/OpagosRepoTarjeton/{estatus}', 'ajaxConsultaTarjeton')->name('ajaxConsultaTarjeton');
    Route::get('/ajaxConsulta/{idopago}/opagoalumnorepotarjeton', 'ajaxShowOpagoAlumnoRepoTarjeton')->name('ajaxShowOpagoAlumnoRepoTarjeton');

    // Route::get('/ajaxConsulta/{idopago}/opagorepotarjeton', 'ajaxShowOpagoRepoTarjeton')->name('ajaxShowOpagoRepoTarjeton');

    // Historico Ordenes de Pago Alumnos
    Route::get('/ajaxConsulta/Alumnos-historico', 'tblhistorico')->name('tblhistorico');
    Route::get('/ajaxConsulta/{idinscripcion}/historico-inscripcion', 'ajaxShowHistoricoIns')->name('ajaxShowHistoricoIns');
    Route::get('/ajaxConsulta/{idinscripcion}/historico-mensualidades', 'ajaxShowHistoricoMens')->name('ajaxShowHistoricoMens');

    // Conceptos por Renta
    Route::get('/ajaxConsulta/ConceptosxRenta/{estatus}', 'ajaxConsultaCxrenta')->name('ajaxConsultaCxrenta');
    Route::get('/ajaxConsulta/edit/{idcxrenta}/conceptoxrenta', 'ajaxEditCxrenta')->name('ajaxEditCxrenta');

    // Ciudadanos
    // Route::get('/ajaxConsulta/Talleres', 'tbltalleres')->name('tbltalleres');
    // Route::get('/ajaxConsulta/verificartaller', 'ajaxConsultaTallerAgregar')->name('ajaxConsultaTallerAgregar');
    // Route::get('/ajaxConsulta/verificartaller/{idtaller}', 'ajaxConsultaTallerEditar')->name('ajaxConsultaTallerEditar');
    // Route::get('/ajaxConsulta/Ciudadanos/{estatus}', 'ajaxConsultaCiudadanos')->name('ajaxConsultaCiudadanos');
    // Route::get('/ajaxConsulta/edit/{idciudadano}/ciudadano', 'ajaxEditCiudadano')->name('ajaxEditCiudadano');

    // Espacios
    Route::get('/ajaxConsulta/Espacios/{estatus}', 'ajaxConsultaEspacios')->name('ajaxConsultaEspacios');
    Route::get('/ajaxConsulta/edit/{idespacio}/espacio', 'ajaxEditEspacio')->name('ajaxEditEspacio');

    // Edificios
    Route::get('/ajaxConsulta/Edificios/{estatus}', 'ajaxConsultaEdificios')->name('ajaxConsultaEdificios')->middleware('auth');
    Route::get('/ajaxConsulta/edit/{idedificio}/edificio', 'ajaxEditEdificio')->name('ajaxEditEdificio')->middleware('auth');

    // Aulas
    Route::get('/ajaxConsulta/Aulas/{estatus}', 'ajaxConsultaAulas')->name('ajaxConsultaAulas')->middleware('auth');
    Route::get('/ajaxConsulta/edit/{idaula}/aula', 'ajaxEditAula')->name('ajaxEditAula')->middleware('auth');

    // Choques de horario (aula o maestro) con otros talleres
    Route::get('/ajaxConsulta/horarios/choques', 'ajaxChoquesHorario')->name('ajaxChoquesHorario')->middleware('auth');
    // Talleres que le chocan a un alumno (misma persona por CURP, incluyendo registros -1, -02...)
    Route::get('/ajaxConsulta/horarios/choques-alumno/{idalumno}', 'ajaxChoquesAlumno')->name('ajaxChoquesAlumno')->middleware('auth');

    // Maestros
    Route::get('/ajaxConsulta/Maestros/{estatus}', 'ajaxConsultaMaestros')->name('ajaxConsultaMaestros')->middleware('auth');
    Route::get('/ajaxConsulta/edit/{idmaestro}/maestro', 'ajaxEditMaestro')->name('ajaxEditMaestro')->middleware('auth');

    // Rentas
    Route::get('/ajaxConsulta/Rentas/mostrar', 'ajaxReadRenta')->name('ajaxReadRenta');
    Route::get('/ajaxConsulta/Rentas/{estatus}', 'ajaxConsultaRenta')->name('ajaxConsultaRenta');

    // Orden de Pago Ciudadanos Rentas
    Route::get('/ajaxConsulta/OpagosRentas', 'tbloprentas')->name('tbloprentas');
    Route::get('/ajaxConsulta/{idrenta}/opagociudadanorenta', 'ajaxShowOpagoCiudadanoRenta')->name('ajaxShowOpagoCiudadanoRenta');
});

Route::controller(AlumnoController::class)->group(function () {
    Route::get('/alumno', 'index')->name('alumno')->middleware('auth');
    Route::post('/alumno/guardar', 'saveAlumno')->name('saveAlumno')->middleware('auth');
    Route::post('/alumno/actAlumno', 'actAlumno')->name('actAlumno')->middleware('auth');
    Route::get('/upAlumno/{idalumno}/{estatus}', 'estAlumno')->name('estAlumno')->middleware('auth');
});

Route::controller(TallerController::class)->group(function () {
    Route::get('/taller', 'index')->name('taller')->middleware('auth');
    Route::post('/taller/guardar', 'saveTaller')->name('saveTaller')->middleware('auth');
    Route::post('/taller/actTaller', 'actTaller')->name('actTaller')->middleware('auth');
    Route::get('/upTaller/{idtaller}/{estatus}', 'estTaller')->name('estTaller')->middleware('auth');
    Route::get('/talleres/preview-bajas', 'previewBajasTalleres')->name('previewBajasTalleres')->middleware('auth');
    Route::post('/talleres/bajas', 'bajaMasivaTalleres')->name('bajaMasivaTalleres')->middleware('auth');
    Route::get('/talleres/exportar-horarios', 'exportarHorarios')->name('exportarHorarios')->middleware('auth');
});

Route::controller(InscripcionController::class)->group(function () {
    Route::get('/inscripcion', 'index')->name('inscripcion')->middleware('auth');
    Route::post('/inscripcion/guardar', 'saveInscripcion')->name('saveInscripcion')->middleware('auth');
    Route::post('/inscripcion/actInscripcion', 'actInscripcion')->name('actInscripcion')->middleware('auth');
    Route::get('/upInscripcion/{idinscripcion}/{estatus}', 'estInscripcion')->name('estInscripcion')->middleware('auth');
    Route::get('/deleteInscripcion/{idinscripcion}', 'deleteInscripcion')->name('deleteInscripcion')->middleware('auth');
    Route::get('/inscripciones/preview-bajas','previewBajasInscripciones')->name('previewBajasInscripciones')->middleware('auth');
    Route::post('/inscripciones/bajas',  'bajaMasivaInscripciones')->name('bajaMasivaInscripciones')->middleware('auth');
});

Route::controller(AdminOpagoController::class)->group(function () {
    Route::get('/adminopago', 'index')->name('adminopago')->middleware('auth');
    Route::post('/adminopago/guardar', 'saveAdminOp')->name('saveAdminOp')->middleware('auth');
    Route::post('/adminopago/actAdminOpago', 'actAdminOp')->name('actAdminOp')->middleware('auth');
    // Route::get('/upAlumno/{idalumno}/{estatus}', 'estAlumno')->name('estAlumno')->middleware('auth');
});

Route::controller(OpagoController::class)->group(function () {
    // ALUMNOS INCRIPCIONES
    Route::get('/opagoalumno', 'index')->name('opagoalumno')->middleware('auth');
    Route::post('/opagoalumno/guardar', 'saveOpagoAlumno')->name('saveOpagoAlumno')->middleware('auth');

    // CIUDADANOS RENTAS
    Route::get('/opagociudadano', 'index2')->name('opagociudadano')->middleware('auth');
    // Route::post('/inscripcion/actInscripcion', 'actInscripcion')->name('actInscripcion')->middleware('auth');
    // Route::get('/upInscripcion/{idinscripcion}/{estatus}', 'estInscripcion')->name('estInscripcion')->middleware('auth');
    // Route::get('/deleteInscripcion/{idinscripcion}', 'deleteInscripcion')->name('deleteInscripcion')->middleware('auth');
});

Route::controller(HistoricoController::class)->group(function () {
    Route::get('/historico', 'index')->name('historico')->middleware('auth');
});

Route::controller(ConceptoController::class)->group(function () {
    Route::get('/concepto', 'index')->name('concepto')->middleware('auth');
    Route::post('/concepto/guardar', 'saveConcepto')->name('saveConcepto')->middleware('auth');
    Route::post('/concepto/actConcepto', 'actConcepto')->name('actConcepto')->middleware('auth');
    Route::get('/upConcepto/{idconcepto}/{estatus}', 'estConcepto')->name('estConcepto')->middleware('auth');
});

Route::controller(TarjetonController::class)->group(function () {
    Route::get('/tarjeton', 'index')->name('tarjeton')->middleware('auth');
    Route::get('/tarjeton/{idinscripcion}', 'downTarjeton')->name('downTarjeton')->middleware('auth');
    Route::post('/tarjeton/guardar', 'saveTarjeton')->name('saveTarjeton')->middleware('auth');
    // Route::get('/reposicion-tarjeton', 'reposicion')->name('repotarjeton')->middleware('auth');
    // Route::post('/tarjeton/actTarjeton', 'actTarjeton')->name('actTarjeton')->middleware('auth');
    Route::get('/upTarjeton/{idtarjeton}/{estatus}', 'estTarjeton')->name('estTarjeton')->middleware('auth');
});

Route::controller(ConceptoXRentaController::class)->group(function () {
    Route::get('/conceptoxrenta', 'index')->name('conceptoxrenta')->middleware('auth');
    Route::post('/conceptoxrenta/guardar', 'saveCxrenta')->name('saveCxrenta')->middleware('auth');
    Route::post('/conceptoxrenta/actCiudadano', 'actCxrenta')->name('actCxrenta')->middleware('auth');
    Route::get('/upConceptoxrenta/{idcxrenta}/{estatus}', 'estCxrenta')->name('estCxrenta')->middleware('auth');
});

// Route::controller(CiudadanoController::class)->group(function () {
//     Route::get('/ciudadano', 'index')->name('ciudadano')->middleware('auth');
//     Route::post('/ciudadano/guardar', 'saveCiudadano')->name('saveCiudadano')->middleware('auth');
//     Route::post('/ciudadano/actCiudadano', 'actCiudadano')->name('actCiudadano')->middleware('auth');
//     Route::get('/upCiudadano/{idciudadano}/{estatus}', 'estCiudadano')->name('estCiudadano')->middleware('auth');
// });

Route::controller(EspacioController::class)->group(function () {
    Route::get('/espacio', 'index')->name('espacio')->middleware('auth');
    Route::post('/espacio/guardar', 'saveEspacio')->name('saveEspacio')->middleware('auth');
    Route::post('/espacio/actEspacio', 'actEspacio')->name('actEspacio')->middleware('auth');
    Route::get('/upEspacio/{idespacio}/{estatus}', 'estEspacio')->name('estEspacio')->middleware('auth');
});

Route::controller(EdificioController::class)->group(function () {
    Route::get('/edificio', 'index')->name('edificio')->middleware('auth');
    Route::post('/edificio/guardar', 'saveEdificio')->name('saveEdificio')->middleware('auth');
    Route::post('/edificio/actEdificio', 'actEdificio')->name('actEdificio')->middleware('auth');
    Route::get('/upEdificio/{idedificio}/{estatus}', 'estEdificio')->name('estEdificio')->middleware('auth');
});

Route::controller(AulaController::class)->group(function () {
    Route::get('/aula', 'index')->name('aula')->middleware('auth');
    Route::post('/aula/guardar', 'saveAula')->name('saveAula')->middleware('auth');
    Route::post('/aula/actAula', 'actAula')->name('actAula')->middleware('auth');
    Route::get('/upAula/{idaula}/{estatus}', 'estAula')->name('estAula')->middleware('auth');
});

Route::controller(MaestroController::class)->group(function () {
    Route::get('/maestro', 'index')->name('maestro')->middleware('auth');
    Route::post('/maestro/guardar', 'saveMaestro')->name('saveMaestro')->middleware('auth');
    Route::post('/maestro/actMaestro', 'actMaestro')->name('actMaestro')->middleware('auth');
    Route::get('/upMaestro/{idmaestro}/{estatus}', 'estMaestro')->name('estMaestro')->middleware('auth');
});

Route::controller(RentaController::class)->group(function () {
    Route::get('/renta', 'index')->name('renta')->middleware('auth');
    Route::post('/renta/guardar', 'saveRenta')->name('saveRenta')->middleware('auth');
    // Route::post('/espacio/actEspacio', 'actEspacio')->name('actEspacio')->middleware('auth');
    Route::get('/upRenta/{idrenta}/{estatus}', 'estRenta')->name('estRenta')->middleware('auth');
});

