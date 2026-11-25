<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class alumno extends Model
{
    protected $table = 'alumnos';
    protected $primaryKey = 'idalumno';

    protected $fillable = [
        'name',
        'apellidop',
        'apellidom',
        'curp',
        'direccion',
        'telefono',
        'estatus',
        'nombretutor',
        'telefonotutor'
    ];

    // Definir valores por defecto para los atributos 
    protected $attributes = [
        'estatus' => 1,
    ];

    // Relación inversa: Un alumno tiene muchas inscripciones
    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class, 'idalumno', 'idalumno');
    }

    /**
     * Fecha de nacimiento tomada de la CURP (posiciones 5 a 10: AAMMDD).
     * La posición 17 indica el siglo: número = 1900, letra = 2000.
     * Retorna null si la CURP no trae una fecha válida.
     */
    public function fechaNacimiento(): ?Carbon
    {
        $curp = strtoupper((string) $this->curp);

        if (!preg_match('/^.{4}(\d{2})(\d{2})(\d{2})/', $curp, $m)) {
            return null;
        }

        [, $yy, $mm, $dd] = $m;
        $siglo = $curp[16] ?? '';

        if (ctype_digit($siglo)) {
            $anio = 1900 + (int) $yy;
        } elseif (ctype_alpha($siglo)) {
            $anio = 2000 + (int) $yy;
        } else {
            // Respaldo: misma regla que al registrar al alumno
            $anio = ((int) $yy > (int) date('y')) ? 1900 + (int) $yy : 2000 + (int) $yy;
        }

        if (!checkdate((int) $mm, (int) $dd, $anio)) {
            return null;
        }

        return Carbon::create($anio, (int) $mm, (int) $dd)->startOfDay();
    }

    /**
     * Edad cumplida a una fecha (por defecto hoy). Null si no se puede calcular.
     */
    public function edadAl($fecha = null): ?int
    {
        $nacimiento = $this->fechaNacimiento();

        if (!$nacimiento) {
            return null;
        }

        return (int) $nacimiento->diffInYears(Carbon::parse($fecha ?? now())->startOfDay());
    }

    /**
     * Edad a partir de una CURP suelta (sin tener el alumno guardado), con la misma regla que edadAl().
     * Se usa al registrar al alumno y en los Excel (pase de lista y reporte general).
     */
    public static function edadDeCurp(?string $curp, $fecha = null): ?int
    {
        $alumno = new static();
        $alumno->curp = $curp;

        return $alumno->edadAl($fecha);
    }
}
