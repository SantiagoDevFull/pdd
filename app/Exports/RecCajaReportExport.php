<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class RecCajaReportExport implements FromView, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */

    protected $lista;


    public function __construct($lista)
    {
        $this->lista = $lista;
    }

    public function view(): View
    {
        $lista = $this->lista;


        return view('backend.excel.rec_caja')->with(compact('lista'));
    }
}
