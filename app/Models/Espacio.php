<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Espacio extends Model
{
    protected $table = 'espacios';
    protected $primaryKey = 'idespacio';

    protected $fillable = [
        'nameroom',
        'cupo',
        'costo',
        'estatus'
    ];

    // Definir valores por defecto para los atributos 
    protected $attributes = [
        'estatus' => 1,
    ];
}
