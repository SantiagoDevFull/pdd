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

class CylinderDataReportSheet implements
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

    public function __construct($datetime, $elements)
    {
        $this->datetime = $datetime;
        $this->elements = $elements;
    }

    public function title(): string
    {
        return 'DATA';
    }

    public function collection()
    {
        return $this->elements;
    }

    public function headings(): array
    {
        return [
            ['DATA DESPACHO Y RETORNO'],
            [
                'EMPRESA',
                'FECHA DESPACHO',
                'FECHA TRASLADO',
                'TIPO',
                'SERIE',
                'GUIA',
                'RUTA',
                'KG',
                'DESCRIPCION',
                'CANTIDAD',
                'LLENOS',
                'SCOP',
                'PLACA',
            ]
        ];
    }

    public function map($elements): array
    {
        return [
            $elements->company_short_name,
            $elements->created_at_date,
            $elements->traslate_at_date,
            $elements->movement_type_name,
            $elements->referral_guide_series,
            $elements->referral_guide_number,
            $elements->route_short_name,
            $elements->convertion,
            $elements->article_name,
            $elements->converted_amount == 0 || $elements->converted_amount == null ? '0' : $elements->converted_amount,
            $elements->new_stock_return == 0 || $elements->new_stock_return == null ? '0' : $elements->new_stock_return,
            $elements->scop_number,
            $elements->license_plate
        ];

    }

    public function styles(Worksheet $sheet)
    {

        return [
            // Estilo para el Título Principal (Fila 1)
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 16,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFB400'],
                ],
            ],
            // Estilo para los Encabezados (Fila 2)
            2 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => '4b0082']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFE5AB'],
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Combinar celdas del título
                $sheet->mergeCells('A1:M1');
                $sheet->getRowDimension('1')->setRowHeight(30);

                // Color de la pestaña
                $sheet->getTabColor()->setRGB('FFB400');
            },
        ];
    }

    public function columnFormats(): array
    {
        return [
            'J' => '#,##0',
            'K' => '#,##0',
        ];
    }
}
