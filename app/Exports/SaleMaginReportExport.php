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
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class SaleMaginReportExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithTitle,
    WithColumnFormatting,
    WithStyles,
    WithEvents
{
    protected $base;

    public function __construct($base)
    {
        $this->base = $base;
    }

    public function title(): string
    {
        return 'BASE';
    }

    public function collection()
    {
        return $this->base;
    }

    public function headings(): array
    {
        return [
            'Compañia',
            'Fecha de Despacho',
            'Unidad de Negocio',
            'Sub Canal',
            'Ruta',
            'TM',
            'Precio',
            'Total',
        ];
    }

    public function map($base): array
    {
        // Forzamos numéricos a float para evitar que Excel los trate como texto
        $tm = is_numeric($base->tm) ? (float) $base->tm : $base->tm;
        $precio = is_numeric($base->precio) ? (float) $base->precio : $base->precio;
        $total = is_numeric($base->total) ? (float) $base->total : $base->total;

        return [
            $base->companies_short_name,
            $base->sales_sale_date,
            $base->business_units_name,
            $base->client_channels_name,
            $base->client_routes_short_name,
            $tm,
            $precio,
            $total,
        ];
    }

    /**
     * Estilos "estáticos" aplicados por WithStyles.
     * Después de insertar la fila de SUBTOTALES, el encabezado queda en la fila 2,
     * por eso aplicamos estilo a la fila 2 aquí.
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => '000000'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFD091'],
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

                // Color de pestaña
                $sheet->getTabColor()->setRGB('FFD091');

                // Ocultar cuadrículas
                $sheet->setShowGridLines(false);

                // Determinar última fila y columna (antes de insertar fila)
                $lastRow = (int) $sheet->getHighestRow();
                $lastColumn = $sheet->getHighestColumn();

                // Si no hay datos (solo encabezado) aún funciona: insertamos fila 1
                $sheet->insertNewRowBefore(1, 1);

                // Nueva última fila (desplazada +1)
                $newLastRow = $lastRow + 1;

                // Fila de subtotales (arriba del encabezado)
                $subtotalRow = 1;

                // Encabezado quedó en la fila 2, datos empiezan en la fila 3
                $headerRow = 2;
                $dataStartRow = 3;

                // Usamos SUBTOTAL(9, rango) para que respete filtros (9 = SUM)
                // TM -> columna F, Total -> columna H
                $sheet->setCellValue("F{$subtotalRow}", "=SUBTOTAL(9,F{$dataStartRow}:F{$newLastRow})");
                $sheet->setCellValue("H{$subtotalRow}", "=SUBTOTAL(9,H{$dataStartRow}:H{$newLastRow})");

                // Porcentaje en G: Hsubtotal / Fsubtotal (evitar división por cero)
                $sheet->setCellValue("G{$subtotalRow}", "=IF(F{$subtotalRow}=0,0,H{$subtotalRow}/F{$subtotalRow})");

                // Formatos numéricos: TM (F) y Total (H) con 2 decimales; G (subtotal) en porcentaje
                $numberFormat = '#,##0.00';
                $pctFormat = '#,##0.00';

                $sheet->getStyle("F{$subtotalRow}:F{$subtotalRow}")->getNumberFormat()->setFormatCode($numberFormat);
                $sheet->getStyle("H{$subtotalRow}:H{$subtotalRow}")->getNumberFormat()->setFormatCode($numberFormat);
                $sheet->getStyle("G{$subtotalRow}:G{$subtotalRow}")->getNumberFormat()->setFormatCode($pctFormat);

                // Estilo visual para la fila de subtotales
                $lastColLetter = $lastColumn; // lastColumn no cambia al insertar fila
                $sheet->getStyle("A{$subtotalRow}:{$lastColLetter}{$subtotalRow}")
                    ->applyFromArray([
                        'font' => ['bold' => true],
                        'borders' => ['bottom' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                $sheet->getStyle("A{$subtotalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // Aplicar formato numérico a las columnas de datos (por si acaso):
                if ($newLastRow >= $dataStartRow) {
                    // TM (F) y Total (H) datos como número con 2 decimales
                    $sheet->getStyle("F{$dataStartRow}:F{$newLastRow}")->getNumberFormat()->setFormatCode($numberFormat);
                    $sheet->getStyle("H{$dataStartRow}:H{$newLastRow}")->getNumberFormat()->setFormatCode($numberFormat);

                    // Precio (G) datos como número con 2 decimales (no porcentaje)
                    $sheet->getStyle("G{$dataStartRow}:G{$newLastRow}")->getNumberFormat()->setFormatCode($numberFormat);
                }

                // Freeze pane para que quede visible el encabezado (fila 2)
                $sheet->freezePane('A3');

                // Aplicar autofiltro sobre el encabezado (ahora fila 2)
                $sheet->setAutoFilter("A{$headerRow}:{$lastColLetter}{$newLastRow}");

                // Ajuste de ancho (opcional): auto size ya está activado por ShouldAutoSize,
                // pero podemos forzar algunas columnas importantes si lo deseas:
                // $sheet->getColumnDimension('A')->setAutoSize(true);
            },
        ];
    }

    public function columnFormats(): array
    {
        // Aplicar formato por columna (esto se aplica a toda la columna).
        // Dejamos G como número con decimales (precio). La celda G1 (subtotal) la formateamos a % en AfterSheet.
        return [
            'F' => '#,##0.00', // TM
            'G' => '#,##0.00', // Precio (datos)
            'H' => '#,##0.00', // Total
        ];
    }
}
