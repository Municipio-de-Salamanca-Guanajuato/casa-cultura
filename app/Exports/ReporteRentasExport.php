<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\Renta;
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

class ReporteRentasExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithColumnFormatting
{
    /**
     * @return \Illuminate\Support\Collection
     */
    
    public function collection()
    {
        $rentas = DB::table('rentas')
            ->join('ciudadanos', 'rentas.idciudadano', '=', 'ciudadanos.idciudadano')
            ->join('espacios', 'rentas.idespacio', '=', 'espacios.idespacio')
            ->leftJoin('opagos', 'rentas.idrenta', '=', 'opagos.idrenta') //  LEFT JOIN a opagos
            ->leftJoin('conceptos', 'opagos.idconcepto', '=', 'conceptos.idconcepto') // LEFT JOIN a conceptos
            ->where('rentas.estatus', 1)
            ->select(
                'ciudadanos.name',
                'ciudadanos.telefono',
                'espacios.nameroom',
                'rentas.idrenta',
                'rentas.fechaevento',
                'rentas.horainicio',
                'rentas.horafin',
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
                'conceptos.concepto',
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
            ->orderBy('opagos.foliocc')
            ->get();

        // dd($rentas);

        return $rentas;
    }

    public function map($renta): array
    {
        return [
            $renta->name,
            $renta->telefono ?? 'N/A',
            $renta->nameroom,
            $renta->fechaevento,
            $renta->horainicio,
            $renta->horafin,
            $renta->concepto,
            $renta->conceptosxr ?? 'No aplica',
            $renta->cantidadxrenta ?? 'No aplica',
            $renta->montototal,
            $renta->mespago,
            $renta->fechapago ?? 'Pago pendiente',
            $renta->foliocc,
            $renta->foliocajas ?? 'Pago pendiente',
            $renta->estatuspago == 1 ? 'Pagada' : 'No pagada',
        ];
    }

    public function headings(): array
    {
        return [
            'Nombre',
            'Teléfono',
            'Espacio',
            'Fecha del evento',
            'Hora de inicio',
            'Hora de fin',
            'Concepto',
            'Conceptos por renta',
            'Cantidad por concepto',
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

        $estatusColumn = 'O'; // Columna "Estatus del Pago"
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
            'J' => NumberFormat::FORMAT_NUMBER_00, // Columna Costo (I)
        ];
    }
}
