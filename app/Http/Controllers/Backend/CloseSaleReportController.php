<?php

namespace App\Http\Controllers\Backend;

use App\Client;
use App\Company;
use App\PaymentReport;
use App\Http\Controllers\Controller;
use App\SaleDetail;
use App\Sale;
use App\Efective;
use App\Deposit;
use App\Liquidation;
use App\WarehouseMovementDetail;
use App\WarehouseMovement;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xls;


class CloseSaleReportController extends Controller
{
	public function index()
	{
		$companies = Company::select('id', 'name')->get();
		$current_date = CarbonImmutable::now()->toAtomString();
		return view('backend.close_sale_report')->with(compact('companies', 'current_date'));
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
		$first_date = CarbonImmutable::createFromDate(request('model.initial_date'))->startOfDay()->format('Y-m-01');
		$price_year = CarbonImmutable::createFromDate(request('model.initial_date'))->startOfDay()->format('Y');
		$price_mes = CarbonImmutable::createFromDate(request('model.initial_date'))->startOfDay()->format('m');
		$anio_date = CarbonImmutable::createFromDate(request('model.initial_date'))->startOfDay()->format('2024-01-01');


		$elements = PaymentReport::select('id as payment_id', 'payment_reports.name as payment_name')
			->groupBy('payment_name')
			->get();

		$response = [];
		$totals_total = 0;
		$totals_sum_total = 0;

		$warehouse_document_type_ids = [13, 5, 7, 4, 6];
		$client_ids = [1031, 427, 13326, 13775, 14072, 14258];


		$total_liquidado_del_dia = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
			->whereNotIn('sales.client_id', $client_ids)
			->where(DB::Raw('sales.sale_date'), '=',  $initial_date)
			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=',  $initial_date)
			->select('sales.total_perception')
			->sum('sales.total_perception');




		$total_venta_del_dia = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
			->whereNotIn('sales.client_id', $client_ids)
			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=',  $initial_date)
			->select('sales.total_perception')
			->sum('sales.total_perception');



		$total_tn_dia = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
			->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
			->whereNotIn('sales.client_id', $client_ids)
			->where(DB::Raw('sales.sale_date'), '=',  $initial_date)
			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=',  $initial_date)
			->select('sale_details.kg')
			->sum('sale_details.kg');


		$total_tn_del_dia = $total_tn_dia / 1000;





		$tot_tn = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
			->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
			->whereNotIn('sales.client_id', $client_ids)
			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=',  $initial_date)
			->select('sale_details.kg')
			->sum('sale_details.kg');


		$total_tn = $tot_tn / 1000;



		$efective_venta = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
			->whereNotIn('sales.client_id', $client_ids)
			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=',  $initial_date)
			->where(DB::Raw('sales.sale_date'), '=',  $initial_date)
			->select('sales.efective')
			->sum('sales.efective');

		$deposit_venta = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
			->whereNotIn('sales.client_id', $client_ids)
			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=',  $initial_date)
			->where(DB::Raw('sales.sale_date'), '=', $initial_date)
			->select('sales.deposit')
			->sum('sales.deposit');

		$credit_venta = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
			->whereNotIn('sales.client_id', $client_ids)
			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=',  $initial_date)
			->where(DB::Raw('sales.sale_date'), '=', $initial_date)
			->select('sales.pre_balance')
			->sum('sales.pre_balance');

		$credit_venta_tn = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
			->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
			->whereNotIn('sales.client_id', $client_ids)
			->where('sales.pre_balance', '>', 0)
			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=',  $initial_date)
			->where(DB::Raw('sales.sale_date'), '=', $initial_date)
			->select('sale_details.kg')
			->sum('sale_details.kg');

		$descuento_venta = Liquidation::leftjoin('sales', 'liquidations.sale_id', '=', 'sales.id')
			->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
			->whereNotIn('sales.client_id', $client_ids)
			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=',  $initial_date)
			->where(DB::Raw('sales.sale_date'), '=', $initial_date)
			->where('liquidations.payment_method_id', '=', 10)
			->select('liquidations.amount')
			->sum('liquidations.amount');

		$credit_dia_venta =  $credit_venta + $descuento_venta;






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

		$credit_tn = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
			->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
			->whereNotIn('sales.client_id', $client_ids)
			->where('sales.pre_balance', '>', 0)
			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=', $initial_date)
			->select('sale_details.kg')
			->sum('sale_details.kg');


		$descuento = Liquidation::leftjoin('sales', 'liquidations.sale_id', '=', 'sales.id')
			->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
			->whereNotIn('sales.client_id', $client_ids)
			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=',  $initial_date)
			->where('liquidations.payment_method_id', '=', 10)
			->select('liquidations.amount')
			->sum('liquidations.amount');



		$total_credit = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
			->whereNotIn('sales.client_id', $client_ids)
			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=', $initial_date)
			->select('sales.pre_balance')
			->sum('sales.pre_balance');

		$total_credit_tn = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
			->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
			->whereNotIn('sales.client_id', $client_ids)
			->where('sales.pre_balance', '>', 0)
			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=', $initial_date)
			->select('sale_details.kg')
			->sum('sale_details.kg');



		$total_credit_pend = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
			->whereNotIn('sales.client_id', $client_ids)
			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=', $initial_date)
			->where('sales.balance', '>', 0)
			->select('sales.balance')
			->sum('sales.balance');

		$total_credit_pend_tn = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
			->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
			->whereNotIn('sales.client_id', $client_ids)
			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=', $initial_date)
			->where('sales.balance', '>', 0)
			->select('sale_details.kg')
			->sum('sale_details.kg');



		$total_liquidado = $efective + $deposit + $credit;


		number_format($total_liquidado, 2, '.', '');

		$diference = $total_venta_del_dia - $total_liquidado; //DINERO DE VENTAS - LIQUIDACION

		number_format($diference, 2, '.', '');


		$dinero = Efective::where('bank_date', '=',  $initial_date)
			->select('efectives.total')
			->sum('efectives.total');

		$deposito = Deposit::where('bank_date', '=',  $initial_date)
			->select('deposits.total')
			->sum('deposits.total');

		$cobranza_efective = Liquidation::leftjoin('sales', 'liquidations.sale_id', '=', 'sales.id')
			->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->where(DB::Raw('DATE_FORMAT(liquidations.created_at, "%Y-%m-%d") '), '=', $initial_date)
			->whereNotIn('sales.client_id', $client_ids)
			->where('liquidations.payment_method_id', 1)
			->whereIn('liquidations.collection', [1, 2])
			->select('liquidations.amount')
			->sum('liquidations.amount');

		$cobranza_deposit = Liquidation::leftjoin('sales', 'liquidations.sale_id', '=', 'sales.id')
			->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->where(DB::Raw('DATE_FORMAT(liquidations.created_at, "%Y-%m-%d") '), '=', $initial_date)
			->whereNotIn('sales.client_id', $client_ids)
			->where('liquidations.payment_method_id', '=', 2)
			->whereIn('liquidations.collection', [1, 2])
			->select('liquidations.amount')
			->sum('liquidations.amount');

		$total_cobranza = $cobranza_efective + $cobranza_deposit;


		$total_cobranza_tn = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
			->leftjoin('liquidations', 'sale_details.sale_id', '=', 'liquidations.sale_id')
			->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->where(DB::Raw('DATE_FORMAT(liquidations.created_at, "%Y-%m-%d") '), '=', $initial_date)
			->whereNotIn('sales.client_id', $client_ids)
			->whereIn('liquidations.collection', [1, 2])
			->select('sale_details.kg')
			->sum('sale_details.kg');



		$cesion_uso_efective = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->leftjoin('liquidations', 'sales.id', '=', 'liquidations.sale_id')
			->whereNotIn('sales.client_id', $client_ids)
			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=', $initial_date)
			->where('liquidations.payment_method_id', '=', 1)
			->where('sales.warehouse_document_type_id', '=', 14)
			->select('liquidations.amount')
			->sum('liquidations.amount');

		$cesion_uso_deposit = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->leftjoin('liquidations', 'sales.id', '=', 'liquidations.sale_id')
			->whereNotIn('sales.client_id', $client_ids)
			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=', $initial_date)
			->where('liquidations.payment_method_id', '=', 2)
			->where('sales.warehouse_document_type_id', '=', 14)
			->select('liquidations.amount')
			->sum('liquidations.amount');

		$otros_efective = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->whereNotIn('sales.client_id', $client_ids)
			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=', $initial_date)
			->where('sales.warehouse_document_type_id', '=', 22)
			->where('sales.payment_method_efective', '=', 1)
			->select('sales.total_perception')
			->sum('sales.total_perception');

		$otros_deposit = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->whereNotIn('sales.client_id', $client_ids)
			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=', $initial_date)
			->where('sales.warehouse_document_type_id', '=', 22)
			->where('sales.payment_method_deposit', '=', 1)
			->select('sales.total_perception')
			->sum('sales.total_perception');

		$pendiente = ($dinero - $cobranza_efective) - ($efective);

		$total_otros_ingresos = $cesion_uso_efective + $cesion_uso_deposit + $otros_efective + $otros_deposit;

		$total_recaudado_efectivo = $dinero + $cesion_uso_efective + $otros_efective;
		$total_recaudado_deposito = $deposito + $cesion_uso_deposit + $otros_deposit;


		$egresos_caja = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->whereNotIn('sales.client_id', $client_ids)
			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=', $initial_date)
			->whereIn('sales.warehouse_document_type_id', [23, 32])
			->select('sales.total_perception')
			->sum('sales.total_perception');

		$total_efective_day = $total_recaudado_efectivo - $egresos_caja;
		$total_recaudado = $total_efective_day  + $total_recaudado_deposito;

		$remesa_hermes = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->whereNotIn('sales.client_id', $client_ids)
			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=', $initial_date)
			->where('sales.warehouse_document_type_id', '=', 31)
			->select('sales.total_perception')
			->sum('sales.total_perception');

		$cuadre = $total_efective_day - $remesa_hermes;




		$gra_gen1k = WarehouseMovementDetail::leftjoin('warehouse_movements', 'warehouse_movement_details.warehouse_movement_id', '=', 'warehouse_movements.id')
			->leftjoin('articles', 'warehouse_movement_details.article_code', '=', 'articles.id')
			->where(DB::Raw('DATE_FORMAT(warehouse_movements.traslate_date, "%Y-%m-%d") '), '=', $initial_date)
			->whereIn('warehouse_movements.movement_type_id', [11, 12])
			->whereNotIn('warehouse_movements.referral_guide_series', [5,138])
			->whereIn('articles.id', [23])
			->sum('warehouse_movement_details.converted_amount');



		$gra_gln = WarehouseMovementDetail::leftjoin('warehouse_movements', 'warehouse_movement_details.warehouse_movement_id', '=', 'warehouse_movements.id')
			->leftjoin('articles', 'warehouse_movement_details.article_code', '=', 'articles.id')
			->where(DB::Raw('DATE_FORMAT(warehouse_movements.traslate_date, "%Y-%m-%d") '), '=', $initial_date)
			->whereIn('warehouse_movements.movement_type_id', [11, 12])
			->whereNotIn('warehouse_movements.referral_guide_series', [5,138])
			->whereIn('articles.id', [24])
			->sum('warehouse_movement_details.converted_amount');

		$kggra_gln = ($gra_gln) * 2.017;



		$env_gen5k = WarehouseMovementDetail::leftjoin('warehouse_movements', 'warehouse_movement_details.warehouse_movement_id', '=', 'warehouse_movements.id')
			->leftjoin('articles', 'warehouse_movement_details.article_code', '=', 'articles.id')
			->where(DB::Raw('DATE_FORMAT(warehouse_movements.traslate_date, "%Y-%m-%d") '), '=', $initial_date)
			->whereIn('warehouse_movements.movement_type_id', [11, 12])
			->whereNotIn('warehouse_movements.referral_guide_series', [5,138])
			->whereIn('articles.subgroup_id', [55])
			->whereIn('articles.convertion', [5])
			->sum('warehouse_movement_details.converted_amount');

		$kggen5k = ($env_gen5k) * 5;

		$env_gen10k = WarehouseMovementDetail::leftjoin('warehouse_movements', 'warehouse_movement_details.warehouse_movement_id', '=', 'warehouse_movements.id')
			->leftjoin('articles', 'warehouse_movement_details.article_code', '=', 'articles.id')
			->where(DB::Raw('DATE_FORMAT(warehouse_movements.traslate_date, "%Y-%m-%d") '), '=', $initial_date)
			->whereIn('warehouse_movements.movement_type_id', [11, 12])
			->whereNotIn('warehouse_movements.referral_guide_series', [5,138])
			->whereIn('articles.subgroup_id', [56])
			->whereIn('articles.convertion', [10])
			->sum('warehouse_movement_details.converted_amount');

		$kggen10k = ($env_gen10k) * 10;

		$env_gen15k = WarehouseMovementDetail::leftjoin('warehouse_movements', 'warehouse_movement_details.warehouse_movement_id', '=', 'warehouse_movements.id')
			->leftjoin('articles', 'warehouse_movement_details.article_code', '=', 'articles.id')
			->where(DB::Raw('DATE_FORMAT(warehouse_movements.traslate_date, "%Y-%m-%d") '), '=', $initial_date)
			->whereIn('warehouse_movements.movement_type_id', [11, 12])
			->whereNotIn('warehouse_movements.referral_guide_series', [5,138])
			->whereIn('articles.subgroup_id', [57])
			->whereIn('articles.convertion', [15])
			->sum('warehouse_movement_details.converted_amount');

		$kggen15k = ($env_gen15k) * 15;

		$env_gen45k = WarehouseMovementDetail::leftjoin('warehouse_movements', 'warehouse_movement_details.warehouse_movement_id', '=', 'warehouse_movements.id')
			->leftjoin('articles', 'warehouse_movement_details.article_code', '=', 'articles.id')
			->where(DB::Raw('DATE_FORMAT(warehouse_movements.traslate_date, "%Y-%m-%d") '), '=', $initial_date)
			->whereIn('warehouse_movements.movement_type_id', [11, 12])
			->whereNotIn('warehouse_movements.referral_guide_series', [5,138])
			->whereIn('articles.subgroup_id', [58])
			->whereIn('articles.convertion', [45])
			->sum('warehouse_movement_details.converted_amount');

		$kggen45k = ($env_gen45k) * 45;



		$env_tran5k = WarehouseMovementDetail::leftjoin('warehouse_movements', 'warehouse_movement_details.warehouse_movement_id', '=', 'warehouse_movements.id')
			->leftjoin('articles', 'warehouse_movement_details.article_code', '=', 'articles.id')
			->where(DB::Raw('DATE_FORMAT(warehouse_movements.traslate_date, "%Y-%m-%d") '), '=', $initial_date)
			->where('warehouse_movements.state', '=', 0)
			->whereIn('warehouse_movements.movement_type_id', [11, 12])
			->whereNotIn('warehouse_movements.referral_guide_series', [5,138])
			->whereIn('articles.subgroup_id', [55])
			->whereIn('articles.convertion', [5])
			->sum('warehouse_movement_details.converted_amount');


		$kgtran5k = ($env_tran5k) * 5;


		$env_tran10k = WarehouseMovementDetail::leftjoin('warehouse_movements', 'warehouse_movement_details.warehouse_movement_id', '=', 'warehouse_movements.id')
			->leftjoin('articles', 'warehouse_movement_details.article_code', '=', 'articles.id')
			->where(DB::Raw('DATE_FORMAT(warehouse_movements.traslate_date, "%Y-%m-%d") '), '=', $initial_date)
			->where('warehouse_movements.state', '=', 0)
			->whereIn('warehouse_movements.movement_type_id', [11, 12])
			->whereNotIn('warehouse_movements.referral_guide_series', [5,138])
			->whereIn('articles.subgroup_id', [56])
			->whereIn('articles.convertion', [10])
			->sum('warehouse_movement_details.converted_amount');

		$kgtran10k = ($env_tran10k) * 10;


		$env_tran15k = WarehouseMovementDetail::leftjoin('warehouse_movements', 'warehouse_movement_details.warehouse_movement_id', '=', 'warehouse_movements.id')
			->leftjoin('articles', 'warehouse_movement_details.article_code', '=', 'articles.id')
			->where(DB::Raw('DATE_FORMAT(warehouse_movements.traslate_date, "%Y-%m-%d") '), '=', $initial_date)
			->where('warehouse_movements.state', '=', 0)
			->whereIn('warehouse_movements.movement_type_id', [11, 12])
			->whereNotIn('warehouse_movements.referral_guide_series', [5,138])
			->whereIn('articles.subgroup_id', [57])
			->whereIn('articles.convertion', [15])
			->sum('warehouse_movement_details.converted_amount');

		$kgtran15k = ($env_tran15k) * 15;

		$env_tran45k = WarehouseMovementDetail::leftjoin('warehouse_movements', 'warehouse_movement_details.warehouse_movement_id', '=', 'warehouse_movements.id')
			->leftjoin('articles', 'warehouse_movement_details.article_code', '=', 'articles.id')
			->where(DB::Raw('DATE_FORMAT(warehouse_movements.traslate_date, "%Y-%m-%d") '), '=', $initial_date)
			->where('warehouse_movements.state', '=', 0)
			->whereIn('warehouse_movements.movement_type_id', [11, 12])
			->whereNotIn('warehouse_movements.referral_guide_series', [5,138])
			->whereIn('articles.subgroup_id', [58])
			->whereIn('articles.convertion', [45])
			->sum('warehouse_movement_details.converted_amount');

		$kgtran45k = ($env_tran45k) * 45;



		$gra_tran1k = WarehouseMovementDetail::leftjoin('warehouse_movements', 'warehouse_movement_details.warehouse_movement_id', '=', 'warehouse_movements.id')
			->leftjoin('articles', 'warehouse_movement_details.article_code', '=', 'articles.id')
			->where(DB::Raw('DATE_FORMAT(warehouse_movements.traslate_date, "%Y-%m-%d") '), '=', $initial_date)
			->where('warehouse_movements.state', '=', 0)
			->whereIn('warehouse_movements.movement_type_id', [11, 12])
			->whereNotIn('warehouse_movements.referral_guide_series', [5,138])
			->whereIn('articles.id', [23])
			->sum('warehouse_movement_details.converted_amount');



		$gra_tran_gln = WarehouseMovementDetail::leftjoin('warehouse_movements', 'warehouse_movement_details.warehouse_movement_id', '=', 'warehouse_movements.id')
			->leftjoin('articles', 'warehouse_movement_details.article_code', '=', 'articles.id')
			->where(DB::Raw('DATE_FORMAT(warehouse_movements.traslate_date, "%Y-%m-%d") '), '=', $initial_date)
			->where('warehouse_movements.state', '=', 0)
			->whereIn('warehouse_movements.movement_type_id', [11, 12])
			->whereNotIn('warehouse_movements.referral_guide_series', [5,138])
			->whereIn('articles.id', [24])
			->sum('warehouse_movement_details.converted_amount');

		$tran_gra_gln = ($gra_tran_gln) * 2.017;






		$env_liq5k = WarehouseMovementDetail::leftjoin('warehouse_movements', 'warehouse_movement_details.warehouse_movement_id', '=', 'warehouse_movements.id')
			->leftjoin('articles', 'warehouse_movement_details.article_code', '=', 'articles.id')
			->where(DB::Raw('DATE_FORMAT(warehouse_movements.traslate_date, "%Y-%m-%d") '), '=', $initial_date)
			->where('warehouse_movements.state', '=', 1)
			->whereIn('warehouse_movements.movement_type_id', [11, 12])
			->whereNotIn('warehouse_movements.referral_guide_series', [5,138])
			->whereIn('articles.subgroup_id', [55])
			->whereIn('articles.convertion', [5])
			->sum('warehouse_movement_details.converted_amount');


		$kgliq5k = ($env_liq5k) * 5;


		$env_liq10k = WarehouseMovementDetail::leftjoin('warehouse_movements', 'warehouse_movement_details.warehouse_movement_id', '=', 'warehouse_movements.id')
			->leftjoin('articles', 'warehouse_movement_details.article_code', '=', 'articles.id')
			->where(DB::Raw('DATE_FORMAT(warehouse_movements.traslate_date, "%Y-%m-%d") '), '=', $initial_date)
			->where('warehouse_movements.state', '=', 1)
			->whereIn('warehouse_movements.movement_type_id', [11, 12])
			->whereNotIn('warehouse_movements.referral_guide_series', [5,138])
			->whereIn('articles.subgroup_id', [56])
			->whereIn('articles.convertion', [10])
			->sum('warehouse_movement_details.converted_amount');

		$kgliq10k = ($env_liq10k) * 10;


		$env_liq15k = WarehouseMovementDetail::leftjoin('warehouse_movements', 'warehouse_movement_details.warehouse_movement_id', '=', 'warehouse_movements.id')
			->leftjoin('articles', 'warehouse_movement_details.article_code', '=', 'articles.id')
			->where(DB::Raw('DATE_FORMAT(warehouse_movements.traslate_date, "%Y-%m-%d") '), '=', $initial_date)
			->where('warehouse_movements.state', '=', 1)
			->whereIn('warehouse_movements.movement_type_id', [11, 12])
			->whereNotIn('warehouse_movements.referral_guide_series', [5,138])
			->whereIn('articles.subgroup_id', [57])
			->whereIn('articles.convertion', [15])
			->sum('warehouse_movement_details.converted_amount');

		$kgliq15k = ($env_liq15k) * 15;

		$env_liq45k = WarehouseMovementDetail::leftjoin('warehouse_movements', 'warehouse_movement_details.warehouse_movement_id', '=', 'warehouse_movements.id')
			->leftjoin('articles', 'warehouse_movement_details.article_code', '=', 'articles.id')
			->where(DB::Raw('DATE_FORMAT(warehouse_movements.traslate_date, "%Y-%m-%d") '), '=', $initial_date)
			->where('warehouse_movements.state', '=', 1)
			->whereIn('warehouse_movements.movement_type_id', [11, 12])
			->whereNotIn('warehouse_movements.referral_guide_series', [5,138])
			->whereIn('articles.subgroup_id', [58])
			->whereIn('articles.convertion', [45])
			->sum('warehouse_movement_details.converted_amount');

		$kgliq45k = ($env_liq45k) * 45;



		$gra_liq1k = WarehouseMovementDetail::leftjoin('warehouse_movements', 'warehouse_movement_details.warehouse_movement_id', '=', 'warehouse_movements.id')
			->leftjoin('articles', 'warehouse_movement_details.article_code', '=', 'articles.id')
			->where(DB::Raw('DATE_FORMAT(warehouse_movements.traslate_date, "%Y-%m-%d") '), '=', $initial_date)
			->where('warehouse_movements.state', '=', 1)
			->whereIn('warehouse_movements.movement_type_id', [11, 12])
			->whereNotIn('warehouse_movements.referral_guide_series', [5,138])
			->whereIn('articles.id', [23])
			->sum('warehouse_movement_details.converted_amount');


		$gra_liq_gln = WarehouseMovementDetail::leftjoin('warehouse_movements', 'warehouse_movement_details.warehouse_movement_id', '=', 'warehouse_movements.id')
			->leftjoin('articles', 'warehouse_movement_details.article_code', '=', 'articles.id')
			->where(DB::Raw('DATE_FORMAT(warehouse_movements.traslate_date, "%Y-%m-%d") '), '=', $initial_date)
			->where('warehouse_movements.state', '=', 1)
			->whereIn('warehouse_movements.movement_type_id', [11, 12])
			->whereNotIn('warehouse_movements.referral_guide_series', [5,138])
			->whereIn('articles.id', [24])
			->sum('warehouse_movement_details.converted_amount');

		$liq_gra_gln = ($gra_liq_gln) * 2.017;



		/*---------------------GLP PENDIENTE-------------------*/

		$env_last5k = WarehouseMovementDetail::leftjoin('warehouse_movements', 'warehouse_movement_details.warehouse_movement_id', '=', 'warehouse_movements.id')
			->leftjoin('articles', 'warehouse_movement_details.article_code', '=', 'articles.id')
			->where(DB::Raw('DATE_FORMAT(warehouse_movements.traslate_date, "%Y-%m-%d") '), '>=', $first_date)
			->where(DB::Raw('DATE_FORMAT(warehouse_movements.traslate_date, "%Y-%m-%d") '), '<', $initial_date)
			->where('warehouse_movements.state', '=', 0)
			->whereIn('warehouse_movements.movement_type_id', [11, 12])
			->whereNotIn('warehouse_movements.referral_guide_series', [5,138])
			->whereIn('articles.subgroup_id', [55])
			->whereIn('articles.convertion', [5])
			->sum('warehouse_movement_details.converted_amount');


		$kglast5k = ($env_last5k) * 5;


		$env_last10k = WarehouseMovementDetail::leftjoin('warehouse_movements', 'warehouse_movement_details.warehouse_movement_id', '=', 'warehouse_movements.id')
			->leftjoin('articles', 'warehouse_movement_details.article_code', '=', 'articles.id')
			->where(DB::Raw('DATE_FORMAT(warehouse_movements.traslate_date, "%Y-%m-%d") '), '>=', $first_date)
			->where(DB::Raw('DATE_FORMAT(warehouse_movements.traslate_date, "%Y-%m-%d") '), '<', $initial_date)
			->where('warehouse_movements.state', '=', 0)
			->whereIn('warehouse_movements.movement_type_id', [11, 12])
			->whereNotIn('warehouse_movements.referral_guide_series', [5,138])
			->whereIn('articles.subgroup_id', [56])
			->whereIn('articles.convertion', [10])
			->sum('warehouse_movement_details.converted_amount');

		$kglast10k = ($env_last10k) * 10;


		$env_last15k = WarehouseMovementDetail::leftjoin('warehouse_movements', 'warehouse_movement_details.warehouse_movement_id', '=', 'warehouse_movements.id')
			->leftjoin('articles', 'warehouse_movement_details.article_code', '=', 'articles.id')
			->where(DB::Raw('DATE_FORMAT(warehouse_movements.traslate_date, "%Y-%m-%d") '), '>=', $first_date)
			->where(DB::Raw('DATE_FORMAT(warehouse_movements.traslate_date, "%Y-%m-%d") '), '<', $initial_date)
			->where('warehouse_movements.state', '=', 0)
			->whereIn('warehouse_movements.movement_type_id', [11, 12])
			->whereNotIn('warehouse_movements.referral_guide_series', [5,138])
			->whereIn('articles.subgroup_id', [57])
			->whereIn('articles.convertion', [15])
			->sum('warehouse_movement_details.converted_amount');

		$kglast15k = ($env_last15k) * 15;

		$env_last45k = WarehouseMovementDetail::leftjoin('warehouse_movements', 'warehouse_movement_details.warehouse_movement_id', '=', 'warehouse_movements.id')
			->leftjoin('articles', 'warehouse_movement_details.article_code', '=', 'articles.id')
			->where(DB::Raw('DATE_FORMAT(warehouse_movements.traslate_date, "%Y-%m-%d") '), '>=', $first_date)
			->where(DB::Raw('DATE_FORMAT(warehouse_movements.traslate_date, "%Y-%m-%d") '), '<', $initial_date)
			->where('warehouse_movements.state', '=', 0)
			->whereIn('warehouse_movements.movement_type_id', [11, 12])
			->whereNotIn('warehouse_movements.referral_guide_series', [5,138])
			->whereIn('articles.subgroup_id', [58])
			->whereIn('articles.convertion', [45])
			->sum('warehouse_movement_details.converted_amount');

		$kglast45k = ($env_last45k) * 45;



		$gra_last1k = WarehouseMovementDetail::leftjoin('warehouse_movements', 'warehouse_movement_details.warehouse_movement_id', '=', 'warehouse_movements.id')
			->leftjoin('articles', 'warehouse_movement_details.article_code', '=', 'articles.id')
			->where(DB::Raw('DATE_FORMAT(warehouse_movements.traslate_date, "%Y-%m-%d") '), '>=', $first_date)
			->where(DB::Raw('DATE_FORMAT(warehouse_movements.traslate_date, "%Y-%m-%d") '), '<', $initial_date)
			->where('warehouse_movements.state', '=', 0)
			->whereIn('warehouse_movements.movement_type_id', [11, 12])
			->whereNotIn('warehouse_movements.referral_guide_series', [5,138])
			->whereIn('articles.id', [23])
			->sum('warehouse_movement_details.converted_amount');


		$gra_last_gln = WarehouseMovementDetail::leftjoin('warehouse_movements', 'warehouse_movement_details.warehouse_movement_id', '=', 'warehouse_movements.id')
			->leftjoin('articles', 'warehouse_movement_details.article_code', '=', 'articles.id')
			->where(DB::Raw('DATE_FORMAT(warehouse_movements.traslate_date, "%Y-%m-%d") '), '>=', $first_date)
			->where(DB::Raw('DATE_FORMAT(warehouse_movements.traslate_date, "%Y-%m-%d") '), '<', $initial_date)
			->whereNotIn('warehouse_movements.referral_guide_series', [5,138])
			->where('warehouse_movements.state', '=', 0)
			->whereIn('warehouse_movements.movement_type_id', [11, 12])
			->whereIn('articles.id', [24])
			->sum('warehouse_movement_details.converted_amount');

		$last_gra_gln = ($gra_last_gln) * 2.017;



		/*-COSTO GLP-*/

		$year_prev = ($price_mes == '01') ? $price_year - 1 : $price_year;
		$mes_prev = ($price_mes == '01') ? '12' : $price_mes - 1;




		$movements_g = WarehouseMovement::join('warehouse_movement_details', 'warehouse_movement_details.warehouse_movement_id', '=', 'warehouse_movements.id')
			->where(function ($query) use ($price_year, $price_mes, $initial_date) {
				$query->where('warehouse_movements.price_año', $price_year)
					->where('warehouse_movements.price_mes', $price_mes)
					->where('warehouse_movements.movement_type_id', 30)
					->where('warehouse_movements.origin', 27)
					->where(DB::Raw('DATE_FORMAT(warehouse_movements.traslate_date, "%Y-%m-%d") '), '<=', $initial_date);
			})
			->orWhere(function ($query) use ($year_prev, $mes_prev, $initial_date) {
				$query->where('warehouse_movements.price_año', $year_prev)
					->where('warehouse_movements.price_mes', $mes_prev)
					->where('warehouse_movement_details.digit2', '<>', null)
					->where('warehouse_movement_details.digit2', '<>', 0)
					->where('warehouse_movements.movement_type_id', 30)
					->where('warehouse_movements.origin', 27)
					->where(DB::Raw('DATE_FORMAT(warehouse_movements.traslate_date, "%Y-%m-%d") '), '<=', $initial_date);
			})
			->where('warehouse_movements.movement_class_id', 2)
			//	->where(DB::Raw('DATE_FORMAT(warehouse_movements.created_at, "%Y-%m-%d") '), '<=', $initial_date)
			->select(
				'warehouse_movement_details.id',
				'warehouse_movements.warehouse_type_id',
				'warehouse_movements.movement_type_id',
				'warehouse_movements.referral_serie_number',
				'warehouse_movements.referral_voucher_number',
				'warehouse_movements.origin',
				'warehouse_movement_details.digit1',
				'warehouse_movement_details.digit2',
				'warehouse_movements.soles_1',
				'warehouse_movements.soles_2',
				'warehouse_movements.price_mes',
				'warehouse_movements.cost_glp'
			)
			->get();

		$total_g = 0;
		$quantity_g = 0;

		$soles_glp_g = 0;
		$kgs_glp_g = 0;

		foreach ($movements_g as $e) {


			if ($e->digit2 != null &&  $e->digit2 != 0) {

				if ($e->price_mes == $price_mes) {

					$quantity_g += $e->digit1;
					$total_g += $e->digit1 * $e->cost_glp;
				} else {
					$quantity_g += $e->digit2;
					$total_g += $e->digit2 * $e->cost_glp;
				}
			} else {
				$quantity_g += $e->digit1;
				$total_g +=  $e->digit1 * $e->cost_glp;
			}
		}



		$soles_glp_g += $total_g;
		$kgs_glp_g += $quantity_g;


		$cost_glp = $kgs_glp_g > 0 ? ($soles_glp_g / ($kgs_glp_g / 1000) / 1000) : null;




		/*---------------------------------*/
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
				'company_short_name' => 'Credito Total',
				'total' => $total_credit
			],
			[
				'company_short_name' => 'Credito',
				'total' => $total_credit_pend
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




		if ($export) {
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





			$sheet->getStyle('E2')->applyFromArray([
				'font' => [
					'color' => array('rgb' => 'FFFFFF'),
					'bold' => true,
					'size' => 12,
				],
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
				],
				'fill' => [
					'fillType' => Fill::FILL_SOLID,
					'startColor' => array('rgb' => '44546a')
				]
			]);


			/*glp generado */
			$sheet->setCellValue('C5', 'GRANEL');

			$sheet->setCellValue('C6', 'ENVASADO');
			$sheet->setCellValue('C7', 'TOTAL GLP');

			$sheet->setCellValue('D4', 'GLP DESPACHADO');
			$sheet->getStyle('D4')->applyFromArray([
				'font' => [
					'color' => array('rgb' => 'FFFFFF'),
					'bold' => true,
					'size' => 12,
				],
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
				],
				'fill' => [
					'fillType' => Fill::FILL_SOLID,
					'startColor' => array('rgb' => '44546a')
				]
			]);


			$gra_gen = $gra_gen1k + $kggra_gln;
			$env_gen = $kggen5k + $kggen10k + $kggen15k + $kggen45k;

			$sheet->setCellValue('D5', $gra_gen);
			$sheet->setCellValue('D6', $env_gen);

			$total_gen = $env_gen + $gra_gen;

			$sheet->setCellValue('D7', $total_gen);

			/*fin glp generado */


			/*glp liquidado */


			$sheet->setCellValue('D9', 'GLP LIQUIDADO');
			$sheet->getStyle('D9')->applyFromArray([
				'font' => [
					'color' => array('rgb' => 'FFFFFF'),
					'bold' => true,
					'size' => 12,
				],
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
				],
				'fill' => [
					'fillType' => Fill::FILL_SOLID,
					'startColor' => array('rgb' => '44546a')
				]
			]);

			$sheet->setCellValue('C10', 'GRANEL');
			$sheet->setCellValue('C11', 'ENVASADO');
			$sheet->setCellValue('C12', 'TOTAL GLP');

			$gra_liq = $gra_liq1k + $liq_gra_gln;
			$env_liq = $kgliq5k + $kgliq10k + $kgliq15k + $kgliq45k;
			$sheet->setCellValue('D10', $gra_liq);
			$sheet->setCellValue('D11', $env_liq);
			$total_liq = $gra_liq + $env_liq;
			$sheet->setCellValue('D12', $total_liq);
			/* FIN glp liquidado */

			/*GLP TRANSITO DEL DÍA */

			$sheet->setCellValue('D14', ' GLP TRANSITO DEL DÍA');
			$sheet->getStyle('D14')->applyFromArray([
				'font' => [
					'color' => array('rgb' => 'FFFFFF'),
					'bold' => true,
					'size' => 12,
				],
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
				],
				'fill' => [
					'fillType' => Fill::FILL_SOLID,
					'startColor' => array('rgb' => '44546a')
				]
			]);

			$sheet->setCellValue('C15', 'GRANEL');
			$sheet->setCellValue('C16', 'ENVASADO');
			$sheet->setCellValue('C17', 'TOTAL GLP');

			$gra_tran = $gra_tran1k + $tran_gra_gln;
			$env_tran = $kgtran5k + $kgtran10k + $kgtran15k + $kgtran45k;
			$sheet->setCellValue('D15', $gra_tran);
			$sheet->setCellValue('D16', $env_tran);

			$total_tran = $gra_tran + $env_tran;


			$sheet->setCellValue('D17', $total_tran);

			/*FIN GLP TRANSITO DEL DÍA */


			/*GLP TRANSITO ACUM. */
			$sheet->setCellValue('D20', 'GLP TRANSITO ANTERIOR ACUM.');
			$sheet->getStyle('D20')->applyFromArray([
				'font' => [
					'color' => array('rgb' => 'FFFFFF'),
					'bold' => true,
					'size' => 12,
				],
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
				],
				'fill' => [
					'fillType' => Fill::FILL_SOLID,
					'startColor' => array('rgb' => '44546a')
				]
			]);

			$sheet->setCellValue('E20', 'COSTO GLP DEL MES KGS');
			$sheet->getStyle('E20')->applyFromArray([
				'font' => [
					'color' => array('rgb' => 'FFFFFF'),
					'bold' => true,
					'size' => 12,
				],
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
				],
				'fill' => [
					'fillType' => Fill::FILL_SOLID,
					'startColor' => array('rgb' => '44546a')
				]
			]);


			$sheet->setCellValue('E21', $cost_glp);
			$sheet->setCellValue('E22', $cost_glp);


			$sheet->setCellValue('F20', 'VALORIZACIÓN PONDERADA S./.');
			$sheet->getStyle('F20')->applyFromArray([
				'font' => [
					'color' => array('rgb' => 'FFFFFF'),
					'bold' => true,
					'size' => 12,
				],
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
				],
				'fill' => [
					'fillType' => Fill::FILL_SOLID,
					'startColor' => array('rgb' => '44546a')
				]
			]);




			$sheet->setCellValue('C21', 'GRANEL');
			$sheet->setCellValue('C22', 'ENVASADO');
			$sheet->setCellValue('C23', 'TOTAL GLP');
			$gra_rep = $gra_last1k + $last_gra_gln;
			$env_rep = $kglast5k + $kglast10k + $kglast15k + $kglast45k;



			$sheet->setCellValue('D21', $gra_rep);
			$sheet->setCellValue('D22', $env_rep);


			$total_rep = $gra_rep + $env_rep;
			$sheet->setCellValue('D23', $total_rep);

			$pond_e = $cost_glp * $gra_rep;
			$pond_g = $cost_glp * $env_rep;

			$sheet->setCellValue('F21', $pond_e);
			$sheet->setCellValue('F22', $pond_g);


			$total_pond = $pond_e + $pond_g;
			$sheet->setCellValue('F23', $total_pond);




			/*FIN GLP TRANSITO ACUM.*/






			/*----------------------FIN CUADRO DE STOCK GLP-----------------------------------------------------*/



			$sheet->setCellValue('E32', 'TONELADAS');
			$sheet->getStyle('E32')->applyFromArray([
				'font' => [
					'color' => array('rgb' => 'FFFFFF'),
					'bold' => true,
					'size' => 12,
				],
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
				],
				'fill' => [
					'fillType' => Fill::FILL_SOLID,
					'startColor' => array('rgb' => '44546a')
				]
			]);
			$sheet->setCellValue('D32', 'VENTA TOTAL');
			$sheet->getStyle('D32')->applyFromArray([
				'font' => [
					'color' => array('rgb' => 'FFFFFF'),
					'bold' => true,
					'size' => 12,
				],
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
				],
				'fill' => [
					'fillType' => Fill::FILL_SOLID,
					'startColor' => array('rgb' => '44546a')
				]
			]);

			$total_dia_liq_contado = $efective_venta + $deposit_venta;

			$tot_dia_tn = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
				->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
				->whereNotIn('sales.client_id', $client_ids)
				->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=',  $initial_date)
				->where(DB::Raw('sales.sale_date'), '=',  $initial_date)
				->select('sale_details.kg')
				->sum('sale_details.kg');

			$tn_dia = $tot_dia_tn / 1000;

			$sheet->setCellValue('C33', 'LIQUIDACION GLP TOTAL');
			$sheet->setCellValue('E33', $total_tn);
			$sheet->setCellValue('D33', $total_venta_del_dia);
			$sheet->setCellValue('C34', 'LIQUIDACIÓN GLP DEL DÍA');
			$sheet->setCellValue('E34', $tn_dia);
			$sheet->setCellValue('D34', $total_liquidado_del_dia);






			$sheet->setCellValue('C37', 'CAJA');
			$efective_env_dia = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
				->whereNotIn('sales.client_id', $client_ids)
				->whereIn('clients.business_unit_id', [1, 3])
				->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=',  $initial_date)
				->where(DB::Raw('sales.sale_date'), '=',  $initial_date)
				->select('sales.efective')
				->sum('sales.efective');

			$sheet->setCellValue('D37', $efective_env_dia);

			$sheet->setCellValue('C38', 'BANCOS');

			$deposit_env_dia = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
				->whereNotIn('sales.client_id', $client_ids)
				->whereIn('clients.business_unit_id', [1, 3])
				->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=',  $initial_date)
				->where(DB::Raw('sales.sale_date'), '=',  $initial_date)
				->select('sales.deposit')
				->sum('sales.deposit');

			$sheet->setCellValue('D38', $deposit_env_dia);


			$sheet->setCellValue('C35', 'TOTAL PAGO CONTADO');

			$sheet->setCellValue('D35', $total_dia_liq_contado);



			$sheet->setCellValue('C36', 'CONTADO ENVASADO');

			$contado_de_dia_env = $efective_env_dia + $deposit_env_dia;


			$sheet->setCellValue('D36', $contado_de_dia_env);







			$sheet->setCellValue('C40', 'CAJA');

			$efective_gra_dia = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
				->whereNotIn('sales.client_id', $client_ids)
				->where('clients.business_unit_id', 2)
				->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=',  $initial_date)
				->where(DB::Raw('sales.sale_date'), '=',  $initial_date)
				->select('sales.efective')
				->sum('sales.efective');

			$sheet->setCellValue('D40', $efective_gra_dia);


			$sheet->setCellValue('C41', 'BANCOS');

			$deposit_gra_dia = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
				->whereNotIn('sales.client_id', $client_ids)
				->where('clients.business_unit_id', 2)
				->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=',  $initial_date)
				->where(DB::Raw('sales.sale_date'), '=',  $initial_date)
				->select('sales.deposit')
				->sum('sales.deposit');

			$sheet->setCellValue('D41', $deposit_gra_dia);


			$sheet->setCellValue('C39', 'CONTADO GRANEL');

			$contado_de_dia_gra = $efective_gra_dia + $deposit_gra_dia;


			$sheet->setCellValue('D39', $contado_de_dia_gra);



			$sheet->setCellValue('C42', 'CREDITO DEL DÍA');

			$sheet->setCellValue('D42', $credit_dia_venta);


			$sheet->setCellValue('C43', 'CREDITO ENVASADO');

			$credit_venta_env = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
				->whereNotIn('sales.client_id', $client_ids)
				->whereIn('clients.business_unit_id', [1, 3])
				->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=',  $initial_date)
				->where(DB::Raw('sales.sale_date'), '=', $initial_date)
				->select('sales.pre_balance')
				->sum('sales.pre_balance');

			$pending_venta_env = Liquidation::leftjoin('sales', 'liquidations.sale_id', '=', 'sales.id')
				->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
				->whereNotIn('sales.client_id', $client_ids)
				->whereIn('clients.business_unit_id', [1, 3])
				->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=',  $initial_date)
				->where(DB::Raw('sales.sale_date'), '=', $initial_date)
				->where('liquidations.payment_method_id', '=', 10)
				->select('liquidations.amount')
				->sum('liquidations.amount');

			$total_credit_venta_env = $credit_venta_env + $pending_venta_env;




			$sheet->setCellValue('D43', $total_credit_venta_env);


			$sheet->setCellValue('C44', 'CREDITO GRANEL');
			$credit_venta_gra = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
				->whereNotIn('sales.client_id', $client_ids)
				->whereIn('clients.business_unit_id', [2])
				->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=',  $initial_date)
				->where(DB::Raw('sales.sale_date'), '=', $initial_date)
				->select('sales.pre_balance')
				->sum('sales.pre_balance');

			$pending_venta_gra = Liquidation::leftjoin('sales', 'liquidations.sale_id', '=', 'sales.id')
				->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
				->whereNotIn('sales.client_id', $client_ids)
				->whereIn('clients.business_unit_id', [2])
				->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=',  $initial_date)
				->where(DB::Raw('sales.sale_date'), '=', $initial_date)
				->where('liquidations.payment_method_id', '=', 10)
				->select('liquidations.amount')
				->sum('liquidations.amount');

			$total_credit_venta_gra = $credit_venta_gra + $pending_venta_gra;

			$sheet->setCellValue('D44', $total_credit_venta_gra);

			$sheet->setCellValue('C45', 'LIQUIDACIÓN GLP DEL DÍAS ANTERIORES');

			$total_liq_venta_ant = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
				->whereNotIn('sales.client_id', $client_ids)
				->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=',  $initial_date)
				->where(DB::Raw('sales.sale_date'), '<', $initial_date)
				->select('sales.total_perception')
				->sum('sales.total_perception');

			$sheet->setCellValue('D45', $total_liq_venta_ant);


			$sheet->setCellValue('C47', 'CAJA');
			$efective_env_ant = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
				->whereNotIn('sales.client_id', $client_ids)
				->whereIn('clients.business_unit_id', [1, 3])
				->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=',  $initial_date)
				->where(DB::Raw('sales.sale_date'), '<',  $initial_date)
				->select('sales.efective')
				->sum('sales.efective');

			$sheet->setCellValue('D47', $efective_env_ant);
			$sheet->setCellValue('C48', 'BANCOS');
			$deposit_env_ant = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
				->whereNotIn('sales.client_id', $client_ids)
				->whereIn('clients.business_unit_id', [1, 3])
				->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=',  $initial_date)
				->where(DB::Raw('sales.sale_date'), '<',  $initial_date)
				->select('sales.deposit')
				->sum('sales.deposit');

			$sheet->setCellValue('D48', $deposit_env_ant);


			$sheet->setCellValue('C46', 'CONTADO ENVASADO');
			$contado_ant_env = $efective_env_ant + $deposit_env_ant;

			$sheet->setCellValue('D46', $contado_ant_env);


			$sheet->setCellValue('C50', 'CAJA');

			$efective_gra_ant = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
				->whereNotIn('sales.client_id', $client_ids)
				->where('clients.business_unit_id', 2)
				->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=',  $initial_date)
				->where(DB::Raw('sales.sale_date'), '<',  $initial_date)
				->select('sales.efective')
				->sum('sales.efective');

			$sheet->setCellValue('D50', $efective_gra_ant);

			$sheet->setCellValue('C51', 'BANCOS');

			$deposit_gra_ant = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
				->whereNotIn('sales.client_id', $client_ids)
				->where('clients.business_unit_id', 2)
				->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=',  $initial_date)
				->where(DB::Raw('sales.sale_date'), '<',  $initial_date)
				->select('sales.deposit')
				->sum('sales.deposit');

			$sheet->setCellValue('D51', $deposit_gra_ant);


			$sheet->setCellValue('C49', 'CONTADO GRANEL');
			$contado_ant_gra = $efective_gra_ant + $deposit_gra_ant;


			$sheet->setCellValue('D49', $contado_ant_gra);

			/* CREDITOS DÍAS ANTERIORES */

			$sheet->setCellValue('C52', 'CREDITO ENVASADO');

			$credit_ant_venta_env = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
				->whereNotIn('sales.client_id', $client_ids)
				->whereIn('clients.business_unit_id', [1, 3])
				->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=',  $initial_date)
				->where(DB::Raw('sales.sale_date'), '<',  $initial_date)
				->select('sales.pre_balance')
				->sum('sales.pre_balance');

			$pending_ant_venta_env = Liquidation::leftjoin('sales', 'liquidations.sale_id', '=', 'sales.id')
				->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
				->whereNotIn('sales.client_id', $client_ids)
				->whereIn('clients.business_unit_id', [1, 3])
				->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=',  $initial_date)
				->where(DB::Raw('sales.sale_date'), '<',  $initial_date)
				->where('liquidations.payment_method_id', '=', 10)
				->select('liquidations.amount')
				->sum('liquidations.amount');

			$total_credit_ant_env = $credit_ant_venta_env + $pending_ant_venta_env;


			$sheet->setCellValue('D52', $total_credit_ant_env);

			$sheet->setCellValue('C53', 'CREDITO GRANEL');

			$credit_ant_venta_gra = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
				->whereNotIn('sales.client_id', $client_ids)
				->whereIn('clients.business_unit_id', [2])
				->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=',  $initial_date)
				->where(DB::Raw('sales.sale_date'), '<',  $initial_date)
				->select('sales.pre_balance')
				->sum('sales.pre_balance');

			$pending_ant_venta_gra = Liquidation::leftjoin('sales', 'liquidations.sale_id', '=', 'sales.id')
				->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
				->whereNotIn('sales.client_id', $client_ids)
				->whereIn('clients.business_unit_id', [2])
				->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=',  $initial_date)
				->where(DB::Raw('sales.sale_date'), '<',  $initial_date)
				->where('liquidations.payment_method_id', '=', 10)
				->select('liquidations.amount')
				->sum('liquidations.amount');

			$total_credit_ant_gra = $credit_ant_venta_gra + $pending_ant_venta_gra;

			$sheet->setCellValue('D53', $total_credit_ant_gra);


			$sheet->setCellValue('C54', 'TOTAL CREDITO DÍAS ANTERIORES');
			$creditfinal_ant_gra = $total_credit_ant_gra + $total_credit_ant_env;


			$sheet->setCellValue('D54', $creditfinal_ant_gra);





			/*----------------------FIN CUADRO DE VENTAS-----------------------------------------------------*/



			$sheet->setCellValue('E56', 'TONELADAS');
			$sheet->getStyle('E56')->applyFromArray([
				'font' => [
					'color' => array('rgb' => 'FFFFFF'),
					'bold' => true,
					'size' => 12,
				],
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
				],
				'fill' => [
					'fillType' => Fill::FILL_SOLID,
					'startColor' => array('rgb' => '44546a')
				]
			]);
			$sheet->setCellValue('D56', 'VENTA TOTAL');
			$sheet->getStyle('D56')->applyFromArray([
				'font' => [
					'color' => array('rgb' => 'FFFFFF'),
					'bold' => true,
					'size' => 12,
				],
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
				],
				'fill' => [
					'fillType' => Fill::FILL_SOLID,
					'startColor' => array('rgb' => '44546a')
				]
			]);


			$sheet->setCellValue('C57', 'COBRANZA DE CREDITOS ANTERIORES');

			$sheet->setCellValue('D57', $total_cobranza);

			$sheet->setCellValue('C58', 'TOTAL COBRANZA DEL MES');
			$cobranza_mes = Liquidation::leftjoin('sales', 'liquidations.sale_id', '=', 'sales.id')
				->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				->where(DB::Raw('DATE_FORMAT(liquidations.created_at, "%Y-%m-%d") '), '=', $initial_date)
				->where('sales.sale_date', '<=', $initial_date)
				->where('sales.sale_date', '>=', $first_date)
				->whereNotIn('sales.client_id', $client_ids)
				->whereIn('liquidations.collection', [1, 2])
				->select('liquidations.amount')
				->sum('liquidations.amount');

			$sheet->setCellValue('D58', $cobranza_mes);




			$sheet->setCellValue('C60', 'CAJA');
			$cobranza_mes_env_ef = Liquidation::leftjoin('sales', 'liquidations.sale_id', '=', 'sales.id')
				->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				->whereIn('clients.business_unit_id', [1, 3])
				->where(DB::Raw('DATE_FORMAT(liquidations.created_at, "%Y-%m-%d") '), '=', $initial_date)
				->where('sales.sale_date', '<=', $initial_date)
				->where('sales.sale_date', '>=', $first_date)
				->whereNotIn('sales.client_id', $client_ids)
				->where('liquidations.payment_method_id', 1)
				->whereIn('liquidations.collection', [1, 2])
				->select('liquidations.amount')
				->sum('liquidations.amount');

			$sheet->setCellValue('D60', $cobranza_mes_env_ef);
			$sheet->setCellValue('C61', 'BANCOS');
			$cobranza_mes_env_dp = Liquidation::leftjoin('sales', 'liquidations.sale_id', '=', 'sales.id')
				->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				->whereIn('clients.business_unit_id', [1, 3])
				->where(DB::Raw('DATE_FORMAT(liquidations.created_at, "%Y-%m-%d") '), '=', $initial_date)
				->where('sales.sale_date', '<=', $initial_date)
				->where('sales.sale_date', '>=', $first_date)
				->whereNotIn('sales.client_id', $client_ids)
				->whereIn('liquidations.payment_method_id', [3, 2])
				->whereIn('liquidations.collection', [1, 2])
				->select('liquidations.amount')
				->sum('liquidations.amount');

			$sheet->setCellValue('D61', $cobranza_mes_env_dp);


			$sheet->setCellValue('C59', 'COBRANZA ENVASADO');

			$cob_mes_env = $cobranza_mes_env_ef + $cobranza_mes_env_dp;


			$sheet->setCellValue('D59', $cob_mes_env);


			$sheet->setCellValue('C63', 'CAJA');
			$cobranza_mes_gra_ef = Liquidation::leftjoin('sales', 'liquidations.sale_id', '=', 'sales.id')
				->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				->where('clients.business_unit_id', '=', 2)
				->where(DB::Raw('DATE_FORMAT(liquidations.created_at, "%Y-%m-%d") '), '=', $initial_date)
				->where('sales.sale_date', '<=', $initial_date)
				->where('sales.sale_date', '>=', $first_date)
				->whereNotIn('sales.client_id', $client_ids)
				->where('liquidations.payment_method_id', 1)
				->whereIn('liquidations.collection', [1, 2])
				->select('liquidations.amount')
				->sum('liquidations.amount');

			$sheet->setCellValue('D63', $cobranza_mes_gra_ef);
			$sheet->setCellValue('C64', 'BANCOS');
			$cobranza_mes_gra_dp = Liquidation::leftjoin('sales', 'liquidations.sale_id', '=', 'sales.id')
				->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				->where('clients.business_unit_id', '=', 2)
				->where(DB::Raw('DATE_FORMAT(liquidations.created_at, "%Y-%m-%d") '), '=', $initial_date)
				->where('sales.sale_date', '<=', $initial_date)
				->where('sales.sale_date', '>=', $first_date)
				->whereNotIn('sales.client_id', $client_ids)
				->whereIn('liquidations.payment_method_id', [3, 2])
				->whereIn('liquidations.collection', [1, 2])
				->select('liquidations.amount')
				->sum('liquidations.amount');

			$sheet->setCellValue('D64', $cobranza_mes_gra_dp);

			$sheet->setCellValue('C62', 'COBRANZA GRANEL');
			$cob_mes_gra = $cobranza_mes_gra_ef + $cobranza_mes_gra_dp;


			$sheet->setCellValue('D62', $cob_mes_gra);

			$sheet->setCellValue('C65', 'COBRANZA DE MESES ANTERIORES');
			$cobranza_mes_ant = Liquidation::leftjoin('sales', 'liquidations.sale_id', '=', 'sales.id')
				->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				->where(DB::Raw('DATE_FORMAT(liquidations.created_at, "%Y-%m-%d") '), '=', $initial_date)
				->where('sales.sale_date', '<', $first_date)
				->whereNotIn('sales.client_id', $client_ids)
				->whereIn('liquidations.collection', [1, 2])
				->select('liquidations.amount')
				->sum('liquidations.amount');

			$sheet->setCellValue('D65', $cobranza_mes_ant);


			$sheet->setCellValue('C67', 'CAJA');
			$cobranza_ant_env_ef = Liquidation::leftjoin('sales', 'liquidations.sale_id', '=', 'sales.id')
				->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				->whereIn('clients.business_unit_id', [1, 3])
				->where(DB::Raw('DATE_FORMAT(liquidations.created_at, "%Y-%m-%d") '), '=', $initial_date)
				->where('sales.sale_date', '<', $first_date)
				->whereNotIn('sales.client_id', $client_ids)
				->where('liquidations.payment_method_id', 1)
				->whereIn('liquidations.collection', [1, 2])
				->select('liquidations.amount')
				->sum('liquidations.amount');

			$sheet->setCellValue('D67', $cobranza_ant_env_ef);

			$sheet->setCellValue('C68', 'BANCOS');
			$cobranza_ant_env_dp = Liquidation::leftjoin('sales', 'liquidations.sale_id', '=', 'sales.id')
				->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				->whereIn('clients.business_unit_id', [1, 3])
				->where(DB::Raw('DATE_FORMAT(liquidations.created_at, "%Y-%m-%d") '), '=', $initial_date)
				->where('sales.sale_date', '<', $first_date)
				->whereNotIn('sales.client_id', $client_ids)
				->whereIn('liquidations.payment_method_id', [3, 2])
				->whereIn('liquidations.collection', [1, 2])
				->select('liquidations.amount')
				->sum('liquidations.amount');

			$sheet->setCellValue('D68', $cobranza_ant_env_dp);

			$sheet->setCellValue('C66', 'CONTADO ENVASADO');
			$cob_ant_env = $cobranza_ant_env_ef + $cobranza_ant_env_dp;


			$sheet->setCellValue('D66', $cob_ant_env);

			$sheet->setCellValue('C70', 'CAJA');
			$cobranza_ant_gra_ef = Liquidation::leftjoin('sales', 'liquidations.sale_id', '=', 'sales.id')
				->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				->where('clients.business_unit_id', '=', 2)
				->where(DB::Raw('DATE_FORMAT(liquidations.created_at, "%Y-%m-%d") '), '=', $initial_date)
				->where('sales.sale_date', '>=', $first_date)
				->whereNotIn('sales.client_id', $client_ids)
				->where('liquidations.payment_method_id', 1)
				->whereIn('liquidations.collection', [1, 2])
				->select('liquidations.amount')
				->sum('liquidations.amount');

			$sheet->setCellValue('D70', $cobranza_ant_gra_ef);
			$sheet->setCellValue('C71', 'BANCOS');
			$cobranza_ant_gra_dp = Liquidation::leftjoin('sales', 'liquidations.sale_id', '=', 'sales.id')
				->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				->where('clients.business_unit_id', '=', 2)
				->where(DB::Raw('DATE_FORMAT(liquidations.created_at, "%Y-%m-%d") '), '=', $initial_date)
				->where('sales.sale_date', '<', $first_date)
				->whereNotIn('sales.client_id', $client_ids)
				->whereIn('liquidations.payment_method_id', [3, 2])
				->whereIn('liquidations.collection', [1, 2])
				->select('liquidations.amount')
				->sum('liquidations.amount');

			$sheet->setCellValue('D71', $cobranza_ant_gra_dp);
			$sheet->setCellValue('C69', 'CONTADO GRANEL');
			$cob_ant_gra = $cobranza_ant_gra_ef + $cobranza_ant_gra_dp;


			$sheet->setCellValue('D69', $cob_ant_gra);


			/*----------------------FIN CUADRO DE COBRANZAS-----------------------------------------------------*/

			$sheet->setCellValue('E73', 'TONELADAS');
			$sheet->getStyle('E73')->applyFromArray([
				'font' => [
					'color' => array('rgb' => 'FFFFFF'),
					'bold' => true,
					'size' => 12,
				],
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
				],
				'fill' => [
					'fillType' => Fill::FILL_SOLID,
					'startColor' => array('rgb' => '44546a')
				]
			]);
			$sheet->setCellValue('D73', 'VENTA TOTAL');
			$sheet->getStyle('D73')->applyFromArray([
				'font' => [
					'color' => array('rgb' => 'FFFFFF'),
					'bold' => true,
					'size' => 12,
				],
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
				],
				'fill' => [
					'fillType' => Fill::FILL_SOLID,
					'startColor' => array('rgb' => '44546a')
				]
			]);


			$sheet->setCellValue('C73', 'CREDITO POR COBRAR');




			$sheet->setCellValue('C75', 'CREDITO ENVASADO');
			$credit_total_mes_e = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
				->whereNotIn('sales.client_id', $client_ids)
				->whereIn('clients.business_unit_id', [1, 3])
				->where(DB::Raw('sales.sale_date'), '<=',  $initial_date)
				->where(DB::Raw('sales.sale_date'), '>=',  $first_date)
				->select('sales.balance')
				->sum('sales.balance');
			$sheet->setCellValue('D75', $credit_total_mes_e);

			$sheet->setCellValue('C76', 'CREDITO GRANEL');
			$credit_total_mes_g = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
				->whereNotIn('sales.client_id', $client_ids)
				->whereIn('clients.business_unit_id', [2])
				->where(DB::Raw('sales.sale_date'), '<=',  $initial_date)
				->where(DB::Raw('sales.sale_date'), '>=',  $first_date)
				->select('sales.balance')
				->sum('sales.balance');
			$sheet->setCellValue('D76', $credit_total_mes_g);


			$sheet->setCellValue('C74', 'CREDITO DEL MES');

			$total_mes_credito = $credit_total_mes_e + $credit_total_mes_g;

			$sheet->setCellValue('D74', $total_mes_credito);

			$sheet->setCellValue('C78', 'CREDITO ENVASADO');
			$credit_total_e = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
				->whereNotIn('sales.client_id', $client_ids)
				->whereIn('clients.business_unit_id', [1, 3])
				->where(DB::Raw('sales.sale_date'), '<',  $first_date)
				->where(DB::Raw('sales.sale_date'), '>=',  $anio_date)
				->select('sales.balance')
				->sum('sales.balance');
			$sheet->setCellValue('D78', $credit_total_e);

			$sheet->setCellValue('C79', 'CREDITO GRANEL');
			$credit_total_g = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
				->whereNotIn('sales.client_id', $client_ids)
				->whereIn('clients.business_unit_id', [2])
				->where(DB::Raw('sales.sale_date'), '<',  $first_date)
				->where(DB::Raw('sales.sale_date'), '>=',  $anio_date)
				->select('sales.balance')
				->sum('sales.balance');
			$sheet->setCellValue('D79', $credit_total_g);

			$sheet->setCellValue('C77', 'CREDITO DE MESES ANTERIORES');

			$total_anterior_credito = $credit_total_e + $credit_total_g;

			$sheet->setCellValue('D77', $total_anterior_credito);


			$sheet->setCellValue('C80', 'TOTAL CREDITO POR COBRAR');
			$total_credito_final = $total_anterior_credito + $total_mes_credito;


			$sheet->getColumnDimension('B')->setAutoSize(true);
			$sheet->getColumnDimension('C')->setAutoSize(true);
			$sheet->getColumnDimension('D')->setAutoSize(true);
			$sheet->getColumnDimension('E')->setAutoSize(true);
			$sheet->getColumnDimension('F')->setAutoSize(true);
			$sheet->getColumnDimension('G')->setAutoSize(true);
			$sheet->getColumnDimension('H')->setAutoSize(true);

			$writer = new Xls($spreadsheet);
			return $writer->save('php://output');
		} else {
			return response()->json($response);
		}
	}
}
