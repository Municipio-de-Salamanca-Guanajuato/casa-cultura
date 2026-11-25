<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Alumno;
use App\Models\Inscripcion;
use App\Models\Opago;
use App\Models\Taller;
use App\Models\TallerHorario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use Throwable;
use Carbon\Carbon;
use Illuminate\Support\Str; // Para generar el folio

class InscripcionController extends Controller
{
    public function index()
    {
        return view('inscripciones');
    }

    public function saveInscripcion(Request $request)
    {
        try {
            // Validar los datos del formulario 
            $request->validate([
                'id_alumno' => 'required|exists:alumnos,idalumno',
                'id_taller' => 'required|exists:talleres,idtaller',
            ]);

            // Verificar si el alumno tiene una inscripción activa
            $inscripcionActiva = Inscripcion::where('idalumno', $request->id_alumno)
                ->where('estatus', 1)
                ->exists();

            // Si existe una inscripción activa, no puede inscribirse a otro taller
            if ($inscripcionActiva) {
                return redirect()->back()->with('error', 'El alumno ya tiene una inscripción activa y no puede inscribirse a otro taller.');
            }

            // Obtener el taller correspondiente usando el ID almacenado
            $taller = Taller::find($request->id_taller);
            if (!$taller) {
                return response()->json(['error' => 'Taller no encontrado'], 404);
            }
            // Verificar si hay lugares disponibles
            if ($taller->disponibles <= 0) {
                return redirect()->back()->with('status', 'No hay lugares disponibles en este taller.');
            }

            // Verificar que la edad del alumno (a la fecha de inscripción) entre en el rango del taller
            $alumno = Alumno::find($request->id_alumno);
            $errorEdad = $this->validarEdad($alumno, $taller);
            if ($errorEdad) {
                return redirect()->back()->with('error', $errorEdad);
            }

            // Verificar que la persona no tenga otro taller activo en el mismo horario (también con registros -1, -02...)
            $errorChoque = $this->validarChoqueHorario($alumno, $taller, 'No se puede inscribir al alumno,');
            if ($errorChoque) {
                return redirect()->back()->with('error', $errorChoque);
            }

            // Fechas importantes
            $fechaInscripcion = Carbon::now();
            $fechaInicioTaller = Carbon::parse($taller->fechainicio); // Fecha inicio del taller
            $fechaFinTaller = Carbon::parse($taller->fechafin); // Fecha fin del taller

            // Nueva validación: Fecha de inscripción vs fin del taller
            if ($fechaInscripcion->gt($fechaFinTaller)) {
                return redirect()->back()->with('error', 'No puedes inscribirte después de la fecha de fin del taller.');
            }

            // Verificar deuda de periodos anteriores: inscripción activa que ya terminó y con pagos incompletos
            // $hasUnpaidPrevious = DB::table('inscripciones as i')
            //     ->join('talleres as t', 'i.idtaller', '=', 't.idtaller')
            //     ->leftJoin('opagos as o', 'o.idinscripcion', '=', 'i.idinscripcion')
            //     ->where('i.idalumno', $request->id_alumno)
            //     ->where('i.estatus', 1)
            //     ->where('t.fechafin', '<', Carbon::now())
            //     ->groupBy('i.idinscripcion')
            //     ->havingRaw('SUM(CASE WHEN o.estatuspago = 1 THEN 1 ELSE 0 END) < COUNT(o.idopago)')
            //     ->exists();

            // if ($hasUnpaidPrevious) {
            //     return redirect()->back()->with('error', 'El alumno tiene pagos pendientes del periodo anterior. Debe pagar o darse de baja antes de inscribirse a otro taller.');
            // }

            // Crear una nueva inscripción y asignar los datos del formulario
            $ins = new Inscripcion();
            $ins->idalumno = $request->id_alumno;
            $ins->idtaller = $request->id_taller;
            // Guardar la nueva inscripción en la base de datos 
            $ins->save();

            // Restar 1 a los lugares disponibles del taller
            $taller->disponibles -= 1;
            $taller->save();

            // Verificar si la inscripción ocurre en los últimos 5 días del mes
            // $ultimoDiaDelMes = $fechaInscripcion->copy()->endOfMonth();
            // $diasRestantes = $ultimoDiaDelMes->diffInDays($fechaInscripcion);

            // if ($diasRestantes <= 5) {
            //     // Si se inscribe en los últimos 5 días del mes, empezar pagos desde el siguiente mes
            //     $fechaInicioMensualidades = $fechaInscripcion->copy()->addMonth()->startOfMonth();
            // } else {
            //     // Si se inscribe antes de los últimos 5 días, comenzar desde el mes actual
            //     $fechaInicioMensualidades = max($fechaInscripcion->copy()->startOfMonth(), $fechaInicioTaller->copy()->startOfMonth());
            // }

            // Si la inscripción es después del inicio del taller, comenzar desde el mes de inscripción
            // if ($fechaInscripcion->greaterThan($fechaInicioTaller)) {
            //     $fechaInicioMensualidades = $fechaInscripcion->copy()->startOfMonth();
            // } else {
            //     $fechaInicioMensualidades = $fechaInicioTaller->copy()->startOfMonth();
            // }

            // Verificar si la inscripción es en los últimos 5 días del mes
            $ultimosDiasDelMes = $fechaInscripcion->copy()->endOfMonth()->subDays(6);
            if ($fechaInscripcion->greaterThanOrEqualTo($ultimosDiasDelMes)) {
                // Si es así, comenzar las mensualidades desde el siguiente mes
                // $fechaInicioMensualidades = $fechaInscripcion->copy()->addMonth()->startOfMonth();
                // Caso 1: Si el taller aún no ha comenzado, comenzar las mensualidades desde el inicio del taller
                if ($fechaInscripcion->lessThan($fechaInicioTaller)) {
                    $fechaInicioMensualidades = $fechaInicioTaller->copy()->startOfMonth();
                    // dd('Inicio de mensualidades');
                }
                // Caso 2: Si el taller ya comenzó, comenzar las mensualidades desde el siguiente mes
                else {
                    $fechaInicioMensualidades = $fechaInscripcion->copy()->addMonthNoOverflow()->startOfMonth();
                    // dd('Mensualidades desde el siguiente mes: ' . $fechaInicioMensualidades);
                    // Si el taller YA comenzó, pero el mes de inicio de mensualidades es MAYOR al mes de fin del taller, ajustar.
                    // (Esto evita casos raros donde el taller termina antes del siguiente mes)
                    if ($fechaInicioMensualidades->gt($fechaFinTaller)) {
                        $fechaInicioMensualidades = $fechaFinTaller->copy()->startOfMonth();
                    }
                }
            } else {
                // Si no, comenzar las mensualidades desde el mes actual (o el mes de inicio del taller)
                if ($fechaInscripcion->greaterThan($fechaInicioTaller)) {
                    $fechaInicioMensualidades = $fechaInscripcion->copy()->startOfMonth();
                } else {
                    $fechaInicioMensualidades = $fechaInicioTaller->copy()->startOfMonth();
                }
                // Si no es en los últimos 5 días, comenzar las mensualidades desde el mes actual (o el mes de inicio del taller)
                // $fechaInicioMensualidades = max(
                //     $fechaInscripcion->copy()->startOfMonth(),
                //     $fechaInicioTaller->copy()->startOfMonth()
                // );
            }

            // Calcular la cantidad de meses entre la fecha de inicio de mensualidades y la fecha fin del taller
            $meses = $fechaInicioMensualidades->diffInMonths($fechaFinTaller->endOfMonth());

            // Generar el folio inicial
            $anioActual = $fechaInscripcion->year;
            $ultimoFolio = Opago::whereYear('created_at', $anioActual)->max('foliocc');
            $folioConsecutivo = $ultimoFolio ? intval(explode('/', $ultimoFolio)[0]) + 1 : 1;
            $folioBase = str_pad($folioConsecutivo, 6, '0', STR_PAD_LEFT) . '/' . $anioActual;

            // Omitir cálculo de meses si la mensualidad es 0.00
            if ($taller->mensualidad == 0.00) {
                $meses = 0; // Forzar a 0 meses
            }

            // Verificar si la inscripción es gratuita (costo = 0.00)
            $inscripcionGratuita = ($taller->inscripcion == 0.00);

            // Orden de pago para la inscripción
            Opago::create([
                'idinscripcion' => $ins->idinscripcion,
                'mespago' => $fechaInscripcion->format('m'),
                'estatuspago' => $inscripcionGratuita ? 1 : 0, // 1 si es gratuita, 0 si no
                'fechalimite' => $fechaInscripcion->copy()->endOfMonth()->format('Y-m-d'),
                'montototal' => $taller->inscripcion,
                'foliocc' => $folioBase,
                'foliocajas' => $inscripcionGratuita ? 'No requiere' : null,
                'fechapago' => $inscripcionGratuita ? $fechaInscripcion->format('Y-m-d H:i:s') : null,
                'idconcepto' => 1, // Inscripción
                'idgeneral' => 1,
            ]);

            // // Órdenes de pago para las mensualidades
            // for ($i = 0; $i <= $meses; $i++) {
            //     $fechaVencimiento = $fechaInicioMensualidades->copy()->addMonths($i);

            //     // Asegurarnos de que la fecha de vencimiento no exceda la fecha de fin del taller
            //     if ($fechaVencimiento->greaterThan($fechaFinTaller->endOfMonth())) {
            //         break; // Salir del bucle si la fecha de vencimiento excede la fecha de fin del taller
            //     }

            // Órdenes de pago para las mensualidades
            // Órdenes de pago para las mensualidades (SOLO si el costo es mayor a 0)
            if ($taller->mensualidad > 0.00) {  // <-- ¡Aquí está la clave!
                for ($i = 0; $i <= $meses; $i++) {
                    $fechaVencimiento = $fechaInicioMensualidades->copy()->addMonths($i);
                    Opago::create([
                        'idinscripcion' => $ins->idinscripcion,
                        'mespago' => $fechaVencimiento->format('m'),
                        'estatuspago' => 0,
                        'fechalimite' => $fechaVencimiento->endOfMonth()->format('Y-m-d'),
                        'montototal' => $taller->mensualidad,
                        'foliocc' => str_pad($folioConsecutivo + $i + 1, 6, '0', STR_PAD_LEFT) . '/' . $anioActual,
                        'foliocajas' => null,
                        'fechapago' => null,
                        'idconcepto' => 2, // Mensualidad
                        'idgeneral' => 1,
                    ]);
                }
            }
            // Redireccionar con un mensaje de éxito 
            return redirect()->back()->with('success', 'Inscripción exitosa.');
        } catch (Exception $e) {
            // Manejar cualquier excepción que ocurra
            return redirect()->back()->with('error', 'Ocurrió un error al procesar la inscripción: ' . $e->getMessage());
        }
    }

    // public function saveInscripcion(Request $request)
    // {
    //     // Validar los datos del formulario 
    //     $request->validate([
    //         'id_alumno' => 'required|exists:alumnos,idalumno',
    //         'id_taller' => 'required|exists:talleres,idtaller',
    //     ]);

    //     // Crear una nueva inscripción y asignar los datos del formulario
    //     $ins = new Inscripcion();
    //     $ins->idalumno = $request->id_alumno;
    //     $ins->idtaller = $request->id_taller;

    //     // Guardar la nueva inscripción en la base de datos 
    //     $ins->save();

    //     // Obtener el taller correspondiente usando el ID almacenado
    //     $taller = Taller::find($request->id_taller);
    //     if (!$taller) {
    //         return response()->json(['error' => 'Taller no encontrado'], 404);
    //     }

    //     // Fechas importantes
    //     $fechaInscripcion = Carbon::now();
    //     $fechaInicioTaller = Carbon::parse($taller->fechainicio); // Fecha inicio del taller
    //     $fechaFinTaller = Carbon::parse($taller->fechafin); // Fecha fin del taller

    //     // Si la inscripción es después del inicio del taller, comenzar desde el mes de inscripción
    //     if ($fechaInscripcion->greaterThan($fechaInicioTaller)) {
    //         $fechaInicioMensualidades = $fechaInscripcion->copy()->startOfMonth();
    //     } else {
    //         $fechaInicioMensualidades = $fechaInicioTaller->copy()->startOfMonth();
    //     }

    //     // Calcular la cantidad de meses entre la fecha de inicio de mensualidades y la fecha fin del taller
    //     $meses = $fechaInicioMensualidades->diffInMonths($fechaFinTaller); // +1 para incluir el mes actual

    //     // Generar el folio inicial
    //     $anioActual = $fechaInscripcion->year;
    //     $ultimoFolio = Opago::whereYear('created_at', $anioActual)->max('foliocc');
    //     $folioConsecutivo = $ultimoFolio ? intval(explode('/', $ultimoFolio)[0]) + 1 : 1;
    //     $folioBase = str_pad($folioConsecutivo, 6, '0', STR_PAD_LEFT) . '/' . $anioActual;

    //     // Orden de pago para la inscripción
    //     Opago::create([
    //         'idinscripcion' => $ins->idinscripcion,
    //         'mespago' => $fechaInscripcion->format('m'),
    //         'estatuspago' => 0,
    //         'fechalimite' => $fechaInscripcion->copy()->endOfMonth()->format('Y-m-d'),
    //         'montototal' => $taller->inscripcion,
    //         'foliocc' => $folioBase,
    //         'foliocajas' => null,
    //         'fechapago' => null,
    //         'idconcepto' => 1, // Inscripción
    //     ]);

    //     // Órdenes de pago para las mensualidades
    //     for ($i = 0; $i < $meses; $i++) {
    //         $fechaVencimiento = $fechaInicioMensualidades->copy()->addMonths($i);
    //         Opago::create([
    //             'idinscripcion' => $ins->idinscripcion,
    //             'mespago' => $fechaVencimiento->format('m'),
    //             'estatuspago' => 0,
    //             'fechalimite' => $fechaVencimiento->endOfMonth()->format('Y-m-d'),
    //             'montototal' => $taller->mensualidad,
    //             'foliocc' => str_pad($folioConsecutivo + $i + 1, 6, '0', STR_PAD_LEFT) . '/' . $anioActual,
    //             'foliocajas' => null,
    //             'fechapago' => null,
    //             'idconcepto' => 2, // Mensualidad
    //         ]);
    //     }

    //     // Redireccionar con un mensaje de éxito 
    //     return redirect()->route('inscripcion')->with('success', 'Inscripción exitosa.');
    // }

    // public function saveInscripcion(Request $request)
    // {
    //     // dd($request);
    //     // Validar los datos del formulario 
    //     $request->validate([
    //         'id_alumno' => 'required|exists:alumnos,idalumno',
    //         'id_taller' => 'required|exists:talleres,idtaller',
    //     ]);

    //     // Crear una nueva inscripcion y asignar los datos del formulario
    //     $ins = new Inscripcion();
    //     $ins->idalumno = $request->id_alumno;
    //     $ins->idtaller = $request->id_taller;

    //     // Guardar el nuevo ins en la base de datos 
    //     $ins->save();

    //     // dd($request->all());

    //     // Obtener el taller correspondiente usando la relación
    //     // Obtener el taller correspondiente usando el ID almacenado
    //     $taller = Taller::find($request->id_taller);
    //     // Verifica que se encontró el taller
    //     if (!$taller) {
    //         return response()->json(['error' => 'Taller no encontrado'], 404);
    //     }

    //     // Ahora que ya tenemos el ID de inscripción, generamos las órdenes de pago
    //     $fechaInscripcion = Carbon::now();
    //     // $fechaInscripcion = date('n');
    //     $fechaInicioTaller = Carbon::parse($taller->fechainicio); // Fecha inicio del taller
    //     $fechaFin = Carbon::parse($taller->fechafin);
    //     $meses = $fechaInscripcion->diffInMonths($fechaFin); // Sumar 1 si se incluye el mes actual
    //     $anioActual = $fechaInscripcion->year;

    //     // Generar el folio inicial
    //     // $ultimoFolio = Opago::whereYear('created_at', $anioActual)->max('foliocc');
    //     // $folioConsecutivo = $ultimoFolio ? intval(substr($ultimoFolio, 0, 4)) + 1 : 1;
    //     // $folioBase = str_pad($folioConsecutivo, 4, '0', STR_PAD_LEFT) . '/' . $anioActual;
    //     // Generar el folio inicial
    //     $ultimoFolio = Opago::whereYear('created_at', $anioActual)->max('foliocc');
    //     $folioConsecutivo = $ultimoFolio ? intval(explode('/', $ultimoFolio)[0]) + 1 : 1;

    //     // Ajustar a 6 dígitos
    //     $folioBase = str_pad($folioConsecutivo, 6, '0', STR_PAD_LEFT) . '/' . $anioActual;

    //     // Verifica si es el primer folio del año actual
    //     // if ($ultimoFolio) {
    //     //     // Extrae la parte numérica del folio y suma 1
    //     //     $folioConsecutivo = intval(explode('/', $ultimoFolio)[0]) + 1;
    //     // } else {
    //     //     $folioConsecutivo = 1; // Si no hay folios este año, empezamos en 1
    //     // }

    //     // Ajustar a 5 dígitos
    //     // $folioBase = str_pad($folioConsecutivo, 6, '0', STR_PAD_LEFT) . '/' . $anioActual;

    //     // Orden de pago para la inscripción
    //     Opago::create([
    //         'idinscripcion' => $ins->idinscripcion,
    //         'mespago' => $fechaInscripcion->format('m'),
    //         // 'estatuspago' => 0,
    //         'fechalimite' => $fechaInscripcion->endOfMonth()->format('Y-m-d'),
    //         'montototal' => $taller->inscripcion, // Cambiado para usar el objeto $taller
    //         'foliocc' => $folioBase,
    //         'foliocajas' => null,
    //         'fechapago' => null,
    //         'idconcepto' => 1, // Inscripción
    //     ]);

    //     // 📌 **Generar orden de pago para la primera mensualidad en el mismo mes**
    //     // Opago::create([
    //     //     'idinscripcion' => $ins->idinscripcion,
    //     //     'mespago' => $fechaInscripcion->format('m'),
    //     //     'fechalimite' => $fechaInscripcion->endOfMonth()->format('Y-m-d'),
    //     //     'montototal' => $taller->mensualidad,
    //     //     'foliocc' => str_pad($folioConsecutivo + 1, 6, '0', STR_PAD_LEFT) . '/' . $anioActual,
    //     //     'foliocajas' => null,
    //     //     'fechapago' => null,
    //     //     'idconcepto' => 2, // Mensualidad
    //     // ]);

    //     // Órdenes de pago para las mensualidades
    //     for ($i = 0; $i < $meses; $i++) { // 🔹 Restamos 1 porque ya generamos la primera mensualidad arriba ($i = 1; $i < $meses - 1; $i++)
    //         $fechaVencimiento = $fechaInscripcion->copy()->addMonths($i);
    //         Opago::create([
    //             'idinscripcion' => $ins->idinscripcion,
    //             'mespago' => $fechaVencimiento->format('m'),
    //             // 'estatuspago' => 0,
    //             'fechalimite' => $fechaVencimiento->endOfMonth()->format('Y-m-d'),
    //             'montototal' => $taller->mensualidad, // Cambiado para usar el objeto $taller
    //             'foliocc' => str_pad($folioConsecutivo + $i + 1, 6, '0', STR_PAD_LEFT) . '/' . $anioActual,
    //             'foliocajas' => null,
    //             'fechapago' => null,
    //             'idconcepto' => 2, // Mensualidad
    //         ]);
    //     }

    //     // Redireccionar con un mensaje de éxito 
    //     return redirect()->route('inscripcion')->with('success', 'Inscripción exitosa.');
    // }

    /**
     * Revisa que la edad del alumno a hoy (fecha de inscripción) entre en el rango del taller.
     * Retorna el mensaje de error, o null si puede inscribirse.
     */
    private function validarEdad(Alumno $alumno, Taller $taller): ?string
    {
        $edad = $alumno->edadAl(now());

        if ($taller->admiteEdad($edad)) {
            return null;
        }

        if ($edad === null) {
            return 'No se pudo calcular la edad del alumno con su CURP (' . $alumno->curp . '). El taller ' . $taller->nametaller . ' es para alumnos de ' . $taller->rangoEdadTexto() . '. Revisa la CURP del alumno.';
        }

        return 'El alumno tiene ' . $edad . ' años y el taller ' . $taller->nametaller . ' es para alumnos de ' . $taller->rangoEdadTexto() . '.';
    }

    /**
     * Revisa que la persona (misma CURP base, incluyendo registros -1, -02, etc.) no tenga otra
     * inscripción activa en un taller cuyo horario choque con el del taller elegido.
     * Retorna el mensaje de error, o null si puede inscribirse.
     */
    private function validarChoqueHorario(Alumno $alumno, Taller $taller, string $accion, ?int $excluirInscripcion = null): ?string
    {
        $choques = TallerHorario::buscarChoquesAlumno($alumno->curp, $taller->idtaller, $excluirInscripcion);
        if (empty($choques)) {
            return null;
        }

        $c = $choques[0];
        $mensaje = $accion . ' ya está inscrito en el taller "' . $c['taller'] . '" el '
            . TallerHorario::DIAS[$c['dia']] . ' de ' . $c['hora_inicio'] . ' a ' . $c['hora_fin'];

        // Si la otra inscripción es de otro registro de la misma persona (-1, -02...), se indica cuál
        if (trim((string) $c['curp']) !== trim((string) $alumno->curp)) {
            $mensaje .= ' (registro ' . $c['curp'] . ')';
        }
        $mensaje .= ', que choca con el horario del taller "' . $taller->nametaller . '".';

        if (count($choques) > 1) {
            $mensaje .= ' Hay ' . (count($choques) - 1) . ' choque(s) más.';
        }

        return $mensaje;
    }

    public function actInscripcion(Request $request)
    {
        // dd($request);
        // Validar los datos recibidos
        $request->validate([
            'idinscripcion_2' => 'required|exists:inscripciones,idinscripcion', // Verifica que la inscripción exista
            'id_alumno_2' => 'required|exists:alumnos,idalumno', // Verifica que el alumno exista
            'id_taller_2' => 'required|exists:talleres,idtaller', // Verifica que el taller exista
        ]);

        try {
            // Buscar la inscripción por su ID
            $inscripcion = Inscripcion::find($request->idinscripcion_2);

            if (!$inscripcion) {
                return redirect()->back()->with('status', 'Inscripción no encontrada.');
            }

            // Verificar que la edad del alumno entre en el rango del taller
            $errorEdad = $this->validarEdad(Alumno::find($request->id_alumno_2), Taller::find($request->id_taller_2));
            if ($errorEdad) {
                return redirect()->back()->with('error', $errorEdad);
            }

            // Actualizar los datos de la inscripción
            $inscripcion->idalumno = $request->id_alumno_2;
            $inscripcion->idtaller = $request->id_taller_2;
            $inscripcion->save();

            return redirect()->back()->with('status', 'Inscripción actualizada con éxito.');
        } catch (Throwable $th) {
            // Registrar el error
            Log::error('Error al actualizar la inscripción: ' . $th->getMessage());

            return redirect()->back()->with('status', 'Ocurrió un error al actualizar la inscripción.');
        }
    }

    public function estInscripcion($idinscripcion, $estatus)
    {
        // dd($idinscripcion, $estatus);
        try {
            // Busca la inscripción por su ID
            $inscripcion = Inscripcion::findOrFail($idinscripcion);
            $taller = Taller::find($inscripcion->idtaller);
            if (!$taller) {
                return redirect()->back()->with('status', 'Taller no encontrado.');
            }
            // Verifica la acción y cambia el estatus
            if ($estatus === 'baja') {
                $inscripcion->estatus = 0; // Cambia el estatus a 0 (baja)
                $inscripcion->save();

                // Sumar 1 a los lugares disponibles del taller
                $taller->disponibles += 1;
                $taller->save();

                return redirect()->back()->with('status', 'La inscripción ha sido dada de baja correctamente.');
            } elseif ($estatus === 'alta') {
                // Verificar si el alumno ya tiene otra inscripción activa
                $inscripcionActiva = Inscripcion::where('idalumno', $inscripcion->idalumno)
                    ->where('estatus', 1)
                    ->where('idinscripcion', '!=', $inscripcion->idinscripcion) // Evita comparar consigo misma
                    ->exists();

                if ($inscripcionActiva) {
                    return redirect()->back()->with('status', 'El alumno ya está inscrito en otro taller y no puede reactivar esta inscripción.');
                }

                // Verificar si hay lugares disponibles antes de reactivar la inscripción
                if ($taller->disponibles <= 0) {
                    return redirect()->back()->with('status', 'No hay lugares disponibles en este taller.');
                }

                // Verificar que la persona no tenga otro taller activo en el mismo horario (también con registros -1, -02...)
                $errorChoque = $this->validarChoqueHorario(Alumno::find($inscripcion->idalumno), $taller, 'No se puede reactivar la inscripción, el alumno', $inscripcion->idinscripcion);
                if ($errorChoque) {
                    return redirect()->back()->with('error', $errorChoque);
                }

                // Alta: Cambia el estatus a 1 (activa)
                $inscripcion->estatus = 1; // Cambia el estatus a 1 (alta)
                $inscripcion->save();

                // Restar 1 a los lugares disponibles del taller
                $taller->disponibles -= 1;
                $taller->save();

                return redirect()->back()->with('status', 'La inscripción ha sido dada de alta correctamente.');
            }

            // Si el estatus no es "baja" ni "alta", redirige con un mensaje de error
            return redirect()->back()->with('status', 'Acción no válida.');
        } catch (Exception $e) {
            // Maneja errores y registra el problema
            Log::error('Error al cambiar el estatus de la inscripción: ' . $e->getMessage());
            return redirect()->back()->with('status', 'Error al cambiar el estatus de la inscripción.');
        }
    }

    public function deleteInscripcion($idinscripcion)
    {
        // dd($idinscripcion);
        try {
            // Buscar la inscripción por su ID
            $inscripcion = Inscripcion::findOrFail($idinscripcion);

            DB::transaction(function () use ($inscripcion) {
                // Si estaba activa, regresar su lugar al taller (sin pasar del cupo)
                if ((int) $inscripcion->estatus === 1) {
                    DB::table('talleres')->where('idtaller', $inscripcion->idtaller)
                        ->update(['disponibles' => DB::raw('LEAST(cupo, disponibles + 1)')]);
                }

                // Eliminar la inscripción
                $inscripcion->delete();
            });

            // Redirigir con un mensaje de éxito
            return redirect()->back()->with('status', 'La inscripción fue eliminada correctamente.');
        } catch (ModelNotFoundException $e) {
            // Manejar el caso en que la inscripción no se encuentre
            return redirect()->back()->with('status', 'Inscripción no encontrada.');
        } catch (Exception $e) {
            // Manejar otros errores
            Log::error('Error al eliminar la inscripción: ' . $e->getMessage());
            return redirect()->back()->with('status', 'Error al eliminar la inscripción.');
        }
    }

    public function previewBajasInscripciones()
    {
        $today = Carbon::today()->toDateString();

        $ids = DB::table('inscripciones as i')
            ->join('talleres as t', 'i.idtaller', '=', 't.idtaller')
            ->leftJoin('opagos as o', 'o.idinscripcion', '=', 'i.idinscripcion')
            ->where('i.estatus', '1')
            ->where('t.fechafin', '<', $today)
            ->groupBy('i.idinscripcion')
            ->havingRaw('COUNT(o.idopago) > 0')
            ->havingRaw("SUM(CASE WHEN o.estatuspago = '1' THEN 1 ELSE 0 END) = COUNT(o.idopago)")
            ->select('i.idinscripcion')
            ->pluck('i.idinscripcion');

        // Si quieres más info (nombre, taller) en el preview, obténlos:
        $data = DB::table('inscripciones as i')
            ->join('talleres as t', 'i.idtaller', '=', 't.idtaller')
            ->join('alumnos as a', 'i.idalumno', '=', 'a.idalumno')
            ->whereIn('i.idinscripcion', $ids)
            ->select(
                'i.idinscripcion',
                DB::raw("CONCAT(a.name,' ',a.apellidop,' ',a.apellidom) AS nombreCompleto"),
                't.nametaller',
                't.fechainicio',
                't.fechafin'
            )
            ->get();

        return response()->json([
            'count' => $ids->count(),
            'ids' => $ids,
            'data' => $data
        ]);
    }

    public function bajaMasivaInscripciones(Request $request)
    {
        $today = Carbon::today()->toDateString();

        $ids = DB::table('inscripciones as i')
            ->join('talleres as t', 'i.idtaller', '=', 't.idtaller')
            ->leftJoin('opagos as o', 'o.idinscripcion', '=', 'i.idinscripcion')
            ->where('i.estatus', '1')
            ->where('t.fechafin', '<', $today)
            ->groupBy('i.idinscripcion')
            ->havingRaw('COUNT(o.idopago) > 0')
            ->havingRaw("SUM(CASE WHEN o.estatuspago = '1' THEN 1 ELSE 0 END) = COUNT(o.idopago)")
            ->select('i.idinscripcion')
            ->pluck('i.idinscripcion');

        if ($ids->isEmpty()) {
            return response()->json(['message' => 'No se encontraron inscripciones a dar de baja.'], 200);
        }

        DB::beginTransaction();
        try {
            // Lugares que se liberan por taller (antes no se regresaban y el cupo disponible quedaba menor)
            $porTaller = DB::table('inscripciones')
                ->whereIn('idinscripcion', $ids)
                ->groupBy('idtaller')
                ->selectRaw('idtaller, COUNT(*) as total')
                ->pluck('total', 'idtaller');

            DB::table('inscripciones')
                ->whereIn('idinscripcion', $ids)
                ->update(['estatus' => '0', 'updated_at' => now()]);

            foreach ($porTaller as $idtaller => $total) {
                DB::table('talleres')->where('idtaller', $idtaller)
                    ->update(['disponibles' => DB::raw('LEAST(cupo, disponibles + ' . (int) $total . ')')]);
            }

            DB::commit();

            Log::info('Baja masiva inscripciones', ['ids' => $ids->toArray(), 'user_id' => auth()->id()]);

            return response()->json([
                'message' => 'Inscripciones dadas de baja correctamente.',
                'count' => $ids->count(),
                'ids' => $ids
            ], 200);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error baja masiva inscripciones', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Error al dar de baja. Revisa logs.'], 500);
        }
    }
}
