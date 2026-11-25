<?php

namespace App\Exports;

use App\Models\Edificio;
use App\Models\TallerHorario;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Horario de talleres en el formato de "HORARIO CULTURA":
 * una hoja por día y edificio (LUNES LEONA VICARIO (LV), ...), columnas = aulas,
 * renglones = medias horas de 09:00 a 21:00 y cada taller en celdas combinadas con "Taller (Maestro)".
 *
 * Todos los talleres: los activos cuyas fechas se cruzan con el periodo (ej. uno de agosto a noviembre
 * sale en el periodo de febrero a noviembre), todas las hojas.
 * Un solo taller: solo las hojas (día y edificio) y aulas donde tiene horario.
 */
class HorariosExport
{
    private const HORA_INICIO = 9 * 60;  // 09:00
    private const HORA_FIN = 21 * 60;    // 21:00
    private const FILA_INICIO = 3;       // fila de 09:00-09:30

    // Colores pastel (uno por taller, para distinguirlos como en el Excel original)
    private const COLORES = [
        'E2F0D9', 'FFF2CC', 'DFEBF7', 'FBE5D6', 'D7C9FF', 'A9E9F3', 'E6E6AA', 'FFB083',
        'FF9999', 'FFFFA4', 'D5D09E', 'FF99FF', '8FDBE5', 'C6E0B4', 'F8CBAD', 'BDD7EE',
        'FFE699', 'D9D2E9', 'B4E5A2', 'F4B6C2',
    ];

    private array $colorPorTaller = [];

    public function __construct(private string $fechaIni, private string $fechaFin, private ?int $idtaller = null)
    {
    }

    public function crear(): Spreadsheet
    {
        $libro = new Spreadsheet();
        $libro->removeSheetByIndex(0);
        $libro->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);

        // Edificios activos con sus aulas activas (orden natural: AULA 2 antes de AULA 10)
        $edificios = Edificio::where('estatus', 1)
            ->with(['aulas' => fn ($q) => $q->where('estatus', 1)])
            ->orderBy('nombre')
            ->get()
            ->filter(fn ($e) => $e->aulas->isNotEmpty());

        $horarios = $this->horarios();

        foreach ($edificios as $edificio) {
            $aulas = $edificio->aulas->sort(fn ($a, $b) => strnatcasecmp($a->nombre, $b->nombre))->values();

            foreach (TallerHorario::DIAS as $dia => $nombreDia) {
                $delDia = $horarios->where('idedificio', $edificio->idedificio)->where('dia', $dia);

                // Un solo taller: solo los días y aulas donde tiene horario
                $aulasHoja = $aulas;
                if ($this->idtaller) {
                    if ($delDia->isEmpty()) {
                        continue;
                    }
                    $aulasHoja = $aulas->whereIn('idaula', $delDia->pluck('idaula')->unique())->values();
                    if ($aulasHoja->isEmpty()) {
                        continue;
                    }
                }

                $this->hoja($libro, mb_strtoupper($nombreDia, 'UTF-8') . ' ' . $edificio->nombre, $aulasHoja, $delDia);
            }
        }

        if ($libro->getSheetCount() === 0) {
            $libro->createSheet()->setTitle('SIN HORARIOS')->setCellValue('A1', 'No hay horarios para exportar en este periodo.');
        }

        $libro->setActiveSheetIndex(0);

        return $libro;
    }

    /**
     * Horarios activos de talleres activos que se cruzan con el periodo (o del taller elegido).
     */
    private function horarios()
    {
        return DB::table('taller_horarios as h')
            ->join('talleres as t', 't.idtaller', '=', 'h.idtaller')
            ->join('aulas as a', 'a.idaula', '=', 'h.idaula')
            ->where('h.estatus', 1)
            ->where('t.estatus', 1)
            ->when(
                $this->idtaller,
                fn ($q) => $q->where('t.idtaller', $this->idtaller),
                fn ($q) => $q->whereDate('t.fechainicio', '<=', $this->fechaFin)->whereDate('t.fechafin', '>=', $this->fechaIni)
            )
            ->orderBy('t.nametaller')
            ->select('h.idaula', 'h.dia', 'h.hora_inicio', 'h.hora_fin', 'a.idedificio', 't.idtaller', 't.nametaller', 't.maestro')
            ->get();
    }

    private function hoja(Spreadsheet $libro, string $titulo, $aulas, $horarios): void
    {
        $hoja = $libro->createSheet();
        $hoja->setTitle($this->tituloHoja($libro, $titulo));

        $ultimaCol = Coordinate::stringFromColumnIndex($aulas->count() + 1);
        $totalFilas = (self::HORA_FIN - self::HORA_INICIO) / 30;
        $ultimaFila = self::FILA_INICIO + $totalFilas - 1;

        // Fila 1: título (ej. LUNES LEONA VICARIO (LV))
        $hoja->mergeCells("B1:{$ultimaCol}1");
        $hoja->setCellValue('B1', $titulo);
        $hoja->getStyle("B1:{$ultimaCol}1")->getFont()->setBold(true)->setSize(12);
        $hoja->getRowDimension(1)->setRowHeight(26);

        // Fila 2: aulas
        $columnaDeAula = [];
        foreach ($aulas as $i => $aula) {
            $col = Coordinate::stringFromColumnIndex($i + 2);
            $columnaDeAula[$aula->idaula] = $col;
            $hoja->setCellValue("{$col}2", $aula->nombre);
            $hoja->getColumnDimension($col)->setWidth(20);
        }
        $hoja->getStyle("A2:{$ultimaCol}2")->getFont()->setBold(true);
        $hoja->getRowDimension(2)->setRowHeight(24);

        // Columna A: medias horas
        $hoja->getColumnDimension('A')->setWidth(13);
        for ($n = 0; $n < $totalFilas; $n++) {
            $ini = self::HORA_INICIO + $n * 30;
            $hoja->setCellValue('A' . (self::FILA_INICIO + $n), $this->hora($ini) . '-' . $this->hora($ini + 30));
            $hoja->getRowDimension(self::FILA_INICIO + $n)->setRowHeight(20);
        }
        $hoja->getStyle('A' . self::FILA_INICIO . ":A{$ultimaFila}")->getFont()->setBold(true);

        // Talleres
        $ocupadas = []; // "col|fila" => celda donde quedó el taller (por si dos se enciman)
        foreach ($horarios as $h) {
            $col = $columnaDeAula[$h->idaula] ?? null;
            if (!$col) {
                continue;
            }

            $desde = max(self::HORA_INICIO, $this->minutos($h->hora_inicio));
            $hasta = min(self::HORA_FIN, $this->minutos($h->hora_fin));
            if ($hasta <= $desde) {
                continue;
            }

            $filaIni = self::FILA_INICIO + intdiv($desde - self::HORA_INICIO, 30);
            $filaFin = self::FILA_INICIO + intdiv($hasta - self::HORA_INICIO, 30) - 1;
            $texto = $h->nametaller . ($h->maestro ? "\n(" . $h->maestro . ')' : '');

            // Si ya hay algo en ese rango (no debería, por las validaciones), se agrega el texto sin combinar
            $choque = null;
            for ($f = $filaIni; $f <= $filaFin; $f++) {
                $choque ??= $ocupadas["$col|$f"] ?? null;
            }
            if ($choque) {
                $hoja->setCellValue($choque, $hoja->getCell($choque)->getValue() . "\n/ " . $texto);
                continue;
            }

            $celda = "{$col}{$filaIni}";
            for ($f = $filaIni; $f <= $filaFin; $f++) {
                $ocupadas["$col|$f"] = $celda;
            }
            if ($filaFin > $filaIni) {
                $hoja->mergeCells("{$celda}:{$col}{$filaFin}");
            }
            $hoja->setCellValue($celda, $texto);
            $hoja->getStyle("{$celda}:{$col}{$filaFin}")->getFill()
                ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($this->color($h));
        }

        // Estilo general: centrado, con ajuste de texto y bordes
        $rango = "A2:{$ultimaCol}{$ultimaFila}";
        $hoja->getStyle("A1:{$ultimaCol}{$ultimaFila}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);
        $hoja->getStyle($rango)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Impresión: horizontal, una página de ancho, repetir encabezados
        $hoja->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(PageSetup::PAPERSIZE_LETTER)
            ->setFitToWidth(1)->setFitToHeight(0);
        $hoja->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 2);
        $hoja->freezePane('B3');
    }

    // Excel: máximo 31 caracteres, sin : \ / ? * [ ] y sin repetidos
    private function tituloHoja(Spreadsheet $libro, string $titulo): string
    {
        $base = mb_substr(trim(str_replace([':', '\\', '/', '?', '*', '[', ']'], ' ', $titulo)), 0, 31);
        $nombre = $base;
        for ($n = 2; $libro->sheetNameExists($nombre); $n++) {
            $nombre = mb_substr($base, 0, 31 - strlen(" $n")) . " $n";
        }

        return $nombre;
    }

    // Un color por taller (el mismo en todas las hojas)
    private function color($h): string
    {
        if (!isset($this->colorPorTaller[$h->idtaller])) {
            $this->colorPorTaller[$h->idtaller] = self::COLORES[count($this->colorPorTaller) % count(self::COLORES)];
        }

        return $this->colorPorTaller[$h->idtaller];
    }

    private function minutos(string $hora): int
    {
        [$h, $m] = explode(':', $hora);

        return (int) $h * 60 + (int) $m;
    }

    private function hora(int $minutos): string
    {
        return sprintf('%02d:%02d', intdiv($minutos, 60), $minutos % 60);
    }

    // HORARIO CULTURA (04-01-2027 al 30-06-2027).xlsx  /  HORARIO PINTURA SOBRE TELA (...).xlsx
    public function nombreArchivo(): string
    {
        $nombre = 'CULTURA';
        if ($this->idtaller) {
            $taller = DB::table('talleres')->where('idtaller', $this->idtaller)->first(['nametaller', 'fechainicio', 'fechafin']);
            $nombre = trim(preg_replace('/[\\\\\/:*?"<>|]+/', ' ', $taller->nametaller));
            [$ini, $fin] = [$taller->fechainicio, $taller->fechafin];
        } else {
            [$ini, $fin] = [$this->fechaIni, $this->fechaFin];
        }

        return 'HORARIO ' . $nombre . ' (' . Carbon::parse($ini)->format('d-m-Y') . ' al ' . Carbon::parse($fin)->format('d-m-Y') . ').xlsx';
    }
}
