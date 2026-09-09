<?php

namespace App\Http\Controllers\Backend;

use App\BankAccount;
use App\Client;
use App\Company;
use App\Currency;
use App\Deposit;
use App\WarehouseAccountType;
use App\PaymentMethod;
use App\Provider;
use App\Employee;
use App\Http\Controllers\Controller;
use App\Liquidation;
use App\Payment;
use App\Sale;
use App\SaleDetail;
use App\Unit;
use App\ValueType;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RegisterOtherCashController extends Controller
{
	public function index()
	{
		$companies = Company::select('id', 'name')->get();
		$min_sale_date = CarbonImmutable::now()->toAtomString();
		$max_sale_date = CarbonImmutable::now()->toAtomString();
		$payments = Payment::select('id', 'name')->whereIn('id', [1])->get();
		$currencies = Currency::select('id', 'name')->whereIn('id', [1])->get();
		$units = Unit::select('id', 'name')->get();
		$value_types = ValueType::select('id', 'name')->whereIn('id', [1])->get();
		$warehouse_account_types = WarehouseAccountType::select('id', 'name')->get();

		$deposits = Deposit::leftjoin('banks', 'deposits.bank_id', '=', 'banks.id')
			->leftjoin('bank_accounts', 'deposits.bank_account_id', '=', 'bank_accounts.id')
			->select(
				'deposits.id as id',
				'deposits.total_pend as total_pend',
				'deposits.bank_account_id as bank_account_id',
				'deposits.operation_number as operation_number',
				DB::Raw('CONCAT(bank_date," ",banks.name," ",bank_accounts.account_number," - ",deposits.operation_number," - S/ ", deposits.total_pend) AS texto')
			)
			->where('total_pend', '>', 0)
			->orderBy('deposits.id', 'desc')
			->get();

		return view('backend.register_other_cash')->with(compact(
			'companies',
			'min_sale_date',
			'max_sale_date',
			'payments',
			'currencies',
			'units',
			'value_types',
			'warehouse_account_types',
			'deposits'
		));
	}

	public function validateFirstStep()
	{
		$messages = [
			'company_id.required'					=> 'Debe seleccionar una Compañía.',
			'issue_date.required'	=> 'Debe seleccionar la Fecha de Emisión.',
			'warehouse_account_id.required'	=> 'Debe seleccionar el Nombre o Razón Social.',

		];

		$rules = [
			'company_id'					=> 'required',
			'issue_date'	=> 'required',
			'warehouse_account_id'	=> 'required',
		];

		request()->validate($rules, $messages);
		return request()->all();
	}

	public function getBankAccount()
	{
		$currency_id = request('currency_id');
		$elements = BankAccount::join('banks', 'bank_accounts.bank_id', '=', 'banks.id')
			->select(
				'bank_accounts.id as id',
				'banks.short_name as bank_short_name',
				'bank_accounts.account_number as bank_account'
			)
			->where('bank_accounts.currency_id', $currency_id)
			//->where('bank_accounts.company_id', 1)
			->get();
		return $elements;
	}

	public function getPaymentMethod()
	{
		$payment_id = request('payment_id');
		$elements = PaymentMethod::where('payment_id', $payment_id)->whereIn('id', [1, 2, 3])->get();
		return $elements;
	}

	public function getAccounts()
	{
		$company_id = request('company_id');
		$warehouse_account_type_id = request('warehouse_account_type_id');
		$q = request('q');

		if ($warehouse_account_type_id == 1) {
			$clients = Client::select('id', 'business_name')
				->where('company_id', $company_id)
				->where('business_name', 'like', '%' . $q . '%')
				->get();

			$clients->map(function ($item, $index) {
				$item->text = $item->business_name;
				unset($item->business_name);

				return $item;
			});
		} elseif ($warehouse_account_type_id == 2) {
			$clients = Provider::select('id', 'business_name')
				->where('business_name', 'like', '%' . $q . '%')
				->get();

			$clients->map(function ($item, $index) {
				$item->text = $item->business_name;
				unset($item->business_name);

				return $item;
			});
		} elseif ($warehouse_account_type_id == 3) {
			$clients = Employee::select('id', 'first_name', 'last_name')
				->where(function ($query) use ($q) {
					$query->where('first_name', 'like', '%' . $q . '%')
						->orWhere('last_name', 'like', '%' . $q . '%');
				})
				->get();

			$clients->map(function ($item, $index) {
				$item->text = $item->first_name . ' ' . $item->last_name;
				unset($item->first_name);
				unset($item->last_name);

				return $item;
			});
		}

		return $clients;
	}

	public function validateThirdStep()
	{
		$messages = [
			'concept.required'					=> 'El Concepto es obligatorio.',
			'unit_id.required'					=> 'Debe seleccionar la Unidad de Medida.',
			'quantity.required'					=> 'Debe digitar la cantidad',
			'price_igv.required'					=> 'Debe digitar el Precio Unitario.',
			'value_type_id.required'					=> 'Debe seleccionar el Tipo de Venta.',

		];

		$rules = [
			'concept'				=> 'required',
			'unit_id'				=> 'required',
			'quantity'				=> 'required',
			'price_igv'				=> 'required',
			'value_type_id'				=> 'required',
		];

		request()->validate($rules, $messages);
		return request()->all();
	}

	public function store()
	{

		$company_id = request('model.company_id');
		$issue_date = request('model.issue_date');
		$client_id = request('model.warehouse_account_id');
		$payment_id = 1; //contado
		$currency_id = request('model.currency_id');
		$warehouse_document_type_id = request('model.warehouse_document_type_id');

		$conceptos = request('conceptos');
		$liquidations_list = request('liquidations_list');

		$serie = '018';

		$last = Sale::where('warehouse_document_type_id', $warehouse_document_type_id)
			->where('referral_serie_number', $serie)
			->max('referral_voucher_number');

		$sale = new Sale();
		$sale->company_id = $company_id;
		$sale->sale_date = $issue_date;
		$sale->expiry_date = $issue_date;
		$sale->client_id = $client_id;
		$sale->payment_id = $payment_id;
		$sale->currency_id = $currency_id;
		$sale->referral_serie_number = $serie;
		$sale->referral_voucher_number = $last ? $last + 1 : 1;
		$sale->warehouse_document_type_id = $warehouse_document_type_id;
		$sale->sale_value = array_sum(array_column($conceptos, 'sale_value'));
		$sale->igv = array_sum(array_column($conceptos, 'igv'));;
		$sale->total = array_sum(array_column($conceptos, 'total'));;
		$sale->total_perception = array_sum(array_column($conceptos, 'total'));
		$sale->balance = 0;
		$sale->paid = 0;
		$sale->created_at_user = Auth::user()->user;
		$sale->updated_at_user = Auth::user()->user;
		$sale->save();

		foreach ($conceptos as $item) {
			$detail = new SaleDetail();
			$detail->sale_id = $sale->id;
			$detail->item_number = $item['item_number'];
			$detail->concept = $item['concept'];
			$detail->unit_id = $item['unit_id'];
			$detail->value_type_id = $item['value_type_id'];
			$detail->quantity = $item['quantity'];
			$detail->price_igv = $item['price_igv'];
			$detail->sale_value = $item['sale_value'];
			$detail->igv = $item['igv'];
			$detail->total = $item['total'];
			$detail->total_perception = $item['total'];
			$detail->igv_percentage = 18.00;
			$detail->created_at_user = Auth::user()->user;
			$detail->updated_at_user = Auth::user()->user;
			$detail->save();
		}

		foreach ($liquidations_list as $liquidation) {
			$obj = new Liquidation();
			$obj->sale_id = $sale->id;
			$obj->company_id = $company_id;
			$obj->payment_method_id = $liquidation['payment_method_id'];
			$obj->currency_id = $liquidation['currency_id'];
			$obj->bank_account_id = $liquidation['bank_account_id'];
			$obj->operation_number = $liquidation['operation_number'];
			$obj->amount = $liquidation['amount'];
			$obj->collection = 5; //Otros ingresos de caja
			$obj->deposit_id = $liquidation['deposit_id'];
			$obj->save();

			if ($liquidation['payment_method_id'] == 2) {
				$deposit = Deposit::find($liquidation['deposit_id']);
				$deposit->total_pend -= $liquidation['amount'];
				$deposit->paid += $liquidation['amount'];
				$deposit->save();
			}
		}

		$type = 3;
		$title = '¡Ok!';
		$msg = "Finalización exitosa.";
		$url = '/facturacion/ingresos';


		return response()->json([
			'type'    => $type,
			'title'    => $title,
			'msg'    => $msg,
			'url'    => $url,
		]);
	}
}
