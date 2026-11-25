<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Concepto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class ConceptoController extends Controller
{
    public function index()
    {
        return view('conceptos');
    }

    public function saveConcepto(Request $request)
    {
        // dd($request);
        // Validar los datos recibidos del formulario
        $request->validate([
            'concepto' => 'required|string|max:50',
            'costo' => 'nullable|numeric|min:0'
        ]);

        // Crear un nuevo fundamento legal y asignar los datos del formulario
        $concepto = new Concepto();
        $concepto->concepto = $request->concepto;
        $concepto->costo = $request->costo ?? 0; // Asignar 0 si no se proporciona un costo
        $concepto->save();

        // Redireccionar con un mensaje de éxito 
        return redirect()->route('concepto')->with('success', 'Creado exitosamente.');
    }

    public function actConcepto(Request $request)
    {
        // dd($request);
        // Validar los datos recibidos del formulario
        $request->validate([
            'concepto_2' => 'required|string|max:50', // Validar el campo concepto_2
            'idconcepto_2' => 'required|integer|exists:conceptos,idconcepto', // Validar que el ID exista
            'costo_2' => 'nullable|numeric|min:0' // Validar el campo costo_2
        ]);

        try {
            // Buscar el registro por su ID y actualizar el campo concepto
            $concepto = Concepto::find($request->idconcepto_2);
            $concepto->concepto = $request->concepto_2;
            $concepto->costo = $request->costo_2;
            $concepto->save();

            // Redireccionar con un mensaje de éxito
            return redirect()->back()->with('status', 'Concepto actualizado con éxito');
        } catch (Throwable $th) {
            // Registrar el error
            Log::error('Error al actualizar el concepto: ' . $th->getMessage());
            return redirect()->back()->with('status', 'Error al actualizar el concepto');
        }
    }

    public function estConcepto($idconcepto, $estatus)
    {
        try {
            // Busca el concepto por su ID
            $concepto = Concepto::findOrFail($idconcepto);
    
            // Verifica la acción y cambia el estatus
            if ($estatus === 'baja') {
                $concepto->estatus = 0; // Cambia el estatus a 0 (inactivo)
                $concepto->save();
                return redirect()->back()->with('status', 'El concepto ha sido desactivado correctamente.');
            } elseif ($estatus === 'alta') {
                $concepto->estatus = 1; // Cambia el estatus a 1 (activo)
                $concepto->save();
                return redirect()->back()->with('status', 'El concepto ha sido activado correctamente.');
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
