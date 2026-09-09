<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ListPriceReportMultiSheetExport implements WithMultipleSheets
{
    protected $prices;
    protected $client_sector_id;

    public function __construct($prices, $client_sector_id)
    {
        $this->prices = $prices;
        $this->client_sector_id = $client_sector_id;
    }
    public function sheets(): array
    {
        if ($this->client_sector_id == 1) {
            return [
                'Lista' => new ReportSheetListPriceEnvasado($this->prices),
            ];
        } elseif ($this->client_sector_id == 5) {
            return [
                'Lista' => new ReportSheetListPriceComercio($this->prices),
            ];
        }

        return [];
    }
}
