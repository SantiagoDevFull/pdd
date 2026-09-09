<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class FacturationCocheraReportExport implements FromView, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */

	protected $datetime;
	protected $movements;

	public function __construct($datetime, $movements)
	{
		$this->datetime = $datetime;
		$this->movements = $movements;
	}

    public function view(): View
    {
        $datetime = $this->datetime;
        $movements = $this->movements;

		return view('backend.excel.cochera_movement')->with(compact('datetime', 'movements'));
    }
}
