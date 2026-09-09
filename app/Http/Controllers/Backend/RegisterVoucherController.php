<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\BankAccount;
use App\ClientRoute;
use App\Company;
use App\Deposit;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RegisterVoucherController extends Controller
{

	public function showRegisterVoucher()

	{
		$data = [
			'companies' => Company::whereIn('id', [1, 2])->get(),
			'routes' => ClientRoute::all(),
			'max_datetime' => date('Y-m-d')
		];

		return view('backend.register_voucher', $data);
	}

	public function getBankAccounts()
	{
		$company_id = request('company_id');
		$bankAccounts = BankAccount::leftjoin('banks', 'banks.id', '=', 'bank_accounts.bank_id')
			->select(
				'bank_accounts.id as id',
				'bank_accounts.account_number as account_number',
				'banks.name as bank_name'
			)
			->where('bank_accounts.company_id', $company_id)
			->get();

		return response()->json($bankAccounts);
	}

	public function detail()
	{

		$id = request('id');
		$deposit = Deposit::find($id);
		return response()->json($deposit);
	}

	public function update()
	{

		request()->validate([
			'company_id' => ['required'],
			'route_id' => ['required'],
			'bank_account_id' => ['required'],
			'bank_date' => ['required'],
			'operation_number' => ['required'],
			'total' => ['required'],
		], [
			'company_id.required' => 'Debe seleccionar empresa',
			'route_id.required' => 'Debe seleccionar ruta',
			'bank_account_id.required' => 'Debe seleccionar cuenta',
			'bank_date.required' => 'Debe seleccionar fecha',
			'operation_number.required' => 'Debe ingresar número de operación',
			'total.required' => 'Debe ingresar total',
		]);

		$id = request('id');
		$bank_id = BankAccount::where('id', request('bank_account_id'))
			->value('bank_id');

		$deposit = Deposit::find($id);

		$deposit->bank_id = $bank_id;
		$deposit->company_id = request('company_id');
		$deposit->bank_account_id = request('bank_account_id');
		$deposit->bank_date = Carbon::parse(request('bank_date'))->format('Y-m-d');
		$deposit->operation_number = request('operation_number');
		$deposit->total = request('total');
		$deposit->total_pend = request('total');
		$deposit->route_id = request('route_id');
		$deposit->obs = request('obs');
		$deposit->updated_at_user = Auth::user()->name;
		$deposit->save();

		return response()->json(null, 200);
	}
	public function list()
	{

		$deposits = Deposit::join('banks', 'deposits.bank_id', '=', 'banks.id')
			->join('bank_accounts', 'deposits.bank_account_id', '=', 'bank_accounts.id')
			->join('client_routes', 'deposits.route_id', '=', 'client_routes.id')
			->join('companies', 'deposits.company_id', '=', 'companies.id')
			->select(
				'deposits.id as id',
				'deposits.bank_date as bank_date',
				'companies.name as company_name',
				DB::raw("CONCAT(banks.name, ' - ', bank_accounts.account_number) as bank_account"),
				'deposits.operation_number as operation_number',
				'deposits.total as total',
				'deposits.total_pend as total_pend',
				'client_routes.name as route_name',
				'deposits.obs as obs'
			)
			->whereColumn('deposits.total_pend', '=', 'deposits.total')
			->whereYear('deposits.bank_date', '>=', 2026)
			->orderBy('deposits.created_at', 'desc')
			->get();

		return response()->json($deposits);
	}

	public function delete()
	{
		$id = request('id');
		Deposit::where('id', $id)->delete();

		return response()->json(null, 200);
	}

	public function registerVoucher()
	{
		request()->validate([
			'model.company_id' => ['required'],
			'model.route_id' => ['required'],
			'model.bank_account_id' => ['required'],
			'model.bank_date' => ['required'],
			'model.operation_number' => ['required'],
			'model.total' => ['required'],
		], [
			'model.company_id.required' => 'Debe seleccionar empresa',
			'model.route_id.required' => 'Debe seleccionar ruta',
			'model.bank_account_id.required' => 'Debe seleccionar cuenta',
			'model.bank_date.required' => 'Debe seleccionar fecha',
			'model.operation_number.required' => 'Debe ingresar número de operación',
			'model.total.required' => 'Debe ingresar total',
		]);

		$bank_id = BankAccount::where('id', request('model.bank_account_id'))
			->value('bank_id');

		$deposit = new Deposit();

		$deposit->bank_id = $bank_id;
		$deposit->company_id = request('model.company_id');
		$deposit->bank_account_id = request('model.bank_account_id');
		$deposit->bank_date = request('model.bank_date');
		$deposit->operation_number = request('model.operation_number');
		$deposit->total = request('model.total');
		$deposit->total_pend = request('model.total');
		$deposit->route_id = request('model.route_id');
		$deposit->obs = request('model.obs');
		$deposit->created_at_user = Auth::user()->name;
		$deposit->save();

		return response()->json(null, 200);
	}
}
