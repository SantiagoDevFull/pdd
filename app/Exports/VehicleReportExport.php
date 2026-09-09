<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class VehicleReportExport implements FromView, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */

	protected $datetime;
	protected $vehicles;

	public function __construct($datetime, $vehicles)
	{
		$this->datetime = $datetime;
		$this->vehicles = $vehicles;
	}

    public function view(): View
    {
        $datetime = $this->datetime;
        $vehicles = $this->vehicles;

		return view('backend.excel.vehicles')->with(compact('datetime', 'vehicles'));
    }
}
