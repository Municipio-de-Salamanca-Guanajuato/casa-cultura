<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Opago extends Model
{
    protected $table = 'opagos';
    protected $primaryKey = 'idopago';

    protected $fillable = [
        'mespago',
        'estatuspago',
        'fechalimite',
        'montototal',
        'foliocc',
        'foliocajas',
        'fechapago',
        'rfc',
        'idinscripcion',
        'idrenta',
        'idconcepto',
        'idgeneral'
    ];

    // Definir valores por defecto para los atributos
    protected $attributes = [
        'estatuspago' => 0,
    ];
}
