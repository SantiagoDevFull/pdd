<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class SaleMarginReportMonthExport implements FromCollection, WithEvents, WithColumnFormatting, WithTitle
{
    protected $tmCollection;
    protected $solesCollection;
    protected $precios;
    protected $kgSoles;
    protected $tm_ruta;
    protected $resultado;

    public function __construct($total_venta_tm, $total_venta_soles, $precios, $kg_soles, $tm_ruta, $resultado)
    {
        $this->tmCollection    = $total_venta_tm instanceof Collection ? $total_venta_tm : collect($total_venta_tm);
        $this->solesCollection = $total_venta_soles instanceof Collection ? $total_venta_soles : collect($total_venta_soles);
        $this->precios         = $precios instanceof Collection ? $precios : collect($precios);
        $this->kgSoles         = is_array($kg_soles)
            ? (float)($kg_soles['kg_soles'] ?? 0.0)
            : (float)($kg_soles->kg_soles ?? 0.0);
        $this->tm_ruta         = $tm_ruta instanceof Collection ? $tm_ruta : collect($tm_ruta);
        $this->resultado       = $resultado instanceof Collection ? $resultado : collect($resultado);
    }

    public function collection()
    {
        return collect([]);
    }

    public function title(): string
    {
        return 'VENTA DIARIA';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getTabColor()->setRGB('05294B');
                $sheet->setShowGridLines(false);

                $table1 = $this->buildPivotStructure($this->tmCollection, 'tm', 'TONELADAS');
                $table2 = $this->buildPivotStructure($this->solesCollection, 'total', 'SOLES');

                $rowAfterTable1 = $this->renderTable($sheet, $table1, 1);
                $startRowTable2 = $rowAfterTable1 + 2;
                $rowAfterTable2 = $this->renderTable($sheet, $table2, $startRowTable2);

                $startRowTable3 = $rowAfterTable2 + 2;
                $table3 = $this->buildPivotStructure($this->tmCollection, 'tm', '');

                $grandTotalTm      = array_sum($table1['overallTotals']);
                $grandTotalSoles   = array_sum($table2['overallTotals']);
                $weightedPriceAcum = ($grandTotalTm > 0) ? ($grandTotalSoles / $grandTotalTm) : 0.0;

                $envasadoTmByDate    = [];
                $envasadoSolesByDate = [];
                $granelTmByDate      = [];
                $granelSolesByDate   = [];

                foreach ($table1['groups'] as $g) {
                    if ($g['label'] === 'ENVASADO') $envasadoTmByDate    = $g['totals'];
                    if ($g['label'] === 'GRANEL')   $granelTmByDate      = $g['totals'];
                }
                foreach ($table2['groups'] as $g) {
                    if ($g['label'] === 'ENVASADO') $envasadoSolesByDate = $g['totals'];
                    if ($g['label'] === 'GRANEL')   $granelSolesByDate   = $g['totals'];
                }

                $precioR19 = 0.0;
                $precioR26 = 0.0;
                foreach ($this->precios as $p) {
                    $routeId = is_array($p) ? ($p['route_id'] ?? null) : ($p->route_id ?? null);
                    $precio  = is_array($p) ? ($p['precio_promedio'] ?? 0) : ($p->precio_promedio ?? 0);
                    if ((int)$routeId === 19) $precioR19 = (float)$precio;
                    if ((int)$routeId === 26) $precioR26 = (float)$precio;
                }

                $tmRuta19 = 0.0;
                $tmRuta26 = 0.0;
                foreach ($this->tm_ruta as $r) {
                    $routeId = is_array($r) ? ($r['route_id'] ?? null) : ($r->route_id ?? null);
                    $tmRuta  = is_array($r) ? ($r['tm_ruta'] ?? 0) : ($r->tm_ruta ?? 0);
                    if ((int)$routeId === 19) $tmRuta19 = (float)$tmRuta;
                    if ((int)$routeId === 26) $tmRuta26 = (float)$tmRuta;
                }

                $this->renderHeadersOnly(
                    $sheet,
                    $table3['dates'],
                    $startRowTable3,
                    $weightedPriceAcum,
                    $envasadoSolesByDate,
                    $envasadoTmByDate,
                    $granelSolesByDate,
                    $granelTmByDate,
                    $precioR19,
                    $precioR26,
                    $table1['overallTotals'],
                    $table2['overallTotals'],
                    $this->kgSoles,
                    $tmRuta19,
                    $tmRuta26
                );

                // Tabla 4: STOCK GLP
                $startRowTable4 = $startRowTable3 + 26;
                $this->renderTableStock($sheet, $table1['dates'], $startRowTable4, $table1['overallTotals']);

                $sheet->getColumnDimension('A')->setAutoSize(true);
            },
        ];
    }

    protected function buildPivotStructure(Collection $collection, string $valueField, string $labelSuffix): array
    {
        $datesSet    = [];
        $channelsSet = [];
        $pivot       = [];
        $channelToBu = [];

        foreach ($collection as $item) {
            $dateRaw  = is_array($item) ? ($item['sales_sale_date'] ?? null) : ($item->sales_sale_date ?? null);
            $channel  = is_array($item) ? ($item['client_channels_name'] ?? '') : ($item->client_channels_name ?? '');
            $valueRaw = is_array($item) ? ($item[$valueField] ?? null) : ($item->{$valueField} ?? null);
            $bu       = is_array($item) ? ($item['business_unit_id'] ?? null) : ($item->business_unit_id ?? null);

            if ($valueRaw === null || $valueRaw === '') {
                $value = 0.0;
            } else {
                $candidate = is_numeric($valueRaw) ? $valueRaw : str_replace(',', '.', (string)$valueRaw);
                $value = is_numeric($candidate) ? (float)$candidate : 0.0;
            }

            if (!$dateRaw) continue;
            try {
                $date = Carbon::parse($dateRaw)->format('Y-m-d');
            } catch (\Exception $e) {
                continue;
            }

            $channel = ($channel === null || $channel === '') ? '(SIN SUBCANAL)' : (string)$channel;

            $datesSet[$date]       = true;
            $channelsSet[$channel] = true;

            if (!isset($channelToBu[$channel]) && $bu !== null) $channelToBu[$channel] = (int)$bu;
            if (!isset($pivot[$channel]))        $pivot[$channel] = [];
            if (!isset($pivot[$channel][$date])) $pivot[$channel][$date] = 0.0;

            $pivot[$channel][$date] += $value;
        }

        $dates    = array_keys($datesSet);
        sort($dates);
        $channels = array_keys($channelsSet);

        $groupA = [];
        $groupB = [];
        $groupOther = [];
        foreach ($channels as $ch) {
            $chBu = $channelToBu[$ch] ?? null;
            if ($chBu === 1 || $chBu === 3)  $groupA[]     = $ch;
            elseif ($chBu === 2)              $groupB[]     = $ch;
            else                              $groupOther[] = $ch;
        }
        sort($groupA,     SORT_STRING | SORT_FLAG_CASE);
        sort($groupB,     SORT_STRING | SORT_FLAG_CASE);
        sort($groupOther, SORT_STRING | SORT_FLAG_CASE);

        $overallTotals = array_fill_keys($dates, 0.0);

        $buildGroup = function ($channelsList, $label) use (&$pivot, $dates, &$overallTotals) {
            $totals = array_fill_keys($dates, 0.0);
            foreach ($channelsList as $ch) {
                foreach ($dates as $d) {
                    $val = $pivot[$ch][$d] ?? 0.0;
                    $totals[$d] += $val;
                }
            }
            foreach ($dates as $d) $overallTotals[$d] += $totals[$d];
            return ['label' => $label, 'channels' => $channelsList, 'totals' => $totals];
        };

        $groups = [];
        if (!empty($groupA))     $groups[] = $buildGroup($groupA,     'ENVASADO');
        if (!empty($groupB))     $groups[] = $buildGroup($groupB,     'GRANEL');
        if (!empty($groupOther)) $groups[] = $buildGroup($groupOther, 'TOTAL OTROS');

        return [
            'dates'         => $dates,
            'groups'        => $groups,
            'pivot'         => $pivot,
            'overallTotals' => $overallTotals,
            'labelSuffix'   => $labelSuffix,
        ];
    }

    protected function renderHeadersOnly(
        $sheet,
        array $dates,
        int $startRow,
        float $weightedPriceAcum,
        array $envasadoSolesByDate = [],
        array $envasadoTmByDate    = [],
        array $granelSolesByDate   = [],
        array $granelTmByDate      = [],
        float $precioR19           = 0.0,
        float $precioR26           = 0.0,
        array $totalTmByDate       = [],
        array $totalSolesByDate    = [],
        float $kgSoles             = 0.0,
        float $tmRuta19            = 0.0,
        float $tmRuta26            = 0.0
    ) {
        $colStartIndex = 2;
        $numDates      = count($dates);
        $accColIndex   = $colStartIndex + $numDates;
        $accColLetter  = Coordinate::stringFromColumnIndex($accColIndex);

        $styleHeader = [
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '05294B']],
        ];

        $styleYellow = [
            'font'      => ['bold' => true],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFF00']],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ];

        $styleWhite = [
            'font'      => ['bold' => true],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFFFF']],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ];

        // Helper: solo ACUMULADO
        $writeAccumOnlyRow = function (int $row, string $label, float $acumValue) use (
            $sheet,
            $accColLetter,
            $styleWhite
        ) {
            $sheet->setCellValue("A{$row}", $label);
            $sheet->setCellValue("{$accColLetter}{$row}", $acumValue);
            $sheet->getStyle("A{$row}:{$accColLetter}{$row}")->applyFromArray($styleWhite);
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("{$accColLetter}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("{$accColLetter}{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
        };

        // Helper: mismo valor todos los días + acumulado (blanco)
        $writeAllDaysRowWhite = function (int $row, string $label, float $value) use (
            $sheet,
            $dates,
            $colStartIndex,
            $accColLetter,
            $styleWhite
        ) {
            $sheet->setCellValue("A{$row}", $label);
            $colIndex = $colStartIndex;
            foreach ($dates as $date) {
                $cell = Coordinate::stringFromColumnIndex($colIndex) . $row;
                $sheet->setCellValue($cell, $value);
                $sheet->getStyle($cell)->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $colIndex++;
            }
            $sheet->setCellValue("{$accColLetter}{$row}", $value);
            $sheet->getStyle("{$accColLetter}{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle("{$accColLetter}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("A{$row}:{$accColLetter}{$row}")->applyFromArray($styleWhite);
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        };

        // Helper: diferencial por día (precio ponderado grupo - kgSoles)
        $writeDiffRow = function (
            int $row,
            string $label,
            array $solesByDate,
            array $tmByDate,
            float $kgSolesVal
        ) use ($sheet, $dates, $colStartIndex, $accColLetter, $styleWhite) {
            $sheet->setCellValue("A{$row}", $label);
            $colIndex = $colStartIndex;
            $totalSol = 0.0;
            $totalTm  = 0.0;
            foreach ($dates as $date) {
                $soles  = (float)($solesByDate[$date] ?? 0.0);
                $tm     = (float)($tmByDate[$date] ?? 0.0);
                $precio = ($tm > 0) ? ($soles / $tm) : 0.0;
                $val    = $precio - $kgSolesVal;
                $cell   = Coordinate::stringFromColumnIndex($colIndex) . $row;
                $sheet->setCellValue($cell, $val);
                $sheet->getStyle($cell)->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $totalSol += $soles;
                $totalTm  += $tm;
                $colIndex++;
            }
            $precioAcum = ($totalTm > 0) ? ($totalSol / $totalTm) : 0.0;
            $acumVal    = $precioAcum - $kgSolesVal;
            $sheet->setCellValue("{$accColLetter}{$row}", $acumVal);
            $sheet->getStyle("{$accColLetter}{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle("{$accColLetter}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("A{$row}:{$accColLetter}{$row}")->applyFromArray($styleWhite);
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        };

        // Helper: TM * Diferencial por día + acumulado
        $writeTmDiffRow = function (
            int $row,
            string $label,
            array $solesByDate,
            array $tmByDate,
            float $kgSolesVal
        ) use ($sheet, $dates, $colStartIndex, $accColLetter, $styleWhite): array {
            $sheet->setCellValue("A{$row}", $label);
            $colIndex  = $colStartIndex;
            $totalSol  = 0.0;
            $totalTm   = 0.0;
            $valByDate = [];

            foreach ($dates as $date) {
                $soles  = (float)($solesByDate[$date] ?? 0.0);
                $tm     = (float)($tmByDate[$date] ?? 0.0);
                $precio = ($tm > 0) ? ($soles / $tm) : 0.0;
                $diff   = $precio - $kgSolesVal;
                $val    = $tm * $diff;
                $valByDate[$date] = $val;
                $cell = Coordinate::stringFromColumnIndex($colIndex) . $row;
                $sheet->setCellValue($cell, $val);
                $sheet->getStyle($cell)->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $totalSol += $soles;
                $totalTm  += $tm;
                $colIndex++;
            }

            $precioAcum = ($totalTm > 0) ? ($totalSol / $totalTm) : 0.0;
            $diffAcum   = $precioAcum - $kgSolesVal;
            $acumVal    = $totalTm * $diffAcum;
            $sheet->setCellValue("{$accColLetter}{$row}", $acumVal);
            $sheet->getStyle("{$accColLetter}{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle("{$accColLetter}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("A{$row}:{$accColLetter}{$row}")->applyFromArray($styleWhite);
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            return ['byDate' => $valByDate, 'acum' => $acumVal];
        };

        // ── ENCABEZADOS ──────────────────────────────────────────────

        // 1) Título principal
        $sheet->mergeCells("A{$startRow}:A" . ($startRow + 1));
        $sheet->setCellValue("A{$startRow}", 'REPORTE DE VENTAS DIARIO');
        $sheet->getStyle("A{$startRow}:A" . ($startRow + 1))->applyFromArray(array_merge($styleHeader, [
            'font'      => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
        ]));

        // 2) Columna ACUMULADO
        $sheet->mergeCells("{$accColLetter}{$startRow}:{$accColLetter}" . ($startRow + 1));
        $sheet->setCellValue("{$accColLetter}{$startRow}", 'ACUMULADO');
        $sheet->getStyle("{$accColLetter}{$startRow}:{$accColLetter}" . ($startRow + 1))->applyFromArray($styleHeader);

        // 3) Nombres de día
        $colIndex = $colStartIndex;
        foreach ($dates as $date) {
            $dayNameUpper = mb_strtoupper(Carbon::parse($date)->locale('es')->translatedFormat('l'), 'UTF-8');
            $cell = Coordinate::stringFromColumnIndex($colIndex) . $startRow;
            $sheet->setCellValue($cell, $dayNameUpper);
            $sheet->getStyle($cell)->applyFromArray($styleHeader);
            $colIndex++;
        }

        // 4) Fechas
        $colIndex = $colStartIndex;
        foreach ($dates as $date) {
            $cell = Coordinate::stringFromColumnIndex($colIndex) . ($startRow + 1);
            $sheet->setCellValue($cell, $date);
            $sheet->getStyle($cell)->applyFromArray($styleHeader);
            $colIndex++;
        }

        // 5) PRECIO PONDERADO VENTA
        $yellowRow = $startRow + 2;
        $sheet->setCellValue("A{$yellowRow}", 'PRECIO PONDERADO VENTA');
        $sheet->setCellValue("{$accColLetter}{$yellowRow}", (float)$weightedPriceAcum);
        $sheet->getStyle("A{$yellowRow}:{$accColLetter}{$yellowRow}")->applyFromArray(array_merge($styleYellow, [
            'borders' => ['top' => ['borderStyle' => Border::BORDER_THIN]],
        ]));
        $sheet->getStyle("A{$yellowRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("{$accColLetter}{$yellowRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("{$accColLetter}{$yellowRow}")->getNumberFormat()->setFormatCode('#,##0.00');

        // 6) ENVASADO
        $envasadoRow   = $startRow + 3;
        $totalEnvSoles = 0.0;
        $totalEnvTm    = 0.0;
        $sheet->setCellValue("A{$envasadoRow}", 'ENVASADO');
        $colIndex = $colStartIndex;
        foreach ($dates as $date) {
            $soles = (float)($envasadoSolesByDate[$date] ?? 0.0);
            $tm    = (float)($envasadoTmByDate[$date] ?? 0.0);
            $val   = ($tm > 0) ? ($soles / $tm) : 0.0;
            $cell  = Coordinate::stringFromColumnIndex($colIndex) . $envasadoRow;
            $sheet->setCellValue($cell, $val);
            $sheet->getStyle($cell)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $totalEnvSoles += $soles;
            $totalEnvTm    += $tm;
            $colIndex++;
        }
        $envasadoAccum = ($totalEnvTm > 0) ? ($totalEnvSoles / $totalEnvTm) : 0.0;
        $sheet->setCellValue("{$accColLetter}{$envasadoRow}", $envasadoAccum);
        $sheet->getStyle("{$accColLetter}{$envasadoRow}")->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle("{$accColLetter}{$envasadoRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("A{$envasadoRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // 7) GRANEL
        $granelRow      = $startRow + 4;
        $totalGranSoles = 0.0;
        $totalGranTm    = 0.0;
        $sheet->setCellValue("A{$granelRow}", 'GRANEL');
        $colIndex = $colStartIndex;
        foreach ($dates as $date) {
            $soles = (float)($granelSolesByDate[$date] ?? 0.0);
            $tm    = (float)($granelTmByDate[$date] ?? 0.0);
            $val   = ($tm > 0) ? ($soles / $tm) : 0.0;
            $cell  = Coordinate::stringFromColumnIndex($colIndex) . $granelRow;
            $sheet->setCellValue($cell, $val);
            $sheet->getStyle($cell)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $totalGranSoles += $soles;
            $totalGranTm    += $tm;
            $colIndex++;
        }
        $granelAccum = ($totalGranTm > 0) ? ($totalGranSoles / $totalGranTm) : 0.0;
        $sheet->setCellValue("{$accColLetter}{$granelRow}", $granelAccum);
        $sheet->getStyle("{$accColLetter}{$granelRow}")->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle("{$accColLetter}{$granelRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("A{$granelRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // 8) PRECIO DE VENTA G R19
        $writeAccumOnlyRow($startRow + 5, 'PRECIO DE VENTA G R19', $precioR19);

        // 9) PRECIO DE VENTA G R26
        $writeAccumOnlyRow($startRow + 6, 'PRECIO DE VENTA G R26', $precioR26);

        // 10) Fila vacía ($startRow + 7)

        // 11) PRECIO PONDERADO DIA VTA
        $ppdRow   = $startRow + 8;
        $totalSol = 0.0;
        $totalTm  = 0.0;
        $sheet->setCellValue("A{$ppdRow}", 'PRECIO PONDERADO DIA VTA');
        $colIndex = $colStartIndex;
        foreach ($dates as $date) {
            $soles = (float)($totalSolesByDate[$date] ?? 0.0);
            $tm    = (float)($totalTmByDate[$date] ?? 0.0);
            $val   = ($tm > 0) ? ($soles / $tm) : 0.0;
            $cell  = Coordinate::stringFromColumnIndex($colIndex) . $ppdRow;
            $sheet->setCellValue($cell, $val);
            $sheet->getStyle($cell)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $totalSol += $soles;
            $totalTm  += $tm;
            $colIndex++;
        }
        $ppdAccum = ($totalTm > 0) ? ($totalSol / $totalTm) : 0.0;
        $sheet->setCellValue("{$accColLetter}{$ppdRow}", $ppdAccum);
        $sheet->getStyle("{$accColLetter}{$ppdRow}")->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle("{$accColLetter}{$ppdRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("A{$ppdRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // 12) Fila vacía ($startRow + 9)

        // 13) PRECIO PONDERADO DE COMPRA
        $pcRow = $startRow + 10;
        $sheet->setCellValue("A{$pcRow}", 'PRECIO PONDERADO DE COMPRA');
        $sheet->setCellValue("{$accColLetter}{$pcRow}", $kgSoles);
        $sheet->getStyle("A{$pcRow}:{$accColLetter}{$pcRow}")->applyFromArray($styleYellow);
        $sheet->getStyle("A{$pcRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("{$accColLetter}{$pcRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("{$accColLetter}{$pcRow}")->getNumberFormat()->setFormatCode('#,##0.00');

        // 14) Precio Ponderado Compra Envasado
        $writeAllDaysRowWhite($startRow + 11, 'Precio Ponderado Compra Envasado', $kgSoles);

        // 15) Precio Ponderado Compra Granel
        $writeAllDaysRowWhite($startRow + 12, 'Precio Ponderado Compra Granel', $kgSoles);

        // 16) Fila vacía ($startRow + 13)

        // 17) Diferencial x TM E
        $writeDiffRow($startRow + 14, 'Diferencial x TM E', $envasadoSolesByDate, $envasadoTmByDate, $kgSoles);
        $sheet->getStyle("A" . ($startRow + 14))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // 18) Diferencial x TM G
        $writeDiffRow($startRow + 15, 'Diferencial x TM G', $granelSolesByDate, $granelTmByDate, $kgSoles);
        $sheet->getStyle("A" . ($startRow + 15))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // 19) Diferencial x TM R19
        $writeAccumOnlyRow($startRow + 16, 'Diferencial x TM R19', $precioR19 - $kgSoles);
        $sheet->getStyle("A" . ($startRow + 16))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // 20) Diferencial x TM R26
        $writeAccumOnlyRow($startRow + 17, 'Diferencial x TM R26', $precioR26 - $kgSoles);
        $sheet->getStyle("A" . ($startRow + 17))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // 21) Fila vacía ($startRow + 18)

        // 22) Dif. Compras Vs Ventas E
        $dcveResult = $writeTmDiffRow($startRow + 19, 'Dif. Compras Vs Ventas E', $envasadoSolesByDate, $envasadoTmByDate, $kgSoles);
        $sheet->getStyle("A" . ($startRow + 19) . ":{$accColLetter}" . ($startRow + 19))->applyFromArray($styleYellow);
        $sheet->getStyle("A" . ($startRow + 19))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // 23) Dif. Compras Vs Ventas G
        $dcvgResult = $writeTmDiffRow($startRow + 20, 'Dif. Compras Vs Ventas G', $granelSolesByDate, $granelTmByDate, $kgSoles);
        $sheet->getStyle("A" . ($startRow + 20) . ":{$accColLetter}" . ($startRow + 20))->applyFromArray($styleYellow);
        $sheet->getStyle("A" . ($startRow + 20))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // 24) GRANEL RUTA 19
        $granelRuta19Val = $tmRuta19 * ($precioR19 - $kgSoles);
        $writeAccumOnlyRow($startRow + 21, 'GRANEL RUTA 19', $granelRuta19Val);
        $sheet->getStyle("A" . ($startRow + 21) . ":{$accColLetter}" . ($startRow + 21))->applyFromArray($styleYellow);
        $sheet->getStyle("A" . ($startRow + 21))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // 25) GRANEL RUTA 26
        $granelRuta26Val = $tmRuta26 * ($precioR26 - $kgSoles);
        $writeAccumOnlyRow($startRow + 22, 'GRANEL RUTA 26', $granelRuta26Val);
        $sheet->getStyle("A" . ($startRow + 22) . ":{$accColLetter}" . ($startRow + 22))->applyFromArray($styleYellow);
        $sheet->getStyle("A" . ($startRow + 22))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // 26) MARGEN DIFERENCIAL
        $margenRow = $startRow + 23;
        $sheet->setCellValue("A{$margenRow}", 'MARGEN DIFERENCIAL');
        $colIndex = $colStartIndex;
        foreach ($dates as $date) {
            $valE = (float)($dcveResult['byDate'][$date] ?? 0.0);
            $valG = (float)($dcvgResult['byDate'][$date] ?? 0.0);
            $val  = $valE + $valG;
            $cell = Coordinate::stringFromColumnIndex($colIndex) . $margenRow;
            $sheet->setCellValue($cell, $val);
            $sheet->getStyle($cell)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $colIndex++;
        }
        $margenAcum = (float)$dcveResult['acum'] + (float)$dcvgResult['acum'];
        $sheet->setCellValue("{$accColLetter}{$margenRow}", $margenAcum);
        $sheet->getStyle("{$accColLetter}{$margenRow}")->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle("{$accColLetter}{$margenRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("A{$margenRow}:{$accColLetter}{$margenRow}")->applyFromArray($styleYellow);
        $sheet->getStyle("A{$margenRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Autoajuste columnas
        for ($ci = 1; $ci <= $accColIndex; $ci++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($ci))->setAutoSize(true);
        }
    }

    protected function renderTableStock($sheet, array $dates, int $startRow, array $totalTmByDate = []): int
    {
        $colStartIndex = 2;
        $numDates      = count($dates);
        $accColIndex   = $colStartIndex + $numDates;
        $accColLetter  = Coordinate::stringFromColumnIndex($accColIndex);

        $styleHeader = [
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '05294B']],
        ];

        $styleOrange = [
            'font'      => ['bold' => true],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFC24F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
        ];

        $styleLight = [
            'font'      => ['bold' => false],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
        ];

        $styleWhite = [
            'font'      => ['bold' => false],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
        ];

        // ── ENCABEZADOS ──────────────────────────────────────────────

        $sheet->mergeCells("A{$startRow}:A" . ($startRow + 1));
        $sheet->setCellValue("A{$startRow}", 'STOCK GLP');
        $sheet->getStyle("A{$startRow}:A" . ($startRow + 1))->applyFromArray(array_merge($styleHeader, [
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]));

        $sheet->mergeCells("{$accColLetter}{$startRow}:{$accColLetter}" . ($startRow + 1));
        $sheet->setCellValue("{$accColLetter}{$startRow}", 'ACUMULADO');
        $sheet->getStyle("{$accColLetter}{$startRow}:{$accColLetter}" . ($startRow + 1))->applyFromArray($styleHeader);

        $colIndex = $colStartIndex;
        foreach ($dates as $date) {
            $dayNameUpper = mb_strtoupper(Carbon::parse($date)->locale('es')->translatedFormat('l'), 'UTF-8');
            $cellDay  = Coordinate::stringFromColumnIndex($colIndex) . $startRow;
            $cellDate = Coordinate::stringFromColumnIndex($colIndex) . ($startRow + 1);
            $sheet->setCellValue($cellDay, $dayNameUpper);
            $sheet->setCellValue($cellDate, $date);
            $sheet->getStyle($cellDay)->applyFromArray($styleHeader);
            $sheet->getStyle($cellDate)->applyFromArray($styleHeader);
            $colIndex++;
        }

        // ── FILAS FIJAS ──────────────────────────────────────────────

        $saldoRow = $startRow + 2;
        $sheet->setCellValue("A{$saldoRow}", 'Saldo Inicial');
        $sheet->getStyle("A{$saldoRow}:{$accColLetter}{$saldoRow}")->applyFromArray($styleWhite);
        $sheet->getStyle("A{$saldoRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $formula = "=SUM(" . Coordinate::stringFromColumnIndex($colStartIndex) . ($saldoRow) . ":" . Coordinate::stringFromColumnIndex($accColIndex - 1) . ($saldoRow) . ")";
        $sheet->setCellValue("{$accColLetter}{$saldoRow}", $formula);

        $excRow = $startRow + 3;
        $sheet->setCellValue("A{$excRow}", 'Excedente de GLP');
        $sheet->getStyle("A{$excRow}:{$accColLetter}{$excRow}")->applyFromArray($styleWhite);
        $sheet->getStyle("A{$excRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $formula = "=SUM(" . Coordinate::stringFromColumnIndex($colStartIndex) . ($excRow) . ":" . Coordinate::stringFromColumnIndex($accColIndex - 1) . ($excRow) . ")";
        $sheet->setCellValue("{$accColLetter}{$excRow}", $formula);

        // ── COMPRA DE GLP TM ──────────────────────────────────────────

        $pivot      = [];
        $warehouses = [];
        foreach ($this->resultado as $item) {
            $wh  = is_array($item) ? ($item['warehouse_type_name'] ?? '') : ($item->warehouse_type_name ?? '');
            $fe  = is_array($item) ? ($item['fecha'] ?? '') : ($item->fecha ?? '');
            $val = (float)(is_array($item) ? ($item['total_toneladas'] ?? 0) : ($item->total_toneladas ?? 0));

            if ($wh === '') continue;
            if (!in_array($wh, $warehouses)) $warehouses[] = $wh;
            if (!isset($pivot[$wh][$fe])) $pivot[$wh][$fe] = 0.0;
            $pivot[$wh][$fe] += $val;
        }

        $compraRow = $startRow + 4;
        $sheet->setCellValue("A{$compraRow}", 'Compra de GLP TM');
        $colIndex   = $colStartIndex;
        $grandTotal = 0.0;
        foreach ($dates as $date) {
            $daySum = 0.0;
            foreach ($warehouses as $wh) {
                $daySum += $pivot[$wh][$date] ?? 0.0;
            }
            $cell = Coordinate::stringFromColumnIndex($colIndex) . $compraRow;
            $sheet->setCellValue($cell, $daySum);
            $sheet->getStyle($cell)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $grandTotal += $daySum;
            $colIndex++;
        }
        $sheet->setCellValue("{$accColLetter}{$compraRow}", $grandTotal);
        $sheet->getStyle("{$accColLetter}{$compraRow}")->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle("{$accColLetter}{$compraRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("A{$compraRow}:{$accColLetter}{$compraRow}")->applyFromArray($styleOrange);

        // ── DETALLE POR WAREHOUSE_TYPE_NAME ──────────────────────────

        $currentRow = $compraRow + 1;
        foreach ($warehouses as $wh) {
            $sheet->setCellValue("A{$currentRow}", $wh);
            $colIndex = $colStartIndex;
            $rowAcum  = 0.0;
            foreach ($dates as $date) {
                $val  = $pivot[$wh][$date] ?? 0.0;
                $cell = Coordinate::stringFromColumnIndex($colIndex) . $currentRow;
                $sheet->setCellValue($cell, $val);
                $sheet->getStyle($cell)->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $rowAcum += $val;
                $colIndex++;
            }
            $sheet->setCellValue("{$accColLetter}{$currentRow}", $rowAcum);
            $sheet->getStyle("{$accColLetter}{$currentRow}")->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle("{$accColLetter}{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("A{$currentRow}:{$accColLetter}{$currentRow}")->applyFromArray($styleLight);
            $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $currentRow++;
        }

        // ── VENTA DE GLP TM ──────────────────────────────────────────
        $ventaRow  = $currentRow;
        $ventaAcum = 0.0;
        $sheet->setCellValue("A{$ventaRow}", 'Venta de GLP TM');
        $colIndex = $colStartIndex;
        foreach ($dates as $date) {
            $val  = (float)($totalTmByDate[$date] ?? 0.0);
            $cell = Coordinate::stringFromColumnIndex($colIndex) . $ventaRow;
            $sheet->setCellValue($cell, $val);
            $sheet->getStyle($cell)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $ventaAcum += $val;
            $colIndex++;
        }
        $sheet->setCellValue("{$accColLetter}{$ventaRow}", $ventaAcum);
        $sheet->getStyle("{$accColLetter}{$ventaRow}")->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle("{$accColLetter}{$ventaRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("A{$ventaRow}:{$accColLetter}{$ventaRow}")->applyFromArray($styleOrange);
        $currentRow++;

        // ── SALDO FINAL = Saldo Inicial + Excedente + Compra - Venta ──
        $saldoFinalRow = $currentRow;
        $saldoInicialR = $startRow + 2;
        $excedenteR    = $startRow + 3;
        $compraR       = $compraRow;
        $ventaR        = $ventaRow;

        $sheet->setCellValue("A{$saldoFinalRow}", 'Saldo Final');
        $colIndex = $colStartIndex;
        foreach ($dates as $date) {
            $colLetter = Coordinate::stringFromColumnIndex($colIndex);
            $formula   = "={$colLetter}{$saldoInicialR}+{$colLetter}{$excedenteR}+{$colLetter}{$compraR}-{$colLetter}{$ventaR}";
            $sheet->setCellValue("{$colLetter}{$saldoFinalRow}", $formula);
            $sheet->getStyle("{$colLetter}{$saldoFinalRow}")->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle("{$colLetter}{$saldoFinalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $colIndex++;
        }
        $formula = "={$accColLetter}{$saldoInicialR}+{$accColLetter}{$excedenteR}+{$accColLetter}{$compraR}-{$accColLetter}{$ventaR}";
        $sheet->setCellValue("{$accColLetter}{$saldoFinalRow}", $formula);
        $sheet->getStyle("{$accColLetter}{$saldoFinalRow}")->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle("{$accColLetter}{$saldoFinalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("A{$saldoFinalRow}:{$accColLetter}{$saldoFinalRow}")->applyFromArray($styleLight);
        $currentRow++;

        // Autoajuste columnas
        for ($ci = 1; $ci <= $accColIndex; $ci++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($ci))->setAutoSize(true);
        }

        return $currentRow - 1;
    }

    protected function renderTable($sheet, array $table, int $startRow): int
    {
        $dates         = $table['dates'];
        $groups        = $table['groups'];
        $pivot         = $table['pivot'];
        $overallTotals = $table['overallTotals'];
        $labelSuffix   = $table['labelSuffix'];

        $colStartIndex = 2;
        $numDates      = count($dates);
        $accColIndex   = $colStartIndex + $numDates;
        $pctColIndex   = $accColIndex + 1;
        $accColLetter  = Coordinate::stringFromColumnIndex($accColIndex);
        $pctColLetter  = Coordinate::stringFromColumnIndex($pctColIndex);
        $lastColLetter = Coordinate::stringFromColumnIndex($pctColIndex);

        $styleHeader = [
            'font'      => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '05294B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ];

        $sheet->mergeCells("A{$startRow}:A" . ($startRow + 1));
        $sheet->setCellValue("A{$startRow}", 'REPORTE DE VENTAS DIARIO');

        $colIndex = $colStartIndex;
        foreach ($dates as $date) {
            $dayNameUpper = mb_strtoupper(Carbon::parse($date)->locale('es')->translatedFormat('l'), 'UTF-8');
            $cell = Coordinate::stringFromColumnIndex($colIndex) . $startRow;
            $sheet->setCellValue($cell, $dayNameUpper);
            $colIndex++;
        }

        $sheet->mergeCells("{$accColLetter}{$startRow}:{$accColLetter}" . ($startRow + 1));
        $sheet->setCellValue("{$accColLetter}{$startRow}", 'ACUMULADO');
        $sheet->mergeCells("{$pctColLetter}{$startRow}:{$pctColLetter}" . ($startRow + 1));
        $sheet->setCellValue("{$pctColLetter}{$startRow}", '%');

        $colIndex = $colStartIndex;
        foreach ($dates as $date) {
            $cell = Coordinate::stringFromColumnIndex($colIndex) . ($startRow + 1);
            $sheet->setCellValue($cell, $date);
            $colIndex++;
        }

        $sheet->getStyle("A{$startRow}:{$lastColLetter}{$startRow}")->applyFromArray($styleHeader);
        $sheet->getStyle("A" . ($startRow + 1) . ":{$lastColLetter}" . ($startRow + 1))->applyFromArray($styleHeader);

        $totalRow   = $startRow + 2;
        $grandTotal = array_sum($overallTotals);
        $sheet->setCellValue("A{$totalRow}", 'TOTAL VENTA EN ' . $labelSuffix);
        $colIndex = $colStartIndex;
        foreach ($dates as $date) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . $totalRow, (float)($overallTotals[$date] ?? 0.0));
            $colIndex++;
        }
        $sheet->setCellValue("{$accColLetter}{$totalRow}", (float)$grandTotal);
        $sheet->setCellValue("{$pctColLetter}{$totalRow}", $grandTotal > 0 ? 1.0 : 0.0);
        $sheet->getStyle("A{$totalRow}:{$lastColLetter}{$totalRow}")->applyFromArray([
            'font'      => ['bold' => true],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFC24F']],
            'borders'   => ['top' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $dataStartRow   = $startRow + 3;
        $currentRow     = $dataStartRow;
        $groupTotalRows = [];

        foreach ($groups as $g) {
            $sheet->setCellValue("A{$currentRow}", $g['label']);
            $colIndex = $colStartIndex;
            $rowSum   = 0.0;
            foreach ($dates as $date) {
                $val = (float)($g['totals'][$date] ?? 0.0);
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . $currentRow, $val);
                $rowSum += $val;
                $colIndex++;
            }
            $sheet->setCellValue("{$accColLetter}{$currentRow}", (float)$rowSum);
            $sheet->setCellValue("{$pctColLetter}{$currentRow}", $grandTotal > 0 ? (float)($rowSum / $grandTotal) : 0.0);
            $groupTotalRows[] = $currentRow;
            $currentRow++;

            foreach ($g['channels'] as $channel) {
                $sheet->setCellValue("A{$currentRow}", $channel);
                $colIndex = $colStartIndex;
                $rowSum   = 0.0;
                foreach ($dates as $date) {
                    $val = (float)($pivot[$channel][$date] ?? 0.0);
                    $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . $currentRow, $val);
                    $rowSum += $val;
                    $colIndex++;
                }
                $sheet->setCellValue("{$accColLetter}{$currentRow}", (float)$rowSum);
                $sheet->setCellValue("{$pctColLetter}{$currentRow}", $grandTotal > 0 ? (float)($rowSum / $grandTotal) : 0.0);
                $currentRow++;
            }
        }

        $lastRowUsed = $currentRow - 1;

        for ($ci = $colStartIndex; $ci <= $pctColIndex; $ci++) {
            $letter = Coordinate::stringFromColumnIndex($ci);
            $fmt    = ($ci === $pctColIndex) ? '0.00%' : '#,##0.00';
            $sheet->getStyle("{$letter}{$totalRow}")->getNumberFormat()->setFormatCode($fmt);
            if ($lastRowUsed >= $dataStartRow) {
                $sheet->getStyle("{$letter}{$dataStartRow}:{$letter}{$lastRowUsed}")->getNumberFormat()->setFormatCode($fmt);
            }
        }

        foreach ($groupTotalRows as $r) {
            $sheet->getStyle("A{$r}:{$lastColLetter}{$r}")->applyFromArray([
                'font'      => ['bold' => true],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFEAC2']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders'   => ['bottom' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            for ($ci = $colStartIndex; $ci <= $pctColIndex; $ci++) {
                $sheet->getStyle(Coordinate::stringFromColumnIndex($ci) . $r)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }
        }

        $groupTotalsLookup = array_flip($groupTotalRows);
        for ($r = $dataStartRow; $r <= $lastRowUsed; $r++) {
            if (isset($groupTotalsLookup[$r])) continue;
            $sheet->getStyle("A{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("A{$r}")->getAlignment()->setIndent(1);
        }

        for ($ci = 1; $ci <= $pctColIndex; $ci++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($ci))->setAutoSize(true);
        }

        return $lastRowUsed;
    }

    public function columnFormats(): array
    {
        return [];
    }
}
