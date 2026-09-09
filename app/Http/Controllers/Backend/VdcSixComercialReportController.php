<?php

namespace App\Http\Controllers\Backend;


use App\Http\Controllers\Controller;
use App\Client;
use App\ClientRoute;
use App\PriceList;
use App\SaleDetail;
use Carbon\CarbonImmutable;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Style\Fill;

use stdClass;


class VdcSixComercialReportController extends Controller
{
    public function index()
    {

        $current_date = date(DATE_ATOM, mktime(0, 0, 0));
        $client_routes = ClientRoute::select('id', 'short_name')->where('type', 3)->get();
        return view('backend.vdc_six_comercial_report')->with(compact('current_date', 'client_routes'));
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


        if ($route_id == 0) {
            $ruta = [34, 35, 36, 37, 38, 45];
        } else if ($route_id == 34) {
            $ruta = [34];
        } else if ($route_id == 35) {
            $ruta = [35];
        } else if ($route_id == 36) {
            $ruta = [36];
        } else if ($route_id == 37) {
            $ruta = [37];
        } else if ($route_id == 38) {
            $ruta = [38];
        } else if ($route_id == 45) {
            $ruta = [45];
        }


        $initial_date = CarbonImmutable::createFromDate(request('model.initial_date'))->startOfDay()->format('Y-m-d');
        $mes_date = CarbonImmutable::createFromDate(request('model.initial_date'))->startOfDay()->format('Y-m-01');
        $day = date('j', strtotime($initial_date));
        $mesActual = date('n', strtotime($initial_date));
        $anioActual = date('Y', strtotime($initial_date));
        $number = cal_days_in_month(CAL_GREGORIAN, $mesActual, $anioActual);




        $elements = Client::leftjoin('client_routes', 'clients.route_id', '=', 'client_routes.id')
            ->leftjoin('client_addresses', function ($join) {
                $join->on('clients.id', '=', 'client_addresses.client_id')
                    ->where('client_addresses.address_type_id', 1);
            })
            ->whereIn('clients.route_id', $ruta)
            ->leftjoin('ubigeos', 'client_addresses.ubigeo_id', '=', 'ubigeos.id')
            ->leftjoin('client_channels', 'clients.channel_id', '=', 'client_channels.id')
            ->leftjoin('client_types', 'clients.business_type', '=', 'client_types.id')
            ->select(
                'clients.id as client_id',
                'clients.route_id as ruta_id',
                'client_routes.short_name as ruta',
                'clients.business_name as name',
                'client_channels.name as subcanal',
                'client_types.name as negocio',
                'ubigeos.district as district',
                'clients.potencial as potencial',
                'clients.frecuencia as frecuencia_cantidad',
                'clients.frecuencia_id as frecuencia_tiempo'
            )
            //	->when($route_id, function($query, $route_id) {
            //		return $query->where('clients.route_id', $route_id);
            //	})
            //    ->groupBy('clients.route_id')
            ->orderBy('ruta_id')
            ->get();
        $response = [];

        $totals_tm = 0;
        $totals_mes_ant = 0;
        $totals_mes_proy = 0;
        $totals_mes_dos = 0;
        $totals_mes_tres = 0;
        $totals_mes_cuatro = 0;
        $totals_mes_cinco = 0;
        $totals_mes_seis = 0;
        $totals_mes_max = 0;

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



            $monthName  = date("F", strtotime($fechaMesPasado));
            $monthName2 = date("F", strtotime($fechaMesPasado2));
            $monthName3 = date("F", strtotime($fechaMesPasado3));
            $monthName4 = date("F", strtotime($fechaMesPasado4));
            $monthName5 = date("F", strtotime($fechaMesPasado5));
            $monthName6 = date("F", strtotime($fechaMesPasado6));




            $saledetail->ruta = $saledetail['ruta'];
            $saledetail->ruta_id = $saledetail['ruta_id'];
            $saledetail->name = $saledetail['name'];
            $saledetail->codigo = $saledetail['client_id'];
            $saledetail->district = $saledetail['district'];



            $tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
                ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
                ->where('sales.sale_date', '>=', $mes_date)
                ->where('sales.sale_date', '<=', $initial_date)
                ->where('sales.client_id', $saledetail['client_id'])
                ->whereNotIn('sales.warehouse_document_type_id', [2, 3, 8, 9, 10, 11, 14, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29])
                ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258, 13233, 14258])
                ->select('kg')
                ->sum('kg');


            $tm_last = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
                ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
                ->where('sales.sale_date', '>=',  $price_mes)
                ->where('sales.sale_date', '<=',  $fechaMesPasado)
                ->where('sales.client_id', $saledetail['client_id'])
                ->whereNotIn('sales.warehouse_document_type_id', [2, 3, 8, 9, 10, 11, 14, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29])
                ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258, 13233, 14258])
                ->select('kg')
                ->sum('kg');

            $tm_dos = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
                ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
                ->where('sales.sale_date', '>=',  $price_mes2)
                ->where('sales.sale_date', '<=',  $fechaMesPasado2)
                ->where('sales.client_id', $saledetail['client_id'])
                ->whereNotIn('sales.warehouse_document_type_id', [2, 3, 8, 9, 10, 11, 14, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29])
                ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258, 13233, 14258])
                ->select('kg')
                ->sum('kg');

            $tm_tres = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
                ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
                ->where('sales.sale_date', '>=',  $price_mes3)
                ->where('sales.sale_date', '<=',  $fechaMesPasado3)
                ->where('sales.client_id', $saledetail['client_id'])
                ->whereNotIn('sales.warehouse_document_type_id', [2, 3, 8, 9, 10, 11, 14, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29])
                ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258, 13233, 14258])
                ->select('kg')
                ->sum('kg');

            $tm_cuatro = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
                ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
                ->where('sales.sale_date', '>=',  $price_mes4)
                ->where('sales.sale_date', '<=',  $fechaMesPasado4)
                ->where('sales.client_id', $saledetail['client_id'])
                ->whereNotIn('sales.warehouse_document_type_id', [2, 3, 8, 9, 10, 11, 14, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29])
                ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258, 13233, 14258])
                ->select('kg')
                ->sum('kg');

            $tm_cinco = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
                ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
                ->where('sales.sale_date', '>=',  $price_mes5)
                ->where('sales.sale_date', '<=',  $fechaMesPasado5)
                ->where('sales.client_id', $saledetail['client_id'])
                ->whereNotIn('sales.warehouse_document_type_id', [2, 3, 8, 9, 10, 11, 14, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29])
                ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258, 13233, 14258])
                ->select('kg')
                ->sum('kg');

            $tm_seis = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
                ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
                ->where('sales.sale_date', '>=',  $price_mes6)
                ->where('sales.sale_date', '<=',  $fechaMesPasado6)
                ->where('sales.client_id', $saledetail['client_id'])
                ->whereNotIn('sales.warehouse_document_type_id', [2, 3, 8, 9, 10, 11, 14, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29])
                ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258, 13233, 14258])
                ->select('kg')
                ->sum('kg');

            $rinde = PriceList::leftjoin('clients', 'price_lists.client_id', '=', 'clients.id')
                ->where('price_lists.initial_effective_date', '>=',  $price_mes)
                ->where('price_lists.client_id', $saledetail['client_id'])
                ->where('price_lists.state', '=', 1)
                ->where('price_lists.article_id', '=', 2)
                ->select('price_igv')
                ->avg('price_igv');

            $maqui = PriceList::leftjoin('clients', 'price_lists.client_id', '=', 'clients.id')
                ->where('price_lists.client_id', $saledetail['client_id'])
                ->where('price_lists.initial_effective_date', '>=',  $price_mes)
                ->where('price_lists.state', '=', 1)
                ->where('price_lists.article_id', '=', 18)
                ->select('price_igv')
                ->avg('price_igv');

            $plus = PriceList::leftjoin('clients', 'price_lists.client_id', '=', 'clients.id')
                ->where('price_lists.client_id', $saledetail['client_id'])
                ->where('price_lists.initial_effective_date', '>=',  $price_mes)
                ->where('price_lists.state', '=', 1)
                ->where('price_lists.article_id', '=', 10)
                ->select('price_igv')
                ->avg('price_igv');

            $premium_5 = PriceList::leftjoin('clients', 'price_lists.client_id', '=', 'clients.id')
                ->where('price_lists.client_id', $saledetail['client_id'])
                //->where('price_lists.initial_effective_date', '>=',  $price_mes)
                ->where('price_lists.state', '=', 1)
                ->where('price_lists.article_id', '=', 5)
                ->orderBy('price_lists.id', 'desc')
                //->select('price_igv')
                ->value('price_igv');

            $premium_10 = PriceList::leftjoin('clients', 'price_lists.client_id', '=', 'clients.id')
                ->where('price_lists.client_id', $saledetail['client_id'])
                //->where('price_lists.initial_effective_date', '>=',  $price_mes)
                ->where('price_lists.state', '=', 1)
                ->where('price_lists.article_id', '=', 6)
                ->orderBy('price_lists.id', 'desc')
                //->select('price_igv')
                ->value('price_igv');

            $premium_15 = PriceList::leftjoin('clients', 'price_lists.client_id', '=', 'clients.id')
                ->where('price_lists.client_id', $saledetail['client_id'])
                //->where('price_lists.initial_effective_date', '>=',  $price_mes)
                ->where('price_lists.state', '=', 1)
                ->where('price_lists.article_id', '=', 7)
                ->orderBy('price_lists.id', 'desc')
                //->select('price_igv')
                ->value('price_igv');

            $premium_45 = PriceList::leftjoin('clients', 'price_lists.client_id', '=', 'clients.id')
                ->where('price_lists.client_id', $saledetail['client_id'])
                //->where('price_lists.initial_effective_date', '>=',  $price_mes)
                ->where('price_lists.state', '=', 1)
                ->where('price_lists.article_id', '=', 8)
                ->orderBy('price_lists.id', 'desc')
                //->select('price_igv')
                ->value('price_igv');

            $chamba = PriceList::leftjoin('clients', 'price_lists.client_id', '=', 'clients.id')
                ->where('price_lists.client_id', $saledetail['client_id'])
                ->where('price_lists.initial_effective_date', '>=',  $price_mes)
                ->where('price_lists.state', '=', 10369)
                ->where('price_lists.article_id', '=', 2)
                ->select('price_igv')
                ->avg('price_igv');

            $poder = PriceList::leftjoin('clients', 'price_lists.client_id', '=', 'clients.id')
                ->where('price_lists.client_id', $saledetail['client_id'])
                ->where('price_lists.initial_effective_date', '>=',  $price_mes)
                ->where('price_lists.state', '=', 1)
                ->where('price_lists.article_id', '=', 10377)
                ->select('price_igv')
                ->avg('price_igv');


            $elgas = PriceList::leftjoin('clients', 'price_lists.client_id', '=', 'clients.id')
                ->where('price_lists.client_id', $saledetail['client_id'])
                ->where('price_lists.initial_effective_date', '>=',  $price_mes)
                ->where('price_lists.state', '=', 1)
                ->where('price_lists.article_id', '=', 14)
                ->select('price_igv')
                ->avg('price_igv');


            $saledetail->tm = $tm_fin / 1000;
            $saledetail->mes_act = $saledetail['tm'];
            $saledetail->mes_proy =  ($saledetail['tm'] / $day) * $number;
            $saledetail->mes_ant = $tm_last / 1000;
            $saledetail->mes_dos = $tm_dos / 1000;
            $saledetail->mes_tres = $tm_tres / 1000;
            $saledetail->mes_cuatro = $tm_cuatro / 1000;
            $saledetail->mes_cinco = $tm_cinco / 1000;
            $saledetail->mes_seis = $tm_seis / 1000;

            $saledetail->rinde = $rinde;
            $saledetail->plus = $plus;
            $saledetail->maqui = $maqui;
            $saledetail->premium_5 = $premium_5;
            $saledetail->premium_10 = $premium_10;
            $saledetail->premium_15 = $premium_15;
            $saledetail->premium_45 = $premium_45;
            $saledetail->elgas = $elgas;
            $saledetail->poder = $poder;
            $saledetail->chamba = $chamba;


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


            $totals_tm += $saledetail['tm'];
            $totals_mes_ant += $saledetail['mes_ant'];
            $totals_mes_dos += $saledetail['mes_dos'];
            $totals_mes_tres += $saledetail['mes_tres'];
            $totals_mes_cuatro += $saledetail['mes_cuatro'];
            $totals_mes_cinco += $saledetail['mes_cinco'];
            $totals_mes_seis += $saledetail['mes_seis'];
            $totals_mes_proy += $saledetail['mes_proy'];
            $totals_mes_max += $saledetail['mes_max'];

            $response[] = $saledetail;
        }


        $totals = new stdClass();
        $totals->ruta = 'TOTAL';
        $totals->codigo = '';
        $totals->name = '';
        $totals->district = '';
        $totals->potencial = '';
        $totals->frecuencia_cantidad = '';
        $totals->subcanal = '';
        $totals->negocio = '';
        $totals->frecuencia_tiempo = '';
        $totals->mes_ant = $totals_mes_ant;
        $totals->tm = $totals_tm;
        $totals->mes_ant = $totals_mes_ant;
        $totals->mes_dos = $totals_mes_dos;
        $totals->mes_tres =  $totals_mes_tres;
        $totals->mes_cuatro =  $totals_mes_cuatro;
        $totals->mes_cinco =  $totals_mes_cinco;
        $totals->mes_seis = $totals_mes_seis;
        $totals->mes_proy =  $totals_mes_proy;
        $totals->mes_max = $totals_mes_max;
        $totals->rinde = '';
        $totals->premium_5 = '';
        $totals->premium_10 = '';
        $totals->premium_15 = '';
        $totals->premium_45 = '';
        $totals->maqui = '';
        $totals->elgas = '';
        $totals->plus = '';
        $totals->poder = '';
        $totals->chamba = '';
        $totals->diff = '';
        $totals->porc_diff = '';



        $response[] = $totals;

        if ($export) {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->mergeCells('A1:P1');
            //$sheet->setCellValue('A1', 'REPORTE SEMESTRAL DE PERDIDOS AL ' . $initial_date);
            $sheet->getStyle('A1')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 16,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]
            ]);


            $sheet->setCellValue('A3', '#');
            $sheet->setCellValue('B3', 'Ruta');
            $sheet->setCellValue('C3', 'Codigo');
            $sheet->setCellValue('D3', 'Nombre');
            $sheet->setCellValue('E3', 'Distrito');
            $sheet->setCellValue('F3', 'Subcanal');
            $sheet->setCellValue('G3', 'Negocio');
            $sheet->setCellValue('H3', 'Potencial');
            $sheet->setCellValue('I3', 'F. Cantidad');
            $sheet->setCellValue('J3', 'F. Tiempo');
            $sheet->setCellValue('K3', $monthName6);
            $sheet->setCellValue('L3', $monthName5);
            $sheet->setCellValue('M3', $monthName4);
            $sheet->setCellValue('N3', $monthName3);
            $sheet->setCellValue('O3', $monthName2);
            $sheet->setCellValue('P3', $monthName);
            $sheet->setCellValue('Q3', 'Mes Actual');
            $sheet->setCellValue('R3', 'Max. Meses');
            $sheet->setCellValue('S3', 'Py Actual');
            $sheet->setCellValue('T3', 'Dif');
            $sheet->setCellValue('U3', '% Dif');
            $sheet->setCellValue('V3', 'Rinde 10k');
            $sheet->setCellValue('W3', 'Maqui 10k');
            $sheet->setCellValue('X3', 'Plus 10k');
            $sheet->setCellValue('Y3', 'Premium 5k');
            $sheet->setCellValue('Z3', 'Premium 10k');
            $sheet->setCellValue('AA3', 'Premium 15k');
            $sheet->setCellValue('AB3', 'Premium 45k');
            $sheet->setCellValue('AC3', 'El Gas 10k');
            $sheet->setCellValue('AD3', 'Poder 10k');
            $sheet->setCellValue('AE3', 'Chamba 10k');
            //  $sheet->setCellValue('L3', 'Día Final Anterior');
            //	$sheet->setCellValue('M3', 'Día Inicial Anterior');


            $sheet->getStyle('A3:P3')->applyFromArray([
                'font' => [
                    'bold' => true,
                ],
            ]);



            $row_number = 4;

            $sheet->getStyle('A3' . ':J3')->applyFromArray([
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

            $sheet->getStyle('K3' . ':P3')->applyFromArray([
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

            $sheet->getStyle('Q3' . ':Q3')->applyFromArray([
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

            $sheet->getStyle('R3' . ':R3')->applyFromArray([
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
                    'startColor' => array('rgb' => '11B464')
                ]
            ]);

            $sheet->getStyle('S3' . ':AE3')->applyFromArray([
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
                    'startColor' => array('rgb' => '808000')
                ]
            ]);




            foreach ($response as $index => $element) {
                $index++;



                $sheet->setCellValueExplicit('A' . $row_number, $index, DataType::TYPE_NUMERIC);
                $sheet->setCellValue('B' . $row_number, $element->ruta);
                $sheet->setCellValue('C' . $row_number, $element->codigo);
                $sheet->setCellValue('D' . $row_number, $element->name);
                $sheet->setCellValue('E' . $row_number, $element->district);
                $sheet->setCellValue('F' . $row_number, $element->subcanal);
                $sheet->setCellValue('G' . $row_number, $element->negocio);
                $sheet->setCellValue('H' . $row_number, $element->potencial);
                $sheet->setCellValue('I' . $row_number, $element->frecuencia_cantidad);
                $sheet->setCellValue('J' . $row_number, $element->frecuencia_tiempo);
                $sheet->setCellValue('K' . $row_number, $element->mes_seis);
                $sheet->setCellValue('L' . $row_number, $element->mes_cinco);
                $sheet->setCellValue('M' . $row_number, $element->mes_cuatro);
                $sheet->setCellValue('N' . $row_number, $element->mes_tres);
                $sheet->setCellValue('O' . $row_number, $element->mes_dos);
                $sheet->setCellValue('P' . $row_number, $element->mes_ant);
                $sheet->setCellValue('Q' . $row_number, $element->tm);
                $sheet->setCellValue('R' . $row_number, $element->mes_max);
                $sheet->setCellValue('S' . $row_number, $element->mes_proy);
                $sheet->setCellValue('T' . $row_number, $element->diff);
                $sheet->setCellValue('U' . $row_number, $element->porc_diff);
                $sheet->setCellValue('V' . $row_number, $element->rinde);
                $sheet->setCellValue('W' . $row_number, $element->maqui);
                $sheet->setCellValue('X' . $row_number, $element->plus);
                $sheet->setCellValue('Y' . $row_number, $element->premium_5);
                $sheet->setCellValue('Z' . $row_number, $element->premium_10);
                $sheet->setCellValue('AA' . $row_number, $element->premium_15);
                $sheet->setCellValue('AB' . $row_number, $element->premium_45);
                $sheet->setCellValue('AC' . $row_number, $element->elgas);
                $sheet->setCellValue('AD' . $row_number, $element->poder);
                $sheet->setCellValue('AE' . $row_number, $element->chamba);



                $sheet->getStyle('K' . $row_number)->getNumberFormat()->setFormatCode('0.00');
                $sheet->getStyle('L' . $row_number)->getNumberFormat()->setFormatCode('0.00');
                $sheet->getStyle('M' . $row_number)->getNumberFormat()->setFormatCode('0.00');
                $sheet->getStyle('N' . $row_number)->getNumberFormat()->setFormatCode('0.00');
                $sheet->getStyle('O' . $row_number)->getNumberFormat()->setFormatCode('0.00');
                $sheet->getStyle('P' . $row_number)->getNumberFormat()->setFormatCode('0.00');
                $sheet->getStyle('Q' . $row_number)->getNumberFormat()->setFormatCode('0.00');
                $sheet->getStyle('R' . $row_number)->getNumberFormat()->setFormatCode('0.00');
                $sheet->getStyle('S' . $row_number)->getNumberFormat()->setFormatCode('0.00');
                $sheet->getStyle('T' . $row_number)->getNumberFormat()->setFormatCode('0.00');
                $sheet->getStyle('U' . $row_number)->getNumberFormat()->setFormatCode('0.00' . '%');
                $sheet->getStyle('V' . $row_number)->getNumberFormat()->setFormatCode('0.00');
                $sheet->getStyle('W' . $row_number)->getNumberFormat()->setFormatCode('0.00');
                $sheet->getStyle('X' . $row_number)->getNumberFormat()->setFormatCode('0.00');
                $sheet->getStyle('Y' . $row_number)->getNumberFormat()->setFormatCode('0.0');
                $sheet->getStyle('Z' . $row_number)->getNumberFormat()->setFormatCode('0.0');
                $sheet->getStyle('AA' . $row_number)->getNumberFormat()->setFormatCode('0.0');
                $sheet->getStyle('AB' . $row_number)->getNumberFormat()->setFormatCode('0.0');
                $sheet->getStyle('AC' . $row_number)->getNumberFormat()->setFormatCode('0.00');
                $sheet->getStyle('AD' . $row_number)->getNumberFormat()->setFormatCode('0.00');
                $sheet->getStyle('AE' . $row_number)->getNumberFormat()->setFormatCode('0.00');


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
            $sheet->getColumnDimension('Q')->setAutoSize(true);
            $sheet->getColumnDimension('R')->setAutoSize(true);
            $sheet->getColumnDimension('S')->setAutoSize(true);
            $sheet->getColumnDimension('T')->setAutoSize(true);
            $sheet->getColumnDimension('U')->setAutoSize(true);
            $sheet->getColumnDimension('V')->setAutoSize(true);
            $sheet->getColumnDimension('W')->setAutoSize(true);
            $sheet->getColumnDimension('X')->setAutoSize(true);
            $sheet->getColumnDimension('Y')->setAutoSize(true);
            $sheet->getColumnDimension('Z')->setAutoSize(true);
            $sheet->getColumnDimension('AA')->setAutoSize(true);
            $sheet->getColumnDimension('AB')->setAutoSize(true);
            $sheet->getColumnDimension('AC')->setAutoSize(true);
            $sheet->getColumnDimension('AD')->setAutoSize(true);
            $sheet->getColumnDimension('AE')->setAutoSize(true);

            $writer = new Xls($spreadsheet);
            return $writer->save('php://output');
        } else {
            return response()->json($response);
        }
    }
}
