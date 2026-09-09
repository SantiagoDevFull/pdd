<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SaleMarginReportMultiSheetExport implements WithMultipleSheets
{
    protected $base;
    protected $total_venta_tm;
    protected $total_venta_soles;
    protected $precios;
    protected $kg_soles;
    protected $tm_ruta;
    protected $resultado;
    protected $dias_totales;
    protected $dias_avance;
    protected $quedan;
    protected $mesAnio;
    protected $months;


    public function __construct($base, $total_venta_tm, $total_venta_soles, $precios, $kg_soles, $tm_ruta, $resultado, $dias_totales, $dias_avance, $quedan, $mesAnio, $months)
    {
        $this->base = $base;
        $this->total_venta_tm = $total_venta_tm;
        $this->total_venta_soles = $total_venta_soles;
        $this->precios = $precios;
        $this->kg_soles = $kg_soles;
        $this->tm_ruta = $tm_ruta;
        $this->resultado = $resultado;
        $this->dias_totales = $dias_totales;
        $this->dias_avance = $dias_avance;
        $this->quedan = $quedan;
        $this->mesAnio = $mesAnio;
        $this->months = $months;
    }
    public function sheets(): array
    {
        return [
            'BASE' => new SaleMaginReportExport($this->base),
            $this->mesAnio => new SaleMarginReportMonthExport($this->total_venta_tm, $this->total_venta_soles, $this->precios, $this->kg_soles, $this->tm_ruta, $this->resultado),
            'MARGEN' => new SaleMarginReportMarginExport($this->dias_totales, $this->dias_avance, $this->quedan, $this->months),
        ];
    }
}
