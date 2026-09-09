<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class SaleDayReportSheetResumen implements FromCollection, WithTitle, WithColumnFormatting, WithHeadings, WithEvents
{
    protected $dias_totales;
    protected $dias_avance;
    protected $quedan;
    protected $datetime;
    protected $mes_actual;
    protected $mes_anterior;
    protected $resumen;
    protected $resumen_costo_glp;
    protected $resumen_costo_glp_anterior;
    protected $resumen_2;
    protected $resumen_3;
    protected $resumen_4;
    protected $resumen_5;
    protected $tabla4SubCanalRows = [];

    public function __construct($dias_totales, $dias_avance, $quedan, $datetime, $mes_actual, $mes_anterior, $resumen, $resumen_costo_glp, $resumen_costo_glp_anterior, $resumen_2, $resumen_3, $resumen_4, $resumen_5)
    {
        $this->dias_totales = $dias_totales;
        $this->dias_avance = $dias_avance;
        $this->quedan = $quedan;
        $this->datetime = $datetime;
        $this->mes_actual = $mes_actual;
        $this->mes_anterior = $mes_anterior;
        $this->resumen = $resumen;
        $this->resumen_costo_glp = $resumen_costo_glp; // W18
        $this->resumen_costo_glp_anterior = $resumen_costo_glp_anterior; // T18
        $this->resumen_2 = $resumen_2;
        $this->resumen_3 = $resumen_3;
        $this->resumen_4 = $resumen_4;
        $this->resumen_5 = $resumen_5;
    }

    public function title(): string
    {
        return 'Resumen';
    }

    public function headings(): array
    {
        // A..AB => 28 columnas (incluyendo nuevas columnas de Ratios)
        return [
            'Reporte de Venta Diario al ' . $this->datetime,
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            ''
        ];
    }

    /**
     * fmtNum ahora acepta decimales.
     * Si dec = 0 retorna entero redondeado; si dec = 1 retorna 1 decimal, etc.
     * Esta función SOLO debe usarse para formatear texto o valores que no necesitan precisión interna.
     */
    private function fmtNum($v, $dec = 0)
    {
        if (!is_numeric($v)) return $v;
        return round($v, $dec);
    }

    private function fmtPct($v)
    {
        if (!is_numeric($v)) return $v;
        return round($v, 2);
    }

    // Factor de conversión: 1 TM = 100 kg (basado en la fórmula de Excel)
    private function calcularMB($ventaSoles, $toneladas, $costoUnitario)
    {
        if ($toneladas == 0) {
            return 0;
        }

        $kilos = $toneladas * 100; // E17 * 100

        // Fórmula de Excel: (Q17 / (E17*100) - W17) * (E17*100)
        // Que se simplifica a: Q17 - (W17 * E17 * 100)
        // Pero usaremos la fórmula literal para evitar confusiones

        $precioPorKilo = $ventaSoles / $kilos; // Q17 / (E17*100)
        $margenUnitarioPorKilo = $precioPorKilo - $costoUnitario; // (Q17 / (E17*100) - W17)
        $margenBrutoTotal = $margenUnitarioPorKilo * $kilos; // (Q17 / (E17*100) - W17) * (E17*100)

        // Manejar posibles errores de división por cero o NaN
        if (!is_numeric($margenBrutoTotal) || !is_finite((float)$margenBrutoTotal)) {
            return 0;
        }

        return $margenBrutoTotal; // Devolvemos el valor real, sin redondeo
    }

    // Calcular MBU (Margen Bruto Unitario) - Equivalente a =SI.ERROR(+K9/E9/100;0)
    private function calcularMBU($ventaSoles, $toneladas)
    {
        if ($toneladas == 0 || $toneladas === null) {
            return 0;
        }

        try {
            $kilos = $toneladas * 100;
            if ($kilos == 0) {
                return 0;
            }
            return $ventaSoles / $kilos;
        } catch (\Exception $e) {
            return 0;
        }
    }

    // Calcular Cx (Costo Unitario) - Equivalente a =SI.ERROR((U9-V9);0)
    private function calcularCx($px, $mbu)
    {
        try {
            return $px - $mbu;
        } catch (\Exception $e) {
            return 0;
        }
    }

    // Calcular diferencias de ratios - Equivalente a =SI.ERROR(+W17-T17;0)
    private function calcularDiferenciaCx($cxActual, $cxAnterior)
    {
        try {
            return $cxActual - $cxAnterior;
        } catch (\Exception $e) {
            return 0;
        }
    }

    // Calcular diferencias de ratios - Equivalente a =SI.ERROR(+X17-U17;0)
    private function calcularDiferenciaPx($pxActual, $pxAnterior)
    {
        try {
            return $pxActual - $pxAnterior;
        } catch (\Exception $e) {
            return 0;
        }
    }

    // Calcular diferencias de ratios - Equivalente a =SI.ERROR(+Y17-V17;0)
    private function calcularDiferenciaMBU($mbuActual, $mbuAnterior)
    {
        try {
            return $mbuActual - $mbuAnterior;
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function collection()
    {
        $data = collect();

        // Info rows (28 columnas)
        $data->push(array_fill(0, 28, ''));
        $row = array_fill(0, 28, '');
        $row[0] = 'Dias Totales:';
        $row[2] = $this->fmtNum($this->dias_totales, 0); // Valor entero, ok formatearlo aquí
        $data->push($row);
        $row = array_fill(0, 28, '');
        $row[0] = 'Dias Avance:';
        $row[2] = $this->fmtNum($this->dias_avance, 0); // Valor entero, ok formatearlo aquí
        $data->push($row);
        $row = array_fill(0, 28, '');
        $row[0] = 'Quedan:';
        $row[2] = ($this->quedan == 0) ? '0' : $this->fmtNum($this->quedan, 0);
        $data->push($row);
        $data->push(array_fill(0, 28, ''));

        // ---------- TABLA 1 ----------
        $data->push(array_merge(['VENTAS_TM_HEADER'], array_fill(0, 27, '')));

        $data->push([
            'Unidad',
            'Canal',
            '',
            // Ventas TM
            $this->mes_anterior,
            $this->mes_actual,
            'Proy',
            'V%MA',
            'Ppto',
            'V%Ppto',
            // MB
            $this->mes_anterior,
            $this->mes_actual,
            'Proy',
            'V%MA',
            'Ppto',
            'V%Ppto',
            // Venta Soles
            $this->mes_anterior,
            $this->mes_actual,
            'Proy',
            'V%MA',
            // Costo Unitarios (nombres dinámicos)
            'Cx ' . $this->mes_anterior,
            'Px ' . $this->mes_anterior,
            'MBU ' . $this->mes_anterior,
            'Cx ' . $this->mes_actual,
            'Px ' . $this->mes_actual,
            'MBU ' . $this->mes_actual,
            // Ratios
            'Dif Cx',
            'Dif Px',
            'Dif MBU'
        ]);

        // Totales acumuladores iniciales (tabla 1)
        $totalAnterior = $totalActual = $totalProy = $totalPpto = 0;
        $totalAntMB = $totalActMB = $totalProyMB = $totalPptoMB = 0;
        $totalSolesAnt = $totalSolesAct = $totalSolesProy = 0;
        $totalCxDic = $totalPxDic = $totalMbuDic = $totalCxEne = $totalPxEne = $totalMbuEne = 0;
        $sumaSolesAnterior = $sumaToneladasAnterior = $sumaSolesActual = $sumaToneladasActual = 0;
        $totalDifCx = $totalDifPx = $totalDifMBU = 0;

        foreach ($this->resumen as $item) {
            $anterior = $item->toneladas_anterior ?? 0;
            $actual = $item->toneladas_actual ?? 0;
            $proy = $this->calcularProy($actual);
            $ppto = $item->ppto ?? 0;
            $ppto_mb = $item->ppto_mb ?? 0;

            // Venta Soles
            $soles_anterior = $item->venta_soles_anterior ?? 0;
            $soles_actual = $item->venta_soles_actual ?? 0;
            $proy_soles = $this->calcularProy($soles_actual);

            // Acumular para cálculo de totales
            $sumaSolesAnterior += $soles_anterior;
            $sumaToneladasAnterior += $anterior;
            $sumaSolesActual += $soles_actual;
            $sumaToneladasActual += $actual;

            // Costo Unitarios: Px (Precio por kg) - solo informativo
            $kilos_anterior = $anterior * 100;
            $kilos_actual = $actual * 100;
            $px_dic = ($kilos_anterior != 0) ? $soles_anterior / $kilos_anterior : 0;
            $px_ene = ($kilos_actual != 0) ? $soles_actual / $kilos_actual : 0;

            // Calcular MBU usando las fórmulas correctas
            $mbu_ene = $px_ene - $this->resumen_costo_glp;

            // Calcular Cx usando las fórmulas correctas
            $cx_dic = $this->resumen_costo_glp_anterior;
            $mbu_dic = $px_dic - $cx_dic;
            $cx_ene = $this->resumen_costo_glp; // Costo GLP del mes actual (W18)

            // Calcular MB usando la fórmula EXACTA de Excel
            $antMB = $this->calcularMB($soles_anterior, $anterior, $cx_dic);
            $actMB = $this->calcularMB($soles_actual, $actual, $cx_ene);

            $proyMB = $this->calcularProy($actMB);
            $pptoMB = $ppto_mb;

            // Calcular diferencias de ratios
            $difCx = $this->calcularDiferenciaCx($cx_ene, $cx_dic);
            $difPx = $this->calcularDiferenciaPx($px_ene, $px_dic);
            $difMBU = $this->calcularDiferenciaMBU($mbu_ene, $mbu_dic);

            $data->push([
                $item->business_unit_name,
                $item->sector_name,
                '',
                // Ventas TM (valores reales, formato en columnFormats)
                $anterior, // Valor real
                $actual,   // Valor real
                $proy,     // Valor real
                $this->calcularVariacionPorcentual($proy, $anterior), // Valor real
                $ppto,     // Valor real
                $this->calcularVariacionPorcentual($proy, $ppto), // Valor real
                // MB (valores reales)
                $antMB,    // Valor real
                $actMB,    // Valor real
                $proyMB,   // Valor real
                $this->calcularVariacionPorcentual($proyMB, $antMB), // Valor real
                $pptoMB,   // Valor real
                $this->calcularVariacionPorcentual($proyMB, $pptoMB), // Valor real
                // Venta Soles (valores reales)
                $soles_anterior, // Valor real
                $soles_actual,   // Valor real
                $proy_soles,     // Valor real
                $this->calcularVariacionPorcentual($proy_soles, $soles_anterior), // Valor real
                // Costo Unitarios (valores reales)
                $cx_dic,   // Valor real
                $px_dic,   // Valor real
                $mbu_dic,  // Valor real
                $cx_ene,   // Valor real
                $px_ene,   // Valor real
                $mbu_ene,  // Valor real
                // Ratios (diferencias)
                $difCx,    // Valor real
                $difPx,    // Valor real
                $difMBU    // Valor real
            ]);

            // Sumar totales (usar valores originales antes de formatear)
            $totalAnterior += $anterior;
            $totalActual += $actual;
            $totalProy += $proy;
            $totalPpto += $ppto;

            $totalAntMB += $antMB;
            $totalActMB += $actMB;
            $totalProyMB += $proyMB;
            $totalPptoMB += $pptoMB;

            $totalSolesAnt += $soles_anterior;
            $totalSolesAct += $soles_actual;
            $totalSolesProy += $proy_soles;

            $totalCxDic += $cx_dic;
            $totalPxDic += $px_dic;
            $totalMbuDic += $mbu_dic;
            $totalCxEne += $cx_ene;
            $totalPxEne += $px_ene;
            $totalMbuEne += $mbu_ene;

            $totalDifCx += $difCx;
            $totalDifPx += $difPx;
            $totalDifMBU += $difMBU;
        }

        // Calcular totales de costos unitarios usando las mismas fórmulas
        $totalPxMesAnterior = ($sumaToneladasAnterior * 100 != 0) ? $sumaSolesAnterior / ($sumaToneladasAnterior * 100) : 0;
        $totalPxMesActual = ($sumaToneladasActual * 100 != 0) ? $sumaSolesActual / ($sumaToneladasActual * 100) : 0;
        $totalMbuMesAnterior = $totalPxMesAnterior - $this->resumen_costo_glp_anterior;
        $totalMbuMesActual = $totalPxMesActual - $this->resumen_costo_glp;
        $totalCxMesAnterior = $this->resumen_costo_glp_anterior;
        $totalCxMesActual = $this->resumen_costo_glp;

        // Calcular totales de diferencias
        $totalDifCxTotal = $this->calcularDiferenciaCx($totalCxMesActual, $totalCxMesAnterior);
        $totalDifPxTotal = $this->calcularDiferenciaPx($totalPxMesActual, $totalPxMesAnterior);
        $totalDifMBUTotal = $this->calcularDiferenciaMBU($totalMbuMesActual, $totalMbuMesAnterior);

        // TOTAL fila (28 columnas) - valores reales, formato en columnFormats
        $data->push([
            'TOTAL',
            '',
            '',
            $totalAnterior, // Valor real
            $totalActual,   // Valor real
            $totalProy,     // Valor real
            $this->calcularVariacionPorcentual($totalProy, $totalAnterior), // Valor real
            $totalPpto,     // Valor real
            $this->calcularVariacionPorcentual($totalProy, $totalPpto), // Valor real
            // MB totals
            $totalAntMB,    // Valor real
            $totalActMB,    // Valor real
            $totalProyMB,   // Valor real
            $this->calcularVariacionPorcentual($totalProyMB, $totalAntMB), // Valor real
            $totalPptoMB,   // Valor real
            $this->calcularVariacionPorcentual($totalProyMB, $totalPptoMB), // Valor real
            // Venta Soles totals
            $totalSolesAnt, // Valor real
            $totalSolesAct, // Valor real
            $totalSolesProy, // Valor real
            $this->calcularVariacionPorcentual($totalSolesProy, $totalSolesAnt), // Valor real
            // Costo Unitarios totals (valores reales calculados con fórmulas)
            $totalCxMesAnterior,    // Valor real
            $totalPxMesAnterior,    // Valor real
            $totalMbuMesAnterior,   // Valor real
            $totalCxMesActual,      // Valor real
            $totalPxMesActual,      // Valor real
            $totalMbuMesActual,     // Valor real
            // Ratios totals
            $totalDifCxTotal,       // Valor real
            $totalDifPxTotal,       // Valor real
            $totalDifMBUTotal       // Valor real
        ]);

        $data->push(array_fill(0, 28, ''));

        // ---------- TABLA 2 ----------
        $data->push(array_merge(['VENTAS_TM_HEADER'], array_fill(0, 27, '')));
        $data->push([
            'Unidad',
            'Canal',
            'Supervisores',
            $this->mes_anterior,
            $this->mes_actual,
            'Proy',
            'V%MA',
            'Ppto',
            'V%Ppto',
            $this->mes_anterior,
            $this->mes_actual,
            'Proy',
            'V%MA',
            'Ppto',
            'V%Ppto',
            $this->mes_anterior,
            $this->mes_actual,
            'Proy',
            'V%MA',
            'Cx ' . $this->mes_anterior,
            'Px ' . $this->mes_anterior,
            'MBU ' . $this->mes_anterior,
            'Cx ' . $this->mes_actual,
            'Px ' . $this->mes_actual,
            'MBU ' . $this->mes_actual,
            // Ratios
            'Dif Cx',
            'Dif Px',
            'Dif MBU'
        ]);

        // reset totals for table 2
        $totalAnterior = $totalActual = $totalProy = $totalPpto = 0;
        $totalAntMB = $totalActMB = $totalProyMB = $totalPptoMB = 0;
        $totalSolesAnt = $totalSolesAct = $totalSolesProy = 0;
        $totalCxDic = $totalPxDic = $totalMbuDic = $totalCxEne = $totalPxEne = $totalMbuEne = 0;
        $sumaSolesAnterior = $sumaToneladasAnterior = $sumaSolesActual = $sumaToneladasActual = 0;
        $totalDifCx = $totalDifPx = $totalDifMBU = 0;

        foreach ($this->resumen_2 as $item) {
            $anterior = $item->toneladas_anterior ?? 0;
            $actual = $item->toneladas_actual ?? 0;
            $proy = $this->calcularProy($actual);
            $ppto = $item->ppto ?? 0;
            $ppto_mb = $item->ppto_mb ?? 0;

            // Venta Soles
            $soles_anterior = $item->venta_soles_anterior ?? 0;
            $soles_actual = $item->venta_soles_actual ?? 0;
            $proy_soles = $this->calcularProy($soles_actual);

            // Acumular para cálculo de totales
            $sumaSolesAnterior += $soles_anterior;
            $sumaToneladasAnterior += $anterior;
            $sumaSolesActual += $soles_actual;
            $sumaToneladasActual += $actual;

            // Costo Unitarios
            $kilos_anterior = $anterior * 100;
            $kilos_actual = $actual * 100;
            $px_dic = ($kilos_anterior != 0) ? $soles_anterior / $kilos_anterior : 0;
            $px_ene = ($kilos_actual != 0) ? $soles_actual / $kilos_actual : 0;

            // Calcular MBU y Cx
            $mbu_ene = $px_ene - $this->resumen_costo_glp;

            $cx_dic = $this->resumen_costo_glp_anterior;
            $mbu_dic = $px_dic - $cx_dic;
            $cx_ene = $this->resumen_costo_glp; // W18

            // Calcular MB usando la fórmula EXACTA de Excel
            $antMB = $this->calcularMB($soles_anterior, $anterior, $cx_dic);
            $actMB = $this->calcularMB($soles_actual, $actual, $cx_ene);

            $proyMB = $this->calcularProy($actMB);
            $pptoMB = $ppto_mb;

            // Calcular diferencias de ratios
            $difCx = $this->calcularDiferenciaCx($cx_ene, $cx_dic);
            $difPx = $this->calcularDiferenciaPx($px_ene, $px_dic);
            $difMBU = $this->calcularDiferenciaMBU($mbu_ene, $mbu_dic);

            $data->push([
                $item->business_unit_name,
                $item->sector_name,
                $item->manager_name,
                $anterior, // Valor real
                $actual,   // Valor real
                $proy,     // Valor real
                $this->calcularVariacionPorcentual($proy, $anterior), // Valor real
                $ppto,     // Valor real
                $this->calcularVariacionPorcentual($proy, $ppto), // Valor real
                $antMB,    // Valor real
                $actMB,    // Valor real
                $proyMB,   // Valor real
                $this->calcularVariacionPorcentual($proyMB, $antMB), // Valor real
                $pptoMB,   // Valor real
                $this->calcularVariacionPorcentual($proyMB, $pptoMB), // Valor real
                $soles_anterior, // Valor real
                $soles_actual,   // Valor real
                $proy_soles,     // Valor real
                $this->calcularVariacionPorcentual($proy_soles, $soles_anterior), // Valor real
                $cx_dic,   // Valor real
                $px_dic,   // Valor real
                $mbu_dic,  // Valor real
                $cx_ene,   // Valor real
                $px_ene,   // Valor real
                $mbu_ene,  // Valor real
                // Ratios (diferencias)
                $difCx,    // Valor real
                $difPx,    // Valor real
                $difMBU    // Valor real
            ]);

            $totalAnterior += $anterior;
            $totalActual += $actual;
            $totalProy += $proy;
            $totalPpto += $ppto;

            $totalAntMB += $antMB;
            $totalActMB += $actMB;
            $totalProyMB += $proyMB;
            $totalPptoMB += $pptoMB;

            $totalSolesAnt += $soles_anterior;
            $totalSolesAct += $soles_actual;
            $totalSolesProy += $proy_soles;

            $totalCxDic += $cx_dic;
            $totalPxDic += $px_dic;
            $totalMbuDic += $mbu_dic;
            $totalCxEne += $cx_ene;
            $totalPxEne += $px_ene;
            $totalMbuEne += $mbu_ene;

            $totalDifCx += $difCx;
            $totalDifPx += $difPx;
            $totalDifMBU += $difMBU;
        }

        // Calcular totales de costos unitarios usando las mismas fórmulas para tabla 2
        $totalPxMesAnteriorTabla2 = ($sumaToneladasAnterior * 100 != 0) ? $sumaSolesAnterior / ($sumaToneladasAnterior * 100) : 0;
        $totalPxMesActualTabla2 = ($sumaToneladasActual * 100 != 0) ? $sumaSolesActual / ($sumaToneladasActual * 100) : 0;
        $totalMbuMesAnteriorTabla2 = $totalPxMesAnteriorTabla2 - $this->resumen_costo_glp_anterior;
        $totalMbuMesActualTabla2 = $totalPxMesActualTabla2 - $this->resumen_costo_glp;
        $totalCxMesAnteriorTabla2 = $this->resumen_costo_glp_anterior;
        $totalCxMesActualTabla2 = $this->resumen_costo_glp;

        // Calcular totales de diferencias para tabla 2
        $totalDifCxTotalTabla2 = $this->calcularDiferenciaCx($totalCxMesActualTabla2, $totalCxMesAnteriorTabla2);
        $totalDifPxTotalTabla2 = $this->calcularDiferenciaPx($totalPxMesActualTabla2, $totalPxMesAnteriorTabla2);
        $totalDifMBUTotalTabla2 = $this->calcularDiferenciaMBU($totalMbuMesActualTabla2, $totalMbuMesAnteriorTabla2);

        $data->push([
            'TOTAL',
            '',
            '',
            $totalAnterior, // Valor real
            $totalActual,   // Valor real
            $totalProy,     // Valor real
            $this->calcularVariacionPorcentual($totalProy, $totalAnterior), // Valor real
            $totalPpto,     // Valor real
            $this->calcularVariacionPorcentual($totalProy, $totalPpto), // Valor real
            $totalAntMB,    // Valor real
            $totalActMB,    // Valor real
            $totalProyMB,   // Valor real
            $this->calcularVariacionPorcentual($totalProyMB, $totalAntMB), // Valor real
            $totalPptoMB,   // Valor real
            $this->calcularVariacionPorcentual($totalProyMB, $totalPptoMB), // Valor real
            $totalSolesAnt, // Valor real
            $totalSolesAct, // Valor real
            $totalSolesProy, // Valor real
            $this->calcularVariacionPorcentual($totalSolesProy, $totalSolesAnt), // Valor real
            $totalCxMesAnteriorTabla2,    // Valor real
            $totalPxMesAnteriorTabla2,    // Valor real
            $totalMbuMesAnteriorTabla2,   // Valor real
            $totalCxMesActualTabla2,      // Valor real
            $totalPxMesActualTabla2,      // Valor real
            $totalMbuMesActualTabla2,     // Valor real
            // Ratios totals
            $totalDifCxTotalTabla2,       // Valor real
            $totalDifPxTotalTabla2,       // Valor real
            $totalDifMBUTotalTabla2       // Valor real
        ]);

        $data->push(array_fill(0, 28, ''));

        // ---------- TABLA 3 ----------
        $data->push(array_merge(['VENTAS_TM_HEADER'], array_fill(0, 27, '')));
        $data->push([
            'Unidad',
            'Canal',
            'Sub Canal',
            $this->mes_anterior,
            $this->mes_actual,
            'Proy',
            'V%MA',
            'Ppto',
            'V%Ppto',
            $this->mes_anterior,
            $this->mes_actual,
            'Proy',
            'V%MA',
            'Ppto',
            'V%Ppto',
            $this->mes_anterior,
            $this->mes_actual,
            'Proy',
            'V%MA',
            'Cx ' . $this->mes_anterior,
            'Px ' . $this->mes_anterior,
            'MBU ' . $this->mes_anterior,
            'Cx ' . $this->mes_actual,
            'Px ' . $this->mes_actual,
            'MBU ' . $this->mes_actual,
            // Ratios
            'Dif Cx',
            'Dif Px',
            'Dif MBU'
        ]);
        // reset totals for table 3
        $totalAnterior = $totalActual = $totalProy = $totalPpto = 0;
        $totalAntMB = $totalActMB = $totalProyMB = $totalPptoMB = 0;
        $totalSolesAnt = $totalSolesAct = $totalSolesProy = 0;
        $totalCxDic = $totalPxDic = $totalMbuDic = $totalCxEne = $totalPxEne = $totalMbuEne = 0;
        $sumaSolesAnterior = $sumaToneladasAnterior = $sumaSolesActual = $sumaToneladasActual = 0;
        $totalDifCx = $totalDifPx = $totalDifMBU = 0;

        foreach ($this->resumen_3 as $item) {
            $anterior = $item->toneladas_anterior ?? 0;
            $actual = $item->toneladas_actual ?? 0;
            $proy = $this->calcularProy($actual);
            $ppto = $item->ppto ?? 0;
            $ppto_mb = $item->ppto_mb ?? 0;

            // Venta Soles
            $soles_anterior = $item->venta_soles_anterior ?? 0;
            $soles_actual = $item->venta_soles_actual ?? 0;
            $proy_soles = $this->calcularProy($soles_actual);

            // Acumular para cálculo de totales
            $sumaSolesAnterior += $soles_anterior;
            $sumaToneladasAnterior += $anterior;
            $sumaSolesActual += $soles_actual;
            $sumaToneladasActual += $actual;

            // Costo Unitarios
            $kilos_anterior = $anterior * 100;
            $kilos_actual = $actual * 100;
            $px_dic = ($kilos_anterior != 0) ? $soles_anterior / $kilos_anterior : 0;
            $px_ene = ($kilos_actual != 0) ? $soles_actual / $kilos_actual : 0;

            // Calcular MBU y Cx
            $mbu_ene = $px_ene - $this->resumen_costo_glp;

            $cx_dic = $this->resumen_costo_glp_anterior;
            $mbu_dic = $px_dic - $cx_dic;
            $cx_ene = $this->resumen_costo_glp; // W18

            // Calcular MB usando la fórmula EXACTA de Excel
            $antMB = $this->calcularMB($soles_anterior, $anterior, $cx_dic);
            $actMB = $this->calcularMB($soles_actual, $actual, $cx_ene);

            $proyMB = $this->calcularProy($actMB);
            $pptoMB = $ppto_mb;

            // Calcular diferencias de ratios
            $difCx = $this->calcularDiferenciaCx($cx_ene, $cx_dic);
            $difPx = $this->calcularDiferenciaPx($px_ene, $px_dic);
            $difMBU = $this->calcularDiferenciaMBU($mbu_ene, $mbu_dic);

            $data->push([
                $item->business_unit_name,
                $item->sector_name,
                $item->channel_name,
                $anterior, // Valor real
                $actual,   // Valor real
                $proy,     // Valor real
                $this->calcularVariacionPorcentual($proy, $anterior), // Valor real
                $ppto,     // Valor real
                $this->calcularVariacionPorcentual($proy, $ppto), // Valor real
                $antMB,    // Valor real
                $actMB,    // Valor real
                $proyMB,   // Valor real
                $this->calcularVariacionPorcentual($proyMB, $antMB), // Valor real
                $pptoMB,   // Valor real
                $this->calcularVariacionPorcentual($proyMB, $pptoMB), // Valor real
                $soles_anterior, // Valor real
                $soles_actual,   // Valor real
                $proy_soles,     // Valor real
                $this->calcularVariacionPorcentual($proy_soles, $soles_anterior), // Valor real
                $cx_dic,   // Valor real
                $px_dic,   // Valor real
                $mbu_dic,  // Valor real
                $cx_ene,   // Valor real
                $px_ene,   // Valor real
                $mbu_ene,  // Valor real
                // Ratios (diferencias)
                $difCx,    // Valor real
                $difPx,    // Valor real
                $difMBU    // Valor real
            ]);

            $totalAnterior += $anterior;
            $totalActual += $actual;
            $totalProy += $proy;
            $totalPpto += $ppto;

            $totalAntMB += $antMB;
            $totalActMB += $actMB;
            $totalProyMB += $proyMB;
            $totalPptoMB += $pptoMB;

            $totalSolesAnt += $soles_anterior;
            $totalSolesAct += $soles_actual;
            $totalSolesProy += $proy_soles;

            $totalCxDic += $cx_dic;
            $totalPxDic += $px_dic;
            $totalMbuDic += $mbu_dic;
            $totalCxEne += $cx_ene;
            $totalPxEne += $px_ene;
            $totalMbuEne += $mbu_ene;

            $totalDifCx += $difCx;
            $totalDifPx += $difPx;
            $totalDifMBU += $difMBU;
        }

        // Calcular totales de costos unitarios usando las mismas fórmulas para tabla 3
        $totalPxMesAnteriorTabla3 = ($sumaToneladasAnterior * 100 != 0) ? $sumaSolesAnterior / ($sumaToneladasAnterior * 100) : 0;
        $totalPxMesActualTabla3 = ($sumaToneladasActual * 100 != 0) ? $sumaSolesActual / ($sumaToneladasActual * 100) : 0;
        $totalMbuMesAnteriorTabla3 = $totalPxMesAnteriorTabla3 - $this->resumen_costo_glp_anterior;
        $totalMbuMesActualTabla3 = $totalPxMesActualTabla3 - $this->resumen_costo_glp;
        $totalCxMesAnteriorTabla3 = $this->resumen_costo_glp_anterior;
        $totalCxMesActualTabla3 = $this->resumen_costo_glp;

        // Calcular totales de diferencias para tabla 3
        $totalDifCxTotalTabla3 = $this->calcularDiferenciaCx($totalCxMesActualTabla3, $totalCxMesAnteriorTabla3);
        $totalDifPxTotalTabla3 = $this->calcularDiferenciaPx($totalPxMesActualTabla3, $totalPxMesAnteriorTabla3);
        $totalDifMBUTotalTabla3 = $this->calcularDiferenciaMBU($totalMbuMesActualTabla3, $totalMbuMesAnteriorTabla3);

        $data->push([
            'TOTAL',
            '',
            '',
            $totalAnterior, // Valor real
            $totalActual,   // Valor real
            $totalProy,     // Valor real
            $this->calcularVariacionPorcentual($totalProy, $totalAnterior), // Valor real
            $totalPpto,     // Valor real
            $this->calcularVariacionPorcentual($totalProy, $totalPpto), // Valor real
            $totalAntMB,    // Valor real
            $totalActMB,    // Valor real
            $totalProyMB,   // Valor real
            $this->calcularVariacionPorcentual($totalProyMB, $totalAntMB), // Valor real
            $totalPptoMB,   // Valor real
            $this->calcularVariacionPorcentual($totalProyMB, $totalPptoMB), // Valor real
            $totalSolesAnt, // Valor real
            $totalSolesAct, // Valor real
            $totalSolesProy, // Valor real
            $this->calcularVariacionPorcentual($totalSolesProy, $totalSolesAnt), // Valor real
            $totalCxMesAnteriorTabla3,    // Valor real
            $totalPxMesAnteriorTabla3,    // Valor real
            $totalMbuMesAnteriorTabla3,   // Valor real
            $totalCxMesActualTabla3,      // Valor real
            $totalPxMesActualTabla3,      // Valor real
            $totalMbuMesActualTabla3,     // Valor real
            // Ratios totals
            $totalDifCxTotalTabla3,       // Valor real
            $totalDifPxTotalTabla3,       // Valor real
            $totalDifMBUTotalTabla3       // Valor real
        ]);

        $data->push(array_fill(0, 28, ''));

        // ---------- TABLA 4 ----------
        $data->push(array_merge(['VENTAS_TM_HEADER'], array_fill(0, 27, '')));
        $data->push([
            'Sub Canal',
            'Ruta',
            'Supervisor',
            $this->mes_anterior,
            $this->mes_actual,
            'Proy',
            'V%MA',
            'Ppto',
            'V%Ppto',
            $this->mes_anterior,
            $this->mes_actual,
            'Proy',
            'V%MA',
            'Ppto',
            'V%Ppto',
            $this->mes_anterior,
            $this->mes_actual,
            'Proy',
            'V%MA',
            'Cx ' . $this->mes_anterior,
            'Px ' . $this->mes_anterior,
            'MBU ' . $this->mes_anterior,
            'Cx ' . $this->mes_actual,
            'Px ' . $this->mes_actual,
            'MBU ' . $this->mes_actual,
            // Ratios
            'Dif Cx',
            'Dif Px',
            'Dif MBU'
        ]);
        // reset totals for table 4
        $totalAnterior = $totalActual = $totalProy = $totalPpto = 0;
        $totalAntMB = $totalActMB = $totalProyMB = $totalPptoMB = 0;
        $totalSolesAnt = $totalSolesAct = $totalSolesProy = 0;
        $totalCxDic = $totalPxDic = $totalMbuDic = $totalCxEne = $totalPxEne = $totalMbuEne = 0;
        $sumaSolesAnterior = $sumaToneladasAnterior = $sumaSolesActual = $sumaToneladasActual = 0;
        $totalDifCx = $totalDifPx = $totalDifMBU = 0;

        $startRowTabla4 = $data->count() + 1;
        foreach ($this->resumen_4 as $item) {
            $anterior = $item->toneladas_anterior ?? 0;
            $actual = $item->toneladas_actual ?? 0;
            $proy = $this->calcularProy($actual);
            $ppto = $item->ppto ?? 0;
            $ppto_mb = $item->ppto_mb ?? 0;

            // Venta Soles
            $soles_anterior = $item->venta_soles_anterior ?? 0;
            $soles_actual = $item->venta_soles_actual ?? 0;
            $proy_soles = $this->calcularProy($soles_actual);

            // Acumular para cálculo de totales
            $sumaSolesAnterior += $soles_anterior;
            $sumaToneladasAnterior += $anterior;
            $sumaSolesActual += $soles_actual;
            $sumaToneladasActual += $actual;

            // Costo Unitarios
            $kilos_anterior = $anterior * 100;
            $kilos_actual = $actual * 100;
            $px_dic = ($kilos_anterior != 0) ? $soles_anterior / $kilos_anterior : 0;
            $px_ene = ($kilos_actual != 0) ? $soles_actual / $kilos_actual : 0;

            // Calcular MBU y Cx
            $mbu_ene = $px_ene - $this->resumen_costo_glp;

            $cx_dic = $this->resumen_costo_glp_anterior;
            $mbu_dic = $px_dic - $cx_dic;
            $cx_ene = $this->resumen_costo_glp; // W18

            // Calcular MB usando la fórmula EXACTA de Excel
            $antMB = $this->calcularMB($soles_anterior, $anterior, $cx_dic);
            $actMB = $this->calcularMB($soles_actual, $actual, $cx_ene);

            $proyMB = $this->calcularProy($actMB);
            $pptoMB = $ppto_mb;

            // Calcular diferencias de ratios
            $difCx = $this->calcularDiferenciaCx($cx_ene, $cx_dic);
            $difPx = $this->calcularDiferenciaPx($px_ene, $px_dic);
            $difMBU = $this->calcularDiferenciaMBU($mbu_ene, $mbu_dic);

            $data->push([
                $item->channel_name,
                $item->route_name,
                $item->manager_name,
                $anterior, // Valor real
                $actual,   // Valor real
                $proy,     // Valor real
                $this->calcularVariacionPorcentual($proy, $anterior), // Valor real
                $ppto,     // Valor real
                $this->calcularVariacionPorcentual($proy, $ppto), // Valor real
                $antMB,    // Valor real
                $actMB,    // Valor real
                $proyMB,   // Valor real
                $this->calcularVariacionPorcentual($proyMB, $antMB), // Valor real
                $pptoMB,   // Valor real
                $this->calcularVariacionPorcentual($proyMB, $pptoMB), // Valor real
                $soles_anterior, // Valor real
                $soles_actual,   // Valor real
                $proy_soles,     // Valor real
                $this->calcularVariacionPorcentual($proy_soles, $soles_anterior), // Valor real
                $cx_dic,   // Valor real
                $px_dic,   // Valor real
                $mbu_dic,  // Valor real
                $cx_ene,   // Valor real
                $px_ene,   // Valor real
                $mbu_ene,  // Valor real
                // Ratios (diferencias)
                $difCx,    // Valor real
                $difPx,    // Valor real
                $difMBU    // Valor real
            ]);

            $subcanal = trim(strtoupper($item->channel_name)); // o el campo correcto
            $this->tabla4SubCanalRows[] = [
                'row' => $data->count(), // fila 1-based
                'subcanal' => $subcanal,
            ];

            $totalAnterior += $anterior;
            $totalActual += $actual;
            $totalProy += $proy;
            $totalPpto += $ppto;

            $totalAntMB += $antMB;
            $totalActMB += $actMB;
            $totalProyMB += $proyMB;
            $totalPptoMB += $pptoMB;

            $totalSolesAnt += $soles_anterior;
            $totalSolesAct += $soles_actual;
            $totalSolesProy += $proy_soles;

            $totalCxDic += $cx_dic;
            $totalPxDic += $px_dic;
            $totalMbuDic += $mbu_dic;
            $totalCxEne += $cx_ene;
            $totalPxEne += $px_ene;
            $totalMbuEne += $mbu_ene;

            $totalDifCx += $difCx;
            $totalDifPx += $difPx;
            $totalDifMBU += $difMBU;
        }

        // Calcular totales de costos unitarios usando las mismas fórmulas para tabla 4
        $totalPxMesAnteriorTabla4 = ($sumaToneladasAnterior * 100 != 0) ? $sumaSolesAnterior / ($sumaToneladasAnterior * 100) : 0;
        $totalPxMesActualTabla4 = ($sumaToneladasActual * 100 != 0) ? $sumaSolesActual / ($sumaToneladasActual * 100) : 0;
        $totalMbuMesAnteriorTabla4 = $totalPxMesAnteriorTabla4 - $this->resumen_costo_glp_anterior;
        $totalMbuMesActualTabla4 = $totalPxMesActualTabla4 - $this->resumen_costo_glp;
        $totalCxMesAnteriorTabla4 = $this->resumen_costo_glp_anterior;
        $totalCxMesActualTabla4 = $this->resumen_costo_glp;

        // Calcular totales de diferencias para tabla 4
        $totalDifCxTotalTabla4 = $this->calcularDiferenciaCx($totalCxMesActualTabla4, $totalCxMesAnteriorTabla4);
        $totalDifPxTotalTabla4 = $this->calcularDiferenciaPx($totalPxMesActualTabla4, $totalPxMesAnteriorTabla4);
        $totalDifMBUTotalTabla4 = $this->calcularDiferenciaMBU($totalMbuMesActualTabla4, $totalMbuMesAnteriorTabla4);

        $data->push([
            'TOTAL',
            '',
            '',
            $totalAnterior, // Valor real
            $totalActual,   // Valor real
            $totalProy,     // Valor real
            $this->calcularVariacionPorcentual($totalProy, $totalAnterior), // Valor real
            $totalPpto,     // Valor real
            $this->calcularVariacionPorcentual($totalProy, $totalPpto), // Valor real
            $totalAntMB,    // Valor real
            $totalActMB,    // Valor real
            $totalProyMB,   // Valor real
            $this->calcularVariacionPorcentual($totalProyMB, $totalAntMB), // Valor real
            $totalPptoMB,   // Valor real
            $this->calcularVariacionPorcentual($totalProyMB, $totalPptoMB), // Valor real
            $totalSolesAnt, // Valor real
            $totalSolesAct, // Valor real
            $totalSolesProy, // Valor real
            $this->calcularVariacionPorcentual($totalSolesProy, $totalSolesAnt), // Valor real
            $totalCxMesAnteriorTabla4,    // Valor real
            $totalPxMesAnteriorTabla4,    // Valor real
            $totalMbuMesAnteriorTabla4,   // Valor real
            $totalCxMesActualTabla4,      // Valor real
            $totalPxMesActualTabla4,      // Valor real
            $totalMbuMesActualTabla4,     // Valor real
            // Ratios totals
            $totalDifCxTotalTabla4,       // Valor real
            $totalDifPxTotalTabla4,       // Valor real
            $totalDifMBUTotalTabla4       // Valor real
        ]);

        $data->push(array_fill(0, 28, ''));

        // ---------- TABLA 5 (por supervisor) ----------
        $resumen5_grouped = collect($this->resumen_5)->groupBy('manager_name');

        foreach ($resumen5_grouped as $manager_name => $items) {
            $data->push(array_merge(['VENTAS_TM_HEADER'], array_fill(0, 27, '')));
            $data->push([
                'Sub Canal',
                'Supervisor',
                '',
                $this->mes_anterior,
                $this->mes_actual,
                'Proy',
                'V%MA',
                'Ppto',
                'V%Ppto',
                $this->mes_anterior,
                $this->mes_actual,
                'Proy',
                'V%MA',
                'Ppto',
                'V%Ppto',
                $this->mes_anterior,
                $this->mes_actual,
                'Proy',
                'V%MA',
                'Cx ' . $this->mes_anterior,
                'Px ' . $this->mes_anterior,
                'MBU ' . $this->mes_anterior,
                'Cx ' . $this->mes_actual,
                'Px ' . $this->mes_actual,
                'MBU ' . $this->mes_actual,
                // Ratios
                'Dif Cx',
                'Dif Px',
                'Dif MBU'
            ]);

            // Reset totals for this supervisor group
            $totalAnterior = $totalActual = $totalProy = $totalPpto = 0;
            $totalAntMB = $totalActMB = $totalProyMB = $totalPptoMB = 0;
            $totalSolesAnt = $totalSolesAct = $totalSolesProy = 0;
            $totalCxDic = $totalPxDic = $totalMbuDic = $totalCxEne = $totalPxEne = $totalMbuEne = 0;
            $sumaSolesAnterior = $sumaToneladasAnterior = $sumaSolesActual = $sumaToneladasActual = 0;
            $totalDifCx = $totalDifPx = $totalDifMBU = 0;

            foreach ($items as $item) {
                $anterior = $item->toneladas_anterior ?? 0;
                $actual = $item->toneladas_actual ?? 0;
                $proy = $this->calcularProy($actual);
                $ppto = $item->ppto ?? 0;
                $ppto_mb = $item->ppto_mb ?? 0;

                // Venta Soles
                $soles_anterior = $item->venta_soles_anterior ?? 0;
                $soles_actual = $item->venta_soles_actual ?? 0;
                $proy_soles = $this->calcularProy($soles_actual);

                // Acumular para cálculo de totales
                $sumaSolesAnterior += $soles_anterior;
                $sumaToneladasAnterior += $anterior;
                $sumaSolesActual += $soles_actual;
                $sumaToneladasActual += $actual;

                // Costo Unitarios
                $kilos_anterior = $anterior * 100;
                $kilos_actual = $actual * 100;
                $px_dic = ($kilos_anterior != 0) ? $soles_anterior / $kilos_anterior : 0;
                $px_ene = ($kilos_actual != 0) ? $soles_actual / $kilos_actual : 0;

                // Calcular MBU y Cx
                $mbu_ene = $px_ene - $this->resumen_costo_glp;

                $cx_dic = $this->resumen_costo_glp_anterior;
                $mbu_dic = $px_dic - $cx_dic;
                $cx_ene = $this->resumen_costo_glp; // W18

                // Calcular MB usando la fórmula EXACTA de Excel
                $antMB = $this->calcularMB($soles_anterior, $anterior, $cx_dic);
                $actMB = $this->calcularMB($soles_actual, $actual, $cx_ene);

                $proyMB = $this->calcularProy($actMB);
                $pptoMB = $ppto_mb;

                // Calcular diferencias de ratios
                $difCx = $this->calcularDiferenciaCx($cx_ene, $cx_dic);
                $difPx = $this->calcularDiferenciaPx($px_ene, $px_dic);
                $difMBU = $this->calcularDiferenciaMBU($mbu_ene, $mbu_dic);

                $data->push([
                    $item->channel_name,
                    $manager_name,
                    '',
                    $anterior, // Valor real
                    $actual,   // Valor real
                    $proy,     // Valor real
                    $this->calcularVariacionPorcentual($proy, $anterior), // Valor real
                    $ppto,     // Valor real
                    $this->calcularVariacionPorcentual($proy, $ppto), // Valor real
                    $antMB,    // Valor real
                    $actMB,    // Valor real
                    $proyMB,   // Valor real
                    $this->calcularVariacionPorcentual($proyMB, $antMB), // Valor real
                    $pptoMB,   // Valor real
                    $this->calcularVariacionPorcentual($proyMB, $pptoMB), // Valor real
                    $soles_anterior, // Valor real
                    $soles_actual,   // Valor real
                    $proy_soles,     // Valor real
                    $this->calcularVariacionPorcentual($proy_soles, $soles_anterior), // Valor real
                    $cx_dic,   // Valor real
                    $px_dic,   // Valor real
                    $mbu_dic,  // Valor real
                    $cx_ene,   // Valor real
                    $px_ene,   // Valor real
                    $mbu_ene,  // Valor real
                    // Ratios (diferencias)
                    $difCx,    // Valor real
                    $difPx,    // Valor real
                    $difMBU    // Valor real
                ]);

                $totalAnterior += $anterior;
                $totalActual += $actual;
                $totalProy += $proy;
                $totalPpto += $ppto;

                $totalAntMB += $antMB;
                $totalActMB += $actMB;
                $totalProyMB += $proyMB;
                $totalPptoMB += $pptoMB;

                $totalSolesAnt += $soles_anterior;
                $totalSolesAct += $soles_actual;
                $totalSolesProy += $proy_soles;

                $totalCxDic += $cx_dic;
                $totalPxDic += $px_dic;
                $totalMbuDic += $mbu_dic;
                $totalCxEne += $cx_ene;
                $totalPxEne += $px_ene;
                $totalMbuEne += $mbu_ene;

                $totalDifCx += $difCx;
                $totalDifPx += $difPx;
                $totalDifMBU += $difMBU;
            }

            // Calcular totales de costos unitarios usando las mismas fórmulas para esta tabla
            $totalPxMesAnteriorTabla5 = ($sumaToneladasAnterior * 100 != 0) ? $sumaSolesAnterior / ($sumaToneladasAnterior * 100) : 0;
            $totalPxMesActualTabla5 = ($sumaToneladasActual * 100 != 0) ? $sumaSolesActual / ($sumaToneladasActual * 100) : 0;
            $totalMbuMesAnteriorTabla5 = $totalPxMesAnteriorTabla5 - $this->resumen_costo_glp_anterior;
            $totalMbuMesActualTabla5 = $totalPxMesActualTabla5 - $this->resumen_costo_glp;
            $totalCxMesAnteriorTabla5 = $this->resumen_costo_glp_anterior;
            $totalCxMesActualTabla5 = $this->resumen_costo_glp;

            // Calcular totales de diferencias para esta tabla
            $totalDifCxTotalTabla5 = $this->calcularDiferenciaCx($totalCxMesActualTabla5, $totalCxMesAnteriorTabla5);
            $totalDifPxTotalTabla5 = $this->calcularDiferenciaPx($totalPxMesActualTabla5, $totalPxMesAnteriorTabla5);
            $totalDifMBUTotalTabla5 = $this->calcularDiferenciaMBU($totalMbuMesActualTabla5, $totalMbuMesAnteriorTabla5);

            $data->push([
                'TOTAL',
                '',
                '',
                $totalAnterior, // Valor real
                $totalActual,   // Valor real
                $totalProy,     // Valor real
                $this->calcularVariacionPorcentual($totalProy, $totalAnterior), // Valor real
                $totalPpto,     // Valor real
                $this->calcularVariacionPorcentual($totalProy, $totalPpto), // Valor real
                $totalAntMB,    // Valor real
                $totalActMB,    // Valor real
                $totalProyMB,   // Valor real
                $this->calcularVariacionPorcentual($totalProyMB, $totalAntMB), // Valor real
                $totalPptoMB,   // Valor real
                $this->calcularVariacionPorcentual($totalProyMB, $totalPptoMB), // Valor real
                $totalSolesAnt, // Valor real
                $totalSolesAct, // Valor real
                $totalSolesProy, // Valor real
                $this->calcularVariacionPorcentual($totalSolesProy, $totalSolesAnt), // Valor real
                $totalCxMesAnteriorTabla5 * 100,    // Valor real
                $totalPxMesAnteriorTabla5 * 100,    // Valor real
                $totalMbuMesAnteriorTabla5 * 100,   // Valor real
                $totalCxMesActualTabla5 * 100,      // Valor real
                $totalPxMesActualTabla5 * 100,      // Valor real
                $totalMbuMesActualTabla5 * 100,     // Valor real
                // Ratios totals
                $totalDifCxTotalTabla5 * 100,       // Valor real
                $totalDifPxTotalTabla5 * 100,       // Valor real
                $totalDifMBUTotalTabla5 * 100      // Valor real
            ]);

            $data->push(array_fill(0, 28, ''));
        }

        $rows = $data->toArray();
        $newRows = [];
        $insideTable = false;
        $skipHeaderRow = false;

        foreach ($rows as $rowIndex => $fila) {
            if (isset($fila[0]) && $fila[0] === 'VENTAS_TM_HEADER') {
                $insideTable = true;
                $skipHeaderRow = true;
                $newRows[] = $fila;
                continue;
            }

            if ($insideTable && $skipHeaderRow) {
                $newRows[] = $fila;
                $skipHeaderRow = false;
                continue;
            }

            // detectar fila completamente vacía -> fin de la tabla
            $allEmpty = true;
            foreach ($fila as $cell) {
                if ($cell !== null && $cell !== '') {
                    $allEmpty = false;
                    break;
                }
            }
            if ($insideTable && $allEmpty) {
                $insideTable = false;
                $newRows[] = $fila;
                continue;
            }

            if ($insideTable) {
                // asegurar D..AB => índices 3..27
                for ($i = 3; $i <= 27; $i++) {
                    // obtener valor seguro (evita undefined offset)
                    $raw = $fila[$i] ?? null;

                    // normalizar strings: quitar NBSP y trim
                    if (is_string($raw)) {
                        $rawClean = trim(str_replace("\xC2\xA0", ' ', $raw));
                    } else {
                        $rawClean = $raw;
                    }


                    // condición robusta: null, cadena vacía, o 0 (numérico o string "0") -> poner '0'
                    if (
                        $rawClean === null ||
                        $rawClean === '' ||
                        $rawClean === '0' ||
                        (is_numeric($rawClean) && $rawClean == 0)
                    ) {
                        $fila[$i] = '0'; // como pediste, STRING '0'
                    } else {
                        // mantener valor normalizado
                        $fila[$i] = $rawClean;
                    }
                }
            }

            $newRows[] = $fila;
        }

        $data = collect($newRows);
        return $data;
    }

    public function calcularProy($valor)
    {
        if ($this->dias_avance == 0 || $this->dias_avance === null) {
            return 0;
        }
        // Devolver valor real para mantener precisión
        return ($valor / $this->dias_avance) * $this->dias_totales;
    }

    public function calcularVariacionPorcentual($actual, $base)
    {
        if ($base == 0 || $base === null) {
            return 0;
        }
        // Devolver valor real para mantener precisión
        return ($actual / $base) - 1;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $event->sheet->getDelegate()->getTabColor()->setRGB('FF1212');
                $event->sheet->getSheetView()->setZoomScale(90);

                $sheet = $event->sheet->getDelegate();
                $sheet->setShowGridlines(false);
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn(); // ahora debería ser 'AB'

                $rows = $this->tabla4SubCanalRows;
                $count = count($rows);

                for ($i = 0; $i < $count; $i++) {
                    $currentRow = $rows[$i]['row'];
                    $currentSubCanal = $rows[$i]['subcanal'];

                    $nextSubCanal = ($i + 1 < $count) ? $rows[$i + 1]['subcanal'] : null;

                    if ($nextSubCanal === null || $nextSubCanal !== $currentSubCanal) {
                        // Aplica borde inferior grueso en toda la fila
                        $borderRow = $currentRow + 1;
                        $sheet->getStyle("A{$borderRow}:AB{$borderRow}")
                            ->getBorders()->getBottom()
                            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    }
                }


                // Columnas A..AB (28 cols) - dejamos anchos mínimo razonables
                $sheet->getColumnDimension('A')->setWidth(20);
                $sheet->getColumnDimension('B')->setWidth(20);
                $sheet->getColumnDimension('C')->setWidth(25);

                // Auto-size para resto columnas D..AB
                foreach (range('D', 'AB') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                $fontSize = 9;
                $sheet->getStyle("A1:{$highestColumn}{$highestRow}")->getFont()->setSize($fontSize);

                // Colors (ARGB)
                $blueDark     = '0D2875';
                $orange       = 'FFFFC000';
                $red          = 'FFFF0000';
                $green_tm     = 'FF00B050'; // Ventas TM
                $green_mb     = '66D656'; // MB (tono)
                $darkGreen    = 'FF006400'; // Venta Soles (más oscuro)
                // Costo Unitarios: dos verdes distintos (anterior / actual)
                $green_costo_prev = 'FF2E8B57'; // verde un poco más claro/mediano (mes anterior)
                $green_costo_act  = 'FF004B23'; // verde más oscuro (mes actual)
                // Ratios: fondo rojo y texto blanco
                $red_ratios       = 'CC3737'; // rojo para ratios

                // Estilo A1
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(10)->getColor()->setARGB('FFFF0000');

                $sheet->mergeCells("A1:B1");
                for ($i = 1; $i <= $highestRow; $i++) {
                    $cellValue = $sheet->getCell('A' . $i)->getValue();

                    if (in_array($cellValue, ['Unidad', 'Sub Canal', 'Canal', 'Ruta', 'Supervisores', 'Supervisor'])) {
                        // Centrar texto de A hasta AB en la fila de encabezados
                        $sheet->getStyle("A{$i}:AB{$i}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    }
                    // Si detecto la fila "VENTAS_TM_HEADER" creo títulos fusionados:
                    // D:I, J:O, P:S, T:V (Costo prev), W:Y (Costo act), Z:AB (Ratios)
                    if ($cellValue === 'VENTAS_TM_HEADER') {
                        // Ventas TM (D:I)
                        $sheet->mergeCells("D{$i}:I{$i}");
                        $sheet->setCellValue("D{$i}", 'Ventas TM');
                        $sheet->getStyle("D{$i}:I{$i}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $green_tm]],
                            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => $fontSize],
                            'alignment' => [
                                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                            ]
                        ]);

                        // MB (J:O)
                        $sheet->mergeCells("J{$i}:O{$i}");
                        $sheet->setCellValue("J{$i}", 'MB');
                        $sheet->getStyle("J{$i}:O{$i}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $green_mb]],
                            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => $fontSize],
                            'alignment' => [
                                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                            ]
                        ]);

                        // Venta Soles (P:S)
                        $sheet->mergeCells("P{$i}:S{$i}");
                        $sheet->setCellValue("P{$i}", 'Venta Soles');
                        $sheet->getStyle("P{$i}:S{$i}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $darkGreen]],
                            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => $fontSize],
                            'alignment' => [
                                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                            ]
                        ]);

                        $sheet->mergeCells("T{$i}:Y{$i}");
                        $sheet->setCellValue("T{$i}", 'MBU');

                        $sheet->getStyle("T{$i}:Y{$i}")->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['argb' => $green_costo_act]
                            ],
                            'font' => [
                                'bold' => true,
                                'color' => ['argb' => 'FFFFFFFF'],
                                'size' => $fontSize
                            ],
                            'alignment' => [
                                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                            ]
                        ]);

                        // Costo Unitarios: Mes Anterior (T:V) - verde distinto

                        $sheet->getStyle("T{$i}:V{$i}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $green_costo_prev]],
                            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => $fontSize],
                            'alignment' => [
                                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                            ]
                        ]);

                        // Costo Unitarios: Mes Actual (W:Y) - otro verde más oscuro

                        $sheet->getStyle("W{$i}:Y{$i}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $green_costo_act]],
                            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => $fontSize],
                            'alignment' => [
                                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                            ]
                        ]);

                        // Ratios (Z:AB) - fondo rojo y texto blanco
                        $sheet->mergeCells("Z{$i}:AB{$i}");
                        $sheet->setCellValue("Z{$i}", 'Ratios');
                        $sheet->getStyle("Z{$i}:AB{$i}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $red_ratios]],
                            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => $fontSize],
                            'alignment' => [
                                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                            ]
                        ]);

                        // limpiar A en esa fila
                        $sheet->setCellValue("A{$i}", '');
                    }

                    // Encabezados A:C style
                    if (in_array($cellValue, ['Unidad', 'Sub Canal', 'Venta Sub Canal', 'Canal', 'Ruta', 'Supervisores', 'Venta Sub Canal', 'Channel', 'Supervisor'])) {
                        $sheet->getStyle("A{$i}:C{$i}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $blueDark]],
                            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => $fontSize]
                        ]);

                        // Mes columns orange (D/E, J/K, P/Q)
                        $sheet->getStyle("D{$i}:E{$i}")->applyFromArray(['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $blueDark]], 'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'],  'size' => $fontSize]]);
                        $sheet->getStyle("J{$i}:K{$i}")->applyFromArray(['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $blueDark]], 'font' => ['bold' => true,  'color' => ['argb' => 'FFFFFFFF'], 'size' => $fontSize]]);
                        $sheet->getStyle("P{$i}:Q{$i}")->applyFromArray(['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $blueDark]], 'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'],  'size' => $fontSize]]);

                        // Proy columns (F, L, R) blueDark
                        $sheet->getStyle("F{$i}")->applyFromArray(['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $blueDark]], 'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => $fontSize]]);
                        $sheet->getStyle("L{$i}")->applyFromArray(['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $blueDark]], 'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => $fontSize]]);
                        $sheet->getStyle("R{$i}")->applyFromArray(['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $blueDark]], 'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => $fontSize]]);

                        // V% columns red (G, I, M, O, S)
                        foreach (['G', 'I', 'M', 'O', 'S'] as $col) {
                            $sheet->getStyle("{$col}{$i}")->applyFromArray(['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $red_ratios]], 'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => $fontSize]]);
                        }

                        // Ppto columns pink (H, N)
                        $sheet->getStyle("H{$i}")->applyFromArray(['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $blueDark]], 'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => $fontSize]]);
                        $sheet->getStyle("N{$i}")->applyFromArray(['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $blueDark]], 'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'],  'size' => $fontSize]]);

                        // Costos unitarios header colores (T:V anterior, W:Y actual)
                        $sheet->getStyle("T{$i}:V{$i}")->applyFromArray(['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $green_costo_prev]], 'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => $fontSize]]);
                        $sheet->getStyle("W{$i}:Y{$i}")->applyFromArray(['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $green_costo_act]], 'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => $fontSize]]);

                        // Ratios header (Z:AB) - fondo rojo y texto blanco
                        $sheet->getStyle("Z{$i}:AB{$i}")->applyFromArray(['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $red_ratios]], 'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => $fontSize]]);
                    }

                    // TOTAL rows (A..AB)
                    if ($cellValue === 'TOTAL') {
                        $sheet->getStyle("A{$i}:AB{$i}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $orange]],
                            'font' => ['bold' => true, 'size' => $fontSize]
                        ]);
                    }

                    // Alineaciones/Wrap por fila (A..AB)
                    $sheet->getStyle("A{$i}:AB{$i}")->applyFromArray([
                        'alignment' => [
                            'wrapText' => false,
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        ]
                    ]);
                }

                // Ajustar altura de fila (auto)
                for ($row = 1; $row <= $highestRow; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(-1);
                }

                $sheet->freezePane('D9');
            },
        ];
    }
    public function columnFormats(): array
    {
        return [
            // Ventas TM (D..I)
            // D, E: Enteros
            'D' =>  '#,##0',
            'E' => '#,##0',
            // F: Entero (Proyección)
            'F' => '#,##0',
            // G, I: Porcentaje (0 decimales)
            'G' => '0%',
            'I' => '0%',
            // H: Entero (Presupuesto)
            'H' => '#,##0',
            // Ventas TM (continuación)

            // MB (J..O)
            // J, K: Números con 1 decimal
            'J' => '#,##0',
            'K' => '#,##0',
            // L: Entero (Proyección MB)
            'L' => '#,##0',
            // M, O: Porcentaje (0 decimales)
            'M' => '0%',
            'O' => '0%',
            // N: Entero (Presupuesto MB)
            'N' => '#,##0',

            // Venta Soles (P..S)
            // P, Q: Enteros
            'P' => '#,##0',
            'Q' => '#,##0',
            // R: Entero (Proyección Soles)
            'R' => '#,##0',
            // S: Porcentaje (0 decimales)
            'S' => '0%',

            // Costo Unitarios (T..Y) - 1 decimal
            'T' => '#,##0.0',
            'U' => '#,##0.0',
            'V' => '#,##0.0',
            'W' => '#,##0.0',
            'X' => '#,##0.0',
            'Y' => '#,##0.0',

            // Ratios (Z..AB) - 1 decimal
            'Z' => '#,##0.00',
            'AA' => '#,##0.00',
            'AB' => '#,##0.00',
        ];
    }
}
