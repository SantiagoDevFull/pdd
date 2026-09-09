<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Client;
use App\Company;
use App\Exports\OtherCashReportExport;
use App\Sale;
use Maatwebsite\Excel\Facades\Excel;

class ReportOtherCashController extends Controller
{
    public function index()
    {
        $companies = Company::select('id', 'name')->whereIn('id', [1])->get();
        $current_date = date(DATE_ATOM, mktime(0, 0, 0));

        return view('backend.other_cash_report')->with(compact('companies', 'current_date'));
    }

    public function validateForm()
    {
        $messages = [
            'company_id.required'                 => 'Debe seleccionar una Compañía.',
            'since_date.required'                 => 'Debe seleccionar una Fecha de Inicio.',
            'to_date.required'                    => 'Debe seleccionar una Fecha de Fin.',
            'type.required'                       => 'Debe seleccionar un Tipo de reporte.'
        ];

        $rules = [
            'company_id'                  => 'required',
            'since_date'                  => 'required',
            'to_date'                     => 'required',
            'type'                        => 'required'
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function getAccounts()
    {
        $company_id = request('company_id');
        $q = request('q');

        $clients = Client::select('id', 'business_name')
            ->where('company_id', $company_id)
            ->where('business_name', 'like', '%' . $q . '%')
            ->get();

        $clients->map(function ($item, $index) {
            $item->text = $item->business_name;
            unset($item->business_name);

            return $item;
        });

        return $clients;
    }

    public function list()
    {

        $company_id = request('model.company_id');
        $initial_date = date('Y-m-d', strtotime(request('model.since_date'))); //desde
        $final_date = date('Y-m-d', strtotime(request('model.to_date'))); //hasta
        $client_id = request('model.warehouse_account_id');
        $type = request('model.type');

        if ($type == 1) {
            $collection = Sale::leftJoin('clients', 'clients.id', '=', 'sales.client_id')
                ->leftJoin('companies', 'companies.id', '=', 'sales.company_id')
                ->leftJoin('warehouse_document_types', 'warehouse_document_types.id', '=', 'sales.warehouse_document_type_id')
                ->leftJoin('client_routes', 'client_routes.id', '=', 'clients.route_id')
                ->leftJoin('document_types', 'document_types.id', '=', 'clients.document_type_id')
                ->leftJoin('sale_details', 'sale_details.sale_id', '=', 'sales.id')
                ->select(
                    'companies.short_name as company_short_name',
                    'sales.client_id as sale_client_id',
                    'document_types.name as document_type_name',
                    'client_routes.name as client_route_name',
                    'clients.document_number as client_document_number',
                    'clients.business_name as client_name',
                    'warehouse_document_types.name as warehouse_document_type_name',
                    'sales.sale_date as issue_date',
                    'sale_details.concept as concept',
                    'sales.total_perception as sale_total_perception',
                    'sales.referral_serie_number as referral_serie_number',
                    'sales.referral_voucher_number as referral_voucher_number',
                    'sale_details.quantity as quantity',
                    'sale_details.price_igv as price_igv',
                    'sale_details.total as subtotal',
                    'sale_details.item_number as item_number'
                )
                ->when($client_id, function ($query, $client_id) {
                    return $query->where('sales.client_id', $client_id);
                })
                ->where('sales.warehouse_document_type_id', 22)
                ->where('sales.company_id', $company_id)
                ->where('sales.sale_date', '>=', $initial_date)
                ->where('sales.sale_date', '<=', $final_date)
                ->get();
        } else {
            $collection = Sale::leftJoin('clients', 'clients.id', '=', 'sales.client_id')
                ->leftJoin('companies', 'companies.id', '=', 'sales.company_id')
                ->leftJoin('warehouse_document_types', 'warehouse_document_types.id', '=', 'sales.warehouse_document_type_id')
                ->leftJoin('client_routes', 'client_routes.id', '=', 'clients.route_id')
                ->leftJoin('document_types', 'document_types.id', '=', 'clients.document_type_id')
                ->leftJoin('liquidations', 'liquidations.sale_id', '=', 'sales.id')
                ->leftJoin('payment_methods', 'payment_methods.id', '=', 'liquidations.payment_method_id')
                ->leftJoin('payments', 'payments.id', '=', 'payment_methods.payment_id')
                ->leftJoin('currencies', 'currencies.id', '=', 'liquidations.currency_id')
                ->leftJoin('bank_accounts', 'bank_accounts.id', '=', 'liquidations.bank_account_id')
                ->leftJoin('banks', 'banks.id', '=', 'bank_accounts.bank_id')
                ->select(
                    'companies.short_name as company_short_name',
                    'sales.client_id as sale_client_id',
                    'document_types.name as document_type_name',
                    'client_routes.name as client_route_name',
                    'clients.document_number as client_document_number',
                    'clients.business_name as client_name',
                    'warehouse_document_types.name as warehouse_document_type_name',
                    'sales.sale_date as issue_date',
                    'sales.total_perception as sale_total_perception',
                    'payments.name as payment_name',
                    'payment_methods.name as payment_mathod_name',
                    'currencies.name as currency_name',
                    'banks.short_name as bank_short_name',
                    'sales.referral_serie_number as referral_serie_number',
                    'sales.referral_voucher_number as referral_voucher_number',
                    'bank_accounts.account_number as bank_account_number',
                    'liquidations.operation_number as liquidation_operation_number',
                    'liquidations.amount as liquidation_amount'
                )
                ->when($client_id, function ($query, $client_id) {
                    return $query->where('sales.client_id', $client_id);
                })
                ->where('sales.warehouse_document_type_id', 22)
                ->where('sales.company_id', $company_id)
                ->where('sales.sale_date', '>=', $initial_date)
                ->where('sales.sale_date', '<=', $final_date)
                ->get();
        }

        $data = new OtherCashReportExport($collection->toArray(), $type);
        $file = Excel::download($data, 'reporte-ingresos-caja.xls');

        return $file;
    }
}
