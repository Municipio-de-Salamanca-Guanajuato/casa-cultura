<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Alumno;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class AlumnoController extends Controller
{
    public function index()
    {
        return view('alumnos');
    }

    public function saveAlumno(Request $request)
    {
        // dd($request);

        // Validar los datos del formulario 
        $request->validate([
            'name' => 'required|string|max:70',
            'apellidop' => 'required|string|max:20',
            'apellidom' => 'required|string|max:20',
            'curp' => 'required|string|max:21|min:18|unique:alumnos,curp',
            // 'curp' => 'required|string|size:18|regex:/^[A-Z]{1}[AEIOU]{1}[A-Z]{2}[0-9]{2}(0[1-9]|1[0-2])(0[1-9]|[12][0-9]|3[01])[HM][A-Z]{2}[B-DF-HJ-NP-TV-Z]{3}[0-9A-Z]{2}[0-9]$/i',
            'direccion' => 'required|string|max:200',
            'telefono' => 'string|digits:10|nullable',
            'nombretutor' => 'string|max:100|nullable',
            'telefonotutor' => 'string|max:15|nullable'
        ]);
        

        // $request->validate([
        //     'curp' => [
        //         'required',
        //         'string',
        //         'size:18',
        //         function ($attribute, $value, $fail) {
        //             $regex = '/^[A-Z]{1}[AEIOU]{1}[A-Z]{2}[0-9]{2}(0[1-9]|1[0-2])(0[1-9]|[12][0-9]|3[01])[HM][A-Z]{2}[B-DF-HJ-NP-TV-Z]{3}[0-9A-Z]{2}[0-9]$/i';
        //             if (!preg_match($regex, $value)) {
        //                 $fail('El formato del CURP no es válido.');
        //             }
        //         },
        //     ],
        // ]);

        // Edad con la fecha de nacimiento de la CURP (misma regla que en inscripciones)
        $age = Alumno::edadDeCurp($request->curp);
        if ($age === null) {
            return redirect()->back()->withInput()
                ->with('error', 'La CURP ' . $request->curp . ' no trae una fecha de nacimiento válida. Revísala.');
        }

        // Validar los campos de tutor si el alumno es menor de edad 
        if ($age < 18) {
            $request->validate([
                'nombretutor' => 'required|string|max:100',
                'telefonotutor' => 'required|string|digits:10'
            ]);
        } else {
            // Si el alumno es mayor o igual a 18, hacer que el campo teléfono sea obligatorio 
            $request->validate([
                'telefono' => 'required|string|digits:10'
            ]);
        }

        // Crear un nuevo alumno y asignar los datos del formulario
        $alumno = new Alumno();
        $alumno->name = mb_strtoupper($request->name, 'UTF-8');
        $alumno->apellidop = mb_strtoupper($request->apellidop, 'UTF-8');
        $alumno->apellidom = mb_strtoupper($request->apellidom, 'UTF-8');
        $alumno->curp = strtoupper($request->curp);
        $alumno->direccion = mb_strtoupper($request->direccion, 'UTF-8');
        $alumno->telefono = $request->telefono;
        // $alumno->estatus = $request->estatus;
        $alumno->nombretutor = mb_strtoupper($request->nombretutor, 'UTF-8');
        $alumno->telefonotutor = $request->telefonotutor;

        // Guardar el nuevo alumno en la base de datos 
        $alumno->save();

        // Redireccionar con un mensaje de éxito 
        return redirect()->route('alumno')->with('success', 'Alumno creado exitosamente.');
        // dd($alumno);
    }

    public function actAlumno(Request $request)
    {
        // Parte 3
        // dd($request);
        // try {
        //     $actAlumno = Alumno::find($request->idalumno_2);
        //     $actAlumno->name = $request->name_2;
        //     $actAlumno->apellidop = $request->apellidop_2;
        //     $actAlumno->apellidom = $request->apellidom_2;
        //     $actAlumno->curp = $request->curp_2;
        //     $actAlumno->direccion = $request->direccion_2;
        //     $actAlumno->telefono = $request->telefono_2;
        //     $actAlumno->estatus = $request->estatus_2;
        //     $actAlumno->nombretutor = $request->nombretutor_2;
        //     $actAlumno->telefonotutor = $request->telefonotutor_2;
        //     // if ($request->cdfactor_1) {
        //     //     $actFact->fk_cat_fclasificacion = $request->cdfactor_1;
        //     // }
        //     // if ($request->tdfactor_1) {
        //     //     $actFact->fk_cat_ftipo = $request->tdfactor_1;
        //     // }
        //     $actAlumno->save();
        //     return redirect()->back()->with('status', 'SuccessUpd');
        // } catch (\Throwable $th) {
        //     return redirect()->back()->with('status', 'Error');
        // }

        // Parte 2
        // dd($request);
        // Validar los datos recibidos del formulario
        // $request->validate([
        //     'idalumno_2' => 'required|exists:alumnos,idalumno', // Verifica que el ID exista
        //     'name_2' => 'required|string|max:255',
        //     'apellidop_2' => 'required|string|max:255',
        //     'apellidom_2' => 'required|string|max:255',
        //     'curp_2' => ['required|string|max:18', Rule::unique('alumnos', 'curp')->ignore($request->idalumno_2, 'idalumno')],
        //     'direccion_2' => 'required|string|max:255',
        //     'telefono_2' => 'nullable|digits:10',
        //     'nombretutor_2' => 'nullable|string|max:255',
        //     'telefonotutor_2' => 'nullable|digits:10',
        // ]);
        
        $request->validate([
            'idalumno_2' => ['required', 'exists:alumnos,idalumno'], // Verifica que el ID exista
            'name_2' => ['required', 'string', 'max:255'],
            'apellidop_2' => ['required', 'string', 'max:255'],
            'apellidom_2' => ['required', 'string', 'max:255'],
            'curp_2' => ['required', 'string', 'max:21', 'min:18', Rule::unique('alumnos', 'curp')->ignore($request->idalumno_2, 'idalumno')],
            'direccion_2' => ['required', 'string', 'max:255'],
            'telefono_2' => ['nullable', 'digits:10'], // No necesitas 'string' porque 'digits' ya valida números
            'nombretutor_2' => ['nullable', 'string', 'max:255'],
            'telefonotutor_2' => ['nullable', 'digits:10'],
        ]);
        // dd($request);

        try {
            // Buscar y actualizar al alumno
            $actAlumno = Alumno::find($request->idalumno_2);
            $actAlumno->name = mb_strtoupper($request->name_2, 'UTF-8');
            $actAlumno->apellidop = mb_strtoupper($request->apellidop_2, 'UTF-8');
            $actAlumno->apellidom = mb_strtoupper($request->apellidom_2, 'UTF-8');
            $actAlumno->curp = strtoupper($request->curp_2);
            $actAlumno->direccion = mb_strtoupper($request->direccion_2, 'UTF-8');
            $actAlumno->telefono = $request->telefono_2;
            // $actAlumno->estatus = $request->estatus_2;
            $actAlumno->nombretutor = mb_strtoupper($request->nombretutor_2, 'UTF-8');
            $actAlumno->telefonotutor = $request->telefonotutor_2;
            $actAlumno->save();

            return redirect()->back()->with('status', 'Alumno actualizado con éxito');

            // // Buscar al alumno por su ID
            // $alumno = Alumno::find($request->idalumno);

            // if (!$alumno) {
            //     return response()->json(['message' => 'Alumno no encontrado'], 404);
            // }

            // // Actualizar los datos del alumno con los valores enviados o mantener los mismos
            // // $alumno->update([
            // //     'name' => $request->input('name_2', $alumno->name),
            // //     'apellidop' => $request->input('apellidop_2', $alumno->apellidop),
            // //     'apellidom' => $request->input('apellidom_2', $alumno->apellidom),
            // //     'curp' => $request->input('curp_2', $alumno->curp),
            // //     'direccion' => $request->input('direccion_2', $alumno->direccion),
            // //     'telefono' => $request->input('telefono_2', $alumno->telefono),
            // //     'nombretutor' => $request->input('nombretutor_2', $alumno->nombretutor),
            // //     'telefonotutor' => $request->input('telefonotutor_2', $alumno->telefonotutor),
            // // ]);

            // // Retornar una respuesta de éxito
            // return redirect()->route('alumno')->with('success', 'Alumno actualizado exitosamente.');
        } catch (\Throwable $th) {
            // Registrar el error
            Log::error('Error al actualizar el alumno: ' . $th->getMessage());
            return redirect()->back()->with('status', 'Error');

            // Manejar errores y retornar una respuesta de error
            // return response()->json(['message' => 'Error al actualizar al alumno', 'error' => $e->getMessage()], 500);
        }
    }

    public function estAlumno($idalumno, $estatus)
    {
        // dd('Trabajando en ello...');
        try {
            // Busca al alumno por su ID
            $alumno = Alumno::findOrFail($idalumno);

            // Verifica la acción y cambia el estatus
            if ($estatus === 'baja') {
                // Verifica si el alumno tiene alguna inscripción activa
                if ($alumno->inscripciones()->where('estatus', '1')->exists()) {
                    return redirect()->back()->with('error', 'No se puede dar de baja al alumno porque tiene una inscripción activa.');
                }
                $alumno->estatus = 0; // Cambia el estatus a 0 (baja)
                $alumno->save();
                return redirect()->back()->with('status', 'El alumno ha sido dado de baja correctamente.');
            } elseif ($estatus === 'alta') {
                $alumno->estatus = 1; // Cambia el estatus a 1 (alta)
                $alumno->save();
                return redirect()->back()->with('status', 'El alumno ha sido dado de alta correctamente.');
            }

            // Si el estatus no es "baja" ni "alta", redirige con un mensaje de error
            return redirect()->back()->with('status', 'Acción no válida.');
        } catch (\Exception $e) {
            // Maneja errores y registra el problema
            Log::error('Error al cambiar el estatus del alumno: ' . $e->getMessage());
            return redirect()->back()->with('status', 'Error al cambiar el estatus del alumno.');
        }
    }
}
