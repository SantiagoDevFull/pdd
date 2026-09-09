<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class OrderWorkReportExport implements FromView, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */

	protected $datetime;
	protected $order_works;

	public function __construct($datetime, $order_works)
	{
		$this->datetime = $datetime;
		$this->order_works = $order_works;
	}

    public function view(): View
    {
        $datetime = $this->datetime;
        $order_works = $this->order_works;

		return view('backend.excel.order_work')->with(compact('datetime', 'order_works'));
    }
}
