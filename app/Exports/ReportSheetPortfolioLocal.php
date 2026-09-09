<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Color;

class ReportSheetPortfolioLocal implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithTitle,
    WithEvents
{
    protected $elements;
    protected $finalDate;

    public function __construct($elements, $finalDate)
    {
        $this->elements = $elements;
        $this->finalDate = $finalDate;
    }

    public function collection()
    {
        return $this->elements;
    }

    public function title(): string
    {
        return 'Cartera Clientes Locales';
    }

    public function headings(): array
    {
        Carbon::setLocale('es');
        return [
            'Cód. Cliente',
            'Cliente',
            'Canal',
            'Subcanal',
            'Estado',
            ucfirst($this->finalDate->copy()->subMonths(6)->translatedFormat('F')),
            ucfirst($this->finalDate->copy()->subMonths(5)->translatedFormat('F')),
            ucfirst($this->finalDate->copy()->subMonths(4)->translatedFormat('F')),
            ucfirst($this->finalDate->copy()->subMonths(3)->translatedFormat('F')),
            ucfirst($this->finalDate->copy()->subMonths(2)->translatedFormat('F')),
            ucfirst($this->finalDate->copy()->subMonths(1)->translatedFormat('F')),
            'Venta Mes Análisis',
            'Venta Proyección',
            'Total',
            'Meses Compra',
            'Negocio',
            'Celular',
            'Dirección',
            'Distrito',
            'Ruta',
            'Zona',
            'Sector',
            'GPS X',
            'GPS Y',
            'Forma de Pago',
            '5KG',
            '10KG',
            '15KG',
            '45KG',
        ];
    }

    public function map($row): array
    {
        return [
            $row->client_id,
            $row->cliente,
            $row->canal,
            $row->subcanal,
            $row->estado,
            $row->mes_7 == 0 ? '0' : (float) $row->mes_7,
            $row->mes_6 == 0 ? '0' : (float) $row->mes_6,
            $row->mes_5 == 0 ? '0' : (float) $row->mes_5,
            $row->mes_4 == 0 ? '0' : (float) $row->mes_4,
            $row->mes_3 == 0 ? '0' : (float) $row->mes_3,
            $row->mes_2 == 0 ? '0' : (float) $row->mes_2,
            $row->mes_1 == 0 ? '0' : (float) $row->mes_1,
            $row->proyeccion ? (float) $row->proyeccion : '0',
            $row->total ? (float) $row->total : '0',
            $row->meses_compra ? (float) $row->meses_compra : '0',
            $row->negocio,
            $row->celular,
            $row->direccion,
            $row->distrito,
            $row->ruta,
            $row->zona,
            $row->sector,
            $row->gps_x,
            $row->gps_y,
            $row->forma_pago,
            $row->precio_cdg_azul_5kg,
            $row->precio_cdg_azul_10kg,
            $row->precio_cdg_azul_15kg,
            $row->precio_cdg_azul_45kg,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                // Color de pestaña
                $sheet->getTabColor()->setRGB('FF1212');

                // Congelar encabezado
                $sheet->freezePane('A2');

                // Estilo base encabezados
                $headerStyle = [
                    'font' => [
                        'bold'  => false,
                        'size'  => 9,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                        'wrapText'   => true,
                    ],
                ];

                // Colores por grupo
                $headers = [
                    'A1:E1'  => '010645',
                    'F1:L1'  => 'FCC02D',
                    'M1:O1'  => '703000',
                    'P1:Y1'  => '0255BD',
                    'Z1:AC1' => '00A321',
                ];

                foreach ($headers as $range => $color) {
                    $style = $headerStyle;
                    $style['fill'] = [
                        'fillType' => Fill::FILL_SOLID,
                        'color'    => ['rgb' => $color],
                    ];
                    $sheet->getStyle($range)->applyFromArray($style);
                }

                // Fuente general
                $sheet->getStyle("A1:AC{$lastRow}")->applyFromArray([
                    'font' => [
                        'name' => 'Calibri',
                        'size' => 10,
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Bordes para toda la tabla
                $sheet->getStyle("A1:AC{$lastRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                // Ajustar texto
                $sheet->getStyle("A1:AC{$lastRow}")
                    ->getAlignment()
                    ->setWrapText(true);

                // Alineaciones
                $sheet->getStyle("A:A")->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle("C:E")->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle("N:O")->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle("W:X")->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle("B:B")->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->getStyle("P:V")->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT);

                // Formato numérico para ventas
                $sheet->getStyle("F2:N{$lastRow}")
                    ->getNumberFormat()
                    ->setFormatCode('#,##0.0');

                // Total
                $sheet->getStyle("O2:O{$lastRow}")
                    ->getNumberFormat()
                    ->setFormatCode('#,##0');

                // Precios
                $sheet->getStyle("Z2:AC{$lastRow}")
                    ->getNumberFormat()
                    ->setFormatCode('#,##0.00');

                // Altura encabezado
                $sheet->getRowDimension(1)->setRowHeight(25);

                // Altura automática filas de datos
                for ($i = 2; $i <= $lastRow; $i++) {
                    $sheet->getRowDimension($i)->setRowHeight(-1);
                }

                // Autoajustar TODAS las columnas (A–AB)
                foreach (range('A', 'Z') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
                $sheet->getColumnDimension('AA')->setAutoSize(true);
                $sheet->getColumnDimension('AB')->setAutoSize(true);
                $sheet->getColumnDimension('AC')->setAutoSize(true);

                // Zoom inicial
                $sheet->getSheetView()->setZoomScale(85);
            },
        ];
    }
}
