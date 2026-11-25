<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConceptoXRenta extends Model
{
    protected $table = 'conceptosxrenta';
    protected $primaryKey = 'idcxrenta';

    protected $fillable = [
        'namecxrenta',
        'costo',
        'estatus'
    ];

    // Definir valores por defecto para los atributos
    protected $attributes = [
        'estatus' => 1,
    ];
}
