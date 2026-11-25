<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tarjeton extends Model
{
    protected $table = 'tarjetones';

    protected $primaryKey = 'idtarjeton';

    protected $fillable = [
        'idinscripcion', 
        'idopago', 
        'estatus'
    ];

    // Definir valores por defecto para los atributos 
    protected $attributes = [ 
        'estatus' => 1, 
    ];

    // Relación inversa: Un tarjeton pertenece a una inscripcion
    public function inscripcion()
    {
        return $this->belongsTo(Inscripcion::class, 'idinscripcion', 'idinscripcion');
    }

    // Relación inversa: Un tarjeton pertenece a un opago
    public function opago()
    {
        return $this->belongsTo(Opago::class, 'idopago', 'idopago');
    }
}
