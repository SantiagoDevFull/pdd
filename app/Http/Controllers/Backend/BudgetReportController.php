<?php

namespace App\Http\Controllers\Backend;

use App\Client;
use App\Company;
use App\ConsReport;
use App\Employee;
use App\Exports\GlpFactReportExport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\MoventClass;
use App\MoventType;
use App\Provider;
use App\WarehouseMovement;
use App\WarehouseMovementDetail;
use App\WarehouseType;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use phpDocumentor\Reflection\Element;
use stdClass;

class BudgetReportController extends Controller
{
    public function index()
    {

        $current_date = date(DATE_ATOM, mktime(0, 0, 0));
        return view('backend.budget_report')->with(compact('current_date'));

        /*$companies = Company::select('id', 'name')->get();
        return view('backend.budget_report')->with(compact('companies'));*/
    }

    public function validateForm()
    {
        $messages = [
            'initial_date.required'    => 'Debe seleccionar una Fecha.',
        ];

        $rules = [
            'initial_date'    => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function getConsReports()
    {
        $company_id = request('company_id');
        $q = request('q');

        $guides = warehousemovement::select('id', 'referral_voucher_number as text')
            ->when($company_id, function ($query, $company_id) {
                return $query->where('company_id', $company_id);
            })
            ->where('referral_voucher_number', 'like', '%' . $q . '%')
            ->withTrashed()
            ->get();

        return $guides;
    }

    public function list()
    {
        $export = request('export');

        $initial_date = CarbonImmutable::createFromDate(request('model.initial_date'))->startOfDay()->format('Y-m-d');
        $mes_date = CarbonImmutable::createFromDate(request('model.initial_date'))->startOfDay()->format('Y-m-01');
        $price_mes = CarbonImmutable::createFromDate(request('model.initial_date'))->startOfDay()->format('m');
        $price_year = CarbonImmutable::createFromDate(request('model.initial_date'))->startOfDay()->format('Y');

        $referral_serie_number = request('');
        $referral_voucher_number = request('');
        $company_id = request('1');

        $result = ConsReport::select(
            'cons_reports.udid as udid',
            'cons_reports.año as year',
            'cons_reports.mes as month',
            'cons_reports.channel_name as client_channel_name',
            'cons_reports.name as name',
            'cons_reports.business_id as business_id',
            'cons_reports.sector_id as sector_id',
            'cons_reports.channel_id as channel_id',
            'cons_reports.manager_id as manager_id',
            'cons_reports.route_id as route_id',
            'cons_reports.zone_id as zone_id',
            'cons_reports.soles as last_total',
            'cons_reports.tm as last_tm',
            'cons_reports.tm_ppto as tm_ppto',
            'cons_reports.mb_ppto as mb_ppto',
            'cons_reports.mb as last_mb',
            'cons_reports.dias as dias_mes',
            'cons_reports.unit as unit',
            'cons_reports.sale_option as option',
            'cons_reports.orden as orden'
        )->where('cons_reports.mes', '=', $price_mes)
            ->where('cons_reports.año', '=', $price_year)

            ->orderBy('cons_reports.sale_option')
            ->orderBy('cons_reports.orden')
            ->get();

        if ($export) {
            $data = new GlpFactReportExport($result);
            $file = Excel::download($data, 'reporte-salidas-de-mercaderia-' . time() . '.xls');

            return $file;
        } else {
            /*if (Auth::user()->user == 'comercial4' || Auth::user()->user == 'admin' || Auth::user()->user == 'sistemas1') {
                $detail->state = $item->state;
            } else {
                $detail->state = 1;
            }*/
            return $result;
        }
    }

    public function detail()
    {
        //$id = request('id');
        $udid = request("udid");
        $mes = request("mes");
        $año = request("año");

        $element = ConsReport::select("*")
            ->where("udid", "=", $udid)
            ->where("mes", "=", $mes)
            ->where("año", "=", $año)
            ->first();

        return $element;
    }

    public function update()
    {
        $udid = request('udid');
        $mes = request('mes');
        $año = request('año');
        $tm_ppto = request('tm_ppto');
        $mb_ppto = request('mb_ppto');

        $element = DB::table("cons_reports")
            ->where("udid", "=", $udid)
            ->where("mes", "=", $mes)
            ->where("año", "=", $año)
            ->update(["tm_ppto" => $tm_ppto, "mb_ppto" => $mb_ppto]);

        $data = new stdClass();
        $data->type = 1;
        $data->title = '¡Ok!';
        $data->msg = 'Registro actualizado exitosamente.';

        return response()->json($data);
    }
}
