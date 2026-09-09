<?php

namespace App\Http\Controllers\Backend;

use App\Exports\SaleDayReportMultiSheetExport;
use App\Http\Controllers\Controller;
use App\SaleDetail;
use App\SalePresupuesto;
use App\WarehouseMovement;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class SaleDayReportController extends Controller
{
    public function index()
    {
        $current_date = date(DATE_ATOM, mktime(0, 0, 0));
        return view('backend.sale_day')->with(compact('current_date'));
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
        $year = CarbonImmutable::createFromDate(request('model.final_date'))->startOfDay()->format('Y');
        $dias_totales = request('model.dias_totales');
        $dias_avance = request('model.dias_avance');
        $quedan = request('model.quedan');

        $mes_actual = ucfirst(CarbonImmutable::parse($initial_date)->locale('es')->isoFormat('MMMM'));
        $fecha = CarbonImmutable::createFromDate(request('model.final_date'))->startOfDay();
        $mes_anterior = ucfirst($fecha->subMonthNoOverflow()->locale('es')->isoFormat('MMMM'));

        $detalle = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
            ->leftjoin('business_units', 'clients.business_unit_id', '=', 'business_units.id')
            ->leftjoin('companies', 'sales.company_id', '=', 'companies.id')
            ->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
            ->leftjoin('client_channels', 'clients.channel_id', '=', 'client_channels.id')
            ->leftjoin('client_zones', 'clients.zone_id', '=', 'client_zones.id')
            ->leftjoin('client_types', 'clients.business_type', '=', 'client_types.id')
            ->leftjoin('client_sectors', 'clients.sector_id', '=', 'client_sectors.id')
            ->leftjoin('client_routes', 'clients.route_id', '=', 'client_routes.id')
            ->leftjoin('managers', 'clients.manager_id', '=', 'managers.id')
            ->leftjoin('payments', 'payments.id', '=', 'sales.payment_id')
            ->leftjoin('client_addresses', function ($join) {
                $join->on('clients.id', '=', 'client_addresses.client_id')
                    ->where('client_addresses.address_type_id', 1);
            })
            ->leftjoin('ubigeos', 'client_addresses.ubigeo_id', '=', 'ubigeos.id')
            ->leftjoin('warehouse_document_types', 'sales.warehouse_document_type_id', '=', 'warehouse_document_types.id')
            ->leftjoin('warehouse_movements', 'sales.warehouse_movement_id', '=', 'warehouse_movements.id')
            ->leftjoin('movent_types', 'warehouse_movements.movement_type_id', '=', 'movent_types.id')
            ->where('sales.sale_date', '>=', $initial_date)
            ->where('sales.sale_date', '<=', $final_date)
            ->whereIn('sales.warehouse_document_type_id', [13, 5, 6, 7, 4])
            ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
            ->select(
                DB::raw('DATE_FORMAT(sales.sale_date, "%Y") as anio'),
                DB::raw('DATE_FORMAT(sales.sale_date, "%m") as mes'),
                'companies.short_name as company_short_name',
                'sales.sale_date as fecha_despacho',
                'business_units.name as business_unit_name',
                'client_sectors.name as client_sector_name',
                'client_channels.name as client_channel_name',
                'client_routes.short_name as client_route_id',
                'warehouse_document_types.short_name as warehouse_document_type_short_name',
                'sales.referral_serie_number',
                'sales.referral_voucher_number',
                'clients.id as client_id',
                'clients.business_name as client_name',
                'articles.name as article_name',
                DB::Raw('(sale_details.kg / 1000) as toneladas'),
                'sale_details.price_igv as price',
                'sale_details.total as total',
                'warehouse_movements.movement_number as movement_number',
                'movent_types.name as movement_type_name',
                DB::Raw('CONCAT(warehouse_movements.referral_guide_series, "-", warehouse_movements.referral_guide_number) as guia_fisica'),
                DB::Raw('CONCAT(warehouse_movements.referral_serie_number, "-", warehouse_movements.referral_voucher_number) as guia_electronica'),
                'sales.license_plate as license_plate_2',
                'client_types.name as client_type_name',
                'client_zones.name as client_zone_name',
                'clients.zona as client_zona',
                'clients.sector as client_sector',
                'ubigeos.district as district',
                'ubigeos.province as province',
                'ubigeos.department as department',
                'clients.seller_id as vendedor',
                'managers.name as supervisor',
                'clients.grupo as grupo',
                'clients.estado as estado',
                'clients.potencial as potencial',
                'payments.name as payment_name',
                DB::raw(" ( SELECT COALESCE(SUM(l.amount),0) FROM liquidations l WHERE l.sale_id = sales.id AND l.payment_method_id IN (8,9)) as bonificacion "),
                'articles.marca as marca',
                'articles.convertion as convertion'
            )
            ->groupBy('sale_details.id')
            ->orderBy('clients.business_unit_id')
            ->orderBy('sale_date')
            ->orderBy('business_unit_name')
            ->orderBy('warehouse_document_type_short_name')
            ->orderBy('sales.referral_serie_number')
            ->orderBy('sales.referral_voucher_number')
            ->get();


        $initialDate = Carbon::parse($final_date);
        $startPreviousMonth = $initialDate->copy()->startOfMonth()->subMonth();
        $endPreviousMonth = $startPreviousMonth->copy()->endOfMonth();
        $startCurrentMonth = $initialDate->copy()->startOfMonth();
        $endCurrentMonth = $initialDate->copy()->endOfDay();

        $analisis_costo = WarehouseMovement::join('warehouse_movement_details', 'warehouse_movement_details.warehouse_movement_id', 'warehouse_movements.id')
            ->join('companies', 'companies.id', 'warehouse_movements.company_id')
            ->join('warehouse_types', 'warehouse_types.id', 'warehouse_movements.warehouse_type_id')
            ->select(
                'warehouse_movements.id',
                'warehouse_movements.created_at',
                'warehouse_movement_details.digit1',
                'warehouse_movement_details.digit2',
                'warehouse_movements.soles_1',
                'warehouse_movements.soles_2',
                'warehouse_movements.created_at',
                'companies.short_name as company_name',
                DB::raw('DATE_FORMAT(warehouse_movements.created_at, "%Y-%m-%d") as fecha_emision'),
                DB::Raw('CONCAT(warehouse_movements.referral_serie_number, "-", warehouse_movements.referral_voucher_number) as factura'),
                'warehouse_types.name as warehouse_type_name',
                'warehouse_movements.origin',
                'warehouse_movements.referral_guide_number as guia',
                'warehouse_movements.license_plate as cisterna',
                'warehouse_movements.license_plate_2 as tracto',
                'warehouse_movements.cost_glp as cost_glp',
                'warehouse_types.short_name as carga_terminal',
                'warehouse_movements.scop_number as scop',
                'warehouse_movements.account_name as chofer'
            )
            ->whereBetween('warehouse_movements.created_at', [$startPreviousMonth, $endCurrentMonth])
            ->where(function ($query) use ($startPreviousMonth, $endPreviousMonth, $startCurrentMonth, $endCurrentMonth) {
                $query->where(function ($q) use ($startPreviousMonth, $endPreviousMonth) {
                    $q->whereBetween('warehouse_movements.created_at', [$startPreviousMonth, $endPreviousMonth])
                        ->where('warehouse_movement_details.digit2', '>', 0);
                })
                    ->orWhere(function ($q) use ($startCurrentMonth, $endCurrentMonth) {
                        $q->whereBetween('warehouse_movements.created_at', [$startCurrentMonth, $endCurrentMonth])
                            ->where('warehouse_movement_details.digit1', '>', 0);
                    });
            })
            ->orderBy('warehouse_movements.created_at', 'ASC')
            ->get()
            ->map(function ($item) use ($startCurrentMonth, $endCurrentMonth) {
                if ($item->created_at >= $startCurrentMonth && $item->created_at <= $endCurrentMonth) {
                    $item->digit = $item->digit1;
                    $item->soles = $item->soles_1;
                    $item->tm = $item->digit / 1000;
                } else {
                    $item->digit = $item->digit2;
                    $item->soles = $item->soles_2;
                    $item->tm = $item->digit / 1000;
                }
                $mes = Carbon::parse($item->created_at)->locale('es')->isoFormat('MMMM');
                $item->nombre_mes = Str::ucfirst($mes);

                if ($item->origin != 27) {
                    $item->article_name = 'E';
                } else {
                    $item->article_name = 'G';
                }

                return $item;
            });

        $totalSoles = $analisis_costo->sum('soles');
        $totalDigit = $analisis_costo->sum('digit');

        $resumen_costo_glp = ($totalDigit > 0) ? ($totalSoles / $totalDigit) * 10 : 0;

        // COSTO MES ANTERIOR

        $initialDate_anterior = Carbon::parse($final_date)->subMonthNoOverflow();
        $startPreviousMonth_anterior = $initialDate_anterior->copy()->subMonthNoOverflow()->startOfMonth();
        $endPreviousMonth_anterior   = $initialDate_anterior->copy()->subMonthNoOverflow()->endOfMonth();
        $startCurrentMonth_anterior  = $initialDate_anterior->copy()->startOfMonth();
        $endCurrentMonth_anterior    = $initialDate_anterior->copy()->endOfMonth();

        $analisis_costo_anterior = WarehouseMovement::join('warehouse_movement_details', 'warehouse_movement_details.warehouse_movement_id', 'warehouse_movements.id')
            ->join('companies', 'companies.id', 'warehouse_movements.company_id')
            ->join('warehouse_types', 'warehouse_types.id', 'warehouse_movements.warehouse_type_id')
            ->select(
                'warehouse_movements.id',
                'warehouse_movements.created_at',
                'warehouse_movement_details.digit1',
                'warehouse_movement_details.digit2',
                'warehouse_movements.soles_1',
                'warehouse_movements.soles_2',
                'warehouse_movements.created_at',
                'companies.short_name as company_name',
                DB::raw('DATE_FORMAT(warehouse_movements.created_at, "%Y-%m-%d") as fecha_emision'),
                DB::Raw('CONCAT(warehouse_movements.referral_serie_number, "-", warehouse_movements.referral_voucher_number) as factura'),
                'warehouse_types.name as warehouse_type_name',
                'warehouse_movements.origin',
                'warehouse_movements.referral_guide_number as guia',
                'warehouse_movements.license_plate as cisterna',
                'warehouse_movements.license_plate_2 as tracto',
                'warehouse_movements.cost_glp as cost_glp',
                'warehouse_types.short_name as carga_terminal',
                'warehouse_movements.scop_number as scop',
                'warehouse_movements.account_name as chofer'
            )
            ->whereBetween('warehouse_movements.created_at', [$startPreviousMonth_anterior, $endCurrentMonth_anterior])
            ->where(function ($query) use ($startPreviousMonth_anterior, $endPreviousMonth_anterior, $startCurrentMonth_anterior, $endCurrentMonth_anterior) {
                $query->where(function ($q) use ($startPreviousMonth_anterior, $endPreviousMonth_anterior) {
                    $q->whereBetween('warehouse_movements.created_at', [$startPreviousMonth_anterior, $endPreviousMonth_anterior])
                        ->where('warehouse_movement_details.digit2', '>', 0);
                })
                    ->orWhere(function ($q) use ($startCurrentMonth_anterior, $endCurrentMonth_anterior) {
                        $q->whereBetween('warehouse_movements.created_at', [$startCurrentMonth_anterior, $endCurrentMonth_anterior])
                            ->where('warehouse_movement_details.digit1', '>', 0);
                    });
            })
            ->orderBy('warehouse_movements.created_at', 'ASC')
            ->get()
            ->map(function ($item) use ($startCurrentMonth_anterior, $endCurrentMonth_anterior) {
                if ($item->created_at >= $startCurrentMonth_anterior && $item->created_at <= $endCurrentMonth_anterior) {
                    $item->digit = $item->digit1;
                    $item->soles = $item->soles_1;
                    $item->tm = $item->digit / 1000;
                } else {
                    $item->digit = $item->digit2;
                    $item->soles = $item->soles_2;
                    $item->tm = $item->digit / 1000;
                }
                $mes = Carbon::parse($item->created_at)->locale('es')->isoFormat('MMMM');
                $item->nombre_mes = Str::ucfirst($mes);

                if ($item->origin != 27) {
                    $item->article_name = 'E';
                } else {
                    $item->article_name = 'G';
                }

                return $item;
            });

        $totalSoles_anterior = $analisis_costo_anterior->sum('soles');
        $totalDigit_anterior = $analisis_costo_anterior->sum('digit');

        $resumen_costo_glp_anterior = ($totalDigit_anterior > 0) ? ($totalSoles_anterior / $totalDigit_anterior) * 10 : 0;

        $prev_initial = CarbonImmutable::createFromDate($initial_date)->subMonth()->format('Y-m-01');
        $prev_final   = CarbonImmutable::createFromDate($initial_date)->subMonth()->format('Y-m-t');

        $anio = CarbonImmutable::parse($final_date)->format('Y');
        $mes_numero = CarbonImmutable::parse($final_date)->format('m');

        $presupuestos = SalePresupuesto::where('anio', $anio)->where('mes', $mes_numero)
            ->select('client_sector_id', DB::raw('SUM(ppto_venta) as ppto_venta'), DB::raw('SUM(ppto_mb) as ppto_mb'))
            ->groupBy('client_sector_id')
            ->get()
            ->keyBy('client_sector_id');

        $resumen = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
            ->leftjoin('business_units', 'clients.business_unit_id', '=', 'business_units.id')
            ->leftjoin('client_sectors', 'clients.sector_id', '=', 'client_sectors.id')
            ->where('sales.sale_date', '>=', $prev_initial)
            ->where('sales.sale_date', '<=', $final_date)
            ->whereIn('sales.warehouse_document_type_id', [13, 5, 6, 7, 4])
            ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
            ->select(
                'business_units.name as business_unit_name',
                'client_sectors.name as sector_name',
                'client_sectors.id as sector_id',

                DB::raw("SUM(CASE WHEN sales.sale_date BETWEEN '$initial_date' AND '$final_date' THEN sale_details.kg / 1000 ELSE 0 END) as toneladas_actual"),
                DB::raw("SUM(CASE WHEN sales.sale_date BETWEEN '$initial_date' AND '$final_date' THEN sale_details.total ELSE 0 END) as venta_soles_actual"),

                DB::raw("SUM(CASE WHEN sales.sale_date BETWEEN '$prev_initial' AND '$prev_final' THEN sale_details.kg / 1000 ELSE 0 END) as toneladas_anterior"),
                DB::raw("SUM(CASE WHEN sales.sale_date BETWEEN '$prev_initial' AND '$prev_final' THEN sale_details.total ELSE 0 END) as venta_soles_anterior")
            )
            ->groupBy('business_units.name', 'client_sectors.name', 'client_sectors.id')
            ->get();

        $resumen->transform(function ($item) use ($presupuestos) {
            $ppto = $presupuestos->get($item->sector_id);
            $item->ppto = $ppto->ppto_venta ?? 0;
            $item->ppto_mb = $ppto->ppto_mb ?? 0;
            return $item;
        });

        $presupuestos_resumen2 = SalePresupuesto::where('anio', $anio)->where('mes', $mes_numero)
            ->select(
                'client_sector_id',
                'manager_id',
                DB::raw('SUM(ppto_venta) as ppto_venta'),
                DB::raw('SUM(ppto_mb) as ppto_mb')
            )
            ->groupBy('client_sector_id', 'manager_id')
            ->get()
            ->keyBy(function ($item) {
                return $item->client_sector_id . '_' . $item->manager_id;
            });

        $resumen_2 = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
            ->leftjoin('business_units', 'clients.business_unit_id', '=', 'business_units.id')
            ->leftjoin('client_sectors', 'clients.sector_id', '=', 'client_sectors.id')
            ->leftjoin('managers', 'clients.manager_id', '=', 'managers.id')
            ->where('sales.sale_date', '>=', $prev_initial)
            ->where('sales.sale_date', '<=', $final_date)
            ->whereIn('sales.warehouse_document_type_id', [13, 5, 6, 7, 4])
            ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
            ->select(
                'business_units.name as business_unit_name',
                'client_sectors.name as sector_name',
                'managers.name as manager_name',
                'client_sectors.id as sector_id',
                'managers.id as manager_id',

                DB::raw("SUM(CASE WHEN sales.sale_date BETWEEN '$initial_date' AND '$final_date' THEN sale_details.kg / 1000 ELSE 0 END) as toneladas_actual"),
                DB::raw("SUM(CASE WHEN sales.sale_date BETWEEN '$initial_date' AND '$final_date' THEN sale_details.total ELSE 0 END) as venta_soles_actual"),

                DB::raw("SUM(CASE WHEN sales.sale_date BETWEEN '$prev_initial' AND '$prev_final' THEN sale_details.kg / 1000 ELSE 0 END) as toneladas_anterior"),
                DB::raw("SUM(CASE WHEN sales.sale_date BETWEEN '$prev_initial' AND '$prev_final' THEN sale_details.total ELSE 0 END) as venta_soles_anterior")
            )
            ->groupBy('business_units.name', 'client_sectors.name', 'managers.name', 'client_sectors.id', 'managers.id')
            ->orderBy('business_units.name', 'asc')
            ->orderBy('client_sectors.name', 'asc')
            ->orderBy('managers.name', 'asc')
            ->get();

        $resumen_2->transform(function ($item) use ($presupuestos_resumen2) {
            $clave = $item->sector_id . '_' . $item->manager_id;
            $ppto = $presupuestos_resumen2->get($clave);
            $item->ppto = $ppto->ppto_venta ?? 0;
            $item->ppto_mb = $ppto->ppto_mb ?? 0;
            return $item;
        });

        $presupuestos_resumen3 = SalePresupuesto::where('anio', $anio)->where('mes', $mes_numero)
            ->select(
                'business_unit_id',
                'client_sector_id',
                'client_channel_id',
                DB::raw('SUM(ppto_venta) as ppto_venta'),
                DB::raw('SUM(ppto_mb) as ppto_mb')
            )
            ->groupBy('business_unit_id', 'client_sector_id', 'client_channel_id')
            ->get()
            ->keyBy(function ($item) {
                return $item->business_unit_id . '_' . $item->client_sector_id . '_' . $item->client_channel_id;
            });

        $resumen_3 = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
            ->leftjoin('business_units', 'clients.business_unit_id', '=', 'business_units.id')
            ->leftjoin('client_sectors', 'clients.sector_id', '=', 'client_sectors.id')
            ->leftjoin('client_channels', 'clients.channel_id', '=', 'client_channels.id')
            ->where('sales.sale_date', '>=', $prev_initial)
            ->where('sales.sale_date', '<=', $final_date)
            ->whereIn('sales.warehouse_document_type_id', [13, 5, 6, 7, 4])
            ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
            ->select(
                'business_units.name as business_unit_name',
                'client_sectors.name as sector_name',
                'client_channels.name as channel_name',
                'business_units.id as business_unit_id',
                'client_sectors.id as sector_id',
                'client_channels.id as channel_id',

                DB::raw("SUM(CASE WHEN sales.sale_date BETWEEN '$initial_date' AND '$final_date' THEN sale_details.kg / 1000 ELSE 0 END) as toneladas_actual"),
                DB::raw("SUM(CASE WHEN sales.sale_date BETWEEN '$initial_date' AND '$final_date' THEN sale_details.total ELSE 0 END) as venta_soles_actual"),

                DB::raw("SUM(CASE WHEN sales.sale_date BETWEEN '$prev_initial' AND '$prev_final' THEN sale_details.kg / 1000 ELSE 0 END) as toneladas_anterior"),
                DB::raw("SUM(CASE WHEN sales.sale_date BETWEEN '$prev_initial' AND '$prev_final' THEN sale_details.total ELSE 0 END) as venta_soles_anterior")
            )
            ->groupBy('business_units.name', 'client_sectors.name', 'client_channels.name', 'business_units.id', 'client_sectors.id', 'client_channels.id')
            ->orderBy('business_units.name', 'asc')
            ->orderBy('client_sectors.name', 'asc')
            ->orderBy('client_channels.name', 'asc')
            ->get();

        $resumen_3->transform(function ($item) use ($presupuestos_resumen3) {
            $clave = $item->business_unit_id . '_' . $item->sector_id . '_' . $item->channel_id;
            $ppto = $presupuestos_resumen3->get($clave);
            $item->ppto = $ppto->ppto_venta ?? 0;
            $item->ppto_mb = $ppto->ppto_mb ?? 0;
            return $item;
        });

        $presupuestos_resumen4 = SalePresupuesto::where('anio', $anio)->where('mes', $mes_numero)
            ->select(
                'client_channel_id',
                'client_route_id',
                'manager_id',
                DB::raw('SUM(ppto_venta) as ppto_venta'),
                DB::raw('SUM(ppto_mb) as ppto_mb')
            )
            ->groupBy('client_channel_id', 'client_route_id', 'manager_id')
            ->get()
            ->keyBy(function ($item) {
                return $item->client_channel_id . '_' . $item->client_route_id . '_' . $item->manager_id;
            });

        $resumen_4 = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
            ->leftjoin('client_channels', 'clients.channel_id', '=', 'client_channels.id')
            ->leftjoin('client_routes', 'clients.route_id', '=', 'client_routes.id')
            ->leftjoin('managers', 'clients.manager_id', '=', 'managers.id')
            ->leftjoin('client_sectors', 'clients.sector_id', '=', 'client_sectors.id')
            ->leftjoin('business_units', 'clients.business_unit_id', '=', 'business_units.id')
            ->where('sales.sale_date', '>=', $prev_initial)
            ->where('sales.sale_date', '<=', $final_date)
            ->whereIn('sales.warehouse_document_type_id', [13, 5, 6, 7, 4])
            ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
            ->select(
                'client_channels.name as channel_name',
                'client_routes.short_name as route_name',
                'managers.name as manager_name',
                'client_channels.id as channel_id',
                'client_routes.id as route_id',
                'managers.id as manager_id',

                DB::raw("SUM(CASE WHEN sales.sale_date BETWEEN '$initial_date' AND '$final_date' THEN sale_details.kg / 1000 ELSE 0 END) as toneladas_actual"),
                DB::raw("SUM(CASE WHEN sales.sale_date BETWEEN '$initial_date' AND '$final_date' THEN sale_details.total ELSE 0 END) as venta_soles_actual"),

                DB::raw("SUM(CASE WHEN sales.sale_date BETWEEN '$prev_initial' AND '$prev_final' THEN sale_details.kg / 1000 ELSE 0 END) as toneladas_anterior"),
                DB::raw("SUM(CASE WHEN sales.sale_date BETWEEN '$prev_initial' AND '$prev_final' THEN sale_details.total ELSE 0 END) as venta_soles_anterior")
            )
            ->groupBy('managers.name', 'client_routes.name', 'client_channels.name', 'managers.id', 'client_routes.id', 'client_channels.id')
            ->orderBy('client_channels.name', 'asc')
            ->orderBy('client_routes.name', 'asc')
            ->orderBy('managers.name', 'asc')
            ->whereIn('business_units.id', [1, 3]) //ENVASADO
            ->get();

        $resumen_4->transform(function ($item) use ($presupuestos_resumen4) {
            $clave = $item->channel_id . '_' . $item->route_id . '_' . $item->manager_id;
            $ppto = $presupuestos_resumen4->get($clave);
            $item->ppto = $ppto->ppto_venta ?? 0;
            $item->ppto_mb = $ppto->ppto_mb ?? 0;
            return $item;
        });

        $presupuestos_resumen5 = SalePresupuesto::where('anio', $anio)->where('mes', $mes_numero)
            ->select(
                'client_channel_id',
                'manager_id',
                DB::raw('SUM(ppto_venta) as ppto_venta'),
                DB::raw('SUM(ppto_mb) as ppto_mb')
            )
            ->groupBy('client_channel_id', 'manager_id')
            ->get()
            ->keyBy(function ($item) {
                return $item->client_channel_id . '_' . $item->manager_id;
            });

        $resumen_5 = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
            ->rightjoin('client_channels', 'clients.channel_id', '=', 'client_channels.id')
            ->leftjoin('managers', 'clients.manager_id', '=', 'managers.id')
            ->leftjoin('client_sectors', 'clients.sector_id', '=', 'client_sectors.id')
            ->leftjoin('business_units', 'clients.business_unit_id', '=', 'business_units.id')
            ->where('sales.sale_date', '>=', $prev_initial)
            ->where('sales.sale_date', '<=', $final_date)
            ->whereIn('sales.warehouse_document_type_id', [13, 5, 6, 7, 4])
            ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
            ->select(
                'client_channels.name as channel_name',
                'managers.name as manager_name',
                'client_channels.id as channel_id',
                'managers.id as manager_id',

                DB::raw("SUM(CASE WHEN sales.sale_date BETWEEN '$initial_date' AND '$final_date' THEN sale_details.kg / 1000 ELSE 0 END) as toneladas_actual"),
                DB::raw("SUM(CASE WHEN sales.sale_date BETWEEN '$initial_date' AND '$final_date' THEN sale_details.total ELSE 0 END) as venta_soles_actual"),

                DB::raw("SUM(CASE WHEN sales.sale_date BETWEEN '$prev_initial' AND '$prev_final' THEN sale_details.kg / 1000 ELSE 0 END) as toneladas_anterior"),
                DB::raw("SUM(CASE WHEN sales.sale_date BETWEEN '$prev_initial' AND '$prev_final' THEN sale_details.total ELSE 0 END) as venta_soles_anterior")
            )
            ->groupBy('client_channels.name', 'managers.name', 'client_channels.id', 'managers.id')
            ->whereIn('business_units.id', [2]) //ENVASADO
            ->orderBy('client_channels.name')
            ->get();

        $resumen_5->transform(function ($item) use ($presupuestos_resumen5) {
            $clave = $item->channel_id . '_' . $item->manager_id;
            $ppto = $presupuestos_resumen5->get($clave);
            $item->ppto = $ppto->ppto_venta ?? 0;
            $item->ppto_mb = $ppto->ppto_mb ?? 0;
            return $item;
        });


        // COBERTURAS 2DA HOJA

        $cobertura_1 = SaleDetail::leftJoin('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->leftJoin('clients', 'sales.client_id', '=', 'clients.id')
            ->leftJoin('client_sectors', 'client_sectors.id', '=', 'clients.sector_id')
            ->leftJoin('business_units', 'clients.business_unit_id', '=', 'business_units.id')
            ->leftJoin('client_channels', 'clients.channel_id', '=', 'client_channels.id')
            ->whereIn('sales.warehouse_document_type_id', [13, 5, 6, 7, 4])
            ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
            ->select(
                DB::raw('YEAR(sales.sale_date) as anio'),
                DB::raw('MONTH(sales.sale_date) as mes'),
                'client_channels.id as channel_id',
                'client_channels.name as channel_name',
                'client_sectors.name as sector_name',
                'client_sectors.id as client_sector_id',
                DB::raw(" COUNT(DISTINCT CASE WHEN clients.business_unit_id = 2 THEN clients.grupo ELSE clients.id END ) as total ")

            )
            ->groupBy('anio', 'mes', 'client_channels.id', 'client_channels.name')
            ->orderBy('anio', 'asc')
            ->orderBy('mes', 'asc')
            ->orderBy('business_units.name', 'asc')
            ->whereYear('sales.sale_date', '>=', 2025)
            ->where('sales.sale_date', '<=', $final_date)
            ->get();

        $cobertura_2 = SaleDetail::leftJoin('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->leftJoin('clients', 'sales.client_id', '=', 'clients.id')
            ->leftJoin('client_sectors', 'client_sectors.id', '=', 'clients.sector_id')
            ->leftJoin('client_routes', 'clients.route_id', '=', 'client_routes.id')
            ->leftJoin('business_units', 'clients.business_unit_id', '=', 'business_units.id')
            ->leftJoin('client_channels', 'clients.channel_id', '=', 'client_channels.id')
            ->whereIn('sales.warehouse_document_type_id', [13, 5, 6, 7, 4])
            ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
            ->select(
                DB::raw('YEAR(sales.sale_date) as anio'),
                DB::raw('MONTH(sales.sale_date) as mes'),
                'client_sectors.name as sector_name',
                'client_channels.name as channel_name',
                'client_routes.short_name as ruta',
                DB::raw("
                COUNT(DISTINCT
                    CASE
                        WHEN clients.business_unit_id = 2
                        THEN clients.grupo
                        ELSE clients.id
                    END
                ) as total
            ")

            )
            ->groupBy('anio', 'mes', 'client_sectors.name', 'client_channels.name', 'client_routes.name')
            ->orderBy('anio', 'asc')
            ->orderBy('mes', 'asc')
            ->orderBy('client_sectors.name', 'asc')
            ->orderBy('client_routes.name', 'asc')
            ->whereYear('sales.sale_date', '>=', $year)
            ->where('sales.sale_date', '<=', $final_date)
            ->get();

        // TERCERA HOJA

        $proyeccion_envasado = SaleDetail::leftJoin('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->leftJoin('clients', 'sales.client_id', '=', 'clients.id')
            ->leftjoin('client_sectors', 'clients.sector_id', '=', 'client_sectors.id')
            ->leftJoin('client_routes', 'clients.route_id', '=', 'client_routes.id')
            ->leftJoin('business_units', 'clients.business_unit_id', '=', 'business_units.id')
            ->leftJoin('client_channels', 'clients.channel_id', '=', 'client_channels.id')
            ->whereIn('sales.warehouse_document_type_id', [13, 5, 6, 7, 4])
            ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
            ->select(
                DB::raw('YEAR(sales.sale_date) as anio'),
                DB::raw('MONTH(sales.sale_date) as mes'),
                DB::raw('DAY(sales.sale_date) as dia'), // 🔥 NUEVO
                'client_sectors.id as sector_id',        // 🔥 NUEVO
                'client_sectors.name as sector_name',
                'client_channels.name as channel_name',
                'client_routes.short_name as ruta',
                DB::raw("SUM(sale_details.kg / 1000) as total")
            )
            ->groupBy('anio', 'mes', 'dia', 'client_sectors.id', 'client_sectors.name', 'client_channels.name', 'client_routes.name')
            ->orderBy('anio')
            ->orderBy('mes')
            ->orderBy('dia') // 🔥 ordenar por día
            ->orderBy('client_routes.name', 'asc')
            ->where('sales.sale_date', '>=', $initial_date)
            ->where('sales.sale_date', '<=', $final_date)
            ->whereNotIn('client_sectors.id', [2])
            ->get();

        $proyeccion_granel = SaleDetail::leftJoin('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->leftJoin('clients', 'sales.client_id', '=', 'clients.id')
            ->leftjoin('client_sectors', 'clients.sector_id', '=', 'client_sectors.id')
            ->leftJoin('managers', 'clients.manager_id', '=', 'managers.id')
            ->leftJoin('client_routes', 'clients.route_id', '=', 'client_routes.id')
            ->leftJoin('business_units', 'clients.business_unit_id', '=', 'business_units.id')
            ->leftJoin('client_channels', 'clients.channel_id', '=', 'client_channels.id')
            ->whereIn('sales.warehouse_document_type_id', [13, 5, 6, 7, 4])
            ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
            ->select(
                DB::raw('YEAR(sales.sale_date) as anio'),
                DB::raw('MONTH(sales.sale_date) as mes'),
                DB::raw('DAY(sales.sale_date) as dia'), // 🔥 NUEVO
                'managers.name as manager_name',
                'client_channels.name as channel_name',
                DB::raw("SUM(sale_details.kg / 1000) as total")
            )
            ->groupBy('anio', 'mes', 'dia', 'managers.name', 'client_channels.name') // 🔥 agregar dia
            ->orderBy('anio')
            ->orderBy('mes')
            ->orderBy('dia')
            ->orderBy('client_routes.name')
            ->where('sales.sale_date', '>=', $initial_date)
            ->where('sales.sale_date', '<=', $final_date)
            ->whereIn('client_sectors.id', [2])
            ->get();

        $excel = new SaleDayReportMultiSheetExport(
            $dias_totales,
            $dias_avance,
            $quedan,
            $final_date,
            $mes_actual,
            $mes_anterior,
            $detalle,
            $analisis_costo,
            $resumen_costo_glp_anterior,
            $resumen,
            $resumen_costo_glp,
            $resumen_2,
            $resumen_3,
            $resumen_4,
            $resumen_5,
            $cobertura_1,
            $cobertura_2,
            $proyeccion_envasado,
            $proyeccion_granel
        );
        return Excel::download($excel, 'reporte-usuarios.xlsx');
    }
}
