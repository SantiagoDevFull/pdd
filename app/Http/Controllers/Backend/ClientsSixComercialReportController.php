<?php

namespace App\Http\Controllers\Backend;


use App\Http\Controllers\Controller;
use App\Client;
use App\Company;
use App\Article;
use App\ClientRoute;
use Illuminate\Http\Request;
use App\SaleDetail;
use App\Sale;
use App\BusinessUnit;
use Carbon\CarbonImmutable;
use Carbon\Carbon;
use strtotime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use stdClass;


class ClientsSixComercialReportController extends Controller
{
    public function index()
    {

        $current_date = date(DATE_ATOM, mktime(0, 0, 0));
        $client_routes = ClientRoute::select('id', 'short_name')->whereIn('type', [1, 2, 3])->get();



        return view('backend.clients_six_comercial_report')->with(compact('current_date', 'client_routes'));
    }

    public function validateForm()
    {
        $messages = [
            'initial_date.required'    => 'Debe seleccionar una Fecha inicial.',

        ];

        $rules = [
            'initial_date'    => 'required',

        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function getClients()
    {
        $business_unit_id = request('business_unit_id');
        $q = request('q');

        $clients = Client::select('id', 'business_name as text')
            ->when($business_unit_id, function ($query, $business_unit_id) {
                return $query->where('business_unit_id', $business_unit_id);
            })
            ->where('business_name', 'like', '%' . $q . '%')
            ->withTrashed()
            ->get();

        return $clients;
    }

    public function list()
    {

        $export = request('export');
        $route_id = request('model.route_id');

        $initial_date = CarbonImmutable::createFromDate(request('model.initial_date'))->startOfDay()->format('Y-m-d');
        $mes_date = CarbonImmutable::createFromDate(request('model.initial_date'))->startOfDay()->format('Y-m-01');
        $day = date('j', strtotime($initial_date));
        $mesActual = date('n', strtotime($initial_date));
        $anioActual = date('Y', strtotime($initial_date));
        $number = cal_days_in_month(CAL_GREGORIAN, $mesActual, $anioActual);
        $price_mes6 = date('Y-m-01', strtotime('-6 month', strtotime($mes_date)));




        $elements = ClientRoute::leftjoin('client_sectors', 'client_routes.sector_id', '=', 'client_sectors.id')
            ->leftjoin('business_units', 'client_routes.business_id', '=', 'business_units.id')
            ->leftjoin('client_channels', 'client_routes.channel_id', '=', 'client_channels.id')
            ->whereIn('client_routes.type', [1, 2, 3])
            ->select(
                'client_routes.id as ruta_id',
                'client_routes.id as ruta_id',
                'client_routes.short_name as ruta',
                'business_units.name as name',
                'client_sectors.name as channel_name',
                'client_channels.name as sector_name'
            )
            ->when($route_id, function ($query, $route_id) {
                return $query->where('client_routes.id', $route_id);
            })
            ->groupBy('ruta_id')
            ->orderBy('client_routes.business_id')
            ->orderBy('client_routes.sector_id')
            ->orderBy('client_routes.channel_id')
            ->orderBy('client_routes.id')
            ->get();



        $newElements = Client::leftjoin('client_routes', 'clients.route_id', '=', 'client_routes.id')
            ->leftjoin('client_channels', 'clients.channel_id', '=', 'client_channels.id')
            ->leftjoin('business_units', 'clients.business_unit_id', '=', 'business_units.id')
            ->leftjoin('client_sectors', 'clients.sector_id', '=', 'client_sectors.id')
            ->whereIn('client_routes.type', [1, 2, 3])
            ->select(
                'client_routes.id as ruta_id',
                'clients.id as client_id',
                'clients.business_name as client_name',
                'client_routes.short_name as ruta',
                'business_units.name as name',
                'client_sectors.name as canal_name',
                'client_channels.name as subcanal_name',
                'clients.created_at as created_date'
            )
            ->when($route_id, function ($query, $route_id) {
                return $query->where('client_routes.id', $route_id);
            })
            ->where('clients.created_at', '<=', $initial_date)
            ->where('clients.created_at', '>=', $price_mes6)
            ->groupBy('client_id')
            ->orderBy('name')
            ->orderBy('ruta')
            ->get();


        $response = [];
        $response_2 = [];

        $totals_clientes = 0;
        $totals_tm = 0;
        $totals_mes_ant = 0;
        $totals_mes_proy = 0;
        $totals_mes_dos = 0;
        $totals_mes_tres = 0;
        $totals_mes_cuatro = 0;
        $totals_mes_cinco = 0;
        $totals_mes_seis = 0;
        $totals_mes_max = 0;
        $totals_tm_ant = 0;
        $totals_tm_proy = 0;
        $totals_tm_dos = 0;
        $totals_tm_tres = 0;
        $totals_tm_cuatro = 0;
        $totals_tm_cinco = 0;
        $totals_tm_seis = 0;
        $totals_tmd_ant = 0;
        $totals_tmd = 0;
        $totals_tmd_dos = 0;
        $totals_tmd_tres = 0;
        $totals_tmd_cuatro = 0;
        $totals_tmd_cinco = 0;
        $totals_tmd_seis = 0;


        foreach ($elements as $saledetail) {

            $price_mes = date('Y-m-01', strtotime('-1 month', strtotime($mes_date)));
            //  $inicioMesPasado = date("Y-m-d", $price_mes);
            $tiempoMesPasado = strtotime("last day of previous month", strtotime($mes_date));
            $fechaMesPasado = date("Y-m-d", $tiempoMesPasado);

            $price_mes2 = date('Y-m-01', strtotime('-2 month', strtotime($mes_date)));
            $price_mes3 = date('Y-m-01', strtotime('-3 month', strtotime($mes_date)));
            $price_mes4 = date('Y-m-01', strtotime('-4 month', strtotime($mes_date)));
            $price_mes5 = date('Y-m-01', strtotime('-5 month', strtotime($mes_date)));
            $price_mes6 = date('Y-m-01', strtotime('-6 month', strtotime($mes_date)));

            $tiempoMesPasado2 = strtotime("last day of previous month", strtotime($price_mes));
            $fechaMesPasado2 = date("Y-m-d", $tiempoMesPasado2);
            $tiempoMesPasado3 = strtotime("last day of previous month", strtotime($price_mes2));
            $fechaMesPasado3 = date("Y-m-d", $tiempoMesPasado3);
            $tiempoMesPasado4 = strtotime("last day of previous month", strtotime($price_mes3));
            $fechaMesPasado4 = date("Y-m-d", $tiempoMesPasado4);
            $tiempoMesPasado5 = strtotime("last day of previous month", strtotime($price_mes4));
            $fechaMesPasado5 = date("Y-m-d", $tiempoMesPasado5);
            $tiempoMesPasado6 = strtotime("last day of previous month", strtotime($price_mes5));
            $fechaMesPasado6 = date("Y-m-d", $tiempoMesPasado6);

            $monthNameact = date("F", strtotime($mes_date));
            $monthName  = date("F", strtotime($fechaMesPasado));
            $monthName2 = date("F", strtotime($fechaMesPasado2));
            $monthName3 = date("F", strtotime($fechaMesPasado3));
            $monthName4 = date("F", strtotime($fechaMesPasado4));
            $monthName5 = date("F", strtotime($fechaMesPasado5));
            $monthName6 = date("F", strtotime($fechaMesPasado6));

            $saledetail->ruta = $saledetail['ruta'];
            $saledetail->ruta_id = $saledetail['ruta_id'];
            $saledetail->client_name = $saledetail['client_name'];
            $saledetail->name = $saledetail['name'];
            $saledetail->sector_name = $saledetail['sector_name'];
            $saledetail->channel_name = $saledetail['channel_name'];
            $saledetail->codigo = $saledetail['client_id'];

            $clientes = Client::where('clients.created_at', '>=', $mes_date)
                ->where('clients.created_at', '<=', $initial_date)
                ->where('clients.route_id', '=', $saledetail->ruta_id)
                ->count();

            $cliente_last = Client::where('clients.route_id', '=', $saledetail->ruta_id)
                ->where('clients.created_at', '>=', $price_mes)
                ->where('clients.created_at', '<=', $fechaMesPasado)
                ->count();

            $cliente_dos = Client::where('clients.route_id', '=', $saledetail->ruta_id)
                ->where('clients.created_at', '>=', $price_mes2)
                ->where('clients.created_at', '<=', $fechaMesPasado2)
                ->count();

            $cliente_tres = Client::where('clients.route_id', '=', $saledetail->ruta_id)
                ->where('clients.created_at', '>=', $price_mes3)
                ->where('clients.created_at', '<=', $fechaMesPasado3)
                ->count();

            $cliente_cuatro = Client::where('clients.route_id', '=', $saledetail->ruta_id)
                ->where('clients.created_at', '>=', $price_mes4)
                ->where('clients.created_at', '<=', $fechaMesPasado4)
                ->count();

            $cliente_cinco = Client::where('clients.route_id', '=', $saledetail->ruta_id)
                ->where('clients.created_at', '>=', $price_mes5)
                ->where('clients.created_at', '<=', $fechaMesPasado5)
                ->count();

            $cliente_seis = Client::where('clients.route_id', '=', $saledetail->ruta_id)
                ->where('clients.created_at', '>=', $price_mes6)
                ->where('clients.created_at', '<=', $fechaMesPasado6)
                ->count();


            $tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
                ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
                ->where('clients.route_id', '=', $saledetail->ruta_id)
                ->where('clients.created_at', '>=', $mes_date)
                ->where('clients.created_at', '<=', $initial_date)
                ->whereNotIn('sales.warehouse_document_type_id', [2, 3, 8, 9, 10, 11, 14, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29])
                ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258, 13233, 14258])
                ->select('kg')
                ->sum('kg');


            $tm_last = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
                ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
                ->where('clients.route_id', '=', $saledetail->ruta_id)
                ->where('clients.created_at', '>=',  $price_mes)
                ->where('clients.created_at', '<=',  $fechaMesPasado)
                ->whereNotIn('sales.warehouse_document_type_id', [2, 3, 8, 9, 10, 11, 14, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29])
                ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258, 13233, 14258])
                ->select('kg')
                ->sum('kg');

            $tm_dos = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
                ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
                ->where('clients.route_id', '=', $saledetail->ruta_id)
                ->where('clients.created_at', '>=',  $price_mes2)
                ->where('clients.created_at', '<=',  $fechaMesPasado2)
                ->whereNotIn('sales.warehouse_document_type_id', [2, 3, 8, 9, 10, 11, 14, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29])
                ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258, 13233, 14258])
                ->select('kg')
                ->sum('kg');

            $tm_tres = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
                ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
                ->where('clients.route_id', '=', $saledetail->ruta_id)
                ->where('clients.created_at', '>=',  $price_mes3)
                ->where('clients.created_at', '<=',  $fechaMesPasado3)
                ->whereNotIn('sales.warehouse_document_type_id', [2, 3, 8, 9, 10, 11, 14, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29])
                ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258, 13233, 14258])
                ->select('kg')
                ->sum('kg');

            $tm_cuatro = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
                ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
                ->where('clients.route_id', '=', $saledetail->ruta_id)
                ->where('clients.created_at', '>=',  $price_mes4)
                ->where('clients.created_at', '<=',  $fechaMesPasado4)
                ->whereNotIn('sales.warehouse_document_type_id', [2, 3, 8, 9, 10, 11, 14, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29])
                ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258, 13233, 14258])
                ->select('kg')
                ->sum('kg');

            $tm_cinco = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
                ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
                ->where('clients.route_id', '=', $saledetail->ruta_id)
                ->where('clients.created_at', '>=',  $price_mes5)
                ->where('clients.created_at', '<=',  $fechaMesPasado5)
                ->whereNotIn('sales.warehouse_document_type_id', [2, 3, 8, 9, 10, 11, 14, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29])
                ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258, 13233, 14258])
                ->select('kg')
                ->sum('kg');

            $tm_seis = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
                ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
                ->where('clients.route_id', '=', $saledetail->ruta_id)
                ->where('clients.created_at', '>=',  $price_mes6)
                ->where('clients.created_at', '<=',  $fechaMesPasado6)
                ->whereNotIn('sales.warehouse_document_type_id', [2, 3, 8, 9, 10, 11, 14, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29])
                ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258, 13233, 14258])
                ->select('kg')
                ->sum('kg');

            $saledetail->tm = $tm_fin / 1000;
            $saledetail->tm_act = $saledetail['tm'];
            $saledetail->tm_proy =  ($saledetail['tm'] / $day) * $number;
            $saledetail->tm_ant = $tm_last / 1000;
            $saledetail->tm_dos = $tm_dos / 1000;
            $saledetail->tm_tres = $tm_tres / 1000;
            $saledetail->tm_cuatro = $tm_cuatro / 1000;
            $saledetail->tm_cinco = $tm_cinco / 1000;
            $saledetail->tm_seis = $tm_seis / 1000;

            $saledetail->clientes = $clientes;
            $saledetail->mes_act = $saledetail['clientes'];
            $saledetail->mes_proy =  ($saledetail['clientes'] / $day) * $number;
            $saledetail->mes_ant = $cliente_last;
            $saledetail->mes_dos = $cliente_dos;
            $saledetail->mes_tres = $cliente_tres;
            $saledetail->mes_cuatro = $cliente_cuatro;
            $saledetail->mes_cinco = $cliente_cinco;
            $saledetail->mes_seis = $cliente_seis;

            $saledetail->mes_max = max(
                $saledetail->mes_ant,
                $saledetail->mes_dos,
                $saledetail->mes_tres,
                $saledetail->mes_cuatro,
                $saledetail->mes_cinco,
                $saledetail->mes_seis
            );

            if ($saledetail->mes_ant != 0)
                $saledetail->diff =   $saledetail->mes_proy - $saledetail->mes_ant;
            else {
                $saledetail->diff = $saledetail->mes_proy;
            }

            if ($saledetail->mes_proy != 0)
                $saledetail->porc_diff = ($saledetail->mes_ant / $saledetail->mes_proy);
            else {
                $saledetail->porc_diff = 0;
            }

            $tmd_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
                ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
                ->where('sales.client_id', '=', $saledetail->codigo)
                ->where('clients.created_at', '>=', $mes_date)
                ->where('clients.created_at', '<=', $initial_date)
                ->whereNotIn('sales.warehouse_document_type_id', [2, 3, 8, 9, 10, 11, 14, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29])
                ->select('kg')
                ->sum('kg');


            $tmd_last = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
                ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
                ->where('sales.client_id', '=', $saledetail->codigo)
                ->where('clients.created_at', '>=',  $price_mes)
                ->where('clients.created_at', '<=',  $fechaMesPasado)
                ->whereNotIn('sales.warehouse_document_type_id', [2, 3, 8, 9, 10, 11, 14, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29])
                ->select('kg')
                ->sum('kg');

            $tmd_dos = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
                ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
                ->where('sales.client_id', '=', $saledetail->codigo)
                ->where('clients.created_at', '>=',  $price_mes2)
                ->where('clients.created_at', '<=',  $fechaMesPasado2)
                ->whereNotIn('sales.warehouse_document_type_id', [2, 3, 8, 9, 10, 11, 14, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29])
                ->select('kg')
                ->sum('kg');

            $tmd_tres = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
                ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
                ->where('sales.client_id', '=', $saledetail->codigo)
                ->where('clients.created_at', '>=',  $price_mes3)
                ->where('clients.created_at', '<=',  $fechaMesPasado3)
                ->whereNotIn('sales.warehouse_document_type_id', [2, 3, 8, 9, 10, 11, 14, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29])
                ->select('kg')
                ->sum('kg');

            $tmd_cuatro = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
                ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
                ->where('sales.client_id', '=', $saledetail->codigo)
                ->where('clients.created_at', '>=',  $price_mes4)
                ->where('clients.created_at', '<=',  $fechaMesPasado4)
                ->whereNotIn('sales.warehouse_document_type_id', [2, 3, 8, 9, 10, 11, 14, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29])
                ->select('kg')
                ->sum('kg');

            $tmd_cinco = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
                ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
                ->where('sales.client_id', '=', $saledetail->codigo)
                ->where('clients.created_at', '>=',  $price_mes5)
                ->where('clients.created_at', '<=',  $fechaMesPasado5)
                ->whereNotIn('sales.warehouse_document_type_id', [2, 3, 8, 9, 10, 11, 14, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29])
                ->select('kg')
                ->sum('kg');

            $tmd_seis = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
                ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
                ->where('sales.client_id', '=', $saledetail->codigo)
                ->where('clients.created_at', '>=',  $price_mes6)
                ->where('clients.created_at', '<=',  $fechaMesPasado6)
                ->whereNotIn('sales.warehouse_document_type_id', [2, 3, 8, 9, 10, 11, 14, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29])
                ->select('kg')
                ->sum('kg');

            $saledetail->tmd = $tmd_fin / 1000;
            $saledetail->tmd_ant = $tmd_last / 1000;
            $saledetail->tmd_dos = $tmd_dos / 1000;
            $saledetail->tmd_tres = $tmd_tres / 1000;
            $saledetail->tmd_cuatro = $tmd_cuatro / 1000;
            $saledetail->tmd_cinco = $tmd_cinco / 1000;
            $saledetail->tmd_seis = $tmd_seis / 1000;

            $totals_tmd += $saledetail['tmd'];
            $totals_tmd_ant += $saledetail['tmd_ant'];
            $totals_tmd_dos += $saledetail['tmd_dos'];
            $totals_tmd_tres += $saledetail['tmd_tres'];
            $totals_tmd_cuatro += $saledetail['tmd_cuatro'];
            $totals_tmd_cinco += $saledetail['tmd_cinco'];
            $totals_tmd_seis += $saledetail['tmd_seis'];

            $totals_clientes += $saledetail['clientes'];
            $totals_mes_ant += $saledetail['mes_ant'];
            $totals_mes_dos += $saledetail['mes_dos'];
            $totals_mes_tres += $saledetail['mes_tres'];
            $totals_mes_cuatro += $saledetail['mes_cuatro'];
            $totals_mes_cinco += $saledetail['mes_cinco'];
            $totals_mes_seis += $saledetail['mes_seis'];
            $totals_mes_proy += $saledetail['mes_proy'];
            $totals_mes_max += $saledetail['mes_max'];

            $totals_tm += $saledetail['tm'];
            $totals_tm_ant += $saledetail['tm_ant'];
            $totals_tm_dos += $saledetail['tm_dos'];
            $totals_tm_tres += $saledetail['tm_tres'];
            $totals_tm_cuatro += $saledetail['tm_cuatro'];
            $totals_tm_cinco += $saledetail['tm_cinco'];
            $totals_tm_seis += $saledetail['tm_seis'];

            $response[] = $saledetail;
        }

        foreach ($newElements as $saledetail) {

            $price_mes = date('Y-m-01', strtotime('-1 month', strtotime($mes_date)));
            //  $inicioMesPasado = date("Y-m-d", $price_mes);
            $tiempoMesPasado = strtotime("last day of previous month", strtotime($mes_date));
            $fechaMesPasado = date("Y-m-d", $tiempoMesPasado);



            $saledetail->codigo = $saledetail['client_id'];
            $saledetail->ruta = $saledetail['ruta'];
            $saledetail->ruta_id = $saledetail['ruta_id'];
            $saledetail->name = $saledetail['name'];
            $saledetail->canal_name = $saledetail['canal_name'];
            $saledetail->subcanal_name = $saledetail['subcanal_name'];
            $saledetail->client_name = $saledetail['client_name'];
            $saledetail->created_date = $saledetail['created_date'];

            //$one_day = date('Y-m-01', strtotime($saledetail->created_date));
            $one_day = $saledetail->created_date;
            $ultimate_day = strtotime("last day of this month", strtotime($saledetail->created_date));
            $ultimate_day_var = date("Y-m-d", $ultimate_day);



            $saledetail->one_day = $one_day;
            $saledetail->ultimate_day_var = $ultimate_day_var;

            $mes_day_var = date("F", strtotime($saledetail->ultimate_day_var));

            $saledetail->mes_day_var = $mes_day_var;



            $tmd_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
                ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
                ->where('sales.client_id', '=', $saledetail->codigo)
                ->where('clients.created_at', '>=', $saledetail->one_day)
                ->where('clients.created_at', '<=', $saledetail->ultimate_day_var)
                ->whereNotIn('sales.warehouse_document_type_id', [2, 3, 8, 9, 10, 11, 14, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29])
                ->select('kg')
                ->sum('kg');




            $saledetail->tmd = $tmd_fin / 1000;


            $totals_tmd += $saledetail['tmd'];


            $response_2[] = $saledetail;
        }

        $totals = new stdClass();
        $totals->name = 'TOTAL';
        $totals->sector_name = '';
        $totals->channel_name = '';
        $totals->client_name = '';
        $totals->ruta = '';
        $totals->mes_ant = $totals_mes_ant;
        $totals->clientes = $totals_clientes;
        $totals->mes_ant = $totals_mes_ant;
        $totals->mes_dos = $totals_mes_dos;
        $totals->mes_tres =  $totals_mes_tres;
        $totals->mes_cuatro =  $totals_mes_cuatro;
        $totals->mes_cinco =  $totals_mes_cinco;
        $totals->mes_seis = $totals_mes_seis;
        $totals->tm = $totals_tm;
        $totals->tm_ant = $totals_tm_ant;
        $totals->tm_dos = $totals_tm_dos;
        $totals->tm_tres =  $totals_tm_tres;
        $totals->tm_cuatro =  $totals_tm_cuatro;
        $totals->tm_cinco =  $totals_tm_cinco;
        $totals->tm_seis = $totals_tm_seis;
        $totals->tmd = $totals_tmd;
        $totals->tmd_ant = $totals_tmd_ant;
        $totals->tmd_dos = $totals_tmd_dos;
        $totals->tmd_tres =  $totals_tmd_tres;
        $totals->tmd_cuatro =  $totals_tmd_cuatro;
        $totals->tmd_cinco =  $totals_tmd_cinco;
        $totals->tmd_seis = $totals_tmd_seis;
        $totals->mes_proy =  $totals_mes_proy;
        $totals->mes_max = $totals_mes_max;
        $totals->diff = '';
        $totals->porc_diff = '';

        $response[] = $totals;

        $totals_2 = new stdClass();
        $totals_2->name = 'TOTAL';
        $totals_2->subcanal_name = '';
        $totals_2->canal_name = '';
        $totals_2->client_name = '';
        $totals_2->ruta = '';
        $totals_2->codigo = '';
        $totals_2->tmd = $totals_tmd;
        $totals_2->one_day = '';
        $totals_2->ultimate_day_var = '';
        $totals_2->mes_day_var = '';


        $response_2[] = $totals_2;

        if ($export) {
            $spreadsheet = new Spreadsheet();

            $sheet = $spreadsheet->getActiveSheet();

            $spreadsheet->getActiveSheet()->setTitle('Resumen');



            $sheet->mergeCells('A1:P1');
            $sheet->setCellValue('A1', 'REPORTE SEMESTRAL DE CLIENTES NUEVOS AL ' . $initial_date);
            $sheet->getStyle('A1')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 16,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]
            ]);


            $sheet->mergeCells('F2:G2');
            $sheet->setCellValue('F2', $monthName6);
            $sheet->getStyle('F2')->applyFromArray([
                'font' => [
                    'color' => array('rgb' => 'FFFFFF'),
                    'bold' => true,
                    'size' => 12,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => array('rgb' => '1E90FF')
                ]
            ]);

            $sheet->mergeCells('H2:I2');
            $sheet->setCellValue('H2', $monthName5);
            $sheet->getStyle('H2')->applyFromArray([
                'font' => [
                    'color' => array('rgb' => 'FFFFFF'),
                    'bold' => true,
                    'size' => 12,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => array('rgb' => '1E90FF')
                ]
            ]);

            $sheet->mergeCells('J2:K2');
            $sheet->setCellValue('J2', $monthName4);
            $sheet->getStyle('J2')->applyFromArray([
                'font' => [
                    'color' => array('rgb' => 'FFFFFF'),
                    'bold' => true,
                    'size' => 12,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => array('rgb' => '1E90FF')
                ]
            ]);

            $sheet->mergeCells('L2:M2');
            $sheet->setCellValue('L2', $monthName3);
            $sheet->getStyle('L2')->applyFromArray([
                'font' => [
                    'color' => array('rgb' => 'FFFFFF'),
                    'bold' => true,
                    'size' => 12,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => array('rgb' => '1E90FF')
                ]
            ]);

            $sheet->mergeCells('N2:O2');
            $sheet->setCellValue('N2', $monthName2);
            $sheet->getStyle('N2')->applyFromArray([
                'font' => [
                    'color' => array('rgb' => 'FFFFFF'),
                    'bold' => true,
                    'size' => 12,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => array('rgb' => '1E90FF')
                ]
            ]);

            $sheet->mergeCells('P2:Q2');
            $sheet->setCellValue('P2', $monthName);
            $sheet->getStyle('P2')->applyFromArray([
                'font' => [
                    'color' => array('rgb' => 'FFFFFF'),
                    'bold' => true,
                    'size' => 12,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => array('rgb' => '1E90FF')
                ]
            ]);




            $sheet->mergeCells('R2:S2');
            $sheet->setCellValue('R2', $monthNameact);
            $sheet->getStyle('R2')->applyFromArray([
                'font' => [
                    'color' => array('rgb' => 'FFFFFF'),
                    'bold' => true,
                    'size' => 12,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => array('rgb' => '1E90FF')
                ]
            ]);



            $sheet->setCellValue('A3', '#');
            $sheet->setCellValue('B3', 'Negocio');
            $sheet->setCellValue('C3', 'Canal');
            $sheet->setCellValue('D3', 'Sub Canal');
            $sheet->setCellValue('E3', 'Ruta');

            $sheet->setCellValue('F3', 'Clientes');
            $sheet->setCellValue('G3', 'TM');

            $sheet->setCellValue('H3', 'Clientes');
            $sheet->setCellValue('I3', 'TM');

            $sheet->setCellValue('J3', 'Clientes');
            $sheet->setCellValue('K3', 'TM');

            $sheet->setCellValue('L3', 'Clientes');
            $sheet->setCellValue('M3', 'TM');

            $sheet->setCellValue('N3', 'Clientes');
            $sheet->setCellValue('O3', 'TM');

            $sheet->setCellValue('P3', 'Clientes');
            $sheet->setCellValue('Q3', 'TM');

            $sheet->setCellValue('R3', 'Mes Actual');
            $sheet->setCellValue('S3', 'TM');

            $sheet->setCellValue('T3', 'Max. Meses');
            $sheet->setCellValue('U3', 'Py Actual');



            $sheet->getStyle('A3:X3')->applyFromArray([
                'font' => [
                    'bold' => true,
                ],
            ]);

            $row_number = 4;

            $sheet->getStyle('A3' . ':E3')->applyFromArray([
                'font' => [
                    'color' => array('rgb' => 'FFFFFF'),
                    'name'      =>  'Calibri',
                    'size'      =>  10,

                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => array('rgb' => '0B2861')
                ]
            ]);

            $sheet->getStyle('F3' . ':S3')->applyFromArray([
                'font' => [
                    'color' => array('rgb' => 'FFFFFF'),
                    'name'      =>  'Calibri',
                    'size'      =>  10,

                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => array('rgb' => '0C8CD1')
                ]
            ]);

            $sheet->getStyle('T3' . ':U3')->applyFromArray([
                'font' => [
                    'color' => array('rgb' => 'FFFFFF'),
                    'name'      =>  'Calibri',
                    'size'      =>  10,

                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => array('rgb' => 'ED5D0A')
                ]
            ]);




            foreach ($response as $index => $element) {
                $index++;

                $sheet->setCellValueExplicit('A' . $row_number, $index, DataType::TYPE_NUMERIC);
                $sheet->setCellValue('B' . $row_number, $element->name);
                $sheet->setCellValue('C' . $row_number, $element->channel_name);
                $sheet->setCellValue('D' . $row_number, $element->sector_name);
                $sheet->setCellValue('E' . $row_number, $element->ruta);
                $sheet->setCellValue('F' . $row_number, $element->mes_seis);
                $sheet->setCellValue('G' . $row_number, $element->tm_seis);
                $sheet->setCellValue('H' . $row_number, $element->mes_cinco);
                $sheet->setCellValue('I' . $row_number, $element->tm_cinco);
                $sheet->setCellValue('J' . $row_number, $element->mes_cuatro);
                $sheet->setCellValue('K' . $row_number, $element->tm_cuatro);
                $sheet->setCellValue('L' . $row_number, $element->mes_tres);
                $sheet->setCellValue('M' . $row_number, $element->tm_tres);
                $sheet->setCellValue('N' . $row_number, $element->mes_dos);
                $sheet->setCellValue('O' . $row_number, $element->tm_dos);
                $sheet->setCellValue('P' . $row_number, $element->mes_ant);
                $sheet->setCellValue('Q' . $row_number, $element->tm_ant);
                $sheet->setCellValue('R' . $row_number, $element->clientes);
                $sheet->setCellValue('S' . $row_number, $element->tm);
                $sheet->setCellValue('T' . $row_number, $element->mes_max);
                $sheet->setCellValue('U' . $row_number, $element->mes_proy);



                $sheet->getStyle('F' . $row_number)->getNumberFormat()->setFormatCode('0');
                $sheet->getStyle('G' . $row_number)->getNumberFormat()->setFormatCode('0.00');
                $sheet->getStyle('H' . $row_number)->getNumberFormat()->setFormatCode('0');
                $sheet->getStyle('I' . $row_number)->getNumberFormat()->setFormatCode('0.00');
                $sheet->getStyle('J' . $row_number)->getNumberFormat()->setFormatCode('0');
                $sheet->getStyle('K' . $row_number)->getNumberFormat()->setFormatCode('0.00');
                $sheet->getStyle('L' . $row_number)->getNumberFormat()->setFormatCode('0');
                $sheet->getStyle('M' . $row_number)->getNumberFormat()->setFormatCode('0.00');
                $sheet->getStyle('N' . $row_number)->getNumberFormat()->setFormatCode('0');
                $sheet->getStyle('O' . $row_number)->getNumberFormat()->setFormatCode('0.00');
                $sheet->getStyle('P' . $row_number)->getNumberFormat()->setFormatCode('0');
                $sheet->getStyle('Q' . $row_number)->getNumberFormat()->setFormatCode('0.00');
                $sheet->getStyle('R' . $row_number)->getNumberFormat()->setFormatCode('0');
                $sheet->getStyle('S' . $row_number)->getNumberFormat()->setFormatCode('0.00');
                $sheet->getStyle('T' . $row_number)->getNumberFormat()->setFormatCode('0');
                $sheet->getStyle('U' . $row_number)->getNumberFormat()->setFormatCode('0');
                //	$sheet->getStyle('V' . $row_number)->getNumberFormat()->setFormatCode('0');
                //	$sheet->getStyle('W' . $row_number)->getNumberFormat()->setFormatCode('0.00' . '%');

                $row_number++;
            }

            $sheet->getColumnDimension('A')->setAutoSize(true);
            $sheet->getColumnDimension('B')->setAutoSize(true);
            $sheet->getColumnDimension('C')->setAutoSize(true);
            $sheet->getColumnDimension('D')->setAutoSize(true);
            $sheet->getColumnDimension('E')->setAutoSize(true);
            $sheet->getColumnDimension('F')->setAutoSize(true);
            $sheet->getColumnDimension('G')->setAutoSize(true);
            $sheet->getColumnDimension('H')->setAutoSize(true);
            $sheet->getColumnDimension('I')->setAutoSize(true);
            $sheet->getColumnDimension('J')->setAutoSize(true);
            $sheet->getColumnDimension('K')->setAutoSize(true);
            $sheet->getColumnDimension('L')->setAutoSize(true);
            $sheet->getColumnDimension('M')->setAutoSize(true);
            $sheet->getColumnDimension('N')->setAutoSize(true);
            $sheet->getColumnDimension('M')->setAutoSize(true);
            $sheet->getColumnDimension('O')->setAutoSize(true);
            $sheet->getColumnDimension('P')->setAutoSize(true);

            //SEGUNDA HOJA


            $newSheet = new Worksheet($spreadsheet, 'Detalle');
            $spreadsheet->addSheet($newSheet);

            $spreadsheet->setActiveSheetIndexByName('Detalle');

            $newSheet->mergeCells('A1:P1');
            $newSheet->setCellValue('A1', 'REPORTE SEMESTRAL DE CLIENTES NUEVOS AL ' . $initial_date);
            $newSheet->getStyle('A1')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 16,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]
            ]);

            /*
			$newSheet->mergeCells('G2:H2');
			$newSheet->setCellValue('G2', $monthName6);
			$newSheet->getStyle('G2')->applyFromArray([
				'font' => [
					'color' => array('rgb' => 'FFFFFF'),
					'bold' => true,
					'size' => 12,
				],
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
				],
				'fill' => [
					'fillType' => Fill::FILL_SOLID,
					'startColor' => array('rgb' => '1E90FF')
				]
			]);

			$newSheet->mergeCells('I2:J2');
			$newSheet->setCellValue('I2', $monthName5);
			$newSheet->getStyle('I2')->applyFromArray([
				'font' => [
					'color' => array('rgb' => 'FFFFFF'),
					'bold' => true,
					'size' => 12,
				],
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
				],
				'fill' => [
					'fillType' => Fill::FILL_SOLID,
					'startColor' => array('rgb' => '1E90FF')
				]
			]);

			$newSheet->mergeCells('K2:L2');
			$newSheet->setCellValue('L2', $monthName4);
			$newSheet->getStyle('L2')->applyFromArray([
				'font' => [
					'color' => array('rgb' => 'FFFFFF'),
					'bold' => true,
					'size' => 12,
				],
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
				],
				'fill' => [
					'fillType' => Fill::FILL_SOLID,
					'startColor' => array('rgb' => '1E90FF')
				]
			]);

			$newSheet->mergeCells('M2:N2');
			$newSheet->setCellValue('M2', $monthName3);
			$newSheet->getStyle('M2')->applyFromArray([
				'font' => [
					'color' => array('rgb' => 'FFFFFF'),
					'bold' => true,
					'size' => 12,
				],
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
				],
				'fill' => [
					'fillType' => Fill::FILL_SOLID,
					'startColor' => array('rgb' => '1E90FF')
				]
			]);

			$newSheet->mergeCells('O2:P2');
			$newSheet->setCellValue('O2', $monthName2);
			$newSheet->getStyle('O2')->applyFromArray([
				'font' => [
					'color' => array('rgb' => 'FFFFFF'),
					'bold' => true,
					'size' => 12,
				],
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
				],
				'fill' => [
					'fillType' => Fill::FILL_SOLID,
					'startColor' => array('rgb' => '1E90FF')
				]
			]);

			$newSheet->mergeCells('Q2:R2');
			$newSheet->setCellValue('Q2', $monthName);
			$newSheet->getStyle('Q2')->applyFromArray([
				'font' => [
					'color' => array('rgb' => 'FFFFFF'),
					'bold' => true,
					'size' => 12,
				],
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
				],
				'fill' => [
					'fillType' => Fill::FILL_SOLID,
					'startColor' => array('rgb' => '1E90FF')
				]
			]);




			$newSheet->mergeCells('S2:T2');
			$newSheet->setCellValue('S2', $monthNameact);
			$newSheet->getStyle('S2')->applyFromArray([
				'font' => [
					'color' => array('rgb' => 'FFFFFF'),
					'bold' => true,
					'size' => 12,
				],
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
				],
				'fill' => [
					'fillType' => Fill::FILL_SOLID,
					'startColor' => array('rgb' => '1E90FF')
				]
			]);

*/

            $newSheet->setCellValue('A3', '#');
            $newSheet->setCellValue('B3', 'Negocio');
            $newSheet->setCellValue('C3', 'Canal');
            $newSheet->setCellValue('D3', 'Sub Canal');
            $newSheet->setCellValue('E3', 'Ruta');
            $newSheet->setCellValue('F3', 'Código');
            $newSheet->setCellValue('G3', 'Cliente');
            $newSheet->setCellValue('H3', 'TM');
            $newSheet->setCellValue('I3', 'Fecha de Creación');
            $newSheet->setCellValue('J3', 'Mes');

            //$newSheet->setCellValue('I3', 'S./');
            //$newSheet->setCellValue('J3', 'TM');

            //$newSheet->setCellValue('K3', 'S./');
            //$newSheet->setCellValue('L3', 'TM');

            //$newSheet->setCellValue('M3', 'S./');
            //$newSheet->setCellValue('N3', 'TM');

            //$newSheet->setCellValue('O3', 'S./');
            //$newSheet->setCellValue('P3', 'TM');

            //$newSheet->setCellValue('Q3', 'S./');
            //$newSheet->setCellValue('R3', 'TM');

            //$newSheet->setCellValue('S3', 'Mes Actual S./');
            //$newSheet->setCellValue('T3', 'TM');

            //  $sheet->setCellValue('L3', 'Día Final Anterior');
            //	$sheet->setCellValue('M3', 'Día Inicial Anterior');


            $newSheet->getStyle('A3:W3')->applyFromArray([
                'font' => [
                    'bold' => true,
                ],
            ]);

            $row_number = 4;

            $newSheet->getStyle('A3' . ':I3')->applyFromArray([
                'font' => [
                    'color' => array('rgb' => 'FFFFFF'),
                    'name'      =>  'Calibri',
                    'size'      =>  10,

                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => array('rgb' => '0B2861')
                ]
            ]);

            /*
			$newSheet->getStyle('G3' . ':T3')->applyFromArray([
				'font' => [
					'color' => array('rgb' => 'FFFFFF'),
					'name'      =>  'Calibri',
					'size'      =>  10,

				],
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
				],
				'fill' => [
					'fillType' => Fill::FILL_SOLID,
					'startColor' => array('rgb' => '0C8CD1')
				]
			]);

*/
            foreach ($response_2 as $index => $element) {
                $index++;

                $newSheet->setCellValueExplicit('A' . $row_number, $index, DataType::TYPE_NUMERIC);
                $newSheet->setCellValue('B' . $row_number, $element->name);
                $newSheet->setCellValue('C' . $row_number, $element->canal_name);
                $newSheet->setCellValue('D' . $row_number, $element->subcanal_name);
                $newSheet->setCellValue('E' . $row_number, $element->ruta);
                $newSheet->setCellValue('F' . $row_number, $element->codigo);
                $newSheet->setCellValue('G' . $row_number, $element->client_name);
                $newSheet->setCellValue('H' . $row_number, $element->tmd);
                $newSheet->setCellValue('I' . $row_number, $element->one_day);
                $newSheet->setCellValue('J' . $row_number, $element->mes_day_var);



                $newSheet->getStyle('H' . $row_number)->getNumberFormat()->setFormatCode('0.00');
                //$newSheet->getStyle('G' . $row_number)->getNumberFormat()->setFormatCode('0.00');
                //$newSheet->getStyle('H' . $row_number)->getNumberFormat()->setFormatCode('0');
                //$newSheet->getStyle('I' . $row_number)->getNumberFormat()->setFormatCode('0.00');
                //$newSheet->getStyle('J' . $row_number)->getNumberFormat()->setFormatCode('0');
                //$newSheet->getStyle('K' . $row_number)->getNumberFormat()->setFormatCode('0.00');
                //$newSheet->getStyle('L' . $row_number)->getNumberFormat()->setFormatCode('0');
                //$newSheet->getStyle('M' . $row_number)->getNumberFormat()->setFormatCode('0.00');
                //$newSheet->getStyle('N' . $row_number)->getNumberFormat()->setFormatCode('0');
                //$newSheet->getStyle('O' . $row_number)->getNumberFormat()->setFormatCode('0.00');
                //$newSheet->getStyle('P' . $row_number)->getNumberFormat()->setFormatCode('0');
                //$newSheet->getStyle('Q' . $row_number)->getNumberFormat()->setFormatCode('0.00');
                //$newSheet->getStyle('R' . $row_number)->getNumberFormat()->setFormatCode('0');
                //$newSheet->getStyle('S' . $row_number)->getNumberFormat()->setFormatCode('0.00');

                $row_number++;
            }

            $newSheet->getColumnDimension('A')->setAutoSize(true);
            $newSheet->getColumnDimension('B')->setAutoSize(true);
            $newSheet->getColumnDimension('C')->setAutoSize(true);
            $newSheet->getColumnDimension('D')->setAutoSize(true);
            $newSheet->getColumnDimension('E')->setAutoSize(true);
            $newSheet->getColumnDimension('F')->setAutoSize(true);
            $newSheet->getColumnDimension('G')->setAutoSize(true);
            $newSheet->getColumnDimension('H')->setAutoSize(true);
            $newSheet->getColumnDimension('I')->setAutoSize(true);
            $newSheet->getColumnDimension('J')->setAutoSize(true);
            //$newSheet->getColumnDimension('K')->setAutoSize(true);
            //$newSheet->getColumnDimension('L')->setAutoSize(true);
            //$newSheet->getColumnDimension('M')->setAutoSize(true);
            //$newSheet->getColumnDimension('N')->setAutoSize(true);
            //$newSheet->getColumnDimension('M')->setAutoSize(true);
            //$newSheet->getColumnDimension('O')->setAutoSize(true);
            //$newSheet->getColumnDimension('P')->setAutoSize(true);


            $writer = new Xls($spreadsheet);
            return $writer->save('php://output');
        } else {
            return response()->json($response);
        }
    }
}
