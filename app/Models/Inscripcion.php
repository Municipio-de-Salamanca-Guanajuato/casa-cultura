<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class inscripcion extends Model
{
    protected $table = 'inscripciones';

    protected $primaryKey = 'idinscripcion';

    protected $fillable = [
        'fechainscripcion', 
        'estatus', 
        'idalumno', 
        'idtaller', 
    ];

    // Definir valores por defecto para los atributos 
    protected $attributes = [ 
        'estatus' => 1, 
    ];

    // Relación con la tabla Alumnos
    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'idalumno', 'idalumno');
    }

    // Relación con la tabla Talleres
    public function taller()
    {
        return $this->belongsTo(Taller::class, 'idtaller', 'idtaller');
    }
}
