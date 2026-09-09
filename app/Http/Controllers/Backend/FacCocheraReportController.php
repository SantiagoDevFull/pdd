<?php

namespace App\Http\Controllers\Backend;

use App\ClientMotor;
use App\Company;
use App\Exports\FacturationCocheraReportExport;
use App\Http\Controllers\Controller;
use App\CocheraMovement;
use App\CocheraMovementDetail;
use App\Vehicle;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Fpdf;

class FacturationCocheraReportController  extends Controller
{
	public function index()
	{
		$companies=Company::select('id','name')->get();
        return view('backend.facturations_cochera_report')->with(compact('companies'));
	}

	public function validateForm()
	{
		$messages = [
			'company_id.required'	=> 'Debe seleccionar una compañía.',
		];

		$rules = [
			'company_id'			=> 'required',
		];

		request()->validate($rules, $messages);
		return request()->all();
	}

	public function list()
	{

		$company_id=request('model.company_id');

		$export = request('flag_export');

		$movements= CocheraMovement::join('companies','company_id','companies.id')
		->join('costos','cochera_movements.costo_id','costos.id')
		->join('costo_secundarios','cochera_movements.costo_secundario_id','costo_secundarios.id')
		->join('areas','cochera_movements.area_id','areas.id')
        ->join('currencies','cochera_movements.currency_id','currencies.id')
        ->select(
        'cochera_movements.id as movements_id',
        'companies.name as company_name',
		DB::Raw('CONCAT(costos.name,"-",costo_secundarios.name) as centro_costos'),
		'cochera_movements.account_document_number as provider_ruc',
        'cochera_movements.account_name as provider_name',
        'currencies.name as currency_name',
		'areas.name as area_name',
		'cochera_movements.payment_id as payment_id',
		'cochera_movements.expiry_date as fecha_pago',
		'cochera_movements.state as estado',
		'cochera_movements.origin as presupuesto',
        'cochera_movements.created_at as movement_date',
        'cochera_movements.total as total',
        DB::Raw('CONCAT(cochera_movements.referral_serie_number,"-",cochera_movements.referral_voucher_number) as factura')
        )
        ->where('company_id',$company_id)
        ->movementBy('cochera_movements.movement_date','desc')
        ->get();

		if ($export) {
			$datetime = CarbonImmutable::now()->format('d/m/Y h:i:s a');

			$excel = new FacturationCocheraReportExport($datetime, $movements);
			return Excel::download($excel, 'reporte-facturas-por-pagar.xlsx');
		}

	
        return $movements;
	}

	

	public function aprobar()
	{
		$movement_id = request('movement_id');
		$movement = CocheraMovement::findOrFail($movement_id);

	

		$movement->state = 1;
		$movement->save();
	}

	
	public function view_asign()
	{
		$movement_id = request('movement_id');

		$movement_details = CocheraMovementDetail::where('movement_id', $movement_id)->get();

		return $movement_details;
	}


	
}
