<?php

namespace App\Http\Controllers\Backend;

use App\BankAccount;
use App\Cicle;
use App\Company;
use App\Exports\FacturationCocheraReportExport;
use App\Http\Controllers\Controller;
use App\CocheraMovement;
use App\CocheraMovementDetail;
use App\Currency;
use App\Paid;
use App\Payment;
use App\PaymentMethod;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use DateTime;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Fpdf;

class FacturationCocheraReportController  extends Controller
{
	public function index()
	{

		$firstDayOfMonth = new DateTime('first day of this month midnight');

		$current_date_1 = $firstDayOfMonth->format(DATE_ATOM);
		$current_date_2 = date(DATE_ATOM, mktime(0, 0, 0));

		$payments = Payment::whereIn('id', [1, 2])->get();
		$currencies = Currency::whereIn('id', [1, 2])->get();
		$companies = Company::select('id', 'name')->get();
		$current_date = date(DATE_ATOM, mktime(0, 0, 0));
		return view('backend.facturation_cochera_report')->with(compact('companies', 'current_date', 'payments', 'currencies', 'current_date_1', 'current_date_2'));
	}

	public function validateForm()
	{
		$messages = [
			'company_id.required'    => 'Debe seleccionar una compañía.',
			'initial_date.required'    => 'Debe seleccionar una Fecha inicial.',
			'final_date.required'    => 'Debe seleccionar una Fecha final.',
		];

		$rules = [
			'company_id'            => 'required',
			'initial_date'    => 'required',
			'final_date'    => 'required',
		];

		request()->validate($rules, $messages);
		return request()->all();
	}

	public function validateModalForm()
	{
		$messages = [
			'payment_id.required'            => 'Debe seleccionar una Condición de Pago.',
			'payment_method_id.required_if'  => 'Debe seleccionar una Forma de Pago.',
			'currency_id.required_if'        => 'Debe seleccionar una Moneda.',
			'tc.required_if'     		     => 'Debe digitar Cambio.',
			'amount.required_if'             => 'Debe digitar un Monto.',
			'bank_account_id.required_if'    => 'Debe seleccionar el Banco.',
			'operation_number.required_if'   => 'Debe digitar el N° de Operación.',
		];

		$rules = [
			'payment_id'                        => 'required',
			'payment_method_id'                 => 'required_if:payment_id,1',
			'currency_id'                       => 'required_if:payment_id,1',
			'tc'                    		    => 'required_if:currency_id,1',
			'amount'                            => 'required_if:payment_id,1',
			'bank_account_id'                   => 'required_if:payment_method_id,2,3',
			'operation_number'                  => 'required_if:payment_method_id,2,3',
		];


		request()->validate($rules, $messages);
		return request()->all();
	}

	public function getPaymentMethod()
	{

		$payment_id = request('payment_id');
		$elements = PaymentMethod::where('payment_id', $payment_id)->get();

		return $elements;
	}

	public function getBankAccount()
	{

		$currency_id = request('currency_id');

		$elements = BankAccount::where('currency_id', $currency_id)->get();

		return $elements;
	}

	public function getLiquidation()
	{

		$this->validateModalForm();

		$payment_method_name = '';
		$currency_name = '';
		$bank_account_name = '';

		$payment_id = request('payment_id');
		$payment_method_id = request('payment_method_id');
		$currency_id = request('currency_id');
		$bank_account_id = request('bank_account_id');
		$operation_number = request('operation_number');
		$amount = request('amount');
		$tc = request('tc');

		$payment = Payment::findOrFail($payment_id);
		$payment_name = $payment->name;

		if ($payment_id != 2) {
			$payment_method = PaymentMethod::findOrFail($payment_method_id);
			$currency = Currency::findOrFail($currency_id);

			if ($payment_method_id == 2 || $payment_method_id == 3) {
				$bank_account = BankAccount::findOrFail($bank_account_id);
				$bank_account_name = $bank_account->account_number;
			}

			$payment_method_name = $payment_method->name;
			$currency_name = $currency->name;
		}

		return response()->json([
			'payment_id' => $payment_id,
			'payment_name' => $payment_name,
			'payment_method_id' => $payment_method_id,
			'currency_id' => $currency_id,
			'bank_account_id' => $bank_account_id,
			'operation_number' => $operation_number,
			'amount' => $amount,
			'tc' => $tc,
			'payment_method_name' => $payment_method_name,
			'currency_name' => $currency_name,
			'bank_account_name' => $bank_account_name,
		]);
	}

	public function store()
	{

		$now = Carbon::now();
		$mes = $now->format('m');
		$anio = $now->format('Y');

		$cicle = Cicle::where('mes', $mes)
			->where('anio', $anio)
			->first();

		$movement_id = request('id');
		$liquidations = request('liquidations');

		$cochera_movement = CocheraMovement::findOrFail($movement_id);

		$total_paid = 0;
		foreach ($liquidations as $item) {

			$paid = new Paid();
			$paid->cochera_movement_id = $movement_id;
			$paid->company_id = $cochera_movement->company_id;
			$paid->payment_method_id = $item['payment_method_id'];
			$paid->currency_id = $item['currency_id'];
			$paid->bank_account_id = $item['bank_account_id'];
			$paid->operation_number = $item['operation_number'];
			$paid->tc = $item['tc'];
			$paid->amount = $item['amount'];
			$paid->total = $item['total'];
			$paid->collection = 3;
			$paid->cicle_id = $cicle->id;
			$paid->save();

			if ($item['payment_id'] == 1) {
				$total_paid = $total_paid + $item['total'];
			}
		}

		$cochera_movement->paid = $total_paid;
		$cochera_movement->balance = $cochera_movement->total - $total_paid;
		$cochera_movement->state = 3; //liquidado
		$cochera_movement->save();

		$type = 1;
		$title = '¡Ok!';
		$msg = "Factura pagada exitosamente.";


		return response()->json([
			'type'    => $type,
			'title'    => $title,
			'msg'    => $msg
		]);
	}

	public function list()
	{

		$company_id = request('model.data.company_id');
		$initial_date = CarbonImmutable::createFromDate(request('model.data.initial_date'))->startOfDay()->format('Y-m-d H:i:s');
		$final_date = CarbonImmutable::createFromDate(request('model.data.final_date'))->endOfDay()->format('Y-m-d H:i:s');

		$export = request('export');

		DB::statement("SET lc_time_names = 'es_ES'");
		$movements = CocheraMovement::leftjoin('companies', 'cochera_movements.company_id', 'companies.id')
			->leftjoin('costos', 'cochera_movements.costo_id', 'costos.id')
			->leftjoin('costo_secundarios', 'cochera_movements.costo_secundario_id', 'costo_secundarios.id')
			->leftjoin('areas', 'cochera_movements.area_id', 'areas.id')
			->leftjoin('currencies', 'cochera_movements.currency_id', 'currencies.id')

			->select(
				'cochera_movements.id as movement_id',
				'companies.name as company_name',
				DB::Raw('CONCAT(costos.name,"-",costo_secundarios.name) as centro_costos'),
				'cochera_movements.account_document_number as provider_ruc',
				'cochera_movements.account_name as provider_name',
				'currencies.name as currency_name',
				'areas.name as area_name',
				'cochera_movements.payment_id as payment_id',
				'cochera_movements.expiry_date as fecha_pago',
				'cochera_movements.state as state',
				'cochera_movements.license_plate as vehicle_plate',
				'cochera_movements.ppto as ppto',
				'cochera_movements.date_at as movement_date',
				'cochera_movements.total as total',
				'cochera_movements.tipo as tipo',
				'cochera_movements.balance as balance',
				'cochera_movements.paid as paid',
				'cochera_movements.observation as observation',
				'cochera_movements.created_at as date',
				DB::raw('DATE_FORMAT(cochera_movements.date_at, "%M") as mes'),
				DB::Raw('CONCAT(cochera_movements.referral_serie_number,"-",cochera_movements.referral_voucher_number) as factura')
			)
			->where('cochera_movements.company_id', $company_id)
			->where('cochera_movements.date_at', '>=', $initial_date)
			->where('cochera_movements.date_at', '<=', $final_date)
			->orderBy('cochera_movements.id', 'desc')
			->get();

		if ($export) {


			$movements = CocheraMovement::leftjoin('companies', 'cochera_movements.company_id', 'companies.id')
			->leftjoin('costos', 'cochera_movements.costo_id', 'costos.id')
			->leftjoin('costo_secundarios', 'cochera_movements.costo_secundario_id', 'costo_secundarios.id')
			->leftjoin('areas', 'cochera_movements.area_id', 'areas.id')
			->leftjoin('currencies', 'cochera_movements.currency_id', 'currencies.id')
			->leftjoin('cochera_movement_details', 'cochera_movement_details.cochera_movement_id', 'cochera_movements.id')
			->select(
				'cochera_movements.id as movement_id',
				'companies.name as company_name',
				DB::Raw('CONCAT(costos.name,"-",costo_secundarios.name) as centro_costos'),
				'cochera_movements.account_document_number as provider_ruc',
				'cochera_movements.account_name as provider_name',
				'currencies.name as currency_name',
				'areas.name as area_name',
				'cochera_movements.payment_id as payment_id',
				'cochera_movements.expiry_date as fecha_pago',
				'cochera_movements.state as state',
				'cochera_movements.license_plate as vehicle_plate',
				'cochera_movements.ppto as ppto',
				'cochera_movements.date_at as movement_date',
				'cochera_movements.total as total',
				'cochera_movements.tipo as tipo',
				'cochera_movements.balance as balance',
				'cochera_movements.paid as paid',
				'cochera_movements.observation as observation',
				'cochera_movement_details.article_name as article_name',
				'cochera_movement_details.plate as plate',
				'cochera_movements.created_at as date',
				DB::raw('DATE_FORMAT(cochera_movements.date_at, "%M") as mes'),
				DB::Raw('CONCAT(cochera_movements.referral_serie_number,"-",cochera_movements.referral_voucher_number) as factura')
			)
			->where('cochera_movements.company_id', $company_id)
			->where('cochera_movements.date_at', '>=', $initial_date)
			->where('cochera_movements.date_at', '<=', $final_date)
			->orderBy('cochera_movements.id', 'desc')
			->get();

			$datetime = CarbonImmutable::now()->format('d/m/Y h:i:s a');

			$excel = new FacturationCocheraReportExport($datetime, $movements);
			return Excel::download($excel, 'reporte-facturas-por-pagar.xlsx');
		}

		return $movements;
	}

	public function delete()
	{
		$movement_id = request('movement_id');
		$movement = CocheraMovement::findOrFail($movement_id);
		$movement->observation = request('observation');
		$movement->state = 2;
		$movement->save();
	}

	public function getFile()
	{
		$id = request('movement_id');
		$movement = CocheraMovement::findOrFail($id);
		$date = $movement->date_at;

		$file = asset('uploads/cochera/facturas/' . $date . '/' . $movement->route_automatic);

		return $file;
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

		$movement_details = CocheraMovementDetail::select(
			'article_name',
			'digit_amount as digit_amount',
			'converted_amount as converted_amount',
			'price as price',
			'sale_value as sale_value',
			'inaccurate_value as inaccurate_value',
			'igv as igv',
			'total as total',
			'plate'
		)
			->where('cochera_movement_id', $movement_id)->get();
		return $movement_details;
	}
}
