<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PortfolioLocalReportMultiSheetExport implements WithMultipleSheets
{
    protected $elements;
    protected $finalDate;
    public function __construct($elements, $finalDate)
    {
        $this->elements = $elements;
        $this->finalDate = $finalDate;
    }
    public function sheets(): array
    {
        return [
            'Cartera Clientes Locales' => new ReportSheetPortfolioLocal($this->elements, $this->finalDate),
        ];
    }
}
