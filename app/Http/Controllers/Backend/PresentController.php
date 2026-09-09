<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;


class PresentController extends Controller
{
	public function index()
	{
		return view('backend.present');
	}
}
