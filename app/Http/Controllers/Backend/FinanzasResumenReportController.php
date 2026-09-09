<?php

namespace App\Http\Controllers\Backend;

use App\Client;
use App\Company;
use App\Article;
use App\PaymentReport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SaleDetail;
use App\Sale;
use App\Efective;
use App\BusinessUnit;
use App\Deposit;
use App\Liquidation;
use App\WarehouseMovementDetail;
use App\WarehouseMovement;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use stdClass;


class FinanzasResumenReportController extends Controller
{
    public function index() {
		$companies = Company::select('id', 'name')->get();
		$current_date =CarbonImmutable::now()->toAtomString();
		return view('backend.finanzas_resumen_report')->with(compact('companies', 'current_date'));
	}

	public function validateForm() {
		$messages = [
			'initial_date.required'	=> 'Debe seleccionar una Fecha inicial.',
			
		];

		$rules = [
			'initial_date'	=> 'required',
		
		];

		request()->validate($rules, $messages);
		return request()->all();
	}

	public function getClients() {
		$company_id = request('company_id');
		$q = request('q');

		$clients = Client::select('id', 'business_name as text')
			->when($company_id, function($query, $company_id) {
				return $query->where('company_id', $company_id);
			})
			->where('business_name', 'like', '%'.$q.'%')
			->withTrashed()
			->get();

		return $clients;
	}

	public function list() {

		$export = request('export');

	    $initial_date = CarbonImmutable::createFromDate(request('model.initial_date'))->startOfDay()->format('Y-m-d');
		$first_date = CarbonImmutable::createFromDate(request('model.initial_date'))->startOfDay()->format('Y-m-01');
		$elements = PaymentReport::select('id as payment_id','payment_reports.name as payment_name' )
            ->groupBy('payment_name')
			->get();

		$response=[];
		$totals_total = 0;
		$totals_sum_total = 0;

		$warehouse_document_type_ids = [13,5,7,4];
		$client_ids = [1031, 427, 13326, 13775, 14072,14258];

		$total_venta_per = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
			->whereNotIn('sales.client_id', $client_ids)
			->where('sales.sale_date', '<=',  $initial_date)
			->where('sales.sale_date', '>=',  $first_date)
			->select('sales.total_perception')
			->sum('sales.total_perception');
		
		$total_tm = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
			->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
			->whereNotIn('sales.client_id', $client_ids)
			->where('sales.sale_date', '<=',  $initial_date)
			->where('sales.sale_date', '>=',  $first_date)
			->select('sales.kgs')
			->sum('sales.kgs');

		$total_venta= Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
			->whereNotIn('sales.client_id', $client_ids)
			->where('sales.sale_date', '<=',  $initial_date)
			->where('sales.sale_date', '>=',  $first_date)
			->select('sales.total')
			->sum('sales.total');


		$contado = Sale::leftjoin('liquidations', 'sales.sales_id', '=', 'liquidations.sale_id')
			->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
			->whereNotIn('sales.client_id', $client_ids)
			->whereIn('liquidations.payment_method_id',  [1,2] )
			->where('sales.sale_date', '<=',  $initial_date)
			->where('sales.sale_date', '>=',  $first_date)
			->select('liquidations.amount')
			->sum('liquidations.amount');

		$credito_historico = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
			->whereNotIn('sales.client_id', $client_ids)
			->where('sales.sale_date', '<=',  $initial_date)
			->where('sales.sale_date', '>=',  $first_date)
			->select('sales.pre_balance')
			->sum('sales.pre_balance');

		$descuento_personal = Sale::leftjoin('liquidations', 'sales.sales_id', '=', 'liquidations.sale_id')
			->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
			->whereNotIn('sales.client_id', $client_ids)
			->where('liquidations.payment_method_id', '=',  10)
			->where('sales.sale_date', '<=',  $initial_date)
			->where('sales.sale_date', '>=',  $first_date)
			->select('liquidations.amount')
			->sum('liquidations.amount');

		$bonificacion = Sale::leftjoin('liquidations', 'sales.sales_id', '=', 'liquidations.sale_id')
			->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
			->whereNotIn('sales.client_id', $client_ids)
			->where('liquidations.payment_method_id', '=',  7)
			->where('sales.sale_date', '<=',  $initial_date)
			->where('sales.sale_date', '>=',  $first_date)
			->select('liquidations.amount')
			->sum('liquidations.amount');

		
		$sustento = $contado+$credito_historico+$descuento_personal+$bonificacion;

		$dif=$total_venta_per-$sustento;


		$total_credito=$credito_historico+$descuento_personal;

		$cobranza_mes =Liquidation::leftjoin('sales','liquidations.sale_id','=','sales.id')
			->leftjoin('clients', 'sales.client_id', '=', 'clients.id')				
			->where(DB::Raw('DATE_FORMAT(liquidations.created_at, "%Y-%m-%d") '), '<=', $initial_date)
			->where(DB::Raw('DATE_FORMAT(liquidations.created_at, "%Y-%m-%d") '), '>=', $first_date)
			->whereNotIn('sales.client_id', $client_ids)
			->where('sales.sale_date', '<=',  $initial_date)
			->where('sales.sale_date', '>=',  $first_date)	 
			->whereIn('liquidations.payment_method_id',[1,2])
			->Where('liquidations.collection',[1])
			->select('liquidations.amount')
			->sum('liquidations.amount');

		$cobranza_des_mes =Liquidation::leftjoin('sales','liquidations.sale_id','=','sales.id')
			->leftjoin('clients', 'sales.client_id', '=', 'clients.id')				
			->where(DB::Raw('DATE_FORMAT(liquidations.created_at, "%Y-%m-%d") '), '<=', $initial_date)
			->where(DB::Raw('DATE_FORMAT(liquidations.created_at, "%Y-%m-%d") '), '>=', $first_date)
			->whereNotIn('sales.client_id', $client_ids)
			->where('sales.sale_date', '<=',  $initial_date)
			->where('sales.sale_date', '>=',  $first_date)	 
			->whereIn('liquidations.payment_method_id',[1,2])
			->Where('liquidations.collection',[2])
			->select('liquidations.amount')
			->sum('liquidations.amount');


		$total_cob_mes=$cobranza_mes+$cobranza_des_mes;

		$saldo_mes = $total_credito-$total_cob_mes;

		$past_credit = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
			->whereNotIn('sales.client_id', $client_ids)
			->where('sales.sale_date', '<',  $first_date)	 
			->select('sales.pre_balance')
			->sum('sales.pre_balance');  


	$total_balance =$saldo_mes+$past_credit;










		$dinero = Efective::where('bank_date', '=',  $initial_date)
			->select('efectives.total')
			->sum('efectives.total');	

		$deposito = Deposit::where('bank_date', '=',  $initial_date)
			->select('deposits.total')
			->sum('deposits.total');		

		
		$deposit = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
			->whereNotIn('sales.client_id', $client_ids)
			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=', $initial_date)
			->select('sales.deposit')
			->sum('sales.deposit');

		

          



        $total_credit_pend = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
			->whereNotIn('sales.client_id', $client_ids)
			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=', $initial_date)
			->where('sales.balance','>',0)
			->select('sales.balance')
			->sum('sales.balance');    

		$total_liquidado = $efective + $deposit + $credit;


		number_format($total_liquidado,2,'.','');

		$diference = $total_venta_del_dia - $total_liquidado;//DINERO DE VENTAS - LIQUIDACION

		number_format($diference,2,'.','');

		$cobranza_efective =Liquidation::leftjoin('sales','liquidations.sale_id','=','sales.id')
			->leftjoin('clients', 'sales.client_id', '=', 'clients.id')				
			->where(DB::Raw('DATE_FORMAT(liquidations.created_at, "%Y-%m-%d") '), '=', $initial_date)
			->whereNotIn('sales.client_id', $client_ids)	 
			->where('liquidations.payment_method_id',[1])
			->Where('liquidations.collection',[1])
			->select('liquidations.amount')
			->sum('liquidations.amount');

		$cobranza_deposit =Liquidation::leftjoin('sales','liquidations.sale_id','=','sales.id')
			->leftjoin('clients', 'sales.client_id', '=', 'clients.id')	
			->where(DB::Raw('DATE_FORMAT(liquidations.created_at, "%Y-%m-%d") '), '=', $initial_date)
			->whereNotIn('sales.client_id', $client_ids)	 
			->where('liquidations.payment_method_id',[2])
			->Where('liquidations.collection',[1])
			->select('liquidations.amount')				
			->sum('liquidations.amount');

		$total_cobranza = $cobranza_efective + $cobranza_deposit;

		$cesion_uso_efective = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->leftjoin('liquidations', 'sales.id', '=', 'liquidations.sale_id')
			->whereNotIn('sales.client_id', $client_ids)
			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=', $initial_date)
			->where('liquidations.payment_method_id',[1])	
			->where('sales.warehouse_document_type_id',[14])			
			->select('liquidations.amount')
			->sum('liquidations.amount');

		$cesion_uso_deposit = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->leftjoin('liquidations', 'sales.id', '=', 'liquidations.sale_id')
			->whereNotIn('sales.client_id', $client_ids)
			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=', $initial_date)
			->where('liquidations.payment_method_id',[2])	
			->where('sales.warehouse_document_type_id',[14])			
			->select('liquidations.amount')
			->sum('liquidations.amount');

		$otros_efective = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->whereNotIn('sales.client_id', $client_ids)
			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=', $initial_date)
			->where('sales.warehouse_document_type_id',[22])			
			->where('sales.payment_method_efective',1)			
			->select('sales.total_perception')
			->sum('sales.total_perception');

		 $otros_deposit = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->whereNotIn('sales.client_id', $client_ids)
			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=', $initial_date)
			->where('sales.warehouse_document_type_id',[22])
			->where('sales.payment_method_deposit',1)				
			->select('sales.total_perception')
			->sum('sales.total_perception');

	     $pendiente = ($dinero-$cobranza_efective)-($efective);

		 $total_otros_ingresos = $cesion_uso_efective + $cesion_uso_deposit + $otros_efective + $otros_deposit;

		 $total_recaudado_efectivo = $dinero + $cesion_uso_efective + $otros_efective;
		 $total_recaudado_deposito = $deposito + $cesion_uso_deposit+ $otros_deposit;
		

		 $egresos_caja = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->whereNotIn('sales.client_id', $client_ids)
			->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=', $initial_date)	
			->whereIn('sales.warehouse_document_type_id',[23,32])			
			->select('sales.total_perception')
			->sum('sales.total_perception');

		 $total_efective_day = $total_recaudado_efectivo - $egresos_caja;
         $total_recaudado = $total_efective_day  + $total_recaudado_deposito;

		 $remesa_hermes = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
		 ->whereNotIn('sales.client_id', $client_ids)
		 ->where(DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") '), '=', $initial_date)	
		 ->where('sales.warehouse_document_type_id',[31])			
		 ->select('sales.total_perception')
		 ->sum('sales.total_perception');

		 $cuadre = $total_efective_day - $remesa_hermes;




		$gra_gen1k= WarehouseMovementDetail::leftjoin('warehouse_movements','warehouse_movement_details.warehouse_movement_id','=','warehouse_movements.id')
		->leftjoin('articles', 'warehouse_movement_details.article_code', '=', 'articles.id')
		->where(DB::Raw('DATE_FORMAT(warehouse_movements.created_at, "%Y-%m-%d") '), '=', $initial_date)
		->whereIn('warehouse_movements.movement_type_id',[11,12])
		->whereIn('articles.id',[23])
		->sum('warehouse_movement_details.converted_amount');

		

		$gra_gln= WarehouseMovementDetail::leftjoin('warehouse_movements','warehouse_movement_details.warehouse_movement_id','=','warehouse_movements.id')
		->leftjoin('articles', 'warehouse_movement_details.article_code', '=', 'articles.id')
		->where(DB::Raw('DATE_FORMAT(warehouse_movements.created_at, "%Y-%m-%d") '), '=', $initial_date)
		->whereIn('warehouse_movements.movement_type_id',[11,12])
		->whereIn('articles.id',[24])
		->sum('warehouse_movement_details.converted_amount');
 
		$kggra_gln=($gra_gln)*2.017;



		$env_gen5k= WarehouseMovementDetail::leftjoin('warehouse_movements','warehouse_movement_details.warehouse_movement_id','=','warehouse_movements.id')
		->leftjoin('articles', 'warehouse_movement_details.article_code', '=', 'articles.id')
		->where(DB::Raw('DATE_FORMAT(warehouse_movements.created_at, "%Y-%m-%d") '), '=', $initial_date)
		->whereIn('warehouse_movements.movement_type_id',[11,12])
		->whereIn('articles.subgroup_id',[55])
		->whereIn('articles.convertion',[5])
		->sum('warehouse_movement_details.converted_amount');

		$kggen5k=($env_gen5k)*5;

		$env_gen10k= WarehouseMovementDetail::leftjoin('warehouse_movements','warehouse_movement_details.warehouse_movement_id','=','warehouse_movements.id')
		->leftjoin('articles', 'warehouse_movement_details.article_code', '=', 'articles.id')
		->where(DB::Raw('DATE_FORMAT(warehouse_movements.created_at, "%Y-%m-%d") '), '=', $initial_date)
		->whereIn('warehouse_movements.movement_type_id',[11,12])
		->whereIn('articles.subgroup_id',[56])
		->whereIn('articles.convertion',[10])
		->sum('warehouse_movement_details.converted_amount');

		$kggen10k=($env_gen10k)*10;

		$env_gen15k= WarehouseMovementDetail::leftjoin('warehouse_movements','warehouse_movement_details.warehouse_movement_id','=','warehouse_movements.id')
		->leftjoin('articles', 'warehouse_movement_details.article_code', '=', 'articles.id')
		->where(DB::Raw('DATE_FORMAT(warehouse_movements.created_at, "%Y-%m-%d") '), '=', $initial_date)
		->whereIn('warehouse_movements.movement_type_id',[11,12])
		->whereIn('articles.subgroup_id',[57])
		->whereIn('articles.convertion',[15])
		->sum('warehouse_movement_details.converted_amount');

		$kggen15k=($env_gen15k)*15;

		$env_gen45k= WarehouseMovementDetail::leftjoin('warehouse_movements','warehouse_movement_details.warehouse_movement_id','=','warehouse_movements.id')
		->leftjoin('articles', 'warehouse_movement_details.article_code', '=', 'articles.id')
		->where(DB::Raw('DATE_FORMAT(warehouse_movements.created_at, "%Y-%m-%d") '), '=', $initial_date)
		->whereIn('warehouse_movements.movement_type_id',[11,12])
		->whereIn('articles.subgroup_id',[58])
		->whereIn('articles.convertion',[45])
		->sum('warehouse_movement_details.converted_amount');

		$kggen45k=($env_gen45k)*45;



		$env_tran5k= WarehouseMovementDetail::leftjoin('warehouse_movements','warehouse_movement_details.warehouse_movement_id','=','warehouse_movements.id')
		->leftjoin('articles', 'warehouse_movement_details.article_code', '=', 'articles.id')
		->where(DB::Raw('DATE_FORMAT(warehouse_movements.created_at, "%Y-%m-%d") '), '=', $initial_date)
		->where('warehouse_movements.state', '=', 0)
		->whereIn('warehouse_movements.movement_type_id',[11,12])
		->whereIn('articles.subgroup_id',[55])
		->whereIn('articles.convertion',[5])
		->sum('warehouse_movement_details.converted_amount');


		$kgtran5k=($env_tran5k)*5;


		$env_tran10k= WarehouseMovementDetail::leftjoin('warehouse_movements','warehouse_movement_details.warehouse_movement_id','=','warehouse_movements.id')
		->leftjoin('articles', 'warehouse_movement_details.article_code', '=', 'articles.id')
		->where(DB::Raw('DATE_FORMAT(warehouse_movements.created_at, "%Y-%m-%d") '), '=', $initial_date)
		->where('warehouse_movements.state', '=', 0)
		->whereIn('warehouse_movements.movement_type_id',[11,12])
		->whereIn('articles.subgroup_id',[56])
		->whereIn('articles.convertion',[10])
		->sum('warehouse_movement_details.converted_amount');

		$kgtran10k=($env_tran10k)*10;


		$env_tran15k= WarehouseMovementDetail::leftjoin('warehouse_movements','warehouse_movement_details.warehouse_movement_id','=','warehouse_movements.id')
		->leftjoin('articles', 'warehouse_movement_details.article_code', '=', 'articles.id')
		->where(DB::Raw('DATE_FORMAT(warehouse_movements.created_at, "%Y-%m-%d") '), '=', $initial_date)
		->where('warehouse_movements.state', '=', 0)
		->whereIn('warehouse_movements.movement_type_id',[11,12])
		->whereIn('articles.subgroup_id',[57])
		->whereIn('articles.convertion',[15])
		->sum('warehouse_movement_details.converted_amount');

		$kgtran15k=($env_tran15k)*15;

		$env_tran45k= WarehouseMovementDetail::leftjoin('warehouse_movements','warehouse_movement_details.warehouse_movement_id','=','warehouse_movements.id')
		->leftjoin('articles', 'warehouse_movement_details.article_code', '=', 'articles.id')
		->where(DB::Raw('DATE_FORMAT(warehouse_movements.created_at, "%Y-%m-%d") '), '=', $initial_date)
		->where('warehouse_movements.state', '=', 0)
		->whereIn('warehouse_movements.movement_type_id',[11,12])
		->whereIn('articles.subgroup_id',[58])
		->whereIn('articles.convertion',[45])
		->sum('warehouse_movement_details.converted_amount');

		$kgtran45k=($env_tran45k)*45;



		$gra_tran1k= WarehouseMovementDetail::leftjoin('warehouse_movements','warehouse_movement_details.warehouse_movement_id','=','warehouse_movements.id')
		->leftjoin('articles', 'warehouse_movement_details.article_code', '=', 'articles.id')
		->where(DB::Raw('DATE_FORMAT(warehouse_movements.created_at, "%Y-%m-%d") '), '=', $initial_date)
		->where('warehouse_movements.state', '=', 0)
		->whereIn('warehouse_movements.movement_type_id',[11,12])
		->whereIn('articles.id',[23])
		->sum('warehouse_movement_details.converted_amount');


		$gra_tran_gln= WarehouseMovementDetail::leftjoin('warehouse_movements','warehouse_movement_details.warehouse_movement_id','=','warehouse_movements.id')
		->leftjoin('articles', 'warehouse_movement_details.article_code', '=', 'articles.id')
		->where(DB::Raw('DATE_FORMAT(warehouse_movements.created_at, "%Y-%m-%d") '), '=', $initial_date)
		->where('warehouse_movements.state', '=', 0)
		->whereIn('warehouse_movements.movement_type_id',[11,12])
		->whereIn('articles.id',[24])
		->sum('warehouse_movement_details.converted_amount');

		$tran_gra_gln=($gra_tran_gln)*2.017;






		$env_liq5k= WarehouseMovementDetail::leftjoin('warehouse_movements','warehouse_movement_details.warehouse_movement_id','=','warehouse_movements.id')
		->leftjoin('articles', 'warehouse_movement_details.article_code', '=', 'articles.id')
		->where(DB::Raw('DATE_FORMAT(warehouse_movements.created_at, "%Y-%m-%d") '), '=', $initial_date)
		->where('warehouse_movements.state', '=', 1)
		->whereIn('warehouse_movements.movement_type_id',[11,12])
		->whereIn('articles.subgroup_id',[55])
		->whereIn('articles.convertion',[5])
		->sum('warehouse_movement_details.converted_amount');


		$kgliq5k=($env_liq5k)*5;


		$env_liq10k= WarehouseMovementDetail::leftjoin('warehouse_movements','warehouse_movement_details.warehouse_movement_id','=','warehouse_movements.id')
		->leftjoin('articles', 'warehouse_movement_details.article_code', '=', 'articles.id')
		->where(DB::Raw('DATE_FORMAT(warehouse_movements.created_at, "%Y-%m-%d") '), '=', $initial_date)
		->where('warehouse_movements.state', '=', 1)
		->whereIn('warehouse_movements.movement_type_id',[11,12])
		->whereIn('articles.subgroup_id',[56])
		->whereIn('articles.convertion',[10])
		->sum('warehouse_movement_details.converted_amount');

		$kgliq10k=($env_liq10k)*10;


		$env_liq15k= WarehouseMovementDetail::leftjoin('warehouse_movements','warehouse_movement_details.warehouse_movement_id','=','warehouse_movements.id')
		->leftjoin('articles', 'warehouse_movement_details.article_code', '=', 'articles.id')
		->where(DB::Raw('DATE_FORMAT(warehouse_movements.created_at, "%Y-%m-%d") '), '=', $initial_date)
		->where('warehouse_movements.state', '=', 1)
		->whereIn('warehouse_movements.movement_type_id',[11,12])
		->whereIn('articles.subgroup_id',[57])
		->whereIn('articles.convertion',[15])
		->sum('warehouse_movement_details.converted_amount');

		$kgliq15k=($env_liq15k)*15;

		$env_liq45k= WarehouseMovementDetail::leftjoin('warehouse_movements','warehouse_movement_details.warehouse_movement_id','=','warehouse_movements.id')
		->leftjoin('articles', 'warehouse_movement_details.article_code', '=', 'articles.id')
		->where(DB::Raw('DATE_FORMAT(warehouse_movements.created_at, "%Y-%m-%d") '), '=', $initial_date)
		->where('warehouse_movements.state', '=', 1)
		->whereIn('warehouse_movements.movement_type_id',[11,12])
		->whereIn('articles.subgroup_id',[58])
		->whereIn('articles.convertion',[45])
		->sum('warehouse_movement_details.converted_amount');

		$kgliq45k=($env_liq45k)*45;



		$gra_liq1k= WarehouseMovementDetail::leftjoin('warehouse_movements','warehouse_movement_details.warehouse_movement_id','=','warehouse_movements.id')
		->leftjoin('articles', 'warehouse_movement_details.article_code', '=', 'articles.id')
		->where(DB::Raw('DATE_FORMAT(warehouse_movements.created_at, "%Y-%m-%d") '), '=', $initial_date)
		->where('warehouse_movements.state', '=', 1)
		->whereIn('warehouse_movements.movement_type_id',[11,12])
		->whereIn('articles.id',[23])
		->sum('warehouse_movement_details.converted_amount');


		$gra_liq_gln= WarehouseMovementDetail::leftjoin('warehouse_movements','warehouse_movement_details.warehouse_movement_id','=','warehouse_movements.id')
		->leftjoin('articles', 'warehouse_movement_details.article_code', '=', 'articles.id')
		->where(DB::Raw('DATE_FORMAT(warehouse_movements.created_at, "%Y-%m-%d") '), '=', $initial_date)
		->where('warehouse_movements.state', '=', 1)
		->whereIn('warehouse_movements.movement_type_id',[11,12])
		->whereIn('articles.id',[24])
		->sum('warehouse_movement_details.converted_amount');

		$liq_gra_gln=($gra_liq_gln)*2.017;




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
			$sheet->setCellValue('A1', 'REPORTE DE LIQUIDACIÓN DEL DÍA '.$initial_date.' DESCARGADO EL  '.CarbonImmutable::now()->format('d/m/Y H:m:s'));
			$sheet->getStyle('A1')->applyFromArray([
				'font' => [
					'bold' => true,
					'size' => 16,
				],
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
				]
			]);
		
			/*VENTAS*/
            $sheet->setCellValue('B5', 'TONELADAS');
            $sheet->setCellValue('B6', 'VENTAS SOLES');
            $sheet->setCellValue('B7', 'VENTA CON PERCEPCION ');
            $sheet->setCellValue('B8', 'CONTADO ');
			$sheet->setCellValue('B9', 'CRÉDITO ');
			$sheet->setCellValue('B10', 'CRÉDITO (RESPONSABILIDAD SUPERVISOR)');
			$sheet->setCellValue('B11', 'BONIFICACIONES');
			$sheet->setCellValue('B12', 'SUSTENTO DE LIQUIDACIÓN');
			$sheet->setCellValue('B13', 'DIFERENCIA');



			/*CREDITOS Y COBRANZAS*/

			$sheet->setCellValue('B16', 'TOTAL CRÉDITOS MES');
            $sheet->setCellValue('B17', 'COBRANZAS  DENTRO DEL MES CRÉD');
            $sheet->setCellValue('B18', 'COBRANZAS  (R.S) DENTRO DEL MES ');
            $sheet->setCellValue('B19', 'TOTAL COBRANZAS MES');
			$sheet->setCellValue('B20', 'SALDO CRÉDITO MES ');
			$sheet->setCellValue('B21', 'SALDO CRÉDITO MESES ANTERIORES');
			$sheet->setCellValue('B22', 'TOTAL CRÉDITOS');
			$sheet->setCellValue('B23', 'COBRANZA DE OTROS MESES ');
			$sheet->setCellValue('B24', 'COBRANZA  DE OTROS MESES R.S.');
			$sheet->setCellValue('B25', 'TOTAL COBRANZAS ANTERIORES');
			$sheet->setCellValue('B26', 'SALDO PENDIENTE MES');



			
			/*CAJA EFECTIVO*/

			$sheet->setCellValue('B29', 'CONTADO DETALLE');
            $sheet->setCellValue('B30', 'BANCO');
            $sheet->setCellValue('B31', 'CAJA - EFECTIVO');
            $sheet->setCellValue('B32', 'TOTAL VENTA CONTADO DETALLE');


			/*COBRANZAS DEL MES*/

			$sheet->setCellValue('B34', 'COBRANZAS  DENTRO DEL MES CRÉD');
            $sheet->setCellValue('B35', 'BANCO');
            $sheet->setCellValue('B36', 'CAJA - EFECTIVO');
			$sheet->setCellValue('B37', 'DSCTO. RRHH');
            $sheet->setCellValue('B38', 'TOTAL COBRANZ DENTRO MES');


			/*COBRANZAS DEL MES SUPERVISORES COMERCIALES*/


			$sheet->setCellValue('B40', 'COBRANZAS  (R.S.) DENTRO DEL MES');
            $sheet->setCellValue('B41', 'BANCO');
            $sheet->setCellValue('B42', 'CAJA - EFECTIVO');
			$sheet->setCellValue('B43', 'RRHH - EBITA');
            $sheet->setCellValue('B44', 'TOTAL COBRANZ (R.S.) DENTRO MES');


			/*RESUMEN 1*/
			$sheet->setCellValue('B46', 'BANCO MES');
            $sheet->setCellValue('B47', 'CAJA - EFECTIVO MES');
            $sheet->setCellValue('B48', 'TOTAL MES');


			/*COBRANZAS DE MESES ANTERIORES*/

			$sheet->setCellValue('B50', 'COBRANZAS DE CRÉDITO ANTERIORES');
            $sheet->setCellValue('B51', 'BANCO');
            $sheet->setCellValue('B52', 'CAJA - EFECTIVO');
			$sheet->setCellValue('B53', 'EBITA');
			$sheet->setCellValue('B54', 'PAGO CON FACTURA');
            $sheet->setCellValue('B55', 'TOTAL COBRANZ (R.S.) DENTRO MES');


			/*COBRANZAS DE MESES ANTERIORES SUPERVISORES COMERCIALES*/
			
		    $sheet->setCellValue('B57', 'COBRANZAS  (R.S.) MESES ANTERIORES');
            $sheet->setCellValue('B58', 'BANCO');
            $sheet->setCellValue('B59', 'CAJA - EFECTIVO');
			$sheet->setCellValue('B60', 'EBITA');
			$sheet->setCellValue('B61', 'PAGO CON FACTURA');
            $sheet->setCellValue('B62', 'TOTAL COBRANZ (R.S.) DE OTROS MESES');

			/*RESUMEN 2*/

			$sheet->setCellValue('B64', 'BANCO');
            $sheet->setCellValue('B65', 'CAJA - EFECTIVO');
            $sheet->setCellValue('B66', 'TOTAL RESPONSABILIDAD SUPERVISOR');

			/*RESUMEN 3*/

			$sheet->setCellValue('B68', 'BANCO');
            $sheet->setCellValue('B69', 'CAJA - EFECTIVO');
            $sheet->setCellValue('B70', 'TOTAL FINAL');




            $gra_gen=$gra_gen1k+$kggra_gln;
            $env_gen=$kggen5k+$kggen10k+$kggen15k+$kggen45k;

            $sheet->setCellValue('D6', $gra_gen );
            $sheet->setCellValue('D7', $env_gen );

            $total_gen=$env_gen+$gra_gen;

            $sheet->setCellValue('D8', $total_gen);

            $sheet->setCellValue('E5', 'TRANSITO');
            $gra_tran=$gra_tran1k+$tran_gra_gln;
            $env_tran=$kgtran5k+$kgtran10k+$kgtran15k+$kgtran45k;
            $sheet->setCellValue('E6', $gra_tran );
            $sheet->setCellValue('E7', $env_tran );

            $total_tran=$gra_tran+$env_tran; 


            $sheet->setCellValue('E8', $total_tran);

            $sheet->setCellValue('F5', 'REPORTADO');
            $gra_rep=0;
            $env_rep=0;
            $sheet->setCellValue('F6', $gra_rep);
            $sheet->setCellValue('F7', $env_rep );
            $total_rep=$gra_rep+$env_rep; 
            $sheet->setCellValue('F8', $total_rep);

            $sheet->setCellValue('G5', 'LIQUIDADO');
            $gra_liq=$gra_liq1k+$liq_gra_gln;
            $env_liq=$kgliq5k+$kgliq10k+$kgliq15k+$kgliq45k;
            $sheet->setCellValue('G6', $gra_liq );
            $sheet->setCellValue('G7', $env_liq );
            $total_liq=$gra_liq+$env_liq; 
            $sheet->setCellValue('G8', $total_liq);



			$cuadre_gra=$gra_gen-$gra_tran-$gra_liq;
			$cuadre_env=$env_gen-$env_tran-$env_liq;
			
			

            $sheet->setCellValue('H5', 'CUADRE');
			$sheet->setCellValue('H6', $cuadre_gra );
            $sheet->setCellValue('H7', $cuadre_env );
			$total_cuadre=$cuadre_gra+$cuadre_env; 
            $sheet->setCellValue('H8', $total_cuadre);



            $sheet->setCellValue('B12', 'TOTAL VENTAS');
            $sheet->setCellValue('C11', 'VENTA TOTAL');
            $sheet->setCellValue('D11', 'EFECTIVO LIQUIDADO');
            $sheet->setCellValue('E11', 'DEPOSITOS LIQUIDADOS');
            $sheet->setCellValue('F11', 'CREDITOS LIQUIDADOS');

            $sheet->setCellValue('C12', $total_venta_del_dia );
            $sheet->setCellValue('D12', $efective );
            $sheet->setCellValue('E12', $deposit );
            $sheet->setCellValue('F12', $credit);

            
            $sheet->setCellValue('B15', 'TOTAL COBRANZAS');
            $sheet->setCellValue('C14', 'COBRANZA TOTAL');
            $sheet->setCellValue('D14', 'COBRANZA EN EFECTIVO');
            $sheet->setCellValue('E14', 'COBRANZA EN DEPOSITO');


            $sheet->setCellValue('C15', $total_cobranza );
            $sheet->setCellValue('D15', $cobranza_efective );
            $sheet->setCellValue('E15', $cobranza_deposit );



            $sheet->setCellValue('B21', 'CAJA VTAS & COB.');
            $sheet->setCellValue('C20', 'SUB TOTAL CAJA');
            $sheet->setCellValue('D20', 'EFECTIVO VTAS. & COB.');
            $sheet->setCellValue('E20', 'DEPOSITO VTAS. & COB.');

            $cajaventas=$dinero +$deposito;

            $sheet->setCellValue('C21', $cajaventas);
            $sheet->setCellValue('D21', $dinero );
            $sheet->setCellValue('E21', $deposito );


            $sheet->setCellValue('B25', 'OTROS INGRESOS CAJA');
            $sheet->setCellValue('C24', 'OTROS INGRESOS');
            $sheet->setCellValue('D24', 'EFECTIVO OTROS INGRESOS');
            $sheet->setCellValue('E24', 'DEPOSITO OTROS INGRESOS');
    

            $sheet->setCellValue('C25', $total_otros_ingresos );
            $sheet->setCellValue('D25', $otros_efective );
            $sheet->setCellValue('E25', $otros_deposit );


            $sheet->setCellValue('B28', 'EGRESOS DE CAJA');
            $sheet->setCellValue('C27', 'TOTAL EGRESOS');
            $sheet->setCellValue('D27', 'EGRESOS EN EFECTIVO');
            $sheet->setCellValue('E27', 'EGRESOS EN DEPOSITO');
    

            $sheet->setCellValue('C28', $egresos_caja );
            $sheet->setCellValue('D28', $egresos_caja );
        //  $sheet->setCellValue('E27', $otros_deposit );


            $sheet->setCellValue('B32', 'FLUJO FINAL DEL DÍA');
            $sheet->setCellValue('C31', 'TOTAL FLUJO CAJA');
            $sheet->setCellValue('D31', 'TOTAL CAJA EFECTIVO');
            $sheet->setCellValue('E31', 'TOTAL CAJA DEPOSITOS');

            $sheet->setCellValue('C32', $total_recaudado );
            $sheet->setCellValue('D32', $total_efective_day );
            $sheet->setCellValue('E32', $total_recaudado_deposito);


            $sheet->setCellValue('B35', 'REMESA HERMES');
            
            $sheet->setCellValue('D34', 'REMESA EFECTIVO');
            $sheet->setCellValue('D35', $remesa_hermes );


            
            $sheet->setCellValue('B37', 'CUADRE EFECTIVO');
            $sheet->setCellValue('D37', $cuadre);


            $sheet->setCellValue('B40', 'CREDITO POR COBRAR');
            $sheet->setCellValue('C39', 'TOTAL CREDITO DEL CIERRE');
            $sheet->setCellValue('D39', 'TOTAL CREDITO CANCELADO');
            $sheet->setCellValue('E39', 'TOTAL CREDITO PENDIENTE');

            $sheet->setCellValue('C40', $total_credit);



			$total_credit_pay=$total_credit- $total_credit_pend;
			
			$sheet->setCellValue('D40', $total_credit_pay);

            $sheet->setCellValue('E40', $total_credit_pend);

			$sheet->getStyle('F3')->applyFromArray([
				'font' => [
					'bold' => true,
				],
			]);

			// $sheet->getStyle('D'.$row_number)->getNumberFormat()->setFormatCode('0.00');

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
	








