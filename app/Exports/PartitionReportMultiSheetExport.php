<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PartitionReportMultiSheetExport implements WithMultipleSheets
{
    protected $datetime;
    protected $analisis_costo;

    public function __construct($datetime, $analisis_costo)
    {
        $this->datetime = $datetime;
        $this->analisis_costo = $analisis_costo;
    }

    public function sheets(): array
    {
        return [
            'Analisis Costo' => new PartitionReportSheetAnalysis($this->datetime, $this->analisis_costo)
        ];
    }
}
