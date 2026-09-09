<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class CylinderResumenReportSheet implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithTitle,
    WithColumnFormatting,
    WithStyles,
    WithEvents
{
    protected $startDate;
    protected $endDate;
    protected $despachosByDate;
    protected $devolucionesByDate;
    protected $rows;

    public function __construct($startDate, $endDate, Collection $despachos, Collection $devoluciones)
    {
        $this->startDate = Carbon::parse($startDate);
        $this->endDate   = Carbon::parse($endDate);

        $this->despachosByDate    = $despachos->keyBy(function ($item) {
            return Carbon::parse(data_get($item, 'date'))->format('Y-m-d');
        });
        $this->devolucionesByDate = $devoluciones->keyBy(function ($item) {
            return Carbon::parse(data_get($item, 'date'))->format('Y-m-d');
        });

        $this->rows = $this->buildRowsByDate();
    }

    protected function buildRowsByDate(): Collection
    {
        $rows = collect();

        for ($d = $this->startDate->copy(); $d->lte($this->endDate); $d->addDay()) {
            $key = $d->format('Y-m-d');

            $des = $this->despachosByDate->has($key) ? $this->despachosByDate->get($key) : null;
            $dev = $this->devolucionesByDate->has($key) ? $this->devolucionesByDate->get($key) : null;

            // Lectura segura y cast a float
            $conv5  = (float) data_get($des, 'conv_5_sum', 0);
            $conv10 = (float) data_get($des, 'conv_10_sum', 0);
            $conv15 = (float) data_get($des, 'conv_15_sum', 0);
            $conv45 = (float) data_get($des, 'conv_45_sum', 0);

            $ret5  = (float) data_get($dev, 'ret_5_sum', 0);
            $ret10 = (float) data_get($dev, 'ret_10_sum', 0);
            $ret15 = (float) data_get($dev, 'ret_15_sum', 0);
            $ret45 = (float) data_get($dev, 'ret_45_sum', 0);

            $rows->push((object)[
                'date_key'      => $key,
                'display_date'  => $d->format('Y-m-d'),
                'conv_5_sum'    => $conv5,
                'conv_10_sum'   => $conv10,
                'conv_15_sum'   => $conv15,
                'conv_45_sum'   => $conv45,
                'ret_5_sum'     => $ret5,
                'ret_10_sum'    => $ret10,
                'ret_15_sum'    => $ret15,
                'ret_45_sum'    => $ret45,
            ]);
        }

        return $rows;
    }

    public function title(): string
    {
        return 'RESUMEN';
    }

    public function headings(): array
    {
        return [
            ['FECHA', 'DESPACHOS', '', '', '', 'DEVOLUCIONES', '', '', ''],
            ['FECHA', '5 KG', '10 KG', '15 KG', '45 KG', '5 KG', '10 KG', '15 KG', '45 KG'],
        ];
    }

    public function collection()
    {
        return $this->rows;
    }

    // Helper para formatear celdas: si es 0 devuelve la cadena '0', si no devuelve el entero como cadena
    protected function formatCellValue($value): string
    {
        $v = (float) $value;
        // Mostrar '0' explícito cuando el valor es cero
        if ($v === 0.0) {
            return '0';
        }
        // devolver entero sin separadores ni decimales como texto
        return (string) (int) round($v);
    }

    public function map($row): array
    {
        return [
            $row->display_date,
            $this->formatCellValue($row->conv_5_sum),
            $this->formatCellValue($row->conv_10_sum),
            $this->formatCellValue($row->conv_15_sum),
            $this->formatCellValue($row->conv_45_sum),
            $this->formatCellValue($row->ret_5_sum),
            $this->formatCellValue($row->ret_10_sum),
            $this->formatCellValue($row->ret_15_sum),
            $this->formatCellValue($row->ret_45_sum),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Desactivar autosize
                foreach (range('A', 'I') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(false);
                }

                // Merge headers
                $sheet->mergeCells('A1:A2');
                $sheet->mergeCells('B1:E1');
                $sheet->mergeCells('F1:I1');

                // Estilos...
                $sheet->getStyle('B1:E1')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0DB14B']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getStyle('F1:I1')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F39C12']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getStyle('A1:A2')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0005FF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $sheet->getStyle('A2:I2')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '111111']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                ]);

                $sheet->freezePane('A3');

                $widths = [
                    'A' => 12,'B' => 8,'C' => 8,'D' => 8,'E' => 8,'F' => 8,'G' => 8,'H' => 8,'I' => 8,
                ];
                foreach ($widths as $col => $w) {
                    $sheet->getColumnDimension($col)->setWidth($w);
                }

                // Bordes/alineación datos
                $dataStart = 3;
                $dataCount = $this->rows->count();
                $lastDataRow = $dataCount > 0 ? $dataStart + $dataCount - 1 : $dataStart - 1;
                if ($lastDataRow >= $dataStart) {
                    $sheet->getStyle("A2:I{$lastDataRow}")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']]],
                    ]);
                    $sheet->getStyle("B{$dataStart}:I{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                }

                // Totales en PHP (numéricos)
                $tot = [
                    'conv5' => 0.0,'conv10' => 0.0,'conv15' => 0.0,'conv45' => 0.0,
                    'ret5'  => 0.0,'ret10'  => 0.0,'ret15'  => 0.0,'ret45'  => 0.0,
                ];
                foreach ($this->rows as $r) {
                    $tot['conv5']  += (float) ($r->conv_5_sum ?? 0);
                    $tot['conv10'] += (float) ($r->conv_10_sum ?? 0);
                    $tot['conv15'] += (float) ($r->conv_15_sum ?? 0);
                    $tot['conv45'] += (float) ($r->conv_45_sum ?? 0);
                    $tot['ret5']   += (float) ($r->ret_5_sum ?? 0);
                    $tot['ret10']  += (float) ($r->ret_10_sum ?? 0);
                    $tot['ret15']  += (float) ($r->ret_15_sum ?? 0);
                    $tot['ret45']  += (float) ($r->ret_45_sum ?? 0);
                }

                $finalRow = $lastDataRow + 1;

                // Escribir TOTAL (numérico)
                $sheet->setCellValue('A' . $finalRow, 'TOTAL');
                $sheet->setCellValue('B' . $finalRow, $tot['conv5']);
                $sheet->setCellValue('C' . $finalRow, $tot['conv10']);
                $sheet->setCellValue('D' . $finalRow, $tot['conv15']);
                $sheet->setCellValue('E' . $finalRow, $tot['conv45']);
                $sheet->setCellValue('F' . $finalRow, $tot['ret5']);
                $sheet->setCellValue('G' . $finalRow, $tot['ret10']);
                $sheet->setCellValue('H' . $finalRow, $tot['ret15']);
                $sheet->setCellValue('I' . $finalRow, $tot['ret45']);

                // Formato numérico totales
                foreach (range('B', 'I') as $col) {
                    $sheet->getStyle($col . $finalRow)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
                }

                // Estilo fila TOTAL
                $sheet->getStyle("A{$finalRow}:I{$finalRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFD966']],
                    'borders' => [
                        'top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '000000']],
                        'bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '000000']],
                    ],
                ]);
                $sheet->getStyle("B{$finalRow}:I{$finalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                try {
                    $sheet->getTabColor()->setRGB('0DB14B');
                } catch (\Throwable $e) {}
            },
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_NUMBER,
            'C' => NumberFormat::FORMAT_NUMBER,
            'D' => NumberFormat::FORMAT_NUMBER,
            'E' => NumberFormat::FORMAT_NUMBER,
            'F' => NumberFormat::FORMAT_NUMBER,
            'G' => NumberFormat::FORMAT_NUMBER,
            'H' => NumberFormat::FORMAT_NUMBER,
            'I' => NumberFormat::FORMAT_NUMBER,
        ];
    }
}
