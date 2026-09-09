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

class SaleDayReportSheetDetalle implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize, // Esto hace que las columnas sean responsivas
    WithTitle,
    WithColumnFormatting,
    WithStyles,
    WithEvents
{
    protected $datetime;
    protected $detalle;
    protected $resumen_costo_glp;

    public function __construct($datetime, $detalle, $resumen_costo_glp)
    {
        $this->datetime = $datetime;
        $this->detalle = $detalle;
        $this->resumen_costo_glp = $resumen_costo_glp;
    }

    public function title(): string
    {
        return 'Detalle';
    }

    public function collection()
    {
        return $this->detalle;
    }

    public function headings(): array
    {
        return [
            'Mes',
            'Año',
            '#',
            'Compañía',
            'Fecha de Despacho',
            'Unidad',
            'Canal',
            'Sub Canal',
            'Ruta',
            'Tipo',
            '# Serie',
            '# Documento',
            'ID',
            'Razón Social',
            'Artículo',
            'TM',
            'Precio',
            'Total',
            '# de Parte',
            'Tipo Movimiento',
            'Guía Física',
            'Guía Electrónica',
            'Placa',
            'Negocio',
            'Area',
            'Zona',
            'Sector',
            'Distrito',
            'Provincia',
            'Departamento',
            'Vendedor',
            'Supervisor',
            'Grupo',
            'Estado',
            'Potencial',
            'Método de Pago',
            'Bonificación',
            'Marca',
            'Producto',
            'CxGLP',
            'Cx Aprox',
            'C.Total',
            'MB'
        ];
    }

    public function map($detalle): array
    {
        static $count = 0;
        $count++;

        return [
            $detalle->mes,
            $detalle->anio,
            $count,
            $detalle->company_short_name,
            $detalle->fecha_despacho,
            $detalle->business_unit_name,
            $detalle->client_sector_name,
            $detalle->client_channel_name,
            $detalle->client_route_id,
            $detalle->warehouse_document_type_short_name,
            $detalle->referral_serie_number,
            $detalle->referral_voucher_number,
            $detalle->client_id,
            $detalle->client_name,
            $detalle->article_name,
            (float)$detalle->toneladas,
            (float)$detalle->price,
            (float)$detalle->total,
            $detalle->movement_number,
            $detalle->movement_type_name,
            $detalle->guia_fisica,
            $detalle->guia_electronica,
            $detalle->license_plate_2,
            $detalle->client_type_name,
            $detalle->client_zone_name,
            $detalle->client_zona,
            $detalle->client_sector,
            $detalle->district,
            $detalle->province,
            $detalle->department,
            $detalle->vendedor,
            $detalle->supervisor,
            $detalle->grupo,
            $detalle->estado,
            $detalle->potencial,
            $detalle->payment_name,
            $detalle->bonificacion,
            $detalle->marca,
            $detalle->convertion,
            $this->resumen_costo_glp
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:B1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '198754'], // verde
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        $sheet->getStyle('C1:AK1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '00057A'], // azul
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        $sheet->getStyle('AL1:AQ1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '198754'], // verde
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->getTabColor()->setRGB('00057A');
                $event->sheet->getDelegate()->freezePane('A2');
                $event->sheet->setAutoFilter('A1:AQ1');
                $event->sheet->getSheetView()->setZoomScale(90);

                $lastRow = $event->sheet->getHighestRow();

                for ($row = 2; $row <= $lastRow; $row++) {

                    $event->sheet->setCellValue(
                        "AP{$row}",
                        "=AN{$row}+AO{$row}"
                    );

                    $event->sheet->setCellValue(
                        "AQ{$row}",
                        "=IFERROR((R{$row}/(P{$row}*100)-AP{$row})*(P{$row}*100),0)"
                    );
                }
            },
        ];
    }

    public function columnFormats(): array
    {
        return [
            'P' => '#,##0.00',
            'Q' => '#,##0.00',
            'R' => '#,##0.00',
            'AK' => '#,##0.00',
            'AN' => '#,##0.00',
            'AO' => '#,##0.00',
            'AP' => '#,##0.00',
            'AQ' => '#,##0.00',
        ];
    }
}
