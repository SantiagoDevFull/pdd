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

class PartitionReportSheetAnalysis implements
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
    protected $analisis_costo;

    public function __construct($datetime, $analisis_costo)
    {
        $this->datetime = $datetime;
        $this->analisis_costo = $analisis_costo;
    }

    public function title(): string
    {
        return 'Analisis Costo';
    }

    public function collection()
    {
        return $this->analisis_costo;
    }

    public function headings(): array
    {
        return [
            ['ANALISIS COSTO DE ENVASADO'],
            [
                'Mes',
                'Compañía',
                'Fecha de Emisión',
                'N° de Factura',
                'N° de Factura',
                'Producto',
                'Nro de Guia',
                'Cisterna',
                'Tracto',
                'Cantidad',
                'Precio Kg soles',
                'Soles',
                'Carga Terminal',
                'SCOP',
                'Conductor',
                'TM'
            ]
        ];
    }

    public function map($analisis_costo): array
    {
        return [
            $analisis_costo->nombre_mes,
            $analisis_costo->company_name,
            $analisis_costo->fecha_emision,
            $analisis_costo->factura,
            $analisis_costo->warehouse_type_name,
            $analisis_costo->article_name,
            $analisis_costo->guia,
            $analisis_costo->cisterna,
            $analisis_costo->tracto,
            (float)$analisis_costo->digit,
            (float)$analisis_costo->cost_glp,
            (float)$analisis_costo->soles,
            $analisis_costo->carga_terminal,
            $analisis_costo->scop,
            $analisis_costo->chofer,
            (float)$analisis_costo->tm,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $rowCount = count($this->analisis_costo) + 2; // +2 por las filas de encabezados

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
                    'startColor' => ['rgb' => '6f42c1'],
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
                    'startColor' => ['rgb' => 'e2d9f3'],
                ],
            ],
            // Estilo para la fila de Costo GLP
            "K" . ($rowCount + 5) => [ // +4 por las 3 filas en blanco + totales + fila central + Costo GLP
                'font' => [
                    'bold' => true,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'EDB32F'],
                ],
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $rowCount = count($this->analisis_costo) + 2; // +2 por las filas de encabezados

                // Combinar celdas del título
                $sheet->mergeCells('A1:P1');
                $sheet->getRowDimension('1')->setRowHeight(30);

                // Color de la pestaña
                $sheet->getTabColor()->setRGB('6f42c1');

                // Agregar fila en blanco después de los datos (3 filas)
                $startTotalRow = $rowCount + 3; // Fila donde van los totales

                // Agregar totales
                $sheet->setCellValue('H' . $startTotalRow, 'AVANCE');
                $sheet->setCellValue('J' . $startTotalRow, '=SUM(J3:J' . ($rowCount) . ')');
                $sheet->setCellValue('L' . $startTotalRow, '=SUM(L3:L' . ($rowCount) . ')');

                // Agregar fila de Costo GLP (2 filas después de los totales)
                $costoGLPRow = $startTotalRow + 2;
                $sheet->setCellValue('H' . $costoGLPRow, 'COSTO GLP');
                $sheet->setCellValue('K' . $costoGLPRow, "=IF(L{$startTotalRow}<>0,L{$startTotalRow}/J{$startTotalRow} * 10,0)");

                $sheet->getStyle('J' . $startTotalRow)->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle('L' . $startTotalRow)->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle('J' . $costoGLPRow)->getNumberFormat()->setFormatCode('#,##0.0000');
            },
        ];
    }

    public function columnFormats(): array
    {
        return [
            'J' => '#,##0',
            'K' => '#,##0.00',
            'L' => '#,##0.00',
            'P' => '#,##0.00',
        ];
    }
}
