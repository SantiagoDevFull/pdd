<?php

namespace App\Http\Controllers\Backend;

use App\ClientRoute;
use App\Exports\OrderHouseReportExport;
use App\HouseOrder;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class OrderHouseReportController extends Controller
{
    public function index()
    {
        $current_date = date(DATE_ATOM, mktime(0, 0, 0));
        $first_date = date(DATE_ATOM, strtotime('first day of this month'));
        $client_routes = ClientRoute::whereIn('id', [81,83])->get();
        return view('backend.report_order_house')->with(compact('current_date', 'first_date','client_routes'));
    }

    public function validateForm()
    {
        $messages = [
            'initial_date.required'    => 'Debe seleccionar una Fecha inicial.',
            'final_date.required'      => 'Debe seleccionar una Fecha final.',
        ];

        $rules = [
            'initial_date'    => 'required',
            'final_date'      => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function list()
    {
        $export_excel = request('export_excel');
        $q = request('query');
        $search = $q['generalSearch'];

        $state = request('model.state');
        $route_id = request('model.route_id');
        $initial_date = request('model.initial_date') ? Carbon::parse(request('model.initial_date'))->startOfDay() : null;
        $final_date = request('model.final_date') ? Carbon::parse(request('model.final_date'))->endOfDay() : null;

        $elements = HouseOrder::leftjoin('clients', 'clients.id', '=', 'house_orders.client_id')
            ->leftjoin('client_channels', 'client_channels.id', '=', 'clients.channel_id')
            ->leftjoin('client_types', 'client_types.id', '=', 'clients.business_type')
            ->leftjoin('client_routes', 'client_routes.id', '=', 'clients.route_id')
            ->leftjoin('origin_houses', 'origin_houses.id', '=', 'house_orders.origin_id')
            ->leftjoin('articles', 'articles.id', '=', 'house_orders.article_id')
            ->leftjoin('payment_methods', 'payment_methods.id', '=', 'house_orders.payment_method_id')
            ->leftjoin('employees', 'employees.id', '=', 'house_orders.employee_id')
            ->leftjoin('vouchers', 'vouchers.id', '=', 'house_orders.boleta_id')
            ->select(
                DB::Raw('DATE_FORMAT(house_orders.created_at, "%Y") as anio'),
                DB::Raw('DATE_FORMAT(house_orders.created_at, "%m") as mes'),
                DB::Raw('DATE_FORMAT(house_orders.created_at, "%d") as dia'),
                DB::Raw('DATE_FORMAT(house_orders.created_at, "%a") as semana'),
                'client_channels.name as canal',
                'client_types.name as negocio',
                'clients.phone_number_1 as celular',
                DB::raw("IF(house_orders.nuevo = 1, 'X', '') as nuevo"),
                DB::raw("IF(house_orders.recuperado = 1, 'X', '') as recuperado"),
                'house_orders.client_id as cliente',
                'clients.business_name as nombre',
                DB::raw('CONCAT(clients.id, " - ", clients.business_name, " - ", clients.phone_number_1) as nom_cliente'),
                'client_routes.short_name as ruta',
                'clients.zona as zona',
                'clients.sector as sector',
                DB::Raw('DATE_FORMAT(clients.created_at, "%Y-%m-%d") as creacion_cli'),
                'origin_houses.name as origen',
                'house_orders.obs_cli as house_order_obs_cli',

                DB::raw("
                    CASE house_orders.product_type
                        WHEN 1 THEN 'GAS'
                        WHEN 2 THEN 'VALVULA'
                        WHEN 3 THEN 'CILINDRO'
                        ELSE 'NA'
                    END AS tipo_producto
                "),

                'articles.name as articulo',
                DB::raw('FORMAT(house_orders.price, 2) as precio'),
                'house_orders.quantity as cantidad',
                DB::raw('FORMAT(house_orders.total, 2) as total'),
                'payment_methods.name as pago',
                DB::raw('FORMAT(house_orders.monto, 2) as monto'),
                DB::raw('FORMAT(house_orders.vuelto, 2) as vuelto'),
                DB::raw('DATE_FORMAT(house_orders.hora_llamada, "%H:%i") as hora_llamada'),
                DB::raw('DATE_FORMAT(house_orders.hora_entrega_balon, "%H:%i") as hora_entrega_balon'),
                DB::raw('DATE_FORMAT(house_orders.hora_salida_pedido, "%H:%i") as hora_salida_pedido'),
                DB::raw('DATE_FORMAT(house_orders.hora_entrega_pedido, "%H:%i") as hora_entrega_pedido'),
                DB::raw('DATE_FORMAT(house_orders.hora_retorno, "%H:%i") as hora_retorno'),
                DB::raw('DATE_FORMAT(house_orders.tiempo_total, "%H:%i") as tiempo_total'),
                DB::raw("
                    CASE
                        WHEN house_orders.turno = 'M' THEN 'MAÑANA'
                        WHEN house_orders.turno = 'N' THEN 'NOCHE'
                        ELSE 'NA'
                    END as turno
                "),
                DB::raw("UPPER(CONCAT(
                    LEFT(SUBSTRING_INDEX(employees.last_name, ' ', 1), 1),
                    LEFT(SUBSTRING_INDEX(employees.first_name, ' ', 1), 1)
                )) as empleado"),
                'house_orders.motivo_demora as motivo_demora',
                'vouchers.serie_number as serie',
                'vouchers.voucher_number as voucher',
                'house_orders.state as estado',
                'house_orders.fecha_proxima as prox_compra',
                'house_orders.serie as serie_pedido',
                'house_orders.correlative as correlative_pedido',
                DB::raw("CONCAT(house_orders.serie, '-', house_orders.correlative) as codigo"),
                DB::raw('DATE_FORMAT(house_orders.created_at, "%Y-%m-%d") as fecha_emision'),

                'clients.estado_2 as estado_cli',
                'clients.razon as razon',
                DB::raw('DATE_FORMAT(clients.created_at, "%Y-%m-%d") as creacion')
            )
            ->when($search, function ($query, $search) {
                return $query->where('clients.id', '=', $search)
                    ->orWhere('clients.business_name', 'like', '%' . $search . '%')
                    ->orWhere('clients.phone_number_1', 'like', '%' . $search . '%');
            })
            ->when(!empty($state), function ($q) use ($state) {
                return $q->where('house_orders.state', $state);
            })
            ->when($initial_date, function ($q) use ($initial_date) {
                $q->where('house_orders.created_at', '>=', $initial_date);
            })
            ->when($final_date, function ($q) use ($final_date) {
                $q->where('house_orders.created_at', '<=', $final_date);
            })
            ->when($route_id, function ($q) use ($route_id) {
                $q->where('clients.route_id', '=', $route_id);
            })
            ->orderBy('house_orders.correlative', 'asc')
            ->get();

        $mapDias = [
            'Mon' => 'L',
            'Tue' => 'M',
            'Wed' => 'X',
            'Thu' => 'J',
            'Fri' => 'V',
            'Sat' => 'S',
            'Sun' => 'D',
        ];

        $elements->transform(function ($item) use ($mapDias) {
            $item->semana = $mapDias[$item->semana] ?? $item->semana;
            $item->formato_completo = "{$item->anio}-{$item->mes}-{$item->dia}-{$item->semana}";
            return $item;
        });

        if ($export_excel) {
            $datetime = CarbonImmutable::now()->format('d/m/Y h:i:s a');

            $excel = new OrderHouseReportExport($datetime, $elements);
            return Excel::download($excel, 'reporte-registro-pedidos.xlsx');
        }

        return response()->json($elements);
    }
}
