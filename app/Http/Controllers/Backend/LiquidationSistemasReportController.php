<?php

namespace App\Http\Controllers\Backend;

use App\Client;
use App\Company;
use App\Article;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SaleDetail;
use App\Sale;
use App\Liquidation;
use App\BusinessUnit;
use Carbon\CarbonImmutable;
use Carbon\Carbon;
use strtotime;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use stdClass;


class LiquidationSistemasReportController extends Controller
{
	public function index()
	{
		$business_units = BusinessUnit::select('id', 'name')->get();
		$current_date = date(DATE_ATOM, mktime(0, 0, 0));
		$companies = Company::select('id', 'name')->get();
		return view('backend.liquidations_sistemas_report')->with(compact('business_units', 'current_date','companies'));
	}

	public function validateForm()
	{
		$messages = [
			'initial_date.required'	=> 'Debe seleccionar una Fecha inicial.',
			'final_date.required'	=> 'Debe seleccionar una Fecha final.',
		];

		$rules = [
			'initial_date'	=> 'required',
			'final_date'	=> 'required',
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

		if ($business_unit_id == 0){
			$business= [1,2,3];
		}
		else if($business_unit_id == 1){
			$business= [1];
		}else if($business_unit_id == 2){
			$business= [2];
		}else if($business_unit_id == 3){
			$business= [3];
		}

		$elements = Sale::leftjoin('sale_details', 'sales.id', '=', 'sale_details.sale_id')
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
				'sales.id as id',
				'payments.name as payment_name',
				'companies.short_name as company_short_name',
				'sale_date',
                DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") as liquidation_date'),
				'business_units.name as business_unit_name',
				'client_channels.name as client_channel_name',
				'client_zones.name as client_zone_name',
				'client_sectors.name as client_sector_name',
				'client_routes.short_name as client_route_id',
				'warehouse_document_types.short_name as warehouse_document_type_short_name',
				'sales.referral_serie_number',
				'sales.referral_voucher_number',
				'articles.name as article_name',
				DB::Raw('(SELECT SUM(sale_details.quantity * (SELECT articles.convertion FROM articles WHERE articles.id = sale_details.article_id ))/1000 ) AS sum_total'),
				'sales.total_perception',
                'sales.efective as efective',
                'sales.deposit as deposit',
                'sales.pre_balance as pre_balance',
				'clients.id as client_id',
				'clients.code as client_code',
				'clients.business_name as client_business_name',
				DB::Raw('CONCAT(sales.guide_series, "-", sales.guide_number) as guide'),
				DB::Raw('CONCAT(warehouse_movements.referral_serie_number, "-", warehouse_movements.referral_voucher_number) as electronica')
				)
			->when($business_unit_id, function ($query, $business_unit_id) {
				return $query->where('clients.business_unit_id', $business_unit_id);
			})
			->when($client_id, function ($query, $client_id) {
				return $query->where('sales.client_id', $client_id);
			})
			->when($company_id, function ($query, $company_id) {
				return $query->where('clients.company_id', $company_id);
			})
			->groupBy('id')
			->orderBy('clients.business_unit_id')
			->orderBy('sale_date')
			->orderBy('business_unit_name')
			->orderBy('warehouse_document_type_short_name')
			->orderBy('sales.referral_serie_number')
			->orderBy('sales.referral_voucher_number')
			->get();
		$response = [];


		$totals_total_perception = 0;
		$totals_credit = 0;
		$totals_efective = 0;
		$totals_deposit= 0;
        $totals_dif= 0;
		$totals_descuento_personal = 0;
        $totals_sum_total = 0;


		foreach ($elements as $saledetail) {

            
			$descuento_personal = Liquidation::where('sale_id', $saledetail['id'])
            ->where('payment_method_id', 10)
            ->where('collection', 0)
            ->select('amount')
            ->sum('amount');


            $efective = Liquidation::where('sale_id', $saledetail['id'])
            ->where('payment_method_id', 1)
            ->where('collection', 0)
            ->select('amount')
            ->sum('amount');

            $deposit = Liquidation::where('sale_id', $saledetail['id'])
            ->where('payment_method_id', 2)
            ->where('collection', 0)
            ->select('amount')
            ->sum('amount');


       



			$saledetail->company_short_name = $saledetail['company_short_name'];
			$saledetail->sale_date = $saledetail['sale_date'];
            $saledetail->liquidation_date = $saledetail['liquidation_date'];
			$saledetail->business_unit_name = $saledetail['business_unit_name'];
			$saledetail->warehouse_document_type_short_name = $saledetail['warehouse_document_type_short_name'];
			$saledetail->referral_serie_number = $saledetail['referral_serie_number'];
			$saledetail->referral_voucher_number = $saledetail['referral_voucher_number'];
			$saledetail->sum_total = $saledetail['sum_total'];
			$saledetail->total_perception = $saledetail['total_perception'];
            $saledetail->efective = $efective;
            $saledetail->deposit = $deposit;
            $saledetail->pre_balance = $saledetail['pre_balance'];
            $saledetail->descuento_personal = $descuento_personal;
			$saledetail->client_id = $saledetail['client_id'];
			$saledetail->client_code = $saledetail['client_code'];
			$saledetail->client_business_name = $saledetail['client_business_name'];
			
			$saledetail->movement_type_name = $saledetail['movement_type_name'];
			$saledetail->guide = $saledetail['guide'];
			$saledetail->electronica = $saledetail['electronica'];
			$saledetail->payment_name = $saledetail['payment_name'];


            $dif=  number_format($saledetail->total_perception - $saledetail->efective - $saledetail->deposit - $saledetail->pre_balance - $saledetail->descuento_personal, 2, '.', '');

            $saledetail->dif = $dif;


            $totals_total_perception  += $saledetail['total_perception'];
            $totals_credit += $saledetail['pre_balance'];
            $totals_efective  += $saledetail['efective'];
            $totals_deposit += $saledetail['deposit'];
            $totals_descuento_personal += $saledetail['descuento_personal'];
            $totals_sum_total  += $saledetail['sum_total'];
            $totals_dif  += $saledetail['dif'];
    

			$response[] = $saledetail;
		}

		$totals = new stdClass();
		$totals->company_short_name = 'TOTAL';
		$totals->sale_date = '';
        $totals->liquidation_date = '';
		$totals->business_unit_name = '';
		$totals->warehouse_document_type_short_name = '';
		$totals->referral_serie_number = '';
		$totals->referral_voucher_number = '';
		$totals->sum_total =  $totals_sum_total ;
        $totals->total_perception = $totals_total_perception;
		$totals->efective = $totals_efective;
        $totals->deposit = $totals_deposit;
        $totals->pre_balance = $totals_credit;
        $totals->descuento_personal = $totals_descuento_personal;
		$totals->client_id = '';
		$totals->client_code = '';
		$totals->client_business_name = '';
		$totals->dif = $totals_dif;
		$totals->movement_type_name = '';
		$totals->guide = '';
		$totals->electronica = '';
		$totals->payment_name = '';

		$response[] = $totals;

		if ($export) {
			$spreadsheet = new Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();
			$sheet->mergeCells('A1:AB1');
			$sheet->setCellValue('A1', 'REPORTE DE LIQUIDACIÓN DE VENTA DEL ' . CarbonImmutable::now()->format('d/m/Y H:m:s'));
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
			$sheet->setCellValue('B3', 'Compañía');
			$sheet->setCellValue('C3', 'Fecha de Despacho');
            $sheet->setCellValue('D3', 'Fecha de Liquidación');
			$sheet->setCellValue('E3', 'Unidad');
			$sheet->setCellValue('F3', 'Tipo');
			$sheet->setCellValue('G3', '# Serie');
			$sheet->setCellValue('H3', '# Documento');
			$sheet->setCellValue('I3', 'ID');
			$sheet->setCellValue('J3', 'Razón Social');
			$sheet->setCellValue('K3', 'TM');
			$sheet->setCellValue('L3', 'Total');
            $sheet->setCellValue('M3', 'Efectivo');
            $sheet->setCellValue('N3', 'Deposito');
            $sheet->setCellValue('O3', 'Crédito');
            $sheet->setCellValue('P3', 'Descuento Personal');
			$sheet->setCellValue('Q3', 'Cuadre');
			$sheet->setCellValue('R3', 'Tipo Movimiento');
			$sheet->setCellValue('S3', 'Guía Física');
			$sheet->setCellValue('T3', 'Guía Electrónica');
			$sheet->setCellValue('U3', 'Método de Pago');

			$sheet->getStyle('A3:U3')->applyFromArray([
				'font' => [
					'bold' => true,
				],
			]);

			$row_number = 4;
			foreach ($response as $index => $element) {
				$index++;

				$saleDateYear = null;
				//	$saleDateMonth = null;
				//	$saleDateDay = null;

				if ($element->sale_date) {
					$saleDateObject = date('d/m/Y', strtotime($element->sale_date));
					$saleDateYear = $saleDateObject;
					//		$saleDateMonth = str_pad($saleDateObject->month, 2, '0', STR_PAD_LEFT);
					//		$saleDateDay = str_pad($saleDateObject->day, 2, '0', STR_PAD_LEFT);
				}

				$sheet->setCellValueExplicit('A' . $row_number, $index, DataType::TYPE_NUMERIC);
				$sheet->setCellValue('B' . $row_number, $element->company_short_name);
				$sheet->setCellValue('C' . $row_number, $saleDateYear);
                $sheet->setCellValue('D' . $row_number, $element->liquidation_date);
				$sheet->setCellValue('E' . $row_number, $element->business_unit_name);
				$sheet->setCellValue('F' . $row_number, $element->warehouse_document_type_short_name);
				$sheet->setCellValue('G' . $row_number, $element->referral_serie_number);
				$sheet->setCellValue('H' . $row_number, $element->referral_voucher_number);
				$sheet->setCellValue('I' . $row_number, $element->client_id);
				$sheet->setCellValue('J' . $row_number, $element->client_business_name);
				$sheet->setCellValue('K' . $row_number, $element->sum_total);
				$sheet->setCellValue('L' . $row_number, $element->total_perception);
                $sheet->setCellValue('M' . $row_number, $element->efective);
                $sheet->setCellValue('N' . $row_number, $element->deposit);
                $sheet->setCellValue('O' . $row_number, $element->pre_balance);
                $sheet->setCellValue('P' . $row_number, $element->descuento_personal);
				$sheet->setCellValue('Q' . $row_number, $element->dif);
				$sheet->setCellValue('R' . $row_number, $element->movement_type_name);
				$sheet->setCellValue('S' . $row_number, $element->guide);
				$sheet->setCellValue('T' . $row_number, $element->electronica);
				$sheet->setCellValue('U' . $row_number, $element->payment_name);

				$sheet->getStyle('L' . $row_number)->getNumberFormat()->setFormatCode('0.00');
				$sheet->getStyle('M' . $row_number)->getNumberFormat()->setFormatCode('0.00');
                $sheet->getStyle('N' . $row_number)->getNumberFormat()->setFormatCode('0.00');
                $sheet->getStyle('O' . $row_number)->getNumberFormat()->setFormatCode('0.00');
                $sheet->getStyle('P' . $row_number)->getNumberFormat()->setFormatCode('0.00');


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

			$writer = new Xls($spreadsheet);
			return $writer->save('php://output');
		} else {
			return response()->json($response);
		}
	}
}
