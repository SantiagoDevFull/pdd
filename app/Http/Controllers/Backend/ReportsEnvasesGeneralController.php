<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\WarehouseType;
use App\Article;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Requiere PhpSpreadsheet
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ReportsEnvasesGeneralController extends Controller
{
    const YEAR_INITIAL = 2019;
    const WAREHOUSE_TYPE_STOCK_REAL = 22; // Planta principal para stock real

    public function index() {
        $plantas = WarehouseType::select('id', 'name')->where('type', 3)->get();
        return view('backend.reports_envases_general')->with(compact('plantas'));
    }

    /**
     * Tu endpoint actual (stock por condición).
     */
    public function getStockArticles()
    {
        $warehouse_type_id = request('warehouse_type_index');

        if ($warehouse_type_id) {
            $warehouse_type_ids = WarehouseType::select('id')
                ->where('id', $warehouse_type_id)
                ->get();
        } else {
            $warehouse_type_ids = WarehouseType::select('id')
                ->where('type', 3)
                ->get();
        }

        $articles = Article::select(
            'id',
            'warehouse_type_id',
            'stock_good',
            'stock_repair',
            'stock_return',
            'stock_damaged',
            'stock_minimum',
            'group_id',
            'name',
            'code'
        )
            ->whereIn('warehouse_type_id', $warehouse_type_ids)
            ->where('group_id', 30)
            ->get();

        $stocks_articles = [];

        $balon5 = [
            'name' => 'BALON 5KG GLP',
            'code' => 11,
            'stock_good' => 0,
            'stock_repair' => 0,
            'stock_return' => 0,
            'stock_damaged' => 0,
            'stock_minimum' => 0,
            'stock' => 0
        ];
        $balon10 = [
            'name' => 'BALON 10KG GLP',
            'code' => 12,
            'stock_good' => 0,
            'stock_repair' => 0,
            'stock_return' => 0,
            'stock_damaged' => 0,
            'stock_minimum' => 0,
            'stock' => 0
        ];
        $balon15 = [
            'name' => 'BALON 15KG GLP',
            'code' => 13,
            'stock_good' => 0,
            'stock_repair' => 0,
            'stock_return' => 0,
            'stock_damaged' => 0,
            'stock_minimum' => 0,
            'stock' => 0
        ];
        $balon45 = [
            'name' => 'BALON 45KG GLP',
            'code' => 14,
            'stock_good' => 0,
            'stock_repair' => 0,
            'stock_return' => 0,
            'stock_damaged' => 0,
            'stock_minimum' => 0,
            'stock' => 0
        ];

        foreach ($articles as $article) {
            $stock = $article->stock_good
                + $article->stock_repair
                + $article->stock_return
                + $article->stock_damaged
                + $article->stock_minimum;

            $code = (int) $article->code;

            if ($code === 11) {
                $balon5['stock_good'] += $article->stock_good;
                $balon5['stock_repair'] += $article->stock_repair;
                $balon5['stock_return'] += $article->stock_return;
                $balon5['stock_damaged'] += $article->stock_damaged;
                $balon5['stock_minimum'] += $article->stock_minimum;
                $balon5['stock'] += $stock;
            } elseif ($code === 12) {
                $balon10['stock_good'] += $article->stock_good;
                $balon10['stock_repair'] += $article->stock_repair;
                $balon10['stock_return'] += $article->stock_return;
                $balon10['stock_damaged'] += $article->stock_damaged;
                $balon10['stock_minimum'] += $article->stock_minimum;
                $balon10['stock'] += $stock;
            } elseif ($code === 13) {
                $balon15['stock_good'] += $article->stock_good;
                $balon15['stock_repair'] += $article->stock_repair;
                $balon15['stock_return'] += $article->stock_return;
                $balon15['stock_damaged'] += $article->stock_damaged;
                $balon15['stock_minimum'] += $article->stock_minimum;
                $balon15['stock'] += $stock;
            } elseif ($code === 14) {
                $balon45['stock_good'] += $article->stock_good;
                $balon45['stock_repair'] += $article->stock_repair;
                $balon45['stock_return'] += $article->stock_return;
                $balon45['stock_damaged'] += $article->stock_damaged;
                $balon45['stock_minimum'] += $article->stock_minimum;
                $balon45['stock'] += $stock;
            }
        }

        $stocks_articles[] = $balon5;
        $stocks_articles[] = $balon10;
        $stocks_articles[] = $balon15;
        $stocks_articles[] = $balon45;

        return $stocks_articles;
    }

    /**
     * Exporta la hoja "Resumen" con tres secciones:
     * 1. Resumen histórico (desde 2019 hasta el año solicitado)
     * 2. Cuadro de Ingresos/Salidas del año específico
     * 3. Stock Real Actual por Clasificación (warehouse_type_id = 22)
     */
    public function exportResumenExcel(Request $request)
    {
        $year = (int) $request->input('year', date('Y'));
        $warehouse_type_id = $request->input('warehouse_type_index');
        $warehouseTypeIds = $this->resolveWarehouseTypeIds($warehouse_type_id);

        $typeColumns = [
            '10kg'  => 12,
            '5kg'   => 11,
            'm15kg' => 13,
            '45kg'  => 14,
        ];

        // --- SECCIÓN 1: RESUMEN HISTÓRICO ---
        $resumenSpec = $this->buildExactResumenSpec($year);
        $rowsResumen = $this->buildResumenRows($warehouseTypeIds, $typeColumns, $resumenSpec);

        // --- SECCIÓN 2: INGRESOS Y SALIDAS DEL AÑO ---
        $movimientosAnioSpec = $this->buildYearlyMovementSpec($year);
        $rowsAnio = $this->buildResumenRows($warehouseTypeIds, $typeColumns, $movimientosAnioSpec);

        // --- SECCIÓN 3: STOCK REAL ACTUAL ---
        $stockRealSpec = $this->buildStockRealSpec();
        $rowsStockReal = $this->buildStockRealRows(self::WAREHOUSE_TYPE_STOCK_REAL, $typeColumns, $stockRealSpec);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Resumen');

        // ========== SECCIÓN 1: RESUMEN HISTÓRICO ==========
        $sheet->setCellValue('A1', 'Inventario Cilindros');
        $sheet->setCellValue('A2', 'INVENTARIO DE CILINDROS DE PROPIEDAD DE LA EMPRESA ENVASADORA');
        $sheet->setCellValue('B2', '10 Kg');
        $sheet->setCellValue('C2', '5 KG');
        $sheet->setCellValue('D2', 'M15 Kg');
        $sheet->setCellValue('E2', '45 KG');

        $sheet->getStyle('A1:E2')->getFont()->setBold(true);
        $sheet->getStyle('B2:E2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $r = 3;
        foreach ($rowsResumen as $row) {
            $sheet->setCellValue("A{$r}", $row['concepto']);
            $sheet->setCellValue("B{$r}", $row['10kg']);
            $sheet->setCellValue("C{$r}", $row['5kg']);
            $sheet->setCellValue("D{$r}", $row['m15kg']);
            $sheet->setCellValue("E{$r}", $row['45kg']);
            $sheet->getStyle("B{$r}:E{$r}")->getNumberFormat()->setFormatCode('#,##0');
            $r++;
        }

        // TOTAL Resumen Histórico
        $lastDataRow = $r - 1;
        $sheet->setCellValue("A{$r}", 'TOTAL');
        $sheet->setCellValue("B{$r}", "=SUM(B3:B{$lastDataRow})");
        $sheet->setCellValue("C{$r}", "=SUM(C3:C{$lastDataRow})");
        $sheet->setCellValue("D{$r}", "=SUM(D3:D{$lastDataRow})");
        $sheet->setCellValue("E{$r}", "=SUM(E3:E{$lastDataRow})");
        $sheet->getStyle("A{$r}:E{$r}")->getFont()->setBold(true);
        $sheet->getStyle("A{$r}:E{$r}")->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle("B{$r}:E{$r}")->getNumberFormat()->setFormatCode('#,##0');

        $r += 3; // Espacio entre secciones

        // ========== SECCIÓN 2: INGRESOS Y SALIDAS DEL AÑO ==========
        $headerRow = $r;
        $sheet->setCellValue("A{$r}", "Ingresos {$year}");
        $sheet->setCellValue("B{$r}", "10 Kg");
        $sheet->setCellValue("C{$r}", "5 KG");
        $sheet->setCellValue("D{$r}", "M15 Kg");
        $sheet->setCellValue("E{$r}", "45 KG");
        
        $sheet->getStyle("A{$r}:E{$r}")->getFont()->setBold(true);
        $sheet->getStyle("A{$r}:E{$r}")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF1F3864');
        $sheet->getStyle("A{$r}:E{$r}")->getFont()->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle("A{$r}:E{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $r++;
        $startSumAnio = $r;

        foreach ($rowsAnio as $row) {
            if (isset($row['is_header']) && $row['is_header']) {
                $sheet->setCellValue("A{$r}", $row['concepto']);
                $sheet->getStyle("A{$r}:E{$r}")->getFont()->setBold(true);
                $sheet->getStyle("A{$r}:E{$r}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFD9E1F2');
                $r++;
                continue;
            }

            $sheet->setCellValue("A{$r}", $row['concepto']);
            $sheet->setCellValue("B{$r}", $row['10kg']);
            $sheet->setCellValue("C{$r}", $row['5kg']);
            $sheet->setCellValue("D{$r}", $row['m15kg']);
            $sheet->setCellValue("E{$r}", $row['45kg']);
            $sheet->getStyle("B{$r}:E{$r}")->getNumberFormat()->setFormatCode('#,##0');
            $r++;
        }

        // TOTAL del año
        $lastDataRowAnio = $r - 1;
        $sheet->setCellValue("A{$r}", "TOTAL {$year}");
        $sheet->setCellValue("B{$r}", "=SUM(B{$startSumAnio}:B{$lastDataRowAnio})");
        $sheet->setCellValue("C{$r}", "=SUM(C{$startSumAnio}:C{$lastDataRowAnio})");
        $sheet->setCellValue("D{$r}", "=SUM(D{$startSumAnio}:D{$lastDataRowAnio})");
        $sheet->setCellValue("E{$r}", "=SUM(E{$startSumAnio}:E{$lastDataRowAnio})");
        
        $sheet->getStyle("A{$r}:E{$r}")->getFont()->setBold(true);
        $sheet->getStyle("A{$r}:E{$r}")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF1F3864');
        $sheet->getStyle("A{$r}:E{$r}")->getFont()->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle("B{$r}:E{$r}")->getNumberFormat()->setFormatCode('#,##0');

        $r += 3; // Espacio entre secciones

        // ========== SECCIÓN 3: STOCK REAL ACTUAL ==========
        $sheet->setCellValue("A{$r}", "Cilindros Propios");
        $sheet->setCellValue("B{$r}", "10 Kg");
        $sheet->setCellValue("C{$r}", "5 Kg");
        $sheet->setCellValue("D{$r}", "M15 Kg");
        $sheet->setCellValue("E{$r}", "M45 Kg");
        
        $sheet->getStyle("A{$r}:E{$r}")->getFont()->setBold(true);
        $sheet->getStyle("A{$r}:E{$r}")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF1F3864');
        $sheet->getStyle("A{$r}:E{$r}")->getFont()->getColor()->setARGB('FFFFFFFF');

        $r++;
        $startStockReal = $r;

        foreach ($rowsStockReal as $row) {
            // Encabezados de sección
            if (isset($row['is_section_header']) && $row['is_section_header']) {
                $sheet->setCellValue("A{$r}", $row['concepto']);
                $sheet->getStyle("A{$r}:E{$r}")->getFont()->setBold(true);
                $sheet->getStyle("A{$r}:E{$r}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFFFEB9C'); // Amarillo
                $r++;
                continue;
            }

            // Subtotales
            if (isset($row['is_subtotal']) && $row['is_subtotal']) {
                $sheet->setCellValue("A{$r}", $row['concepto']);
                $sheet->setCellValue("B{$r}", $row['10kg']);
                $sheet->setCellValue("C{$r}", $row['5kg']);
                $sheet->setCellValue("D{$r}", $row['m15kg']);
                $sheet->setCellValue("E{$r}", $row['45kg']);
                $sheet->getStyle("A{$r}:E{$r}")->getFont()->setBold(true);
                $sheet->getStyle("A{$r}:E{$r}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFFFC000'); // Naranja
                $sheet->getStyle("B{$r}:E{$r}")->getNumberFormat()->setFormatCode('#,##0');
                $r++;
                continue;
            }

            // Fila de stock final verde
            if (isset($row['is_final']) && $row['is_final']) {
                $sheet->setCellValue("A{$r}", $row['concepto']);
                $sheet->setCellValue("B{$r}", $row['10kg']);
                $sheet->setCellValue("C{$r}", $row['5kg']);
                $sheet->setCellValue("D{$r}", $row['m15kg']);
                $sheet->setCellValue("E{$r}", $row['45kg']);
                $sheet->getStyle("A{$r}:E{$r}")->getFont()->setBold(true);
                $sheet->getStyle("A{$r}:E{$r}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FF92D050'); // Verde
                $sheet->getStyle("B{$r}:E{$r}")->getNumberFormat()->setFormatCode('#,##0');
                $r++;
                continue;
            }

            // Filas normales
            $sheet->setCellValue("A{$r}", $row['concepto']);
            $sheet->setCellValue("B{$r}", $row['10kg']);
            $sheet->setCellValue("C{$r}", $row['5kg']);
            $sheet->setCellValue("D{$r}", $row['m15kg']);
            $sheet->setCellValue("E{$r}", $row['45kg']);
            $sheet->getStyle("B{$r}:E{$r}")->getNumberFormat()->setFormatCode('#,##0');
            
            // Filas rojas (rechazados)
            if (isset($row['is_red']) && $row['is_red']) {
                $sheet->getStyle("A{$r}:E{$r}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFFF0000');
                $sheet->getStyle("A{$r}:E{$r}")->getFont()->getColor()->setARGB('FFFFFFFF');
            }
            
            $r++;
        }

        // Ajustar anchos de columna
        foreach (range('A', 'E') as $col) { 
            $sheet->getColumnDimension($col)->setAutoSize(true); 
        }

        $fileName = "Resumen_Inventario_{$year}.xlsx";
        $tempPath = storage_path("app/{$fileName}");
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        return response()->download($tempPath, $fileName)->deleteFileAfterSend(true);
    }

    /**
     * Define los conceptos del Resumen Histórico
     */
    private function buildExactResumenSpec(int $targetYear)
    {
        $spec = [];
        $spec[] = ['concepto' => 'INVENTARIO TOTAL DE CILINDROS AL INICIO DEL AÑO-2019', 'kind' => 'opening_balance', 'year' => 2019];

        for ($y = 2019; $y <= $targetYear; $y++) {
            $spec[] = ['concepto' => "CILINDROS NUEVOS ADQUIRIDOS DURANTE EL AÑO $y", 'kind' => 'movement_sum', 'movement_type' => 'PURCHASE_NEW', 'year' => $y, 'sign' => 1];
            $spec[] = ['concepto' => "CILINDROS EN CONDICION DE USO DURANTE EL AÑO $y", 'kind' => 'movement_sum', 'movement_type' => 'IN_USE', 'year' => $y, 'sign' => -1];
        }

        $eventos = [
            ['c' => 'AJUSTE DE INVENTARIO NOVIEMBRE 2021', 't' => 'ADJUSTMENT', 'y' => 2021, 's' => 1, 'ref' => 'NOV_2021'],
            ['c' => 'AJUSTE DE INVENTARIO DICIEMBRE 2021', 't' => 'ADJUSTMENT', 'y' => 2021, 's' => 1, 'ref' => 'DIC_2021'],
            ['c' => 'AJUSTE DE INVENTARIO ABRIL / OCTUBRE 2022', 't' => 'ADJUSTMENT', 'y' => 2022, 's' => 1, 'ref' => 'ABR_OCT_2022'],
            ['c' => 'CILINDROS DADO DE BAJA OCTUBRE 2021', 't' => 'WRITE_OFF', 'y' => 2021, 's' => -1, 'ref' => 'OCT_2021'],
            ['c' => 'CILINDROS DADO DE BAJA ENERO 2022', 't' => 'WRITE_OFF', 'y' => 2022, 's' => -1, 'ref' => 'ENE_2022'],
            ['c' => 'CILINDROS DADO DE BAJA OCTUBRE 2022 (2X1)', 't' => 'WRITE_OFF', 'y' => 2022, 's' => -1, 'ref' => 'OCT_2022_2X1'],
            ['c' => 'AJUSTE DE INVENTARIO NOVIEMBRE 2021', 't' => 'ADJUSTMENT', 'y' => 2021, 's' => 1, 'ref' => 'NOV_2021_NEG'],
            ['c' => 'CILINDROS DESCONTADOS DEL ERP / CORDIA OCTUBRE 2021', 't' => 'ERP_DISCOUNT', 'y' => 2021, 's' => -1, 'ref' => 'CORDIA_OCT_2021'],
            ['c' => 'CILINDROS DESCONTADOS DEL ERP / CORDIA ENERO 2022', 't' => 'ERP_DISCOUNT', 'y' => 2022, 's' => -1, 'ref' => 'CORDIA_ENE_2022'],
            ['c' => 'CILINDROS DESCONTADOS DEL ERP / PUNTO OCTUBRE-DICIEMBRE 2022', 't' => 'ERP_DISCOUNT', 'y' => 2022, 's' => -1, 'ref' => 'PUNTO_OCT_DIC_2022'],
        ];

        foreach ($eventos as $e) {
            if ($e['y'] <= $targetYear) {
                $spec[] = [
                    'concepto' => $e['c'], 
                    'kind' => 'movement_sum', 
                    'movement_type' => $e['t'], 
                    'year' => $e['y'], 
                    'sign' => $e['s'], 
                    'reference' => $e['ref']
                ];
            }
        }

        return $spec;
    }

    /**
     * Define los conceptos del cuadro de Ingresos/Salidas del año
     */
    private function buildYearlyMovementSpec(int $year)
    {
        return [
            ['concepto' => "CILINDROS NUEVOS ADQUIRIDOS DURANTE EL AÑO {$year}", 'kind' => 'movement_sum', 'movement_type' => 'PURCHASE_NEW', 'year' => $year, 'sign' => 1],
            ['concepto' => "AJUSTE DE CILINDROS {$year}", 'kind' => 'movement_sum', 'movement_type' => 'ADJUSTMENT', 'year' => $year, 'sign' => 1],
            ['concepto' => "INCREMENTO MASA OPERATIVA {$year}", 'kind' => 'movement_sum', 'movement_type' => 'MASA_OPERATIVA', 'year' => $year, 'sign' => 1],
            ['concepto' => "Salidas {$year}", 'is_header' => true],
            ['concepto' => "CILINDROS EN CONDICION DE USO DURANTE EL AÑO {$year}", 'kind' => 'movement_sum', 'movement_type' => 'IN_USE', 'year' => $year, 'sign' => -1],
            ['concepto' => "CILINDROS DESCONTADOS DEL ERP {$year} (BAJA CONTABLE JUNIO {$year})", 'kind' => 'movement_sum', 'movement_type' => 'ERP_DISCOUNT', 'year' => $year, 'sign' => -1, 'reference' => 'BAJA_CONTABLE_JUNIO'],
        ];
    }

    /**
     * NUEVO: Define la estructura del Stock Real Actual
     */
    private function buildStockRealSpec()
    {
        return [
            ['concepto' => 'INVENTARIO DE CILINDROS DE PROPIEDAD DE LA EMPRESA ENVASADORA', 'field' => null],
            ['concepto' => 'OPERATIVOS EN PLANTA VACIOS', 'field' => 'stock_operativo_planta_vacios'],
            ['concepto' => 'OPERATIVOS EN PLANTA VACIOS-NUEVOS-COSINERMIN', 'field' => 'stock_operativo_planta_nuevos_cosinermin'],
            ['concepto' => 'OPERATIVOS EN PLANTA VACIOS BURROS', 'field' => 'stock_operativo_planta_nuevos_burros'],
            ['concepto' => 'OPERATIVOS EN PLANTA LLENOS', 'field' => 'stock_operativo_planta_llenos'],
            ['concepto' => 'OPERATIVOS EN LOS CAMIONES POR DESCARGAR EN PLANTA', 'field' => 'stock_operativo_camiones_descargar'],
            
            ['concepto' => 'INOPERATIVOS EN PLANTA POR TRASEGAR', 'field' => 'stock_inoperativo_trasegar', 'is_section_header' => true],
            ['concepto' => 'INOPERATIVOS EN PLANTA POR MANTTO INTERNO (PIN-VALVULA)', 'field' => 'stock_inoperativo_mantto_interno'],
            ['concepto' => 'INOPERATIVOS EN PLANTA POR MANTTO EXTERNO (HUESO)', 'field' => 'stock_inoperativo_mantto_externo'],
            ['concepto' => 'INOPERATIVOS EN PLANTA POR MANTTO EXTERNO (ASAS-BASE)', 'field' => 'stock_inoperativo_mantto_externo'],
            ['concepto' => 'INOPERATIVOS EN PLANTA (POR DAR DE BAJA)', 'field' => 'stock_inoperativo_dar_baja'],
            
            ['concepto' => 'EN GRANALLADO (Cochera)', 'field' => 'stock_granallado', 'is_section_header' => true],
            ['concepto' => 'EN MOTORIZADO (Cochera)', 'field' => 'stock_motorizado'],
            ['concepto' => 'CILINDROS EN BENIGAS (CANJE 2.5X1 ) OCTUBRE', 'field' => 'stock_benigas', 'is_red' => true],
            ['concepto' => 'CILINDROS EN UNIDAD BACK UP (Cochera)', 'field' => 'stock_unidad_backup'],
            ['concepto' => 'EN TALLER DE MANTENIMIENTO BENIGAS', 'field' => 'stock_taller_mantto_benigas'],
            ['concepto' => 'EN TALLER DE MANTENIMIENTO VGT', 'field' => 'stock_taller_mantto_vgt'],
            ['concepto' => 'EN TALLER DE GRANALLADO (VGT)', 'field' => 'stock_taller_granallado'],
            
            ['concepto' => 'EN LOS CAMIONES (MAY-VD)', 'field' => 'stock_camiones_prestados', 'is_section_header' => true],
            ['concepto' => 'PRESTADOS', 'field' => 'stock_camiones_prestados'],
            ['concepto' => 'RECHAZADOS AL CIERRE DEL INVENTARIO (Rechazos burros, asas, base etc.)', 'field' => 'stock_camiones_rechazados', 'is_red' => true],
            
            ['concepto' => 'CONSIGNADOS EN LOS CLIENTES (MAYORISTA)', 'field' => 'stock_consignado_mayorista', 'is_section_header' => true],
            ['concepto' => 'CONSIGNADOS EN LOS CLIENTES (CORDIA)', 'field' => 'stock_consignado_cordia'],
            ['concepto' => 'CONDICION DE USO AL CREDITO', 'field' => 'stock_condicion_uso_credito'],
            ['concepto' => 'TOTAL EN LIBROS PDD + AJENOS', 'is_subtotal' => true],
            
            ['concepto' => 'Cilindros Ajenos', 'is_section_header' => true],
            ['concepto' => 'AJENOS', 'field' => 'stock_ajenos'],
            ['concepto' => 'PRESTAMOS SOL-GAS', 'field' => 'stock_prestamos_solgas'],
            ['concepto' => 'OBSERVADOS (Por revision)', 'field' => 'stock_observados'],
            
            ['concepto' => 'STOCK EN LIBROS PDD', 'is_final' => true],
        ];
    }

    /**
     * NUEVO: Construye las filas del Stock Real desde la tabla articles
     */
    private function buildStockRealRows(int $warehouseTypeId, array $typeColumns, array $spec)
    {
        // Obtener artículos de la planta específica
        $articles = Article::where('warehouse_type_id', $warehouseTypeId)
            ->where('group_id', 7)
            ->whereIn('code', array_values($typeColumns))
            ->get()
            ->keyBy('code');

        $rows = [];
        $totals = ['10kg' => 0, '5kg' => 0, 'm15kg' => 0, '45kg' => 0];
        $totalsAjenos = ['10kg' => 0, '5kg' => 0, 'm15kg' => 0, '45kg' => 0];

        foreach ($spec as $item) {
            $row = [
                'concepto' => $item['concepto'],
                '10kg' => 0,
                '5kg' => 0,
                'm15kg' => 0,
                '45kg' => 0,
            ];

            // Marcar encabezados de sección
            if (isset($item['is_section_header'])) {
                $row['is_section_header'] = true;
            }

            // Marcar subtotales
            if (isset($item['is_subtotal'])) {
                $row['is_subtotal'] = true;
                $row['10kg'] = $totals['10kg'];
                $row['5kg'] = $totals['5kg'];
                $row['m15kg'] = $totals['m15kg'];
                $row['45kg'] = $totals['45kg'];
                $rows[] = $row;
                continue;
            }

            // Marcar fila final (verde)
            if (isset($item['is_final'])) {
                $row['is_final'] = true;
                $row['10kg'] = $totals['10kg'] - $totalsAjenos['10kg'];
                $row['5kg'] = $totals['5kg'] - $totalsAjenos['5kg'];
                $row['m15kg'] = $totals['m15kg'] - $totalsAjenos['m15kg'];
                $row['45kg'] = $totals['45kg'] - $totalsAjenos['45kg'];
                $rows[] = $row;
                continue;
            }

            // Marcar filas rojas
            if (isset($item['is_red'])) {
                $row['is_red'] = true;
            }

            // Obtener valores de los campos
            if (isset($item['field']) && $item['field']) {
                foreach ($typeColumns as $colKey => $code) {
                    $article = $articles->get($code);
                    if ($article && isset($article->{$item['field']})) {
                        $value = (int) $article->{$item['field']};
                        $row[$colKey] = $value;
                        
                        // Acumular en totales (excepto si es sección de ajenos)
                        if (strpos($item['concepto'], 'AJENOS') === false && 
                            strpos($item['concepto'], 'PRESTAMOS') === false && 
                            strpos($item['concepto'], 'OBSERVADOS') === false) {
                            $totals[$colKey] += $value;
                        } else {
                            $totalsAjenos[$colKey] += $value;
                        }
                    }
                }
            }

            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * Resuelve IDs de plantas
     */
    private function resolveWarehouseTypeIds($warehouse_type_id)
    {
        if ($warehouse_type_id) {
            return WarehouseType::select('id')
                ->where('id', $warehouse_type_id)
                ->pluck('id')
                ->toArray();
        }

        return WarehouseType::select('id')
            ->where('type', 3)
            ->pluck('id')
            ->toArray();
    }

    /**
     * Arma las filas del Resumen en base a spec + tablas históricas
     */
    private function buildResumenRows(array $warehouseTypeIds, array $typeColumns, array $resumenSpec)
    {
        $rows = [];

        foreach ($resumenSpec as $item) {
            if (isset($item['is_header']) && $item['is_header']) {
                $rows[] = [
                    'concepto' => $item['concepto'],
                    'is_header' => true,
                    '10kg' => 0,
                    '5kg' => 0,
                    'm15kg' => 0,
                    '45kg' => 0,
                ];
                continue;
            }

            $row = [
                'concepto' => $item['concepto'],
                '10kg'  => 0,
                '5kg'   => 0,
                'm15kg' => 0,
                '45kg'  => 0,
            ];

            if ($item['kind'] === 'opening_balance') {
                $balancesByCode = $this->sumOpeningBalancesByCode(
                    $warehouseTypeIds,
                    array_values($typeColumns),
                    (int) $item['year']
                );

                foreach ($typeColumns as $colKey => $code) {
                    $row[$colKey] = (int) ($balancesByCode[$code] ?? 0);
                }
            }

            if ($item['kind'] === 'movement_sum') {
                $sumByCode = $this->sumMovementsByCode(
                    $warehouseTypeIds,
                    array_values($typeColumns),
                    $item['movement_type'],
                    $item['year'],
                    $item['reference'] ?? null
                );

                $sign = (int) ($item['sign'] ?? +1);

                foreach ($typeColumns as $colKey => $code) {
                    $row[$colKey] = $sign * (int) ($sumByCode[$code] ?? 0);
                }
            }

            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * Suma saldos iniciales
     */
    private function sumOpeningBalancesByCode(array $warehouseTypeIds, array $codes, int $year)
    {
        return DB::table('cylinder_opening_balances')
            ->select('cylinder_code', DB::raw('SUM(qty) as total'))
            ->whereIn('warehouse_type_id', $warehouseTypeIds)
            ->where('year', $year)
            ->whereIn('cylinder_code', $codes)
            ->groupBy('cylinder_code')
            ->pluck('total', 'cylinder_code')
            ->toArray();
    }

    /**
     * Suma movimientos
     */
    private function sumMovementsByCode(array $warehouseTypeIds, array $codes, string $type, int $year, $reference = null)
    {
        $query = DB::table('cylinder_movements')
            ->select('cylinder_code', DB::raw('SUM(qty) as total'))
            ->whereIn('warehouse_type_id', $warehouseTypeIds)
            ->where('movement_type', $type)
            ->whereYear('movement_date', $year)
            ->whereIn('cylinder_code', $codes);

        if ($reference) {
            $query->where('reference', $reference);
        }

        return $query->groupBy('cylinder_code')->pluck('total', 'cylinder_code')->toArray();
    }
}