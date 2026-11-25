<?php

namespace App\Exports;

use App\Models\Opago;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PaseListaExport implements FromCollection, WithHeadings, WithStyles, WithTitle, WithColumnFormatting
{
    /**
    //  * @return \Illuminate\Support\Collection
     */

    protected $idtaller;
    protected $nombreTaller;

    public function __construct($idtaller, $nombreTaller)
    {
        $this->idtaller = $idtaller;
        $this->nombreTaller = $nombreTaller;
    }

    public function title(): string
    {
        return $this->nombreTaller;
    }

    // public function collection()
    // {
    //     return Opago::join('inscripciones', 'opagos.idinscripcion', '=', 'inscripciones.idinscripcion')
    //     ->join('alumnos', 'inscripciones.idalumno', '=', 'alumnos.idalumno')
    //     ->join('talleres', 'inscripciones.idtaller', '=', 'talleres.idtaller')
    //     ->join('conceptos', 'opagos.idconcepto', '=', 'conceptos.idconcepto')
    //     ->where('inscripciones.estatus', 1)
    //     ->where('inscripciones.idtaller', $this->idtaller) // Filtrar por taller seleccionado
    //     ->orderBy('opagos.foliocc', 'ASC') // Ordenar por foliocc como en la consulta original
    //     ->selectRaw("
    //         CONCAT(alumnos.name, ' ', alumnos.apellidop, ' ', alumnos.apellidom) AS NombreCompleto, 
    //         talleres.nametaller AS Taller, 
    //         CONCAT(talleres.fechainicio, ' - ', talleres.fechafin) AS Periodo,
    //         conceptos.concepto AS Concepto,
    //         opagos.mespago AS MesPago,
    //         opagos.foliocc AS FolioCultura,
    //         opagos.foliocajas AS FolioCajas,

    //         CASE 
    //             WHEN opagos.estatuspago = 1 THEN 'Pagado' 
    //             ELSE 'En proceso de pago' 
    //         END AS EstatusPago
    //     ")
    //     ->get();
    // }

    public function collection()
    {
        // Consulta para obtener los datos de alumnos, talleres y pagos
        $alumnos = DB::table('inscripciones')
            ->join('alumnos', 'inscripciones.idalumno', '=', 'alumnos.idalumno')
            ->join('talleres', 'inscripciones.idtaller', '=', 'talleres.idtaller')
            ->join('opagos', 'inscripciones.idinscripcion', '=', 'opagos.idinscripcion')
            ->join('conceptos', 'opagos.idconcepto', '=', 'conceptos.idconcepto')
            ->where('inscripciones.estatus', 1)
            ->where('inscripciones.idtaller', $this->idtaller) // Filtrar por taller seleccionado
            ->orderBy('opagos.foliocc', 'ASC') // Ordenar por foliocc
            ->selectRaw("
                CONCAT(alumnos.name, ' ', alumnos.apellidop, ' ', alumnos.apellidom) AS NombreCompleto,
                alumnos.curp,
                alumnos.telefono,
                alumnos.telefonotutor,
                talleres.nametaller AS Taller,
                talleres.maestro AS Maestro,
                CONCAT(talleres.fechainicio, ' - ', talleres.fechafin) AS Periodo,
                conceptos.concepto AS Concepto,
                opagos.montototal,
                opagos.mespago AS MesPago,
                opagos.fechapago AS FechaPago,
                opagos.foliocc AS FolioCultura,
                opagos.foliocajas AS FolioCajas,
                opagos.estatuspago AS EstatusPago
            ")
            ->get();

        // Combinar datos de alumnos y pagos
        $data = [];
        foreach ($alumnos as $alumno) {
            $fila = [
                'Nombre del Alumno' => $alumno->NombreCompleto,
                'Edad' => $this->calcularEdad($alumno->curp), // Calcular edad a partir del CURP
                'Teléfono' => $alumno->telefono ?? 'N/A', // Valor predeterminado si está vacío
                'Teléfono Tutor' => $alumno->telefonotutor ?? 'N/A', // Valor predeterminado si está vacío
                'Taller' => $alumno->Taller,
                'Maestro' => $alumno->Maestro,
                'Periodo del Taller' => $alumno->Periodo,
                'Concepto' => $alumno->Concepto,
                'Costo' => $alumno->montototal,
                'Mes del Pago' => $alumno->MesPago,
                'Fecha de pago' => $alumno->FechaPago ?? 'Pago pendiente', // Valor predeterminado si está vacío
                'No. Orden de pago' => $alumno->FolioCultura,
                'Folio de Cajas' => $alumno->FolioCajas ?? 'Pago pendiente', // Valor predeterminado si está vacío
                'Estatus del Pago' => $alumno->EstatusPago ? 'Pagada' : 'No pagada', // Convertir estatuspago a texto
            ];

            $data[] = $fila;
        }

        return collect($data);
    }

    public function headings(): array
    {
        return [
            "Nombre del Alumno",
            "Edad",
            "Teléfono",
            "Teléfono Tutor",
            "Taller",
            "Maestro",
            "Periodo del Taller",
            "Concepto",
            "Costo",
            "Mes del Pago",
            "Fecha de pago",
            "No. Orden de pago",
            "Folio de Cajas",
            "Estatus del Pago"
        ];
    }

    // public function collection()
    // {
    //     // Consulta 1: Datos de alumnos y talleres
    //     $alumnos = DB::table('inscripciones')
    //         ->join('alumnos', 'inscripciones.idalumno', '=', 'alumnos.idalumno')
    //         ->join('talleres', 'inscripciones.idtaller', '=', 'talleres.idtaller')
    //         ->where('inscripciones.estatus', 1)
    //         ->where('inscripciones.idtaller', $this->idtaller)
    //         ->selectRaw("
    //             inscripciones.idinscripcion,
    //             CONCAT(alumnos.name, ' ', alumnos.apellidop, ' ', alumnos.apellidom) AS NombreCompleto,
    //             alumnos.curp,
    //             talleres.nametaller AS Taller,
    //             talleres.maestro AS Maestro
    //         ")
    //         ->get();

    //     // Consulta 2: Datos de pagos (inscripción y mensualidades)
    //     $pagos = DB::table('opagos')
    //         ->join('inscripciones', 'opagos.idinscripcion', '=', 'inscripciones.idinscripcion')
    //         ->join('conceptos', 'opagos.idconcepto', '=', 'conceptos.idconcepto')
    //         ->where('inscripciones.estatus', 1)
    //         ->where('inscripciones.idtaller', $this->idtaller)
    //         ->selectRaw("
    //             opagos.idinscripcion,
    //             MAX(CASE WHEN conceptos.concepto = 'Inscripción' THEN opagos.estatuspago ELSE NULL END) AS Inscripcion,
    //             MAX(CASE WHEN conceptos.concepto = 'Mensualidad' AND opagos.mespago = '01' THEN opagos.estatuspago ELSE NULL END) AS Mensualidad_01,
    //             MAX(CASE WHEN conceptos.concepto = 'Mensualidad' AND opagos.mespago = '02' THEN opagos.estatuspago ELSE NULL END) AS Mensualidad_02,
    //             MAX(CASE WHEN conceptos.concepto = 'Mensualidad' AND opagos.mespago = '03' THEN opagos.estatuspago ELSE NULL END) AS Mensualidad_03,
    //             MAX(CASE WHEN conceptos.concepto = 'Mensualidad' AND opagos.mespago = '04' THEN opagos.estatuspago ELSE NULL END) AS Mensualidad_04,
    //             MAX(CASE WHEN conceptos.concepto = 'Mensualidad' AND opagos.mespago = '05' THEN opagos.estatuspago ELSE NULL END) AS Mensualidad_05,
    //             MAX(CASE WHEN conceptos.concepto = 'Mensualidad' AND opagos.mespago = '06' THEN opagos.estatuspago ELSE NULL END) AS Mensualidad_06,
    //             MAX(CASE WHEN conceptos.concepto = 'Mensualidad' AND opagos.mespago = '07' THEN opagos.estatuspago ELSE NULL END) AS Mensualidad_07,
    //             MAX(CASE WHEN conceptos.concepto = 'Mensualidad' AND opagos.mespago = '08' THEN opagos.estatuspago ELSE NULL END) AS Mensualidad_08,
    //             MAX(CASE WHEN conceptos.concepto = 'Mensualidad' AND opagos.mespago = '09' THEN opagos.estatuspago ELSE NULL END) AS Mensualidad_09,
    //             MAX(CASE WHEN conceptos.concepto = 'Mensualidad' AND opagos.mespago = '10' THEN opagos.estatuspago ELSE NULL END) AS Mensualidad_10,
    //             MAX(CASE WHEN conceptos.concepto = 'Mensualidad' AND opagos.mespago = '11' THEN opagos.estatuspago ELSE NULL END) AS Mensualidad_11,
    //             MAX(CASE WHEN conceptos.concepto = 'Mensualidad' AND opagos.mespago = '12' THEN opagos.estatuspago ELSE NULL END) AS Mensualidad_12
    //         ")
    //         ->groupBy('opagos.idinscripcion')
    //         ->get();

    //     // Combinar datos de alumnos y pagos
    //     $data = [];
    //     foreach ($alumnos as $alumno) {
    //         $pagoAlumno = $pagos->where('idinscripcion', $alumno->idinscripcion)->first();

    //         $fila = [
    //             'Nombre del Alumno' => $alumno->NombreCompleto,
    //             'Edad' => $this->calcularEdad($alumno->curp), // Calcular edad a partir del CURP
    //             'Taller' => $alumno->Taller,
    //             'Maestro' => $alumno->Maestro,
    //             'Inscripción' => $pagoAlumno->Inscripcion ? 'Pagada' : 'No pagada',
    //         ];

    //         // Agregar las 12 mensualidades
    //         for ($mes = 1; $mes <= 12; $mes++) {
    //             $mesFormateado = str_pad($mes, 2, '0', STR_PAD_LEFT); // Formato 01, 02, ..., 12
    //             $columna = "Mensualidad_{$mesFormateado}";
    //             $fila["Mensualidad (Mes: {$mesFormateado})"] = $pagoAlumno->$columna !== null ? ($pagoAlumno->$columna ? 'Pagada' : 'No pagada') : 'No aplica';
    //         }

    //         $data[] = $fila;
    //     }

    //     return collect($data);
    // }

    // public function headings(): array
    // {
    //     // Encabezados fijos para las 12 mensualidades
    //     $encabezados = [
    //         'Nombre del Alumno',
    //         'Edad',
    //         'Taller',
    //         'Maestro',
    //         'Inscripción',
    //     ];

    //     // Agregar encabezados para las 12 mensualidades
    //     for ($mes = 1; $mes <= 12; $mes++) {
    //         $mesFormateado = str_pad($mes, 2, '0', STR_PAD_LEFT); // Formato 01, 02, ..., 12
    //         $encabezados[] = "Mensualidad (Mes: {$mesFormateado})";
    //     }

    //     return $encabezados;
    // }

    public function columnFormats(): array
    {
        return [
            'I' => NumberFormat::FORMAT_NUMBER_00, // I = columna de "Costo"
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

        // Sin alumnos inscritos: solo se deja el encabezado (antes tronaba al dar formato a filas que no existen)
        $ultimaFila = $sheet->getHighestRow();
        if ($ultimaFila < 2) {
            return [
                '1' => ['font' => ['bold' => true]],
            ];
        }

        $contenido = 'A2:' . $highestColumn . $ultimaFila;

        // Estilo para el contenido de las celdas
        $sheet->getStyle($contenido)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN); // Bordes finos

        // Fondo de las celdas de contenido
        $sheet->getStyle($contenido)->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle($contenido)->getFill()->getStartColor()->setRGB('FFF5E4'); // Color de fondo naranja claro

        // Alinear el texto de las celdas de contenido
        $sheet->getStyle($contenido)
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT); // Alinear texto a la izquierda

        $estatusColumn = 'N'; // Columna "Estatus del Pago"
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

        // foreach ($sheet->getRowIterator() as $row) {
        //     foreach ($row->getCellIterator() as $cell) {
        //         $value = $cell->getValue();
        //         if ($value === 'Pagada') {
        //             $cell->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('92D050');
        //         } elseif ($value === 'No pagada') {
        //             $cell->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FABF8F');
        //         } elseif ($value === 'No aplica') {
        //             $cell->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('BFBFBF');
        //         }
        //     }
        // }

        // // Estilos condicionales para las celdas de mensualidades
        // $conditionalStyles = [];

        // // Estilo para "Pagada" (Verde: #92D050)
        // $conditionalPagada = new Conditional();
        // $conditionalPagada->setConditionType(Conditional::CONDITION_CELLIS)
        //     ->setOperatorType(Conditional::OPERATOR_EQUAL)
        //     ->addCondition('Pagada')
        //     ->getStyle()->getFill()
        //     ->setFillType(Fill::FILL_SOLID)
        //     ->getStartColor()->setRGB('92D050');
        // $conditionalStyles[] = $conditionalPagada;

        // // Estilo para "No pagada" (Naranja: #FABF8F)
        // $conditionalNoPagada = new Conditional();
        // $conditionalNoPagada->setConditionType(Conditional::CONDITION_CELLIS)
        //     ->setOperatorType(Conditional::OPERATOR_EQUAL)
        //     ->addCondition('No pagada')
        //     ->getStyle()->getFill()
        //     ->setFillType(Fill::FILL_SOLID)
        //     ->getStartColor()->setRGB('FABF8F');
        // $conditionalStyles[] = $conditionalNoPagada;

        // // Estilo para "No aplica" (Gris: #BFBFBF)
        // $conditionalNoAplica = new Conditional();
        // $conditionalNoAplica->setConditionType(Conditional::CONDITION_CELLIS)
        //     ->setOperatorType(Conditional::OPERATOR_EQUAL)
        //     ->addCondition('No aplica')
        //     ->getStyle()->getFill()
        //     ->setFillType(Fill::FILL_SOLID)
        //     ->getStartColor()->setRGB('BFBFBF');
        // $conditionalStyles[] = $conditionalNoAplica;
        // // Aplicar estilos condicionales a la columna de Inscripción (columna E)
        // $sheet->getStyle('E2:E' . ($sheet->getHighestRow()))
        //     ->setConditionalStyles($conditionalStyles);

        // // Aplicar estilos condicionales a las columnas de mensualidades (F a Q)
        // $sheet->getStyle('F2:Q' . ($sheet->getHighestRow()))
        //     ->setConditionalStyles($conditionalStyles);

        return [
            // Asegurar que la fila de encabezados esté en negrita
            '1' => ['font' => ['bold' => true]],
        ];
    }

    // Edad cumplida a hoy con la CURP (misma regla que en inscripciones)
    private function calcularEdad($curp)
    {
        return \App\Models\Alumno::edadDeCurp($curp) ?? 'N/A';
    }
}
