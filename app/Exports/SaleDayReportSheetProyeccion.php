<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Carbon\Carbon;

class SaleDayReportSheetProyeccion implements
    FromCollection,
    WithMapping,
    WithTitle,
    WithEvents,
    WithColumnFormatting
{
    protected $proyeccion_envasado;
    protected $proyeccion_granel;
    protected $final_date;
    protected $dias;

    public function __construct($proyeccion_envasado, $proyeccion_granel, $final_date)
    {
        $this->proyeccion_envasado = $proyeccion_envasado;
        $this->proyeccion_granel = $proyeccion_granel;
        $this->final_date = $final_date;

        $this->dias = range(1, (int) date('d', strtotime($final_date)));
    }

    public function title(): string
    {
        return 'Proyeccion';
    }

    public function collection()
    {
        $result = collect();

        // 🔥 4 SUB-TABLAS DE ENVASADO (una por sector)
        $sectores = $this->proyeccion_envasado->groupBy('sector_id');

        $primerSector = true;
        foreach ($sectores as $sectorId => $sectorData) {
            if (!$primerSector) {
                $result->push((object)['canal' => '', 'ruta' => '']);
            }
            $result = $result->merge(
                $this->buildBlock($sectorData, 'ENVASADO')
            );
            $primerSector = false;
        }

        // 🔥 ESPACIO entre envasado y granel
        $result->push((object)['canal' => '', 'ruta' => '']);

        // 🔥 TABLA GRANEL
        $result = $result->merge(
            $this->buildBlock($this->proyeccion_granel, 'GRANEL')
        );

        return $result;
    }

    private function buildBlock($data, $tipo)
    {
        $result = collect();

        if ($tipo === 'GRANEL') {
            $grouped = $data->groupBy(['manager_name', 'channel_name']);
        } else {
            $grouped = $data->groupBy(['channel_name', 'ruta']);
        }

        Carbon::setLocale('es');
        $mes = ucfirst(Carbon::parse($this->final_date)->translatedFormat('F'));

        // 🔥 HEADER VISUAL PARA ENVASADO (nombre del sector)
        if ($tipo === 'ENVASADO') {
            $sectorName = strtoupper($data->first()->sector_name ?? 'SECTOR');

            $headerRow = [
                'canal' => $sectorName,
                'ruta'  => 'RUTA'
            ];

            foreach ($this->dias as $dia) {
                $headerRow[$dia] = "{$dia}-{$mes}";
            }

            $headerRow['total'] = 'TOTAL';
            $result->push((object)$headerRow);
        }

        // 🔥 HEADER VISUAL PARA GRANEL
        if ($tipo === 'GRANEL') {
            $headerRow = [
                'canal' => 'SUPERVISOR',
                'ruta'  => 'CANAL'
            ];

            foreach ($this->dias as $dia) {
                $headerRow[$dia] = "{$dia}-{$mes}";
            }

            $headerRow['total'] = 'TOTAL';
            $result->push((object)$headerRow);
        }

        // 🔥 ACUMULADORES
        $totales = array_fill_keys($this->dias, 0);
        $totales['total'] = 0;

        // Construir filas en un array temporal para poder ordenarlas por RUTA numérica
        $rows = [];

        foreach ($grouped as $nivel1 => $nivel2) {

            foreach ($nivel2 as $nivelFinal => $items) {

                $row = [
                    'canal' => $nivel1,
                    'ruta'  => $nivelFinal
                ];

                foreach ($this->dias as $dia) {
                    $row[$dia] = 0;
                }

                foreach ($items as $item) {
                    $row[$item->dia] += $item->total;
                }

                $row['total'] = collect($row)->only($this->dias)->sum();

                // Acumular totales globales (independiente del orden final)
                foreach ($this->dias as $dia) {
                    $totales[$dia] += $row[$dia];
                }
                $totales['total'] += $row['total'];

                $rows[] = (object)$row;
            }
        }

        // Función auxiliar para extraer el número de la ruta
        $extractRouteNumber = function ($rutaLabel) {
            if (!is_string($rutaLabel)) {
                return PHP_INT_MAX;
            }
            // Buscar el primer número en la cadena
            if (preg_match('/(\d+)/', $rutaLabel, $m)) {
                return (int)$m[1];
            }
            // Si no hay número, devolver un valor muy grande para que quede al final
            return PHP_INT_MAX;
        };

        // Ordenar las filas por número de ruta (numérico). Si no tiene número, quedan al final y se ordenan por texto.
        $rows = collect($rows)->sortBy(function ($r) use ($extractRouteNumber) {
            $num = $extractRouteNumber($r->ruta);
            if ($num !== PHP_INT_MAX) {
                return $num; // orden numérico por la parte numérica
            }
            // fallback: orden alfabético si no contiene número
            return $r->ruta;
        })->values();

        // Ahora empujar filas ordenadas al resultado
        foreach ($rows as $r) {
            $result->push($r);
        }

        // 🔥 FILA TOTAL
        $totalRow = [
            'canal' => 'TOTAL',
            'ruta'  => ''
        ];

        foreach ($this->dias as $dia) {
            $totalRow[$dia] = $totales[$dia];
        }

        $totalRow['total'] = $totales['total'];
        $result->push((object)$totalRow);

        return $result;
    }
    public function map($row): array
    {
        $data = [
            $row->canal ?? '',
            $row->ruta  ?? ''
        ];

        $esHeader = ($row->canal === 'SUPERVISOR' || ($row->ruta ?? '') === 'RUTA');

        foreach ($this->dias as $dia) {

            $valor = $row->$dia ?? '';

            if ($esHeader) {
                $data[] = $valor;
            } else {
                $data[] = $valor === '' ? '' : (float)$valor;
            }
        }

        if ($esHeader) {
            $data[] = $row->total ?? '';
        } else {
            $data[] = isset($row->total) ? (float)$row->total : '';
        }

        foreach ($data as $i => $raw) {

            if ($i < 2) {
                continue;
            }

            // 🔥 NO llenar la fila vacía separadora
            if (($row->canal ?? '') === '' && ($row->ruta ?? '') === '') {
                continue;
            }

            if (is_string($raw)) {
                $rawClean = trim(str_replace("\xC2\xA0", ' ', $raw));
            } else {
                $rawClean = $raw;
            }

            if (
                $rawClean === null ||
                $rawClean === '' ||
                $rawClean === '0' ||
                (is_numeric($rawClean) && $rawClean == 0)
            ) {
                $data[$i] = '0';
            } else {
                $data[$i] = $rawClean;
            }
        }

        return $data;
    }

    public function columnFormats(): array
    {
        $formats = [];

        for ($col = 3; $col <= count($this->dias) + 2; $col++) {
            $formats[Coordinate::stringFromColumnIndex($col)] = '#,##0.0';
        }

        return $formats;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function ($event) {

                $sheet = $event->sheet->getDelegate();
                $sheet->getTabColor()->setRGB('FF1212');
                $event->sheet->getSheetView()->setZoomScale(90);

                $highestColumn      = $sheet->getHighestDataColumn();
                $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
                $lastColLetter      = Coordinate::stringFromColumnIndex($highestColumnIndex);
                $highestRow         = $sheet->getHighestRow();

                // 🔥 ANCHOS
                $sheet->getColumnDimension('A')->setWidth(25);
                $sheet->getColumnDimension('B')->setWidth(20);

                for ($col = 3; $col <= $highestColumnIndex; $col++) {
                    $sheet->getColumnDimension(
                        Coordinate::stringFromColumnIndex($col)
                    )->setWidth(12);
                }

                // 🔥 HEADER PRINCIPAL (fila 1 = headings de Laravel Excel)
                $sheet->getStyle('A1:B1')->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '007BFF']],
                    'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
                ]);

                $sheet->getStyle("C1:{$lastColLetter}1")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '28A745']],
                    'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
                ]);

                // 🔥 BUSCAR FILA SUPERVISOR (inicio tabla GRANEL)
                $secondHeaderRow  = 0;
                $firstTableLastRow = 0;

                for ($row = 1; $row <= $highestRow; $row++) {
                    $val = trim((string)$sheet->getCell("A{$row}")->getValue());
                    if ($val === 'SUPERVISOR') {
                        $secondHeaderRow   = $row;
                        $firstTableLastRow = $row - 2;
                        break;
                    }
                }
                for ($row = 1; $row <= $highestRow; $row++) {
                    $valA = trim((string)$sheet->getCell("A{$row}")->getValue());
                    $valB = trim((string)$sheet->getCell("B{$row}")->getValue());

                    // Saltar filas completamente vacías
                    if ($valA === '' && $valB === '') {
                        continue;
                    }

                    $sheet->getStyle("{$lastColLetter}{$row}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F7C4BC']],
                        'font' => ['bold' => true],
                    ]);
                }

                // 🔥 FILAS ESPECIALES
                for ($row = 2; $row <= $highestRow; $row++) {

                    $val  = trim((string)$sheet->getCell("A{$row}")->getValue());
                    $valB = trim((string)$sheet->getCell("B{$row}")->getValue());

                    // FILA TOTAL
                    if ($val === 'TOTAL') {
                        $sheet->getStyle("A{$row}:{$lastColLetter}{$row}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F7C4BC']],
                            'font' => ['bold' => true],
                        ]);
                    }

                    // 🔥 HEADER DE SECTOR ENVASADO (col B = "RUTA")
                    if ($val !== '' && $val !== 'TOTAL' && $val !== 'SUPERVISOR' && $valB === 'RUTA') {

                        $sheet->getStyle("A{$row}:B{$row}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '007BFF']],
                            'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
                        ]);

                        $sheet->getStyle("C{$row}:{$lastColLetter}{$row}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '28A745']],
                            'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
                        ]);
                    }

                    // 🔥 HEADER TABLA GRANEL
                    if ($val === 'SUPERVISOR') {

                        $sheet->getStyle("A{$row}:B{$row}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '007BFF']],
                            'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
                        ]);

                        $sheet->getStyle("C{$row}:{$lastColLetter}{$row}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '28A745']],
                            'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
                        ]);
                    }
                }

                // 🔥 FORMATO NUMÉRICO
                $sheet->getStyle("C2:{$lastColLetter}{$highestRow}")
                    ->getNumberFormat()
                    ->setFormatCode('#,##0.0');

                $sheet->freezePane('C2');
            },
        ];
    }
}
