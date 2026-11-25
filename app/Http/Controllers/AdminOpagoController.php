<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\General;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class AdminOpagoController extends Controller
{
    public function index()
    {
        return view('adminopagos');
    }

    public function saveAdminOp(Request $request)
    {
        // dd($request);
        // Validar los datos del formulario 
        $request->validate([
            // 'namearea' => 'required|string|max:100',
            'funlegal' => 'required|string|max:250'
        ]);

        // Crear un nuevo fundamento legal y asignar los datos del formulario
        $adminop = new General();
        // $adminop->namearea = $request->namearea;
        $adminop->fundamentolegal = $request->funlegal;

        // Guardar el nuevo alumno en la base de datos 
        $adminop->save();

        // Redireccionar con un mensaje de éxito 
        return redirect()->route('adminopago')->with('success', 'Creado exitosamente.');
    }

    public function actAdminOp(Request $request)
    {
        // dd($request);
        // Validar los datos recibidos del formulario
        $request->validate([
            // 'namearea_2' => 'required|string|max:100',
            'funlegal_2' => 'required|string|max:250'
        ]);

        try {
            // Buscar y actualizar al alumno
            $actAdminOp = General::find($request->idgeneral_2);
            // $actAdminOp->namearea = $request->namearea_2;
            $actAdminOp->fundamentolegal = $request->funlegal_2;
            $actAdminOp->save();

            return redirect()->back()->with('status', 'Registro actualizado con éxito');
        } catch (Throwable $th) {
            // Registrar el error
            Log::error('Error al actualizar el registro: ' . $th->getMessage());
            return redirect()->back()->with('status', 'Error');
        }
    }
}
