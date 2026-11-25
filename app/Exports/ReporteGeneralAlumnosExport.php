<?php

namespace App\Exports;

use App\Models\Inscripcion;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ReporteGeneralAlumnosExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithColumnFormatting
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', '300');
        
        $inscripciones = DB::table('inscripciones')
            ->join('alumnos', 'inscripciones.idalumno', '=', 'alumnos.idalumno')
            ->join('talleres', 'inscripciones.idtaller', '=', 'talleres.idtaller')
            ->join('opagos', 'inscripciones.idinscripcion', '=', 'opagos.idinscripcion')
            ->join('conceptos', 'opagos.idconcepto', '=', 'conceptos.idconcepto')
            ->where('inscripciones.estatus', 1)
            ->orderBy('talleres.nametaller')
            ->orderBy('alumnos.name')
            ->orderBy('opagos.foliocc')
            ->select(
                DB::raw("CONCAT(alumnos.name, ' ', alumnos.apellidop, ' ', alumnos.apellidom) AS NombreCompleto"),
                'alumnos.curp',
                'alumnos.telefono',
                'alumnos.telefonotutor',
                'talleres.nametaller as Taller',
                'talleres.maestro as Maestro',
                DB::raw("CONCAT(talleres.fechainicio, ' al ', talleres.fechafin) AS Periodo"),
                'conceptos.concepto as Concepto',
                'opagos.montototal',
                'opagos.mespago',
                'opagos.fechapago',
                'opagos.foliocc',
                'opagos.foliocajas',
                'opagos.estatuspago'
            )
            ->get();

            $rentas = DB::table('rentas')
            ->join('ciudadanos', 'rentas.idciudadano', '=', 'ciudadanos.idciudadano')
            ->join('espacios', 'rentas.idespacio', '=', 'espacios.idespacio')
            ->leftJoin('opagos', 'rentas.idrenta', '=', 'opagos.idrenta') //  LEFT JOIN a opagos
            ->leftJoin('conceptos', 'opagos.idconcepto', '=', 'conceptos.idconcepto') // LEFT JOIN a conceptos
            ->where('rentas.estatus', 1)
            ->orderBy('opagos.foliocc')
            ->select(
                'ciudadanos.name AS NombreCompleto',
                // DB::raw("NULL as curp"), // no aplica
                'ciudadanos.telefono',
                DB::raw("NULL as telefonotutor"), // no aplica
                'espacios.nameroom as Taller',
                // DB::raw("NULL as Maestro"), // no aplica
                DB::raw("CONCAT(rentas.fechaevento, ' de ', rentas.horainicio, ' a ', rentas.horafin) as Periodo"),
                'rentas.idrenta',
                'rentas.cantidadxrenta',
                // 'rentas.conceptosxrenta',
                'rentas.estatus',
                'rentas.idciudadano',
                // Campos de opagos:
                'opagos.montototal',
                'opagos.mespago',
                'opagos.fechapago',
                'opagos.foliocc',
                'opagos.foliocajas',
                'opagos.estatuspago',
                'conceptos.concepto as Concepto',
                // Subconsulta para obtener los nombres de conceptos
                DB::raw("(
                    SELECT GROUP_CONCAT(
                        conceptosxrenta.namecxrenta
                        ORDER BY FIND_IN_SET(conceptosxrenta.idcxrenta, rentas.conceptosxrenta)
                        SEPARATOR ', '
                    )
                    FROM conceptosxrenta
                    WHERE FIND_IN_SET(conceptosxrenta.idcxrenta, rentas.conceptosxrenta)
                ) as conceptosxr")
            )
            ->get();

            // Combinar datos de alumnos y pagos
        $data = [];
        foreach ($inscripciones as $item) {
            $data[] = [
                'Nombre' => $item->NombreCompleto,
                'Edad' => $this->calcularEdad($item->curp),
                'Teléfono' => $item->telefono ?? 'N/A',
                'Teléfono Tutor' => $item->telefonotutor ?? 'N/A',
                'Taller/Espacio' => $item->Taller,
                'Maestro' => $item->Maestro ?? 'N/A',
                'Periodo' => $item->Periodo,
                'Concepto' => $item->Concepto,
                'Conceptos por Renta' => 'N/A', // no aplica
                'Cantidad por Concepto' => 'N/A', // no aplica
                'Costo' => $item->montototal,
                'Mes del Pago' => $item->mespago ?? '-',
                'Fecha de pago' => $item->fechapago ?? 'Pago pendiente',
                'No. Orden de pago' => $item->foliocc,
                'Folio de Cajas' => $item->foliocajas ?? 'Pago pendiente',
                'Estatus del Pago' => $item->estatuspago ? 'Pagada' : 'No pagada',
            ];
        }
    
        foreach ($rentas as $item) {
            $data[] = [
                'Nombre' => $item->NombreCompleto,
                'Edad' => 'N/A', // no aplica
                'Teléfono' => $item->telefono ?? 'N/A',
                'Teléfono Tutor' => 'N/A', // no aplica
                'Taller/Espacio' => $item->Taller,
                'Maestro' => 'N/A', // no aplica
                'Periodo' => $item->Periodo,
                'Concepto' => $item->Concepto ?? '-',
                'Conceptos por Renta' => $item->conceptosxr ?? 'N/A',
                'Cantidad por Concepto' => $item->cantidadxrenta ?? 'N/A',
                'Costo' => $item->montototal,
                'Mes del Pago' => $item->mespago ?? '-', // no aplica
                'Fecha de pago' => $item->fechapago ?? 'Pago pendiente',
                'No. Orden de pago' => $item->foliocc,
                'Folio de Cajas' => $item->foliocajas ?? 'Pago pendiente',
                'Estatus del Pago' => $item->estatuspago ? 'Pagada' : 'No pagada',
            ];
        }


        return collect($data);
    }

    // public function map($alumno): array
    // {
    //     return [
    //         $alumno->name . ' ' . $alumno->apellidop . ' ' . $alumno->apellidom,
    //         $this->calcularEdad($alumno->curp),
    //         $alumno->telefono ?? 'N/A',
    //         $alumno->telefonotutor ?? 'N/A',
    //         $alumno->nametaller,
    //         $alumno->maestro,
    //         $alumno->fechainicio . ' - ' . $alumno->fechafin,
    //         $alumno->concepto,
    //         $alumno->montototal,
    //         $alumno->mespago,
    //         $alumno->fechapago ?? 'Pago pendiente',
    //         $alumno->foliocc,
    //         $alumno->foliocajas ?? 'Pago pendiente',
    //         $alumno->estatuspago == 1 ? 'Pagada' : 'No pagada',
    //     ];
    // }

    public function headings(): array
    {
        return [
            'Nombre',
            'Edad',
            'Teléfono',
            'Teléfono Tutor',
            'Taller/Espacio',
            'Maestro',
            'Periodo',
            'Concepto',
            'Conceptos por Renta',
            'Cantidad por Concepto',
            'Costo',
            'Mes del Pago',
            'Fecha de pago',
            'No. Orden de pago',
            'Folio de Cajas',
            'Estatus del Pago'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Ajustar automáticamente el tamaño de las columnas
        $highestColumn = $sheet->getHighestColumn(); // Obtiene la última columna con datos
        foreach (range('A', $highestColumn) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Estilo de los encabezados
        $sheet->getStyle('A1:' . $highestColumn . '1')->getFont()->setBold(true);
        $sheet->getStyle('A1:' . $highestColumn . '1')->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('A1:' . $highestColumn . '1')->getFill()->getStartColor()->setRGB('FFA725'); // Color de fondo naranja
        $sheet->getStyle('A1:' . $highestColumn . '1')->getFont()->getColor()->setRGB('FFFFFF'); // Color de texto blanco
        $sheet->getStyle('A1:' . $highestColumn . '1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Centrar el texto

        // Estilo para el contenido de las celdas
        $sheet->getStyle('A2:' . $highestColumn . (count($this->collection()) + 1))
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN); // Bordes finos

        // Fondo de las celdas de contenido
        $sheet->getStyle('A2:' . $highestColumn . (count($this->collection()) + 1))->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('A2:' . $highestColumn . (count($this->collection()) + 1))->getFill()->getStartColor()->setRGB('FFF5E4'); // Color de fondo naranja claro

        // Alinear el texto de las celdas de contenido
        $sheet->getStyle('A2:' . $highestColumn . (count($this->collection()) + 1))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT); // Alinear texto a la izquierda

        $estatusColumn = 'P'; // Columna "Estatus del Pago"
        foreach ($sheet->getRowIterator(2) as $row) { // Comienza desde la fila 2 (omitir encabezados)
            $cell = $sheet->getCell($estatusColumn . $row->getRowIndex());
            $value = $cell->getValue();
            if ($value === 'Pagada') {
                $cell->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('92D050');
            } elseif ($value === 'No pagada') {
                $cell->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9D9D9');
            } elseif ($value === 'No aplica') {
                $cell->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('BFBFBF');
            }
        }
        // Resaltar encabezados
        return [
            '1' => ['font' => ['bold' => true]]
        ];
    }

    public function columnFormats(): array
    {
        return [
            'K' => NumberFormat::FORMAT_NUMBER_00, // Columna Costo (I)
        ];
    }

    // Edad cumplida a hoy con la CURP (misma regla que en inscripciones)
    private function calcularEdad($curp)
    {
        return \App\Models\Alumno::edadDeCurp($curp) ?? 'N/A';
    }

}
