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


class LiquidationTotalSistemasReportController extends Controller
{
	public function index()
	{
		$business_units = BusinessUnit::select('id', 'name')->get();
		$current_date = date(DATE_ATOM, mktime(0, 0, 0));
		$companies = Company::select('id', 'name')->get();
		return view('backend.liquidations_total_sistemas_report')->with(compact('business_units', 'current_date','companies'));
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
            ->leftjoin('cicles', 'sales.cicle_id', '=', 'cicles.id')
			->leftjoin('business_units', 'clients.business_unit_id', '=', 'business_units.id')
			->leftjoin('companies', 'sales.company_id', '=', 'companies.id')
			->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
			->leftjoin('warehouse_document_types', 'sales.warehouse_document_type_id', '=', 'warehouse_document_types.id')
			->where('sales.sale_date', '>=', $initial_date)
			->where('sales.sale_date', '<=', $final_date)
			->whereNotIn('sales.warehouse_document_type_id', [2, 3, 8, 9, 10, 11, 14, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33])
			->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
			->select( 
				'sales.id as id',
                'sales.cicle_id as cicle_id',
                'sales.mes as mes',
                'sales.anio as anio',
                'cicles.inicio as fecha_ini',
                'cicles.final as fecha_fin',
                'cicles.short_name as cicle_name',
				'companies.short_name as company_short_name',
				'sales.sale_date',
                'business_units.id as business_unit_id',
				'business_units.name as business_unit_name',

				DB::Raw('(SELECT SUM(sale_details.quantity * (SELECT articles.convertion FROM articles WHERE articles.id = sale_details.article_id ))/1000 ) AS sum_total'))

			->when($business_unit_id, function ($query, $business_unit_id) {
				return $query->where('clients.business_unit_id', $business_unit_id);
			})

			->when($company_id, function ($query, $company_id) {
				return $query->where('clients.company_id', $company_id);
			})
            ->groupBy('cicle_id')
			->groupBy('business_unit_id')
			->orderBy('cicle_id')
			->orderBy('business_unit_name')
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

    $client_ids = [1031, 427, 13326, 13775, 14072,14258];
    $warehouse_document_type_ids = [13,5,7,4];


			$saledetail->company_short_name = $saledetail['company_short_name'];
            $saledetail->cicle_id = $saledetail['cicle_id'];
			$saledetail->mes = $saledetail['mes'];
            $saledetail->cicle_name = $saledetail['cicle_name'];
            $saledetail->fecha_ini = $saledetail['fecha_ini'];
            $saledetail->fecha_fin = $saledetail['fecha_fin'];
            $saledetail->business_unit_name = $saledetail['business_unit_name'];
			$saledetail->business_unit_id = $saledetail['business_unit_id'];
			$saledetail->sum_total = $saledetail['sum_total'];
        


            $efective = Liquidation::leftjoin('sales', 'liquidations.sale_id', '=', 'sales.id')
            ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
            ->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
            ->whereNotIn('sales.client_id', $client_ids)
            ->where('clients.business_unit_id', $saledetail['business_unit_id'])
            ->where('sales.cicle_id', '=', $saledetail['cicle_id'])
            ->where('sales.sale_date', '>=', $saledetail['fecha_ini'])
			->where('sales.sale_date', '<=', $saledetail['fecha_fin'])              
            ->where('liquidations.payment_method_id', '=' , 1)
            ->where('liquidations.collection', '=' , 0)
            ->select('liquidations.amount')
            ->sum('liquidations.amount'); 
            
            
            $deposit = Liquidation::leftjoin('sales', 'liquidations.sale_id', '=', 'sales.id')
            ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
            ->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
            ->whereNotIn('sales.client_id', $client_ids)
            ->where('clients.business_unit_id', $saledetail['business_unit_id'])
            ->where('sales.cicle_id', '=', $saledetail['cicle_id'])
            ->where('sales.sale_date', '>=', $saledetail['fecha_ini'])
			->where('sales.sale_date', '<=', $saledetail['fecha_fin']) 
            ->where('liquidations.payment_method_id', '=' , 2)
            ->where('liquidations.collection', '=' , 0)
            ->select('liquidations.amount')
            ->sum('liquidations.amount'); 
        
            $credit = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
                ->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
                ->whereNotIn('sales.client_id', $client_ids)
                ->where('clients.business_unit_id', $saledetail['business_unit_id'])
                ->where('sales.cicle_id', '=', $saledetail['cicle_id'])
                ->where('sales.sale_date', '>=', $saledetail['fecha_ini'])
			    ->where('sales.sale_date', '<=', $saledetail['fecha_fin']) 
                ->select('sales.pre_balance')
                ->sum('sales.pre_balance'); 

			
                
                
            $descuento_personal = Liquidation::leftjoin('sales', 'liquidations.sale_id', '=', 'sales.id')
            ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
            ->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
            ->whereNotIn('sales.client_id', $client_ids)
            ->where('clients.business_unit_id', $saledetail['business_unit_id'])
            ->where('sales.cicle_id', '=', $saledetail['cicle_id'])
            ->where('sales.sale_date', '>=', $saledetail['fecha_ini'])
			->where('sales.sale_date', '<=', $saledetail['fecha_fin']) 
            ->where('liquidations.payment_method_id', '=' , 10)
            ->where('liquidations.collection', '=' , 0)
            ->select('liquidations.amount')
            ->sum('liquidations.amount'); 


            $total_perception = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
            ->whereIn('sales.warehouse_document_type_id', $warehouse_document_type_ids)
            ->whereNotIn('sales.client_id', $client_ids)
            ->where('clients.business_unit_id', $saledetail['business_unit_id'])
            ->where('sales.cicle_id', '=', $saledetail['cicle_id'])
            ->where('sales.sale_date', '>=', $saledetail['fecha_ini'])
            ->where('sales.sale_date', '<=', $saledetail['fecha_fin']) 
            ->select('sales.total_perception')
            ->sum('sales.total_perception');


            $saledetail->total_perception = $total_perception;


            $saledetail->efective = $efective;
            $saledetail->deposit = $deposit;
            $saledetail->pre_balance = $credit;
            $saledetail->descuento_personal = $descuento_personal;
		
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
		$totals->cicle_name = '';
		$totals->business_unit_name = '';
		$totals->sum_total =  $totals_sum_total ;
        $totals->total_perception = $totals_total_perception;
		$totals->efective = $totals_efective;
        $totals->deposit = $totals_deposit;
        $totals->pre_balance = $totals_credit;
        $totals->descuento_personal = $totals_descuento_personal;
		$totals->dif = $totals_dif;

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
			$sheet->setCellValue('C3', 'Mes de Despacho');
			$sheet->setCellValue('D3', 'Unidad');
			$sheet->setCellValue('E3', 'TM');
			$sheet->setCellValue('F3', 'Total');
            $sheet->setCellValue('G3', 'Efectivo');
            $sheet->setCellValue('H3', 'Deposito');
            $sheet->setCellValue('I3', 'Crédito');
            $sheet->setCellValue('J3', 'Descuento Personal');
			$sheet->setCellValue('K3', 'Cuadre');

			$sheet->getStyle('A3:K3')->applyFromArray([
				'font' => [
					'bold' => true,
				],
			]);

			$row_number = 4;
			foreach ($response as $index => $element) {
				$index++;
/*
				$saleDateYear = null;
				//	$saleDateMonth = null;
				//	$saleDateDay = null;

				if ($element->sale_date) {
					$saleDateObject = date('F', strtotime($element->sale_date));
					$saleDateYear = $saleDateObject;
					//		$saleDateMonth = str_pad($saleDateObject->month, 2, '0', STR_PAD_LEFT);
					//		$saleDateDay = str_pad($saleDateObject->day, 2, '0', STR_PAD_LEFT);
				}*/

				$sheet->setCellValueExplicit('A' . $row_number, $index, DataType::TYPE_NUMERIC);
				$sheet->setCellValue('B' . $row_number, $element->company_short_name);
				$sheet->setCellValue('C' . $row_number, $element->cicle_name);
				$sheet->setCellValue('D' . $row_number, $element->business_unit_name);
				$sheet->setCellValue('E' . $row_number, $element->sum_total);
				$sheet->setCellValue('F' . $row_number, $element->total_perception);
                $sheet->setCellValue('G' . $row_number, $element->efective);
                $sheet->setCellValue('H' . $row_number, $element->deposit);
                $sheet->setCellValue('I' . $row_number, $element->pre_balance);
                $sheet->setCellValue('J' . $row_number, $element->descuento_personal);
				$sheet->setCellValue('K' . $row_number, $element->dif);


				$sheet->getStyle('F' . $row_number)->getNumberFormat()->setFormatCode('0.00');
				$sheet->getStyle('G' . $row_number)->getNumberFormat()->setFormatCode('0.00');
                $sheet->getStyle('H' . $row_number)->getNumberFormat()->setFormatCode('0.00');
                $sheet->getStyle('I' . $row_number)->getNumberFormat()->setFormatCode('0.00');
                $sheet->getStyle('J' . $row_number)->getNumberFormat()->setFormatCode('0.00');
                $sheet->getStyle('K' . $row_number)->getNumberFormat()->setFormatCode('0.00');


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


			$writer = new Xls($spreadsheet);
			return $writer->save('php://output');
		} else {
			return response()->json($response);
		}
	}
}
