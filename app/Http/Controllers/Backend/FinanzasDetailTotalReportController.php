<?php

namespace App\Http\Controllers\Backend;

use App\CashClosing;
use App\CierreCaja;
use App\Client;
use App\Company;
use App\PaymentReport;
use App\Http\Controllers\Controller;
use App\Sale;
use App\Efective;
use App\Deposit;
use App\Liquidation;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;


class FinanzasDetailTotalReportController extends Controller
{
	public function index()
	{
		$companies = Company::select('id', 'name')->get();
		$current_date = CarbonImmutable::now()->toAtomString();
		return view('backend.finanzas_detail_total_report')->with(compact('companies', 'current_date'));
	}

	public function validateForm()
	{
		$messages = [
			'initial_date.required'	=> 'Debe seleccionar una Fecha inicial.',

		];

		$rules = [
			'initial_date'	=> 'required',

		];

		request()->validate($rules, $messages);
		return request()->all();
	}

	public function getClients()
	{
		$company_id = request('company_id');
		$q = request('q');

		$clients = Client::select('id', 'business_name as text')
			->when($company_id, function ($query, $company_id) {
				return $query->where('company_id', $company_id);
			})
			->where('business_name', 'like', '%' . $q . '%')
			->withTrashed()
			->get();

		return $clients;
	}

	public function list()
	{

		$export = request('export');

		$initial_date = CarbonImmutable::createFromDate(request('model.initial_date'))->startOfDay()->format('Y-m-d');

		$elements = PaymentReport::select('id as payment_id', 'payment_reports.name as payment_name')
			->groupBy('payment_name')
			->get();

		$response = [];
		$totals_total = 0;
		$totals_sum_total = 0;

		$warehouse_document_type_ids = [13, 5, 7, 4];
		$client_ids = [1031, 427, 13326, 13775, 14072, 14258];

		$total_venta_del_dia = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
			->whereNotIn('sales.client_id', $client_ids)
			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=',  $initial_date)
			->select('sales.total_perception')
			->sum('sales.total_perception');


		$dinero = Efective::where('bank_date', '=',  $initial_date)
			->select('efectives.total')
			->sum('efectives.total');

		$deposito = Deposit::where('bank_date', '=',  $initial_date)
			->select('deposits.total')
			->sum('deposits.total');

		$efective = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
			->whereNotIn('sales.client_id', $client_ids)
			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=',  $initial_date)
			->select('sales.efective')
			->sum('sales.efective');

		$deposit = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
			->whereNotIn('sales.client_id', $client_ids)
			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=', $initial_date)
			->select('sales.deposit')
			->sum('sales.deposit');

		$credit = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
			->whereNotIn('sales.client_id', $client_ids)
			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=', $initial_date)
			->select('sales.pre_balance')
			->sum('sales.pre_balance');

		$total_liquidado = $efective + $deposit + $credit;


		number_format($total_liquidado, 2, '.', '');

		$diference = $total_venta_del_dia - $total_liquidado; //DINERO DE VENTAS - LIQUIDACION

		number_format($diference, 2, '.', '');

		$cobranza_efective = Liquidation::leftjoin('sales', 'liquidations.sale_id', '=', 'sales.id')
			->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->where(DB::Raw('DATE_FORMAT(liquidations.created_at, "%Y-%m-%d") '), '=', $initial_date)
			->whereNotIn('sales.client_id', $client_ids)
			->where('liquidations.payment_method_id', [1])
			->Where('liquidations.collection', [1])
			->select('liquidations.amount')
			->sum('liquidations.amount');

		$cobranza_deposit = Liquidation::leftjoin('sales', 'liquidations.sale_id', '=', 'sales.id')
			->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->where(DB::Raw('DATE_FORMAT(liquidations.created_at, "%Y-%m-%d") '), '=', $initial_date)
			->whereNotIn('sales.client_id', $client_ids)
			->where('liquidations.payment_method_id', [2])
			->Where('liquidations.collection', [1])
			->select('liquidations.amount')
			->sum('liquidations.amount');

		$total_cobranza = $cobranza_efective + $cobranza_deposit;

		$cesion_uso_efective = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->leftjoin('liquidations', 'sales.id', '=', 'liquidations.sale_id')
			->whereNotIn('sales.client_id', $client_ids)
			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=', $initial_date)
			->where('liquidations.payment_method_id', [1])
			->where('sales.warehouse_document_type_id', [14])
			->select('liquidations.amount')
			->sum('liquidations.amount');

		$cesion_uso_deposit = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->leftjoin('liquidations', 'sales.id', '=', 'liquidations.sale_id')
			->whereNotIn('sales.client_id', $client_ids)
			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=', $initial_date)
			->where('liquidations.payment_method_id', [2])
			->where('sales.warehouse_document_type_id', [14])
			->select('liquidations.amount')
			->sum('liquidations.amount');

		$otros_efective = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->whereNotIn('sales.client_id', $client_ids)
			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=', $initial_date)
			->where('sales.warehouse_document_type_id', [22])
			->where('sales.payment_method_efective', 1)
			->select('sales.total_perception')
			->sum('sales.total_perception');

		$otros_deposit = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->whereNotIn('sales.client_id', $client_ids)
			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=', $initial_date)
			->where('sales.warehouse_document_type_id', [22])
			->where('sales.payment_method_deposit', 1)
			->select('sales.total_perception')
			->sum('sales.total_perception');

		$pendiente = ($dinero - $cobranza_efective) - ($efective);

		$total_otros_ingresos = $cesion_uso_efective + $cesion_uso_deposit + $otros_efective + $otros_deposit;

		$total_recaudado_efectivo = $dinero + $cesion_uso_efective + $otros_efective;
		$total_recaudado_deposito = $deposito + $cesion_uso_deposit + $otros_deposit;
		$total_recaudado = $total_recaudado_efectivo + $total_recaudado_deposito;

		$egresos_caja = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->whereNotIn('sales.client_id', $client_ids)
			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=', $initial_date)
			->whereIn('sales.warehouse_document_type_id', [23, 32])
			->select('sales.total_perception')
			->sum('sales.total_perception');

		$total_efective_day = $total_recaudado_efectivo - $egresos_caja;

		$remesa_hermes = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->whereNotIn('sales.client_id', $client_ids)
			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=', $initial_date)
			->where('sales.warehouse_document_type_id', [31])
			->select('sales.total_perception')
			->sum('sales.total_perception');

		$cuadre = $total_efective_day - $remesa_hermes;

		$response = [
			[
				'company_short_name' => 'Total Venta del Día',
				'total' => $total_venta_del_dia
			],
			[
				'company_short_name' => 'Efectivo Recibido',
				'total' => $dinero
			],
			[
				'company_short_name' => 'Depositos Bancarios',
				'total' => $deposito
			],
			[
				'company_short_name' => 'Efectivo Liquidado',
				'total' => $efective
			],
			[
				'company_short_name' => 'Deposito',
				'total' => $deposit
			],
			[
				'company_short_name' => 'Credito',
				'total' => $credit
			],

			[
				'company_short_name' => 'Total Liquidado',
				'total' => $total_liquidado
			],
			[
				'company_short_name' => 'Efectivo por liquidar',
				'total' => $pendiente
			],
			[
				'company_short_name' => 'Diferencia',
				'total' => $diference
			],
			[
				'company_short_name' => 'Cobranza en Efectivo',
				'total' => $cobranza_efective
			],
			[
				'company_short_name' => 'Cobranza en Deposito',
				'total' => $cobranza_deposit
			],
			[
				'company_short_name' => 'Total Cobranza',
				'total' => $total_cobranza
			],
			[
				'company_short_name' => 'Cesión de Uso en Efectivo',
				'total' => $cesion_uso_efective
			],
			[
				'company_short_name' => 'Cesión de Uso en Deposito',
				'total' => $cesion_uso_deposit
			],
			[
				'company_short_name' => 'Otros Ingresos en Efectivo',
				'total' => $otros_efective
			],
			[
				'company_short_name' => 'Otros Ingresos en Deposito',
				'total' => $otros_deposit
			],
			[
				'company_short_name' => 'Total Otros Ingresos',
				'total' => $total_otros_ingresos
			],
			[
				'company_short_name' => 'Total Recaudado Efectivo',
				'total' => $total_recaudado_efectivo
			],
			[
				'company_short_name' => 'Total Recaudado Deposito',
				'total' => $total_recaudado_deposito
			],
			[
				'company_short_name' => 'Total Recaudado del Día',
				'total' => $total_recaudado
			],
			[
				'company_short_name' => 'Egresos de Caja',
				'total' => $egresos_caja
			],
			[
				'company_short_name' => 'Total Efectivo del Día',
				'total' => $total_efective_day
			],
			[
				'company_short_name' => 'Remesa Hermes',
				'total' => $remesa_hermes
			],
			[
				'company_short_name' => 'Cuadre',
				'total' => $cuadre
			],
		];

		// foreach ($elements as $saledetail) {

		// 	$initial_date = CarbonImmutable::createFromDate(request('model.initial_date'))->startOfDay()->format('Y-m-d');
		// 	$saledetail->payment_id = $saledetail['payment_id'];
		// 	$saledetail->company_short_name = $saledetail['payment_name'];

		// 	// Total venta del dia
		// 	if ($saledetail->payment_id == 12) {
		// 		$sum_soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
		// 			->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
		// 			->whereNotIn('sales.client_id', $client_ids)
		// 			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=',  $initial_date)
		// 			->select('sales.total_perception')
		// 			->sum('sales.total_perception');
		// 	}

		// 	// Efectivo
		// 	if ($saledetail->payment_id == 1) {
		// 		$sum_soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
		// 			->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
		// 			->whereNotIn('sales.client_id', $client_ids)
		// 			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=',  $initial_date)
		// 			->select('sales.efective')
		// 			->sum('sales.efective');
		// 	}

		// 	// Deposito
		// 	elseif ($saledetail->payment_id == 2) {
		// 		$sum_soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
		// 			->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
		// 			->whereNotIn('sales.client_id', $client_ids)
		// 			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=', $initial_date)
		// 			->select('sales.deposit')
		// 			->sum('sales.deposit');
		// 	}

		// 	// Creditos
		// 	elseif ($saledetail->payment_id == 3) {
		// 		$sum_soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
		// 			->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
		// 			->whereNotIn('sales.client_id', $client_ids)
		// 			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=', $initial_date)
		// 			->select('sales.pre_balance')
		// 			->sum('sales.pre_balance');
		// 	}

		// 	// Total liquidado
		// 	if ($saledetail->payment_id == 13) {
		// 		$sum_efective = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
		// 			->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
		// 			->whereNotIn('sales.client_id', $client_ids)
		// 			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=',  $initial_date)
		// 			->select('sales.efective')
		// 			->sum('sales.efective');

		// 		$sum_deposit = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
		// 			->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
		// 			->whereNotIn('sales.client_id', $client_ids)
		// 			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=', $initial_date)
		// 			->select('sales.deposit')
		// 			->sum('sales.deposit');

		// 		$sum_credit = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
		// 				->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
		// 				->whereNotIn('sales.client_id', $client_ids)
		// 				->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=', $initial_date)
		// 				->select('sales.pre_balance')
		// 				->sum('sales.pre_balance');

		// 		$sum_soles = $sum_efective + $sum_deposit + $sum_credit;
		// 	}

		// 	// Diferencia
		// 	if ($saledetail->payment_id == 16) {
		// 		$sum_total_perception = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
		// 			->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
		// 			->whereNotIn('sales.client_id', $client_ids)
		// 			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=',  $initial_date)
		// 			->select('sales.total_perception')
		// 			->sum('sales.total_perception');

		// 		$sum_efective = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
		// 			->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
		// 			->whereNotIn('sales.client_id', $client_ids)
		// 			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=',  $initial_date)
		// 			->select('sales.efective')
		// 			->sum('sales.efective');

		// 		$sum_deposit = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
		// 			->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
		// 			->whereNotIn('sales.client_id', $client_ids)
		// 			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=', $initial_date)
		// 			->select('sales.deposit')
		// 			->sum('sales.deposit');

		// 		$sum_credit = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
		// 				->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
		// 				->whereNotIn('sales.client_id', $client_ids)
		// 				->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=', $initial_date)
		// 				->select('sales.pre_balance')
		// 				->sum('sales.pre_balance');

		// 		$sum_total_liquidado = $sum_efective + $sum_deposit + $sum_credit;

		// 		$sum_soles = $sum_total_perception - $sum_total_liquidado;
		// 	}

		// 	// Cobranza en efectivo
		// 	elseif ($saledetail->payment_id == 4) {
		// 		$sum_soles =Liquidation::leftjoin('sales','liquidations.sale_id','=','sales.id')
		// 			->leftjoin('clients', 'sales.client_id', '=', 'clients.id')				
		// 			->where(DB::Raw('DATE_FORMAT(liquidations.created_at, "%Y-%m-%d") '), '=', $initial_date)
		// 			->whereNotIn('sales.client_id', $client_ids)	 
		// 			->where('liquidations.payment_method_id',[1])
		// 			->Where('liquidations.collection',[1])
		// 			->select('liquidations.amount')
		// 			->sum('liquidations.amount');
		// 	}

		// 	// Cobranza en deposito
		// 	elseif ($saledetail->payment_id == 5) {
		// 		$sum_soles =Liquidation::leftjoin('sales','liquidations.sale_id','=','sales.id')
		// 			->leftjoin('clients', 'sales.client_id', '=', 'clients.id')	
		// 			->where(DB::Raw('DATE_FORMAT(liquidations.created_at, "%Y-%m-%d") '), '=', $initial_date)
		// 			->whereNotIn('sales.client_id', $client_ids)	 
		// 			->where('liquidations.payment_method_id',[2])
		// 			->Where('liquidations.collection',[1])
		// 			->select('liquidations.amount')				
		// 			->sum('liquidations.amount');
		// 	}

		// 	// Total cobranza
		// 	elseif ($saledetail->payment_id == 14) {
		// 		$sum_cobranza_efective =Liquidation::leftjoin('sales','liquidations.sale_id','=','sales.id')
		// 			->leftjoin('clients', 'sales.client_id', '=', 'clients.id')	
		// 			->where(DB::Raw('DATE_FORMAT(liquidations.created_at, "%Y-%m-%d") '), '=', $initial_date)
		// 			->whereNotIn('sales.client_id', $client_ids)	 
		// 			->where('liquidations.payment_method_id',[1])
		// 			->Where('liquidations.collection',[1])
		// 			->select('liquidations.amount')				
		// 			->sum('liquidations.amount');

		// 		$sum_cobranza_deposit =Liquidation::leftjoin('sales','liquidations.sale_id','=','sales.id')
		// 			->leftjoin('clients', 'sales.client_id', '=', 'clients.id')	
		// 			->where(DB::Raw('DATE_FORMAT(liquidations.created_at, "%Y-%m-%d") '), '=', $initial_date)
		// 			->whereNotIn('sales.client_id', $client_ids)	 
		// 			->where('liquidations.payment_method_id',[2])
		// 			->Where('liquidations.collection',[1])
		// 			->select('liquidations.amount')				
		// 			->sum('liquidations.amount');

		// 		$sum_soles = $sum_cobranza_efective + $sum_cobranza_deposit;
		// 	}

		// 	// Cesion de uso efectivo
		// 	elseif ($saledetail->payment_id == 8) {
		// 		$sum_soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
		// 			->leftjoin('liquidations', 'sales.id', '=', 'liquidations.sale_id')
		// 			->whereNotIn('sales.client_id', $client_ids)
		// 			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=', $initial_date)
		// 			->where('liquidations.payment_method_id',[1])	
		// 			->where('sales.warehouse_document_type_id',[14])			
		// 			->select('liquidations.amount')
		// 			->sum('liquidations.amount');
		// 	}

		// 	// Cesion de uso depositos
		// 	elseif ($saledetail->payment_id == 11) {
		// 		$sum_soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
		// 			->leftjoin('liquidations', 'sales.id', '=', 'liquidations.sale_id')
		// 			->whereNotIn('sales.client_id', $client_ids)
		// 			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=', $initial_date)
		// 			->where('liquidations.payment_method_id',[2])	
		// 			->where('sales.warehouse_document_type_id',[14])			
		// 			->select('liquidations.amount')
		// 			->sum('liquidations.amount');
		// 	}

		// 	// Otros ingresos efectivo
		// 	elseif ($saledetail->payment_id == 7) {
		// 		$sum_soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
		// 			->leftjoin('liquidations', 'sales.id', '=', 'liquidations.sale_id')
		// 			->whereNotIn('sales.client_id', $client_ids)
		// 			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=', $initial_date)
		// 			->where('liquidations.payment_method_id',[1])	
		// 			->where('sales.warehouse_document_type_id',[22])			
		// 			->select('liquidations.amount')
		// 			->sum('liquidations.amount');
		// 	}

		// 	// Otros Ingresos deposito
		// 	elseif ($saledetail->payment_id == 9) {
		// 		$sum_soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
		// 			->leftjoin('liquidations', 'sales.id', '=', 'liquidations.sale_id')
		// 			->whereNotIn('sales.client_id', $client_ids)
		// 			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=', $initial_date)
		// 			->where('liquidations.payment_method_id',[2])	
		// 			->where('sales.warehouse_document_type_id',[22])			
		// 			->select('liquidations.amount')
		// 			->sum('liquidations.amount');
		// 	}

		// 	// Total otros ingresos
		// 	elseif ($saledetail->payment_id == 15) {
		// 		$sum_otros_efective = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
		// 			->leftjoin('liquidations', 'sales.id', '=', 'liquidations.sale_id')
		// 			->whereNotIn('sales.client_id', $client_ids)
		// 			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=', $initial_date)
		// 			->where('liquidations.payment_method_id',[1])	
		// 			->where('sales.warehouse_document_type_id',[22])			
		// 			->select('liquidations.amount')
		// 			->sum('liquidations.amount');

		// 		$sum_otros_deposit = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
		// 			->leftjoin('liquidations', 'sales.id', '=', 'liquidations.sale_id')
		// 			->whereNotIn('sales.client_id', $client_ids)
		// 			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=', $initial_date)
		// 			->where('liquidations.payment_method_id',[2])	
		// 			->where('sales.warehouse_document_type_id',[22])			
		// 			->select('liquidations.amount')
		// 			->sum('liquidations.amount');

		// 		$sum_soles = $sum_otros_efective + $sum_otros_deposit;
		// 	}

		// 	// Total reacuadado del dia
		// 	elseif ($saledetail->payment_id == 17) {
		// 		$sum_efective = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
		// 			->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
		// 			->whereNotIn('sales.client_id', $client_ids)
		// 			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=',  $initial_date)
		// 			->select('sales.efective')
		// 			->sum('sales.efective');

		// 		$sum_deposit = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
		// 			->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
		// 			->whereNotIn('sales.client_id', $client_ids)
		// 			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=', $initial_date)
		// 			->select('sales.deposit')
		// 			->sum('sales.deposit');

		// 		$sum_credit = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
		// 				->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
		// 				->whereNotIn('sales.client_id', $client_ids)
		// 				->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=', $initial_date)
		// 				->select('sales.pre_balance')
		// 				->sum('sales.pre_balance');

		// 		$sum_total_liquidacion = $sum_efective + $sum_deposit + $sum_credit;

		// 		$sum_cobranza_efective =Liquidation::leftjoin('sales','liquidations.sale_id','=','sales.id')
		// 			->leftjoin('clients', 'sales.client_id', '=', 'clients.id')	
		// 			->where(DB::Raw('DATE_FORMAT(liquidations.created_at, "%Y-%m-%d") '), '=', $initial_date)
		// 			->whereNotIn('sales.client_id', $client_ids)	 
		// 			->where('liquidations.payment_method_id',[1])
		// 			->Where('liquidations.collection',[1])
		// 			->select('liquidations.amount')				
		// 			->sum('liquidations.amount');

		// 		$sum_cobranza_deposit =Liquidation::leftjoin('sales','liquidations.sale_id','=','sales.id')
		// 			->leftjoin('clients', 'sales.client_id', '=', 'clients.id')	
		// 			->where(DB::Raw('DATE_FORMAT(liquidations.created_at, "%Y-%m-%d") '), '=', $initial_date)
		// 			->whereNotIn('sales.client_id', $client_ids)	 
		// 			->where('liquidations.payment_method_id',[2])
		// 			->Where('liquidations.collection',[1])
		// 			->select('liquidations.amount')				
		// 			->sum('liquidations.amount');

		// 		$sum_total_cobranza = $sum_cobranza_efective + $sum_cobranza_deposit;

		// 		$sum_otros_efective = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
		// 			->leftjoin('liquidations', 'sales.id', '=', 'liquidations.sale_id')
		// 			->whereNotIn('sales.client_id', $client_ids)
		// 			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=', $initial_date)
		// 			->where('liquidations.payment_method_id',[1])	
		// 			->where('sales.warehouse_document_type_id',[22])			
		// 			->select('liquidations.amount')
		// 			->sum('liquidations.amount');

		// 		$sum_otros_deposit = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
		// 			->leftjoin('liquidations', 'sales.id', '=', 'liquidations.sale_id')
		// 			->whereNotIn('sales.client_id', $client_ids)
		// 			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=', $initial_date)
		// 			->where('liquidations.payment_method_id',[2])	
		// 			->where('sales.warehouse_document_type_id',[22])			
		// 			->select('liquidations.amount')
		// 			->sum('liquidations.amount');

		// 		$sum_total_otros = $sum_otros_efective + $sum_otros_deposit;

		// 		$sum_soles = $sum_total_liquidacion + $sum_total_cobranza + $sum_total_otros;
		// 	}

		// 	// Egresos de Caja
		// 	elseif ($saledetail->payment_id == 10) {
		// 		$sum_soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
		// 			->whereNotIn('sales.client_id', $client_ids)
		// 			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=', $initial_date)	
		// 			->where('sales.warehouse_document_type_id',[23])			
		// 			->select('sales.total_perception')
		// 			->sum('sales.total_perception');
		// 	}

		// 	$saledetail->total = $sum_soles;

		// 	$totals_total += $saledetail['total'];

		// 	$response[] = $saledetail;
		// }

		// $totals = new stdClass();
		// $totals->company_short_name = 'TOTAL';
		// $totals->total = $totals_total;
		// $response[] = $totals;

		if ($export == 1) {
			$spreadsheet = new Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();
			$sheet->mergeCells('A1:L1');
			$sheet->setCellValue('A1', 'REPORTE DE LIQUIDACIÓN DEL DÍA ' . $initial_date . ' DESCARGADO EL  ' . CarbonImmutable::now()->format('d/m/Y H:m:s'));
			$sheet->getStyle('A1')->applyFromArray([
				'font' => [
					'bold' => true,
					'size' => 16,
				],
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
				]
			]);

			// $sheet->setCellValue('A3', '#');
			// $sheet->setCellValue('B3', 'Caja');		
			// $sheet->setCellValue('C3', 'Fecha de Liquidación');
			// $sheet->setCellValue('D3', 'Total');

			// $sheet->getStyle('A3:M3')->applyFromArray([
			// 	'font' => [
			// 		'bold' => true,
			// 	],
			// ]);

			// $row_number = 4;
			// foreach ($response as $index => $element) {
			// 	$index++;
			// 	$sheet->setCellValueExplicit('A'.$row_number, $index, DataType::TYPE_NUMERIC);
			// 	$sheet->setCellValue('B'.$row_number, $element->company_short_name);
			// 	$sheet->setCellValue('C'.$row_number, $initial_date );
			// 	$sheet->setCellValue('D'.$row_number, $element->total);

			//     $sheet->getStyle('D'.$row_number)->getNumberFormat()->setFormatCode('0.00');

			// 	$row_number++;
			// }
			// $sheet->setCellValueExplicit('A4', 1, DataType::TYPE_NUMERIC);
			// Total venta del dia
			$sheet->setCellValue('F3', 'TOTAL VENTA DEL DIA');
			$sheet->setCellValue('G3', $total_venta_del_dia);

			$sheet->getStyle('F3')->applyFromArray([
				'font' => [
					'bold' => true,
				],
			]);

			// Forma de Pago
			$sheet->setCellValue('F5', 'Forma de Pago');

			$sheet->getStyle('F5')->applyFromArray([
				'font' => [
					'bold' => true,
				],
			]);


			// Efectivo Recibido
			$sheet->setCellValue('F6', 'EFECTIVO DEL DÍA');
			$sheet->setCellValue('G6', $dinero);


			// Efectivo
			$sheet->setCellValue('F7', 'EFECTIVO LIQUIDADO');
			$sheet->setCellValue('G7', $efective);

			// Deposito
			$sheet->setCellValue('F8', 'DEPOSITO');
			$sheet->setCellValue('G8', $deposit);

			// Credito
			$sheet->setCellValue('F9', 'CREDITO');
			$sheet->setCellValue('G9', $credit);

			// Diferencia
			$sheet->setCellValue('F10', 'EFECTIVO POR LIQUIDAR');
			$sheet->setCellValue('G10', $pendiente);

			// Total liquidado
			$sheet->setCellValue('F11', 'TOTAL LIQUIDADO');
			$sheet->setCellValue('G11', $total_liquidado);

			// Diferencia
			$sheet->setCellValue('F13', 'DIFERENCIA');
			$sheet->setCellValue('G13', $diference);

			// Cobranzas de creditos
			$sheet->setCellValue('F15', 'Cobranzas de Creditos');

			$sheet->getStyle('F15')->applyFromArray([
				'font' => [
					'bold' => true,
				],
			]);

			// Cobranza en efectivo
			$sheet->setCellValue('F16', 'COBRANZA EN EFECTIVO');
			$sheet->setCellValue('G16', $cobranza_efective);

			// Cobranza en deposito
			$sheet->setCellValue('F17', 'COBRANZA EN DEPOSITO');
			$sheet->setCellValue('G17', $cobranza_deposit);

			// Total cobranza
			$sheet->setCellValue('F18', 'TOTAL COBRANZA');
			$sheet->setCellValue('G18', $total_cobranza);

			// Otros ingresos de caja
			$sheet->setCellValue('F20', 'Otros Ingresos de Caja');

			$sheet->getStyle('F20')->applyFromArray([
				'font' => [
					'bold' => true,
				],
			]);

			// Cesion de Uso en Efectivo
			/*	$sheet->setCellValue('F21', 'CESION DE USO EN EFECTIVO');
			$sheet->setCellValue('G21', $cesion_uso_efective );

			// Cesion de Uso en Deposito
			$sheet->setCellValue('F22', 'CESION DE USO EN DEPOSITO');
			$sheet->setCellValue('G22', $cesion_uso_deposit );*/

			// Depositos Recibido

			// Otros ingresos efectivo
			$sheet->setCellValue('F23', 'OTROS INGRESOS EN EFECTIVO');
			$sheet->setCellValue('G23', $otros_efective);

			// Otros ingresos deposito
			$sheet->setCellValue('F24', 'OTROS INGRESOS EN DEPOSITO');
			$sheet->setCellValue('G24', $otros_deposit);

			// Total otros ingresos
			$sheet->setCellValue('F25', 'TOTAL OTROS INGRESOS');
			$sheet->setCellValue('G25', $total_otros_ingresos);

			$sheet->setCellValue('F26', 'DEPOSITOS DEL DÍA');
			$sheet->setCellValue('G26', $deposito);



			// Total recaudado del dia
			$sheet->setCellValue('F27', 'EFECTIVO RECAUDADO');
			$sheet->setCellValue('G27', $total_recaudado_efectivo);
			// Total recaudado del dia
			$sheet->setCellValue('F28', 'DEPOSITOS RECAUDADO');
			$sheet->setCellValue('G28', $total_recaudado_deposito);



			// Total recaudado del dia
			$sheet->setCellValue('F30', 'TOTAL RECAUDADO');
			$sheet->setCellValue('G30', $total_recaudado);

			// Egresos de caja
			$sheet->setCellValue('F32', 'EGRESOS DE CAJA');
			$sheet->setCellValue('G32', $egresos_caja);

			// Total efectivo del dìa
			$sheet->setCellValue('F34', 'TOTAL EFECTIVO DEL DIA');
			$sheet->setCellValue('G34', $total_efective_day);

			// Remesa Hermes
			$sheet->setCellValue('F36', 'REMESA HERMES');
			$sheet->setCellValue('G36', $remesa_hermes);

			// Cuadre
			$sheet->setCellValue('F38', 'CUADRE');
			$sheet->setCellValue('G38', $cuadre);

			// $sheet->getStyle('D'.$row_number)->getNumberFormat()->setFormatCode('0.00');

			$sheet->getColumnDimension('F')->setAutoSize(true);
			$sheet->getColumnDimension('G')->setAutoSize(true);
			// $sheet->getColumnDimension('C')->setAutoSize(true);
			// $sheet->getColumnDimension('D')->setAutoSize(true);
			// $sheet->getColumnDimension('E')->setAutoSize(true);

			$writer = new Xls($spreadsheet);
			return $writer->save('php://output');
		} else if ($export == 2) {

			$caja = new CashClosing();
			$caja->issue_date = $initial_date;
			$caja->total_sale_day = $total_venta_del_dia;
			$caja->efect_day = $dinero;
			$caja->efect_liq = $efective;
			$caja->deposit = $deposit;
			$caja->credit = $credit;
			$caja->efect_por_liq = $pendiente;
			$caja->total_liq = $total_liquidado;
			$caja->difference = $diference;

			$caja->cob_efect = $cobranza_efective;
			$caja->cob_dep = $cobranza_deposit;
			$caja->total_cob = $total_cobranza;

			$caja->other_ing_efect = $otros_efective;
			$caja->other_ing_deposit = $otros_deposit;
			$caja->total_other_ing = $total_otros_ingresos;
			$caja->deposit_day = $deposito;
			$caja->efect_rec = $total_recaudado_efectivo;
			$caja->deposit_rec = $total_recaudado_deposito;

			$caja->total_rec = $total_recaudado;
			$caja->egreso_caja = $egresos_caja;
			$caja->total_efect_day = $total_efective_day;
			$caja->remesa_hermes = $remesa_hermes;
			$caja->cuadre = $cuadre;
			$caja->created_at_user =  Auth::user()->user;
			$caja->save();

			$yesterday_sale_date = CarbonImmutable::createFromDate(request('model.initial_date'))->startOfDay()->subDay()->format('Y-m-d');

			$yes_cierre_caja = CierreCaja::where('cierre_date', $yesterday_sale_date)
				->where('state', 1) // Cerrado
				->where('company_id', 1)
				->first();

			if ($yes_cierre_caja) { //Cerrado


				$cierre_caja = CierreCaja::where('cierre_date', $initial_date)
					->where('company_id', 1)
					->first();

				if ($cierre_caja) {
					$cierre_caja->state = 1; // Cerrado
					$cierre_caja->save();

					$type = 3;
					$title = 'Éxito !';
					$msg = 'El cierre de caja del día ' . $initial_date . ' se ha realizado correctamente.';
					$url = route('dashboard.report.finanzas_detail_total');
				} else {
					$type = 2;
					$title = 'Advertencia !';
					$msg = 'Debe aperturar la caja del día actual antes de continuar con el cierre de caja.';
					$url = '';
				}
			} else {
				$type = 2;
				$title = 'Advertencia !';
				$msg = 'Debe cerrar la caja del día anterior antes de continuar con el cierre de caja del día actual.';
				$url = '';
			}


			return response()->json([
				'type'  => $type,
				'title' => $title,
				'msg'   => $msg,
				'url'   => $url
			], 200);
		} else {
			return response()->json($response);
		}
	}
}
