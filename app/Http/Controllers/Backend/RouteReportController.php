<?php

namespace App\Http\Controllers\Backend;

use App\Client;
use App\Company;
use App\Article;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SaleDetail;
use App\Sale;
use App\BusinessUnit;
use App\ClientRoute;
use App\WarehouseMovement;
use Carbon\CarbonImmutable;
use Carbon\Carbon;
use strtotime;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use stdClass;


class RouteReportController extends Controller
{
    public function index()
    {
        $client_routes = ClientRoute::select('id', 'name')->get();
        $current_date = date(DATE_ATOM, mktime(0, 0, 0));
        $companies = Company::select('id', 'name')->get();
        return view('backend.route_report')->with(compact('client_routes', 'current_date', 'companies'));
    }

    public function validateForm()
    {
        $messages = [
            'initial_date.required'    => 'Debe seleccionar una Fecha inicial.',
            'final_date.required'    => 'Debe seleccionar una Fecha final.',
            'company_id.required'    => 'Debe seleccionar una Compañía.',
        ];

        $rules = [
            'initial_date'    => 'required',
            'final_date'    => 'required',
            'company_id'    => 'required',
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

        $initial_date = CarbonImmutable::createFromDate(request('model.initial_date'))->startOfDay()->format('Y-m-d H:i:s');
        $final_date = CarbonImmutable::createFromDate(request('model.final_date'))->endOfDay()->format('Y-m-d H:i:s');
        $business_unit_id = request('model.business_unit_id');
        $client_id = request('model.client_id');
        $company_id = request('model.company_id');
        $client_route_id = request('model.client_route_id');

        $price_year = CarbonImmutable::createFromDate(request('model.initial_date'))->startOfDay()->format('Y');
        $price_mes = CarbonImmutable::createFromDate(request('model.initial_date'))->startOfDay()->format('m');

        if ($business_unit_id == 0) {
            $business = [1, 2, 3];
        } else if ($business_unit_id == 1) {
            $business = [1];
        } else if ($business_unit_id == 2) {
            $business = [2];
        } else if ($business_unit_id == 3) {
            $business = [3];
        }

        /*-COSTO GLP-*/

        $year_prev = ($price_mes == '01') ? $price_year - 1 : $price_year;
        $mes_prev = ($price_mes == '01') ? '12' : $price_mes - 1;


        $movements_g = WarehouseMovement::join('warehouse_movement_details', 'warehouse_movement_details.warehouse_movement_id', '=', 'warehouse_movements.id')
            ->where(function ($query) use ($price_year, $price_mes, $initial_date) {
                $query->where('warehouse_movements.price_año', $price_year)
                    ->where('warehouse_movements.price_mes', $price_mes)
                    ->where('warehouse_movements.movement_type_id', 30)
                    ->where('warehouse_movements.origin', 27)
                    ->where(DB::Raw('DATE_FORMAT(warehouse_movements.created_at, "%Y-%m-%d") '), '<=', $initial_date);
            })
            ->orWhere(function ($query) use ($year_prev, $mes_prev, $initial_date) {
                $query->where('warehouse_movements.price_año', $year_prev)
                    ->where('warehouse_movements.price_mes', $mes_prev)
                    ->where('warehouse_movement_details.digit2', '<>', null)
                    ->where('warehouse_movement_details.digit2', '<>', 0)
                    ->where('warehouse_movements.movement_type_id', 30)
                    ->where('warehouse_movements.origin', 27)
                    ->where(DB::Raw('DATE_FORMAT(warehouse_movements.created_at, "%Y-%m-%d") '), '<=', $initial_date);
            })
            ->where('warehouse_movements.movement_class_id', 2)
            //	->where(DB::Raw('DATE_FORMAT(warehouse_movements.created_at, "%Y-%m-%d") '), '<=', $initial_date)
            ->select(
                'warehouse_movement_details.id',
                'warehouse_movements.warehouse_type_id',
                'warehouse_movements.movement_type_id',
                'warehouse_movements.referral_serie_number',
                'warehouse_movements.referral_voucher_number',
                'warehouse_movements.origin',
                'warehouse_movement_details.digit1',
                'warehouse_movement_details.digit2',
                'warehouse_movements.soles_1',
                'warehouse_movements.soles_2',
                'warehouse_movements.price_mes',
                'warehouse_movements.cost_glp'
            )
            ->get();

        $total_g = 0;
        $quantity_g = 0;

        $soles_glp_g = 0;
        $kgs_glp_g = 0;

        foreach ($movements_g as $e) {


            if ($e->digit2 != null &&  $e->digit2 != 0) {

                if ($e->price_mes == $price_mes) {

                    $quantity_g += $e->digit1;
                    $total_g += $e->digit1 * $e->cost_glp;
                } else {
                    $quantity_g += $e->digit2;
                    $total_g += $e->digit2 * $e->cost_glp;
                }
            } else {
                $quantity_g += $e->digit1;
                $total_g +=  $e->digit1 * $e->cost_glp;
            }
        }



        $soles_glp_g += $total_g;
        $kgs_glp_g += $quantity_g;


        $cost_glp = $kgs_glp_g > 0 ? ($soles_glp_g / ($kgs_glp_g / 1000) / 1000) : null;




        /*---------------------------------*/

        $elements = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
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
            ->whereNotIn('sales.warehouse_document_type_id', [2, 3, 8, 9, 10, 11, 14, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33])
            ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
            ->select(
                'sale_details.id',
                'payments.name as payment_name',
                'companies.short_name as company_short_name',
                'sale_date',
                'business_units.name as business_unit_name',
                'client_channels.name as client_channel_name',
                'client_zones.name as client_zone_name',
                'client_sectors.name as client_sector_name',
                'client_routes.short_name as client_route_id',
                DB::Raw('(SELECT SUM(sale_details.quantity * (SELECT articles.convertion FROM articles WHERE articles.id = sale_details.article_id ))/1000 ) AS sum_total'),
                'sale_details.price_igv as price',

                DB::Raw('SUM(sale_details.total)AS total'),
                'clients.business_name as client_business_name'
            )
            ->when($client_route_id != 0, function ($query) use ($client_route_id) {
                return $query->where('client_routes.id', $client_route_id);
            })
            ->when($client_id, function ($query, $client_id) {
                return $query->where('sales.client_id', $client_id);
            })
            ->when($company_id, function ($query, $company_id) {
                return $query->where('clients.company_id', $company_id);
            })
            ->groupBy('sale_date', 'client_routes.id')
            ->orderBy('clients.business_unit_id')
            ->orderBy('sale_date')
            ->orderBy('business_unit_name')
            ->get();
        $response = [];
        foreach ($elements as $saledetail) {
            $saledetail->company_short_name = $saledetail['company_short_name'];
            $saledetail->sale_date = $saledetail['sale_date'];
            $saledetail->business_unit_name = $saledetail['business_unit_name'];
            $saledetail->client_sector_name = $saledetail['client_sector_name'];
            $saledetail->client_route_id = $saledetail['client_route_id'];
            $saledetail->sum_total = $saledetail['sum_total'];
            $saledetail->price = $saledetail['price'];
            $saledetail->total = $saledetail['total'];

            $response[] = $saledetail;
        }



        if ($export) {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->mergeCells('A1:L1');
            $sheet->setCellValue('A1', 'REPORTE DE MARGEN POR RUTA DEL ' . CarbonImmutable::now()->format('d/m/Y H:i:s'));
            $sheet->getStyle('A1')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 14,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]
            ]);


            $sheet->setCellValue('A3', '#');
            $sheet->setCellValue('B3', 'Compañía');
            $sheet->setCellValue('C3', 'Fecha de Despacho');
            $sheet->setCellValue('D3', 'Unidad');
            $sheet->setCellValue('E3', 'Canal');
            $sheet->setCellValue('F3', 'Ruta');
            $sheet->setCellValue('G3', 'TM');
            $sheet->setCellValue('H3', 'Precio Unit Kgs');
            $sheet->setCellValue('I3', 'Total');
            $sheet->setCellValue('J3', 'Costo GLP');
            $sheet->setCellValue('K3', 'Costo Venta');
            $sheet->setCellValue('L3', 'Margen BT');

            $sheet->getStyle('A3:L3')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 14,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '00B050'],
                ],

            ]);

            $row_number = 4;
            foreach ($response as $index => $element) {

                $index++;
                $saleDateYear = null;

                if ($element->sale_date) {
                    $saleDateObject = date('d/m/Y', strtotime($element->sale_date));
                    $saleDateYear = $saleDateObject;
                    //		$saleDateMonth = str_pad($saleDateObject->month, 2, '0', STR_PAD_LEFT);
                    //		$saleDateDay = str_pad($saleDateObject->day, 2, '0', STR_PAD_LEFT);
                }

                $sheet->setCellValueExplicit('A' . $row_number, $index, DataType::TYPE_NUMERIC);
                $sheet->setCellValue('B' . $row_number, $element->company_short_name);
                $sheet->setCellValue('C' . $row_number, $saleDateYear);
                $sheet->setCellValue('D' . $row_number, $element->business_unit_name);
                $sheet->setCellValue('E' . $row_number, $element->client_sector_name);
                $sheet->setCellValue('F' . $row_number, $element->client_route_id);
                $sheet->setCellValue('G' . $row_number, $element->sum_total);
                $sheet->setCellValue('H' . $row_number, $element->total / ($element->sum_total * 1000));
                $sheet->setCellValue('I' . $row_number, $element->total);
                $sheet->setCellValue('J' . $row_number, $cost_glp);
                $sheet->setCellValue('K' . $row_number, $cost_glp * $element->sum_total * 1000);
                $sheet->setCellValue('L' . $row_number, $element->total - ($cost_glp * $element->sum_total * 1000));
                $sheet->getStyle('H' . $row_number)->getNumberFormat()->setFormatCode('0.00');
                $sheet->getStyle('J' . $row_number)->getNumberFormat()->setFormatCode('0.00');

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


            $writer = new Xls($spreadsheet);
            return $writer->save('php://output');
        } else {
            return response()->json($response);
        }
    }
}
