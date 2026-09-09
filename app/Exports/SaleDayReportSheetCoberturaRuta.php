<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class SaleDayReportSheetCoberturaRuta implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithTitle,
    WithEvents
{
    protected $data;
    protected $meses;
    protected $canalRows = []; // [rowNumber => canal]

    public function __construct($data)
    {
        $this->data = $data;

        // 🔥 Meses dinámicos según data
        $this->meses = $data->pluck('mes')
            ->unique()
            ->sort()
            ->values()
            ->toArray();
    }

    public function title(): string
    {
        return 'Cobertura Ruta';
    }

    public function collection()
    {
        // agrupar por CANAL (sector_name) -> SUBCANAL (channel_name) -> RUTA
        $grouped = $this->data->groupBy(['sector_name', 'channel_name', 'ruta']);

        $result = collect();
        $rowNumber = 2; // fila 1 = headings

        foreach ($grouped as $sector => $channels) {
            foreach ($channels as $channel => $rutas) {

                foreach ($rutas as $ruta => $items) {

                    $row = [
                        'canal' => $sector,
                        'subcanal' => $channel,
                        'ruta' => $ruta,
                    ];

                    // inicializar meses dinámicos
                    foreach ($this->meses as $mes) {
                        $row[$mes] = 0;
                    }

                    foreach ($items as $item) {
                        $row[$item->mes] += $item->total;
                    }

                    // total dinámico
                    $row['total'] = collect($row)->only($this->meses)->sum();

                    // registrar fila por CANAL (sector) para separadores
                    $this->canalRows[$rowNumber] = $sector;
                    $rowNumber++;

                    $result->push((object)$row);
                }
            }
        }

        return $result;
    }
    public function headings(): array
    {
        return array_merge(['CANAL', 'SUBCANAL', 'RUTA'], $this->meses, ['TOTAL']);
    }

    public function map($row): array
    {
        $data = [
            $row->canal,
            $row->subcanal,
            $row->ruta,
        ];

        // meses dinámicos
        foreach ($this->meses as $mes) {
            $data[] = $row->$mes ?? 0;
        }

        $data[] = $row->total;

        // normalizar valores (manteniendo tu lógica previa)
        foreach ($data as $i => $raw) {
            if ($i < 3) continue; // saltar CANAL, SUBCANAL, RUTA

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
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function ($event) {

                $sheet = $event->sheet->getDelegate();

                $sheet->setShowGridlines(false);
                $sheet->getTabColor()->setRGB('FF1212');
                $event->sheet->getSheetView()->setZoomScale(90);

                $highestColumn = $sheet->getHighestDataColumn();
                $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
                $lastColLetter = Coordinate::stringFromColumnIndex($highestColumnIndex);

                // Altura header
                $sheet->getRowDimension(1)->setRowHeight(-1);

                // Anchos: A CANAL, B SUBCANAL, C RUTA
                $sheet->getColumnDimension('A')->setWidth(25); // CANAL
                $sheet->getColumnDimension('B')->setWidth(25); // SUBCANAL
                $sheet->getColumnDimension('C')->setWidth(20); // RUTA

                // Columnas de meses y total: ancho por defecto
                for ($col = 4; $col <= $highestColumnIndex; $col++) {
                    $letter = Coordinate::stringFromColumnIndex($col);
                    $sheet->getColumnDimension($letter)->setWidth(10);
                }

                // 🎨 Encabezado A:C (AZUL)
                $sheet->getStyle('A1:C1')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '007BFF'],
                    ],
                    'font' => [
                        'color' => ['rgb' => 'FFFFFF'],
                        'bold' => true
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Si hay al menos una columna de meses (col D = índice 4)
                if ($highestColumnIndex >= 4) {
                    // 🎨 MESES (VERDE) desde D1 hasta la penúltima columna
                    // NOTA: aplicamos verde hasta la última columna; luego sobrescribimos TOTAL en rojo
                    $sheet->getStyle('D1:' . $lastColLetter . '1')->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => '28A745'],
                        ],
                        'font' => [
                            'color' => ['rgb' => 'FFFFFF'],
                            'bold' => true
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);
                }

                // 🎨 TOTAL (ROJO) - última columna (si existe)
                if ($highestColumnIndex >= 1) {
                    $sheet->getStyle($lastColLetter . '1')->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'DC3545'],
                        ],
                        'font' => [
                            'color' => ['rgb' => 'FFFFFF'],
                            'bold' => true
                        ],
                    ]);
                }

                // ❌ quitar borde raro del header
                $sheet->getStyle('A1:' . $lastColLetter . '1')
                    ->getBorders()
                    ->getBottom()
                    ->setBorderStyle(Border::BORDER_NONE);

                // 🔲 Línea separadora inferior cuando cambia el CANAL (sector)
                // $this->canalRows debe contener [rowNumber => canalName]
                foreach ($this->canalRows as $rowNum => $canal) {
                    $nextCanal = $this->canalRows[$rowNum + 1] ?? null;
                    if ($nextCanal !== $canal) {
                        $sheet->getStyle("A{$rowNum}:{$lastColLetter}{$rowNum}")
                            ->getBorders()
                            ->getBottom()
                            ->setBorderStyle(Border::BORDER_MEDIUM);
                    }
                }

                // Congelar las primeras 3 columnas (CANAL, SUBCANAL, RUTA)
                // La celda D2 es la primera celda no congelada (columna 4)
                $sheet->freezePane('D2');
            },
        ];
    }
}
