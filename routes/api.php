<?php

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(ApiController::class)->group(function () {
    // API CONSULTA DE ALUMNOS
    Route::get('apiConsulta', 'index');
    Route::get('/apiConsulta/ordenespagos/{idinscripcion}', 'apiConsultaOpagos');
    Route::post('/apiConsulta/recepcionpagos', 'apiRpagos');
    
    // SUGA
    Route::get('/apiConsulta/suga', 'apiSuga');
    Route::get('/apiConsulta/suga/ordenes/{idopago}', 'apiSugaOrdenes');
    Route::get('/apiConsulta/suga/ordenes-pagadas', 'apiSugaPagadas');
    Route::get('/apiConsulta/suga/ordenes-pendientes', 'apiSugaPendientes');
    Route::get('/apiConsulta/suga/ordenes-canceladas', 'apiSugaCanceladas');

    // API CONSULTA RENTAS
    // Route::get('apiConsultaRentas', 'index2');
    // Route::get('/apiConsultaRentas/ordenespagos/{idrenta}', 'apiConsultaOpRentas');
    // Route::post('/apiConsultaRentas/recepcionpagos', 'apiOpagos');
});