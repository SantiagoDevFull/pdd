<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SaleDayReportMultiSheetExport implements WithMultipleSheets
{
    protected $dias_totales;
    protected $dias_avance;
    protected $quedan;
    protected $datetime;
    protected $mes_actual;
    protected $mes_anterior;
    protected $detalle;
    protected $analisis_costo;
    protected $resumen_costo_glp_anterior;
    protected $resumen;
    protected $resumen_costo_glp;
    protected $resumen_2;
    protected $resumen_3;
    protected $resumen_4;
    protected $resumen_5;
    protected $cobertura_1;
    protected $cobertura_2;
    protected $proyeccion_envasado;
    protected $proyeccion_granel;

    public function __construct(
        $dias_totales,
        $dias_avance,
        $quedan,
        $datetime,
        $mes_actual,
        $mes_anterior,
        $detalle,
        $analisis_costo,
        $resumen_costo_glp_anterior,
        $resumen,
        $resumen_costo_glp,
        $resumen_2,
        $resumen_3,
        $resumen_4,
        $resumen_5,
        $cobertura_1,
        $cobertura_2,
        $proyeccion_envasado,
        $proyeccion_granel
    ) {
        $this->dias_totales = $dias_totales;
        $this->dias_avance = $dias_avance;
        $this->quedan = $quedan;
        $this->datetime = $datetime;
        $this->mes_actual = $mes_actual;
        $this->mes_anterior = $mes_anterior;
        $this->detalle = $detalle;
        $this->analisis_costo = $analisis_costo;
        $this->resumen_costo_glp_anterior = $resumen_costo_glp_anterior;
        $this->resumen = $resumen;
        $this->resumen_costo_glp = $resumen_costo_glp;
        $this->resumen_2 = $resumen_2;
        $this->resumen_3 = $resumen_3;
        $this->resumen_4 = $resumen_4;
        $this->resumen_5 = $resumen_5;
        $this->cobertura_1 = $cobertura_1;
        $this->cobertura_2 = $cobertura_2;
        $this->proyeccion_envasado = $proyeccion_envasado;
        $this->proyeccion_granel = $proyeccion_granel;
    }

    public function sheets(): array
    {
        return [
            //  'Analisis Costo' => new SaleDayReportSheetAnalysis($this->datetime, $this->analisis_costo),
            'Resumen' => new SaleDayReportSheetResumen(
                $this->dias_totales,
                $this->dias_avance,
                $this->quedan,
                $this->datetime,
                $this->mes_actual,
                $this->mes_anterior,
                $this->resumen,
                $this->resumen_costo_glp,
                $this->resumen_costo_glp_anterior,
                $this->resumen_2,
                $this->resumen_3,
                $this->resumen_4,
                $this->resumen_5
            ),
            'Cobertura Canal' => new SaleDayReportSheetCoberturaCanal($this->cobertura_1),
            'Cobertura Ruta' => new SaleDayReportSheetCoberturaRuta($this->cobertura_2),
            'Proyeccion' => new SaleDayReportSheetProyeccion($this->proyeccion_envasado, $this->proyeccion_granel, $this->datetime),
            'Detalle' => new SaleDayReportSheetDetalle($this->datetime, $this->detalle,$this->resumen_costo_glp),
        ];
    }
}
