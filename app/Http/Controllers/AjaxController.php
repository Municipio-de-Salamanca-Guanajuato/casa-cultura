<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Aula;
use App\Models\Ciudadano;
use App\Models\ConceptoXRenta;
use App\Models\Espacio;
use App\Models\Renta;
use Illuminate\Http\Request;
use App\Models\Alumno;
use App\Models\Concepto;
use App\Models\Edificio;
use App\Models\Maestro;
use App\Models\Taller;
use App\Models\TallerHorario;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\General;


class AjaxController extends Controller
{
    public function tblalumnos()
    {
        // dd('Pintar tabla...');
        // Obtener todos los alumnos 
        $alumnos = Alumno::all()->map(function ($alumno) {
            $alumno->nombreCompleto = $alumno->name . ' ' . $alumno->apellidop . ' ' . $alumno->apellidom;
            return $alumno;
        });

        // Devolver los datos como JSON para DataTables 
        return response()->json(['data' => $alumnos]);
    }

    public function ajaxConsultaCurpAgregar(Request $request)
    {
        // Validación de CURP solo para agregar
        $exists = Alumno::where('curp', $request->curp)
            ->exists();
        return response()->json(['exists' => $exists]);
    }

    public function ajaxConsultaCurpEditar(Request $request)
    {
        // Excluir el alumno actual en la validación de CURP durante la edición
        $exists = Alumno::where('curp', $request->curp)
            ->where('idalumno', '!=', $request->idalumno) // No verificar el mismo alumno
            ->exists();
        return response()->json(['exists' => $exists]);
    }

    public function ajaxConsultaAlumnos($estatus)
    {
        if ($estatus == 'act') {
            // Filtra los alumnos con estatus 1 (activos)
            $alumnos = Alumno::where('estatus', 1)->get();
        } elseif ($estatus == 'ina') {
            // Filtra los alumnos con estatus 0 (inactivos)
            $alumnos = Alumno::where('estatus', 0)->get();
        } else {
            // Si el estatus no es 'act' ni 'ina', devuelve un conjunto vacío o todos los alumnos
            $alumnos = Alumno::all();
        }

        // Mapea los datos para agregar el nombre completo
        $alumnos = $alumnos->map(function ($alumno) {
            $alumno->nombreCompleto = $alumno->name . ' ' . $alumno->apellidop . ' ' . $alumno->apellidom;
            return $alumno;
        });

        // Retorna los datos en formato JSON
        return response()->json(['data' => $alumnos]);
    }

    public function ajaxEditAlumno($idalumno)
    {
        // dd($idalumno);
        // dd('Pintar modal...');
        // Busca el alumno por su ID
        $alumnos = Alumno::find($idalumno);

        // Verifica si el alumno existe
        if ($alumnos) {
            return response()->json($alumnos); // Retorna los datos del alumno
        } else {
            return response()->json(['message' => 'Alumno no encontrado'], 404);
        }
    }

    public function tbltalleres()
    {
        // Obtener todos los talleres
        $talleres = Taller::all()->map(function ($taller) {
            // Si necesitas agregar alguna propiedad calculada, como 'nombreCompleto' en los alumnos, puedes hacerlo aquí
            // Ejemplo (solo si lo necesitas): 
            // $taller->nombreCompleto = $taller->nametaller . ' - ' . $taller->maestro;
            return $taller;
        });

        // Devolver los datos como JSON para DataTables
        return response()->json(['data' => $talleres]);
    }

    public function ajaxConsultaTallerAgregar(Request $request)
    {
        $taller = Taller::where('nametaller', $request->nametaller)
            ->where('estatus', 1) // Solo verifica talleres activos
            ->first();
        return response()->json(['existe' => $taller ? true : false]);
    }

    public function ajaxConsultaTallerEditar(Request $request, $idtaller)
    {
        $taller = Taller::where('nametaller', $request->nametaller)
            ->where('estatus', 1) // Solo verifica talleres activos
            ->where('idTaller', '!=', $idtaller)
            ->first();
        return response()->json(['existe' => $taller ? true : false]);
    }

    public function ajaxConsultaTalleres($estatus)
    {
        // Filtrar los talleres por estatus
        if ($estatus == 'act') {
            // Talleres activos (con cuántos alumnos inscritos tienen, para avisar en el pase de lista)
            $talleres = Taller::where('estatus', 1)
                ->withCount(['inscripciones as inscritos' => fn ($q) => $q->where('estatus', 1)])
                ->get();
        } elseif ($estatus == 'ina') {
            // Talleres inactivos
            $talleres = Taller::where('estatus', 0)->get();
        } else {
            // Si no es 'act' ni 'ina', devuelve todos los talleres
            $talleres = Taller::all();
        }

        // Mapea los datos si necesitas realizar algún ajuste
        $talleres = $talleres->map(function ($taller) {
            // $taller->rangoFechas = $taller->fechainicio . ' - ' . $taller->fechafin; // Ejemplo: añadir rango de fechas
            return $taller;
        });

        // Retorna los datos en formato JSON
        return response()->json(['data' => $talleres]);
    }

    public function ajaxEditTaller($idtaller)
    {
        // Incluye el maestro del catálogo (si tiene) para el select del modal
        $talleres = Taller::with([
            'maestroCatalogo:idmaestro,nombre_comp,estatus',
            // Horarios (activos e inactivos) con su aula y edificio, para armar los bloques del modal
            'horarios' => fn ($q) => $q->orderBy('dia')->orderBy('hora_inicio'),
            'horarios.aula:idaula,idedificio,nombre',
            'horarios.aula.edificio:idedificio,nombre',
        ])->find($idtaller);
        // dd($idtaller);
        // Verifica si el alumno existe
        if ($talleres) {
            return response()->json($talleres); // Retorna los datos del alumno
        } else {
            return response()->json(['message' => 'Taller no encontrado'], 404);
        }
    }

    public function ajaxReadInscripcion()
    {
        $currentDate = Carbon::now();
        // Obtener los alumnos activos y que NO tienen una inscripción activa (estatus = 1)
        $alumnos = Alumno::where('alumnos.estatus', 1)
            ->whereNotExists(function ($query) {
                $query->select('inscripciones.idalumno')
                    ->from('inscripciones')
                    ->whereColumn('inscripciones.idalumno', 'alumnos.idalumno')
                    ->where('inscripciones.estatus', 1); // Inscripción activa
                // ->join('talleres', 'inscripciones.idtaller', '=', 'talleres.idtaller')
                // ->where('talleres.fechainicio', '<=', $currentDate)
                // ->where('talleres.fechafin', '>=', $currentDate)
                // ->where('inscripciones.estatus', 1); // Solo considerar inscripciones activas
            })
            ->select('idalumno', 'name', 'apellidop', 'apellidom', 'curp')
            ->get()
            ->map(function ($alumno) {
                $alumno->nombreCompleto = $alumno->name . ' ' . $alumno->apellidop . ' ' . $alumno->apellidom;
                // Edad a la fecha de inscripción (hoy), calculada con la CURP
                $alumno->edad = $alumno->edadAl(now());
                $alumno->makeHidden('curp');
                return $alumno;
            });

        // $now = Carbon::now();

        // // 1) Alumnos bloqueados por tener una inscripción ACTIVA (taller aún vigente)
        // $blockedActive = DB::table('inscripciones as i')
        //     ->join('talleres as t', 'i.idtaller', '=', 't.idtaller')
        //     ->where('i.estatus', 1)
        //     // ->where('t.fechafin', '>=', $now)
        //     ->pluck('i.idalumno');

        // // 2) Alumnos bloqueados por tener una inscripción FINALIZADA pero con pagos INCOMPLETOS
        // $blockedUnpaidFinished = DB::table('inscripciones as i')
        //     ->join('talleres as t', 'i.idtaller', '=', 't.idtaller')
        //     ->leftJoin('opagos as o', 'o.idinscripcion', '=', 'i.idinscripcion')
        //     ->where('i.estatus', 1)
        //     ->where('t.fechafin', '<', $now)
        //     ->groupBy('i.idinscripcion', 'i.idalumno')
        //     ->havingRaw('SUM(CASE WHEN o.estatuspago = 1 THEN 1 ELSE 0 END) < COUNT(o.idopago)')
        //     ->pluck('i.idalumno');

        // // Unión de IDs bloqueados
        // $blockedIds = $blockedActive->merge($blockedUnpaidFinished)->unique()->toArray();

        // // Obtener alumnos activos que NO estén bloqueados
        // $alumnos = Alumno::where('alumnos.estatus', 1)
        //     ->when(!empty($blockedIds), function ($q) use ($blockedIds) {
        //         $q->whereNotIn('idalumno', $blockedIds);
        //     })
        //     ->select('idalumno', 'name', 'apellidop', 'apellidom')
        //     ->get()
        //     ->map(function ($alumno) {
        //         $alumno->nombreCompleto = trim($alumno->name . ' ' . $alumno->apellidop . ' ' . $alumno->apellidom);
        //         return $alumno;
        //     });

        // Talleres activos, con cupo y que no han terminado
        $talleres = $this->talleresParaInscripcion()
            ->select('idtaller', 'nametaller', 'fechainicio', 'fechafin', 'cupo', 'disponibles', 'edad_min', 'edad_max')
            ->get();

        // Retornar los datos como JSON
        return response()->json([
            'alumnos' => $alumnos,
            'talleres' => $talleres
        ]);
    }

    /**
     * Talleres que se ofrecen en el modal de inscripción: activos, con cupo y que no han terminado
     * (incluye los del siguiente periodo aunque inicien el próximo año, para inscribir por adelantado).
     */
    private function talleresParaInscripcion()
    {
        return Taller::where('estatus', 1)
            ->where('disponibles', '>', 0) // Filtrar talleres sin cupo
            ->whereDate('fechafin', '>=', Carbon::now()->toDateString()); // Aseguramos que el taller no haya terminado
    }

    /**
     * Para el modal de inscripción: de los talleres que se ofrecen, cuáles le chocan al alumno
     * por horario con otra inscripción activa de la misma persona (registros -1, -02...).
     * Responde {choques: {idtaller: "motivo corto"}}.
     */
    public function ajaxChoquesAlumno($idalumno)
    {
        $alumno = Alumno::findOrFail($idalumno);
        $choques = [];

        foreach ($this->talleresParaInscripcion()->pluck('idtaller') as $idtaller) {
            $encontrados = TallerHorario::buscarChoquesAlumno($alumno->curp, $idtaller);
            if (empty($encontrados)) {
                continue;
            }

            $c = $encontrados[0];
            $motivo = (int) $idtaller === (int) ($c['idtaller'] ?? 0)
                ? 'ya está inscrito en este taller'
                : 'choca con ' . $c['taller'] . ', ' . mb_substr(TallerHorario::DIAS[$c['dia']], 0, 3) . ' ' . $c['hora_inicio'] . '-' . $c['hora_fin'];
            if (trim((string) $c['curp']) !== trim((string) $alumno->curp)) {
                $motivo .= ' · registro ' . $c['curp'];
            }

            $choques[$idtaller] = $motivo;
        }

        return response()->json(['choques' => (object) $choques]);
    }

    public function tblinscripcion()
    {
        // dd('Pintar tabla');
        // Obtener las inscripciones junto con los datos de los alumnos y talleres
        $inscripciones = DB::table('inscripciones')
            ->join('alumnos', 'inscripciones.idalumno', '=', 'alumnos.idalumno')
            ->join('talleres', 'inscripciones.idtaller', '=', 'talleres.idtaller')
            ->select(
                'inscripciones.idinscripcion',
                'alumnos.name',
                'alumnos.apellidop',
                'alumnos.apellidom',
                'talleres.nametaller',
                'inscripciones.created_at',
                'talleres.fechainicio',
                'talleres.fechafin',
                'inscripciones.estatus'
            )
            ->get()
            ->map(function ($inscripcion) {
                // Agregar nombre completo del alumno y periodo
                $inscripcion->nombreCompleto = $inscripcion->name . ' ' . $inscripcion->apellidop . ' ' . $inscripcion->apellidom;
                $inscripcion->periodo = $inscripcion->fechainicio . ' - ' . $inscripcion->fechafin;
                return $inscripcion;
            });

        // Retornar los datos como JSON para DataTables
        return response()->json(['data' => $inscripciones]);
    }

    public function ajaxConsultaInscripciones($estatus)
    {
        // Filtra las inscripciones por el estatus (1 para activos, 0 para inactivos)
        $inscripcionesQuery = DB::table('inscripciones')
            ->join('alumnos', 'inscripciones.idalumno', '=', 'alumnos.idalumno')
            ->join('talleres', 'inscripciones.idtaller', '=', 'talleres.idtaller')
            ->select(
                'inscripciones.idinscripcion',
                'alumnos.name',
                'alumnos.apellidop',
                'alumnos.apellidom',
                'talleres.nametaller',
                'inscripciones.created_at',
                'talleres.fechainicio',
                'talleres.fechafin',
                'inscripciones.estatus'
            );

        if ($estatus == 'act') {
            // Filtra las inscripciones con estatus 1 (activos)
            $inscripcionesQuery->where('inscripciones.estatus', 1);
        } elseif ($estatus == 'ina') {
            // Filtra las inscripciones con estatus 0 (inactivos)
            $inscripcionesQuery->where('inscripciones.estatus', 0);
        }

        // Obtener las inscripciones
        $inscripciones = $inscripcionesQuery->get();

        // Mapea los datos para agregar el nombre completo y periodo
        $inscripciones = $inscripciones->map(function ($inscripcion) {
            $inscripcion->nombreCompleto = $inscripcion->name . ' ' . $inscripcion->apellidop . ' ' . $inscripcion->apellidom;
            $inscripcion->periodo = $inscripcion->fechainicio . ' - ' . $inscripcion->fechafin;
            return $inscripcion;
        });

        // Retornar los datos como JSON para DataTables
        return response()->json(['data' => $inscripciones]);
    }

    public function ajaxEditInscripcion($idinscripcion)
    {
        $currentDate = Carbon::now();
        $startOfYear = Carbon::now()->startOfYear();
        $endOfYear = Carbon::now()->endOfYear();

        // Obtener la inscripción actual
        $inscripcion = DB::table('inscripciones')
            ->where('idinscripcion', $idinscripcion)
            ->select('idinscripcion', 'idalumno', 'idtaller')
            ->first();

        if (!$inscripcion) {
            return response()->json(['error' => 'Inscripción no encontrada.'], 404);
        }

        // Obtener los alumnos activos excluyendo los ya inscritos, pero incluir al alumno actual de la inscripción
        $alumnos = Alumno::where('estatus', 1)
            ->where(function ($query) use ($currentDate, $inscripcion) {
                $query->whereNotIn('idalumno', function ($subquery) use ($currentDate) {
                    $subquery->select('idalumno')
                        ->from('inscripciones')
                        ->join('talleres', 'inscripciones.idtaller', '=', 'talleres.idtaller')
                        ->where('talleres.fechainicio', '<=', $currentDate)
                        ->where('talleres.fechafin', '>=', $currentDate);
                })
                    ->orWhere('idalumno', $inscripcion->idalumno); // Incluir al alumno inscrito actualmente
            })
            ->select('idalumno', 'name', 'apellidop', 'apellidom')
            ->get()
            ->map(function ($alumno) {
                $alumno->nombreCompleto = $alumno->name . ' ' . $alumno->apellidop . ' ' . $alumno->apellidom;
                return $alumno;
            });

        // Obtener los talleres activos con cupo disponible o el taller en el que el alumno ya está inscrito
        $talleres = Taller::where('estatus', 1)
            ->where(function ($query) use ($startOfYear, $endOfYear) {
                $query->whereBetween('fechainicio', [$startOfYear, $endOfYear])
                    ->where('fechafin', '>=', Carbon::now());
            })
            ->where(function ($query) use ($inscripcion) {
                $query->where('disponibles', '>', 0)  // Solo mostrar talleres con cupo
                    ->orWhere('idtaller', $inscripcion->idtaller); // Incluir el taller del alumno aunque tenga 0 disponibles
            })
            ->select('idtaller', 'nametaller', 'fechainicio', 'fechafin', 'cupo', 'disponibles')
            ->get();

        // Retornar los datos como JSON
        return response()->json([
            'alumnos' => $alumnos,
            'talleres' => $talleres,
            'inscripcion' => $inscripcion
        ]);
    }

    public function tbladminopagos()
    {
        // Obtener todos los registros de la tabla General
        $adminopagos = General::all();

        // Devolver los datos como JSON para DataTables
        return response()->json(['data' => $adminopagos]);
    }

    public function ajaxEditAdminOp($idgeneral)
    {
        // dd($request);
        // Buscar el registro por su ID
        $adminOp = General::find($idgeneral);

        // Verificar si el registro existe
        if ($adminOp) {
            return response()->json($adminOp); // Retornar los datos como JSON
        } else {
            return response()->json(['message' => 'Registro no encontrado'], 404); // Error si no existe
        }
    }

    public function tblconceptos()
    {
        // Obtener todos los registros de la tabla Concepto
        $conceptos = Concepto::all();

        // Devolver los datos como JSON para DataTables
        return response()->json(['data' => $conceptos]);
    }

    public function ajaxConsultaConcepto($estatus)
    {
        if ($estatus == 'act') {
            // Filtra los conceptos con estatus 1 (activos)
            $conceptos = Concepto::where('estatus', 1)->get();
        } elseif ($estatus == 'ina') {
            // Filtra los conceptos con estatus 0 (inactivos)
            $conceptos = Concepto::where('estatus', 0)->get();
        } else {
            // Si el estatus no es 'act' ni 'ina', devuelve todos los conceptos
            $conceptos = Concepto::all();
        }

        // Retorna los datos en formato JSON
        return response()->json(['data' => $conceptos]);
    }

    public function ajaxEditConcepto($idconcepto)
    {
        // dd('Bucando id: ', $idconcepto);
        // Buscar el registro por su ID
        $concepto = Concepto::find($idconcepto);

        // Verificar si el registro existe
        if ($concepto) {
            return response()->json($concepto); // Retornar los datos como JSON
        } else {
            return response()->json(['message' => 'Registro no encontrado'], 404); // Error si no existe
        }
    }

    public function tblopinsc()
    {
        // Consulta para obtener solo las inscripciones activas
        $opagos = DB::table('opagos')
            ->join('inscripciones', 'opagos.idinscripcion', '=', 'inscripciones.idinscripcion')
            ->join('alumnos', 'inscripciones.idalumno', '=', 'alumnos.idalumno')
            ->join('talleres', 'inscripciones.idtaller', '=', 'talleres.idtaller')
            ->where('opagos.idconcepto', 1) // Filtra solo pagos de inscripción
            ->where('inscripciones.estatus', 1) // Filtra inscripciones activas
            ->select(
                'opagos.idopago',
                'opagos.mespago',
                'opagos.estatuspago',
                'opagos.fechalimite',
                'opagos.montototal',
                'opagos.foliocc',
                'opagos.foliocajas',
                'opagos.fechapago',
                'opagos.idinscripcion',
                'opagos.idconcepto',
                'inscripciones.idinscripcion',
                DB::raw("CONCAT(alumnos.name, ' ', alumnos.apellidop, ' ', alumnos.apellidom) AS nombreCompleto"),
                'talleres.nametaller',
                'talleres.inscripcion'
            )
            ->get();

        // $inscripciones = DB::table('inscripciones')
        //     ->join('alumnos', 'inscripciones.idalumno', '=', 'alumnos.idalumno')
        //     ->join('talleres', 'inscripciones.idtaller', '=', 'talleres.idtaller')
        //     ->leftJoin('opagos', function ($join) {
        //         $join->on('inscripciones.idinscripcion', '=', 'opagos.idinscripcion')
        //             ->where('opagos.idconcepto', '=', function ($subquery) {
        //                 $subquery->select('idconcepto')
        //                     ->from('conceptos')
        //                     ->where('concepto', 'Inscripción')
        //                     ->limit(1);
        //             });
        //     })
        //     ->where('inscripciones.estatus', 1) // Filtra inscripciones activas
        //     ->select(
        //         'inscripciones.idinscripcion',
        //         DB::raw("CONCAT(alumnos.name, ' ', alumnos.apellidop, ' ', alumnos.apellidom) AS nombreCompleto"),
        //         'talleres.nametaller',
        //         'talleres.inscripcion',
        //         'opagos.estatuspago'
        //     )
        //     ->get();

        // Retornar los datos en formato JSON para DataTables
        return response()->json(['data' => $opagos]);
    }

    public function ajaxShowOpagoAlumnoInsc($idopago)
    {
        // Consulta para obtener solo las inscripciones activas
        $opagos = DB::table('opagos')
            ->join('inscripciones', 'opagos.idinscripcion', '=', 'inscripciones.idinscripcion')
            ->join('alumnos', 'inscripciones.idalumno', '=', 'alumnos.idalumno')
            ->join('talleres', 'inscripciones.idtaller', '=', 'talleres.idtaller')
            ->join('conceptos', 'opagos.idconcepto', '=', 'conceptos.idconcepto')
            // ->where('opagos.idconcepto', 1) // Filtra solo pagos de inscripción
            // ->where('inscripciones.estatus', 1) // Filtra inscripciones activas
            ->where('opagos.idopago', $idopago) // Filtra el pago específico
            ->select(
                'opagos.idopago',
                'opagos.mespago',
                'opagos.estatuspago',
                'opagos.fechalimite',
                'opagos.montototal',
                'opagos.foliocc',
                'opagos.foliocajas',
                'opagos.fechapago',
                'opagos.idinscripcion',
                'opagos.idconcepto',
                'inscripciones.idinscripcion',
                'conceptos.concepto',
                DB::raw("CONCAT(alumnos.name, ' ', alumnos.apellidop, ' ', alumnos.apellidom) AS nombreCompleto"),
                'talleres.nametaller',
                'talleres.inscripcion'
            )
            ->first();
        // Obtener datos de la inscripción con INNER JOIN
        // $inscripcion = DB::table('inscripciones')
        //     ->join('alumnos', 'alumnos.idalumno', '=', 'inscripciones.idalumno')
        //     ->join('talleres', 'talleres.idtaller', '=', 'inscripciones.idtaller')
        //     ->select(
        //         'inscripciones.idinscripcion',
        //         'inscripciones.estatus',
        //         'inscripciones.idalumno',
        //         'inscripciones.idtaller',
        //         'inscripciones.created_at',
        //         'alumnos.idalumno',
        //         'alumnos.name',
        //         'alumnos.apellidop',
        //         'alumnos.apellidom',
        //         'alumnos.estatus as estatus_alumno',
        //         'talleres.idtaller',
        //         'talleres.nametaller',
        //         'talleres.inscripcion'
        //     )
        //     ->where('inscripciones.idinscripcion', $idinscripcion)
        //     ->first();

        // Obtener lista de fundamentos (generales)
        // $inscripcion = DB::table('inscripciones')->get();

        // if ($inscripcion) {
        //     $inscripcion->nombreCompleto = $inscripcion->name . ' ' . $inscripcion->apellidop . ' ' . $inscripcion->apellidom;
        // }
        // Retornar los datos en JSON
        return response()->json(['data' => $opagos]);
    }

    public function tblopmens()
    {
        $mensualidades = DB::table('inscripciones')
            ->join('alumnos', 'inscripciones.idalumno', '=', 'alumnos.idalumno')
            ->join('talleres', 'inscripciones.idtaller', '=', 'talleres.idtaller')
            ->where('inscripciones.estatus', 1) // Solo inscripciones activas
            ->select(
                'inscripciones.idinscripcion',
                DB::raw("CONCAT(alumnos.name, ' ', alumnos.apellidop, ' ', alumnos.apellidom) AS nombreCompleto"),
                'talleres.nametaller',
                'talleres.mensualidad',
                DB::raw("CONCAT(talleres.fechainicio, ' - ', talleres.fechafin) AS periodo")
            )
            ->get();

        return response()->json(['data' => $mensualidades]);
        // $mensualidades = DB::table('opagos')
        //     ->join('inscripciones', 'opagos.idinscripcion', '=', 'inscripciones.idinscripcion')
        //     ->join('alumnos', 'inscripciones.idalumno', '=', 'alumnos.idalumno')
        //     ->join('talleres', 'inscripciones.idtaller', '=', 'talleres.idtaller')
        //     ->where('opagos.idconcepto', 2) // Filtra solo pagos de mensualidades
        //     ->where('inscripciones.estatus', 1) // Filtra inscripciones activas
        //     ->select(
        //         'opagos.idinscripcion',
        //         DB::raw("CONCAT(alumnos.name, ' ', alumnos.apellidop, ' ', alumnos.apellidom) AS nombreCompleto"),
        //         'talleres.nametaller',
        //         'talleres.mensualidad',
        //         DB::raw("CONCAT(talleres.fechainicio, ' - ', talleres.fechafin) AS periodo"), // Periodo del taller

        //         // Obtiene la fecha límite más próxima
        //         DB::raw("(SELECT MIN(opagos_sub.fechalimite) FROM opagos AS opagos_sub WHERE opagos_sub.idinscripcion = opagos.idinscripcion) AS fechalimite"),

        //         // Obtiene el último estatus de pago según la fecha límite más reciente
        //         DB::raw("(SELECT opagos_sub.estatuspago FROM opagos AS opagos_sub WHERE opagos_sub.idinscripcion = opagos.idinscripcion ORDER BY opagos_sub.fechalimite DESC LIMIT 1) AS estatuspago")


        // // Obtiene la fecha límite más próxima
        // DB::raw("(SELECT MIN(opagos.fechalimite) FROM opagos WHERE opagos.idinscripcion = opagos.idinscripcion) AS fechalimite"),

        // // Obtiene el último estatus de pago
        // DB::raw("(SELECT opagos.estatuspago FROM opagos WHERE opagos.idinscripcion = opagos.idinscripcion ORDER BY opagos.fechalimite DESC LIMIT 1) AS estatuspago")

        // DB::raw('(SELECT MIN(opagos.fechalimite) FROM opagos WHERE opagos.idinscripcion = inscripciones.idinscripcion) AS fechalimite'), // Obtiene la fecha más próxima
        // DB::raw('(SELECT estatuspago FROM opagos WHERE opagos.idinscripcion = inscripciones.idinscripcion ORDER BY fechalimite DESC LIMIT 1) AS estatuspago') // Último estatus
        // DB::raw('SUM(opagos.montototal) AS totalMensualidades') // Opcional: suma total de mensualidades
        // )
        // ->groupBy('opagos.idinscripcion', 'alumnos.name', 'alumnos.apellidop', 'alumnos.apellidom', 'talleres.nametaller', 'talleres.mensualidad', 'talleres.fechainicio', 'talleres.fechafin')
        // ->get();

        // Consulta para obtener las mensualidades activas
        // $mensualidades = DB::table('inscripciones')
        //     ->join('alumnos', 'inscripciones.idalumno', '=', 'alumnos.idalumno')
        //     ->join('talleres', 'inscripciones.idtaller', '=', 'talleres.idtaller')
        //     ->leftJoin('opagos', function ($join) {
        //         $join->on('inscripciones.idinscripcion', '=', 'opagos.idinscripcion')
        //             ->where('opagos.idconcepto', '=', function ($subquery) {
        //                 $subquery->select('idconcepto')
        //                     ->from('conceptos')
        //                     ->where('concepto', 'Mensualidad')
        //                     ->limit(1);
        //             });
        //     })
        //     ->where('inscripciones.estatus', 1) // Filtra inscripciones activas
        //     ->select(
        //         'inscripciones.idinscripcion',
        //         'alumnos.name',
        //         'alumnos.apellidop',
        //         'alumnos.apellidom',
        //         'talleres.nametaller',
        //         'talleres.mensualidad',
        //         'opagos.estatuspago',
        //         'talleres.fechainicio',
        //         'talleres.fechafin'
        //     )
        //     ->get();

        // // Mapea los datos para agregar campos derivados
        // $mensualidades = $mensualidades->map(function ($mensualidad) {
        //     $mensualidad->nombreCompleto = $mensualidad->name . ' ' . $mensualidad->apellidop . ' ' . $mensualidad->apellidom;
        //     $mensualidad->periodo = $mensualidad->fechainicio . ' - ' . $mensualidad->fechafin;
        //     return $mensualidad;
        // });

        // Retornar los datos en formato JSON para DataTables
        // return response()->json(['data' => $mensualidades]);
    }

    public function ajaxShowOpagoAlumnoMens($idinscripcion)
    {
        $ordenes = DB::table('opagos')
            ->join('inscripciones', 'opagos.idinscripcion', '=', 'inscripciones.idinscripcion')
            ->join('alumnos', 'inscripciones.idalumno', '=', 'alumnos.idalumno')
            ->join('talleres', 'inscripciones.idtaller', '=', 'talleres.idtaller')
            ->join('conceptos', 'opagos.idconcepto', '=', 'conceptos.idconcepto')
            ->select(
                'opagos.idopago',
                'opagos.mespago',
                'opagos.estatuspago',
                'opagos.fechalimite',
                'opagos.montototal',
                'opagos.foliocc',
                'opagos.foliocajas',
                'opagos.fechapago',
                'opagos.idinscripcion',
                'opagos.idconcepto',
                'inscripciones.idinscripcion',
                'inscripciones.estatus',
                'inscripciones.idalumno',
                'inscripciones.idtaller',
                'conceptos.idconcepto',
                'conceptos.concepto',
                DB::raw("CONCAT(alumnos.name, ' ', alumnos.apellidop, ' ', alumnos.apellidom) as nombre"),
                'talleres.nametaller'
            )
            ->where('opagos.idconcepto', 2)
            ->where('inscripciones.estatus', 1)
            ->where('opagos.idinscripcion', $idinscripcion)
            ->orderBy('opagos.mespago', 'asc')
            ->get();

        return response()->json([
            'data' => $ordenes,
            'nombre' => $ordenes->first()->nombre ?? null
        ]);
    }

    public function ajaxConsultaCxrenta($estatus)
    {
        if ($estatus == 'act') {
            // Filtra los conceptos con estatus 1 (activos)
            $cxrenta = ConceptoXRenta::where('estatus', 1)->get();
        } elseif ($estatus == 'ina') {
            // Filtra los conceptos con estatus 0 (inactivos)
            $cxrenta = ConceptoXRenta::where('estatus', 0)->get();
        } else {
            // Si el estatus no es 'act' ni 'ina', devuelve todos los conceptos
            $cxrenta = ConceptoXRenta::all();
        }

        // Retorna los datos en formato JSON
        return response()->json(['data' => $cxrenta]);
    }

    public function ajaxEditCxrenta($idcxrenta)
    {
        // Busca el concepto por su ID
        $cxrenta = ConceptoXRenta::find($idcxrenta);

        // Verifica si el concepto existe
        if ($cxrenta) {
            return response()->json($cxrenta); // Retorna los datos del cxrenta
        } else {
            return response()->json(['message' => 'Concepto no encontrado'], 404);
        }
    }

    public function ajaxConsultaCiudadanos($estatus)
    {
        if ($estatus == 'act') {
            // Filtra los conceptos con estatus 1 (activos)
            $ciudadanos = Ciudadano::where('estatus', 1)->get();
        } elseif ($estatus == 'ina') {
            // Filtra los conceptos con estatus 0 (inactivos)
            $ciudadanos = Ciudadano::where('estatus', 0)->get();
        } else {
            // Si el estatus no es 'act' ni 'ina', devuelve todos los conceptos
            $ciudadanos = Ciudadano::all();
        }

        // Mapea los datos para agregar el nombre completo
        $ciudadanos = $ciudadanos->map(function ($ciudadano) {
            $ciudadano->nombreCompleto = $ciudadano->name . ' ' . $ciudadano->apellidop . ' ' . $ciudadano->apellidom;
            return $ciudadano;
        });

        // Retorna los datos en formato JSON
        return response()->json(['data' => $ciudadanos]);
    }

    public function ajaxEditCiudadano($idciudadano)
    {
        // Busca el ciudadano por su ID
        $ciudadanos = Ciudadano::find($idciudadano);

        // Verifica si el ciudadano existe
        if ($ciudadanos) {
            return response()->json($ciudadanos); // Retorna los datos del alumno
        } else {
            return response()->json(['message' => 'Ciudadano no encontrado'], 404);
        }
    }

    public function ajaxConsultaEspacios($estatus)
    {
        if ($estatus == 'act') {
            // Filtra los conceptos con estatus 1 (activos)
            $espacios = Espacio::where('estatus', 1)->get();
        } elseif ($estatus == 'ina') {
            // Filtra los conceptos con estatus 0 (inactivos)
            $espacios = Espacio::where('estatus', 0)->get();
        } else {
            // Si el estatus no es 'act' ni 'ina', devuelve todos los conceptos
            $espacios = Espacio::all();
        }

        // Retorna los datos en formato JSON
        return response()->json(['data' => $espacios]);
    }

    public function ajaxEditEspacio($idespacio)
    {
        // Busca el espacio por su ID
        $espacios = Espacio::find($idespacio);

        // Verifica si el espacio existe
        if ($espacios) {
            return response()->json($espacios); // Retorna los datos del alumno
        } else {
            return response()->json(['message' => 'Espacio no encontrado'], 404);
        }
    }

    public function ajaxConsultaEdificios($estatus)
    {
        // Incluye el número de aulas activas de cada edificio
        $query = Edificio::withCount(['aulas as aulas_activas' => function ($q) {
            $q->where('estatus', 1);
        }]);

        if ($estatus == 'act') {
            $query->where('estatus', 1);
        } elseif ($estatus == 'ina') {
            $query->where('estatus', 0);
        }

        $edificios = $query->orderBy('nombre')->get();

        return response()->json(['data' => $edificios]);
    }

    public function ajaxEditEdificio($idedificio)
    {
        $edificio = Edificio::find($idedificio);

        if ($edificio) {
            return response()->json($edificio);
        } else {
            return response()->json(['message' => 'Edificio no encontrado'], 404);
        }
    }

    public function ajaxConsultaAulas($estatus)
    {
        // Incluye el nombre y estatus del edificio
        $query = Aula::join('edificios as e', 'aulas.idedificio', '=', 'e.idedificio')
            ->select('aulas.*', 'e.nombre as edificio', 'e.estatus as edificio_estatus');

        if ($estatus == 'act') {
            $query->where('aulas.estatus', 1);
        } elseif ($estatus == 'ina') {
            $query->where('aulas.estatus', 0);
        }

        $aulas = $query->orderBy('e.nombre')->orderBy('aulas.nombre')->get();

        return response()->json(['data' => $aulas]);
    }

    public function ajaxEditAula($idaula)
    {
        $aula = Aula::find($idaula);

        if ($aula) {
            return response()->json($aula);
        } else {
            return response()->json(['message' => 'Aula no encontrada'], 404);
        }
    }

    /**
     * Revisa al momento si un bloque de horario choca con otros talleres (aula o maestro).
     * Parámetros: dias[], idaula, hora_inicio, hora_fin, fechaini, fechafin, idmaestro (opcional), idtaller (opcional, para excluirlo).
     */
    public function ajaxChoquesHorario(Request $request)
    {
        $dias = array_filter(array_map('intval', (array) $request->input('dias', [])), fn ($d) => $d >= 1 && $d <= 6);
        $inicio = (string) $request->input('hora_inicio');
        $fin = (string) $request->input('hora_fin');
        $fechaIni = (string) $request->input('fechaini');
        $fechaFin = (string) $request->input('fechafin');

        $horaValida = fn ($h) => (bool) preg_match('/^\d{2}:\d{2}$/', $h);
        $fechaValida = fn ($f) => (bool) preg_match('/^\d{4}-\d{2}-\d{2}$/', $f);

        if (!$dias || !$request->filled('idaula') || !$horaValida($inicio) || !$horaValida($fin) || !$fechaValida($fechaIni) || !$fechaValida($fechaFin)) {
            return response()->json(['choques' => [], 'mensaje' => 'Datos incompletos'], 422);
        }

        $filas = array_map(fn ($dia) => [
            'dia' => $dia,
            'idaula' => (int) $request->idaula,
            'hora_inicio' => $inicio . ':00',
            'hora_fin' => $fin . ':00',
        ], $dias);

        $choques = TallerHorario::buscarChoques(
            $filas,
            $fechaIni,
            $fechaFin,
            $request->filled('idmaestro') ? (int) $request->idmaestro : null,
            $request->filled('idtaller') ? (int) $request->idtaller : null
        );

        $mensajes = array_map([TallerHorario::class, 'mensajeChoque'], $choques);

        // Al editar un taller: el horario que se agrega tampoco debe chocar con otros talleres de sus alumnos inscritos
        if ($request->filled('idtaller')) {
            $inscritos = TallerHorario::buscarChoquesInscritos((int) $request->idtaller, $filas, $fechaIni, $fechaFin);
            $mensajes = array_merge($mensajes, array_map([TallerHorario::class, 'mensajeChoqueInscrito'], $inscritos));
        }

        return response()->json([
            'choques' => $mensajes,
        ]);
    }

    public function ajaxConsultaMaestros($estatus)
    {
        if ($estatus == 'act') {
            $maestros = Maestro::where('estatus', 1)->orderBy('nombre_comp')->get();
        } elseif ($estatus == 'ina') {
            $maestros = Maestro::where('estatus', 0)->orderBy('nombre_comp')->get();
        } else {
            $maestros = Maestro::orderBy('nombre_comp')->get();
        }

        return response()->json(['data' => $maestros]);
    }

    public function ajaxEditMaestro($idmaestro)
    {
        $maestro = Maestro::find($idmaestro);

        if ($maestro) {
            return response()->json($maestro);
        } else {
            return response()->json(['message' => 'Maestro no encontrado'], 404);
        }
    }

    public function ajaxReadRenta()
    {
        // Obtener ciudadanos activos
        // $ciudadanos = Ciudadano::select('idciudadano', DB::raw("CONCAT(name, ' ', apellidop, ' ', apellidom) AS nombreCompleto"))
        //     ->where('estatus', 1)
        //     ->orderBy('name', 'asc')
        //     ->get();

        // Obtener espacios activos
        $espacios = Espacio::select('idespacio', 'nameroom', 'cupo', 'costo')
            ->where('estatus', 1)
            ->orderBy('nameroom', 'asc')
            ->get();

        // Obtener conceptos por renta activos
        $conceptosxrenta = ConceptoXRenta::select('idcxrenta', 'namecxrenta', 'costo')
            ->where('estatus', 1)
            ->orderBy('namecxrenta', 'asc')
            ->get();

        // Devolver los datos como JSON
        return response()->json([
            'espacios' => $espacios,
            'conceptosxrenta' => $conceptosxrenta
        ]);
    }

    public function ajaxConsultaRenta($estatus)
    {
        // Verificar el estatus
        $estatusValor = ($estatus === 'act') ? 1 : (($estatus === 'ina') ? 0 : null);

        // Consulta con relaciones
        $query = Renta::select(
            'ciudadanos.name',
            'ciudadanos.telefono',
            'espacios.nameroom',
            'rentas.idrenta',
            'rentas.fechaevento',
            'rentas.horainicio',
            'rentas.horafin',
            'rentas.cantidadxrenta',
            // 'rentas.conceptosxrenta',
            'rentas.estatus',
            'rentas.idciudadano',
            DB::raw("(
                SELECT GROUP_CONCAT(
                    conceptosxrenta.namecxrenta
                    ORDER BY FIND_IN_SET(conceptosxrenta.idcxrenta, rentas.conceptosxrenta)
                    SEPARATOR ', '
                )
                FROM conceptosxrenta
                WHERE FIND_IN_SET(conceptosxrenta.idcxrenta, rentas.conceptosxrenta)
            ) as conceptosxr")
        )
            ->join('ciudadanos', 'rentas.idciudadano', '=', 'ciudadanos.idciudadano')
            ->join('espacios', 'rentas.idespacio', '=', 'espacios.idespacio');

        // Filtrar por estatus si está definido
        if (!is_null($estatusValor)) {
            $query->where('rentas.estatus', $estatusValor);
        }
        $rentas = $query->get();

        // Retorna los datos en formato JSON
        return response()->json(['data' => $rentas]);
    }

    public function tbloprentas()
    {
        // Consulta para obtener solo las inscripciones activas
        $opagos = DB::table('opagos')
            ->join('rentas', 'opagos.idrenta', '=', 'rentas.idrenta')
            ->join('ciudadanos', 'rentas.idciudadano', '=', 'ciudadanos.idciudadano')
            ->join('espacios', 'rentas.idrenta', '=', 'espacios.idespacio')
            ->where('opagos.idconcepto', 3) // Filtra solo pagos de inscripción
            ->where('rentas.estatus', 1) // Filtra inscripciones activas
            ->select(
                'opagos.idopago',
                // 'opagos.mespago',
                'opagos.estatuspago',
                // 'opagos.fechalimite',
                'opagos.montototal',
                // 'opagos.foliocc',
                // 'opagos.foliocajas',
                // 'opagos.fechapago',
                // 'opagos.idinscripcion',
                // 'opagos.idconcepto',
                // 'inscripciones.idinscripcion',
                DB::raw("CONCAT(ciudadanos.name, ' ', ciudadanos.apellidop, ' ', ciudadanos.apellidom) AS nombreCompleto"),
                'espacios.nameroom',
                // 'talleres.inscripcion'
            )
            ->get();

        // Retornar los datos en formato JSON para DataTables
        return response()->json(['data' => $opagos]);
    }

    public function ajaxShowOpagoCiudadanoRenta($idrenta)
    {
        // Consulta para obtener solo las inscripciones activas
        $opagos = DB::table('opagos')
            ->join('rentas', 'opagos.idrenta', '=', 'rentas.idrenta')
            ->join('conceptos', 'opagos.idconcepto', '=', 'conceptos.idconcepto')
            ->where('rentas.idrenta', $idrenta)
            ->where('opagos.idconcepto', 3) // Filtra solo pagos de renta (según tu SQL original)
            ->select(
                'opagos.estatuspago',
                'opagos.montototal',
                'opagos.foliocc',
                'opagos.foliocajas',
                'opagos.fechapago',
                'conceptos.concepto'
            )
            ->get();

        // Retornar los datos en JSON
        return response()->json(['data' => $opagos]);
    }

    public function ajaxReadTarjeton()
    {
        // Consulta para obtener solo las inscripciones activas
        $tarjetones = DB::table('inscripciones')
            ->join('alumnos', 'inscripciones.idalumno', '=', 'alumnos.idalumno')
            ->join('talleres', 'inscripciones.idtaller', '=', 'talleres.idtaller')
            ->where('inscripciones.estatus', 1) // Filtra inscripciones activas
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('opagos')
                    ->whereColumn('opagos.idinscripcion', 'inscripciones.idinscripcion')
                    ->where('opagos.idconcepto', 4)
                    ->where('opagos.estatuspago', 0);
            })
            ->select(
                'inscripciones.idinscripcion',
                DB::raw("CONCAT(alumnos.name, ' ', alumnos.apellidop, ' ', alumnos.apellidom) AS nombreCompleto"),
                'talleres.nametaller',
                DB::raw("CONCAT(talleres.fechainicio, ' - ', talleres.fechafin) AS periodo")
            )
            ->get();

        // Retornar los datos en formato JSON para DataTables
        return response()->json(['data' => $tarjetones]);
    }

    public function tbloprepotarjeton()
    {
        // Consulta para obtener solo las inscripciones activas
        $opagos = DB::table('opagos')
            ->join('inscripciones', 'opagos.idinscripcion', '=', 'inscripciones.idinscripcion')
            ->join('alumnos', 'inscripciones.idalumno', '=', 'alumnos.idalumno')
            ->join('talleres', 'inscripciones.idtaller', '=', 'talleres.idtaller')
            ->where('opagos.idconcepto', 4) // Filtra solo pagos de inscripción
            // ->where('inscripciones.estatus', 1) // Filtra inscripciones activas
            ->select(
                'opagos.idopago',
                'opagos.mespago',
                'opagos.estatuspago',
                'opagos.fechalimite',
                'opagos.montototal',
                'opagos.foliocc',
                'opagos.foliocajas',
                'opagos.fechapago',
                'opagos.idinscripcion',
                'opagos.idconcepto',
                'inscripciones.idinscripcion',
                DB::raw("CONCAT(alumnos.name, ' ', alumnos.apellidop, ' ', alumnos.apellidom) AS nombreCompleto"),
                'talleres.nametaller',
                DB::raw("CONCAT(talleres.fechainicio, ' - ', talleres.fechafin) AS periodo")
            )
            ->get();
        // dd($opagos);

        // Retornar los datos en formato JSON para DataTables
        return response()->json(['data' => $opagos]);
    }

    public function ajaxConsultaTarjeton($estatus)
    {
        $query = DB::table('opagos')
            ->join('inscripciones', 'opagos.idinscripcion', '=', 'inscripciones.idinscripcion')
            ->join('tarjetones', 'opagos.idopago', '=', 'tarjetones.idopago')
            ->join('alumnos', 'inscripciones.idalumno', '=', 'alumnos.idalumno')
            ->join('talleres', 'inscripciones.idtaller', '=', 'talleres.idtaller')
            ->where('opagos.idconcepto', 4)
            ->select(
                'tarjetones.idtarjeton',
                'tarjetones.estatus',
                'opagos.idopago',
                'opagos.mespago',
                'opagos.estatuspago',
                'opagos.fechalimite',
                'opagos.montototal',
                'opagos.foliocc',
                'opagos.foliocajas',
                'opagos.fechapago',
                'opagos.idinscripcion',
                'opagos.idconcepto',
                'inscripciones.idinscripcion',
                DB::raw("CONCAT(alumnos.name, ' ', alumnos.apellidop, ' ', alumnos.apellidom) AS nombreCompleto"),
                'talleres.nametaller',
                DB::raw("CONCAT(talleres.fechainicio, ' - ', talleres.fechafin) AS periodo")
            );

        if ($estatus == 'act') {
            $query->where('tarjetones.estatus', '1');
        } elseif ($estatus == 'ina') {
            $query->where('tarjetones.estatus', '0');
        }

        $tarjetones = $query->get();

        return response()->json(['data' => $tarjetones]);
    }
    
    public function ajaxShowOpagoAlumnoRepoTarjeton($idopago)
    {
        // Consulta para obtener solo las inscripciones activas
        $opagos = DB::table('opagos')
            ->join('inscripciones', 'opagos.idinscripcion', '=', 'inscripciones.idinscripcion')
            ->join('alumnos', 'inscripciones.idalumno', '=', 'alumnos.idalumno')
            ->join('talleres', 'inscripciones.idtaller', '=', 'talleres.idtaller')
            ->join('conceptos', 'opagos.idconcepto', '=', 'conceptos.idconcepto')
            ->where('opagos.idopago', $idopago) // Filtra el pago específico
            ->select(
                'opagos.idopago',
                'opagos.mespago',
                'opagos.estatuspago',
                'opagos.fechalimite',
                'opagos.montototal',
                'opagos.foliocc',
                'opagos.foliocajas',
                'opagos.fechapago',
                'conceptos.concepto',
                DB::raw("CONCAT(alumnos.name, ' ', alumnos.apellidop, ' ', alumnos.apellidom) AS nombreCompleto"),
                'talleres.nametaller',
                DB::raw("CONCAT(talleres.fechainicio, ' - ', talleres.fechafin) AS periodo")
            )
            ->first();
        // Retornar los datos en JSON
        return response()->json(['data' => $opagos]);
    }


    public function tblhistorico()
    {
        // Consulta para obtener solo las inscripciones activas
        $historico = DB::table('inscripciones')
            ->join('alumnos', 'inscripciones.idalumno', '=', 'alumnos.idalumno')
            ->join('talleres', 'inscripciones.idtaller', '=', 'talleres.idtaller')
            ->select(
                'inscripciones.idinscripcion',
                DB::raw("CONCAT(alumnos.name, ' ', alumnos.apellidop, ' ', alumnos.apellidom) AS nombreCompleto"),
                'talleres.nametaller',
                DB::raw("CONCAT(talleres.fechainicio, ' - ', talleres.fechafin) AS periodo"),
                'inscripciones.estatus'
            )
            ->get();

        // Retornar los datos en formato JSON para DataTables
        return response()->json(['data' => $historico]);
    }

    public function ajaxShowHistoricoIns($idinscripcion)
    {
        // Consulta para obtener solo las inscripciones activas
        $historico = DB::table('inscripciones')
            ->join('alumnos', 'inscripciones.idalumno', '=', 'alumnos.idalumno')
            ->join('talleres', 'inscripciones.idtaller', '=', 'talleres.idtaller')
            ->join('opagos', 'inscripciones.idinscripcion', '=', 'opagos.idinscripcion')
            ->join('conceptos', 'opagos.idconcepto', '=', 'conceptos.idconcepto')
            ->where('inscripciones.idinscripcion', $idinscripcion)
            ->select(
                'inscripciones.idinscripcion',
                DB::raw("CONCAT(alumnos.name, ' ', alumnos.apellidop, ' ', alumnos.apellidom) AS nombreCompleto"),
                'talleres.nametaller',
                'inscripciones.estatus',
                'opagos.foliocc',
                'opagos.foliocajas',
                'opagos.montototal',
                'opagos.fechapago',
                'opagos.estatuspago',
                'conceptos.concepto'
            )
            ->first();

        // Retornar los datos en JSON
        return response()->json(['data' => $historico]);
    }

    public function ajaxShowHistoricoMens($idinscripcion)
    {
        $historico = DB::table('opagos')
            ->join('conceptos', 'opagos.idconcepto', '=', 'conceptos.idconcepto')
            ->join('inscripciones', 'opagos.idinscripcion', '=', 'inscripciones.idinscripcion')
            ->join('alumnos', 'inscripciones.idalumno', '=', 'alumnos.idalumno')
            ->where('opagos.idinscripcion', $idinscripcion)
            ->select(
                DB::raw("CONCAT(alumnos.name, ' ', alumnos.apellidop, ' ', alumnos.apellidom) as nombre"),
                'opagos.mespago',
                'opagos.estatuspago',
                'opagos.fechalimite',
                'opagos.montototal',
                'opagos.foliocc',
                'opagos.foliocajas',
                'opagos.fechapago',
                'conceptos.concepto'
            )
            ->get();

        $nombre = $historico->first()->nombre ?? null;

        return response()->json([
            'data' => $historico,
            'nombre' => $nombre
        ]);
    }
}
