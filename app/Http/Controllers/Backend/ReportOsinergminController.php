<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Exports\OsinergminReportExport;
use App\Guide;
use App\Guides;
use App\Sale;
use App\Voucher;
use App\WarehouseMovement;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use DateTime;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportOsinergminController extends Controller
{
    public function index()
    {

        $firstDayOfMonth = new DateTime('first day of this month midnight');

        $current_date_1 = $firstDayOfMonth->format(DATE_ATOM);
        $current_date_2 = date(DATE_ATOM, mktime(0, 0, 0));

        $current_date = date(DATE_ATOM, mktime(0, 0, 0));
        return view('backend.osinergmin_report')->with(compact('current_date', 'current_date_1', 'current_date_2'));

    }

    public function validateForm()
    {
        $messages = [
            'since_date.required'                 => 'Debe seleccionar una Fecha de Inicio.',
            'to_date.required'                    => 'Debe seleccionar una Fecha de Fin.',
        ];

        $rules = [
            'since_date'                  => 'required',
            'to_date'                     => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function list()
    {
        $initial_date = Carbon::parse(request('model.since_date'))->format('Y-m-d');
        $final_date = Carbon::parse(request('model.to_date'))->format('Y-m-d');

        // 2.1 - COMPRA DE GLP EFECTUADA A UN AGENTE
        $abastecimientos_glp = WarehouseMovement::leftjoin('companies', 'companies.id', '=', 'warehouse_movements.company_id')
            ->leftjoin('warehouse_types', 'warehouse_types.id', '=', 'warehouse_movements.warehouse_type_id')
            ->leftjoin('warehouse_movement_details', 'warehouse_movement_details.warehouse_movement_id', '=', 'warehouse_movements.id')
            ->leftjoin('agents', 'agents.id', 'warehouse_types.agent_id')
            ->select(
                DB::Raw('date_format(warehouse_movements.created_at, "%M %Y") as mes'),
                'warehouse_types.business_name as nombre_agente',
                'warehouse_types.ruc as ruc',
                DB::Raw('date_format(warehouse_movements.created_at, "%Y-%m-%d") as fecha_despacho'),
                'warehouse_movements.scop_number as scop',
                'warehouse_movements.license_plate as placa',
                DB::Raw('date_format(warehouse_movements.created_at, "%Y-%m-%d") as fecha_facturacion'),
                'warehouse_movements.referral_serie_number as serie',
                'warehouse_movements.referral_voucher_number as numero',
                DB::Raw('SUM(warehouse_movement_details.converted_amount) as total'),
                'warehouse_types.agent_uno as registro',
                'warehouse_types.agent_dos as tipo_agent'
            )
            ->where('warehouse_movements.movement_class_id', 2) // SALIDAS
            ->where('warehouse_movements.movement_type_id', 30) // ABASTECIMIENTO GLP PLANTA
            ->whereIn('warehouse_movements.warehouse_type_id', [8, 9, 10, 11, 20]) // PLUSPETROL - PLUS-ZETA - PETROPERU - SOLGAS - ZETA GAS
            ->where('warehouse_movements.company_id', 1)
            ->where(DB::Raw('DATE(warehouse_movements.created_at)'), '>=', $initial_date)
            ->where(DB::Raw('DATE(warehouse_movements.created_at)'), '<=', $final_date)
            ->groupBy('warehouse_movements.id')
            ->get();

        //3.1 - VENTA DE GLP A GRANEL EFECTUADA A UN AGENTE
        $facturas_vd = Voucher::leftjoin('clients', 'clients.id', '=', 'vouchers.client_id')
            ->leftjoin('client_channels', 'client_channels.id', '=', 'clients.channel_id')
            ->leftjoin('voucher_details', 'voucher_details.voucher_id', '=', 'vouchers.id')
            ->leftjoin('agents', 'agents.id', '=', 'clients.activity_id')
            ->leftjoin('guides', function ($join) {
                $join->on('guides.referral_guide_series', '=', 'vouchers.referral_guide_series')
                    ->on('guides.referral_guide_number', '=', 'vouchers.referral_guide_number');
            })
            ->leftjoin('vehicles', 'vehicles.id', '=', 'guides.vehicle_id')
            ->select(
                DB::Raw('date_format(vouchers.issue_date, "%M %Y") as mes'),
                'clients.business_name as client_name',
                'clients.document_number as ruc',
                DB::Raw('date_format(vouchers.issue_date, "%Y-%m-%d") as fecha_despacho'),
                'guides.scop_number as scop',
                DB::Raw('date_format(vouchers.issue_date, "%Y-%m-%d") as fecha_facturacion'),
                'vouchers.serie_number as serie',
                'vouchers.voucher_number as numero',
                'vehicles.plate as vehiculo',
                DB::Raw('SUM(voucher_details.quantity) as total'),
                'guides.activity as registro',
                'guides.agent as tipo_agent'
            )
            ->whereIn('vouchers.voucher_type_id', [1, 5]) // FACTURA ELECTRONICA - FACTURA PORTAL SUNAT
            ->whereIn('guides.route_id', [26]) // ENVASADO - GRANEL
            ->where('clients.business_unit_id', 2)
            ->where('clients.channel_id', 5)
            ->where('vouchers.company_id', 1) // PDD
            ->where('vouchers.ose', 1)
            ->where('vouchers.issue_date', '>=', $initial_date)
            ->where('vouchers.issue_date', '<=', $final_date)
            ->groupBy('vouchers.id')
            ->get();

        //3.2 - TRANSFERENCIA DE GLP A GRANEL HACIA UN AGENTE DE LA MISMA RAZÓN SOCIAL
            
            $guias_elect = Sale::leftJoin('companies','companies.id','=','sales.company_id')
            ->leftJoin('sale_details','sale_details.sale_id','=','sales.id')
            ->leftJoin('clients','clients.id','=','sales.client_id')
            ->leftjoin('warehouse_movements','warehouse_movements.id','=','sales.warehouse_movement_id')
            ->leftjoin('guides', function ($join) {
                $join->on('guides.referral_guide_series', '=', 'warehouse_movements.referral_serie')
                    ->on('guides.referral_guide_number', '=', 'warehouse_movements.referral_number');
            })  
            ->where('sales.guide_series','137')
            ->where('sales.sale_date','>=',$initial_date)
            ->where('sales.sale_date','<=',$final_date)
          //  ->groupBy('sales.warehouse_movement_id')
            ->select(

                DB::Raw('date_format(sales.sale_date, "%M %Y") as mes'),
                'companies.document_number as company_document_number',
                'guides.traslate_date as fecha_despacho',
                'guides.scop_number as scop',
                'sales.license_plate as placa_principal',
                DB::Raw('date_format(guides.created_at, "%Y-%m-%d") as fecha_remision'),
                'guides.referral_guide_series as serie',
                'guides.referral_guide_number as numero',
                DB::Raw('SUM(sale_details.kg) as total'),
                'guides.activity as registro',
                'guides.agent as tipo_agent'
            )
            
        //    ->where('guides.state',1)
         //   ->groupBy('sales.warehouse_movement_id')
            ->groupBy('guides.referral_guide_number')
            ->get();


        //3.3 - VENTA DE GLP EN CILINDROS EFECTUADA A UN AGENTE
        $tres_tres =Voucher::leftjoin('clients', 'clients.id', '=', 'vouchers.client_id')
            ->leftjoin('voucher_details', 'voucher_details.voucher_id', '=', 'vouchers.id')
            ->leftjoin('articles', 'articles.id', '=', 'voucher_details.article_id')
            ->leftjoin('agents', 'agents.id', '=', 'clients.activity_id')
            ->leftjoin('guides', function ($join) {
                $join->on('guides.referral_guide_series', '=', 'vouchers.referral_guide_series')
                    ->on('guides.referral_guide_number', '=', 'vouchers.referral_guide_number');
            })
            ->leftjoin('vehicles', 'vehicles.id', '=', 'guides.vehicle_id')
            ->select(
                DB::Raw('date_format(vouchers.issue_date, "%M %Y") as mes'),
                'clients.business_name as client_name',
                'clients.document_number as ruc',

                DB::Raw('date_format(vouchers.issue_date, "%Y-%m-%d") as fecha_despacho'),
                'vouchers.scop_number as scop',

                'vouchers.client_address as client_address',
                
                DB::Raw('date_format(vouchers.issue_date, "%Y-%m-%d") as fecha_facturacion'),
                'vouchers.serie_number as serie',
                'vouchers.voucher_number as numero',
                'vehicles.plate as vehiculo',
                DB::Raw('SUM(voucher_details.quantity * articles.convertion) as total'),
                'guides.activity as registro',
                'guides.agent as tipo_agent'
            )
            ->whereIn('vouchers.voucher_type_id', [1,5]) // FACTURA ELECTRONICA - FACTURA PORTAL SUNAT
            ->whereIn('vouchers.serie_number', ['F106','F105', 'F109','F108'])
            ->where('clients.business_unit_id', 1)
            ->where('vouchers.company_id', 1) // PDD
            ->where('vouchers.ose', 1)
            ->where('vouchers.issue_date', '>=', $initial_date)
            ->where('vouchers.issue_date', '<=', $final_date)
            ->groupBy('vouchers.id')
            ->get();



        //3.4 - TRANSFERENCIA GLP EN CILINDROS HACIA UN AGENTE DE LA MISMA RAZÓN SOCIAL
        $guias_elect_cilindros = Guide::leftjoin('companies', 'companies.id', '=', 'guides.company_id')
            ->leftjoin('vehicles', 'vehicles.id', '=', 'guides.vehicle_id')
            ->leftjoin('guides_details', 'guides_details.guides_id', '=', 'guides.id')
            ->leftjoin('articles', 'articles.id', '=', 'guides_details.article_code')
            ->select(
                DB::Raw('date_format(guides.created_at, "%M %Y") as mes'),
                'companies.document_number as company_document_number',
                'guides.traslate_date as fecha_despacho',
                'guides.scop_number as scop',
                'vehicles.plate as placa_principal',
                DB::Raw('date_format(guides.created_at, "%Y-%m-%d") as fecha_remision'),
                'guides.referral_guide_series as serie',
                'guides.referral_guide_number as numero',
                DB::Raw('SUM(guides_details.converted_amount * articles.convertion) as total'),
                'guides.activity as registro',
                'guides.agent as tipo_agent'
            )
            ->where('guides.route_id', '!=', 26)
            ->where('vehicles.route', '!=', 2)
            ->where('guides.route_id', '!=', 19)
            ->where('guides.company_id',1)
            ->where('guides.movement_type_id', 11)
            ->where('guides.state', 1)
            ->whereDate('guides.created_at', '>=', $initial_date)
            ->whereDate('guides.created_at', '<=', $final_date)
            ->groupBy('guides.id')
            ->get();

        //3.6 - VENTA DE GLP EN CILINDROS A USUARIOS FINALES CON FACTURA
    

        //3.7 - VENTA DE GLP EN CILINDROS A USUARIOS FINALES CON BOLETA DE VENTA
        $vouchers = Voucher::leftjoin('voucher_types', 'voucher_types.id', '=', 'vouchers.voucher_type_id')
            ->leftjoin('voucher_details', 'voucher_details.voucher_id', '=', 'vouchers.id')
            ->select(
                DB::Raw('date_format(vouchers.issue_date, "%M %Y") as mes'),
                'voucher_types.name as tipo',
                'vouchers.serie_number as serie',
                DB::Raw('MIN(vouchers.voucher_number) as inicio'),
                DB::Raw('MAX(vouchers.voucher_number) as fin'),
                DB::Raw('SUM(voucher_details.quantity) as total')
            )
            ->whereIn('vouchers.serie_number', ['B022', 'B023', 'B024', 'B025', 'B026', 'B027', 'B028', 'B029', 'B030', 'B031', 'B032', 'B033'
            , 'B034', 'B035', 'B036', 'B037', 'B038', 'B039', 'B040'])
            ->where('vouchers.issue_date', '>=', $initial_date)
            ->where('vouchers.issue_date', '<=', $final_date)
            ->groupBy('vouchers.serie_number')
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        $datetime = CarbonImmutable::now()->format('d/m/Y h:i:s a');

        $excel = new OsinergminReportExport($datetime, $abastecimientos_glp, $facturas_vd, $guias_elect, $tres_tres, $guias_elect_cilindros, $vouchers);
        return Excel::download($excel, 'reporte-osinergmin.xlsx');
    }
}
