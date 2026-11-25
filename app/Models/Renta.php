<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Renta extends Model
{
    protected $table = 'rentas';
    protected $primaryKey = 'idrenta';

    protected $fillable = [
        'fechaevento',
        'horainicio',
        'horafin',
        'cantidadxrenta',
        'conceptosxrenta',
        'estatus',
        'idciudadano',
        'idespacio'
    ];

    // Definir valores por defecto para los atributos
    protected $attributes = [
        'estatus' => 1,
    ];

    // Relación con la tabla Ciudadanos
    public function ciudadano()
    {
        return $this->belongsTo(Ciudadano::class, 'idciudadano', 'idciudadano');
    }

    // Relación con la tabla Espacios
    public function espacio()
    {
        return $this->belongsTo(Espacio::class, 'idespacio', 'idespacio');
    }
}
