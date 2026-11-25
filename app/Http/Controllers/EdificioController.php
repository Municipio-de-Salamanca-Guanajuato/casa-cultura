<?php

namespace App\Http\Controllers;

use App\Models\Edificio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class EdificioController extends Controller
{
    public function index()
    {
        return view('edificios');
    }

    public function saveEdificio(Request $request)
    {
        // Se guarda en mayúsculas, así que se compara ya convertido
        $request->merge(['nombre' => mb_strtoupper(trim($request->nombre), 'UTF-8')]);

        $request->validate([
            'nombre' => 'required|string|max:100|unique:edificios,nombre',
        ], [
            'nombre.unique' => 'El edificio ya está registrado.',
        ]);

        $edificio = new Edificio();
        $edificio->nombre = $request->nombre;
        $edificio->save();

        return redirect()->back()->with('success', 'Edificio registrado exitosamente.');
    }

    public function actEdificio(Request $request)
    {
        $request->merge(['nombre_2' => mb_strtoupper(trim($request->nombre_2), 'UTF-8')]);

        $request->validate([
            'id_edificio' => ['required', 'exists:edificios,idedificio'],
            'nombre_2' => ['required', 'string', 'max:100', Rule::unique('edificios', 'nombre')->ignore($request->id_edificio, 'idedificio')],
        ], [
            'nombre_2.unique' => 'Ya existe otro edificio con ese nombre.',
        ]);

        try {
            $actEdificio = Edificio::find($request->id_edificio);
            $actEdificio->nombre = $request->nombre_2;
            $actEdificio->save();

            return redirect()->back()->with('success', 'Edificio actualizado con éxito');
        } catch (\Throwable $th) {
            Log::error('Error al actualizar el edificio: ' . $th->getMessage());
            return redirect()->back()->with('status', 'Error al actualizar el edificio.');
        }
    }

    public function estEdificio($idedificio, $estatus)
    {
        try {
            $edificio = Edificio::findOrFail($idedificio);

            if ($estatus === 'baja') {
                // No se puede desactivar mientras tenga aulas activas
                $aulasActivas = $edificio->aulasActivas();
                if ($aulasActivas > 0) {
                    return redirect()->back()->with('error', 'No se puede desactivar el edificio ' . $edificio->nombre . ', tiene ' . $aulasActivas . ' aula(s) activa(s). Desactívalas primero.');
                }
                $edificio->estatus = 0;
                $edificio->save();
                return redirect()->back()->with('status', 'El edificio ha sido dado de baja correctamente.');
            } elseif ($estatus === 'alta') {
                $edificio->estatus = 1;
                $edificio->save();
                return redirect()->back()->with('success', 'El edificio ha sido dado de alta correctamente.');
            }

            return redirect()->back()->with('status', 'Acción no válida.');
        } catch (\Exception $e) {
            Log::error('Error al cambiar el estatus del edificio: ' . $e->getMessage());
            return redirect()->back()->with('status', 'Error al cambiar el estatus del edificio.');
        }
    }
}
