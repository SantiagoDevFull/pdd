<?php

namespace App\Http\Controllers\Backend;

use App\Client;
use App\Exports\PortfolioLocalReportMultiSheetExport;
use App\Http\Controllers\Controller;
use App\PriceList;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportPortfolioLocalController extends Controller
{
    public function index()
    {

        $current_date = date(DATE_ATOM, mktime(0, 0, 0));
        return view('backend.portfolio_local')->with(compact('current_date'));
    }

    public function validateForm()
    {
        $messages = [
            'final_date.required'    => 'Debe seleccionar una Fecha.',
        ];

        $rules = [
            'final_date'    => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }


    public function export()
    {

        $finalDate = Carbon::createFromFormat('d-m-Y', request('model.final_date'));
        $fecha_mes_1_calculo = $finalDate->copy()->startOfMonth();


        // Desde el inicio del mes hasta la fecha seleccionada
        $inicioMes = $finalDate->copy()->startOfMonth();

        // Fin del mes de la fecha seleccionada
        $finMes = $finalDate->copy()->endOfMonth();

        $diasHastaFecha = 0;
        $diasTotalesMes = 0;

        // Días desde inicio de mes hasta la fecha seleccionada (sin domingos)
        foreach (CarbonPeriod::create($inicioMes, $finalDate) as $fecha) {
            if ($fecha->dayOfWeek !== Carbon::SUNDAY) {
                $diasHastaFecha++;
            }
        }

        // Días totales del mes (sin domingos)
        foreach (CarbonPeriod::create($inicioMes, $finMes) as $fecha) {
            if ($fecha->dayOfWeek !== Carbon::SUNDAY) {
                $diasTotalesMes++;
            }
        }

        $fecha_mes_1_fin = $finalDate->copy()->toDateString();
        $fecha_mes_1_inicio = $finalDate->copy()->startOfMonth()->toDateString();

        $fecha_mes_2_inicio = $fecha_mes_1_calculo->copy()->subMonth()->startOfMonth()->toDateString();
        $fecha_mes_2_fin    = $fecha_mes_1_calculo->copy()->subMonth()->endOfMonth()->toDateString();

        $fecha_mes_3_inicio = $fecha_mes_1_calculo->copy()->subMonths(2)->startOfMonth()->toDateString();
        $fecha_mes_3_fin    = $fecha_mes_1_calculo->copy()->subMonths(2)->endOfMonth()->toDateString();

        $fecha_mes_4_inicio = $fecha_mes_1_calculo->copy()->subMonths(3)->startOfMonth()->toDateString();
        $fecha_mes_4_fin    = $fecha_mes_1_calculo->copy()->subMonths(3)->endOfMonth()->toDateString();

        $fecha_mes_5_inicio = $fecha_mes_1_calculo->copy()->subMonths(4)->startOfMonth()->toDateString();
        $fecha_mes_5_fin    = $fecha_mes_1_calculo->copy()->subMonths(4)->endOfMonth()->toDateString();

        $fecha_mes_6_inicio = $fecha_mes_1_calculo->copy()->subMonths(5)->startOfMonth()->toDateString();
        $fecha_mes_6_fin    = $fecha_mes_1_calculo->copy()->subMonths(5)->endOfMonth()->toDateString();

        $fecha_mes_7_inicio = $fecha_mes_1_calculo->copy()->subMonths(6)->startOfMonth()->toDateString();
        $fecha_mes_7_fin    = $fecha_mes_1_calculo->copy()->subMonths(6)->endOfMonth()->toDateString();


        $elements = Client::leftJoin('sales', 'clients.id', '=', 'sales.client_id')
            ->leftJoin('sale_details', 'sales.id', '=', 'sale_details.sale_id')
            ->leftJoin('client_sectors', 'clients.sector_id', '=', 'client_sectors.id')
            ->leftJoin('client_channels', 'client_channels.id', '=', 'clients.channel_id')
            ->leftJoin('client_routes', 'client_routes.id', '=', 'clients.route_id')
            ->leftJoin('client_types', 'clients.business_type', '=', 'client_types.id')
            ->leftJoin('client_addresses', function ($join) {
                $join->on('clients.id', '=', 'client_addresses.client_id')
                    ->where('client_addresses.address_type_id', 1);
            })
            ->leftJoin('ubigeos', 'client_addresses.ubigeo_id', '=', 'ubigeos.id')
            ->leftjoin('payments', 'clients.payment_id', '=', 'payments.id')
            ->select(
                'clients.id as client_id',
                'clients.business_name as cliente',

                'client_sectors.name as canal',
                'client_channels.name as subcanal',
                'client_types.name as negocio',
                'clients.estado_2 as estado',
                'clients.phone_number_1 as celular',
                'client_addresses.address as direccion',
                'ubigeos.district as distrito',
                'client_routes.short_name as ruta',
                'clients.zona as zona',
                'clients.sector as sector',
                'client_addresses.gps_x as gps_x',
                'client_addresses.gps_y as gps_y',
                DB::Raw("SUM(CASE WHEN sales.sale_date BETWEEN '$fecha_mes_7_inicio' AND '$fecha_mes_7_fin' THEN sale_details.kg ELSE 0 END) / 1000 as mes_7"),
                DB::Raw("SUM(CASE WHEN sales.sale_date BETWEEN '$fecha_mes_6_inicio' AND '$fecha_mes_6_fin' THEN sale_details.kg ELSE 0 END) / 1000 as mes_6"),
                DB::Raw("SUM(CASE WHEN sales.sale_date BETWEEN '$fecha_mes_5_inicio' AND '$fecha_mes_5_fin' THEN sale_details.kg ELSE 0 END) / 1000 as mes_5"),
                DB::Raw("SUM(CASE WHEN sales.sale_date BETWEEN '$fecha_mes_4_inicio' AND '$fecha_mes_4_fin' THEN sale_details.kg ELSE 0 END) / 1000 as mes_4"),
                DB::Raw("SUM(CASE WHEN sales.sale_date BETWEEN '$fecha_mes_3_inicio' AND '$fecha_mes_3_fin' THEN sale_details.kg ELSE 0 END) / 1000 as mes_3"),
                DB::Raw("SUM(CASE WHEN sales.sale_date BETWEEN '$fecha_mes_2_inicio' AND '$fecha_mes_2_fin' THEN sale_details.kg ELSE 0 END) / 1000 as mes_2"),
                DB::Raw("SUM(CASE WHEN sales.sale_date BETWEEN '$fecha_mes_1_inicio' AND '$fecha_mes_1_fin' THEN sale_details.kg ELSE 0 END) / 1000 as mes_1"),
                DB::Raw("SUM(CASE WHEN sales.sale_date BETWEEN '$fecha_mes_1_inicio' AND '$fecha_mes_1_fin' THEN sale_details.kg ELSE 0 END) / 1000 / '$diasHastaFecha' * '$diasTotalesMes' as proyeccion"),
                DB::Raw("SUM(sale_details.kg) / 1000 as total"),
                DB::raw("
                    (
                        CASE WHEN SUM(CASE WHEN sales.sale_date BETWEEN '$fecha_mes_7_inicio' AND '$fecha_mes_7_fin' THEN sale_details.kg ELSE 0 END) > 0 THEN 1 ELSE 0 END +
                        CASE WHEN SUM(CASE WHEN sales.sale_date BETWEEN '$fecha_mes_6_inicio' AND '$fecha_mes_6_fin' THEN sale_details.kg ELSE 0 END) > 0 THEN 1 ELSE 0 END +
                        CASE WHEN SUM(CASE WHEN sales.sale_date BETWEEN '$fecha_mes_5_inicio' AND '$fecha_mes_5_fin' THEN sale_details.kg ELSE 0 END) > 0 THEN 1 ELSE 0 END +
                        CASE WHEN SUM(CASE WHEN sales.sale_date BETWEEN '$fecha_mes_4_inicio' AND '$fecha_mes_4_fin' THEN sale_details.kg ELSE 0 END) > 0 THEN 1 ELSE 0 END +
                        CASE WHEN SUM(CASE WHEN sales.sale_date BETWEEN '$fecha_mes_3_inicio' AND '$fecha_mes_3_fin' THEN sale_details.kg ELSE 0 END) > 0 THEN 1 ELSE 0 END +
                        CASE WHEN SUM(CASE WHEN sales.sale_date BETWEEN '$fecha_mes_2_inicio' AND '$fecha_mes_2_fin' THEN sale_details.kg ELSE 0 END) > 0 THEN 1 ELSE 0 END +
                        CASE WHEN SUM(CASE WHEN sales.sale_date BETWEEN '$fecha_mes_1_inicio' AND '$fecha_mes_1_fin' THEN sale_details.kg ELSE 0 END) > 0 THEN 1 ELSE 0 END
                    ) as meses_compra
                "),
                'payments.name as forma_pago'
            )
            ->where('clients.company_id', 2)
            ->where('clients.business_unit_id', 3)
            ->where('clients.sector_id', 4)
            ->whereIn('clients.route_id', [81,83])
            ->whereBetween('sales.sale_date', [$fecha_mes_7_inicio, $fecha_mes_1_fin])
            ->groupBy('clients.id')
            ->get();

        $elements->map(function ($item) {
            $item->precio_cdg_azul_5kg = PriceList::leftjoin('clients', 'price_lists.client_id', '=', 'clients.id')
                ->where('price_lists.client_id', $item->client_id)
                ->where('price_lists.state', '=', 1)
                ->where('price_lists.article_id', '=', 10385)
                ->orderBy('price_lists.id', 'desc')
                ->value('price_igv');

            $item->precio_cdg_azul_10kg = PriceList::leftjoin('clients', 'price_lists.client_id', '=', 'clients.id')
                ->where('price_lists.client_id', $item->client_id)
                ->where('price_lists.state', '=', 1)
                ->where('price_lists.article_id', '=', 10386)
                ->orderBy('price_lists.id', 'desc')
                ->value('price_igv');

            $item->precio_cdg_azul_15kg = PriceList::leftjoin('clients', 'price_lists.client_id', '=', 'clients.id')
                ->where('price_lists.client_id', $item->client_id)
                ->where('price_lists.state', '=', 1)
                ->where('price_lists.article_id', '=', 10387)
                ->orderBy('price_lists.id', 'desc')
                ->value('price_igv');

            $item->precio_cdg_azul_45kg = PriceList::leftjoin('clients', 'price_lists.client_id', '=', 'clients.id')
                ->where('price_lists.client_id', $item->client_id)
                ->where('price_lists.state', '=', 1)
                ->where('price_lists.article_id', '=', 10388)
                ->orderBy('price_lists.id', 'desc')
                ->value('price_igv');

            return $item;
        });



        $excel = new PortfolioLocalReportMultiSheetExport($elements, $fecha_mes_1_calculo);
        return Excel::download($excel, 'reporte-cartera-clientes-locales.xlsx');
    }
}
