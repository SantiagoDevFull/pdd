<?php

namespace App\Http\Controllers\Backend;

use App\BankAccount;
use App\ClientMotor;
use App\Currency;
use App\Exports\OrderWorkReportExport;
use App\Http\Controllers\Controller;
use App\Liquidation;
use App\OrderWork;
use App\OrderWorkDetail;
use App\Payment;
use App\PaymentMethod;
use App\PmLiquidation;
use App\Vehicle;
use App\Voucher;
use App\VoucherDetail;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Fpdf;

class LiquidationPmController extends Controller
{
    public function index()
    {

        $payments = Payment::whereIn('id', [1, 2])->get();
        $currencies = Currency::whereIn('id', [1])->get();

        return view('backend.liquidations_pm')->with(compact('payments', 'currencies'));
    }

    public function list()
    {
        $company_id = 5;

        $vouchers = Voucher::leftjoin('companies', 'companies.id', '=', 'vouchers.company_id')
            ->leftjoin('voucher_types', 'voucher_types.id', '=', 'vouchers.voucher_type_id')
            ->leftjoin('client_motors', 'client_motors.id', '=', 'vouchers.client_id')
            ->leftjoin('payments', 'payments.id', '=', 'client_motors.payment_id')
            ->select(
                'vouchers.id as id',
                'companies.name as company_name',
                'voucher_types.name AS voucher_type_name',
                DB::Raw('CONCAT(vouchers.serie_number,"-",vouchers.voucher_number) as factura'),
                DB::Raw('CONCAT(vouchers.referral_guide_series,"-",vouchers.referral_guide_number) as cotizacion'),
                'vouchers.client_name as client_name',
                'payments.name as payment_name',
                'client_motors.credit_limit as limit',
                'vouchers.ose as ose',
                'vouchers.taxed_operation as subtotal',
                'vouchers.igv as igv',
                'vouchers.total as total'
            )
            ->where('vouchers.company_id', $company_id)
            ->orderBy('vouchers.id', 'DESC')
            ->get();

        return $vouchers;
    }

    public function validateModalForm()
    {
        $messages = [
            'payment_id.required'            => 'Debe seleccionar una Condición de Pago.',
            'payment_method_id.required_if'  => 'Debe seleccionar una Forma de Pago.',
            'currency_id.required_if'        => 'Debe seleccionar una Moneda.',
            'amount.required_if'             => 'Debe digitar un Monto.',
            'bank_account_id.required_if'    => 'Debe seleccionar el Banco.',
            'operation_number.required_if'   => 'Debe digitar el N° de Operación.',
        ];

        $rules = [
            'payment_id'                        => 'required',
            'payment_method_id'                 => 'required_if:payment_id,1',
            'currency_id'                       => 'required_if:payment_id,1',
            'amount'                            => 'required_if:payment_id,1',
            'bank_account_id'                   => 'required_if:payment_method_id,2,3',
            'operation_number'                  => 'required_if:payment_method_id,2,3',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function getDetail()
    {
        $voucher_id = request('id');

        $voucher_details = VoucherDetail::where('voucher_id', $voucher_id)->get();

        return $voucher_details;
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
            'payment_method_name' => $payment_method_name,
            'currency_name' => $currency_name,
            'bank_account_name' => $bank_account_name,
        ]);
    }

    public function store()
    {

        $voucher_id = request('id');
        $liquidations = request('liquidations');

        $voucher = Voucher::findOrFail($voucher_id);
        $voucher->ose = 0;
        $voucher->save();

        $client = ClientMotor::findOrFail($voucher->client_id);

        foreach ($liquidations as $item) {
            if ($item['payment_id'] == 1) { //contado
                $liquidation = new PmLiquidation();
                $liquidation->voucher_id = $voucher_id;
                $liquidation->company_id = $voucher->company_id;
                $liquidation->payment_method_id = $item['payment_method_id'];
                $liquidation->currency_id = $item['currency_id'];
                $liquidation->bank_account_id = $item['bank_account_id'];
                $liquidation->operation_number = $item['operation_number'];
                $liquidation->amount = $item['amount'];
                $liquidation->save();
            } else if ($item['payment_id'] == 2) { //CREDITO
                DB::table('credits')->insert([
                    'voucher_id' => $voucher_id,
                    'company_id' =>  $voucher->company_id,
                    'client_id' => $voucher->client_id,
                    'currency_id' =>  $item['currency_id'],
                    'amount' =>  $item['amount'],
                    'expiry_date' =>  Carbon::now()->addDays($client->credit_limit_days)->toDateString(),
                    'created_at_user' => auth()->user()->name,
                    'created_at' => Carbon::now(),
                ]);
            }
        }

        $type = 3;
        $title = '¡Ok!';
        $msg = "Factura liquidado correctamente.";
        $url = '/facturacion-liquidaciones-pm';


        return response()->json([
            'type'    => $type,
            'title'    => $title,
            'msg'    => $msg,
            'url'    => $url,
        ]);
    }
}
