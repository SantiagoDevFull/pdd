<?php

namespace App\Http\Controllers\Backend;

use App\Exports\SaleMarginReportMultiSheetExport;
use App\Http\Controllers\Controller;
use App\SaleDetail;
use App\WarehouseMovement;
use App\WarehouseType;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class SaleMarginReportController extends Controller
{
    public function index()
    {
        $current_date = date(DATE_ATOM, mktime(0, 0, 0));
        return view('backend.sale_margin')->with(compact('current_date'));
    }

    public function validateForm()
    {
        $messages = [
            'final_date.required'    => 'Debe seleccionar una Fecha.',

            'dias_totales.required'            => 'Debe ingresar un día.',
            'dias_totales.numeric'                => 'El día debe ser mayor a 0.',
            'dias_totales.min'                    => 'El día debe ser mayor a 0.',
            'dias_totales.not_in'                => 'El día debe ser mayor a 0.',

            'dias_avance.required'            => 'Debe ingresar un día.',
            'dias_avance.numeric'                => 'El día debe ser mayor a 0.',
            'dias_avance.min'                    => 'El dia debe ser mayor a 0.',
            'dias_avance.not_in'                => 'El día debe ser mayor a 0.',
        ];

        $rules = [
            'final_date'    => 'required',
            'dias_totales'                => 'required|numeric|min:0|not_in:0',
            'dias_avance'                => 'required|numeric|min:0|not_in:0',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }


    public function export()
    {

        $initial_date = CarbonImmutable::createFromDate(request('model.final_date'))->startOfDay()->format('Y-m-01');
        $final_date = CarbonImmutable::createFromDate(request('model.final_date'))->startOfDay()->format('Y-m-d');

        $dias_totales = request('model.dias_totales');
        $dias_avance = request('model.dias_avance');
        $quedan  = request('model.quedan');


        $fecha = CarbonImmutable::parse(request('model.final_date'))->locale('es');
        $mesAnio = mb_strtoupper($fecha->translatedFormat('F Y'));

        $months = [
            mb_strtoupper($fecha->translatedFormat('F')),
            mb_strtoupper($fecha->subMonth()->translatedFormat('F')),
            mb_strtoupper($fecha->subMonths(2)->translatedFormat('F')),
            mb_strtoupper($fecha->subMonths(3)->translatedFormat('F')),
            mb_strtoupper($fecha->subMonths(4)->translatedFormat('F')),
        ];

        $base = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
            ->leftjoin('companies', 'sales.company_id', '=', 'companies.id')
            ->leftjoin('business_units', 'clients.business_unit_id', '=', 'business_units.id')
            ->leftjoin('client_sectors', 'clients.sector_id', '=', 'client_sectors.id')
            ->leftjoin('client_channels', 'clients.channel_id', '=', 'client_channels.id')
            ->leftjoin('client_routes', 'clients.route_id', '=', 'client_routes.id')
            ->where('sales.sale_date', '>=', $initial_date)
            ->where('sales.sale_date', '<=', $final_date)
            ->whereIn('sales.warehouse_document_type_id', [13, 5, 6, 7, 4])
            ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
            ->select(

                'companies.short_name as companies_short_name',
                'sales.sale_date as sales_sale_date',
                'business_units.name as business_units_name',
                'client_channels.name as client_channels_name',
                'client_routes.short_name as client_routes_short_name',
                DB::Raw('SUM(sale_details.kg / 1000) as tm'),
                'sale_details.price_igv as precio',
                DB::Raw('SUM(sale_details.total)  as total')
            )
            ->groupBy(
                'companies.short_name',
                'sales.sale_date',
                'business_units.name',
                'client_channels.name',
                'sale_details.price_igv',
                'client_routes.short_name'
            )
            ->orderBy('sales.sale_date', 'asc')
            ->orderBy('business_units.name', 'asc')
            ->orderBy('client_channels.name', 'asc')
            ->orderBy('client_routes.short_name', 'asc')
            ->get();

        $base = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
            ->leftjoin('companies', 'sales.company_id', '=', 'companies.id')
            ->leftjoin('business_units', 'clients.business_unit_id', '=', 'business_units.id')
            ->leftjoin('client_sectors', 'clients.sector_id', '=', 'client_sectors.id')
            ->leftjoin('client_channels', 'clients.channel_id', '=', 'client_channels.id')
            ->leftjoin('client_routes', 'clients.route_id', '=', 'client_routes.id')
            ->where('sales.sale_date', '>=', $initial_date)
            ->where('sales.sale_date', '<=', $final_date)
            ->whereIn('sales.warehouse_document_type_id', [13, 5, 6, 7, 4])
            ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
            ->select(

                'companies.short_name as companies_short_name',
                'sales.sale_date as sales_sale_date',
                'business_units.name as business_units_name',
                'client_channels.name as client_channels_name',
                'client_routes.short_name as client_routes_short_name',
                DB::Raw('SUM(sale_details.kg / 1000) as tm'),
                'sale_details.price_igv as precio',
                DB::Raw('SUM(sale_details.total)  as total')
            )
            ->groupBy(
                'companies.short_name',
                'sales.sale_date',
                'business_units.name',
                'client_channels.name',
                'sale_details.price_igv',
                'client_routes.short_name'
            )
            ->orderBy('sales.sale_date', 'asc')
            ->orderBy('business_units.name', 'asc')
            ->orderBy('client_channels.name', 'asc')
            ->orderBy('client_routes.short_name', 'asc')
            ->get();


        //HOJA 2

        $total_venta_tm = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
            ->leftjoin('business_units', 'clients.business_unit_id', '=', 'business_units.id')
            ->leftjoin('client_sectors', 'clients.sector_id', '=', 'client_sectors.id')
            ->leftjoin('client_channels', 'clients.channel_id', '=', 'client_channels.id')
            ->where('sales.sale_date', '>=', $initial_date)
            ->where('sales.sale_date', '<=', $final_date)
            ->whereIn('sales.warehouse_document_type_id', [13, 5, 6, 7, 4])
            ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
            ->select(
                'sales.sale_date as sales_sale_date',
                'client_channels.name as client_channels_name',
                'clients.business_unit_id as business_unit_id',
                DB::Raw('SUM(sale_details.kg / 1000) as tm')
            )
            ->groupBy(
                'sales.sale_date',
                'client_channels.name',
                'clients.business_unit_id'
            )
            ->orderBy('sales.sale_date', 'asc')
            ->orderBy('client_channels.name', 'asc')
            ->get();

        $total_venta_soles = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
            ->leftjoin('business_units', 'clients.business_unit_id', '=', 'business_units.id')
            ->leftjoin('client_sectors', 'clients.sector_id', '=', 'client_sectors.id')
            ->leftjoin('client_channels', 'clients.channel_id', '=', 'client_channels.id')
            ->where('sales.sale_date', '>=', $initial_date)
            ->where('sales.sale_date', '<=', $final_date)
            ->whereIn('sales.warehouse_document_type_id', [13, 5, 6, 7, 4])
            ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
            ->select(
                'sales.sale_date as sales_sale_date',
                'client_channels.name as client_channels_name',
                'clients.business_unit_id as business_unit_id',
                DB::Raw('SUM(sale_details.total) as total')
            )
            ->groupBy(
                'sales.sale_date',
                'client_channels.name',
                'clients.business_unit_id'
            )
            ->orderBy('sales.sale_date', 'asc')
            ->orderBy('client_channels.name', 'asc')
            ->get();


        $precios = SaleDetail::leftJoin('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->leftJoin('clients', 'sales.client_id', '=', 'clients.id')
            ->leftJoin('business_units', 'clients.business_unit_id', '=', 'business_units.id')
            ->leftJoin('client_sectors', 'clients.sector_id', '=', 'client_sectors.id')
            ->leftJoin('client_channels', 'clients.channel_id', '=', 'client_channels.id')
            ->whereBetween('sales.sale_date', [$initial_date, $final_date])
            ->whereIn('sales.warehouse_document_type_id', [13, 5, 6, 7, 4])
            ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
            ->whereIn('clients.route_id', [19, 26])
            ->select(
                'clients.route_id',
                DB::raw('SUM(sale_details.total) / NULLIF(SUM(sale_details.kg / 1000), 0) as precio_promedio')
            )
            ->groupBy('clients.route_id')
            ->get();

        $initial_date_time = Carbon::parse($initial_date)->startOfDay()->format('Y-m-d H:i:s');
        $final_date_time = Carbon::parse($final_date)->endOfDay()->format('Y-m-d H:i:s');

        $kg_soles = WarehouseMovement::join('warehouse_movement_details',  'warehouse_movements.id', '=', 'warehouse_movement_details.warehouse_movement_id')
            ->whereIn('warehouse_type_id', [8, 9, 10, 11, 20, 24])
            ->where('movement_class_id', 1)
            ->whereIn('movement_type_id', [1])
            ->whereBetween('warehouse_movements.created_at', [$initial_date_time, $final_date_time])
            ->select(
                DB::raw('SUM(warehouse_movements.total * warehouse_movements.tc) / SUM(warehouse_movement_details.converted_amount) * 1000 as kg_soles')
            )
            ->first();

        $tm_ruta = SaleDetail::leftJoin('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->leftJoin('clients', 'sales.client_id', '=', 'clients.id')
            ->leftJoin('business_units', 'clients.business_unit_id', '=', 'business_units.id')
            ->leftJoin('client_sectors', 'clients.sector_id', '=', 'client_sectors.id')
            ->leftJoin('client_channels', 'clients.channel_id', '=', 'client_channels.id')
            ->whereBetween('sales.sale_date', [$initial_date, $final_date])
            ->whereIn('sales.warehouse_document_type_id', [13, 5, 6, 7, 4])
            ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
            ->whereIn('clients.route_id', [19, 26])
            ->select(
                'clients.route_id',
                DB::raw('SUM(sale_details.kg / 1000) as tm_ruta')
            )
            ->groupBy('clients.route_id')
            ->get();

        $compra_glp = WarehouseMovement::join(
            'warehouse_movement_details',
            'warehouse_movements.id',
            '=',
            'warehouse_movement_details.warehouse_movement_id'
        )
            ->rightJoin('warehouse_types', 'warehouse_movements.warehouse_type_id', '=', 'warehouse_types.id')
            ->whereIn('warehouse_type_id', [8, 9, 10, 11, 20, 24])
            ->where('movement_class_id', 1)
            ->whereIn('movement_type_id', [1])
            ->whereBetween('warehouse_movements.created_at', [$initial_date_time, $final_date_time])
            ->select(
                DB::raw('DATE(warehouse_movements.created_at) as fecha'),
                'warehouse_types.name as warehouse_type_name',
                DB::raw('SUM(warehouse_movement_details.converted_amount) / 1000 as total_toneladas')
            )
            ->groupBy('warehouse_types.name', 'fecha')
            ->get();

        $periodo = CarbonPeriod::create(
            $initial_date_time,
            $final_date_time
        );

        $warehouseTypes = WarehouseType::whereIn('id', [8, 9, 10, 11, 20, 24])
            ->pluck('name');

        $resultado = collect();

        foreach ($periodo as $fecha) {

            $dia = $fecha->format('Y-m-d');

            foreach ($warehouseTypes as $warehouseType) {

                $registro = $compra_glp->first(function ($item) use ($dia, $warehouseType) {
                    return $item->fecha == $dia
                        && $item->warehouse_type_name == $warehouseType;
                });

                $resultado->push([
                    'fecha' => $dia,
                    'warehouse_type_name' => $warehouseType,
                    'total_toneladas' => $registro->total_toneladas ?? 0,
                ]);
            }
        }

        $excel = new SaleMarginReportMultiSheetExport(
            $base,
            $total_venta_tm,
            $total_venta_soles,
            $precios,
            $kg_soles,
            $tm_ruta,
            $resultado,
            $dias_totales,
            $dias_avance,
            $quedan,
            $mesAnio,
            $months
        );
        return Excel::download($excel, 'reporte-venta-margen.xlsx');
    }
}
