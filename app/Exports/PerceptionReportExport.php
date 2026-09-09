<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;

class PerceptionReportExport implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithMapping
{
    protected $collection;

    public function __construct(array $collection)
    {
        $this->collection = $collection;
    }

    public function array(): array
    {
        return $this->collection;
    }

    public function title(): string
    {
        return 'Reporte de Percepciones';
    }

    public function headings(): array
    {
        return [
            'Serie',
            'N°',
            'Fecha Emisión',
            'RUC/DNI/CE',
            'Razón Social',
            'Total Venta',
            'Percepción %',
            'Percepción',
            'Total Percepción',
            'Serie de Ref.',
            'N° de Ref.',
            'Fecha Ref.',
            'Fecha de Pago.',
            'Efact'
        ];
    }

    public function map($collection): array
    {
        return [
            $collection['serie_number'],
            $collection['voucher_number'],
            $collection['issue_date'],
            $collection['client_document_number'],
            $collection['client_name'],
            $collection['total'],
            $collection['igv_perception_percentage'],
            $collection['percepcion'],
            $collection['total_perception'],
            $collection['credit_note_reference_serie'],
            $collection['credit_note_reference_number'],
            $collection['issue_date'],
            $collection['shipping_date'],
            $collection['ose_status'],
        ];
    }
}
