<?php

namespace App\Http\Controllers\Backend;

use App\Deposit;
use App\Http\Controllers\Controller;
use App\Liquidation;
use App\Sale;
use App\WarehouseMovement;
use Illuminate\Support\Facades\DB;
use PDF;
use QrCode;

class PreventaReportController extends Controller
{
    public function index()
    {
        return view('backend.report_preventa');
    }

    public function list()
    {
        $q = request('query');
        $search = $q['generalSearch'];

        $company_id = 1;
        $warehouse_type_id = 5;
        $movement_type_id = 11;


        $warehouse_movements = WarehouseMovement::leftjoin('companies', 'companies.id', 'warehouse_movements.company_id')
            ->leftjoin('movent_types', 'movent_types.id', 'warehouse_movements.movement_type_id')
            ->leftjoin('employees', 'employees.id', 'warehouse_movements.account_id')
            ->leftjoin('client_routes', 'client_routes.id', 'warehouse_movements.route_id')
            ->select(
                'warehouse_movements.id as id',
                'companies.name as company_name',
                'warehouse_movements.movement_number as movement_number',
                'movent_types.name as movement_type_name',
                'warehouse_movements.referral_serie_number as referral_serie_number',
                'warehouse_movements.referral_voucher_number as referral_voucher_number',
                DB::Raw('CONCAT(employees.first_name," ",employees.last_name) as chofer'),
                'client_routes.name as route_name'
            )
            ->when($search, function ($query, $search) {
                return $query->where('warehouse_movements.referral_serie_number', 'like', '%' . $search . '%')
                    ->orWhere('warehouse_movements.referral_voucher_number', 'like', '%' . $search . '%')
                    ->orWhere('warehouse_movements.movement_number', 'like', '%' . $search . '%');
            })
            ->where('warehouse_movements.company_id', $company_id)
            ->where('warehouse_movements.movement_type_id', $movement_type_id)
            ->where('warehouse_movements.warehouse_type_id', $warehouse_type_id)
            //->where('warehouse_movements.state', 0)
            ->where('warehouse_movements.movement_class_id', 2)
            ->orderBy('warehouse_movements.id', 'desc')
            ->get();


        return $warehouse_movements;
    }

    public function generarPdf()
    {

        $id = request('id');

        $warehouse_movement = WarehouseMovement::findOrFail($id);
        $total_efectivo = 0;
        $total_deposit = 0;

        $sales = Sale::leftJoin('liquidations', 'sales.id', '=', 'liquidations.sale_id')
            ->leftJoin('clients', 'sales.client_id', '=', 'clients.id')
            ->leftJoin('document_types', 'clients.document_type_id', '=', 'document_types.id')
            ->leftJoin('payments as payment_sale', 'sales.payment_id', '=', 'payment_sale.id')
            ->leftJoin('currencies', 'sales.currency_id', '=', 'currencies.id')
            ->leftJoin('warehouse_document_types', 'sales.warehouse_document_type_id', '=', 'warehouse_document_types.id')
            ->leftJoin('payment_methods', 'liquidations.payment_method_id', '=', 'payment_methods.id')
            ->leftJoin('bank_accounts', 'liquidations.bank_account_id', '=', 'bank_accounts.id')
            ->select(
                'sales.client_id as client_id',
                DB::raw('CONCAT(document_types.name, " ", clients.document_number, " - ", clients.business_name) as client_full_name'),
                'payment_sale.name as payment_name',
                'warehouse_document_types.short_name as warehouse_document_type_short_name',
                DB::raw('CONCAT(sales.guide_series, "-", sales.guide_number) as guia_electronica'),
                DB::raw("
                CASE 
                    WHEN warehouse_document_types.id = 5 
                    THEN CONCAT('F', sales.referral_serie_number, '-', sales.referral_voucher_number)
                    ELSE ''
                END AS comprobante
            "),
                'sales.total_perception as total_perception',

                DB::raw("ROUND(SUM(CASE WHEN payment_methods.id = 1 THEN liquidations.amount ELSE 0 END), 2) as total_efectivo"),
                DB::raw("ROUND(SUM(CASE WHEN payment_methods.id = 2 THEN liquidations.amount ELSE 0 END), 2) as total_deposito"),
                DB::raw("ROUND(SUM(CASE WHEN payment_methods.id = 10 THEN liquidations.amount ELSE 0 END), 2) as total_descuento"),
                DB::raw("ROUND(sales.pre_balance, 2) as total_credito")
            )
            ->where('sales.warehouse_movement_id', $warehouse_movement->id)
            ->groupBy(
                'sales.id',
                'document_types.name',
                'clients.id',
                'clients.document_number',
                'clients.business_name',
                'payment_sale.name',
                'warehouse_document_types.short_name',
                'sales.referral_serie_number',
                'sales.referral_voucher_number',
                'sales.total_perception'
            )
            ->get();

        $cobranzas = Liquidation::leftJoin('sales', 'liquidations.sale_id', '=', 'sales.id')
            ->leftJoin('clients', 'sales.client_id', '=', 'clients.id')
            ->leftJoin('warehouse_document_types', 'sales.warehouse_document_type_id', '=', 'warehouse_document_types.id')
            ->leftjoin('payment_methods', 'liquidations.payment_method_id', '=', 'payment_methods.id')
            ->leftjoin('bank_accounts', 'liquidations.bank_account_id', '=', 'bank_accounts.id')
            ->leftjoin('banks', 'bank_accounts.bank_id', '=', 'banks.id')
            ->select(
                'clients.id as client_id',
                'warehouse_document_types.name as warehouse_document_type_name',
                'clients.business_name as client_name',
                'sales.sale_date as sale_date',
                'sales.total_perception as total_perception',
                'payment_methods.name as payment_method_name',
                'liquidations.amount as liquidation_amount',
                'liquidations.operation_number as operation_number',
                'liquidations.payment_method_id as payment_method_id',
                DB::raw('CONCAT(banks.short_name," - ",bank_accounts.account_number) AS bank_account_name')
            )
            ->where('liquidations.guia_cobranza_id', $warehouse_movement->id)
            ->get();

        $deposits = Deposit::leftjoin('banks', 'deposits.bank_id', '=', 'banks.id')
            ->leftjoin('bank_accounts', 'deposits.bank_account_id', '=', 'bank_accounts.id')
            ->leftjoin('companies', 'deposits.company_id', '=', 'companies.id')

            ->select(
                'companies.name as company_name',
                'deposits.bank_date as bank_date',
                DB::raw('CONCAT(banks.short_name," - ",bank_accounts.account_number) AS bank_account_name'),
                'deposits.operation_number as operation_number',
                'deposits.total as total',
                'deposits.total_pend as total_pend',
                'deposits.paid as paid'
            )
            ->where('deposits.guia_id', $warehouse_movement->id)
            ->get();

        $totalVenta    = $sales->sum('total_perception');
        $totalEfectivo = $sales->sum('total_efectivo');
        $totalDeposito = $sales->sum('total_deposito');
        $totalDescuento = $sales->sum('total_descuento');
        $totalCredito  = $sales->sum('total_credito');

        $cobranza_efective = $cobranzas->where('payment_method_id', 1)->sum('liquidation_amount');
        $cobranza_deposit = $cobranzas->where('payment_method_id', 2)->sum('liquidation_amount');

        $total_efectivo = $totalEfectivo + $cobranza_efective;
        $total_deposit = $totalDeposito + $cobranza_deposit;

        $pdf = PDF::loadView('backend.pdf_preventa', compact('total_efectivo', 'total_deposit', 'warehouse_movement', 'sales', 'cobranzas', 'deposits', 'totalVenta', 'totalEfectivo', 'totalDeposito', 'totalDescuento', 'totalCredito'))->setPaper('a4', 'landscape');
        return $pdf->download('movimiento.pdf');
    }
}
