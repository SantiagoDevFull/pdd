<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class EmployeeReportExport implements FromView, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */

	protected $datetime;
	protected $employees;

	public function __construct($datetime, $employees)
	{
		$this->datetime = $datetime;
		$this->employees = $employees;
	}

    public function view(): View
    {
        $datetime = $this->datetime;
        $employees = $this->employees;

		return view('backend.excel.employees')->with(compact('datetime', 'employees'));
    }
}
