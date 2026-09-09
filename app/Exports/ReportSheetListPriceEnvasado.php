<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ReportSheetListPriceEnvasado implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithTitle,
    WithStyles,
    WithEvents,
    WithCustomStartCell
{
    protected $prices;

    public function __construct($prices)
    {
        $this->prices = $prices;
    }

    public function startCell(): string
    {
        return 'A3';
    }

    public function title(): string
    {
        return 'Lista';
    }

    public function collection()
    {
        return $this->prices;
    }

    public function headings(): array
    {
        return [
            'Código',
            'Cliente',
            'Ruta',
            'Sub Canal',
            'Area',

            'C05K',
            'C10K',
            'C15K',
            'C45K',

            'E05K',
            'E10K',
            'E15K',
            'E45K',

            'M05K',
            'M10K',
            'M15K',
            'M45K',

            'P05K',
            'P10K',
            'P15K',
            'P45K',

            'R05K',
            'R10K',
            'R15K',
            'R45K',

        ];
    }

    public function map($row): array
    {
        return [
            $row->client_id,
            $row->client_name,
            $row->route_name,
            $row->channel_name,
            $row->zone_name,

            $row->C05K,
            $row->C10K,
            $row->C15K,
            $row->C45K,

            $row->E05K,
            $row->E10K,
            $row->E15K,
            $row->E45K,

            $row->M05K,
            $row->M10K,
            $row->M15K,
            $row->M45K,

            $row->P05K,
            $row->P10K,
            $row->P15K,
            $row->P45K,

            $row->R05K,
            $row->R10K,
            $row->R15K,
            $row->R45K,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                $sheet->getParent()->getDefaultStyle()->getFont()->setSize(10);
                $sheet->setShowGridlines(false);
                $sheet->getTabColor()->setRGB('000B70');

                // TÍTULO
                $sheet->setCellValue('A1', 'Lista de Precios Envasado al ' . date('Y-m-d'));

                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 10,
                        'color' => ['rgb' => 'FF0000'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->getSheetView()->setZoomScale(85);

                // Encabezado de tabla (fila 3)
                $sheet->getStyle('A3:Y3')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '000B70'],
                    ],
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                        'size' => 10,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Última fila con datos
                $lastRow = $sheet->getHighestRow();

                $sheet->getStyle("A3:A{$lastRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->getStyle("F4:Y{$lastRow}")
                    ->getNumberFormat()
                    ->setFormatCode('0.0');

                // Bordes simples para toda la tabla
                $sheet->getStyle("A3:Y{$lastRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                $sheet->getColumnDimension('A')->setWidth(6);
                $sheet->getColumnDimension('B')->setWidth(30);
                $sheet->getColumnDimension('C')->setWidth(6);
                $sheet->getColumnDimension('D')->setWidth(20);
                $sheet->getColumnDimension('E')->setWidth(20);

                foreach (
                    [
                        'F',
                        'G',
                        'H',
                        'I',
                        'J',
                        'K',
                        'L',
                        'M',
                        'N',
                        'O',
                        'P',
                        'Q',
                        'R',
                        'S',
                        'T',
                        'U',
                        'V',
                        'W',
                        'X',
                        'Y'
                    ] as $column
                ) {
                    $sheet->getColumnDimension($column)->setWidth(6);
                }
            },
        ];
    }
}
