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

class ReportSheetListPriceComercio implements
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
            'Razón Social',
            'ID Ruta',
            'Negocio',
            'Sub Canal',
            'Area',

            '05 KLG',
            '10 KLG',
            '15 KLG',
            '45 KLG',

        ];
    }

    public function map($row): array
    {
        return [
            $row->client_id,
            $row->client_name,
            $row->route_name,
            $row->business_type_name,
            $row->channel_name,
            $row->zone_name,

            $row->PREM05K,
            $row->PREM10K,
            $row->PREM15K,
            $row->PREM45K,

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
                $sheet->setCellValue('A1', 'Lista de Precios Comercio al ' . date('Y-m-d'));

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

                $sheet->getSheetView()->setZoomScale(100);

                // Encabezado de tabla (fila 3)
                $sheet->getStyle('A3:J3')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '6BBD5B'],
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

                $sheet->getStyle("G4:J{$lastRow}")
                    ->getNumberFormat()
                    ->setFormatCode('0.0');

                // Bordes simples para toda la tabla
                $sheet->getStyle("A3:J{$lastRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                $sheet->getColumnDimension('A')->setWidth(6);
                $sheet->getColumnDimension('B')->setWidth(30);
                $sheet->getColumnDimension('C')->setWidth(6);
                $sheet->getColumnDimension('D')->setWidth(20);
                $sheet->getColumnDimension('E')->setWidth(20);
                $sheet->getColumnDimension('F')->setWidth(20);

                foreach (
                    [
                        'G',
                        'H',
                        'I',
                        'J',
                    ] as $column
                ) {
                    $sheet->getColumnDimension($column)->setWidth(6);
                }
            },
        ];
    }
}
