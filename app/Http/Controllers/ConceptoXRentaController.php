<?php

namespace App\Http\Controllers;

use App\Models\ConceptoXRenta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ConceptoXRentaController extends Controller
{
    public function index()
    {
        return view('conceptosxrenta');
    }

    public function saveCxrenta(Request $request)
    {
        // dd($request);
        $request->validate([
            'cxrenta' => 'required|string|max:300',
            'costo' => 'required|numeric|min:1'
        ]);

        // Guardar el nuevo ciudadano en la base de datos
        $cxrenta = new ConceptoXRenta();
        $cxrenta->namecxrenta = mb_strtoupper($request->cxrenta, 'UTF-8');
        $cxrenta->costo = $request->costo;


        $cxrenta->save();

        // Redireccionar con un mensaje de éxito
        return redirect()->back()->with('success', 'Espacio registrado exitosamente.');
    }

    public function actCxrenta(Request $request)
    {
        // dd($request);
        $request->validate([
            'id_cxrenta' => ['required', 'exists:conceptosxrenta,idcxrenta'], // Verifica que el ID exista
            'cxrenta_2' => ['required', 'string', 'max:300'],
            'costo_2' => ['required', 'numeric', 'min:1']
        ]);

        try {
            // Buscar y actualizar al alumno
            $actCxrenta = ConceptoXRenta::find($request->id_cxrenta);
            $actCxrenta->namecxrenta = mb_strtoupper($request->cxrenta_2, 'UTF-8');
            $actCxrenta->costo = $request->costo_2;
            $actCxrenta->save();

            return redirect()->back()->with('success', 'Concepto actualizado con éxito');
        } catch (\Throwable $th) {
            // Registrar el error
            Log::error('Error al actualizar el concepto: ' . $th->getMessage());
            return redirect()->back()->with('status', 'Error'); 
        }
    }

    public function estCxrenta($idcxrenta, $estatus)
    {
        // dd('Trabajando en ello...');
        try {
            // Busca al alumno por su ID
            $estCxrenta = ConceptoXRenta::findOrFail($idcxrenta);

            // Verifica la acción y cambia el estatus
            if ($estatus === 'baja') {
                // Verifica si el alumno tiene alguna inscripción activa
                // if ($ciudadano->inscripciones()->where('estatus', '1')->exists()) {
                //     return redirect()->back()->with('error', 'No se puede dar de baja al alumno porque tiene una inscripción activa.');
                // }
                $estCxrenta->estatus = 0; // Cambia el estatus a 0 (baja)
                $estCxrenta->save();
                return redirect()->back()->with('status', 'El concepto ha sido dado de baja correctamente.');
            } elseif ($estatus === 'alta') {
                $estCxrenta->estatus = 1; // Cambia el estatus a 1 (alta)
                $estCxrenta->save();
                return redirect()->back()->with('success', 'El concepto ha sido dado de alta correctamente.');
            }

            // Si el estatus no es "baja" ni "alta", redirige con un mensaje de error
            return redirect()->back()->with('status', 'Acción no válida.');
        } catch (\Exception $e) {
            // Maneja errores y registra el problema
            Log::error('Error al cambiar el estatus del concepto: ' . $e->getMessage());
            return redirect()->back()->with('status', 'Error al cambiar el estatus del concepto.');
        }
    }
}
