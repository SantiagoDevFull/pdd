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
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use stdClass;


class LiquidationComercialReportController extends Controller
{
    public function index() {
	
		$current_date = date(DATE_ATOM, mktime(0, 0, 0));
		$client_routes = ClientRoute::select('id','short_name')->whereIn('type', [1])->get();
		return view('backend.liquidations_comercial_report')->with(compact('current_date' ,'client_routes'));
	}

	public function validateForm() {
		$messages = [
			'initial_date.required'	=> 'Debe seleccionar una Fecha inicial.',
			
		];

		$rules = [
			'initial_date'	=> 'required',
			
		];

		request()->validate($rules, $messages);
		return request()->all();
	}

	public function getClients() {
		$business_unit_id = request('business_unit_id');
		$q = request('q');

		$clients = Client::select('id', 'business_name as text')
			->when($business_unit_id, function($query, $business_unit_id) {
				return $query->where('business_unit_id', $business_unit_id);
			})
			->where('business_name', 'like', '%'.$q.'%')
			->withTrashed()
			->get();

		return $clients;
	}

	public function list() {

		$export = request('export');
		$route_id = request('model.route_id');

	    $initial_date = CarbonImmutable::createFromDate(request('model.initial_date'))->startOfDay()->format('Y-m-d');		
        $mes_date = CarbonImmutable::createFromDate(request('model.initial_date'))->startOfDay()->format('Y-m-01');       
		$day = date('j', strtotime($initial_date));
		$mesActual= date('n', strtotime($initial_date));
		$anioActual= date ('Y', strtotime($initial_date));
		$number =cal_days_in_month(CAL_GREGORIAN, $mesActual, $anioActual);

		
       
		$elements = Client::leftjoin('client_routes', 'clients.route_id', '=', 'client_routes.id')
            ->leftjoin('client_addresses', function($join) {
			    $join->on('clients.id', '=', 'client_addresses.client_id')
				->where('client_addresses.address_type_id', 1);})
			->leftjoin('ubigeos', 'client_addresses.ubigeo_id', '=', 'ubigeos.id')
			->select('clients.id as client_id',
            'clients.route_id as ruta_id',
            'client_routes.short_name as ruta',
            'clients.business_name as name', 
            'ubigeos.district as district')
			->when($route_id, function($query, $route_id) {
				return $query->where('clients.route_id', $route_id);
			})	
		//  ->groupBy('clients.route_id')
            ->orderBy('ruta_id')
			->get();
			$response=[];

		    $totals_tm = 0;
		    $totals_mes_ant = 0;
		    $totals_mes_proy = 0;
		

			foreach ($elements as $saledetail) {

				
				
				$price_mes = date('Y-m-01', strtotime('-1 month', strtotime($initial_date)));
             // $inicioMesPasado = date("Y-m-d", $price_mes);
                $tiempoMesPasado = strtotime( "last day of previous month",strtotime($initial_date));
                $fechaMesPasado = date("Y-m-d", $tiempoMesPasado);


				$monthName  = date("F", strtotime ($initial_date));
                $monthName2 = date("F", strtotime ($fechaMesPasado));


				
               

                $saledetail->ruta = $saledetail['ruta'];
                $saledetail->ruta_id = $saledetail['ruta_id'];
				$saledetail->name = $saledetail ['name'];
				$saledetail->codigo = $saledetail['client_id'];
				$saledetail->district = $saledetail['district'];
               

              $tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
                ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
                ->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
                ->where('sales.sale_date', '>=', $mes_date)
                ->where('sales.sale_date', '<=', $initial_date)
                ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29])
                ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258,13233])
                ->where('clients.id',$saledetail->codigo)
                ->select('kg')
                ->sum('kg');
                
              $tm_last= SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
                ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
                ->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
                ->where('sales.sale_date', '>=',  $price_mes)
                ->where('sales.sale_date', '<=',  $fechaMesPasado)
                ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29])
                ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258,13233])
                ->where('clients.id',$saledetail->codigo)
                ->select('kg')
                ->sum('kg');

                $saledetail->tm = $tm_fin/1000;
                $saledetail->mes_act = $saledetail['tm'];
                $saledetail->mes_proy =  ($saledetail ['tm']/ $day)* $number;

               
                $saledetail->mes_ant =$tm_last/1000;

                if ( $saledetail->mes_ant != 0)
	            $saledetail->diff =   $saledetail->mes_proy -$saledetail->mes_ant ;
	            else
	            {
                    $saledetail->diff = $saledetail->mes_proy;
	            }

	            if ( $saledetail->mes_proy != 0)
	            $saledetail->porc_diff =($saledetail->mes_ant/ $saledetail->mes_proy);
	            else
	            {
                $saledetail->porc_diff=0;
	            } 


						
			
			   $totals_tm += $saledetail['tm'];
			   $totals_mes_ant += $saledetail['mes_ant'];
			   $totals_mes_proy += $saledetail['mes_proy'];
				
			   $response[] = $saledetail;


			}

		   $totals = new stdClass();
		   $totals->ruta= 'TOTAL';
		   $totals->codigo = '';
		   $totals->name = '';
		   $totals->district = '';
		   $totals->mes_ant = $totals_mes_ant;
		   $totals->tm=  $totals_tm;
		   $totals->mes_ant= $totals_mes_ant;
		   $totals->mes_proy = $totals_mes_proy;
		   $totals->diff = '';
		   $totals->porc_diff = '';
		

		   $response[] = $totals;

		   if ( $export) {
			$spreadsheet = new Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();
			$sheet->mergeCells('A1:K1');
			$sheet->setCellValue('A1', 'REPORTE DE CLIENTES PERDIDOS AL '.$initial_date);
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
            $sheet->setCellValue('F3', $monthName2);
            $sheet->setCellValue('G3', 'Mes Actual');
			$sheet->setCellValue('H3', 'Mes Anterior');
			$sheet->setCellValue('I3', 'Proyección Actual');
            $sheet->setCellValue('J3', 'Dif');
            $sheet->setCellValue('K3', '%Dif');
         // $sheet->setCellValue('L3', 'Día Final Anterior');
		 //	$sheet->setCellValue('M3', 'Día Inicial Anterior');
			
			
			$sheet->getStyle('A3:K3')->applyFromArray([
				'font' => [
					'bold' => true,
				],
			]);

			

			$row_number = 4;

			$sheet->getStyle('A3'.':E3')->applyFromArray([
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

			$sheet->getStyle('F3'.':G3')->applyFromArray([
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

			$sheet->getStyle('H3'.':I3')->applyFromArray([
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
			
			


			$sheet->getStyle('J3'.':K3' )->applyFromArray([
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
			

			

			foreach ($response as $index => $element) {
				$index++;
				
			

				$sheet->setCellValueExplicit('A'.$row_number, $index, DataType::TYPE_NUMERIC);
				$sheet->setCellValue('B'.$row_number, $element->ruta);
				$sheet->setCellValue('C'.$row_number, $element->codigo);
                $sheet->setCellValue('D'.$row_number, $element->name);
                $sheet->setCellValue('E'.$row_number, $element->district);
                $sheet->setCellValue('F'.$row_number, $element->mes_ant);
				$sheet->setCellValue('G'.$row_number, $element->tm);
				$sheet->setCellValue('H'.$row_number, $element->mes_ant);
				$sheet->setCellValue('I'.$row_number, $element->mes_proy);
				$sheet->setCellValue('J'.$row_number, $element->diff);
				$sheet->setCellValue('K'.$row_number, $element->porc_diff);
           
           		
				$sheet->getStyle('F'.$row_number)->getNumberFormat()->setFormatCode('0.00');
				$sheet->getStyle('G'.$row_number)->getNumberFormat()->setFormatCode('0.00');
				$sheet->getStyle('H'.$row_number)->getNumberFormat()->setFormatCode('0.00');
				$sheet->getStyle('I'.$row_number)->getNumberFormat()->setFormatCode('0.00');
			   	$sheet->getStyle('J'.$row_number)->getNumberFormat()->setFormatCode('0.00');
			  	$sheet->getStyle('K'.$row_number)->getNumberFormat()->setFormatCode('0.00'.'%');
		
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
		} 
		
		    else {
			return response()->json($response);
		    }
		
	}
}
	








