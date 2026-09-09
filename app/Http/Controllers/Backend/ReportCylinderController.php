<?php

namespace App\Http\Controllers\Backend;

use App\Exports\CylinderReportMultiSheetExport;
use App\Http\Controllers\Controller;
use App\WarehouseMovementDetail;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportCylinderController extends Controller
{
    public function index()
    {

        $current_date = date(DATE_ATOM, mktime(0, 0, 0));
        return view('backend.cylinder_report')->with(compact('current_date'));
    }

    public function validateForm()
    {
        $messages = [
            'initial_date.required'  => 'Debe seleccionar una Fecha inicial.',
            'final_date.required'    => 'Debe seleccionar una Fecha final.',
        ];

        $rules = [
            'initial_date'  => 'required',
            'final_date'    => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function list()
    {

        $initial_date = date_create(request('model.initial_date') . ' 00:00:00');
        $final_date = date_create(request('model.final_date') . ' 23:59:59');
        $initial_date = date_format($initial_date, 'Y-m-d H:i:s');
        $final_date = date_format($final_date, 'Y-m-d H:i:s');

        $elements = WarehouseMovementDetail::leftjoin('warehouse_movements', 'warehouse_movements.id', '=', 'warehouse_movement_details.warehouse_movement_id')
            ->leftjoin('articles', 'warehouse_movement_details.article_code', '=', 'articles.id')
            ->leftjoin('companies', 'companies.id', '=', 'warehouse_movements.company_id')
            ->leftjoin('client_routes', 'client_routes.id', '=', 'warehouse_movements.route_id')
            ->leftjoin('movent_types', 'movent_types.id', '=', 'warehouse_movements.movement_type_id')
            ->select(
                'companies.short_name as company_short_name',
                DB::raw('DATE_FORMAT(warehouse_movements.created_at, "%Y-%m-%d") as created_at_date'),
                DB::raw('DATE_FORMAT(warehouse_movements.traslate_date, "%Y-%m-%d") as traslate_at_date'),
                'movent_types.name as movement_type_name',
                'warehouse_movements.referral_guide_series as referral_guide_series',
                'warehouse_movements.referral_guide_number as referral_guide_number',
                'client_routes.short_name as route_short_name',
                'articles.convertion as convertion',
                'articles.name as article_name',
                'warehouse_movement_details.converted_amount as converted_amount',
                'warehouse_movement_details.new_stock_return as new_stock_return',
                'warehouse_movements.scop_number as scop_number',
                'warehouse_movements.license_plate as license_plate',
                'warehouse_movements.movement_type_id as movement_type_id'
            )
            ->whereBetween('warehouse_movements.created_at', [$initial_date, $final_date])
            ->whereIn('warehouse_movements.movement_type_id', [11, 12, 29])
            ->whereIn('warehouse_movements.company_id', [1, 2])
            ->whereNotIn('client_routes.id', [19, 26])
            ->where('warehouse_movements.referral_guide_series', '!=', 5)
            ->orderBy('warehouse_movements.created_at', 'asc')
            ->get();


        $elements_2 = WarehouseMovementDetail::leftJoin('warehouse_movements', 'warehouse_movements.id', '=', 'warehouse_movement_details.warehouse_movement_id')
            ->leftJoin('articles', 'warehouse_movement_details.article_code', '=', 'articles.id')
            ->leftJoin('companies', 'companies.id', '=', 'warehouse_movements.company_id')
            ->leftJoin('client_routes', 'client_routes.id', '=', 'warehouse_movements.route_id')
            ->leftJoin('movent_types', 'movent_types.id', '=', 'warehouse_movements.movement_type_id')
            ->select(
                'movent_types.name as movement_type_name',
                DB::raw('DATE_FORMAT(warehouse_movements.created_at, "%Y-%m-%d") as created_at_date'),
                DB::raw('DATE_FORMAT(warehouse_movements.traslate_date, "%Y-%m-%d") as traslate_at_date'),
                'warehouse_movements.referral_guide_series as referral_guide_series',
                'warehouse_movements.referral_guide_number as referral_guide_number',
                'client_routes.short_name as route_short_name',
                'warehouse_movements.scop_number as scop_number',
                'warehouse_movements.license_plate as license_plate',

                // Sumas condicionadas por articles.convertion
                DB::raw('SUM(CASE WHEN articles.convertion = 5 THEN warehouse_movement_details.converted_amount ELSE 0 END) as conv_5_sum'),
                DB::raw('SUM(CASE WHEN articles.convertion = 10 THEN warehouse_movement_details.converted_amount ELSE 0 END) as conv_10_sum'),
                DB::raw('SUM(CASE WHEN articles.convertion = 15 THEN warehouse_movement_details.converted_amount ELSE 0 END) as conv_15_sum'),
                DB::raw('SUM(CASE WHEN articles.convertion = 45 THEN warehouse_movement_details.converted_amount ELSE 0 END) as conv_45_sum'),

                // Sumas condicionadas para new_stock_return (retornos)
                DB::raw('SUM(CASE WHEN articles.convertion = 5 THEN warehouse_movement_details.new_stock_return ELSE 0 END) as ret_5_sum'),
                DB::raw('SUM(CASE WHEN articles.convertion = 10 THEN warehouse_movement_details.new_stock_return ELSE 0 END) as ret_10_sum'),
                DB::raw('SUM(CASE WHEN articles.convertion = 15 THEN warehouse_movement_details.new_stock_return ELSE 0 END) as ret_15_sum'),
                DB::raw('SUM(CASE WHEN articles.convertion = 45 THEN warehouse_movement_details.new_stock_return ELSE 0 END) as ret_45_sum')
            )
            ->whereBetween('warehouse_movements.created_at', [$initial_date, $final_date])
            ->whereIn('warehouse_movements.movement_type_id', [11, 12])
            ->whereIn('warehouse_movements.company_id', [1, 2])
            ->whereNotIn('client_routes.id', [19, 26])
            ->where('warehouse_movements.referral_guide_series', '!=', 5)
            ->groupBy(
                'warehouse_movements.id',
                'movent_types.name',
                DB::raw('DATE_FORMAT(warehouse_movements.created_at, "%Y-%m-%d")'),
                DB::raw('DATE_FORMAT(warehouse_movements.traslate_date, "%Y-%m-%d")'),
                'warehouse_movements.referral_guide_series',
                'warehouse_movements.referral_guide_number',
                'client_routes.short_name',
                'warehouse_movements.scop_number',
                'warehouse_movements.license_plate'
            )
            ->orderBy('warehouse_movements.referral_guide_number', 'asc')
            ->get();

        // 1) DESPACHOS por created_at (converted_amount)
        $despachos = WarehouseMovementDetail::leftJoin('warehouse_movements', 'warehouse_movements.id', '=', 'warehouse_movement_details.warehouse_movement_id')
            ->leftJoin('articles', 'warehouse_movement_details.article_code', '=', 'articles.id')
            ->leftJoin('client_routes', 'client_routes.id', '=', 'warehouse_movements.route_id')
            ->select(
                DB::raw('DATE_FORMAT(warehouse_movements.created_at, "%Y-%m-%d") as date'),
                DB::raw('SUM(CASE WHEN articles.convertion = 5 THEN warehouse_movement_details.converted_amount ELSE 0 END) as conv_5_sum'),
                DB::raw('SUM(CASE WHEN articles.convertion = 10 THEN warehouse_movement_details.converted_amount ELSE 0 END) as conv_10_sum'),
                DB::raw('SUM(CASE WHEN articles.convertion = 15 THEN warehouse_movement_details.converted_amount ELSE 0 END) as conv_15_sum'),
                DB::raw('SUM(CASE WHEN articles.convertion = 45 THEN warehouse_movement_details.converted_amount ELSE 0 END) as conv_45_sum')
            )
            ->whereBetween('warehouse_movements.created_at', [$initial_date . ' 00:00:00', $final_date . ' 23:59:59'])
            ->whereIn('warehouse_movements.movement_type_id', [11, 12])
            ->whereIn('warehouse_movements.company_id', [1, 2])
            ->whereNotIn('client_routes.id', [19, 26])
            ->where('warehouse_movements.referral_guide_series', '!=', 5)
            ->groupBy(DB::raw('DATE_FORMAT(warehouse_movements.created_at, "%Y-%m-%d")'))
            ->orderBy('date')
            ->get()
            ->keyBy('date'); // clave: 'YYYY-MM-DD'

        // 2) DEVOLUCIONES por traslate_at (new_stock_return)
        $devoluciones = WarehouseMovementDetail::leftJoin('warehouse_movements', 'warehouse_movements.id', '=', 'warehouse_movement_details.warehouse_movement_id')
            ->leftJoin('articles', 'warehouse_movement_details.article_code', '=', 'articles.id')
            ->leftJoin('client_routes', 'client_routes.id', '=', 'warehouse_movements.route_id')
            ->select(
                DB::raw('DATE_FORMAT(warehouse_movements.traslate_date, "%Y-%m-%d") as date'),
                DB::raw('SUM(CASE WHEN articles.convertion = 5 THEN warehouse_movement_details.new_stock_return ELSE 0 END) as ret_5_sum'),
                DB::raw('SUM(CASE WHEN articles.convertion = 10 THEN warehouse_movement_details.new_stock_return ELSE 0 END) as ret_10_sum'),
                DB::raw('SUM(CASE WHEN articles.convertion = 15 THEN warehouse_movement_details.new_stock_return ELSE 0 END) as ret_15_sum'),
                DB::raw('SUM(CASE WHEN articles.convertion = 45 THEN warehouse_movement_details.new_stock_return ELSE 0 END) as ret_45_sum')
            )
            ->whereNotNull('warehouse_movements.traslate_date')
            ->whereBetween('warehouse_movements.traslate_date', [$initial_date . ' 00:00:00', $final_date . ' 23:59:59'])
            ->whereIn('warehouse_movements.movement_type_id', [11, 12])
            ->whereIn('warehouse_movements.company_id', [1, 2])
            ->whereNotIn('client_routes.id', [19, 26])
            ->where('warehouse_movements.referral_guide_series', '!=', 5)
            ->groupBy(DB::raw('DATE_FORMAT(warehouse_movements.traslate_date, "%Y-%m-%d")'))
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $datetime = CarbonImmutable::now()->format('d/m/Y h:i:s a');


        $excel = new CylinderReportMultiSheetExport($datetime, $elements, $elements_2, $despachos, $devoluciones, $initial_date, $final_date);
        return Excel::download($excel, 'reporte-despacho-retorno.xlsx');
    }
}
