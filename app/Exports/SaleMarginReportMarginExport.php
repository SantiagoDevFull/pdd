<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class SaleMarginReportMarginExport implements FromCollection, WithEvents, WithColumnFormatting, WithTitle
{

    protected $dias_totales;
    protected $dias_avance;
    protected $quedan;
    protected $months;

    public function __construct($dias_totales, $dias_avance, $quedan, $months)
    {
        $this->dias_totales = $dias_totales;
        $this->dias_avance = $dias_avance;
        $this->quedan = $quedan;
        $this->months = $months;
    }

    public function collection()
    {
        return collect([]);
    }

    public function title(): string
    {
        return 'MARGEN';
    }

    public function columnFormats(): array
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->getTabColor()->setRGB('FF2B00');
                $sheet->setShowGridLines(false);

                // =============================================
                // COLUMN WIDTHS
                // =============================================
                $sheet->getColumnDimension('A')->setWidth(3);
                $sheet->getColumnDimension('B')->setWidth(32);
                $sheet->getColumnDimension('C')->setWidth(14);
                $sheet->getColumnDimension('D')->setWidth(5);
                $sheet->getColumnDimension('E')->setWidth(32);
                $sheet->getColumnDimension('F')->setWidth(14);
                $sheet->getColumnDimension('G')->setWidth(14);
                $sheet->getColumnDimension('H')->setWidth(12);
                $sheet->getColumnDimension('I')->setWidth(5);
                $sheet->getColumnDimension('J')->setWidth(12);
                $sheet->getColumnDimension('K')->setWidth(12);
                $sheet->getColumnDimension('L')->setWidth(12);
                $sheet->getColumnDimension('M')->setWidth(12);
                $sheet->getColumnDimension('N')->setWidth(12);

                // =============================================
                // COLORS
                // =============================================
                $darkBlue  = '1F3864'; // dark navy
                $orange    = 'C55A11'; // orange
                $lightCream = 'FFF2CC'; // light yellow/cream
                $red       = 'FF0000';
                $white     = 'FFFFFF';
                $darkBlue2 = '203864';

                // =============================================
                // HELPER CLOSURES
                // =============================================
                $applyBg = function (string $range, string $hex) use ($sheet) {
                    $sheet->getStyle($range)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB($hex);
                };

                $applyFont = function (string $range, string $hex, bool $bold = false, int $size = 10) use ($sheet) {
                    $font = $sheet->getStyle($range)->getFont();
                    $font->getColor()->setRGB($hex);
                    $font->setBold($bold);
                    $font->setSize($size);
                };

                $applyAlign = function (string $range, string $h = Alignment::HORIZONTAL_LEFT, string $v = Alignment::VERTICAL_CENTER) use ($sheet) {
                    $sheet->getStyle($range)->getAlignment()
                        ->setHorizontal($h)
                        ->setVertical($v)
                        ->setWrapText(true);
                };

                $thinBorder = function (string $range) use ($sheet) {
                    $sheet->getStyle($range)->getBorders()->getAllBorders()
                        ->setBorderStyle(Border::BORDER_THIN)
                        ->getColor()->setRGB('BFBFBF');
                };

                // =============================================
                // ROW HEIGHTS
                // =============================================
                foreach (range(2, 40) as $r) {
                    $sheet->getRowDimension($r)->setRowHeight(18);
                }

                // =============================================
                // ===== LEFT PANEL: PROYECCION DE MARGEN =====
                // =============================================

                // Title
                $sheet->mergeCells('B2:C2');
                $sheet->setCellValue('B2', 'Proyeccion de Margen');
                $applyBg('B2:C2', $darkBlue);
                $applyFont('B2:C2', $white, true, 11);
                $applyAlign('B2:C2', Alignment::HORIZONTAL_CENTER);

                // Al Corte row
                $sheet->setCellValue('B3', 'Al Corte');
                $sheet->setCellValue('C3', '18/05/2026');
                $applyBg('B3:C3', $darkBlue);
                $applyFont('B3:C3', $white, false, 9);
                $applyAlign('C3', Alignment::HORIZONTAL_RIGHT);

                // Info rows
                $infoRows = [
                    4  => ['Dias Habiles',   $this->dias_totales],
                    5  => ['Dias Ejecutados', $this->dias_avance],
                    6  => ['Dias pendientes', $this->quedan],
                ];
                foreach ($infoRows as $row => [$label, $val]) {
                    $sheet->setCellValue("B{$row}", $label);
                    $sheet->setCellValue("C{$row}", $val);
                    $applyBg("B{$row}:C{$row}", $white);
                    $applyFont("B{$row}", '0');
                    $applyFont("C{$row}", '0');
                    $applyAlign("C{$row}", Alignment::HORIZONTAL_RIGHT);
                    $thinBorder("B{$row}:C{$row}");
                }

                // Margen Diferencial Finanzas (orange)
                $sheet->mergeCells('B8:C8');
                $sheet->setCellValue('B8', 'Margen Diferencial Finanzas');

                $hojaVenta = $event->sheet->getParent()->getSheetByName('VENTA DIARIA');

                $valorMargen = 0;

                // Recorrer todas las filas
                foreach ($hojaVenta->getRowIterator() as $row) {

                    $rowIndex = $row->getRowIndex();

                    // Buscar en todas las columnas de la fila
                    foreach ($hojaVenta->getColumnIterator() as $column) {

                        $col = $column->getColumnIndex();
                        $celda = $hojaVenta->getCell($col . $rowIndex)->getValue();

                        if (trim($celda) === 'MARGEN DIFERENCIAL') {

                            // Última columna con datos de la fila
                            $ultimaColumna = $hojaVenta->getHighestDataColumn($rowIndex);

                            // Obtener valor de la última columna
                            $valorMargen = $hojaVenta->getCell($ultimaColumna . $rowIndex)->getCalculatedValue();

                            break 2;
                        }
                    }
                }

                // Asignar valor a C8
                $sheet->setCellValue('C8', $valorMargen);

                $sheet->mergeCells('B8:B8');
                $sheet->unmergeCells('B8:C8');

                $applyBg('B8:C8', $orange);
                $applyFont('B8:C8', $white, true);
                $applyAlign('C8', Alignment::HORIZONTAL_RIGHT);

                // Margen Diferencial Comercial (cream)
                $sheet->setCellValue('B9', 'Margen Diferencial Comercial');
                $sheet->setCellValue('C9', 0);
                $applyBg('B9:C9', $lightCream);
                $applyFont('B9', '0', true);
                $applyFont('C9', '0', true);
                $applyAlign('C9', Alignment::HORIZONTAL_RIGHT);
                $thinBorder('B9:C9');

                // Margen por ejecutar al mes Finanzas (orange)
                $sheet->setCellValue('B10', 'Margen por ejecutar al mes Finanzas');
                $sheet->setCellValue('C10', '=C8/C5*C6');
                $applyBg('B10:C10', $orange);
                $applyFont('B10:C10', $white, true);
                $applyAlign('C10', Alignment::HORIZONTAL_RIGHT);

                // Margen total Finanzas (orange)
                $sheet->setCellValue('B12', 'Margen total Finanzas');
                $sheet->setCellValue('C12', '=C8+C10');
                $applyBg('B12:C12', $orange);
                $applyFont('B12:C12', $white, true);
                $applyAlign('C12', Alignment::HORIZONTAL_RIGHT);

                // Margen total Comercial (cream)
                $sheet->setCellValue('B13', 'Margen total Comercial');
                $sheet->setCellValue('C13', 0);
                $applyBg('B13:C13', $lightCream);
                $applyFont('B13', '0', true);
                $applyFont('C13', '0', true);
                $applyAlign('C13', Alignment::HORIZONTAL_RIGHT);
                $thinBorder('B13:C13');

                // Excedente ejecutado al mes
                $sheet->setCellValue('B15', 'Excedente ejecutado al mes');
                $sheet->setCellValue('C15', 0);
                $applyBg('B15:C15', $white);
                $applyFont('B15', '0');
                $applyFont('C15', '0');
                $applyAlign('C15', Alignment::HORIZONTAL_RIGHT);
                $thinBorder('B15:C15');

                // Excedente por ejecutar al mes
                $sheet->setCellValue('B17', 'Excedente por ejecutar al mes');
                $sheet->setCellValue('C17', 0);
                $applyBg('B17:C17', $white);
                $applyFont('B17', '0');
                $applyFont('C17', '0');
                $applyAlign('C17', Alignment::HORIZONTAL_RIGHT);
                $thinBorder('B17:C17');

                // Exced total (red)
                $sheet->setCellValue('B19', 'Excedente total');
                $sheet->setCellValue('C19', '=C15+C17');
                $applyBg('B19:C19', $red);
                $applyFont('B19:C19', $white, true);
                $applyAlign('C19', Alignment::HORIZONTAL_RIGHT);

                // Margen total + excedente Finanzas (orange)
                $sheet->setCellValue('B21', 'Margen total + excedente Finanzas');
                $sheet->setCellValue('C21', '=C12+C19');
                $applyBg('B21:C21', $orange);
                $applyFont('B21:C21', $white, true);
                $applyAlign('C21', Alignment::HORIZONTAL_RIGHT);

                // Margen total + excedente Comercial (cream)
                $sheet->setCellValue('B22', 'Margen total + excedente  Comercial');
                $sheet->setCellValue('C22', '=C13+C19');
                $applyBg('B22:C22', $lightCream);
                $applyFont('B22', '0', true);
                $applyFont('C22', '0', true);
                $applyAlign('C22', Alignment::HORIZONTAL_RIGHT);
                $thinBorder('B22:C22');

                // =============================================
                // ===== CUADRO DE EXCEDENTE GLP (bottom left)
                // =============================================
                $sheet->mergeCells('B25:C25');
                $sheet->setCellValue('B25', 'CUADRO DE EXCEDENTE GLP');
                $applyBg('B25:C25', $darkBlue);
                $applyFont('B25:C25', $white, true);
                $applyAlign('B25', Alignment::HORIZONTAL_CENTER);

                $glpRows = [
                    26 => ['Excedente proyectado - TM', 0],
                    27 => ['Excedente ejecutado - TM',  0],
                    28 => ['Diferencia por ejecutar - TM', 0],
                ];
                foreach ($glpRows as $row => [$label, $val]) {
                    $sheet->setCellValue("B{$row}", $label);
                    $sheet->setCellValue("C{$row}", $val);
                    $applyBg("B{$row}:C{$row}", $white);
                    $applyFont("B{$row}", '0');
                    $applyFont("C{$row}", '0');
                    $applyAlign("C{$row}", Alignment::HORIZONTAL_RIGHT);
                    $thinBorder("B{$row}:C{$row}");
                }

                // =============================================
                // ======== CENTER PANEL: May-26 TABLE =========
                // =============================================

                // May-26 header
                $sheet->mergeCells('E2:H2');
                $sheet->setCellValue('E2', 'May-26');
                $applyBg('E2:H2', $darkBlue);
                $applyFont('E2:H2', $white, true, 12);
                $applyAlign('E2', Alignment::HORIZONTAL_CENTER);

                // Sub-headers
                $sheet->setCellValue('F3', 'REAL');
                $sheet->setCellValue('G3', 'PROYECTADO');
                $sheet->setCellValue('H3', 'VARIACION');
                $applyBg('E3:H3', $darkBlue);
                $applyFont('E3:H3', $white, true);
                $applyAlign('F3', Alignment::HORIZONTAL_CENTER);
                $applyAlign('G3', Alignment::HORIZONTAL_CENTER);
                $applyAlign('H3', Alignment::HORIZONTAL_CENTER);

                // Total Margen (orange)
                $sheet->mergeCells('E4:E4');
                $sheet->setCellValue('E4', 'Total Margen');
                $sheet->setCellValue('F4', '=F5+F6');
                $sheet->setCellValue('G4', '=G5+G6');
                $sheet->setCellValue('H4', '=H5+H6');
                $applyBg('E4:H4', $orange);
                $applyFont('E4:H4', $white, true);
                $applyAlign('H4', Alignment::HORIZONTAL_CENTER);

                // Margen bruto
                $sheet->setCellValue('E5', 'Margen bruto');
                $sheet->setCellValue('F5', '=C8');
                $sheet->setCellValue('G5', '=C13');
                $sheet->setCellValue('H5', '=IF(G5=0,0,F5/G5)');
                $applyBg('E5:H5', $white);
                $applyFont('E5', '0');
                $applyFont('F5', '0');
                $applyFont('G5', '0');
                $applyFont('H5', '0');
                $thinBorder('E5:H5');

                // Excedente
                $sheet->setCellValue('E6', 'Excedente');
                $sheet->setCellValue('F6', 0);
                $sheet->setCellValue('G6', 0);
                $sheet->setCellValue('H6', '=IF(G6=0,0,F6/G6)');
                $applyBg('E6:H6', $white);
                $applyFont('E6', '0');
                $applyFont('F6', '0');
                $applyFont('G6', '0');
                $applyFont('H6', '0');
                $thinBorder('E6:H6');

                // Total egresos operativos (dark blue)
                $sheet->setCellValue('E7', 'Total egresos operativos');
                $sheet->setCellValue('F7', 0);
                $sheet->setCellValue('G7', 0);
                $sheet->setCellValue('H7', 0);
                $applyBg('E7:H7', $darkBlue);
                $applyFont('E7:H7', $white, true);

                // Egresos por pago de provisiones (orange)
                $sheet->setCellValue('E9', 'Egresos por pago de provisiones');
                $sheet->setCellValue('F9', 0);
                $sheet->setCellValue('G9', 0);
                $sheet->setCellValue('H9', 0);
                $applyBg('E9:H9', $darkBlue);
                $applyFont('E9:H9', $white, true);

                // Margen Operacional (orange)
                $sheet->setCellValue('E11', 'Margen Operacional');
                $sheet->setCellValue('F11', '=F4-F7-F9');
                $sheet->setCellValue('G11', '=G4-G7-G9');
                $sheet->setCellValue('H11', '=IF(G11=0,0,F11/G1)');
                $applyBg('E11:H11', $orange);
                $applyFont('E11:H11', $white, true);

                // Total otros ingresos (dark blue)
                $sheet->setCellValue('E13', 'Total otros ingresos');
                $sheet->setCellValue('F13', 0);
                $sheet->setCellValue('G13', 0);
                $sheet->setCellValue('H13', 0);
                $applyBg('E13:H13', $darkBlue);
                $applyFont('E13:H13', $white, true);

                // Sub-rows otros ingresos
                $sheet->setCellValue('E14', 'Ingresos por pago de deuda Blue Line');
                $sheet->setCellValue('F14', 0);
                $sheet->setCellValue('G14', 0);
                $sheet->setCellValue('H14', 0);
                $applyBg('E14:H14', $white);
                $applyFont('E14', 0);
                $applyFont('F14', 0);
                $applyFont('G14', 0);
                $applyFont('H14', 0);
                $thinBorder('E14:H14');

                $sheet->setCellValue('E15', 'Ingresos por pago de deuda Punto Motor');
                $sheet->setCellValue('F15', 0);
                $sheet->setCellValue('G15', 0);
                $sheet->setCellValue('H15', 0);
                $applyBg('E15:H15', $white);
                $applyFont('E15', 0);
                $applyFont('F15', 0);
                $applyFont('G15', 0);
                $applyFont('H15', 0);
                $thinBorder('E15:H15');

                $sheet->setCellValue('E16', 'Otros ingresos diversos');
                $sheet->setCellValue('F16', 0);
                $sheet->setCellValue('G16', 0);
                $sheet->setCellValue('H16', 0);
                $applyBg('E16:H16', $white);
                $applyFont('E16', 0);
                $applyFont('F16', 0);
                $applyFont('G16', 0);
                $applyFont('H16', 0);
                $thinBorder('E16:H16');

                // Margen Operacional + Otros ingresos (orange)
                $sheet->setCellValue('E17', 'Margen Operacional + Otros ingresos');
                $sheet->setCellValue('F17', 0);
                $sheet->setCellValue('G17', 0);
                $sheet->setCellValue('H17', 0);
                $applyBg('E17:H17', $orange);
                $applyFont('E17:H17', $white, true);

                // Egresos despues del EBITDA (dark blue)
                $sheet->setCellValue('E19', 'Egresos  despues del EBITDA');
                $sheet->setCellValue('F19', 0);
                $sheet->setCellValue('G19', 0);
                $sheet->setCellValue('H19', 0);
                $applyBg('E19:H19', $darkBlue);
                $applyFont('E19:H19', $white, true);

                // Margen despues del EBITDA (orange)
                $sheet->setCellValue('E21', 'Margen despues del EBITDA');
                $sheet->setCellValue('F21', 0);
                $sheet->setCellValue('G21', 0);
                $sheet->setCellValue('H21', 0);
                $applyBg('E21:H21', $orange);
                $applyFont('E21:H21', $white, true);

                // =============================================
                // ===== RIGHT PANEL: PAGOS REALIZADOS ========
                // =============================================

                // Header
                $sheet->mergeCells('J2:N2');
                $sheet->setCellValue('J2', 'PAGOS REALIZADOS');
                $applyBg('J2:N2', $orange);
                $applyFont('J2:N2', $white, true, 11);

                // Month headers
                $cells = ['J3', 'K3', 'L3', 'M3', 'N3'];

                foreach ($cells as $index => $cell) {
                    $sheet->setCellValue($cell, $this->months[$index]);
                    $applyBg($cell, $darkBlue);
                    $applyFont($cell, $white, true);
                    $applyAlign($cell, Alignment::HORIZONTAL_CENTER);
                }

                // Values row
                $pagos = ['J7' => '=F7-K7-L7-M7-N7', 'K7' => 0, 'L7' => 0, 'M7' => 0, 'N7' => 0];
                foreach ($pagos as $cell => $val) {
                    $sheet->setCellValue($cell, $val);
                    $applyBg($cell, $white);
                    $applyFont($cell, $white, true);
                    $applyFont($cell, 0);
                    $applyAlign($cell, Alignment::HORIZONTAL_RIGHT);
                    $thinBorder($cell);
                }

                // =============================================
                // NUMBER FORMAT for numeric cells
                // =============================================
                $numFormat = '#,##0';
                $numericCells = [
                    'C4',
                    'C8',
                    'C9',
                    'C10',
                    'C12',
                    'C13',
                    'C15',
                    'C17',
                    'C19',
                    'C21',
                    'C22',
                    'C26',
                    'C27',
                    'C28',
                    'F4',
                    'F5',
                    'F6',
                    'F7',
                    'F9',
                    'F11',
                    'F13',
                    'F16',
                    'F17',
                    'F19',
                    'F21',
                    'G4',
                    'G5',
                    'G6',
                    'G7',
                    'G9',
                    'G11',
                    'G13',
                    'G14',
                    'G16',
                    'G17',
                    'G19',
                    'G21',
                    'J7',
                    'K7',
                    'L7',
                    'M7',
                    'N7',
                ];
                foreach ($numericCells as $cell) {
                    $sheet->getStyle($cell)->getNumberFormat()->setFormatCode($numFormat);
                }

                // Percentage format
                $pctCells = ['H4','H5','H6', 'H7', 'H9','H11', 'H13','H14','H15','H16', 'H17', 'H19','H21'];
                foreach ($pctCells as $cell) {
                    $sheet->getStyle($cell)->getNumberFormat()->setFormatCode('0%');
                }
            },
        ];
    }
}
