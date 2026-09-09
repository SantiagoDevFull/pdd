<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class OsinergminReportExport implements FromView, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */

    protected $datetime;
    protected $abastecimientos_glp;
    protected $facturas_vd;
    protected $guias_elect;
    protected $tres_tres;
    protected $guias_elect_cilindros;
    protected $vouchers;


    public function __construct($datetime, $abastecimientos_glp, $facturas_vd, $guias_elect, $tres_tres, $guias_elect_cilindros, $vouchers)
    {
        $this->datetime = $datetime;
        $this->abastecimientos_glp = $abastecimientos_glp;
        $this->facturas_vd = $facturas_vd;
        $this->guias_elect = $guias_elect;
        $this->tres_tres = $tres_tres;
        $this->guias_elect_cilindros = $guias_elect_cilindros;
        $this->vouchers = $vouchers;

    }

    public function view(): View
    {
        $datetime = $this->datetime;
        $abastecimientos_glp = $this->abastecimientos_glp;
        $facturas_vd = $this->facturas_vd;
        $guias_elect = $this->guias_elect;
        $tres_tres = $this->tres_tres;
        $guias_elect_cilindros = $this->guias_elect_cilindros;
        $vouchers = $this->vouchers;


        return view('backend.excel.osinergmin')->with(compact('datetime', 'abastecimientos_glp', 'facturas_vd', 'guias_elect', 'tres_tres','guias_elect_cilindros', 'vouchers'));
    }
}
