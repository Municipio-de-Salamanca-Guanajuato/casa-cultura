<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ciudadano extends Model
{
    protected $table = 'ciudadanos';
    protected $primaryKey = 'idciudadano';

    protected $fillable = [
        'name',
        'telefono',
        'estatus'
    ];

    // Definir valores por defecto para los atributos
    protected $attributes = [
        'estatus' => 1,
    ];
}
