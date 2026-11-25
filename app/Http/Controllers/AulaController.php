<?php

namespace App\Http\Controllers;

use App\Models\Aula;
use App\Models\Edificio;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class AulaController extends Controller
{
    public function index()
    {
        // Solo edificios activos para el select de los modales
        $edificios = Edificio::where('estatus', 1)->orderBy('nombre')->get(['idedificio', 'nombre']);

        return view('aulas', compact('edificios'));
    }

    public function saveAula(Request $request)
    {
        // Se guarda en mayúsculas, así que se compara ya convertido
        $request->merge(['nombre' => mb_strtoupper(trim($request->nombre), 'UTF-8')]);

        $request->validate([
            'idedificio' => ['required', Rule::exists('edificios', 'idedificio')->where('estatus', 1)],
            'nombre' => ['required', 'string', 'max:100', Rule::unique('aulas', 'nombre')->where('idedificio', $request->idedificio)],
        ], [
            'idedificio.exists' => 'El edificio seleccionado no existe o está inactivo.',
            'nombre.unique' => 'Esa aula ya está registrada en el edificio seleccionado.',
        ]);

        $aula = new Aula();
        $aula->idedificio = $request->idedificio;
        $aula->nombre = $request->nombre;
        $aula->save();

        return redirect()->back()->with('success', 'Aula registrada exitosamente.');
    }

    public function actAula(Request $request)
    {
        $request->merge(['nombre_2' => mb_strtoupper(trim($request->nombre_2), 'UTF-8')]);

        $request->validate([
            'id_aula' => ['required', 'exists:aulas,idaula'],
            'idedificio_2' => ['required', Rule::exists('edificios', 'idedificio')->where('estatus', 1)],
            'nombre_2' => [
                'required', 'string', 'max:100',
                Rule::unique('aulas', 'nombre')->where('idedificio', $request->idedificio_2)->ignore($request->id_aula, 'idaula'),
            ],
        ], [
            'idedificio_2.exists' => 'El edificio seleccionado no existe o está inactivo.',
            'nombre_2.unique' => 'Ya existe otra aula con ese nombre en el edificio seleccionado.',
        ]);

        try {
            $actAula = Aula::find($request->id_aula);
            $actAula->idedificio = $request->idedificio_2;
            $actAula->nombre = $request->nombre_2;
            $actAula->save();

            return redirect()->back()->with('success', 'Aula actualizada con éxito');
        } catch (\Throwable $th) {
            Log::error('Error al actualizar el aula: ' . $th->getMessage());
            return redirect()->back()->with('status', 'Error al actualizar el aula.');
        }
    }

    public function estAula($idaula, $estatus)
    {
        try {
            $aula = Aula::with('edificio')->findOrFail($idaula);

            if ($estatus === 'baja') {
                // No se puede desactivar si la usan talleres activos que no han terminado
                $talleres = $aula->talleresVigentes();
                if ($talleres->isNotEmpty()) {
                    return redirect()->back()->with('error', 'No se puede desactivar el aula ' . $aula->nombre . ' (' . $aula->edificio->nombre . '), la usa(n) '
                        . $talleres->count() . ' taller(es) activo(s): ' . $talleres->pluck('nametaller')->implode(', ')
                        . '. Quita sus horarios en esta aula o desactiva esos talleres primero.');
                }
                $aula->estatus = 0;
                $aula->save();

                $mensaje = 'El aula ' . $this->nombreAula($aula) . ' ha sido dada de baja correctamente.';

                // Aviso: talleres que siguen activos aunque ya terminaron y tienen horario en esta aula
                $terminados = $aula->talleresTerminadosActivos();
                if ($terminados->isNotEmpty()) {
                    $lista = $terminados->map(fn ($t) => $t->nametaller . ' (' . Carbon::parse($t->fechafin)->format('d/m/Y') . ')')->implode(', ');
                    $mensaje .= $terminados->count() === 1
                        ? ' Nota: el taller ' . $lista . ' tiene horario en esta aula y sigue activo, aunque ya terminó. Te recomendamos desactivarlo.'
                        : ' Nota: los talleres ' . $lista . ' tienen horario en esta aula y siguen activos, aunque ya terminaron. Te recomendamos desactivarlos.';
                }

                return redirect()->back()->with('status', $mensaje);
            } elseif ($estatus === 'alta') {
                // No se puede activar un aula de un edificio inactivo
                if ($aula->edificio->estatus != 1) {
                    return redirect()->back()->with('error', 'No se puede activar el aula ' . $aula->nombre . ', el edificio ' . $aula->edificio->nombre . ' está inactivo. Actívalo primero.');
                }
                $aula->estatus = 1;
                $aula->save();
                return redirect()->back()->with('success', 'El aula ' . $this->nombreAula($aula) . ' ha sido dada de alta correctamente.');
            }

            return redirect()->back()->with('status', 'Acción no válida.');
        } catch (\Exception $e) {
            Log::error('Error al cambiar el estatus del aula: ' . $e->getMessage());
            return redirect()->back()->with('status', 'Error al cambiar el estatus del aula.');
        }
    }

    // "AULA 1 (LEONA VICARIO (LV))"
    private function nombreAula(Aula $aula): string
    {
        return $aula->nombre . ' (' . $aula->edificio->nombre . ')';
    }
}
