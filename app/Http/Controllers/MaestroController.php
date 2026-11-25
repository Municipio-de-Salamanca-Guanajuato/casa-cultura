<?php

namespace App\Http\Controllers;

use App\Models\Maestro;
use App\Models\Taller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class MaestroController extends Controller
{
    public function index()
    {
        return view('maestros');
    }

    public function saveMaestro(Request $request)
    {
        // Nombre en mayúsculas y sin espacios dobles, para comparar duplicados
        $request->merge([
            'nombre_comp' => $this->normalizarNombre($request->nombre_comp),
            'telefono' => $request->filled('telefono') ? $request->telefono : null,
        ]);

        $request->validate([
            'nombre_comp' => 'required|string|max:150|unique:maestros,nombre_comp',
            'telefono' => 'nullable|digits:10',
        ], [
            'nombre_comp.unique' => 'El maestro ya está registrado.',
            'telefono.digits' => 'El teléfono debe contener 10 dígitos.',
        ]);

        $maestro = new Maestro();
        $maestro->nombre_comp = $request->nombre_comp;
        $maestro->telefono = $request->telefono;
        $maestro->save();

        return redirect()->back()->with('success', 'Maestro registrado exitosamente.');
    }

    public function actMaestro(Request $request)
    {
        $request->merge([
            'nombre_comp_2' => $this->normalizarNombre($request->nombre_comp_2),
            'telefono_2' => $request->filled('telefono_2') ? $request->telefono_2 : null,
        ]);

        $request->validate([
            'id_maestro' => ['required', 'exists:maestros,idmaestro'],
            'nombre_comp_2' => ['required', 'string', 'max:150', Rule::unique('maestros', 'nombre_comp')->ignore($request->id_maestro, 'idmaestro')],
            'telefono_2' => ['nullable', 'digits:10'],
        ], [
            'nombre_comp_2.unique' => 'Ya existe otro maestro con ese nombre.',
            'telefono_2.digits' => 'El teléfono debe contener 10 dígitos.',
        ]);

        try {
            $actMaestro = Maestro::find($request->id_maestro);
            $actMaestro->nombre_comp = $request->nombre_comp_2;
            $actMaestro->telefono = $request->telefono_2;
            $actMaestro->save();

            // Mantener el nombre al día en sus talleres (lo leen el pase de lista, reportes y tarjetón)
            Taller::where('idmaestro', $actMaestro->idmaestro)
                ->update(['maestro' => TallerController::textoMaestro($actMaestro)]);

            return redirect()->back()->with('success', 'Maestro actualizado con éxito');
        } catch (\Throwable $th) {
            Log::error('Error al actualizar el maestro: ' . $th->getMessage());
            return redirect()->back()->with('status', 'Error al actualizar el maestro.');
        }
    }

    public function estMaestro($idmaestro, $estatus)
    {
        try {
            $maestro = Maestro::findOrFail($idmaestro);

            if ($estatus === 'baja') {
                // No se puede desactivar si tiene talleres activos que no han terminado
                $talleres = $maestro->talleresVigentes();
                if ($talleres->isNotEmpty()) {
                    return redirect()->back()->with('error', 'No se puede desactivar al maestro ' . $maestro->nombre_comp . ', tiene '
                        . $talleres->count() . ' taller(es) activo(s): ' . $talleres->pluck('nametaller')->implode(', ')
                        . '. Cambia el maestro de esos talleres o desactívalos primero.');
                }
                $maestro->estatus = 0;
                $maestro->save();

                $mensaje = 'El maestro ' . $maestro->nombre_comp . ' ha sido dado de baja correctamente.';

                // Aviso: talleres que siguen activos con este maestro aunque ya terminaron
                $terminados = $maestro->talleresTerminadosActivos();
                if ($terminados->isNotEmpty()) {
                    $lista = $terminados->map(fn ($t) => $t->nametaller . ' (' . Carbon::parse($t->fechafin)->format('d/m/Y') . ')')->implode(', ');
                    $mensaje .= $terminados->count() === 1
                        ? ' Nota: el taller ' . $lista . ' sigue activo con este maestro, aunque ya terminó. Te recomendamos desactivarlo.'
                        : ' Nota: los talleres ' . $lista . ' siguen activos con este maestro, aunque ya terminaron. Te recomendamos desactivarlos.';
                }

                return redirect()->back()->with('status', $mensaje);
            } elseif ($estatus === 'alta') {
                $maestro->estatus = 1;
                $maestro->save();
                return redirect()->back()->with('success', 'El maestro ' . $maestro->nombre_comp . ' ha sido dado de alta correctamente.');
            }

            return redirect()->back()->with('status', 'Acción no válida.');
        } catch (\Exception $e) {
            Log::error('Error al cambiar el estatus del maestro: ' . $e->getMessage());
            return redirect()->back()->with('status', 'Error al cambiar el estatus del maestro.');
        }
    }

    private function normalizarNombre($nombre)
    {
        return mb_strtoupper(preg_replace('/\s+/u', ' ', trim((string) $nombre)), 'UTF-8');
    }
}
