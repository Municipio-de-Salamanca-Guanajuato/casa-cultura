<?php

namespace App\Http\Controllers;

use App\Models\Ciudadano;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CiudadanoController extends Controller
{
    public function index()
    {
        return view('ciudadanos');
    }

    public function saveCiudadano(Request $request)
    {
        // dd($request);
        // Validar los datos del formulario 
        $request->validate([
            'name' => 'required|string|max:70',
            'apellidop' => 'required|string|max:20',
            'apellidom' => 'required|string|max:20',
            'direccion' => 'required|string|max:200',
            'telefono' => 'required|string|digits:10',
        ]);

        // Crear un nuevo ciudadano y asignar los datos del formulario
        $ciudadano = new Ciudadano();
        $ciudadano->name = mb_strtoupper($request->name, 'UTF-8');
        $ciudadano->apellidop = mb_strtoupper($request->apellidop, 'UTF-8');
        $ciudadano->apellidom = mb_strtoupper($request->apellidom, 'UTF-8');
        $ciudadano->direccion = mb_strtoupper($request->direccion, 'UTF-8');
        $ciudadano->telefono = $request->telefono;

        // Guardar el nuevo ciudadano en la base de datos
        $ciudadano->save();

        // Redireccionar con un mensaje de éxito
        return redirect()->route('ciudadano')->with('success', 'Ciudadano registrado exitosamente.');
    }

    public function actCiudadano(Request $request)
    {
        $request->validate([
            'id_ciudadano' => ['required', 'exists:ciudadanos,idciudadano'], // Verifica que el ID exista
            'name_2' => ['required', 'string', 'max:70'],
            'apellidop_2' => ['required', 'string', 'max:20'],
            'apellidom_2' => ['required', 'string', 'max:20'],
            'direccion_2' => ['required', 'string', 'max:100'],
            'telefono_2' => ['nullable', 'digits:10']
        ]);

        try {
            // Buscar y actualizar al alumno
            $actCiudadano = Ciudadano::find($request->id_ciudadano);
            $actCiudadano->name = mb_strtoupper($request->name_2, 'UTF-8');
            $actCiudadano->apellidop = mb_strtoupper($request->apellidop_2, 'UTF-8');
            $actCiudadano->apellidom = mb_strtoupper($request->apellidom_2, 'UTF-8');
            $actCiudadano->direccion = mb_strtoupper($request->direccion_2, 'UTF-8');
            $actCiudadano->telefono = $request->telefono_2;
            $actCiudadano->save();

            return redirect()->back()->with('success', 'Ciudadano actualizado con éxito');
        } catch (\Throwable $th) {
            // Registrar el error
            Log::error('Error al actualizar el ciudadano: ' . $th->getMessage());
            return redirect()->back()->with('status', 'Error');

            // Manejar errores y retornar una respuesta de error
            // return response()->json(['message' => 'Error al actualizar al alumno', 'error' => $e->getMessage()], 500);
        }
    }

    public function estCiudadano($idciudadano, $estatus)
    {
        // dd('Trabajando en ello...');
        try {
            // Busca al alumno por su ID
            $ciudadano = Ciudadano::findOrFail($idciudadano);

            // Verifica la acción y cambia el estatus
            if ($estatus === 'baja') {
                // Verifica si el alumno tiene alguna inscripción activa
                // if ($ciudadano->inscripciones()->where('estatus', '1')->exists()) {
                //     return redirect()->back()->with('error', 'No se puede dar de baja al alumno porque tiene una inscripción activa.');
                // }
                $ciudadano->estatus = 0; // Cambia el estatus a 0 (baja)
                $ciudadano->save();
                return redirect()->back()->with('status', 'El ciudadano ha sido dado de baja correctamente.');
            } elseif ($estatus === 'alta') {
                $ciudadano->estatus = 1; // Cambia el estatus a 1 (alta)
                $ciudadano->save();
                return redirect()->back()->with('success', 'El ciudadano ha sido dado de alta correctamente.');
            }

            // Si el estatus no es "baja" ni "alta", redirige con un mensaje de error
            return redirect()->back()->with('status', 'Acción no válida.');
        } catch (\Exception $e) {
            // Maneja errores y registra el problema
            Log::error('Error al cambiar el estatus del ciudadano: ' . $e->getMessage());
            return redirect()->back()->with('status', 'Error al cambiar el estatus del ciudadano.');
        }
    }
}
