<?php

namespace App\Http\Controllers;

use App\Models\Concepto;
use App\Models\Inscripcion;
use App\Models\Opago;
use App\Models\TallerHorario;
use App\Models\Tarjeton;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TarjetonController extends Controller
{
    public function index()
    {
        $conceptoRepoTarjeton = Concepto::find(4);

        return view('repotarjeton', compact('conceptoRepoTarjeton'));
        // $inscripcion = (object) [
        //     'fecha_inscripcion' => '2026-03-01',
        //     'alumno' => (object) [
        //         'nombre' => 'MA DEL SOCORRO MAGNOLIA LOZANO CAMARENA'
        //     ],
        //     'taller' => (object) [
        //         'nombre' => 'DIBUJO Y PINTURA INFANTIL (VILLAFAÑA SABADO) 2026',
        //         'horario' => '4:00 PM - 6:00 PM',
        //         'maestro' => 'MARÍA VERÓNICA DE LA CONCEPCIÓN MEJÍA MARTÍNEZ'
        //     ]
        // ];

        // $mesesPagados = [
        //     'marzo' => [
        //         'fecha' => '2026-03-05',
        //         'recibo' => 'AA123'
        //     ],
        //     'abril' => [
        //         'fecha' => '2026-04-06',
        //         'recibo' => 'AA124'
        //     ],
        //     'mayo' => [
        //         'fecha' => '2026-05-07',
        //         'recibo' => 'AA125'
        //     ],
        //     'junio' => [
        //         'fecha' => '2026-06-08',
        //         'recibo' => 'AA126'
        //     ],
        //     'julio' => [
        //         'fecha' => '2026-07-09',
        //         'recibo' => 'AA127'
        //     ],
        //     'agosto' => [
        //         'fecha' => '2026-08-10',
        //         'recibo' => 'AA128'
        //     ],
        //     'septiembre' => [
        //         'fecha' => '2026-09-11',
        //         'recibo' => 'AA129'
        //     ],
        //     'octubre' => [
        //         'fecha' => '2026-10-12',
        //         'recibo' => 'AA130'
        //     ],
        //     'noviembre' => [
        //         'fecha' => '2026-11-13',
        //         'recibo' => 'AA131'
        //     ],
        // ];

        // return view('pdf.tarjeton', compact('inscripcion', 'mesesPagados'));
        // return view('pdf.tarjeton');
    }

    public function downTarjeton($idinscripcion)
    {
        $inscripcion = Inscripcion::with(['alumno', 'taller'])->findOrFail($idinscripcion);

        $pagos = Opago::where('idinscripcion', $idinscripcion)
            ->whereIn('idconcepto', [1, 2])
            ->where('estatuspago', 1)
            ->get();

        $mesesPagados = $this->mapearMesesPagados($pagos);

        $horario = $this->textoHorario($inscripcion->idtaller);
        // La línea de HORARIO mide ~240px: si el texto no cabe a 11px, se reduce la letra (mínimo 6px)
        $tamHorario = $horario === '' ? 11 : max(6, min(11, round(240 / (mb_strlen($horario) * 0.53), 1)));

        $pdf = Pdf::loadView('pdf.tarjeton', compact('inscripcion', 'mesesPagados', 'horario', 'tamHorario'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('tarjeton.pdf');
    }

    /**
     * Horario del taller para el tarjetón, agrupando los días con la misma hora y aula:
     * "LUN Y MIÉ 16:00 A 18:00 · AULA 2 (LV) / SÁB 10:00 A 12:00 · AULA 1 (LV)".
     * Talleres sin horario registrado: cadena vacía.
     */
    private function textoHorario(int $idtaller): string
    {
        $dias = [1 => 'LUN', 2 => 'MAR', 3 => 'MIÉ', 4 => 'JUE', 5 => 'VIE', 6 => 'SÁB'];

        $horarios = TallerHorario::with('aula.edificio')
            ->where('idtaller', $idtaller)
            ->where('estatus', 1)
            ->orderBy('dia')
            ->orderBy('hora_inicio')
            ->get();

        $grupos = [];
        foreach ($horarios as $h) {
            $clave = $h->hora_inicio . '|' . $h->hora_fin . '|' . $h->idaula;
            $grupos[$clave]['dias'][] = $dias[$h->dia] ?? '';
            $grupos[$clave]['horario'] = $h;
        }

        $partes = [];
        foreach ($grupos as $g) {
            $h = $g['horario'];
            $listaDias = count($g['dias']) > 1
                ? implode(', ', array_slice($g['dias'], 0, -1)) . ' Y ' . end($g['dias'])
                : $g['dias'][0];

            $texto = $listaDias . ' ' . substr($h->hora_inicio, 0, 5) . ' A ' . substr($h->hora_fin, 0, 5);

            if ($h->aula) {
                // Edificio: la abreviatura entre paréntesis si la tiene ("LEONA VICARIO (LV)" -> LV)
                $edificio = $h->aula->edificio->nombre ?? '';
                if (preg_match('/\(([^)]+)\)\s*$/', $edificio, $m)) {
                    $edificio = $m[1];
                }
                $texto .= ' · ' . $h->aula->nombre . ($edificio !== '' ? ' (' . $edificio . ')' : '');
            }

            $partes[] = mb_strtoupper($texto, 'UTF-8');
        }

        return implode(' / ', $partes);
    }

    private function mapearMesesPagados($pagos)
    {
        $meses = [];

        $mapaMeses = [
            '01' => 'enero',
            '02' => 'febrero',
            '03' => 'marzo',
            '04' => 'abril',
            '05' => 'mayo',
            '06' => 'junio',
            '07' => 'julio',
            '08' => 'agosto',
            '09' => 'septiembre',
            '10' => 'octubre',
            '11' => 'noviembre',
            '12' => 'diciembre',
        ];

        foreach ($pagos as $pago) {
            $mesNumero = str_pad($pago->mespago, 2, '0', STR_PAD_LEFT);

            if (isset($mapaMeses[$mesNumero])) {
                $mesNombre = $mapaMeses[$mesNumero];

                $meses[$mesNombre] = [
                    'fecha' => $pago->fechapago,
                    'recibo' => $pago->foliocajas,
                ];
            }
        }

        return $meses;
    }

    public function saveTarjeton(Request $request)
    {
        try {
            $request->validate([
                'id_inscripcion' => 'required|exists:inscripciones,idinscripcion',
            ]);

            DB::transaction(function () use ($request) {
                $existePendiente = Opago::where('idinscripcion', $request->id_inscripcion)
                    ->where('idconcepto', 4)
                    ->where('estatuspago', 0)
                    ->lockForUpdate()
                    ->exists();

                if ($existePendiente) {
                    throw new Exception('Ya existe una reposición de tarjetón pendiente de pago para esta inscripción.');
                }

                $conceptoRepoTarjeton = Concepto::findOrFail(4);
                $fechaActual = now();
                $anioActual = $fechaActual->year;

                $ultimoFolio = Opago::whereYear('created_at', $anioActual)
                    ->lockForUpdate()
                    ->max('foliocc');

                $folioConsecutivo = 1;
                if ($ultimoFolio) {
                    $folioConsecutivo = ((int) explode('/', $ultimoFolio)[0]) + 1;
                }

                $opago = Opago::create([
                    'idinscripcion' => $request->id_inscripcion,
                    'mespago' => $fechaActual->format('m'),
                    'estatuspago' => 0,
                    'fechalimite' => $fechaActual->copy()->endOfMonth()->format('Y-m-d'),
                    'montototal' => $conceptoRepoTarjeton->costo,
                    'foliocc' => str_pad($folioConsecutivo, 6, '0', STR_PAD_LEFT) . '/' . $anioActual,
                    'foliocajas' => null,
                    'fechapago' => null,
                    'idconcepto' => 4,
                    'idgeneral' => 1,
                ]);

                Tarjeton::create([
                    'idinscripcion' => $request->id_inscripcion,
                    'idopago' => $opago->idopago,
                    'estatus' => '1',
                ]);
            });

            return redirect()->back()->with('success', 'Solicitud de reposición creada correctamente.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage());
        }
    }
    
    public function estTarjeton($idtarjeton, $estatus)
    {
        try {
            $tarjeton = Tarjeton::findOrFail($idtarjeton);
            // Verifica la acción y cambia el estatus
            if ($estatus === 'baja') {
                $tarjeton->estatus = 0; // Cambia el estatus a 0 (baja)
                $tarjeton->save();
            } elseif ($estatus === 'alta') {
                $tarjeton->estatus = 1;  // alta
                $tarjeton->save();
            } else {
                return redirect()->back()->with('status', 'Acción no válida.');
            }

            return redirect()->back()->with('success', 'El estatus del tarjetón ha sido actualizado correctamente.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Ocurrió un error al actualizar el estatus: ' . $e->getMessage());
        }
    }

    // public function actTarjeton(Request $request)
    // {
    //     $idtarjeton = $request->input('idtarjeton');
    //     $estatus = $request->input('estatus');

    //     $tarjeton = \App\Models\Tarjeton::find($idtarjeton);
    //     if ($tarjeton) {
    //         $tarjeton->estatus = $estatus;
    //         $tarjeton->save();
    //         return response()->json(['success' => true, 'message' => 'Estatus del tarjetón actualizado.']);
    //     } else {
    //         return response()->json(['success' => false, 'message' => 'Tarjetón no encontrado.']);
    //     }
    // }
}
