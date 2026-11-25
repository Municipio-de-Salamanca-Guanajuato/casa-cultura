<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Maestro extends Model
{
    protected $table = 'maestros';
    protected $primaryKey = 'idmaestro';

    protected $fillable = [
        'nombre_comp',
        'telefono',
        'estatus'
    ];

    // Definir valores por defecto para los atributos
    protected $attributes = [
        'estatus' => 1,
    ];

    // Un maestro tiene muchos talleres
    public function talleres()
    {
        return $this->hasMany(Taller::class, 'idmaestro', 'idmaestro');
    }

    /**
     * Talleres activos del maestro que no han terminado (fecha de cierre de hoy en adelante).
     */
    public function talleresVigentes()
    {
        return $this->talleres()
            ->where('estatus', 1)
            ->whereDate('fechafin', '>=', now()->toDateString())
            ->orderBy('nametaller')
            ->get(['idtaller', 'nametaller']);
    }

    /**
     * Talleres del maestro que siguen activos pero cuya fecha de cierre ya pasó.
     */
    public function talleresTerminadosActivos()
    {
        return $this->talleres()
            ->where('estatus', 1)
            ->whereDate('fechafin', '<', now()->toDateString())
            ->orderBy('nametaller')
            ->get(['idtaller', 'nametaller', 'fechafin']);
    }
}
