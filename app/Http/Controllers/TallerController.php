<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Aula;
use App\Models\Edificio;
use App\Models\Maestro;
use App\Models\Taller;
use App\Models\TallerHorario;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TallerController extends Controller
{
    public function index()
    {
        // Maestros activos para el select de los modales
        $maestros = Maestro::where('estatus', 1)->orderBy('nombre_comp')->get(['idmaestro', 'nombre_comp']);

        // Edificios activos con sus aulas activas, para el paso de horarios
        $edificios = Edificio::where('estatus', 1)
            ->with(['aulas' => fn ($q) => $q->where('estatus', 1)->orderBy('nombre')->select('idaula', 'idedificio', 'nombre')])
            ->orderBy('nombre')
            ->get(['idedificio', 'nombre']);

        // Para el modal de exportar horarios: talleres activos con horario, y sus periodos
        $talleresHorario = Taller::where('estatus', 1)
            ->whereHas('horarios', fn ($q) => $q->where('estatus', 1))
            ->orderBy('nametaller')
            ->get(['idtaller', 'nametaller', 'fechainicio', 'fechafin'])
            ->map(fn ($t) => [
                'idtaller' => $t->idtaller,
                'nametaller' => $t->nametaller,
                'fechainicio' => Carbon::parse($t->fechainicio)->toDateString(),
                'fechafin' => Carbon::parse($t->fechafin)->toDateString(),
            ]);

        $periodos = $talleresHorario
            ->groupBy(fn ($t) => $t['fechainicio'] . '|' . $t['fechafin'])
            ->map(fn ($grupo, $clave) => [
                'valor' => $clave,
                'fechainicio' => $grupo[0]['fechainicio'],
                'fechafin' => $grupo[0]['fechafin'],
                // Talleres cuyas fechas se cruzan con el periodo (lo mismo que trae el Excel)
                'talleres' => $talleresHorario
                    ->filter(fn ($t) => $t['fechainicio'] <= $grupo[0]['fechafin'] && $t['fechafin'] >= $grupo[0]['fechainicio'])
                    ->count(),
            ])
            ->sortBy([['fechainicio', 'desc'], ['fechafin', 'desc']])
            ->values();

        return view('talleres', compact('maestros', 'edificios', 'talleresHorario', 'periodos'));
    }

    /**
     * Copia el nombre del maestro del catálogo a talleres.maestro, que es lo que leen
     * el pase de lista, el reporte general y el tarjetón (la columna es de 100 caracteres).
     */
    public static function textoMaestro(Maestro $maestro): string
    {
        return mb_substr($maestro->nombre_comp, 0, 100, 'UTF-8');
    }

    // Rango permitido para los horarios (en bloques de 30 minutos)
    private const HORA_MIN = '09:00';
    private const HORA_MAX = '21:00';

    /**
     * Valida los bloques de horario del formulario y los convierte en una fila por día.
     * Cada bloque llega como: horarios[i][dias][], horarios[i][idaula], horarios[i][hora_inicio], horarios[i][hora_fin].
     *
     * @return array<int, array{dia:int, idaula:int, hora_inicio:string, hora_fin:string}>
     * @throws ValidationException
     */
    private function normalizarHorarios(Request $request, ?Taller $taller = null): array
    {
        $bloques = $request->input('horarios', []);
        if (!is_array($bloques)) {
            throw ValidationException::withMessages(['horarios' => 'Los horarios no tienen un formato válido.']);
        }

        // Aulas activas, más las que el taller ya usa (por si alguna se desactivó después)
        $aulasPermitidas = Aula::where('estatus', 1)->pluck('idaula')->all();
        if ($taller) {
            $aulasPermitidas = array_merge($aulasPermitidas, $taller->horarios()->where('estatus', 1)->pluck('idaula')->all());
        }

        $errores = [];
        $filas = [];

        foreach (array_values($bloques) as $i => $bloque) {
            $num = $i + 1;
            $dias = array_unique(array_map('intval', (array) ($bloque['dias'] ?? [])));
            $idaula = (int) ($bloque['idaula'] ?? 0);
            $inicio = (string) ($bloque['hora_inicio'] ?? '');
            $fin = (string) ($bloque['hora_fin'] ?? '');

            if (empty($dias) || array_diff($dias, array_keys(TallerHorario::DIAS))) {
                $errores[] = "Horario {$num}: selecciona al menos un día válido (Lunes a Sábado).";
                continue;
            }
            if (!in_array($idaula, $aulasPermitidas)) {
                $errores[] = "Horario {$num}: el aula seleccionada no existe o está inactiva.";
                continue;
            }
            if (!$this->esHoraValida($inicio) || !$this->esHoraValida($fin)) {
                $errores[] = "Horario {$num}: las horas deben ser en punto o y media (ej. 16:00 o 16:30).";
                continue;
            }
            if ($inicio < self::HORA_MIN || $fin > self::HORA_MAX) {
                $errores[] = "Horario {$num}: el horario debe estar entre las " . self::HORA_MIN . ' y las ' . self::HORA_MAX . '.';
                continue;
            }
            if ($fin <= $inicio) {
                $errores[] = "Horario {$num}: la hora de fin debe ser mayor que la hora de inicio.";
                continue;
            }

            foreach ($dias as $dia) {
                $filas[] = ['dia' => $dia, 'idaula' => $idaula, 'hora_inicio' => $inicio . ':00', 'hora_fin' => $fin . ':00'];
            }
        }

        // El mismo taller no puede tener dos horarios encimados el mismo día
        $porDia = collect($filas)->groupBy('dia');
        foreach ($porDia as $dia => $delDia) {
            $ordenados = $delDia->sortBy('hora_inicio')->values();
            for ($k = 1; $k < $ordenados->count(); $k++) {
                $anterior = $ordenados[$k - 1];
                $actual = $ordenados[$k];
                if ($actual['hora_inicio'] < $anterior['hora_fin']) {
                    $errores[] = 'El ' . TallerHorario::DIAS[$dia] . ' hay dos horarios que se enciman: '
                        . substr($anterior['hora_inicio'], 0, 5) . '-' . substr($anterior['hora_fin'], 0, 5) . ' y '
                        . substr($actual['hora_inicio'], 0, 5) . '-' . substr($actual['hora_fin'], 0, 5) . '.';
                }
            }
        }

        if ($errores) {
            throw ValidationException::withMessages(['horarios' => $errores]);
        }

        return $filas;
    }

    /**
     * Revisa que los horarios no choquen (aula o maestro) con otros talleres activos del mismo periodo.
     * @throws ValidationException
     */
    private function validarChoques(array $filas, string $fechaIni, string $fechaFin, ?int $idmaestro, ?int $excluirTaller = null): void
    {
        if (empty($filas)) {
            return;
        }

        $choques = TallerHorario::buscarChoques($filas, $fechaIni, $fechaFin, $idmaestro, $excluirTaller);

        if ($choques) {
            throw ValidationException::withMessages([
                'horarios' => array_map([TallerHorario::class, 'mensajeChoque'], $choques),
            ]);
        }
    }

    /**
     * Al editar: los horarios AGREGADOS (los que no estaban activos) no deben chocar con otros talleres
     * de los alumnos ya inscritos en este taller (incluye sus registros -1, -02...).
     * @throws ValidationException
     */
    private function validarChoquesInscritos(Taller $taller, array $filas): void
    {
        $clave = fn ($h) => $h['dia'] . '|' . $h['idaula'] . '|' . $h['hora_inicio'] . '|' . $h['hora_fin'];
        $activos = $taller->horarios()->where('estatus', 1)->get()->map(fn ($h) => $clave($h->toArray()))->all();
        $agregados = array_values(array_filter($filas, fn ($f) => !in_array($clave($f), $activos, true)));

        if (empty($agregados)) {
            return;
        }

        $choques = TallerHorario::buscarChoquesInscritos($taller->idtaller, $agregados, $taller->fechainicio, $taller->fechafin);

        if ($choques) {
            throw ValidationException::withMessages([
                'horarios' => array_map(
                    fn ($c) => 'No se puede agregar el horario: ' . TallerHorario::mensajeChoqueInscrito($c),
                    $choques
                ),
            ]);
        }
    }

    // Hora en formato HH:MM con minutos 00 o 30
    private function esHoraValida(string $hora): bool
    {
        return (bool) preg_match('/^([01]\d|2[0-3]):(00|30)$/', $hora);
    }

    /**
     * Deja activos en la BD exactamente los horarios recibidos:
     * los que ya no vienen pasan a estatus 0 y los nuevos se agregan (o se reactivan si ya existían).
     */
    private function guardarHorarios(Taller $taller, array $filas): void
    {
        $clave = fn ($h) => $h['dia'] . '|' . $h['idaula'] . '|' . $h['hora_inicio'] . '|' . $h['hora_fin'];

        $nuevas = collect($filas)->keyBy($clave);
        $existentes = $taller->horarios()->get()->keyBy(fn ($h) => $clave($h->toArray()));

        // Desactivar los que ya no están
        foreach ($existentes as $key => $horario) {
            if ($horario->estatus == 1 && !$nuevas->has($key)) {
                $horario->estatus = 0;
                $horario->save();
            }
        }

        // Agregar los nuevos o reactivar los que ya existían
        foreach ($nuevas as $key => $fila) {
            $horario = $existentes->get($key);
            if ($horario) {
                if ($horario->estatus != 1) {
                    $horario->estatus = 1;
                    $horario->save();
                }
            } else {
                $taller->horarios()->create($fila);
            }
        }
    }

    public function saveTaller(Request $request)
    {
        // dd('Trabajando: ', $request);
        // Validar los datos recibidos
        $request->validate([
            // 'nametaller' => 'required|string|max:200|unique:talleres,nametaller', // Obligatorio, máximo 200 caracteres
            'nametaller' => [
                'required',
                'string',
                'max:200',
                Rule::unique('talleres', 'nametaller')->where(function ($query) {
                    return $query->where('estatus', 1); // Solo verifica unicidad si el estatus es 1 (activo)
                }),
            ],
            'idmaestro' => ['required', Rule::exists('maestros', 'idmaestro')->where('estatus', 1)], // Maestro del catálogo (activo)
            'fechaini' => 'required|date',         // Obligatorio, tipo fecha
            'fechafin' => 'required|date|after:fechaini', // Obligatorio, tipo fecha y después de fechainicio
            'cupo' => 'required|integer|min:1|max:65535', // Obligatorio, cadena numérica de hasta 20 caracteres
            'inscripcion' => 'required|numeric|min:0',  // Obligatorio, número mayor o igual a 0
            'mensualidad' => 'required|numeric|min:0',  // Obligatorio, número mayor o igual a 0
            'edad_min' => 'nullable|integer|min:0|max:99', // Opcional, vacío = sin límite
            'edad_max' => ['nullable', 'integer', 'min:0', 'max:99', Rule::when($request->filled('edad_min'), 'gte:edad_min')],
        ], [
            'idmaestro.required' => 'Selecciona el maestro del taller.',
            'idmaestro.exists' => 'El maestro seleccionado no existe o está inactivo.',
            'edad_max.gte' => 'La edad máxima no puede ser menor que la edad mínima.',
        ]);

        // Horarios (opcionales): se validan antes de guardar nada
        $horarios = $this->normalizarHorarios($request);
        $this->validarChoques($horarios, $request->fechaini, $request->fechafin, (int) $request->idmaestro);

        try {
            $maestro = Maestro::find($request->idmaestro);

            DB::beginTransaction();

            // Crear el taller
            $taller = new Taller();
            $taller->nametaller = mb_strtoupper($request->nametaller, 'UTF-8');
            $taller->idmaestro = $maestro->idmaestro;
            $taller->maestro = self::textoMaestro($maestro);
            $taller->fechainicio = $request->fechaini;
            $taller->fechafin = $request->fechafin;
            $taller->cupo = $request->cupo;
            $taller->disponibles = $request->cupo;
            $taller->inscripcion = $request->inscripcion;
            $taller->mensualidad = $request->mensualidad;
            $taller->edad_min = $request->filled('edad_min') ? $request->edad_min : null;
            $taller->edad_max = $request->filled('edad_max') ? $request->edad_max : null;
            $taller->save();

            $this->guardarHorarios($taller, $horarios);

            DB::commit();

            // Redirigir con mensaje de éxito
            return redirect()->back()->with('success', 'Taller guardado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            // Registrar el error y redirigir con mensaje de fallo
            Log::error('Error al guardar el taller: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al guardar el taller.');
        }
    }

    public function actTaller(Request $request)
    {
        // dd($request);
        $request->validate([
            'idtaller_2' => 'required|exists:talleres,idtaller', // Verifica que el ID exista
            'nametaller_2' => [
                'required',
                'string',
                'max:200',
                Rule::unique('talleres', 'nametaller')->ignore($request->idtaller_2, 'idTaller')
                    ->where(function ($query) {
                        return $query->where('estatus', 1);
                    })
            ], // Evita duplicados excluyendo el actual
            // 'required|string|max:200', // Obligatorio, máximo 200 caracteres
            // Maestro del catálogo: obligatorio si el taller ya tiene uno; opcional en talleres anteriores (solo texto)
            'idmaestro_2' => [
                Rule::requiredIf(fn () => Taller::whereKey($request->idtaller_2)->whereNotNull('idmaestro')->exists()),
                'nullable',
                function ($attribute, $value, $fail) use ($request) {
                    // Debe estar activo, salvo que sea el mismo que ya tenía el taller
                    $esElActual = Taller::whereKey($request->idtaller_2)->where('idmaestro', $value)->exists();
                    if (!$esElActual && !Maestro::whereKey($value)->where('estatus', 1)->exists()) {
                        $fail('El maestro seleccionado no existe o está inactivo.');
                    }
                },
            ],
            // 'fechaini_2' => 'nullable|date',         // Obligatorio, tipo fecha
            // 'fechafin_2' => 'nullable|date|after:fechaini_2', // Obligatorio, tipo fecha y después de fechainicio
            'cupo_2' => 'required|integer|min:1|max:65535', // Obligatorio, cadena numérica de hasta 20 caracteres
            'inscripcion_2' => 'required|numeric|min:0',  // Obligatorio, número mayor o igual a 0
            'mensualidad_2' => 'required|numeric|min:0',  // Obligatorio, número mayor o igual a 0
            'edad_min_2' => 'nullable|integer|min:0|max:99', // Opcional, vacío = sin límite
            'edad_max_2' => ['nullable', 'integer', 'min:0', 'max:99', Rule::when($request->filled('edad_min_2'), 'gte:edad_min_2')],
        ], [
            'idmaestro_2.required' => 'Selecciona el maestro del taller.',
            'edad_max_2.gte' => 'La edad máxima no puede ser menor que la edad mínima.',
        ]);

        // Buscar el taller
        $actTaller = Taller::find($request->idtaller_2);

        // Horarios: se validan antes de guardar nada
        $horarios = $this->normalizarHorarios($request, $actTaller);
        $idmaestroFinal = $request->filled('idmaestro_2') ? (int) $request->idmaestro_2 : $actTaller->idmaestro;
        $this->validarChoques($horarios, $actTaller->fechainicio, $actTaller->fechafin, $idmaestroFinal, $actTaller->idtaller);
        $this->validarChoquesInscritos($actTaller, $horarios);

        try {
            DB::beginTransaction();

            // Obtener la cantidad de alumnos inscritos en este taller
            $inscritos = DB::table('inscripciones')
                ->where('idtaller', $actTaller->idtaller)
                ->where('estatus', 1) // Solo contar inscripciones activas
                ->count();

            $actTaller->nametaller = mb_strtoupper($request->nametaller_2, 'UTF-8');
            // Si no eligen maestro (taller anterior), se conserva el texto que ya tenía
            if ($request->filled('idmaestro_2')) {
                $maestro = Maestro::find($request->idmaestro_2);
                $actTaller->idmaestro = $maestro->idmaestro;
                $actTaller->maestro = self::textoMaestro($maestro);
            }
            // $actTaller->fechainicio = $request->fechaini_2;
            // $actTaller->fechafin = $request->fechafin_2;
            $actTaller->cupo = $request->cupo_2;
            $actTaller->disponibles = max(0, $request->cupo_2 - $inscritos); // Restar inscritos al nuevo cupo
            $actTaller->inscripcion = $request->inscripcion_2;
            $actTaller->mensualidad = $request->mensualidad_2;
            $actTaller->edad_min = $request->filled('edad_min_2') ? $request->edad_min_2 : null;
            $actTaller->edad_max = $request->filled('edad_max_2') ? $request->edad_max_2 : null;
            $actTaller->save();

            $this->guardarHorarios($actTaller, $horarios);

            DB::commit();

            return redirect()->back()->with('success', 'Taller actualizado con éxito');
        } catch (\Throwable $th) {
            DB::rollBack();
            // Registrar el error
            Log::error('Error al actualizar el taller: ' . $th->getMessage());
            return redirect()->back()->with('status', 'Error');

            // Manejar errores y retornar una respuesta de error
            // return response()->json(['message' => 'Error al actualizar al taller', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Talleres activos cuya fecha fin ya pasó, con su número de inscripciones activas.
     */
    private function talleresTerminados()
    {
        return Taller::where('estatus', 1)
            ->whereDate('fechafin', '<', now()->toDateString())
            ->withCount(['inscripciones as inscritos' => fn ($q) => $q->where('estatus', 1)])
            ->orderBy('fechafin')->orderBy('nametaller')
            ->get(['idtaller', 'nametaller', 'maestro', 'fechainicio', 'fechafin']);
    }

    /**
     * Previsualización de la baja masiva: los que se pueden dar de baja (sin inscripciones activas)
     * y los que no (con inscripciones activas).
     */
    public function previewBajasTalleres()
    {
        $talleres = $this->talleresTerminados()->map(fn ($t) => [
            'idtaller' => $t->idtaller,
            'nametaller' => $t->nametaller,
            'maestro' => $t->maestro,
            'fechainicio' => Carbon::parse($t->fechainicio)->toDateString(),
            'fechafin' => Carbon::parse($t->fechafin)->toDateString(),
            'inscritos' => (int) $t->inscritos,
        ]);

        return response()->json([
            'candidatos' => $talleres->where('inscritos', 0)->values(),
            'bloqueados' => $talleres->where('inscritos', '>', 0)->values(),
        ]);
    }

    /**
     * Da de baja los talleres terminados SIN inscripciones activas.
     * Se vuelve a calcular aquí (no se confía en la lista del navegador).
     */
    public function bajaMasivaTalleres()
    {
        $talleres = $this->talleresTerminados()->where('inscritos', 0);

        if ($talleres->isEmpty()) {
            session()->flash('status', 'No hay talleres terminados para dar de baja.');
            return response()->json(['count' => 0]);
        }

        try {
            DB::beginTransaction();
            Taller::whereIn('idtaller', $talleres->pluck('idtaller'))
                ->where('estatus', 1)
                ->update(['estatus' => 0, 'updated_at' => now()]);
            DB::commit();

            Log::info('Baja masiva de talleres terminados', ['ids' => $talleres->pluck('idtaller')->all(), 'user_id' => auth()->id()]);

            session()->flash('status', 'Se dieron de baja ' . $talleres->count() . ' taller(es) terminado(s): '
                . $talleres->pluck('nametaller')->implode(', ') . '.');

            return response()->json(['count' => $talleres->count()]);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Error en la baja masiva de talleres: ' . $th->getMessage());
            return response()->json(['message' => 'Ocurrió un error al dar de baja los talleres.'], 500);
        }
    }

    /**
     * Descarga el Excel de horarios (una hoja por día y edificio) de un periodo:
     * todos los talleres activos que se encuentran en ese periodo, o solo uno.
     */
    public function exportarHorarios(Request $request)
    {
        $request->validate([
            'periodo' => ['required', 'regex:/^\d{4}-\d{2}-\d{2}\|\d{4}-\d{2}-\d{2}$/'],
            'idtaller' => ['nullable', 'integer', Rule::exists('talleres', 'idtaller')->where('estatus', 1)],
        ], [
            'periodo.required' => 'Selecciona el periodo.',
            'periodo.regex' => 'El periodo no es válido.',
            'idtaller.exists' => 'El taller seleccionado no existe o está inactivo.',
        ]);

        [$fechaIni, $fechaFin] = explode('|', $request->periodo);

        $export = new \App\Exports\HorariosExport($fechaIni, $fechaFin, $request->filled('idtaller') ? (int) $request->idtaller : null);
        $libro = $export->crear();

        return response()->streamDownload(function () use ($libro) {
            (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($libro))->save('php://output');
        }, $export->nombreArchivo(), [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function estTaller($idtaller, $estatus)
    {
        // dd('Trabajando en ello...');
        try {
            // Busca el taller por su ID
            $taller = Taller::findOrFail($idtaller);

            $inscritos = $taller->inscritosActivos();

            // Verifica la acción y cambia el estatus
            if ($estatus === 'baja') {
                if ($inscritos > 0) {
                    return redirect()->back()
                        ->with('error', 'No se puede dar de baja al taller porque tiene inscripciones activas.');
                }
                $taller->estatus = 0; // Cambia el estatus a 0 (baja)
                $taller->save();
                return redirect()->back()->with('status', 'El taller ha sido dado de baja correctamente.');
            }
            if ($estatus === 'alta') {
                // Verificar nombre duplicado
                $existe = Taller::where('nametaller', $taller->nametaller)
                    ->where('estatus', 1)
                    ->exists();

                if ($existe) {
                    return redirect()->back()
                        ->with('error', 'Ya existe un taller activo con el mismo nombre.');
                }

                // Verificar que su maestro y las aulas de sus horarios sigan activos
                if ($taller->idmaestro && $taller->maestroCatalogo && $taller->maestroCatalogo->estatus != 1) {
                    return redirect()->back()->with('error', 'No se puede activar este taller, el maestro ' . $taller->maestroCatalogo->nombre_comp
                        . ' está inactivo. Actívalo en Maestros primero.');
                }
                $aulasInactivas = $taller->horarios()->where('estatus', 1)
                    ->whereHas('aula', fn ($q) => $q->where('estatus', '!=', 1))
                    ->with('aula')->get()->pluck('aula.nombre')->unique();
                if ($aulasInactivas->isNotEmpty()) {
                    return redirect()->back()->with('error', 'No se puede activar este taller, tiene horarios en aula(s) inactiva(s): '
                        . $aulasInactivas->implode(', ') . '. Actívalas en Aulas primero.');
                }

                // Verificar que sus horarios no choquen con otro taller activo (aula o maestro)
                $filas = $taller->horarios()->where('estatus', 1)->get(['dia', 'idaula', 'hora_inicio', 'hora_fin'])->toArray();
                $choques = TallerHorario::buscarChoques($filas, $taller->fechainicio, $taller->fechafin, $taller->idmaestro, $taller->idtaller);
                if ($choques) {
                    $c = $choques[0];
                    $cuando = TallerHorario::DIAS[$c['dia']] . ' ' . $c['hora_inicio'] . '-' . $c['hora_fin'];
                    $mensaje = $c['tipo'] === 'aula'
                        ? 'No se puede activar este taller, debido a que el taller "' . $c['taller'] . '" está en el mismo horario y aula (' . $c['aula'] . ', ' . $c['edificio'] . ', ' . $cuando . ').'
                        : 'No se puede activar este taller, debido a que el maestro ' . $c['maestro'] . ' imparte el taller "' . $c['taller'] . '" en el mismo horario (' . $cuando . ').';
                    if (count($choques) > 1) {
                        $mensaje .= ' Hay ' . (count($choques) - 1) . ' choque(s) más.';
                    }
                    return redirect()->back()->with('error', $mensaje);
                }

                // Recalcular disponibles
                $taller->disponibles = max(0, $taller->cupo - $inscritos);
                $taller->estatus = 1;  // alta
                $taller->save();

                return redirect()->back()->with('success', 'El taller ha sido dado de alta correctamente.');
            }

            // Si el estatus no es "baja" ni "alta", redirige con un mensaje de error
            return redirect()->back()->with('status', 'Acción no válida.');
        } catch (\Exception $e) {
            // Maneja errores y registra el problema
            Log::error('Error al cambiar el estatus del taller: ' . $e->getMessage());
            return redirect()->back()->with('status', 'Error al cambiar el estatus del taller.');
        }
    }
}
