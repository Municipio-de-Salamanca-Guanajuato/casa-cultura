<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Opago;
use Illuminate\Http\Request;

class OpagoController extends Controller
{
    public function index()
    {
        return view('opagosalumnos');
    }

    public function index2()
    {
        return view('opagosciudadanos');
    }

    public function saveOpagoAlumno(Request $request)
    {
        // dd($request);
        // Validar los datos recibidos
        $request->validate([
            'idinscripcion' => 'required|exists:inscripciones,idinscripcion',
            'id_concepto' => 'required|exists:conceptos,idconcepto',
            'inscripcion' => 'required|numeric|min:0',
            'fechalimite' => 'nullable|date',
            'id_funlegal' => 'required|exists:generales,idgeneral',
        ]);

        try {
            // Crear nueva orden de pago
            $opago = new Opago();
            $opago->idinscripcion = $request->idinscripcion;
            $opago->mespago = null;
            // $opago->estatuspago = '0'; // 0 = No pagado
            $opago->fechalimite = $request->fechalimite;
            $opago->montototal = $request->inscripcion;
            $opago->folio = null; // Se llenará cuando se pague
            $opago->fechapago = null; // Se llenará cuando se pague
            $opago->idrenta = null; // No aplica para inscripción
            $opago->idgeneral = $request->id_funlegal;
            $opago->idconcepto = $request->id_concepto;

            // Guardar en la base de datos
            $opago->save();

            return redirect()->route('opagoalumno')->with('success', 'Orden de pago generada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar la orden de pago: ' . $e->getMessage());
        }
    }
}
