<?php

namespace App\Http\Controllers\Backend;

use App\Article;
use App\Bank;
use App\BankAccount;
use App\Client;
use App\ClientAddress;
use App\Cicle;
use App\Company;
use App\ContPrice;
use App\Currency;
use App\Deposit;
use App\Discount;
use App\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Liquidation;
use App\PaymentMethod;
use App\PriceList;
use App\Rate;
use App\Sale;
use App\SaleDetail;
use App\Voucher;
use App\VoucherDetail;
use App\VoucherType;
use App\WarehouseDocumentType;
use App\WarehouseMovement;
use App\WarehouseMovementDetail;
use App\FactSerie;
use Carbon\Carbon;
use App\Payment;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mockery\Undefined;

class LiquidationEnvasadoController extends Controller
{
    public function index()
    {
        $companies = Company::select('id', 'name')->get();
        $warehouse_document_types = WarehouseDocumentType::select('id', 'name')->whereIn('id', [4, 5, 6, 7, 13])->get();
        $payment_methods = PaymentMethod::select('id', 'name', 'payment_id')->get();
        $currencies = Currency::select('id', 'name')->get();
        $fact_series = FactSerie::select('id', 'num_serie')->whereIn('id', [2, 3, 4, 8, 10, 11, 5, 6, 19, 17])->get();
        $payments = Payment::all();
        $payment_cash = Payment::CASH;
        $payment_credit = Payment::CREDIT;
        //	$payment_credit = PaymentMethod::CREDIT;
        $deposits = Deposit::leftjoin('banks', 'deposits.bank_id', '=', 'banks.id')
            ->leftjoin('bank_accounts', 'deposits.bank_account_id', '=', 'bank_accounts.id')
            ->select(
                'deposits.id as id',
                'deposits.total_pend as total_pend',
                'deposits.operation_number as operation_number',
                DB::Raw('CONCAT(bank_date," ",banks.short_name," ",bank_accounts.account_number," - ",deposits.operation_number," - S/ ", deposits.total_pend) AS texto')
            )
            ->where('total_pend', '>', 0)
            ->orderBy('deposits.id', 'desc')
            ->get();

        return view('backend.liquidations_envasado')->with(
            compact(
                'companies',
                'warehouse_document_types',
                'payment_methods',
                'currencies',
                'payments',
                'payment_cash',
                'payment_credit',
                'fact_series',
                'deposits'
            )
        );
    }

    public function validateClient()
    {


        $client_id = request('client_id');
        $payment_id = request('payment_id');
        $client = Client::findOrFail($client_id);

        $allowedPayments = [
            1 => [1],                     // Solo contado
            2 => [1, 2],                  // Contado y crédito
            3 => [1, 2, 3, 4],            // Contado, crédito, gasto y descuento personal
            4 => [1, 2, 4],               // Contado, crédito y descuento personal
        ];


        if (isset($allowedPayments[$client->payment_id]) && in_array($payment_id, $allowedPayments[$client->payment_id])) {
            return response()->json([
                'status' => 'valid',
                'message' => 'Cliente sí cuenta con el método de pago.'
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Cliente no cuenta con el método de pago.'
        ], 400);
    }

    public function validateForm()
    {
        $messages = [
            'company_id.required'               => 'Debe seleccionar una Compañía.',
            'warehouse_movement_id.required'    => 'El Nº de Parte es obligatorio.',
        ];

        $rules = [
            'company_id'            => 'required',
            'warehouse_movement_id' => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function getWarehouseMovements()
    {
        $company_id = request('company_id');

        $elements = WarehouseMovement::select(
            'id',
            'movement_number',
            'referral_guide_series',
            'referral_guide_number',
            'license_plate',
            'traslate_date'
        )
            ->where('company_id', $company_id)
            ->where('warehouse_type_id', 5)
            ->whereIn('movement_type_id', [11, 12])
            ->where('sale_id', null)
            ->where('state', 0)
            ->when($company_id == 2, function ($query) {
                $query->where('referral_guide_series', '!=', 5);
            })
            ->whereNotIn('referral_guide_series', ['138'])
            ->orderBy('movement_number', 'asc')
            ->get();

        $elements->map(function ($item, $index) {
            $item->creation_date = date('d-m-Y', strtotime($item->traslate_date));
        });

        return $elements;
    }

    public function list()
    {
        $company_id = request('model.company_id');
        $warehouse_type_id = request('model.warehouse_type_id');
        $warehouse_movement_id = request('model.warehouse_movement_id');

        $saleWarehouseMovement = WarehouseMovement::select('id', 'referral_serie_number', 'referral_voucher_number')
            ->where('id', $warehouse_movement_id)
            ->where('company_id', $company_id)
            ->first();

        $movementDetails = WarehouseMovementDetail::select('id', 'warehouse_movement_id', 'item_number', 'article_code', 'converted_amount', 'new_stock_return')
            ->where('warehouse_movement_id', $warehouse_movement_id)
            ->orderBy('item_number', 'asc')
            ->get();

        $movementDetails->map(function ($item, $index) {
            $item->sale_warehouse_movement_id = $item->warehouse_movement_id;
            $item->article_id = $item->article->id;
            $item->article_code = $item->article->code;
            $item->article_name = $item->article->name . ' ' . $item->article->warehouse_unit->name . ' x ' . $item->article->package_warehouse;
            $item->presale_converted_amount = $item->converted_amount;
            $item->sale_converted_amount = number_format(0, 2, '.', '');
            $item->return_converted_amount = $item->new_stock_return;
            $item->balance_converted_amount = number_format($item->converted_amount - $item->return_converted_amount, 2, '.', '');
            $item->new_balance_converted_amount = number_format($item->converted_amount - $item->return_converted_amount, 2, '.', '');

            unset($item->warehouse_movement_id);
            unset($item->converted_amount);
        });

        return $movementDetails;
    }

    public function getClients()
    {
        $company_id = request('company_id');
        $client_id = request('client_id');
        $q = request('q');

        if (isset($client_id)) {
            $elements = Client::select('id', 'code', 'business_name', 'payment_id', 'perception_percentage_id', 'credit_limit', 'route_id')
                ->where('id', $client_id)
                ->whereNotIn('route_id', [98, 99])
                ->first();

            $elements->text = $elements->business_name;
            unset($elements->business_name);
        } else {
            $elements = Client::select('id', 'code', 'business_name', 'document_type_id', 'payment_id', 'perception_percentage_id', 'credit_limit', 'route_id')
                ->where('company_id', $company_id)

                ->where(function ($query) use ($q) {
                    $query->where('business_name', 'like', '%' . $q . '%')
                        ->orWhere('id', 'like', '%' . $q . '%');
                })
                ->whereNotIn('route_id', [98, 99])
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
        }

        return $elements;
    }

    public function getArticlePrice()
    {
        $article_id = request('article_id');
        $client_id = request('client_id');
        $warehouse_movement_id = request('warehouse_movement_id');
        $warehouse_movement = WarehouseMovement::find($warehouse_movement_id, ['id', 'traslate_date']);
        $today = Carbon::now()->startOfDay();
        $current_date = date('Y-m-d', strtotime($warehouse_movement->traslate_date));

        $element = PriceList::select('id', 'article_id', 'price_igv')
            ->where('client_id', $client_id)
            ->where('warehouse_type_id', 5)
            ->where('article_id', $article_id)
            ->where('initial_effective_date', '<=', $current_date)
            ->where('final_effective_date', '>=', $current_date)
            ->where('state', 1)
            ->with(['article' => function ($query) {
                $query->select('id', 'igv', 'perception');
            }])
            ->first();

        return $element;
    }

    public function getBankAccounts()
    {
        $company_id = request('company_id');
        $currency_id = request('currency_id');

        $elements = BankAccount::select('id', 'company_id', 'bank_id', 'bank_account_type_id', 'currency_id', 'account_number')
            ->where('company_id', $company_id)
            ->where('currency_id', $currency_id)
            ->orderBy('bank_id', 'asc')
            ->get();

        $elements->map(function ($item, $index) {
            $item->name = $item->bank->name . ' ' . $item->bank_account_type->name . ' - ' . $item->account_number;

            unset($item->bank);
            unset($item->bank_account_type);
        });

        return $elements;
    }

    public function verifyDocumentType()
    {
        $company_id = request('model.company_id');
        $warehouse_movement_id = request('model.warehouse_movement_id');
        $warehouse_document_type_id = request('warehouse_document_type_id');
        $referral_serie_number = request('referral_serie_number');
        $referral_voucher_number = request('referral_voucher_number');

        $warehouse_document_type = WarehouseDocumentType::find($warehouse_document_type_id, ['id', 'voucher_type_id', 'previous_date_flag', 'same_voucher_number_flag']);

        $voucher_type = VoucherType::find($warehouse_document_type->voucher_type_id, ['id', 'serie_type']);
        if ($voucher_type) {
            if ($voucher_type->id == 3 || $voucher_type->id == 4) {
                $serie_number = $voucher_type->serie_type . sprintf('%02d', $referral_serie_number);
            } else {
                $serie_number = $voucher_type->serie_type . sprintf('%03d', $referral_serie_number);
            }
        }
        $warehouse_movement = WarehouseMovement::find($warehouse_movement_id, ['id', 'traslate_date']);
        $warehouse_movement_traslate_date = date('Y-m-d', strtotime($warehouse_movement->traslate_date));

        if ($warehouse_document_type->previous_date_flag) {
            $voucher = Voucher::where('company_id', $company_id)
                ->where('voucher_type_id', $voucher_type->id)
                ->where('serie_number', $serie_number)
                ->orderBy('voucher_number', 'desc')
                ->select('id', 'issue_date')
                ->first();

            if ($voucher) {
                if ($voucher->issue_date <= $warehouse_movement_traslate_date) {
                    return response()->json([
                        'verify' => true
                    ]);
                } else {
                    return response()->json([
                        'verify' => false,
                        'msg' => 'Ya existe comprobante con fecha posterior al despacho de este parte. Debe cambiar la Serie de Referencia.',
                    ]);
                }
            }
        }

        if ($warehouse_document_type->same_voucher_number_flag) {
            $voucher = Voucher::where('company_id', $company_id)
                ->where('voucher_type_id', $voucher_type->id)
                ->where('serie_number', $serie_number)
                ->where('voucher_number', $referral_voucher_number)
                ->first();

            if ($voucher) {
                return response()->json([
                    'verify' => false,
                    'msg' => 'Este comprobante ya fue registrado anteriormente.',
                ]);
            } else {
                return response()->json([
                    'verify' => true
                ]);
            }
        }
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

        $model = request('model');
        $sales = request('sales');
        $boleteo = request('boleteo');

        $warehouse_movement = WarehouseMovement::find($model['warehouse_movement_id'], ['id', 'referral_guide_series', 'referral_guide_number', 'scop_number', 'license_plate', 'state', 'traslate_date', 'created_at']);
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
            if ($sale['payment_id'] == 2) {
                $expiry_date = CarbonImmutable::createFromFormat('Y-m-d', $sale_date)->addDays($client->credit_limit_days);
            }


            $mesActual = date('m', strtotime($sale_date));
            $anioActual = date('Y', strtotime($sale_date));

            $cicle_id = Cicle::where('mes', $mesActual)
                ->where('anio', $anioActual)
                ->select('id')
                ->sum('id');

            //	$cicle_id= $cicle->id;

            $sale_model = new Sale();
            $sale_model->company_id = $model['company_id'];
            $sale_model->sale_date = $sale_date;
            $sale_model->expiry_date = $expiry_date;
            $sale_model->warehouse_movement_id = $warehouse_movement->id;
            $sale_model->guide_date = $warehouse_movement->created_at;
            $sale_model->client_id = $client->id;
            $sale_model->client_code = $client->code;
            $sale_model->route_id = $client->route_id;
            if ($sale['payment_id'] == 3 || $sale['payment_id'] == 4) {
                $sale['payment_id'] = 1;
            }
            $sale_model->payment_id =  $sale['payment_id'];
            $sale_model->currency_id = $sale['currency_id'];
            $sale_model->guide_series = $warehouse_movement->referral_guide_series;
            $sale_model->guide_number = $warehouse_movement->referral_guide_number;
            $sale_model->warehouse_document_type_id = $sale['warehouse_document_type_id'];
            $sale_model->mes = $mesActual;
            $sale_model->anio = $anioActual;
            $sale_model->cicle_id = $cicle_id;

            if ($sale['warehouse_document_type_id'] == 5) {
                $sale_model->scop_number = $sale['scop_number'] ? $sale['scop_number'] : '';
            }

            if ($sale['warehouse_document_type_id'] >= 4 && $sale['warehouse_document_type_id'] <= 9) {
                switch ($sale['warehouse_document_type_id']) {
                    case 4:
                        $voucher_type_id = 5;
                        break;
                    case 5:
                        $voucher_type_id = 1;
                        break;
                    case 6:
                        $voucher_type_id = 6;
                        break;
                    case 7:
                        $voucher_type_id = 2;
                        break;
                    case 8:
                        $voucher_type_id = 7;
                        break;
                    case 9:
                        $voucher_type_id = 3;
                        break;
                }

                $voucher_type = VoucherType::find($voucher_type_id, ['id', 'serie_type']);
                $serie_number = $voucher_type->serie_type . sprintf('%03d', $sale['referral_serie_number']);
                $last_voucher_number = Voucher::where('company_id', $model['company_id'])
                    ->where('voucher_type_id', $voucher_type->id)
                    ->where('serie_number', $serie_number)
                    ->max('voucher_number');

                $voucher = new Voucher();
                $voucher->company_id = $model['company_id'];
                $voucher->client_id = $client->id;
                $voucher->original_client_id = $client->id;
                $voucher->client_name = $client->business_name;
                $voucher->client_address = $client_address->address;
                $voucher->voucher_type_id = $voucher_type->id;
                $voucher->serie_number = $serie_number;
                $voucher->voucher_number = ++$last_voucher_number;
                $voucher->referral_guide_series = ($sale['referral_guide_series'] ? $sale['referral_guide_series'] : $warehouse_movement->referral_guide_series);
                $voucher->referral_guide_number = ($sale['referral_guide_number'] ? $sale['referral_guide_number'] : $warehouse_movement->referral_guide_number);
                $voucher->issue_date = date('Y-m-d', strtotime($warehouse_movement->traslate_date));
                $voucher->issue_hour = date('H:i:s', strtotime($warehouse_movement->traslate_date));
                $voucher->expiry_date = $expiry_date;
                $voucher->currency_id = $sale['currency_id'];
                $voucher->payment_id = $sale['payment_id'];
                $voucher->total = $sale['total'];
                $voucher->igv_perception = $sale['perception'];
                $voucher->total_perception = $sale['total_perception'];
                $voucher->igv_percentage = $rate->value;
                $voucher->igv_perception_percentage = $sale['perception_percentage'] / 100;

                if ($voucher_type->id >= 1 && $voucher_type->id <= 4) {
                    $voucher->ose = 0;
                } else {
                    $voucher->ose = 1;
                }

                if ($sale['warehouse_document_type_id'] == 5) {
                    $voucher->scop_number = $sale['scop_number'] ? $sale['scop_number'] : '';
                }

                $voucher->user = Auth::user()->user;
                $voucher->save();

                $taxed_operation = 0;
                $igv = 0;
                foreach ($sale['details'] as $detail) {
                    $article = Article::find($detail['article_id'], ['id', 'name', 'sale_unit_id']);

                    $voucher_detail = new VoucherDetail();
                    $voucher_detail->voucher_id = $voucher->id;
                    $voucher_detail->unit_id = $article->sale_unit_id;
                    $voucher_detail->name = $article->name;
                    $voucher_detail->quantity = $detail['quantity'];
                    $voucher_detail->original_price = round($detail['price_igv'], 4);
                    $voucher_detail->unit_price = round($detail['price_igv'] / $igv_percentage, 4);
                    $voucher_detail->sale_value = round($detail['price_igv'], 4);
                    $voucher_detail->exonerated_value = 0;
                    $voucher_detail->inaccurate_value = 0;
                    $voucher_detail->igv = round($detail['sale_value'], 4) - round($detail['sale_value'] / $igv_percentage, 4);
                    $voucher_detail->total = round($detail['sale_value'], 4);
                    $voucher_detail->user = Auth::user()->user;
                    $voucher_detail->article_id = $article->id;
                    $voucher_detail->save();

                    if ($detail['igv'] == 1) {
                        $taxed_operation += round($detail['sale_value'] / $igv_percentage, 4);
                        $igv += round($detail['sale_value'], 4) - round($detail['sale_value'] / $igv_percentage, 4);
                    } else {
                        $taxed_operation += round($detail['sale_value'], 4);
                    }
                }

                $voucher->taxed_operation = round($taxed_operation, 4);
                $voucher->unaffected_operation = 0;
                $voucher->exonerated_operation = 0;
                $voucher->igv = round($igv, 4);
                $voucher->save();

                $sale_model->referral_serie_number = $sale['referral_serie_number'];
                $sale_model->referral_voucher_number = $voucher->voucher_number;
            } else {
                $referral_serie_number = CarbonImmutable::now()->format('Ym');
                $last_voucher_number = Sale::where('company_id', $model['company_id'])
                    ->where('warehouse_document_type_id', $sale['warehouse_document_type_id'])
                    ->where('referral_serie_number', $referral_serie_number)
                    ->max('referral_voucher_number');

                if ($sale['warehouse_document_type_id'] == 4 || $sale['warehouse_document_type_id'] == 6 || $sale['warehouse_document_type_id'] == 8) {
                    $sale_model->referral_serie_number = $sale['referral_serie_number'];
                    $sale_model->referral_voucher_number = $sale['referral_voucher_number'];
                } else {
                    $int_last_voucher_number = (int)$last_voucher_number;
                    $sale_model->referral_serie_number = $referral_serie_number;
                    $sale_model->referral_voucher_number = $last_voucher_number != '' ? ++$int_last_voucher_number : 1;
                }
            }

            $sale_model->license_plate = $warehouse_movement->license_plate;


            $sale_value = 0;
            $igv = 0;
            foreach ($sale['details'] as $detail) {
                if ($detail['igv'] == 1) {
                    $sale_value += round($detail['sale_value'] / $igv_percentage, 4);
                    $igv += round($detail['sale_value'], 4) - round($detail['sale_value'] / $igv_percentage, 4);
                } else {
                    $sale_value += round($detail['sale_value'], 4);
                }
            }

            $total = $sale['total'];
            $total_perception = $sale['total_perception'];

            if ($sale['warehouse_document_type_id'] == 8 || $sale['warehouse_document_type_id'] == 9 || $sale['warehouse_document_type_id'] == 20 || $sale['warehouse_document_type_id'] == 22) {
                $sale_value = abs($sale_value);
                $igv = abs($igv);
                $total = abs($sale['total']);
                $total_perception = abs($sale['total_perception']);
            }



            if ($sale['payment_id'] == 2) {
                $balance = $total_perception;
                $pre_balance = $total_perception;
                $paid = 0;
            }

            $balance = 0;
            $pre_balance = 0;
            $paid = $total_perception;

            /*
			if (array_key_exists('liquidations', $sale)) {

				if (count($sale['liquidations']) > 0) {

					$contado = 0;
					$credito = 0;

					foreach ($sale['liquidations'] as $liquidation) {
						if ($liquidation['payment_id'] == 1 || $liquidation['payment_id'] == 3) { //contado - gasto
							$contado++;
						} else {
							$credito++;
						}
					}
					if ($sale['warehouse_document_type_id'] == 5) { // factura electronica
						if ($contado > 0) {
							$voucher->payment_id = 1;
						} else {
							$voucher->payment_id = 2;
						}
						$sale['payment_id'] = $voucher->payment_id;
						$sale_model->payment_id = $sale['payment_id'];

						$sale_model->save();
						$voucher->save();
					}
				}
			}
			*/

            if ($sale['warehouse_document_type_id'] == 5) {
                if ($sale['payment_id']  == 2) {
                    if ($model['company_id'] == 1) {

                        $voucher_type = VoucherType::find(14, ['id', 'serie_type', 'type']);

                        $con_voucher_number = Voucher::where('company_id', $model['company_id'])
                            ->where('voucher_type_id', 14)
                            ->max('voucher_number');

                        $voucher2 = new Voucher();
                        $voucher2->company_id = $model['company_id'];
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
                        $voucher2->currency_id = $sale['currency_id'];
                        $voucher2->payment_id = $sale['payment_id'];
                        $voucher2->taxed_operation = $sale_value;
                        $voucher2->unaffected_operation = 0;
                        $voucher2->exonerated_operation = 0;
                        $voucher2->igv = $igv;
                        $voucher2->total = $total;
                        $voucher2->referral_guide_series = ($sale['referral_guide_series'] ? $sale['referral_guide_series'] : $warehouse_movement->referral_guide_series);
                        $voucher2->referral_guide_number = ($sale['referral_guide_number'] ? $sale['referral_guide_number'] : $warehouse_movement->referral_guide_number);

                        $voucher2->igv_perception = $sale['perception_percentage'] / 100;
                        $voucher2->total_perception = $sale['total_perception'];
                        $voucher2->igv_percentage = $igv_percentage;
                        $voucher2->igv_perception_percentage = $sale['perception_percentage'];
                        $voucher2->ose = 0;

                        if ($sale['warehouse_document_type_id'] == 5) {
                            $voucher2->scop_number = $sale['scop_number'] ? $sale['scop_number'] : '';
                        }

                        $voucher2->user = Auth::user()->user;
                        $voucher2->save();
                    }
                }
            }
            if ($sale['warehouse_document_type_id'] == 1) {
                if ($sale['payment_id']  == 2) {
                    foreach ($sale['details'] as $detail) {
                        $article = Article::find($detail['article_id'], ['id', 'name', 'sale_unit_id']);

                        $voucherDetail2 = new VoucherDetail();
                        $voucherDetail2->voucher_id = $voucher2->id;
                        $voucherDetail2->unit_id = $article->sale_unit_id;
                        $voucherDetail2->name = $article->name;
                        $voucherDetail2->quantity = $detail['quantity'];
                        $voucherDetail2->original_price = round($detail['price_igv'], 4);
                        $voucherDetail2->unit_price = round($detail['price_igv'] / $igv_percentage, 4);
                        $voucherDetail2->sale_value = $voucherDetail2->original_price;
                        $voucherDetail2->exonerated_value = abs($detail['exonerated_value']);
                        $voucherDetail2->inaccurate_value = abs($detail['inaccurate_value']);
                        $voucherDetail2->igv = round($detail['sale_value'], 4) - round($detail['sale_value'] / $igv_percentage, 4);
                        $voucherDetail2->total =  round($detail['sale_value'], 4);
                        $voucherDetail2->user = Auth::user()->user;
                        $voucherDetail2->save();
                    }
                }
            }

            if ($sale['payment_id'] == 2) {
                $balance = $total_perception;
                $pre_balance = $total_perception;
                $paid = 0;
            }

            $sale_model->sale_value = $sale_value;
            $sale_model->exonerated_value = 0;
            $sale_model->inaccurate_value = 0;
            $sale_model->igv = $igv;
            $sale_model->total = $total;
            $sale_model->total_perception = $total_perception;
            $sale_model->balance = $balance;
            $sale_model->pre_balance = $pre_balance;
            $sale_model->paid = $paid;
            $sale_model->created_at_user = Auth::user()->user;
            $sale_model->updated_at_user = Auth::user()->user;
            $sale_model->pend = 0;
            $sale_model->save();

            if ($sale['warehouse_document_type_id'] >= 4 && $sale['warehouse_document_type_id'] <= 9) {
                $voucher->sale_id = $sale_model->id;
                $voucher->save();
            }

            if ($sale['warehouse_document_type_id'] == 5) {
                if ($sale['payment_id']  == 2) {
                    if ($model['company_id'] == 1) {
                        $voucher2->sale_id = $sale_model->id;
                        $voucher2->save();
                    }
                }
            }

            // if ( $client->payment_id == 2 ) {
            $client->credit_balance += $total_perception;
            $client->save();
            // }

            foreach ($sale['details'] as $index => $detail) {
                $article = Article::find($detail['article_id'], ['convertion']);

                $sale_detail = new SaleDetail();
                $sale_detail->sale_id = $sale_model->id;
                $sale_detail->item_number = ++$index;
                $sale_detail->article_id = $detail['article_id'];
                $sale_detail->quantity = $detail['quantity'];
                $sale_detail->price_igv = round($detail['price_igv'], 4);
                $sale_detail->sale_value = round($detail['sale_value'], 4);
                $sale_detail->inaccurate_value = 0;
                $sale_detail->exonerated_value = 0;
                $sale_detail->igv = round($detail['sale_value'], 4) - round($detail['sale_value'] / $igv_percentage, 4);
                $sale_detail->total = round($detail['sale_value'], 4);
                $sale_detail->total_perception = round($detail['total_perception'], 4);
                $sale_detail->igv_percentage = $rate->value;
                $sale_detail->igv_perception_percentage = $sale['perception_percentage'];
                $sale_detail->referential_convertion = $article->convertion;
                $sale_detail->kg = (float)$detail['quantity'] * (float)$article->convertion;
                $sale_detail->created_at_user = Auth::user()->user;
                $sale_detail->updated_at_user = Auth::user()->user;
                $sale_detail->save();
            }

            if (array_key_exists('liquidations', $sale)) {

                if (count($sale['liquidations']) > 0) {
                    foreach ($sale['liquidations'] as $liquidation) {
                        $liquidation_model = new Liquidation();
                        $liquidation_model->sale_id = $sale_model->id;
                        $liquidation_model->company_id = $model['company_id'];
                        $liquidation_model->payment_method_id = $liquidation['payment_method'];
                        $liquidation_model->currency_id = $liquidation['currency'];
                        $liquidation_model->exchange_rate = $liquidation['exchange_rate'];

                        if ($liquidation['payment_method'] == 2) {
                            $deposit_id = $liquidation['deposit_id'];
                            $deposit = Deposit::find($deposit_id, ['id', 'bank_account_id', 'operation_number', 'total_pend']);
                            $deposit->total_pend -= round($liquidation['amount'], 4);
                            $deposit->save();

                            $liquidation_model->bank_account_id = $deposit->bank_account_id;
                            $liquidation_model->operation_number = $deposit->operation_number;
                        } else {
                            $liquidation_model->bank_account_id = $liquidation['bank_account'];
                            $liquidation_model->operation_number = $liquidation['operation_number'];
                        }

                        $liquidation_model->deposit_id = $liquidation['deposit_id'];
                        $liquidation_model->amount = round($liquidation['amount'], 4);
                        $liquidation_model->created_at_user = Auth::user()->user;
                        $liquidation_model->updated_at_user = Auth::user()->user;
                        $liquidation_model->save();

                        if ($liquidation['payment_method'] == 7) { // DEPOSITO PENDIENTE
                            $sale_model->pend = $sale_model->pend + round($liquidation['amount'], 4);
                        }

                        if ($liquidation['payment_method'] == 7 || $liquidation['payment_method'] == 5) { // APLICACION SALDO A FAVOR
                            $sale_model->balance = $sale_model->balance + round($liquidation['amount'], 4);
                        }

                        //	if ($liquidation['payment_id'] == 1) {

                        if ($liquidation['payment_method'] == 1) {

                            $sale_model->payment_method_efective = 1;
                            $sale_model->efective += round($liquidation['amount'], 4);
                        } elseif ($liquidation['payment_method'] == 2) {

                            $sale_model->payment_method_deposit = 4;
                            $sale_model->deposit += round($liquidation['amount'], 4);
                        }

                        $sale_model->save();
                        //		}

                        if ($sale['payment_id'] == 2) {
                            $sale_model->balance -= $liquidation['amount'];
                            $sale_model->pre_balance -= $liquidation['amount'];
                            $sale_model->paid += $liquidation['amount'];
                            $sale_model->payment_method_credit = 3;
                            $sale_model->save();

                            DB::table('credits')->insert([
                                'sale_id' => $sale_model->id,
                                'company_id' => $model['company_id'],
                                'client_id' => $sale['client_id'],
                                'currency_id' =>  $liquidation['currency'],
                                'amount' => round($sale_model['pre_balance'], 4),
                                'created_at_user' => auth()->user()->name,
                                'created_at' => Carbon::now(),
                            ]);
                        }

                        $client->credit_balance -= $liquidation['amount'];
                        $client->save();

                        if ($liquidation['payment_method'] == 10) {

                            $discount = new Discount();
                            $discount->employee_id = $liquidation['employee_id'];
                            $discount->sale_id = $sale_model->id;
                            $discount->sale_date = $sale_date;
                            $discount->total = $liquidation['amount'];
                            $discount->paid = 0;
                            $discount->balance =  $liquidation['amount'];
                            $discount->save();
                        }
                    }
                }
            }
        }

        if ($boleteo != []) {
            DB::beginTransaction();

            try {
                $company_id = 1;
                $voucher_type_id = 2;

                $date = Carbon::now()->format('Y-m-d');

                $voucher_data = [];
                $voucher_detail_data = [];

                $series = collect(['B022' => 0, 'B038' => 0])->mapWithKeys(function ($item, $key) {
                    return [$key => 0];
                });

                $max_vouchers = Voucher::where('company_id', $company_id)
                    ->where('voucher_type_id', $voucher_type_id)
                    ->whereBetween('serie_number', ['B021', 'B042'])
                    ->select('serie_number', DB::raw('MAX(voucher_number) as max_number'))
                    ->groupBy('serie_number')
                    ->pluck('max_number', 'serie_number');


                $max_vouchers = collect($series)->merge($max_vouchers);


                foreach ($boleteo as $bol) {

                    $article = Article::find($bol['article_id']);

                    if ($article->serie_boleteo != 'B034' && $article->serie_boleteo != 'B026') {
                        $price = ContPrice::where('initial_date', '<=', $date)
                            ->where('final_date', '>=', $date)
                            ->where('article_id', $bol['article_id'])
                            ->where('state', 1)
                            ->value('price') ?? 1;

                        if ($article->presentation == 10) {
                            $serie = $article ? $article->serie_boleteo : 'B100';
                            if (empty($serie)) {
                                continue;
                            }

                            if (!$max_vouchers->has($serie)) {
                                continue;
                            }
                            $max_voucher = $max_vouchers[$serie] ?? 0;

                            $voucher_number = ++$max_voucher;
                            $max_vouchers[$serie] = $voucher_number;

                            $voucher_data[] = [
                                'company_id' => $company_id,
                                'client_id' => 1541,
                                'original_client_id' => 1541,
                                'client_name' => 'CLIENTE VARIOS',
                                'client_address' => 'SMP',
                                'voucher_type_id' => $voucher_type_id,
                                'serie_number' => $serie,
                                'voucher_number' => $voucher_number,
                                'issue_date' => now()->format('Y-m-d'),
                                'currency_id' => 1,
                                'payment_id' => 1,
                                'ose' => 0,
                                'igv_percentage' => 18.00,
                                'total_perception' => $price * $bol['quantity'],
                                'total' => $price * $bol['quantity'],
                                'taxed_operation' => ($price * $bol['quantity']) / 1.18,
                                'igv' => ($price * $bol['quantity']) - (($price * $bol['quantity']) / 1.18),
                                'user' => Auth::user()->user,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];

                            $voucher_detail_data[] = [
                                'voucher_id' => null,
                                'unit_id' => $article->sale_unit_id,
                                'name' => $article->name,
                                'quantity' => $bol['quantity'],
                                'original_price' => $price,
                                'unit_price' => $price / 1.18,
                                'sale_value' => $price,
                                'total' => $price * $bol['quantity'],
                                'igv' => ($price * $bol['quantity']) - (($price * $bol['quantity']) / 1.18),
                                'user' => Auth::user()->user,
                                'article_id' => $bol['article_id'],
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    }
                }

                Voucher::insert($voucher_data);
                $insertedCount = count($voucher_data);


                $voucher_ids = DB::table('vouchers')
                    ->whereBetween('serie_number', ['B021', 'B042'])
                    ->where('voucher_type_id', 2)
                    ->orderBy('id', 'desc')
                    ->limit($insertedCount)
                    ->pluck('id')
                    ->toArray();
                $voucher_ids = array_reverse($voucher_ids);

                foreach ($voucher_detail_data as $key => $detail) {
                    if (isset($voucher_ids[$key])) {
                        $voucher_detail_data[$key]['voucher_id'] = $voucher_ids[$key];
                    }
                }

                VoucherDetail::insert($voucher_detail_data);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        }

        $warehouse_movement->state = 1;
        $warehouse_movement->save();

        // return request()->all();
    }

    /*	public function getOperationNumber() {
		if (request('payment_method') == '2') {
			$count = Liquidation::where('bank_account_id', request('bank_account'))
								->where('operation_number', request('operation_number'))
								->count();

			if ($count > 0) {
				return response()->json([], 422);
			}
		}

		return response()->json([], 200);
	}*/
}
