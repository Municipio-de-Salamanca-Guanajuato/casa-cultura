<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aula extends Model
{
    protected $table = 'aulas';
    protected $primaryKey = 'idaula';

    protected $fillable = [
        'idedificio',
        'nombre',
        'estatus'
    ];

    // Definir valores por defecto para los atributos
    protected $attributes = [
        'estatus' => 1,
    ];

    // Relación inversa: Un aula pertenece a un edificio
    public function edificio()
    {
        return $this->belongsTo(Edificio::class, 'idedificio', 'idedificio');
    }

    /**
     * Talleres activos que no han terminado (fecha de cierre de hoy en adelante)
     * y que tienen al menos un horario activo en esta aula.
     */
    public function talleresVigentes()
    {
        return Taller::where('estatus', 1)
            ->whereDate('fechafin', '>=', now()->toDateString())
            ->whereHas('horarios', fn ($q) => $q->where('estatus', 1)->where('idaula', $this->idaula))
            ->orderBy('nametaller')
            ->get(['idtaller', 'nametaller']);
    }

    /**
     * Talleres que siguen activos pero cuya fecha de cierre ya pasó
     * y que tienen al menos un horario activo en esta aula.
     */
    public function talleresTerminadosActivos()
    {
        return Taller::where('estatus', 1)
            ->whereDate('fechafin', '<', now()->toDateString())
            ->whereHas('horarios', fn ($q) => $q->where('estatus', 1)->where('idaula', $this->idaula))
            ->orderBy('nametaller')
            ->get(['idtaller', 'nametaller', 'fechafin']);
    }
}
