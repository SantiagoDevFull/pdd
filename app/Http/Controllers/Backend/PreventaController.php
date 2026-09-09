<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Article;
use App\BankAccount;
use App\Cicle;
use App\Client;
use App\ClientAddress;
use App\ClientRoute;
use App\Currency;
use App\Employee;
use App\Payment;
use App\PaymentMethod;
use App\PlantOrder;
use App\PlantSale;
use App\PlantLiquidation;
use App\PlantDeposit;
use App\PlantDiscount;
use App\PlantEfective;
use App\PlantSaleDetail;
use App\PreDiscount;
use App\PreLiquidation;
use App\PlantVoucher;
use App\PlantVoucherDetail;
use App\PreSale;
use App\PreSaleDetail;
use App\PriceList;
use App\Rate;
use App\Vehicle;
use App\VoucherType;
use App\WarehouseDocumentType;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PDF;

class PreventaController extends Controller
{


	public function start()
	{
		return view('backend.preventa_start');
	}
	public function index()
	{

		$payments = Payment::whereIn('id', [1, 2, 4])->get();
		$currencies = Currency::whereIn('id', [1])->get();

		$bank_accounts = BankAccount::leftjoin('banks', 'bank_accounts.bank_id', '=', 'banks.id')
			->select(
				'bank_accounts.id as id',
				DB::raw('CONCAT(banks.short_name," - ",bank_accounts.account_number) AS account_number')
			)
			->get();

		$client_routes = ClientRoute::select('id', 'name')
			->where('business_id', 1)
			->get();
		$warehouse_document_types = WarehouseDocumentType::select('id', 'name')->whereIn('id', [5, 13])->get();
		$payment_methods = PaymentMethod::select('id', 'name')->whereIn('id', [1, 2])->get();

		return view('backend.preventa')->with(compact('client_routes', 'warehouse_document_types', 'payments', 'currencies', 'bank_accounts', 'payment_methods'));
	}

	public function getDeposit()
	{

		$guia_id = request('guia_id');

		$guia = PlantOrder::find($guia_id);

		$plant_deposits = PlantDeposit::leftjoin('banks', 'plant_deposits.bank_id', '=', 'banks.id')
			->leftjoin('bank_accounts', 'plant_deposits.bank_account_id', '=', 'bank_accounts.id')
			->select(
				'plant_deposits.id as id',
				'plant_deposits.total_pend as total_pend',
				'plant_deposits.bank_date as bank_date',
				'plant_deposits.bank_account_id as bank_account_id',
				'plant_deposits.operation_number as operation_number',
				DB::Raw('CONCAT(bank_date," ",banks.short_name," ",bank_accounts.account_number," - ",plant_deposits.operation_number," - S/ ", plant_deposits.total_pend) AS texto')
			)
			->where('total_pend', '>', 0)
			->where('plant_deposits.route_id', $guia->route_id)
			->orderBy('plant_deposits.id', 'desc')
			->get();

		return $plant_deposits;
	}

	public function validateVoucherForm()
	{
		$messages = [
			'guia_id.required'			=> 'Debe seleccionar la guia.',
			'bank_date.required'	    => 'Debe seleccionar una Fecha del Deposito.',
			'bank_account_id.required'	=> 'Debe seleccionar una Cuenta Bancaria.',
			'operation_number.required'	=> 'Debe ingresar un Número de Operación.',
			'total.required'			=> 'Debe ingresar el Saldo Total.',
			'total.numeric'				=> 'Debe ingresar un Saldo Total Valido.',
			'total.min'					=> 'Debe ingresar un Saldo minimo 1.',
		];

		$rules = [
			'guia_id'	    	=> 'required',
			'bank_date'			=> 'required',
			'bank_account_id'	=> 'required',
			'operation_number'	=> 'required',
			'total'				=> 'required|numeric|min:1',
		];

		request()->validate($rules, $messages);
		return request()->all();
	}

	public function registerVoucher()
	{
		$this->validateVoucherForm();
		$company_id = 1;
		$currency_id = 1;
		$guia_id = request('guia_id');
		$bank_account_id = request('bank_account_id');
		$bank_date = CarbonImmutable::createFromDate(request('bank_date'))->format('Y-m-d');
		$operation_number = request('operation_number');
		$total = request('total');
		$obs = request('obs');

		$guia = PlantOrder::find($guia_id);

		$deposit = new PlantDeposit();
		$deposit->company_id = $company_id;
		$deposit->bank_date = $bank_date;
		$deposit->currency_id = $currency_id;
		$deposit->operation_number = $operation_number;
		$deposit->total = $total;
		$deposit->total_pend = $total;
		$deposit->paid = 0;

		$bank_account = BankAccount::find($bank_account_id);

		$deposit->bank_id = $bank_account->bank_id;
		$deposit->bank_account_id = $bank_account_id;
		$deposit->route_id = $guia->route_id;
		$deposit->obs = $obs;
		$deposit->plant_order_id = $guia_id;

		$deposit->save();

		$type = 1;
		$title = '¡Ok!';
		$msg = 'Deposito Registrado Exitosamente.';

		return response()->json([
			'type' => $type,
			'title' => $title,
			'msg' => $msg
		]);
	}

	public function getClientsCobranza()
	{
		$q = request('q');
		$guia = request('guia_id');

		$guia = PlantOrder::find($guia);

		$clients = Client::select('id', 'business_name')
			->where('route_id', $guia->route_id)
			->where(function ($query) use ($q) {
				$query->where('business_name', 'like', '%' . $q . '%')
					->orWhere('id', 'like', '%' . $q . '%');
			})
			->get()
			->map(function ($item) {
				return [
					'id' => $item->id,
					'text' => $item->id . ' - ' . $item->business_name,
				];
			});

		return response()->json($clients);
	}

	public function updateCobranza()
	{

		$cobranzas = request('cobranzas');
		$guia_id = request('guia_id');

		$client_id = request('model.client_cobranza_id');
		$payment_method_id = request('model.payment_method_id');
		$currency_id = request('model.currency_id');

		if ($payment_method_id == 2) {
			$deposit_id = request('model.deposit_id');
			$deposit = PlantDeposit::find($deposit_id);

			$bank_account_id = $deposit->bank_account_id;
			$operation_number = $deposit->operation_number;
			$date_emision = $deposit->bank_date;
		}

		$amount = request('model.amount');

		$client = Client::find($client_id);
		$liquidationIds = [];
		foreach ($cobranzas as $cobranza) {

			$sale = PlantSale::find($cobranza['sale_id']);
			$sale->paid = $sale->paid + $cobranza['monto'];
			$sale->balance = $sale->balance - $cobranza['monto'];
			$sale->save();

			$last_collection_number = PlantLiquidation::max('collection_number');
			$collection_number = $last_collection_number ? ++$last_collection_number : 1;

			$liquidation = new PlantLiquidation();
			$liquidation->plant_sale_id = $sale->id;
			$liquidation->collection_number = $collection_number;
			$liquidation->company_id = $sale->company_id;
			$liquidation->payment_method_id = $payment_method_id;
			$liquidation->currency_id = $currency_id;
			$liquidation->amount = $cobranza['monto'];
			$liquidation->collection = 1;

			if ($payment_method_id == 2) {
				$liquidation->bank_account_id = $bank_account_id;
				$liquidation->operation_number = $operation_number;
				$liquidation->created_at = $date_emision;

				$liquidation->plant_deposit_id = $deposit->id;

				$deposit->total_pend = $deposit->total_pend - $cobranza['monto'];
				$deposit->paid = $deposit->paid + $cobranza['monto'];
				$deposit->save();
			}

			$liquidation->plant_order_id = $guia_id;
			$liquidation->save();
			$liquidationIds[] = $liquidation->id;
		}
		$client->credit_balance = $amount;
		$client->save();

		return $liquidationIds;
	}

	public function listCobranzasPagadas()
	{

		$ids_liq = request('ids_liq');

		$plant_liquidations = PlantLiquidation::leftjoin('sales', 'plant_liquidations.plant_sale_id', '=', 'sales.id')
			->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->leftjoin('payment_methods', 'plant_liquidations.payment_method_id', '=', 'payment_methods.id')
			->leftjoin('bank_accounts', 'plant_liquidations.bank_account_id', '=', 'bank_accounts.id')
			->leftjoin('warehouse_document_types', 'sales.warehouse_document_type_id', '=', 'warehouse_document_types.id')
			->leftjoin('banks', 'bank_accounts.bank_id', '=', 'banks.id')
			->select(
				'clients.business_name as client_name',
				'warehouse_document_types.name as warehouse_document_type_name',
				'sales.referral_serie_number as referral_serie_number',
				'sales.referral_voucher_number as referral_voucher_number',
				'payment_methods.name as payment_method_name',
				'plant_liquidations.operation_number as operation_number',
				'sales.total_perception as total_perception',
				'sales.balance as balance',
				'sales.paid as paid',
				DB::raw('CONCAT(banks.short_name," - ",bank_accounts.account_number) AS bank_account_name'),
				DB::raw("DATE_FORMAT(plant_liquidations.created_at, '%Y-%m-%d') as liquidation_date"),
				DB::raw('ROUND(plant_liquidations.amount, 2) as liquidation_amount')
			)
			->whereIn('liquidations.id', $ids_liq)
			->get();

		return $plant_liquidations;
	}

	public function getSaleCobranza()
	{

		$client_id = request('client_id');

		$plant_sales = PlantSale::join('warehouse_document_types', 'plant_sales.warehouse_document_type_id', '=', 'warehouse_document_types.id')
			->where('plant_sales.balance', '>', 0)
			->where('plant_sales.client_id', $client_id)
			->select(
				'plant_sales.id as sale_id',
				'warehouse_document_types.name as warehouse_document_type_name',
				'referral_serie_number',
				'referral_voucher_number',
				'total_perception',
				'balance',
				'paid',
				'sale_date',
				'expiry_date'
			)
			->get();

		return $plant_sales;
	}

	public function getGuias()
	{

		$route_id = request('route_id');

		$elements = PlantOrder::leftjoin('vehicles', 'plant_orders.vehicle_id', '=', 'vehicles.id')
			->select(
				'plant_orders.id as id',
				'plant_orders.traslate_date as traslate_date',
				'vehicles.plate as plate',
				DB::Raw("CONCAT(plant_orders.serie , '-',plant_orders.correlative) as pedido")
			)
			->where('plant_orders.route_id', $route_id)

			->where(function ($q) use ($route_id) {
				if ($route_id == 20) {
					$q->where('plant_orders.state_id', 2);
				} else {
					$q->where('plant_orders.state_id', 5);
				}
			})

			->get();


		return $elements;
	}

	public function validateForm()
	{
		$messages = [
			'client_route_id.required'	=> 'Debe seleccionar la Ruta.',
			'pin.required'	=> 'Debe colocar su PIN.',
			'guia_id.required'	=> 'Debe seleccionar una Guía.',

		];

		$rules = [
			'client_route_id'	=> 'required',
			'pin'	=> 'required',
			'guia_id'	=> 'required',

		];

		request()->validate($rules, $messages);
		return request()->all();
	}

	public function getVerifyCredit()
	{
		$client_id = request('client_id');

		$client = Client::where('id', $client_id)->first();

		return $client;
	}

	public function getPaymentMethod()
	{

		$payment_id = request('payment_id');

		$elements = PaymentMethod::where('payment_id', $payment_id)->whereIn('id', [1, 2, 10])->get();

		return $elements;
	}

	public function getArticlePrice()
	{
		$article_id = request('article_id');
		$client_id = request('client_id');
		$guia_id = request('guia_id');


		$plant_order = PlantOrder::find($guia_id, ['id', 'traslate_date']);
		$traslate_date = date('Y-m-d', strtotime($plant_order->traslate_date));

		$element = PriceList::select('id', 'article_id', 'price_igv')
			->where('client_id', $client_id)
			->where('warehouse_type_id', 5)
			->where('article_id', $article_id)
			->where('initial_effective_date', '<=', $traslate_date)
			->where('final_effective_date', '>=', $traslate_date)
			->where('state', 1)
			->with(['article' => function ($query) {
				$query->select('id', 'igv', 'perception');
			}])
			->first();

		if (!$element) {
			return response()->json(['error' => 'Precio no encontrado'], 404);
		}

		return $element;
	}


	public function getClients()
	{
		$q = request('q');
		$guia = request('guia_id');

		$guia = PlantOrder::find($guia);
		$company_id = $guia->company_id;


		$elements = Client::select('id', 'code', 'business_name', 'document_type_id', 'payment_id', 'perception_percentage_id', 'credit_limit', 'route_id')
			->where(function ($query) use ($q) {
				$query->where('business_name', 'like', '%' . $q . '%')
					->orWhere('id', 'like', '%' . $q . '%');
			})
			//->where('company_id', $company_id)
			//->where('route_id', $guia->route_id)
			->orderBy('business_name', 'asc')
			->with(['perception_percentage' => function ($query) {
				$query->select('id', 'value');
			}])
			->get();

		$elements->map(function ($item, $index) {
			$item->text = $item->id . ' - ' . $item->business_name;
			unset($item->business_name);
			unset($item->code);

			return $item;
		});


		return $elements;
	}

	public function getEmployees()
	{
		$q = request('q');

		$employees = Employee::select('id', 'first_name', 'last_name')
			->where('first_name', 'like', '%' . $q . '%')
			->get();

		$employees->map(function ($item, $index) {
			$item->text = $item->first_name . ' ' . $item->last_name;
			unset($item->first_name);
			unset($item->last_name);
			return $item;
		});

		return $employees;
	}

	public function store()
	{
		$sales = request('sales');
		$guia_id = request('guia_id');
		$referral_factura = 120; //fACTURA
		$referral_nota_interna = CarbonImmutable::now()->format('Ym');

		$currency_id = 1;
		$total_efective = 0;

		$warehouse_movement = PlantOrder::find(
			$guia_id,
			['id', 'company_id', 'vehicle_id', 'movement_number', 'serie', 'correlative', 'traslate_date', 'created_at', 'route_id', 'state_id']
		);

		$company_id = $warehouse_movement->company_id;

		$rate = Rate::where('description', 'IGV')
			->where('state', 1)
			->select('id', 'value')
			->first();


		$igv_percentage = ($rate->value / 100) + 1;

		foreach ($sales as $sale) {

			$client = Client::find($sale['client_id'], ['id', 'code', 'business_name', 'link_client_id', 'payment_id', 'credit_limit_days', 'credit_limit', 'credit_balance', 'route_id']);
			$client_address = ClientAddress::where('client_id', $client->id)
				->where('address_type_id', 1)
				->select('id', 'address')
				->first();

			$client->credit_limit_days = $client->credit_limit_days ? $client->credit_limit_days : 0;
			$sale_date = date('Y-m-d', strtotime($warehouse_movement->traslate_date));
			$expiry_date = $sale_date;


			$credito = collect($sale['liquidations'])->contains(function ($liquidation) {
				return $liquidation['payment_id'] == 2;
			});

			$sale_model = new PlantSale();
			$sale_model->company_id = $company_id;
			$sale_model->sale_date = $sale_date;
			$sale_model->plant_order_id = $guia_id;
			$sale_model->client_id = $sale['client_id'];
			$sale_model->payment_id = $credito ? 2 : 1;

			if ($sale_model->payment_id == 2) {
				$expiry_date = CarbonImmutable::createFromFormat('Y-m-d', $sale_date)->addDays($client->credit_limit_days);
			}
			$sale_model->expiry_date = $expiry_date;

			$sale_model->currency_id = 1;
			$sale_model->guide_series = $warehouse_movement->serie;
			$sale_model->guide_number = $warehouse_movement->correlative;
			$sale_model->warehouse_document_type_id = $sale['warehouse_document_type_id'];

			if ($sale['warehouse_document_type_id'] == 5) //FACTURA ELECTRONICA 
			{

				$sale_model->referral_serie_number = $referral_factura;
				$last_voucher_number = PlantSale::where('company_id', $company_id)
					->where('warehouse_document_type_id', $sale['warehouse_document_type_id'])
					->where('referral_serie_number', $referral_factura)
					->max('referral_voucher_number');

				$sale_model->referral_voucher_number = $last_voucher_number ? $last_voucher_number + 1 : 1;

				// ************************************** VOUCHER

				$warehouse_document_type_id = WarehouseDocumentType::find($sale['warehouse_document_type_id'], ['id', 'voucher_type_id']);
				$voucher_type = VoucherType::find($warehouse_document_type_id->voucher_type_id, ['id', 'serie_type']);
				$serie_number = $voucher_type->serie_type . sprintf('%03d', $referral_factura);
				$last_voucher_number = PlantVoucher::where('company_id', $company_id)
					->where('voucher_type_id', $voucher_type->id)
					->where('serie_number', $serie_number)
					->max('voucher_number');

				$voucher = new PlantVoucher();
				$voucher->company_id = $company_id;
				$voucher->client_id = $client->id;
				$voucher->original_client_id = $client->id;
				$voucher->client_name = $client->business_name;
				$voucher->client_address = $client_address->address;
				$voucher->voucher_type_id = $voucher_type->id;
				$voucher->serie_number = $serie_number;
				$voucher->voucher_number = ++$last_voucher_number;
				$voucher->referral_guide_series = $warehouse_movement->serie;
				$voucher->referral_guide_number = $warehouse_movement->correlative;
				$voucher->issue_date = date('Y-m-d', strtotime($warehouse_movement->traslate_date));
				$voucher->issue_hour = date('H:i:s', strtotime($warehouse_movement->traslate_date));
				$voucher->expiry_date = $expiry_date;
				$voucher->currency_id = $currency_id;
				$voucher->payment_id = $sale_model->payment_id;
				$voucher->total = $sale['sale_value'];
				$voucher->igv_perception = $sale['perception'];
				$voucher->total_perception = $sale['total'];
				$voucher->igv_percentage = $rate->value;
				$voucher->igv_perception_percentage = $sale['perception_percentage'] / 100;

				if ($voucher_type->id >= 1 && $voucher_type->id <= 4) {
					$voucher->ose = 0;
				} else {
					$voucher->ose = 1;
				}


				$voucher->save();

				$taxed_operation = 0;
				$igv = 0;
				foreach ($sale['details'] as $detail) {
					$article = Article::find($detail['article_id'], ['id', 'name', 'sale_unit_id']);

					$voucher_detail = new PlantVoucherDetail();
					$voucher_detail->plant_voucher_id = $voucher->id;
					$voucher_detail->unit_id = $article->sale_unit_id;
					$voucher_detail->name = $article->name;
					$voucher_detail->quantity = $detail['article_quantity'];
					$voucher_detail->original_price = round($detail['article_price'], 4);
					$voucher_detail->unit_price = round($detail['article_price'] / $igv_percentage, 4);
					$voucher_detail->sale_value = round($detail['article_price'], 4);
					$voucher_detail->exonerated_value = 0;
					$voucher_detail->inaccurate_value = 0;
					$voucher_detail->igv = round($detail['article_sale_value'], 4) - round($detail['article_sale_value'] / $igv_percentage, 4);
					$voucher_detail->total = round($detail['article_sale_value'], 4);

					$voucher_detail->article_id = $article->id;
					$voucher_detail->save();

					if ($detail['article_igv'] == 1) {
						$taxed_operation += round($detail['article_sale_value'] / $igv_percentage, 4);
						$igv += round($detail['article_sale_value'], 4) - round($detail['article_sale_value'] / $igv_percentage, 4);
					} else {
						$taxed_operation += round($detail['article_sale_value'], 4);
					}
				}

				$voucher->taxed_operation = round($taxed_operation, 4);
				$voucher->unaffected_operation = 0;
				$voucher->exonerated_operation = 0;
				$voucher->igv = round($igv, 4);
				$voucher->plant_order_id = $guia_id;
				$voucher->save();

				$sale_model->referral_serie_number = $referral_factura;
				$sale_model->referral_voucher_number = $voucher->voucher_number;
			} else if ($sale['warehouse_document_type_id'] == 13) //NOTA INTERNA
			{

				$sale_model->referral_serie_number = $referral_nota_interna;
				$last_voucher_number = PlantSale::where('company_id', $company_id)
					->where('warehouse_document_type_id', $sale['warehouse_document_type_id'])
					->where('referral_serie_number', $referral_nota_interna)
					->max('referral_voucher_number');

				$sale_model->referral_voucher_number = $last_voucher_number ? $last_voucher_number + 1 : 1;
			}


			$vehicle = Vehicle::findOrFail($warehouse_movement->vehicle_id);
			$sale_model->license_plate = $vehicle->plate;

			$sale_value = 0;
			$igv = 0;



			foreach ($sale['details'] as $index => $detail) {
				if ($detail['article_igv'] == 1) {
					$sale_value += round($detail['article_sale_value'] / $igv_percentage, 4);
					$igv += round($detail['article_sale_value'], 4) - round($detail['article_sale_value'] / $igv_percentage, 4);
				} else {
					$sale_value += round($detail['article_sale_value'], 4);
				}
			}

			$sale_model->sale_value = $sale_value;
			$sale_model->igv = $igv;
			$sale_model->total = $sale['sale_value'];
			$sale_model->total_perception = $sale['total'];

			$balance = 0;
			$paid = 0;

			foreach ($sale['liquidations'] as $liquidation) {
				if ($liquidation['payment_id'] == 1 || $liquidation['payment_id'] == 4) {
					$paid = $paid + $liquidation['amount'];
				} else {
					$balance = $balance + $liquidation['amount'];
				}
			}

			$sale_model->balance = $balance;
			$sale_model->paid = $paid;
			$sale_model->pre_balance = $balance;

			$mesActual = date('m', strtotime($sale_date));
			$anioActual = date('Y', strtotime($sale_date));

			$cicle_id = Cicle::where('mes', $mesActual)
				->where('anio', $anioActual)
				->select('id')
				->sum('id');

			$sale_model->route_id = $client->route_id;
			$sale_model->guide_date = $warehouse_movement->created_at;
			$sale_model->mes = $mesActual;
			$sale_model->anio = $anioActual;
			$sale_model->cicle_id = $cicle_id;
			$sale_model->save();

			if ($sale['warehouse_document_type_id'] == 5) //FACTURA ELECTRONICA 
			{
				$voucher->plant_sale_id = $sale_model->id;
				$voucher->plant_order_id = $guia_id;
				$voucher->save();
			}

			foreach ($sale['details'] as $index => $detail) {

				$article = Article::find($detail['article_id'], ['convertion', 'name']);

				$sale_detail = new PlantSaleDetail();
				$sale_detail->plant_sale_id = $sale_model->id;
				$sale_detail->item_number = $index + 1;
				$sale_detail->article_id = $detail['article_id'];
				$sale_detail->concept = $article->name;
				$sale_detail->quantity = $detail['article_quantity'];
				$sale_detail->price_igv = $detail['article_price'];
				$sale_detail->sale_value = $detail['article_sale_value'];
				$sale_detail->igv = round($detail['article_sale_value'], 4) - round($detail['article_sale_value'] / $igv_percentage, 4);
				$sale_detail->total = $detail['article_sale_value'];
				$sale_detail->total_perception = $detail['article_total_perception'];
				$sale_detail->igv_percentage = $rate->value;
				$sale_detail->igv_perception_percentage = $sale['perception_percentage'];
				$sale_detail->referential_convertion = $article->convertion;
				$sale_detail->kg = $detail['article_quantity'] * $sale_detail->referential_convertion;
				$sale_detail->save();
			}

			foreach ($sale['liquidations'] as $liquidation) {

				if ($liquidation['payment_id'] != 2) {
					$sale_liquidation = new PlantLiquidation();
					$sale_liquidation->plant_sale_id = $sale_model->id;
					$sale_liquidation->company_id = $company_id;
					$sale_liquidation->payment_method_id = $liquidation['payment_method_id'];
					$sale_liquidation->currency_id = $liquidation['currency_id'];

					if ($liquidation['payment_method_id'] == 2) {

						$deposit = PlantDeposit::find($liquidation['deposit_id']);
						$sale_liquidation->bank_account_id = $deposit->bank_account_id;
						$sale_liquidation->operation_number = $deposit->operation_number;
						$sale_liquidation->created_at = $deposit->bank_date;

						$deposit->total_pend = $deposit->total_pend - $liquidation['amount'];
						$deposit->paid = $deposit->paid + $liquidation['amount'];
						$deposit->save();

						$sale_liquidation->plant_deposit_id = $deposit->id;
					} else if ($liquidation['payment_method_id'] == 1) {
						$total_efective = $total_efective + $liquidation['amount'];
					}

					$sale_liquidation->amount = $liquidation['amount'];
					$sale_liquidation->collection = 0;
					$sale_liquidation->save();
				}
				if ($liquidation['payment_id'] == 4) {
					$discount = new PlantDiscount();
					$discount->employee_id = $liquidation['employee_id'];
					$discount->plant_sale_id = $sale_model->id;
					$discount->sale_date = $sale_date;
					$discount->total = $liquidation['amount'];
					$discount->paid = 0;
					$discount->balance =  $liquidation['amount'];
					$discount->save();
				}
			}


			if ($sale['warehouse_document_type_id'] == 5) {
				if ($credito == 2) {
					if ($company_id == 1) {

						$voucher_type = VoucherType::find(14, ['id', 'serie_type', 'type']);

						$con_voucher_number = PlantVoucher::where('company_id', $company_id)
							->where('voucher_type_id', 14)
							->max('voucher_number');

						$voucher2 = new PlantVoucher();
						$voucher2->company_id = $company_id;
						$voucher2->client_id = $client->id;
						$voucher2->original_client_id = $client->id;
						$voucher2->client_name = $client->business_name;
						$voucher2->client_address = $client_address->address;
						$voucher2->voucher_type_id = 14; // Comprobante de Percepcion
						$voucher2->serie_number = $voucher_type->serie_type . sprintf('%03d', $voucher_type->type);
						$voucher2->voucher_number = $con_voucher_number ? ++$con_voucher_number : 1;
						$voucher2->credit_note_reference_serie = $serie_number;
						$voucher2->credit_note_reference_number = $last_voucher_number;
						$voucher2->issue_date = $sale_date;
						$voucher2->issue_hour = Carbon::now()->format('H:i:s');
						$voucher2->expiry_date = $expiry_date;
						$voucher2->currency_id = $currency_id;
						$voucher2->payment_id = 2;
						$voucher2->taxed_operation = $sale_value;
						$voucher2->unaffected_operation = 0;
						$voucher2->exonerated_operation = 0;
						$voucher2->igv = $igv;
						$voucher2->total = $sale['sale_value'];
						$voucher2->referral_guide_series = $warehouse_movement->serie;
						$voucher2->referral_guide_number = $warehouse_movement->correlative;

						$voucher2->igv_perception = $sale['perception_percentage'] / 100;
						$voucher2->total_perception = $sale['total'];
						$voucher2->igv_percentage = $igv_percentage;
						$voucher2->igv_perception_percentage = $sale['perception_percentage'];
						$voucher2->ose = 0;
						$voucher2->plant_order_id = $guia_id;

						$voucher2->save();
					}
				}
			}
		}

		$warehouse_movement->temporal = 0;
		$warehouse_movement->pre_liquidado = 1;
		$warehouse_movement->state_id = 6;
		$warehouse_movement->save();

		if ($total_efective > 0) {
			$efective = new PlantEfective();
			$efective->company_id = $company_id;
			$efective->bank_date =  Carbon::now()->format('Y-m-d');
			$efective->plant_date =  date('Y-m-d', strtotime($warehouse_movement->traslate_date));
			$efective->total = $total_efective;
			$efective->total_pend = $total_efective;
			$efective->plant_order_id = $guia_id;
			$efective->save();
		}

		$sales = PlantSale::leftJoin('plant_liquidations', 'plant_sales.id', '=', 'plant_liquidations.plant_sale_id')
			->leftJoin('clients', 'plant_sales.client_id', '=', 'clients.id')
			->leftJoin('document_types', 'clients.document_type_id', '=', 'document_types.id')
			->leftJoin('payments as payment_sale', 'plant_sales.payment_id', '=', 'payment_sale.id')
			->leftJoin('currencies', 'plant_sales.currency_id', '=', 'currencies.id')
			->leftJoin('warehouse_document_types', 'plant_sales.warehouse_document_type_id', '=', 'warehouse_document_types.id')
			->leftJoin('payment_methods', 'plant_liquidations.payment_method_id', '=', 'payment_methods.id')
			->leftJoin('bank_accounts', 'plant_liquidations.bank_account_id', '=', 'bank_accounts.id')
			->select(
				'plant_sales.client_id as client_id',
				DB::raw('CONCAT(document_types.name, " ", clients.document_number, " - ", clients.business_name) as client_full_name'),
				'payment_sale.name as payment_name',
				'warehouse_document_types.short_name as warehouse_document_type_short_name',
				DB::raw('CONCAT(plant_sales.guide_series, "-", plant_sales.guide_number) as guia_electronica'),
				DB::raw("
					CASE 
						WHEN warehouse_document_types.id = 5 
						THEN CONCAT('F', plant_sales.referral_serie_number, '-', plant_sales.referral_voucher_number)
						ELSE ''
					END AS comprobante
				"),
				'plant_sales.total_perception as total_perception',

				DB::raw("ROUND(SUM(CASE WHEN payment_methods.id = 1 THEN plant_liquidations.amount ELSE 0 END), 2) as total_efectivo"),
				DB::raw("ROUND(SUM(CASE WHEN payment_methods.id = 2 THEN plant_liquidations.amount ELSE 0 END), 2) as total_deposito"),
				DB::raw("ROUND(SUM(CASE WHEN payment_methods.id = 10 THEN plant_liquidations.amount ELSE 0 END), 2) as total_descuento"),
				DB::raw("ROUND(plant_sales.pre_balance, 2) as total_credito")
			)
			->where('plant_sales.plant_order_id', $warehouse_movement->id)
			->groupBy(
				'plant_sales.id',
				'document_types.name',
				'clients.id',
				'clients.document_number',
				'clients.business_name',
				'payment_sale.name',
				'warehouse_document_types.short_name',
				'plant_sales.referral_serie_number',
				'plant_sales.referral_voucher_number',
				'plant_sales.total_perception'
			)
			->get();



		$cobranzas = PlantLiquidation::leftJoin('plant_sales', 'plant_liquidations.plant_sale_id', '=', 'plant_sales.id')
			->leftJoin('clients', 'plant_sales.client_id', '=', 'clients.id')
			->leftJoin('warehouse_document_types', 'plant_sales.warehouse_document_type_id', '=', 'warehouse_document_types.id')
			->leftjoin('payment_methods', 'plant_liquidations.payment_method_id', '=', 'payment_methods.id')
			->leftjoin('bank_accounts', 'plant_liquidations.bank_account_id', '=', 'bank_accounts.id')
			->leftjoin('banks', 'bank_accounts.bank_id', '=', 'banks.id')
			->select(
				'clients.id as client_id',
				'warehouse_document_types.name as warehouse_document_type_name',
				'clients.business_name as client_name',
				'plant_sales.sale_date as sale_date',
				'plant_sales.total_perception as total_perception',
				'payment_methods.name as payment_method_name',
				'plant_liquidations.amount as liquidation_amount',
				'plant_liquidations.operation_number as operation_number',
				DB::raw('CONCAT(banks.short_name," - ",bank_accounts.account_number) AS bank_account_name')
			)
			->where('plant_liquidations.plant_order_id', $warehouse_movement->id)
			->get();

		$deposits = PlantDeposit::leftjoin('banks', 'plant_deposits.bank_id', '=', 'banks.id')
			->leftjoin('bank_accounts', 'plant_deposits.bank_account_id', '=', 'bank_accounts.id')
			->leftjoin('companies', 'plant_deposits.company_id', '=', 'companies.id')

			->select(
				'companies.name as company_name',
				'plant_deposits.bank_date as bank_date',
				DB::raw('CONCAT(banks.short_name," - ",bank_accounts.account_number) AS bank_account_name'),
				'plant_deposits.operation_number as operation_number',
				'plant_deposits.total as total',
				'plant_deposits.total_pend as total_pend',
				'plant_deposits.paid as paid'
			)
			->where('plant_deposits.plant_order_id', $warehouse_movement->id)
			->get();

		$totalVenta    = $sales->sum('total_perception');
		$totalEfectivo = $sales->sum('total_efectivo');
		$totalDeposito = $sales->sum('total_deposito');
		$totalDescuento = $sales->sum('total_descuento');
		$totalCredito  = $sales->sum('total_credito');

		$pdf = PDF::loadView('backend.pdf_preventa', compact(
			'warehouse_movement',
			'sales',
			'cobranzas',
			'deposits',
			'totalVenta',
			'totalEfectivo',
			'totalDeposito',
			'totalDescuento',
			'totalCredito'
		))->setPaper('a4', 'landscape');

		$ruta = 'uploads/ventas/' . $warehouse_movement->movement_number . '.pdf';
		Storage::disk('public')->put($ruta, $pdf->output());

		$type = 3;
		$title = '¡Ok!';
		$msg = '¡La venta se ha registrado exitosamente!';
		$url = route('dashboard.preventa.start');

		return response()->json([
			'type'     => $type,
			'title'    => $title,
			'msg'      => $msg,
			'url'      => $url,
			'pdfUrl'   => asset('uploads/ventas/' . $warehouse_movement->movement_number . '.pdf')
		]);
	}

	public function store_pre()
	{
		$sales = request('sales');
		$guia_id = request('guia_id');

		$ventas = PreSale::where('plant_order_id', $guia_id)->get();

		foreach ($ventas as $v) {
			PreSaleDetail::where('pre_sale_id', $v->id)->delete();
			PreLiquidation::where('pre_sale_id', $v->id)->delete();
			PreDiscount::where('pre_sale_id', $v->id)->delete();

			$v->delete();
		}

		$rate = Rate::where('description', 'IGV')
			->where('state', 1)
			->select('id', 'value')
			->first();

		$warehouse_movement = PlantOrder::find($guia_id);

		$warehouse_movement->temporal = 1;
		$warehouse_movement->save();

		$igv_percentage = ($rate->value / 100) + 1;

		foreach ($sales as $sale) {

			$client = Client::find($sale['client_id'], [
				'id',
				'code',
				'business_name',
				'link_client_id',
				'payment_id',
				'credit_limit_days',
				'credit_limit',
				'credit_balance',
				'route_id'
			]);

			$client->credit_limit_days = $client->credit_limit_days ? $client->credit_limit_days : 0;

			$sale_model = new PreSale();
			$sale_model->plant_order_id = $guia_id;
			$sale_model->client_id = $sale['client_id'];
			$sale_model->warehouse_document_type_id = $sale['warehouse_document_type_id'];
			$sale_model->total = $sale['sale_value'];
			$sale_model->total_perception = $sale['total'];

			$sale_model->save();

			foreach ($sale['details'] as $index => $detail) {

				$sale_detail = new PreSaleDetail();
				$sale_detail->pre_sale_id = $sale_model->id;
				$sale_detail->item_number = $index + 1;
				$sale_detail->article_id = $detail['article_id'];
				$sale_detail->quantity = $detail['article_quantity'];
				$sale_detail->price_igv = $detail['article_price'];
				$sale_detail->sale_value = $detail['article_sale_value'];
				$sale_detail->igv = round($detail['article_sale_value'], 4) - round($detail['article_sale_value'] / $igv_percentage, 4);
				$sale_detail->total = $detail['article_sale_value'];
				$sale_detail->total_perception = $detail['article_total_perception'];
				$sale_detail->igv_perception_percentage = $sale['perception_percentage'];
				$sale_detail->save();
			}

			$balance = 0;
			$paid = 0;

			foreach ($sale['liquidations'] as $liquidation) {
				if ($liquidation['payment_id'] == 1 || $liquidation['payment_id'] == 4) {
					$paid = $paid + $liquidation['amount'];
				} else {
					$balance = $balance + $liquidation['amount'];
				}
			}

			$sale_model->balance = $balance;
			$sale_model->paid = $paid;
			$sale_model->pre_balance = $balance;
			$sale_model->save();

			foreach ($sale['liquidations'] as $liquidation) {

				if ($liquidation['payment_id'] != 2) {
					$sale_liquidation = new PreLiquidation();
					$sale_liquidation->pre_sale_id = $sale_model->id;
					$sale_liquidation->payment_method_id = $liquidation['payment_method_id'];
					$sale_liquidation->currency_id = $liquidation['currency_id'];

					if ($liquidation['payment_method_id'] == 2) {
						$deposit = PlantDeposit::find($liquidation['deposit_id']);
						$sale_liquidation->deposit_id = $deposit->id;
					}

					if ($liquidation['payment_id'] == 4) {
						$discount = new PreDiscount();
						$discount->employee_id = $liquidation['employee_id'];
						$discount->pre_sale_id = $sale_model->id;
						$discount->total = $liquidation['amount'];
						$discount->paid = 0;
						$discount->balance =  $liquidation['amount'];
						$discount->save();
					}

					$sale_liquidation->amount = $liquidation['amount'];
					$sale_liquidation->save();
				}
			}
		}

		$type = 3;
		$title = '¡Ok!';
		$msg = '¡La venta se ha registrado exitosamente!';
		$url = route('dashboard.preventa.start');

		return response()->json([
			'type'     => $type,
			'title'    => $title,
			'msg'      => $msg,
			'url'      => $url,
		]);
	}


	public function login()
	{

		$this->validateForm();

		$pin = request('pin');
		$guia_id = request('guia_id');
		$articles = [];
		$temporal = 0;

		$sales = [];
		$liquidations = [];

		if ($pin == '123') {

			$articles = PlantOrder::leftjoin('plant_order_details', 'plant_order_details.plant_order_id', '=', 'plant_orders.id')
				->leftjoin('articles', 'articles.id', '=', 'plant_order_details.article_id')
				->select(
					'articles.id as article_id',
					'articles.name as article_name',
					'articles.igv as article_igv',
					'plant_order_details.quantity as preventa',
					DB::raw('(IFNULL(plant_order_details.return, 0) ) AS retorno'),
					DB::raw('IFNULL(plant_order_details.new_quantity,0) as venta'),
					DB::raw('IFNULL(plant_order_details.new_quantity,0) as saldo')
				)
				->where('plant_orders.id', $guia_id)
				->where('plant_order_details.quantity', '>', 0)
				->get();

			$obj = PlantOrder::findOrFail($guia_id);
			$temporal = $obj->temporal;

			if ($temporal == 1) {
				$sales = PreSale::with([
					'sale_details.article',
					'warehouseDocumentType',
					'client',
					'pre_liquidations.payment_method.payment',
					'pre_liquidations.currency',
					'pre_liquidations.deposit',
					'pre_liquidations.deposit.bank_account',
					'discount',
					'discount.employee',
					'client.perception_percentage'
				])
					->where('plant_order_id', $guia_id)
					->get()
					->map(function ($sale) {

						$liquidations = $sale->pre_liquidations->map(function ($pre) use ($sale) {
							return [
								'payment_method_id' => optional($pre->payment_method)->id,
								'payment_method_name' => optional($pre->payment_method)->name,
								'payment_id' => optional(optional($pre->payment_method)->payment)->id,
								'payment_name' => optional(optional($pre->payment_method)->payment)->name,
								'currency_id' => optional($pre->currency)->id,
								'currency_name' => optional($pre->currency)->name,
								'bank_account' => (optional(optional(optional($pre->deposit)->bank_account)->bank)->short_name ?? '') .
									' - ' .
									(optional(optional($pre->deposit)->bank_account)->account_number ?? ''),
								'deposit_id' => optional($pre->deposit)->id ?? '',
								'operation_number' => optional($pre->deposit)->operation_number ?? '',
								'date_emision'     => optional($pre->deposit)->bank_date ?? '',
								'employee_id' => (optional(optional($pre->payment_method)->payment)->id == 4)
									? (optional(optional($sale->discount)->employee)->id ?? '')
									: '',
								'employee_name' => (optional(optional($pre->payment_method)->payment)->id == 4)
									? trim(
										(optional(optional($sale->discount)->employee)->first_name ?? '') . ' ' .
											(optional(optional($sale->discount)->employee)->last_name ?? '')
									)
									: '',
								'amount' => $pre->amount
							];
						});

						if ($sale->pre_balance > 0) {
							$liquidations->push([
								'payment_method_id' => '',
								'payment_method_name' => '',
								'payment_id' => 2,
								'payment_name' => 'Crédito',
								'currency_id' => 1,
								'currency_name' => 'Soles',
								'bank_account' => '',
								'deposit_id' => '',
								'operation_number' => '',
								'date_emision' => '',
								'employee_id' => '',
								'employee_name' => '',
								'amount' => $sale->pre_balance,
							]);
						}

						return [
							'warehouse_document_type_id' => $sale->warehouse_document_type_id,
							'client_name' => $sale->client->id . ' - ' . $sale->client->business_name,
							'client_id' => $sale->client->id,
							'warehouse_document_type_name' => $sale->warehouseDocumentType->name,
							'sale_value' => $sale->total,
							'perception' => round($sale->total_perception - $sale->total, 2),
							'total' => $sale->total_perception,
							'perception_percentage' => $sale->client->perception_percentage->value,
							'details' => $sale->sale_details->map(function ($detail) {
								return [
									'article_id' => $detail->article_id,
									'article_name' => optional($detail->article)->name,
									'article_price' => $detail->price_igv,
									'article_igv' => $detail->total,
									'article_quantity' => $detail->quantity,
									'article_sale_value' => $detail->sale_value,
									'article_igv_perception' => $detail->total,
									'article_total_perception' => $detail->total_perception,
								];
							}),
							'liquidations' => $liquidations
						];
					});
			}

			$type = 1;
			$title = 'Bien';
			$msg = 'Bienvenido.';
		} else {
			$type = 5;
			$title = 'Error';
			$msg = 'PIN incorrecto';
		}

		return response()->json([
			'type'  => $type,
			'title' => $title,
			'msg'   => $msg,
			'articles' => $articles,
			'guia_id'	=> $guia_id,
			'temporal' => $temporal,
			'sales' => $sales
		]);
	}
}
