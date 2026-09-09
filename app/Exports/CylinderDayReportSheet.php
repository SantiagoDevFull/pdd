<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class CylinderDayReportSheet implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithTitle,
    WithColumnFormatting,
    WithStyles,
    WithEvents
{
    protected $datetime;
    protected $elements;
    protected $title;

    public function __construct($datetime, $elements, $title)
    {
        $this->datetime = $datetime;
        $this->elements = $elements; // se espera una colección con los campos conv_x_sum y ret_x_sum
        $this->title = $title;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function collection()
    {
        return $this->elements;
    }

    public function headings(): array
    {
        return [
            [
                'RUTA',
                'SERIE',
                'GUIA',
                '5',
                '10',
                '15',
                '45',
                '5',
                '10',
                '15',
                '45',
                'FECHA EMISION',
                'FECHA TRASLADO',
                'PLACA'
            ]
        ];
    }

    public function map($elements): array
    {
        // Siempre retornar valores numéricos (int/float) para evitar "números como texto"
        return [
            $elements->route_short_name,
            $elements->referral_guide_series,
            $elements->referral_guide_number,
            $elements->conv_5_sum ? $elements->conv_5_sum : '0',
            $elements->conv_10_sum ? $elements->conv_10_sum : '0',
            $elements->conv_15_sum ? $elements->conv_15_sum : '0',
            $elements->conv_45_sum ? $elements->conv_45_sum : '0',
            $elements->ret_5_sum ? $elements->ret_5_sum : '0',
            $elements->ret_10_sum ? $elements->ret_10_sum : '0',
            $elements->ret_15_sum ? $elements->ret_15_sum : '0',
            $elements->ret_45_sum ? $elements->ret_45_sum : '0',

            $elements->created_at_date,
            $elements->traslate_at_date,
            $elements->license_plate
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Vacío: la mayor parte de estilos se aplica en registerEvents
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Freeze header row
                $sheet->freezePane('A2');

                // Column auto-size (redundante con ShouldAutoSize, pero por seguridad)
                $lastCol = 'N'; // columnas A..N (14 columnas)
                foreach (range('A', $lastCol) as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(false);
                }

                // Estilizar encabezado (fila 1)
                $headerRange = 'A1:' . $lastCol . '1';
                $sheet->getStyle($headerRange)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '082180']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => [
                        'bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                    ],
                ]);

                $sheet->getStyle('D1:G1')->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0DB14B']],
                ]);
                $sheet->getStyle('H1:K1')->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F39C12']],
                ]);

                // Calcular totales en PHP
                $sumConv5  = 0.0;
                $sumConv10 = 0.0;
                $sumConv15 = 0.0;
                $sumConv45 = 0.0;
                $sumRet5   = 0.0;
                $sumRet10  = 0.0;
                $sumRet15  = 0.0;
                $sumRet45  = 0.0;

                $count = 0;
                if (is_countable($this->elements)) {
                    $count = count($this->elements);
                } elseif (method_exists($this->elements, 'count')) {
                    $count = $this->elements->count();
                }

                foreach ($this->elements as $row) {
                    $sumConv5  += (float) ($row->conv_5_sum ?? 0);
                    $sumConv10 += (float) ($row->conv_10_sum ?? 0);
                    $sumConv15 += (float) ($row->conv_15_sum ?? 0);
                    $sumConv45 += (float) ($row->conv_45_sum ?? 0);
                    $sumRet5   += (float) ($row->ret_5_sum ?? 0);
                    $sumRet10  += (float) ($row->ret_10_sum ?? 0);
                    $sumRet15  += (float) ($row->ret_15_sum ?? 0);
                    $sumRet45  += (float) ($row->ret_45_sum ?? 0);
                }

                // Determinar filas: encabezado=1, datos desde 2
                $dataStartRow = 2;
                $highestRow = $count > 0 ? $dataStartRow + $count - 1 : 1;
                $totalsRow = $highestRow >= $dataStartRow ? $highestRow + 1 : $dataStartRow;

                // Escribir totales como valores numéricos (columnas D..K)
                $sheet->setCellValue('D' . $totalsRow, $sumConv5);
                $sheet->setCellValue('E' . $totalsRow, $sumConv10);
                $sheet->setCellValue('F' . $totalsRow, $sumConv15);
                $sheet->setCellValue('G' . $totalsRow, $sumConv45);

                $sheet->setCellValue('H' . $totalsRow, $sumRet5);
                $sheet->setCellValue('I' . $totalsRow, $sumRet10);
                $sheet->setCellValue('J' . $totalsRow, $sumRet15);
                $sheet->setCellValue('K' . $totalsRow, $sumRet45);

                // Aplicar formato numérico a las celdas de totales
                $numberCells = ['D', 'E', 'F', 'G', 'H', 'I', 'J', 'K'];
                foreach ($numberCells as $col) {
                    $sheet->getStyle($col . $totalsRow)
                        ->getNumberFormat()
                        ->setFormatCode(NumberFormat::FORMAT_NUMBER);
                }

                // Estilar fila de totales
                $totalRange = 'A' . $totalsRow . ':' . $lastCol . $totalsRow;
                $sheet->getStyle($totalRange)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF2CC']],
                    'borders' => [
                        'top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                        'bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                    ],
                ]);

                // Alinear totales a la derecha (D..K)
                $sheet->getStyle('D' . $totalsRow . ':' . 'K' . $totalsRow)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // Añadir bordes a todo el rango (opcional)
                $finalRange = 'A1:' . $lastCol . $totalsRow;
                $sheet->getStyle($finalRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']],
                    ],
                ]);

                $sheet->getTabColor()->setRGB('082180');

                // Definir anchos por columna (unidades = caracteres aproximados)
                $widths = [
                    'A' => 8, // RUTA
                    'B' => 8,  // SERIE
                    'C' => 8, // GUIA
                    'D' => 6,  // 5
                    'E' => 6,  // 10
                    'F' => 6,  // 15
                    'G' => 6,  // 45
                    'H' => 6,  // 5 (ret)
                    'I' => 6,  // 10 (ret)
                    'J' => 6,  // 15 (ret)
                    'K' => 6,  // 45 (ret)
                    'L' => 18, // FECHA EMISION
                    'M' => 18, // FECHA TRASLADO
                    'N' => 16, // PLACA
                ];

                // Aplicar anchos
                foreach ($widths as $col => $w) {
                    $sheet->getColumnDimension($col)->setWidth($w);
                }

                // Si quieres además permitir ajuste de texto en una columna concreta:
                $sheet->getStyle('A1:A' . $sheet->getHighestRow())
                    ->getAlignment()
                    ->setWrapText(true);
            },
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_NUMBER,
            'E' => NumberFormat::FORMAT_NUMBER,
            'F' => NumberFormat::FORMAT_NUMBER,
            'G' => NumberFormat::FORMAT_NUMBER,
            'H' => NumberFormat::FORMAT_NUMBER,
            'I' => NumberFormat::FORMAT_NUMBER,
            'J' => NumberFormat::FORMAT_NUMBER,
            'K' => NumberFormat::FORMAT_NUMBER,
        ];
    }
}
