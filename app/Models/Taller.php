<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class taller extends Model
{
    protected $table = 'talleres';

    protected $primaryKey = 'idtaller';

    protected $fillable = [
        'nametaller',
        'maestro',
        'idmaestro',
        'fechainicio', 
        'fechafin', 
        'cupo', 
        'disponibles',
        'inscripcion', 
        'mensualidad',
        'edad_min',
        'edad_max',
        'estatus'
    ];

    // Definir valores por defecto para los atributos 
    protected $attributes = [ 
        'estatus' => 1, 
    ];

    // Relación inversa: Un taller tiene muchas inscripciones
    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class, 'idtaller', 'idtaller');
    }

    // Un taller tiene muchos horarios (uno por día)
    public function horarios()
    {
        return $this->hasMany(TallerHorario::class, 'idtaller', 'idtaller');
    }

    // Maestro del catálogo (los talleres anteriores pueden no tenerlo)
    public function maestroCatalogo()
    {
        return $this->belongsTo(Maestro::class, 'idmaestro', 'idmaestro');
    }

    /**
     * Retorna el número de inscripciones activas (estatus = 1).
     */
    public function inscritosActivos(): int
    {
        return $this->inscripciones()
                    ->where('estatus', 1)
                    ->count();
    }

    /**
     * Indica si una edad entra en el rango del taller (límites inclusivos).
     * Sin límites, admite a todos. Con límites y edad desconocida (null), no admite.
     */
    public function admiteEdad(?int $edad): bool
    {
        if ($this->edad_min === null && $this->edad_max === null) {
            return true;
        }

        if ($edad === null) {
            return false;
        }

        return ($this->edad_min === null || $edad >= $this->edad_min)
            && ($this->edad_max === null || $edad <= $this->edad_max);
    }

    /**
     * Texto del rango de edad, ej. "6 a 12 años".
     */
    public function rangoEdadTexto(): string
    {
        if ($this->edad_min !== null && $this->edad_max !== null) {
            return $this->edad_min . ' a ' . $this->edad_max . ' años';
        }
        if ($this->edad_min !== null) {
            return 'desde ' . $this->edad_min . ' años';
        }
        if ($this->edad_max !== null) {
            return 'hasta ' . $this->edad_max . ' años';
        }
        return 'sin límite de edad';
    }
}
