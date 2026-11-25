<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Ciudadano;
use App\Models\Renta;
use App\Models\Espacio;
use App\Models\Opago;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class RentaController extends Controller
{
    public function index()
    {
        return view('rentas');
    }

    public function saveRenta(Request $request)
    {

        //$c1 = '';
        // $c1 = implode(',', $request->cpt); // "valor1,valor2,valor3"
        /*for ($i=0; $i < count($request->cpt); $i++) { 
            $c1 .= $request->cpt[$i] . ',';
        }*/
        // dd(rtrim($c1, ','));
        // Validar los datos del solicitante formulario
        
        $request->validate([
            'name' => 'required|string|max:300',
            'telefono' => 'required|string|digits:10',
            'fechaeve' => 'required|date',
            'horaini' => 'required|date_format:H:i',
            'horafin' => 'required|date_format:H:i|after:horaini',
            // 'id_cxrenta[]' => 'nullable',          // El array es requerido
            // 'id_cxrenta.*' => 'nullable|exists:conceptosxrenta,idcxrenta', // Guardar los ids separados por comas
            // 'cantidad[]' => 'nullable',            // Array de cantidades es requerido
            //'cantidad.*' => 'nullable|array',
            'id_cxrenta' => 'nullable|array',
            'id_cxrenta.*' => 'nullable|exists:conceptosxrenta,idcxrenta',
            'cantidad' => 'nullable|array',
            'cantidad.*' => 'nullable|numeric|min:1|max:99', // o 'integer' si deben ser enteros
            // 'id_ciudadano' => 'required|exists:ciudadanos,idciudadano',
            'id_espacio' => 'required|exists:espacios,idespacio',

            'total_general' => 'required|numeric',
            'rfc' => 'nullable|string|min:12|max:13|regex:/^[A-Z&Ñ]{3,4}[0-9]{6}[A-Z0-9]{3}$/'
        ]);
        // dd($request);

        DB::beginTransaction();

        try {
            // 1. Guardar en tabla ciudadanos
            // A lo mejor agregar una validacion (if) para no registrar la misma persona varias veces y tomar sus datos y rellenarlo automaticamente al tomar el RFC o un campo en común
            // $ciudadano = new Ciudadano();
            // $ciudadano->name = $request->name;
            // $ciudadano->telefono = $request->telefono;
            // $ciudadano->save();
            $ciudadano = Ciudadano::create([
                'name' => mb_strtoupper($request->name, 'UTF-8'),
                'telefono' => $request->telefono
            ]);

            // 2. Preparar datos para renta
            $cantidadxrenta = [];
            $cxrentaIds = [];

            // if ($request->conceptos) {
            //     $conceptos = json_decode($request->conceptos, true);
            //     $cxrentaIds = array_column($conceptos, 'id_cxrenta');
            //     // $cantxrenta = array_column($conceptos, 'id_cxrenta');
            //     // Sumar todas las horas extra
            //     $horaExtra = array_sum(array_column($conceptos, 'horas_extra'));
            // }
            if (!empty($request->id_cxrenta)) {
                foreach ($request->id_cxrenta as $index => $id) {
                    $cxrentaIds[] = $id;
                    // $cantidad = $request->cantidad[$index] ?? 0;
                    $cantidadxrenta[] = $request->cantidad[$index] ?? 1;
                }
            }

            // 3. Guardar en tabla rentas
            $renta = Renta::create([
                'fechaevento' => $request->fechaeve,
                'horainicio' => $request->horaini,
                'horafin' => $request->horafin,
                // Luego al guardar:
                'cantidadxrenta' => !empty($cantidadxrenta) ? implode(',', $cantidadxrenta) : null, // "1,2"
                'conceptosxrenta' => !empty($cxrentaIds) ? implode(',', $cxrentaIds) : null,        // "4,1"
                'idciudadano' => $ciudadano->idciudadano,
                'idespacio' => $request->id_espacio
                // 'estatus' => 1,
            ]);
            $renta->save();

            // 4. Generar folio consecutivo (ejemplo: 003517/2025)
            // Obtener año de la fecha de renta
            $anioRenta = date('Y', strtotime($request->fechaeve));
            // Buscar último folio del mismo año
            $ultimoFolio = Opago::where('foliocc', 'LIKE', '%/' . $anioRenta)
                ->orderBy('foliocc', 'desc')
                ->value('foliocc');
            // Generar nuevo folio
            $numero = $ultimoFolio ? intval(explode('/', $ultimoFolio)[0]) + 1 : 1;
            $nuevoFolio = str_pad($numero, 6, '0', STR_PAD_LEFT) . '/' . $anioRenta;

            // 5. Obtener último día del mes
            // $fechaLimite = date('Y-m-t', strtotime($request->fechaini));

            // 6. Guardar en tabla opagos
            Opago::create([
                'mespago' => date('m', strtotime($request->fechaeve)), // Mes en formato 2 dígitos
                'estatuspago' => 0,
                'fechalimite' => date('Y-m-t', strtotime($request->fechaeve)),
                'montototal' => $request->total_general,
                'foliocc' => $nuevoFolio,
                'foliocajas' => null, // Método que debes implementar
                'fechapago' => null, // Se actualizará cuando se pague
                'rfc' => $request->filled('rfc') ? $request->rfc : null,
                'idinscripcion' => null,
                'idrenta' => $renta->idrenta,
                'idconcepto' => 3, // ID fijo para rentas
                'idgeneral' => 2 // ID fijo para artículos
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Renta registrada correctamente'); // Opcional: para mostrar el folio generado
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al registrar la renta: ' . $e->getMessage());
        }

        // try {
        //     DB::beginTransaction();

        //     // Insertar la renta
        //     $renta = new Renta();
        //     $renta->idciudadano = $request->id_ciudadano;
        //     $renta->idespacio = $request->id_espacio;
        //     $renta->fechainicio = Carbon::parse($request->fechaini)->format('Y-m-d H:i:s');
        //     $renta->fechafin = Carbon::parse($request->fechafin)->format('Y-m-d H:i:s');
        //     $renta->save();

        //     // Obtener datos del espacio para el costo
        //     $espacio = Espacio::findOrFail($request->id_espacio);

        //     // Calcular el monto total basado en la duración
        //     $fechaInicio = Carbon::parse($request->fechaini);
        //     $fechaFin = Carbon::parse($request->fechafin);
        //     $horas = $fechaInicio->diffInHours($fechaFin); // Diferencia en horas

        //     // Lógica para calcular el monto total
        //     if ($horas <= 3) {
        //         // Si la renta es de 3 horas o menos, solo cobra el costo base
        //         $montoTotal = $espacio->costo;
        //     } else {
        //         // Si la renta es más de 3 horas:
        //         // - Costo base para las primeras 3 horas
        //         // - Suma el costo por cada hora extra
        //         $horasExtras = $horas - 3;
        //         $montoTotal = $espacio->costo + ($horasExtras * $espacio->horaextra);
        //     }

        //     // Generar el folio inicial basado en el año actual
        //     $anioActual = now()->year;  // Año actual
        //     // Generar el folio único
        //     $ultimoFolio = Opago::whereYear('created_at', $anioActual)->max('foliocc');
        //     // Calcular el consecutivo
        //     $folioConsecutivo = $ultimoFolio
        //         ? (int) substr($ultimoFolio, 0, 6) + 1  // Suma 1 al número actual
        //         : 1;  // Si no hay folio previo, empieza en 1
        //     // Formatear el folio con ceros a la izquierda y el año actual
        //     $foliocc = str_pad($folioConsecutivo, 6, '0', STR_PAD_LEFT) . '/' . $anioActual;
        //     // $ultimoFolio = Opago::max('foliocc');  // Obtener el último folio
        //     // $numero = $ultimoFolio ? (int) substr($ultimoFolio, 0, 6) + 1 : 1;
        //     // $foliocc = str_pad($numero, 6, '0', STR_PAD_LEFT) . '/' . now()->format('Y');

        //     // Calcular mespago y fechalimite
        //     $mespago = $fechaInicio->format('m');  // Mes de la fecha de inicio en formato MM
        //     $fechalimite = $fechaFin->endOfMonth()->format('Y-m-d');  // Último día del mes de finalización

        //     // Insertar la orden de pago
        //     $opago = new Opago();
        //     $opago->idrenta = $renta->idrenta; // ID de la renta
        //     $opago->montototal = $montoTotal;
        //     $opago->foliocc = $foliocc; // Folio consecutivo
        //     $opago->fechapago = null; // Se llenará cuando la persona pague
        //     $opago->mespago = $mespago; // Mes de pago en formato MM
        //     $opago->estatuspago = 0; // Estatus inicial = 0 (pendiente)
        //     $opago->fechalimite = $fechalimite; // Último día del mes de terminación
        //     $opago->idinscripcion = null; // Null porque no es una inscripción
        //     $opago->idconcepto = 3; // ID concepto = 3 (Renta)
        //     $opago->idgeneral = 2; // ID general = 2
        //     $opago->rfc = $request->rfc; // RFC si existe, null si no
        //     $opago->save();

        //     DB::commit();

        //     return redirect()->back()->with('success', 'Renta y orden de pago generadas correctamente.');

        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     Log::error('Error al guardar la renta: ' . $e->getMessage());
        //     return redirect()->back()->with('error', 'Ocurrió un error al guardar la renta.');
        // }
    }

    public function estRenta($idrenta, $estatus)
    {
        // dd('Trabajando en ello...');
        try {
            // Busca al alumno por su ID
            $renta = Renta::findOrFail($idrenta);

            // Verifica la acción y cambia el estatus
            if ($estatus === 'baja') {
                // Verifica si el alumno tiene alguna inscripción activa
                // if ($alumno->inscripciones()->where('estatus', '1')->exists()) {
                //     return redirect()->back()->with('error', 'No se puede dar de baja al alumno porque tiene una inscripción activa.');
                // }
                $renta->estatus = 0; // Cambia el estatus a 0 (baja)
                $renta->save();
                return redirect()->back()->with('status', 'La renta ha sido dada de baja correctamente.');
            } elseif ($estatus === 'alta') {
                $renta->estatus = 1; // Cambia el estatus a 1 (alta)
                $renta->save();
                return redirect()->back()->with('success', 'La renta ha sido dada de alta correctamente.');
            }

            // Si el estatus no es "baja" ni "alta", redirige con un mensaje de error
            return redirect()->back()->with('status', 'Acción no válida.');
        } catch (\Exception $e) {
            // Maneja errores y registra el problema
            Log::error('Error al cambiar el estatus de la renta: ' . $e->getMessage());
            return redirect()->back()->with('status', 'Error al cambiar el estatus de la renta.');
        }
    }
}
