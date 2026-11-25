<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Edificio extends Model
{
    protected $table = 'edificios';
    protected $primaryKey = 'idedificio';

    protected $fillable = [
        'nombre',
        'estatus'
    ];

    // Definir valores por defecto para los atributos
    protected $attributes = [
        'estatus' => 1,
    ];

    // Un edificio tiene muchas aulas
    public function aulas()
    {
        return $this->hasMany(Aula::class, 'idedificio', 'idedificio');
    }

    /**
     * Retorna el número de aulas activas (estatus = 1).
     */
    public function aulasActivas(): int
    {
        return $this->aulas()
                    ->where('estatus', 1)
                    ->count();
    }
}
