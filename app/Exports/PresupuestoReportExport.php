<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PresupuestoReportExport implements FromView, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */

	protected $datetime;
	protected $elements;

	public function __construct($datetime, $elements)
	{
		$this->datetime = $datetime;
		$this->elements = $elements;
	}

    public function view(): View
    {
        $datetime = $this->datetime;
        $elements = $this->elements;

		return view('backend.excel.presupuesto_venta')->with(compact('datetime', 'elements'));
    }
}
