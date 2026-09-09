<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ServiceReportExport implements FromView, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */

	protected $datetime;
	protected $services;

	public function __construct($datetime, $services)
	{
		$this->datetime = $datetime;
		$this->services = $services;
	}

    public function view(): View
    {
        $datetime = $this->datetime;
        $services = $this->services;

		return view('backend.excel.services')->with(compact('datetime', 'services'));
    }
}
