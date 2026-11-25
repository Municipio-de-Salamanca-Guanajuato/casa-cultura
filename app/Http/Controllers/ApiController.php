<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Opago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    public function index()
    {
        $inscripciones = DB::table('inscripciones')
            ->select(
                DB::raw("CONCAT(inscripciones.idinscripcion, '-I') as idinscripcion"),
                DB::raw("CONCAT(alumnos.name, ' ', alumnos.apellidop, ' ', alumnos.apellidom) AS NombreCompleto"),
                'talleres.nametaller'
            )
            ->join('alumnos', 'inscripciones.idalumno', '=', 'alumnos.idalumno')
            ->join('talleres', 'inscripciones.idtaller', '=', 'talleres.idtaller')
            ->where('inscripciones.estatus', 1)
            ->where(function ($query) {
                $query->whereNotExists(function ($subQuery) {
                    $subQuery->select(DB::raw(1))
                        ->from('opagos')
                        ->whereRaw('opagos.idinscripcion = inscripciones.idinscripcion')
                        ->where('opagos.idconcepto', 1)
                        ->where('opagos.estatuspago', 1);
                })
                    ->orWhereExists(function ($subQuery) {
                        $subQuery->select(DB::raw(1))
                            ->from('opagos')
                            ->whereRaw('opagos.idinscripcion = inscripciones.idinscripcion')
                            ->where('opagos.idconcepto', 2)
                            ->where('opagos.estatuspago', 0);
                    })
                    ->orWhereExists(function ($subQuery) {
                        $subQuery->select(DB::raw(1))
                            ->from('opagos')
                            ->join('tarjetones', 'opagos.idopago', '=', 'tarjetones.idopago')
                            ->whereRaw('opagos.idinscripcion = inscripciones.idinscripcion')
                            ->where('opagos.idconcepto', 4)
                            ->where('opagos.estatuspago', 0)
                            ->where('tarjetones.estatus', 1);
                    });
            })
            ->get()
            ->toArray();

        $rentas = DB::table('rentas')
            ->select(
                DB::raw("CONCAT(idrenta, '-R') as idinscripcion"),
                'ciudadanos.name AS NombreCompleto',
                'espacios.nameroom AS nametaller'
            )
            ->join('ciudadanos', 'rentas.idciudadano', '=', 'ciudadanos.idciudadano')
            ->join('espacios', 'rentas.idespacio', '=', 'espacios.idespacio')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('opagos')
                    ->whereRaw('opagos.idrenta = rentas.idrenta')
                    ->where('opagos.idconcepto', 3)
                    ->where('opagos.estatuspago', 1);
            })
            ->get()
            ->toArray();

        $resultadoCombinado = array_merge($inscripciones, $rentas);

        return response()->json($resultadoCombinado, 200);
    }

    public function apiConsultaOpagos($id)
    {
        $tipo = substr($id, -2);
        $idReal = substr($id, 0, -2);

        if (strpos($tipo, 'I') !== false) {
            return $this->getOpagosInscripcion($idReal);
        } elseif (strpos($tipo, 'R') !== false) {
            return $this->getOpagosRenta($idReal);
        }

        return response()->json(['error' => 'Tipo de operacion no valida'], 400);
    }

    protected function getOpagosInscripcion($idinscripcion)
    {
        $apiOpago = Opago::select(
            'opagos.idopago',
            'opagos.mespago',
            'opagos.montototal',
            'opagos.foliocc',
            'opagos.rfc',
            'conceptos.concepto',
            DB::raw("CONCAT(alumnos.name, ' ', alumnos.apellidop, ' ', alumnos.apellidom) AS NombreCompleto"),
            'talleres.nametaller', 
            DB::raw("'' AS fechaevento")
        )
            ->join('inscripciones', 'opagos.idinscripcion', '=', 'inscripciones.idinscripcion')
            ->join('alumnos', 'inscripciones.idalumno', '=', 'alumnos.idalumno')
            ->join('talleres', 'inscripciones.idtaller', '=', 'talleres.idtaller')
            ->join('conceptos', 'opagos.idconcepto', '=', 'conceptos.idconcepto')
            ->where('inscripciones.estatus', 1)
            ->where(function ($query) {
                $query->whereIn('opagos.idconcepto', [1, 2])
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('opagos.idconcepto', 4)
                            ->whereExists(function ($existsQuery) {
                                $existsQuery->select(DB::raw(1))
                                    ->from('tarjetones')
                                    ->whereColumn('tarjetones.idopago', 'opagos.idopago')
                                    ->where('tarjetones.estatus', 1);
                            });
                    });
            })
            ->where('opagos.estatuspago', 0)
            ->where('opagos.idinscripcion', $idinscripcion)
            ->where('opagos.montototal', '>', 0)
            ->get();

        return response()->json($apiOpago, 200);
    }

    protected function getOpagosRenta($idrenta)
    {
        $apiOpago = Opago::select(
            'opagos.idopago',
            'opagos.mespago',
            'opagos.montototal',
            'opagos.foliocc',
            'opagos.rfc',
            'rentas.fechaevento',
            'conceptos.concepto',
            'ciudadanos.name AS NombreCompleto',
            'espacios.nameroom AS nametaller'
        )
            ->join('rentas', 'opagos.idrenta', '=', 'rentas.idrenta')
            ->join('ciudadanos', 'rentas.idciudadano', '=', 'ciudadanos.idciudadano')
            ->join('espacios', 'rentas.idespacio', '=', 'espacios.idespacio')
            ->join('conceptos', 'opagos.idconcepto', '=', 'conceptos.idconcepto')
            ->where('rentas.estatus', 1)
            ->where('opagos.estatuspago', 0)
            ->where('opagos.idrenta', $idrenta)
            ->where('opagos.montototal', '>', 0)
            ->get();

        return response()->json($apiOpago, 200);
    }

    public function apiRpagos(Request $request)
    {
        $request->validate([
            'id_inscri' => ['required'],
            'foliocaja' => ['required', 'string', 'max:50'],
        ]);

        $id_inscris = $request->id_inscri;
        $fcaja = $request->foliocaja;
        $fpago = $request->fecha;

        $inscris = explode(",", $id_inscris);

        for ($i = 0; $i < count($inscris); $i++) {
            $uoPago = Opago::findOrFail($inscris[$i]);
            $uoPago->foliocajas = $fcaja;
            $uoPago->fechapago = $fpago;
            $uoPago->estatuspago = 1;
            $uoPago->save();
        }

        return response()->json('Recepcionado correctamente', 200);
    }

    protected function aplicarFiltroFechas($query, Request $request, $columna = 'o.created_at')
    {
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        if ($fechaInicio && $fechaFin) {
            $query->whereBetween($columna, [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59']);
        } elseif ($fechaInicio) {
            $query->where($columna, '>=', $fechaInicio . ' 00:00:00');
        } elseif ($fechaFin) {
            $query->where($columna, '<=', $fechaFin . ' 23:59:59');
        }

        return $query;
    }

    public function apiSuga(Request $request)
    {
        $apiSugaQuery = DB::table('opagos as o')
            ->leftJoin('inscripciones as i', 'o.idinscripcion', '=', 'i.idinscripcion')
            ->leftJoin('rentas as r', 'o.idrenta', '=', 'r.idrenta');

        $this->aplicarFiltroFechas($apiSugaQuery, $request);

        $apiSuga = $apiSugaQuery
            ->selectRaw("
                COUNT(o.idopago) AS total_ordenes,

                SUM(
                    CASE
                        WHEN o.estatuspago = 1
                        THEN o.montototal
                        ELSE 0
                    END
                ) AS total_recaudado,

                SUM(
                    CASE
                        WHEN o.estatuspago = 1
                        THEN 1
                        ELSE 0
                    END
                ) AS ordenes_pagadas,

                SUM(
                    CASE
                        WHEN o.estatuspago = 0
                        AND (
                            i.estatus = 1
                            OR r.estatus = 1
                        )
                        THEN 1
                        ELSE 0
                    END
                ) AS ordenes_pendientes,

                SUM(
                    CASE
                        WHEN o.estatuspago = 0
                        AND (
                            i.estatus = 0
                            OR r.estatus = 0
                        )
                        THEN 1
                        ELSE 0
                    END
                ) AS ordenes_canceladas
            ")
            ->first();

        return response()->json($apiSuga, 200);
    }

    public function apiSugaOrdenes(Request $request, $idopago)
    {
        $apiSugaOrdenesQuery = DB::table('opagos as o')
            ->leftJoin('inscripciones as i', 'o.idinscripcion', '=', 'i.idinscripcion')
            ->leftJoin('alumnos as a', 'i.idalumno', '=', 'a.idalumno')
            ->leftJoin('talleres as t', 'i.idtaller', '=', 't.idtaller')
            ->leftJoin('rentas as r', 'o.idrenta', '=', 'r.idrenta')
            ->leftJoin('ciudadanos as c', 'r.idciudadano', '=', 'c.idciudadano')
            ->leftJoin('espacios as e', 'r.idespacio', '=', 'e.idespacio')
            ->leftJoin('conceptos as con', 'o.idconcepto', '=', 'con.idconcepto');

        $this->aplicarFiltroFechas($apiSugaOrdenesQuery, $request);

        $apiSugaOrdenes = $apiSugaOrdenesQuery
            ->select(
                'o.idopago',
                DB::raw("
                    CASE
                        WHEN a.idalumno IS NOT NULL THEN CONCAT_WS(' ', a.name, a.apellidop, a.apellidom)
                        ELSE c.name
                    END AS NombreCompleto
                "),
                'o.montototal',
                'o.created_at',
                DB::raw("
                    CASE
                        WHEN o.foliocajas LIKE 'AA%' THEN SUBSTRING(o.foliocajas, 3)
                        ELSE o.foliocajas
                    END AS foliocajas
                "),
                'con.concepto',
                DB::raw("
                    COALESCE(
                        t.nametaller,
                        e.nameroom
                    ) AS lugar
                ")
            )
            ->where('o.idopago', $idopago)
            ->first();

        if (!$apiSugaOrdenes) {
            return response()->json(['error' => 'Orden no encontrada'], 404);
        }

        return response()->json($apiSugaOrdenes, 200);
    }

    protected function sugaOrdenesBaseQuery(Request $request, $incluirFolioCajas = false)
    {
        $query = DB::table('opagos as o')
            ->leftJoin('inscripciones as i', 'o.idinscripcion', '=', 'i.idinscripcion')
            ->leftJoin('alumnos as a', 'i.idalumno', '=', 'a.idalumno')
            ->leftJoin('rentas as r', 'o.idrenta', '=', 'r.idrenta')
            ->leftJoin('ciudadanos as c', 'r.idciudadano', '=', 'c.idciudadano')
            ->select(
                'o.idopago',
                DB::raw("
                    CASE
                        WHEN a.idalumno IS NOT NULL THEN CONCAT_WS(' ', a.name, a.apellidop, a.apellidom)
                        ELSE c.name
                    END AS NombreCompleto
                "),
                'o.montototal',
                'o.created_at'
            );

        if ($incluirFolioCajas) {
            $query->addSelect(DB::raw("
                CASE
                    WHEN o.foliocajas LIKE 'AA%' THEN SUBSTRING(o.foliocajas, 3)
                    ELSE o.foliocajas
                END AS foliocajas
            "));
        }

        $this->aplicarFiltroFechas($query, $request);

        return $query;
    }

    public function apiSugaPagadas(Request $request)
    {
        $apiSugaPagadas = $this->sugaOrdenesBaseQuery($request, true)
            ->where('o.estatuspago', 1)
            ->get();

        return response()->json($apiSugaPagadas, 200);
    }

    public function apiSugaPendientes(Request $request)
    {
        $apiSugaPendientes = $this->sugaOrdenesBaseQuery($request)
            ->where('o.estatuspago', 0)
            ->where(function ($query) {
                $query->where('i.estatus', 1)
                    ->orWhere('r.estatus', 1);
            })
            ->get();

        return response()->json($apiSugaPendientes, 200);
    }

    public function apiSugaCanceladas(Request $request)
    {
        $apiSugaCanceladas = $this->sugaOrdenesBaseQuery($request)
            ->where('o.estatuspago', 0)
            ->where(function ($query) {
                $query->where('i.estatus', 0)
                    ->orWhere('r.estatus', 0);
            })
            ->get();

        return response()->json($apiSugaCanceladas, 200);
    }
}
