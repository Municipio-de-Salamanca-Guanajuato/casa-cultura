<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Concepto extends Model
{
    protected $table = 'conceptos';
    protected $primaryKey = 'idconcepto';

    protected $fillable = [
        'concepto', 
        'costo',
        'estatus',
    ];

    // Definir valores por defecto para los atributos 
    protected $attributes = [ 
        'estatus' => 1, 
        'costo' => 0
    ];
}
