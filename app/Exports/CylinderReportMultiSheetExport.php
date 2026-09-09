<?php

namespace App\Exports;

use Carbon\CarbonImmutable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CylinderReportMultiSheetExport implements WithMultipleSheets
{
    protected $datetime;
    protected $elements;
    protected $elements_2;
    protected $despachos;
    protected $devoluciones;
    protected $initial_date;
    protected $final_date;

    public function __construct($datetime, $elements, $elements_2, $despachos, $devoluciones, $initial_date, $final_date)
    {
        $this->datetime     = $datetime;
        $this->elements     = $elements;
        $this->elements_2   = $elements_2;
        $this->despachos    = $despachos;
        $this->devoluciones = $devoluciones;
        $this->initial_date = $initial_date;
        $this->final_date   = $final_date;
    }

    public function sheets(): array
    {
        $sheets = ['DATA' => new CylinderDataReportSheet($this->datetime, $this->elements)];
        $sheets['RESUMEN'] = new CylinderResumenReportSheet($this->initial_date, $this->final_date, $this->despachos, $this->devoluciones);

        // Generar un sheet por cada día del rango
        $start = CarbonImmutable::parse($this->initial_date);
        $end   = CarbonImmutable::parse($this->final_date);

        // En CylinderReportMultiSheetExport.php dentro del while:

        while ($start->lte($end)) {
            // Generar el nombre de la hoja con formato numérico
            $dayLabel = $start->format('Y-m-d');

            // Comparar directamente si created_at_date ya está en formato Y-m-d
            $comparisonDate = $start->format('Y-m-d');

            // Filtrar elements_2 solo con los de ese día
            $dayElements = $this->elements_2->filter(function ($item) use ($comparisonDate) {
                return isset($item->created_at_date) && $item->created_at_date === $comparisonDate;
            });

            // Pasar el $dayLabel dinámico al constructor
            $sheets[$dayLabel] = new CylinderDayReportSheet($this->datetime, $dayElements, $dayLabel);

            $start = $start->addDay();
        }

        return $sheets;
    }
}
