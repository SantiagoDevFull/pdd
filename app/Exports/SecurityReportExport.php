<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SecurityReportExport implements FromView, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */

	protected $datetime;
	protected $users;

	public function __construct($datetime, $users)
	{
		$this->datetime = $datetime;
		$this->users = $users;
	}

    public function view(): View
    {
        $datetime = $this->datetime;
        $users = $this->users;

		return view('backend.excel.security')->with(compact('datetime', 'users'));
    }
}
