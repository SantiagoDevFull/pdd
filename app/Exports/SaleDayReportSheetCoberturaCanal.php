<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class SaleDayReportSheetCoberturaCanal implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithTitle,
    WithColumnFormatting,
    WithStyles,
    WithEvents,
    WithCustomStartCell
{
    protected $cobertura_1;

    protected $estructura;

    public function __construct($cobertura_1)
    {
        $this->cobertura_1 = $cobertura_1;

        /*
        |--------------------------------------------------------------------------
        | ESTRUCTURA DINÁMICA
        |--------------------------------------------------------------------------
        */

        $channels = $this->cobertura_1
            ->map(function ($item) {
                return [
                    'sector' => strtoupper(trim($item->sector_name)),
                    'channel_id' => $item->channel_id, // <- Nuevo campo
                    'channel' => strtoupper(trim($item->channel_name)),
                    'sector_id' => $item->client_sector_id,
                ];
            })
            ->unique('channel_id') // <- Ahora usamos channel_id para unicidad
            ->sortBy(function ($item) {
                return $item['sector_id'] . '_' . $item['channel_id']; // <- Ordenar por channel_id
            })
            ->values();

        $this->estructura = $channels->groupBy('sector');
    }

    public function startCell(): string
    {
        return 'A2';
    }

    public function title(): string
    {
        return 'Cobertura Canal';
    }

    /*
    |--------------------------------------------------------------------------
    | COLLECTION
    |--------------------------------------------------------------------------
    */

    public function collection()
    {
        $grouped = $this->cobertura_1->groupBy(['anio', 'mes']);

        $result = collect();

        foreach ($grouped as $anio => $meses) {

            foreach ($meses as $mes => $items) {

                $row = [
                    'anio' => $anio,
                    'mes' => $mes,
                ];

                foreach ($this->estructura as $sector => $channels) {

                    $totalSector = 0;

                    foreach ($channels as $channelData) {
                        $channelId = $channelData['channel_id']; // <- Usar channel_id
                        $value = 0;

                        foreach ($items as $item) {
                            if ($item->channel_id == $channelId) { // <- Comparar por channel_id
                                $value = $item->total;
                                break;
                            }
                        }

                        $row['channel_' . $channelId] = $value; // <- Clave basada en channel_id
                        $totalSector += $value;
                    }

                    $row['TOTAL_' . $sector] = $totalSector;
                }

                $row['TOTAL_GENERAL'] = collect($row)
                    ->filter(function ($value, $key) {

                        return !in_array($key, [
                            'anio',
                            'mes'
                        ]) && strpos($key, 'TOTAL_') === false;
                    })
                    ->sum();

                $result->push((object)$row);
            }
        }

        return $result;
    }

    /*
    |--------------------------------------------------------------------------
    | HEADINGS
    |--------------------------------------------------------------------------
    */

    public function headings(): array
    {
        $headers = [
            'AÑO',
            'MES',
        ];

        foreach ($this->estructura as $sector => $channels) {

            foreach ($channels as $channelData) {

                $headers[] = $channelData['channel'];
            }

            $headers[] = 'TOTAL';
        }

        $headers[] = 'TOTAL GENERAL';

        return $headers;
    }

    /*
    |--------------------------------------------------------------------------
    | MAP
    |--------------------------------------------------------------------------
    */

    public function map($row): array
    {
        static $lastYear = null;

        $anioMostrar = ($lastYear === $row->anio)
            ? ''
            : $row->anio;

        $lastYear = $row->anio;

        $mapped = [
            $anioMostrar,
            $row->mes,
        ];

        foreach ($this->estructura as $sector => $channels) {

            foreach ($channels as $channelData) {
                $channelId = $channelData['channel_id']; // <- Obtener channel_id
                $value = $row->{'channel_' . $channelId} ?? 0; // <- Acceder por clave channel_id

                $mapped[] = ($value == 0)
                    ? '0'
                    : $value;
            }

            $mapped[] = $row->{'TOTAL_' . $sector} ?? 0;
        }

        $mapped[] = $row->TOTAL_GENERAL ?? 0;

        return $mapped;
    }

    /*
    |--------------------------------------------------------------------------
    | STYLES
    |--------------------------------------------------------------------------
    */

    public function styles(Worksheet $sheet)
    {
        return [];
    }

    /*
    |--------------------------------------------------------------------------
    | EVENTS
    |--------------------------------------------------------------------------
    */

    public function registerEvents(): array
    {
        return [

            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                $sheet->setShowGridlines(false);

                $event->sheet->getSheetView()->setZoomScale(90);

                $sheet->getTabColor()->setRGB('FF1212');

                $highestColumn = $sheet->getHighestDataColumn();

                $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

                /*
                |--------------------------------------------------------------------------
                | AÑO / MES
                |--------------------------------------------------------------------------
                */

                $sheet->mergeCells('A1:A2');
                $sheet->mergeCells('B1:B2');

                $sheet->setCellValue('A1', 'AÑO');
                $sheet->setCellValue('B1', 'MES');

                /*
                |--------------------------------------------------------------------------
                | SECTORES
                |--------------------------------------------------------------------------
                */

                $currentCol = 3;

                foreach ($this->estructura as $sector => $channels) {

                    $startCol = $currentCol;

                    foreach ($channels as $channelData) {

                        $sheet->setCellValueByColumnAndRow(
                            $currentCol,
                            2,
                            $channelData['channel']
                        );

                        $currentCol++;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | TOTAL POR SECTOR
                    |--------------------------------------------------------------------------
                    */

                    $sheet->setCellValueByColumnAndRow(
                        $currentCol,
                        2,
                        'TOTAL'
                    );

                    $currentCol++;

                    $endCol = $currentCol - 1;

                    $startLetter = Coordinate::stringFromColumnIndex($startCol);

                    $endLetter = Coordinate::stringFromColumnIndex($endCol);

                    /*
                    |--------------------------------------------------------------------------
                    | MERGE SECTOR
                    |--------------------------------------------------------------------------
                    */

                    $sheet->mergeCells(
                        $startLetter . '1:' . $endLetter . '1'
                    );

                    $sheet->setCellValue(
                        $startLetter . '1',
                        $sector
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | ESTILO SECTOR
                    |--------------------------------------------------------------------------
                    */

                    $sheet->getStyle(
                        $startLetter . '1:' . $endLetter . '1'
                    )->applyFromArray([

                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => '343A40'],
                        ],

                        'font' => [
                            'bold' => true,
                            'color' => ['rgb' => 'FFFFFF'],
                        ],

                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | ESTILO TOTAL SECTOR
                    |--------------------------------------------------------------------------
                    */

                    $totalLetter = Coordinate::stringFromColumnIndex($endCol);

                    $sheet->getStyle(
                        $totalLetter . '2'
                    )->applyFromArray([

                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'DC3545'],
                        ],

                        'font' => [
                            'bold' => true,
                            'color' => ['rgb' => 'FFFFFF'],
                        ],

                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | TOTAL GENERAL
                |--------------------------------------------------------------------------
                */

                $lastColLetter = Coordinate::stringFromColumnIndex($highestColumnIndex);

                $sheet->mergeCells(
                    $lastColLetter . '1:' . $lastColLetter . '2'
                );

                $sheet->setCellValue(
                    $lastColLetter . '1',
                    'TOTAL GENERAL'
                );

                $sheet->getStyle(
                    $lastColLetter . '1'
                )->applyFromArray([

                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'C00000'],
                    ],

                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],

                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                /*
                |--------------------------------------------------------------------------
                | HEADERS FILA 2
                |--------------------------------------------------------------------------
                */

                for ($col = 3; $col <= $highestColumnIndex; $col++) {

                    $letter = Coordinate::stringFromColumnIndex($col);

                    $value = $sheet->getCell($letter . '2')->getValue();

                    // TOTAL SECTOR
                    if ($value === 'TOTAL') {

                        $sheet->getStyle($letter . '2')->applyFromArray([

                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'FD7E14'], // naranja bootstrap
                            ],

                            'font' => [
                                'bold' => true,
                                'color' => ['rgb' => 'FFFFFF'],
                            ],

                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_CENTER,
                                'vertical' => Alignment::VERTICAL_CENTER,
                            ],
                        ]);
                    } else {

                        // CANALES
                        $sheet->getStyle($letter . '2')->applyFromArray([

                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => '28A745'],
                            ],

                            'font' => [
                                'bold' => true,
                                'color' => ['rgb' => 'FFFFFF'],
                            ],

                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_CENTER,
                                'vertical' => Alignment::VERTICAL_CENTER,
                            ],
                        ]);
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | AÑO / MES ESTILO
                |--------------------------------------------------------------------------
                */

                $sheet->getStyle('A1:B2')->applyFromArray([

                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '007BFF'],
                    ],

                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],

                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                /*
                |--------------------------------------------------------------------------
                | WRAP TEXT
                |--------------------------------------------------------------------------
                */

                $sheet->getStyle(
                    'A1:' . $highestColumn . '2'
                )->getAlignment()->setWrapText(true);

                $sheet->getRowDimension(1)->setRowHeight(25);
                $sheet->getRowDimension(2)->setRowHeight(40);

                /*
                |--------------------------------------------------------------------------
                | WIDTHS
                |--------------------------------------------------------------------------
                */

                $sheet->getColumnDimension('A')->setWidth(10);
                $sheet->getColumnDimension('B')->setWidth(10);

                for ($col = 3; $col <= $highestColumnIndex; $col++) {

                    $letter = Coordinate::stringFromColumnIndex($col);

                    $sheet->getColumnDimension($letter)
                        ->setWidth(16);
                }

                /*
                |--------------------------------------------------------------------------
                | BORDES
                |--------------------------------------------------------------------------
                */

                $highestRow = $sheet->getHighestRow();

                for ($row = 3; $row <= $highestRow; $row++) {

                    $mes = $sheet->getCell('B' . $row)->getValue();

                    if ($mes == 12) {

                        $range = 'A' . $row . ':' . $highestColumn . $row;

                        $sheet->getStyle($range)
                            ->getBorders()
                            ->getBottom()
                            ->setBorderStyle(Border::BORDER_MEDIUM);
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | FREEZE
                |--------------------------------------------------------------------------
                */

                $sheet->freezePane('C3');
            },
        ];
    }

    public function columnFormats(): array
    {
        return [];
    }
}
