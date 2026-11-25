<?php

namespace App\Http\Controllers;

use App\Models\Espacio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EspacioController extends Controller
{
    public function index()
    {
        return view('espacios');
    }

    public function saveEspacio(Request $request)
    {
        // dd($request);
        // Validar los datos del formulario 
        $request->validate([
            'nameroom' => 'required|string|max:200',
            // 'descripcion' => 'required|string|max:250',
            'cupo' => 'required|integer|min:1|max:65535',
            'costo' => 'required|numeric|min:1',
            // 'horaextra' => 'required|numeric|min:1'
        ]);

        // Crear un nuevo ciudadano y asignar los datos del formulario
        $espacio = new Espacio();
        $espacio->nameroom = mb_strtoupper($request->nameroom, 'UTF-8');
        // $espacio->descripcion = mb_strtoupper($request->descripcion, 'UTF-8');
        $espacio->cupo = $request->cupo;
        $espacio->costo = $request->costo;
        // $espacio->horaextra = $request->horaextra;

        // Guardar el nuevo ciudadano en la base de datos
        $espacio->save();

        // Redireccionar con un mensaje de éxito
        return redirect()->back()->with('success', 'Espacio registrado exitosamente.');
    }
    
    public function actEspacio(Request $request)
    {
        // dd($request);
        $request->validate([
            'id_espacio' => ['required', 'exists:espacios,idespacio'], // Verifica que el ID exista
            'nameroom_2' => ['required', 'string', 'max:200'],
            // 'descripcion_2' => ['required', 'string', 'max:250'],
            'cupo_2' => ['required', 'integer', 'min:1', 'max:65535'],
            'costo_2' => ['required', 'numeric', 'min:1'],
            // 'horaextra_2' => ['required', 'numeric', 'min:1'],
        ]);

        try {
            // Buscar y actualizar al alumno
            $actEspacio = Espacio::find($request->id_espacio);
            $actEspacio->nameroom = mb_strtoupper($request->nameroom_2, 'UTF-8');
            // $actEspacio->descripcion = mb_strtoupper($request->descripcion_2, 'UTF-8');
            $actEspacio->cupo = $request->cupo_2;
            $actEspacio->costo = $request->costo_2;
            // $actEspacio->horaextra = $request->horaextra_2;
            $actEspacio->save();

            return redirect()->back()->with('success', 'Espacio actualizado con éxito');
        } catch (\Throwable $th) {
            // Registrar el error
            Log::error('Error al actualizar el espacio: ' . $th->getMessage());
            return redirect()->back()->with('status', 'Error'); 
        }
    }

    public function estEspacio($idespacio, $estatus)
    {
        // dd('Trabajando en ello...');
        try {
            // Busca al alumno por su ID
            $espacio = Espacio::findOrFail($idespacio);

            // Verifica la acción y cambia el estatus
            if ($estatus === 'baja') {
                // Verifica si el alumno tiene alguna inscripción activa
                // if ($ciudadano->inscripciones()->where('estatus', '1')->exists()) {
                //     return redirect()->back()->with('error', 'No se puede dar de baja al alumno porque tiene una inscripción activa.');
                // }
                $espacio->estatus = 0; // Cambia el estatus a 0 (baja)
                $espacio->save();
                return redirect()->back()->with('status', 'El espacio ha sido dado de baja correctamente.');
            } elseif ($estatus === 'alta') {
                $espacio->estatus = 1; // Cambia el estatus a 1 (alta)
                $espacio->save();
                return redirect()->back()->with('success', 'El espacio ha sido dado de alta correctamente.');
            }

            // Si el estatus no es "baja" ni "alta", redirige con un mensaje de error
            return redirect()->back()->with('status', 'Acción no válida.');
        } catch (\Exception $e) {
            // Maneja errores y registra el problema
            Log::error('Error al cambiar el estatus del espacio: ' . $e->getMessage());
            return redirect()->back()->with('status', 'Error al cambiar el estatus del ciudadano.');
        }
    }
}
