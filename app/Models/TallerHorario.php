<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TallerHorario extends Model
{
    protected $table = 'taller_horarios';
    protected $primaryKey = 'idhorario';

    protected $fillable = [
        'idtaller',
        'idaula',
        'dia',
        'hora_inicio',
        'hora_fin',
        'estatus'
    ];

    // Definir valores por defecto para los atributos
    protected $attributes = [
        'estatus' => 1,
    ];

    // Nombres de los días (1 = Lunes ... 6 = Sábado)
    public const DIAS = [
        1 => 'Lunes',
        2 => 'Martes',
        3 => 'Miércoles',
        4 => 'Jueves',
        5 => 'Viernes',
        6 => 'Sábado',
    ];

    // Relación inversa: Un horario pertenece a un taller
    public function taller()
    {
        return $this->belongsTo(Taller::class, 'idtaller', 'idtaller');
    }

    // Relación inversa: Un horario pertenece a un aula
    public function aula()
    {
        return $this->belongsTo(Aula::class, 'idaula', 'idaula');
    }

    /**
     * Busca horarios de OTROS talleres activos que choquen con las filas dadas.
     * Hay choque si: mismo día, las horas se enciman, los periodos (fechas) de los talleres se enciman,
     * y es la misma aula o el mismo maestro. Solo cuentan talleres y horarios con estatus = 1.
     *
     * @param array $filas  [['dia' => 1, 'idaula' => 3, 'hora_inicio' => '16:00:00', 'hora_fin' => '18:00:00'], ...]
     * @return array Lista de choques: tipo (aula|maestro), taller, maestro, aula, edificio, dia, hora_inicio, hora_fin
     */
    public static function buscarChoques(array $filas, string $fechaIni, string $fechaFin, ?int $idmaestro, ?int $excluirTaller = null): array
    {
        $choques = [];

        foreach ($filas as $fila) {
            $encontrados = DB::table('taller_horarios as h')
                ->join('talleres as t', 't.idtaller', '=', 'h.idtaller')
                ->join('aulas as a', 'a.idaula', '=', 'h.idaula')
                ->join('edificios as e', 'e.idedificio', '=', 'a.idedificio')
                ->where('h.estatus', 1)
                ->where('t.estatus', 1)
                ->when($excluirTaller, fn ($q) => $q->where('t.idtaller', '!=', $excluirTaller))
                ->where('h.dia', $fila['dia'])
                // Las horas se enciman (16-18 sí choca con 17-19; 16-18 no choca con 18-20)
                ->where('h.hora_inicio', '<', $fila['hora_fin'])
                ->where('h.hora_fin', '>', $fila['hora_inicio'])
                // Los periodos de los talleres se enciman
                ->where('t.fechainicio', '<=', $fechaFin)
                ->where('t.fechafin', '>=', $fechaIni)
                // Misma aula o mismo maestro
                ->where(function ($q) use ($fila, $idmaestro) {
                    $q->where('h.idaula', $fila['idaula']);
                    if ($idmaestro) {
                        $q->orWhere('t.idmaestro', $idmaestro);
                    }
                })
                ->select('t.idtaller', 't.nametaller', 't.maestro', 't.idmaestro', 'h.idaula', 'h.dia', 'h.hora_inicio', 'h.hora_fin', 'a.nombre as aula', 'e.nombre as edificio')
                ->get();

            foreach ($encontrados as $h) {
                $base = [
                    'taller' => $h->nametaller,
                    'maestro' => $h->maestro,
                    'aula' => $h->aula,
                    'edificio' => $h->edificio,
                    'dia' => (int) $h->dia,
                    'hora_inicio' => substr($h->hora_inicio, 0, 5),
                    'hora_fin' => substr($h->hora_fin, 0, 5),
                ];
                if ((int) $h->idaula === (int) $fila['idaula']) {
                    $choques[] = ['tipo' => 'aula'] + $base;
                }
                if ($idmaestro && (int) $h->idmaestro === (int) $idmaestro) {
                    $choques[] = ['tipo' => 'maestro'] + $base;
                }
            }
        }

        // Sin repetidos (un mismo choque puede salir de varias filas)
        return collect($choques)->unique(fn ($c) => implode('|', $c))->values()->all();
    }

    /**
     * CURP base de una persona: lo que va antes del guion, en mayúsculas.
     * "ABCD010101HGTXXX01-02" y "ABCD010101HGTXXX01" son la misma persona.
     */
    public static function curpBase(?string $curp): string
    {
        return mb_strtoupper(trim(explode('-', (string) $curp)[0]), 'UTF-8');
    }

    /**
     * Busca inscripciones activas de la MISMA PERSONA (misma CURP base, cualquier registro: -1, -02, etc.)
     * en talleres activos cuyo horario choca con los horarios activos del taller $idtaller.
     * Hay choque si: mismo día, las horas se enciman y los periodos de los talleres se enciman.
     * Si el taller no tiene horarios activos no hay nada que revisar.
     *
     * @return array Lista de choques: taller, dia, hora_inicio, hora_fin, curp (del registro que ya está inscrito)
     */
    public static function buscarChoquesAlumno(?string $curp, int $idtaller, ?int $excluirInscripcion = null): array
    {
        $base = self::curpBase($curp);
        if ($base === '') {
            return [];
        }

        $encontrados = DB::table('taller_horarios as hn') // horarios del taller al que se quiere inscribir
            ->join('talleres as tn', 'tn.idtaller', '=', 'hn.idtaller')
            ->join('taller_horarios as h', function ($j) {
                $j->on('h.dia', '=', 'hn.dia')
                    ->on('h.hora_inicio', '<', 'hn.hora_fin')
                    ->on('h.hora_fin', '>', 'hn.hora_inicio');
            })
            ->join('talleres as t', function ($j) {
                $j->on('t.idtaller', '=', 'h.idtaller')
                    ->on('t.fechainicio', '<=', 'tn.fechafin')
                    ->on('t.fechafin', '>=', 'tn.fechainicio');
            })
            ->join('inscripciones as i', 'i.idtaller', '=', 't.idtaller')
            ->join('alumnos as a', 'a.idalumno', '=', 'i.idalumno')
            ->where('hn.idtaller', $idtaller)
            ->where('hn.estatus', 1)
            ->where('h.estatus', 1)
            ->where('t.estatus', 1)
            ->where('i.estatus', 1)
            ->when($excluirInscripcion, fn ($q) => $q->where('i.idinscripcion', '!=', $excluirInscripcion))
            ->whereRaw("UPPER(TRIM(SUBSTRING_INDEX(a.curp, '-', 1))) = ?", [$base])
            ->select('t.idtaller', 't.nametaller', 'h.dia', 'h.hora_inicio', 'h.hora_fin', 'a.curp')
            ->orderBy('t.nametaller')->orderBy('h.dia')->orderBy('h.hora_inicio')
            ->get();

        return $encontrados->map(fn ($h) => [
            'idtaller' => (int) $h->idtaller,
            'taller' => $h->nametaller,
            'dia' => (int) $h->dia,
            'hora_inicio' => substr($h->hora_inicio, 0, 5),
            'hora_fin' => substr($h->hora_fin, 0, 5),
            'curp' => $h->curp,
        ])->unique(fn ($c) => implode('|', $c))->values()->all();
    }

    /**
     * Para cuando se AGREGAN horarios a un taller que ya tiene alumnos: busca si a alguno de sus inscritos
     * (o a otro registro de la misma persona: -1, -02...) le choca el horario nuevo con otro taller activo
     * en el que tenga inscripción activa. Mismo día, horas encimadas y periodos encimados.
     *
     * @param array $filas Horarios nuevos [['dia' => 1, 'hora_inicio' => '16:00:00', 'hora_fin' => '18:00:00'], ...]
     * @return array Lista de choques: alumno, curp, taller, dia, hora_inicio, hora_fin
     */
    public static function buscarChoquesInscritos(int $idtaller, array $filas, string $fechaIni, string $fechaFin): array
    {
        $choques = [];

        foreach ($filas as $fila) {
            $encontrados = DB::table('inscripciones as i') // inscritos del taller que se edita
                ->join('alumnos as a', 'a.idalumno', '=', 'i.idalumno')
                ->join('alumnos as a2', DB::raw("UPPER(TRIM(SUBSTRING_INDEX(a2.curp, '-', 1)))"), '=', DB::raw("UPPER(TRIM(SUBSTRING_INDEX(a.curp, '-', 1)))"))
                ->join('inscripciones as i2', 'i2.idalumno', '=', 'a2.idalumno') // otras inscripciones de esa persona
                ->join('talleres as t', 't.idtaller', '=', 'i2.idtaller')
                ->join('taller_horarios as h', 'h.idtaller', '=', 't.idtaller')
                ->where('i.idtaller', $idtaller)
                ->where('i.estatus', 1)
                ->where('i2.estatus', 1)
                ->where('i2.idtaller', '!=', $idtaller)
                ->where('t.estatus', 1)
                ->where('h.estatus', 1)
                ->where('h.dia', $fila['dia'])
                ->where('h.hora_inicio', '<', $fila['hora_fin'])
                ->where('h.hora_fin', '>', $fila['hora_inicio'])
                ->where('t.fechainicio', '<=', $fechaFin)
                ->where('t.fechafin', '>=', $fechaIni)
                ->select('a.name', 'a.apellidop', 'a.apellidom', 'a2.curp', 't.nametaller', 'h.dia', 'h.hora_inicio', 'h.hora_fin')
                ->get();

            foreach ($encontrados as $h) {
                $choques[] = [
                    'alumno' => trim($h->name . ' ' . $h->apellidop . ' ' . $h->apellidom),
                    'curp' => $h->curp,
                    'taller' => $h->nametaller,
                    'dia' => (int) $h->dia,
                    'hora_inicio' => substr($h->hora_inicio, 0, 5),
                    'hora_fin' => substr($h->hora_fin, 0, 5),
                ];
            }
        }

        return collect($choques)->unique(fn ($c) => implode('|', $c))->values()->all();
    }

    /**
     * Texto de un choque de alumno inscrito para mostrarlo al usuario.
     */
    public static function mensajeChoqueInscrito(array $c): string
    {
        return 'El alumno ' . $c['alumno'] . ' (registro ' . $c['curp'] . ') ya está inscrito en el taller "' . $c['taller'] . '" el '
            . self::DIAS[$c['dia']] . ' de ' . $c['hora_inicio'] . ' a ' . $c['hora_fin'] . '.';
    }

    /**
     * Texto de un choque para mostrarlo al usuario.
     */
    public static function mensajeChoque(array $c): string
    {
        $cuando = self::DIAS[$c['dia']] . ' de ' . $c['hora_inicio'] . ' a ' . $c['hora_fin'];

        if ($c['tipo'] === 'aula') {
            return 'El aula ' . $c['aula'] . ' (' . $c['edificio'] . ') ya está ocupada el ' . $cuando
                . ' por el taller "' . $c['taller'] . '".';
        }

        return 'El maestro ' . $c['maestro'] . ' ya imparte el taller "' . $c['taller'] . '" el ' . $cuando
            . ' (' . $c['aula'] . ', ' . $c['edificio'] . ').';
    }
}
