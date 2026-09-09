<?php

namespace App\Http\Controllers\Backend;

use App\Client;
use App\Company;
use App\Article;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Report;
use App\ConsReport;
use App\Sale;
use App\SaleDetail;
use App\WarehouseMovement;
use App\WarehouseMovementDetail;
use App\AcRep;
use App\GlpGeneral;
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

class DaysSaleReportController extends Controller
{
    public function index() {

		$current_date = date(DATE_ATOM, mktime(0, 0, 0));
		return view('backend.days_sale_report')->with(compact('current_date'));
	}

	public function validateForm() {
		$messages = [
			'initial_date.required'	=> 'Debe seleccionar una Fecha.',
			'day.required'			=> 'Debe ingresar un día.',
			'day.numeric'				=> 'El día debe ser mayor a 0.',
			'day.min'					=> 'El día debe ser mayor a 0.',
			'day.not_in'				=> 'El día debe ser mayor a 0.',
			
		];

		$rules = [
			'initial_date'	=> 'required',
			'day'				=> 'required|numeric|min:0|not_in:0',
			
			
		];

		request()->validate($rules, $messages);
		return request()->all();
	}


	public function list() {
		$export = request('export');
		$initial_date = CarbonImmutable::createFromDate(request('model.initial_date'))->startOfDay()->format('Y-m-d');
    	$mes_date = CarbonImmutable::createFromDate(request('model.initial_date'))->startOfDay()->format('Y-m-01');
		$price_mes = CarbonImmutable::createFromDate(request('model.initial_date'))->startOfDay()->format('m');
		$price_year = CarbonImmutable::createFromDate(request('model.initial_date'))->startOfDay()->format('Y');
		$day = request('model.day');


		$result = ConsReport::select(
			'cons_reports.udid as udid',
			'cons_reports.año as year',
			'cons_reports.mes as month',
			'cons_reports.channel_name as client_channel_name',
			'cons_reports.name as name',
		    'cons_reports.business_id as business_id',
		    'cons_reports.sector_id as sector_id',
		    'cons_reports.channel_id as channel_id',
		    'cons_reports.manager_id as manager_id',
			'cons_reports.route_id as route_id',
			'cons_reports.zone_id as zone_id',
			'cons_reports.soles as last_total',
			'cons_reports.tm as last_tm',
			'cons_reports.tm_ppto as tm_ppto',
			 'cons_reports.mb_ppto as mb_ppto',
			 'cons_reports.mb as last_mb',
			 'cons_reports.dias as dias_mes',
			 'cons_reports.unit as unit',
			 'cons_reports.sale_option as option',
			 'cons_reports.company_id as company',
			 'cons_reports.type as type',
			 'cons_reports.orden as orden'
		 )

		 ->where('cons_reports.mes', '=', $price_mes)
		 ->where('cons_reports.año', '=', $price_year)

		 ->orderBy('cons_reports.sale_option')
		 ->orderBy('cons_reports.orden')		 
		 ->get();

	
		$response = [];
		$totals_last_tm = 0;
		$totals_tm = 0;
		$totals_proy_tm = 0;
		$totals_tm_ppto = 0;
		$totals_last_mb = 0;
		$totals_mb = 0;
		$totals_proy_mb = 0;
		$totals_mb_ppto = 0;
		$totals_last_total = 0;
		$totals_total = 0;
		$totals_proy_soles = 0;

	    
		if ($export) {
			$options = $result->groupBy('option');
			$row_number = 4;

			$spreadsheet = new Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();
			$sheet->mergeCells('A1:Y1');
			
			$sheet->setCellValue('A1', 'REPORTE DE VENTA CANALES DEL '.CarbonImmutable::createFromDate(request('model.initial_date'))->startOfDay()->format('Y-m-d'));
			$sheet->getStyle('A1')->applyFromArray([
				'font' => [
					'bold' => true,
					'size' => 16,
				],
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
				]

			]);
			
		
			foreach($options as $option => $group) {
				$response = [];
			//	$row_number++;
			    $row_number+=2;
				$sheet->setCellValue('A' . ($row_number-1), '');
				$sheet->setCellValue('B' . ($row_number-1), '');
				$sheet->setCellValue('C' . ($row_number-1), '');
				$sheet->setCellValue('D' . ($row_number-1), '');
				$sheet->setCellValue('E' . ($row_number-1),'');
				$sheet->setCellValue('F' . ($row_number-1), 'Ventas');
				$sheet->setCellValue('G' . ($row_number-1), 'TM');
				$sheet->setCellValue('H' . ($row_number-1), '');
				$sheet->setCellValue('I' . ($row_number-1), '');
				
				$sheet->setCellValue('J' . ($row_number-1), '');
				$sheet->setCellValue('K' . ($row_number-1), '');
				$sheet->setCellValue('L' . ($row_number-1), 'MB');
				$sheet->setCellValue('M' . ($row_number-1), '');
				$sheet->setCellValue('N' . ($row_number-1),'');
				$sheet->setCellValue('O' . ($row_number-1), '');
				$sheet->setCellValue('P' . ($row_number-1), '');
				$sheet->setCellValue('Q' . ($row_number-1), 'Venta');
				$sheet->setCellValue('R' . ($row_number-1), 'Soles');
				$sheet->setCellValue('S' . ($row_number-1), '');
				$sheet->setCellValue('T' . ($row_number-1), '');
	            $sheet->setCellValue('U' . ($row_number-1), '');
	            $sheet->setCellValue('V' . ($row_number-1), 'Costos');
				$sheet->setCellValue('W' . ($row_number-1), 'Unitarios');
	            $sheet->setCellValue('X' . ($row_number-1), '');
	            $sheet->setCellValue('Y' . ($row_number-1), '');
			//	$sheet->setCellValue('Z' . ($row_number-1), '');
	        //  $sheet->setCellValue('AA' . ($row_number-1), '');
	        //  $sheet->setCellValue('AB' . ($row_number-1), '');
	        //  $sheet->setCellValue('AC' . ($row_number-1), '');
			//	$sheet->setCellValue('AD' . ($row_number-1), '');
			//	$sheet->setCellValue('AE' . ($row_number-1), '');
			//	$sheet->setCellValue('AF' . ($row_number-1), '');
			//	$sheet->setCellValue('AG' . ($row_number-1), '');
			//	$sheet->setCellValue('AH' . ($row_number-1), '');
			//	$sheet->setCellValue('AI' . ($row_number-1), '');
			//	$sheet->setCellValue('AJ' . ($row_number-1), '');
			//	$sheet->setCellValue('AK' . ($row_number-1), '');


				$sheet->getStyle('D' . ($row_number-1). ':I' . ($row_number-1))->applyFromArray([
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
						'startColor' => array('rgb' => '0F6626')
					]
				]);

				$sheet->getStyle('J' . ($row_number-1). ':O' . ($row_number-1))->applyFromArray([
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
						'startColor' => array('rgb' => '5CD618')
					]
				]);

				$sheet->getStyle('P' . ($row_number-1). ':S' . ($row_number-1))->applyFromArray([
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
						'startColor' => array('rgb' => '4C621B')
					]
				]);
				$sheet->getStyle('T' . ($row_number-1). ':Y' . ($row_number-1))->applyFromArray([
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
						'startColor' => array('rgb' => '66D56B')
					]
				]);


				$sheet->setCellValue('A' . $row_number, '#');
				$sheet->setCellValue('B' . $row_number, 'Tipo');
				$sheet->setCellValue('C' . $row_number, 'Nombre');
				$sheet->setCellValue('D' . $row_number, 'TM_MA');
				$sheet->setCellValue('E' . $row_number, 'TM');
				$sheet->setCellValue('F' . $row_number, 'PROY');
				$sheet->setCellValue('G' . $row_number, 'TM%MA');
				$sheet->setCellValue('H' . $row_number, 'PPTO');
				$sheet->setCellValue('I' . $row_number, 'TM%PPTO');
				$sheet->setCellValue('J' . $row_number, 'MB_MA');
				$sheet->setCellValue('K' . $row_number, 'MB');
				$sheet->setCellValue('L' . $row_number, 'MB_PROY');
				$sheet->setCellValue('M' . $row_number, 'MB%MA');
				$sheet->setCellValue('N' . $row_number, 'PPTO_MB');
				$sheet->setCellValue('O' . $row_number, 'MB%PPTO');
				$sheet->setCellValue('P' . $row_number, 'S./ MES_ANT');
				$sheet->setCellValue('Q' . $row_number, 'S./ MES');
				$sheet->setCellValue('R' . $row_number, 'PROY');
				$sheet->setCellValue('S' . $row_number, 'S./%MA');
				$sheet->setCellValue('T' . $row_number, 'CX_MA');
	            $sheet->setCellValue('U' . $row_number, 'PX_MA');
	            $sheet->setCellValue('V' . $row_number, 'MBU_MA');
				$sheet->setCellValue('W' . $row_number, 'CX');
	            $sheet->setCellValue('X' . $row_number, 'PX');
	            $sheet->setCellValue('Y' . $row_number, 'MBU');
			 //	$sheet->setCellValue('Z' . $row_number, 'DIF CX');
	         //  $sheet->setCellValue('AA' . $row_number, 'DIF PX');
	         //  $sheet->setCellValue('AB' . $row_number, 'DIF MBU');
	         //  $sheet->setCellValue('AC' . $row_number, 'RATIO PPTO');
			 //	$sheet->setCellValue('AD' . $row_number, 'RATIO TM');
			 //	$sheet->setCellValue('AE' . $row_number, 'RATIO %');
			 //	$sheet->setCellValue('AF' . $row_number, 'C.Glp M_A');
			 //	$sheet->setCellValue('AG' . $row_number, 'C.Aprov.M_A');
			 //	$sheet->setCellValue('AH' . $row_number, 'C. Total M_A');
			 //	$sheet->setCellValue('AI' . $row_number, 'C.Glp');
			 //	$sheet->setCellValue('AJ' . $row_number, 'C.Aprov');
			 //	$sheet->setCellValue('AK' . $row_number, 'C. Total');




				$sheet->getStyle('A' . $row_number . ':Y' . $row_number)->applyFromArray([
					'font' => ['bold' => true],
					'name'      =>  'Calibri',
					'size'      =>  10,
				]);


		
			
			 $sheet->getStyle('A'. $row_number . ':F' . $row_number)->applyFromArray([
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
			 $sheet->getStyle('G'. $row_number )->applyFromArray([
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
					'startColor' => array('rgb' => 'E91010')
				]
			 ]);
			 $sheet->getStyle('H'. $row_number )->applyFromArray([
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

			 $sheet->getStyle('I'. $row_number )->applyFromArray([
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
					'startColor' => array('rgb' => 'E91010')
				]
			 ]);

			 $sheet->getStyle('J'. $row_number . ':L' . $row_number)->applyFromArray([
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

		
			 $sheet->getStyle('M'. $row_number)->applyFromArray([
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
					'startColor' => array('rgb' => 'E91010')
				]
			 ]);

			
			 $sheet->getStyle('N'. $row_number )->applyFromArray([
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

			 $sheet->getStyle('O'. $row_number)->applyFromArray([
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
					'startColor' => array('rgb' => 'E91010')
				]
			 ]);

			 $sheet->getStyle('P'. $row_number . ':R' . $row_number)->applyFromArray([
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

			 $sheet->getStyle('S'. $row_number)->applyFromArray([
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

			 $sheet->getStyle('T'. $row_number . ':V' . $row_number)->applyFromArray([
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

			 $sheet->getStyle('W'. $row_number . ':Y' . $row_number)->applyFromArray([
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
					'startColor' => array('rgb' => '4B700D')
				]
			 ]);

		      /*	$sheet->getStyle('Z'. $row_number . ':AB' . $row_number)->applyFromArray([
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

			 $sheet->getStyle('AC'. $row_number)->applyFromArray([
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
					'startColor' => array('rgb' => 'FF8C00')
				]
			 ]);

			 $sheet->getStyle('AD'. $row_number . ':AE' . $row_number)->applyFromArray([
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
					'startColor' => array('rgb' => 'CD5C5C')
				]
			 ]);

			 $sheet->getStyle('AF'. $row_number . ':AH' . $row_number)->applyFromArray([
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
					'startColor' => array('rgb' => '483D8B')
				]
			 ]);

			 $sheet->getStyle('AI'. $row_number . ':AK' . $row_number)->applyFromArray([
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
					'startColor' => array('rgb' => '4B0082')
				]
			]);*/


				$totals_last_tm = 0.00;
	            $totals_tm = 0.00;
	            $totals_proy_tm = 0.00;
	            $totals_tm_ppto = 0.00;
	            $totals_last_mb = 0.00;
	            $totals_mb = 0.00;
	            $totals_proy_mb = 0.00;
	            $totals_mb_ppto = 0.00;
	            $totals_last_total = 0.00;
	            $totals_total = 0.00;
	            $totals_proy_soles = 0.00;


				foreach ($group as $report) {



									// <<--------------------------------------------------------------CALCULO DE MB -------------------------------------------------------------------------------->>
	
									$report->unit = $report ['unit'];
									$report->type = $report ['type'];
									
									$year_prev = ($price_mes == '01') ? $price_year - 1 : $price_year;
									$mes_prev = ($price_mes == '01') ? '12' : $price_mes - 1;
									
									//pdd envasado
									$movements_e = WarehouseMovement::join('warehouse_movement_details', 'warehouse_movement_details.warehouse_movement_id', '=', 'warehouse_movements.id')
											->where(function ($query) use ($price_year , $price_mes) {
												$query->where('warehouse_movements.price_año', $price_year)
													->where('warehouse_movements.price_mes', $price_mes)
													->where('warehouse_movements.movement_type_id', 30)
													->where('warehouse_movements.origin', 26);
											})
											->orWhere(function ($query) use ($year_prev , $mes_prev) {
												$query->where('warehouse_movements.price_año', $year_prev)
													->where('warehouse_movements.price_mes', $mes_prev)
													->where('warehouse_movement_details.digit2','<>', null)
													->where('warehouse_movement_details.digit2','<>', 0)
													->where('warehouse_movements.movement_type_id', 30)
													->where('warehouse_movements.origin', 26);
											})
											->where('warehouse_movements.movement_class_id', 2)
											->select('warehouse_movement_details.id',
											'warehouse_movements.warehouse_type_id',
											'warehouse_movements.movement_type_id',
											'warehouse_movements.referral_serie_number',
											'warehouse_movements.referral_voucher_number',
											'warehouse_movements.origin',
											'warehouse_movement_details.digit1',
											'warehouse_movement_details.digit2',
											'warehouse_movements.soles_1',
											'warehouse_movements.soles_2',
											'warehouse_movements.price_mes'
											)
											->get();
				
											$total_e = 0 ;
											$quantity_e = 0 ;
				
											foreach($movements_e as $e){
				
												if( $e->digit2 != null &&  $e->digit2 != 0){
				
													if($e->price_mes == $price_mes ){
				
														$quantity_e += $e->digit1;
														$total_e += $e->soles_1 ;
				
													}else{
														$quantity_e += $e->digit2;
														$total_e += $e->soles_2 ;
													}
													
												}else{
													$quantity_e += $e->digit1;
													$total_e +=  $e->soles_1 ;
												}
											}  
				
								$report->soles_glp_e +=$total_e;
								$report->kgs_glp_e +=$quantity_e;
				
								//pdd granel
				
									$movements_g = WarehouseMovement::join('warehouse_movement_details', 'warehouse_movement_details.warehouse_movement_id', '=', 'warehouse_movements.id')
									->where(function ($query) use ($price_year , $price_mes) {
										$query->where('warehouse_movements.price_año', $price_year)
											->where('warehouse_movements.price_mes', $price_mes)
											->where('warehouse_movements.movement_type_id', 30)
											->where('warehouse_movements.origin', 27);
									})
									->orWhere(function ($query) use ($year_prev , $mes_prev) {
										$query->where('warehouse_movements.price_año', $year_prev)
											->where('warehouse_movements.price_mes', $mes_prev)
											->where('warehouse_movement_details.digit2','<>', null)
											->where('warehouse_movement_details.digit2','<>', 0)
											->where('warehouse_movements.movement_type_id', 30)
											->where('warehouse_movements.origin', 27);
									})
									->where('warehouse_movements.movement_class_id', 2)
									->select('warehouse_movement_details.id',
									'warehouse_movements.warehouse_type_id',
									'warehouse_movements.movement_type_id',
									'warehouse_movements.referral_serie_number',
									'warehouse_movements.referral_voucher_number',
									'warehouse_movements.origin',
									'warehouse_movement_details.digit1',
									'warehouse_movement_details.digit2',
									'warehouse_movements.soles_1',
									'warehouse_movements.soles_2',
									'warehouse_movements.price_mes'
									)
									->get();
				
									$total_g = 0 ;
									$quantity_g = 0 ;
				
									foreach($movements_g as $e){
				
										if( $e->digit2 != null &&  $e->digit2 != 0){
				
											if($e->price_mes == $price_mes ){
				
												$quantity_g += $e->digit1;
												$total_g += $e->soles_1 ;
				
											}else{
												$quantity_g += $e->digit2;
												$total_g += $e->soles_2 ;
											}
											
										}else{
											$quantity_g += $e->digit1;
											$total_g +=  $e->soles_1 ;
										}
									}   
				
								$report->soles_glp_g +=$total_g;
								$report->kgs_glp_g +=$quantity_g;
				
								if ( $report->unit != 2)
				
								$report->cost_glp = $report['kgs_glp_e'] > 0 ? ($report['soles_glp_e']/($report['kgs_glp_e']/1000)/100): null;
								else
									{
									$report->cost_glp = $report['kgs_glp_g'] > 0 ? ($report['soles_glp_g']/($report['kgs_glp_g']/1000)/100): null;	
									}
				
									$sheet->setCellValue('W' . (2), 'CANTIDAD E');
									$sheet->setCellValue('W' . (3), $quantity_e);
									$sheet->setCellValue('X' . (2), 'CANTIDAD G');
									$sheet->setCellValue('X' . (3), $quantity_g);
				
									$sheet->setCellValue('Y' . (2), 'SOLES E');
									$sheet->setCellValue('Y' . (3), $total_e);
									$sheet->setCellValue('Z' . (2), 'SOLES G');
									$sheet->setCellValue('Z' . (3), $total_g);
				
				
									$sheet->setCellValue('A' . ($row_number-1), '');
				
								//<<-------------------------------------------------------------------------------------------------------------------------------------------------------------------->>
				
				
				
				/*------------------------------------------------------------------------MARGEN DE GRANEL ---------------------------------------------------------*/
				
				$cost_e =  $report['kgs_glp_e'] > 0 ? ($report['soles_glp_e']/($report['kgs_glp_e']/1000)/100): null;
				
				
				$cost_g = $report['kgs_glp_g'] > 0 ? ($report['soles_glp_g']/($report['kgs_glp_g']/1000)/100): null;	
				
				
				$tm_env = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
										->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
										->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
										->where('sales.sale_date', '>=', $mes_date)
										->where('sales.sale_date', '<=', $initial_date)
										->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
										->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
										->where('clients.route_id',19)
										->whereIn('clients.channel_id',[5,6,12])
										->whereIn('clients.manager_id',[7,8])
										->select('kg')
										->sum('kg');
					
				$soles_env = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
										 ->where('sales.sale_date', '>=', $mes_date)
										 ->where('sales.sale_date', '<=', $initial_date)
										 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
										 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
										 ->whereIn('clients.route_id',[19])
										 ->whereIn('clients.channel_id',[5,6,12])
										 ->whereIn('clients.manager_id',[7,8])
										 ->select('total')
										 ->sum('total');
				
				
				 $tm_gra = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
										 ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
										 ->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
										 ->where('sales.sale_date', '>=', $mes_date)
										 ->where('sales.sale_date', '<=', $initial_date)
										 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
										 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
										 ->whereIn('clients.route_id',[26])
										 ->whereIn('clients.channel_id',[5,6,12])
										 ->whereIn('clients.manager_id',[7,8,9])
										 ->select('kg')
										 ->sum('kg');
					 
				 $soles_gra = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
										  ->where('sales.sale_date', '>=', $mes_date)
										  ->where('sales.sale_date', '<=', $initial_date)
										  ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
										  ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
										  ->whereIn('clients.route_id',[26])
										  ->whereIn('clients.channel_id',[5,6,12])
										  ->whereIn('clients.manager_id',[7,8,9])
										  ->select('total')
										  ->sum('total');



				if ( $report->year == '2022' || $report->year == '2023'){
				
				// <<--------------------------------------------------------------CALCULO DE TM EXCEL-------------------------------------------------------------------------------->>

				if ( $report->option == 1) {
					$tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
					->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
					->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
					->where('sales.sale_date', '>=', $mes_date)
					->where('sales.sale_date', '<=', $initial_date)
					->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
					->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
					->where('clients.business_unit_id',  $report->business_id)
					->select('kg')
					->sum('kg');

					$soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
					->where('sales.sale_date', '>=', $mes_date)
					->where('sales.sale_date', '<=', $initial_date)
					->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,32])
					->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
					->where('clients.business_unit_id',  $report->business_id)
					->select('total')
					->sum('total');

					$mb_final=0;
					$mb_final_gra=0;
				

					}
		
					elseif ( $report->option == 2 ) {
					$tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
					->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
					->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
					->where('sales.sale_date', '>=', $mes_date)
					->where('sales.sale_date', '<=', $initial_date)
					->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
					->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
					->where('clients.manager_id', $report->manager_id)
					->select('kg')
					->sum('kg');

					$soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
					->where('sales.sale_date', '>=', $mes_date)
					->where('sales.sale_date', '<=', $initial_date)
					->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
					->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
					->where('clients.manager_id', $report->manager_id)
					->select('total')
					->sum('total');

					$mb_final=0;
					$mb_final_gra=0;

				
				} 
			
			 elseif ( $report->option == 3) {
				if ($report->orden >=1 && $report->orden <=5)
				{
				 $tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
				 ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				 ->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
				 ->where('sales.sale_date', '>=', $mes_date)
				 ->where('sales.sale_date', '<=', $initial_date)
				 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
				 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
				 ->where('clients.sector_id',$report->sector_id)
				 ->select('kg')
				 ->sum('kg');

				 $soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
	 			->where('sales.sale_date', '>=', $mes_date)
	 			->where('sales.sale_date', '<=', $initial_date)
	 			->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
	 			->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
	 			->where('clients.sector_id',$report->sector_id)
	 			->select('total')
	 			->sum('total');

				 $mb_final=0;
				 $mb_final_gra=0;

				} elseif ( $report->orden >=6 && $report->orden <=9)
				{ 	$tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
					->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
					->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
					->where('sales.sale_date', '>=', $mes_date)
					->where('sales.sale_date', '<=', $initial_date)
					->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
					->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
					->where('clients.channel_id',$report->channel_id)
					->select('kg')
					->sum('kg');

					$soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
					->where('sales.sale_date', '>=', $mes_date)
					->where('sales.sale_date', '<=', $initial_date)
					->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
					->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
					->where('clients.channel_id',$report->channel_id)
					->select('total')
					->sum('total');

					$mb_final=0;
					$mb_final_gra=0;
				}
			 }
						
			 elseif ( $report->option == 4) {
				if ($report->orden >=1 && $report->orden <=24)
                {
				 $tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
				 ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				 ->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
				 ->where('sales.sale_date', '>=', $mes_date)
				 ->where('sales.sale_date', '<=', $initial_date)
				 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
				 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
				 ->where('clients.route_id',$report->route_id)
				 ->select('kg')
				 ->sum('kg');
				 $soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				 ->where('sales.sale_date', '>=', $mes_date)
	 			 ->where('sales.sale_date', '<=', $initial_date)
	 			 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
				 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
				 ->where('clients.route_id',$report->route_id)
				 ->select('total')
				 ->sum('total');
				 $mb_final=0;
				 $mb_final_gra=0;

				} 

				elseif ($report->orden >=25 && $report->orden <=32) 
				{
				 $tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
				 ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				 ->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
				 ->where('sales.sale_date', '>=', $mes_date)
				 ->where('sales.sale_date', '<=', $initial_date)
				 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
				 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
				 ->where('clients.route_id',$report->route_id)
				 ->where('clients.zone_id',$report->zone_id)
				 ->select('kg')
				 ->sum('kg');
				 $soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
	 			 ->where('sales.sale_date', '>=', $mes_date)
				 ->where('sales.sale_date', '<=', $initial_date)
				 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
				 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
				 ->where('clients.route_id',$report->route_id)
				 ->where('clients.zone_id',$report->zone_id)
				 ->select('total')
				 ->sum('total');
				  $mb_final=0;
				  $mb_final_gra=0;
				} 

				elseif ($report->orden >=33 && $report->orden <=34) 
				{
				 $tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
				 ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				 ->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
				 ->where('sales.sale_date', '>=', $mes_date)
				 ->where('sales.sale_date', '<=', $initial_date)
				 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
				 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
				 ->where('clients.route_id',$report->route_id)				
				 ->where('clients.manager_id',$report->manager_id)
				 ->where('clients.zone_id',9)
				 ->select('kg')
				 ->sum('kg');

				 $soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				 ->where('sales.sale_date', '>=', $mes_date)
				 ->where('sales.sale_date', '<=', $initial_date)
				 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
				 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
				 ->where('clients.route_id',$report->route_id)
				 ->where('clients.zone_id',9)
				 ->where('clients.manager_id',$report->manager_id)
				 ->select('total')
				 ->sum('total');
				 $mb_final=0;
				 $mb_final_gra=0;

				}

				elseif ($report->orden >=35 && $report->orden <=38) 
				{
				 $tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
				 ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				 ->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
				 ->where('sales.sale_date', '>=', $mes_date)
				 ->where('sales.sale_date', '<=', $initial_date)
				 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
				 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
				 ->where('clients.route_id',$report->route_id)
				 ->select('kg')
				 ->sum('kg');

				 $soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				 ->where('sales.sale_date', '>=', $mes_date)
				 ->where('sales.sale_date', '<=', $initial_date)
				 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
				 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
				 ->where('clients.route_id',$report->route_id)
				 ->select('total')
				 ->sum('total');
				 $mb_final=0;
				 $mb_final_gra=0;


				} 

			 }
			
			 
			 elseif ( $report->option == 5) {
				 $tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
				 ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				 ->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
				 ->where('sales.sale_date', '>=', $mes_date)
				 ->where('sales.sale_date', '<=', $initial_date)
				 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
				 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
				 ->where('clients.channel_id',$report->channel_id)
				 ->where('clients.manager_id',$report->manager_id)
				 ->select('kg')
				 ->sum('kg');

				 $soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				 ->where('sales.sale_date', '>=', $mes_date)
				 ->where('sales.sale_date', '<=', $initial_date)
				 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
				 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
	 			 ->where('clients.channel_id',$report->channel_id)
				 ->where('clients.manager_id',$report->manager_id)
				 ->select('total')
				 ->sum('total');

				 $mb_final=0;
				 $mb_final_gra=0;


			    }
				
				elseif ( $report->option == 6) {
					$tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
					->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
					->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
					->where('sales.sale_date', '>=', $mes_date)
					->where('sales.sale_date', '<=', $initial_date)
					->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
					->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
					->where('clients.channel_id',$report->channel_id)
					->where('clients.manager_id',$report->manager_id)
					->select('kg')
					->sum('kg');

					$soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
					 ->where('sales.sale_date', '>=', $mes_date)
					 ->where('sales.sale_date', '<=', $initial_date)
					 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
					 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
					 ->where('clients.channel_id',$report->channel_id)
					 ->where('clients.manager_id',$report->manager_id)
					 ->select('total')
					 ->sum('total');

					 $mb_final=0;
					 $mb_final_gra=0;
   
				   }			 				
			

				   elseif ( $report->option == 7) {
					$tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
					->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
					->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
					->where('sales.sale_date', '>=', $mes_date)
					->where('sales.sale_date', '<=', $initial_date)
					->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
					->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
					->where('clients.channel_id',$report->channel_id)
					->where('clients.manager_id',$report->manager_id)
					->select('kg')
					->sum('kg');

					$soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
					 ->where('sales.sale_date', '>=', $mes_date)
					 ->where('sales.sale_date', '<=', $initial_date)
					 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
					 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
					 ->where('clients.channel_id',$report->channel_id)
					 ->where('clients.manager_id',$report->manager_id)
					 ->select('total')
					 ->sum('total');

					 $mb_final=0;
					 $mb_final_gra=0;
   

				   }			 				
			
				   elseif ( $report->option == 8) {
					$tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
					->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
					->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
					->where('sales.sale_date', '>=', $mes_date)
					->where('sales.sale_date', '<=', $initial_date)
					->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
					->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
					->where('clients.sector_id',$report->sector_id)
					->where('clients.manager_id',$report->manager_id)
					->select('kg')
					->sum('kg');

					$soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
					 ->where('sales.sale_date', '>=', $mes_date)
					 ->where('sales.sale_date', '<=', $initial_date)
					 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
					 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
					 ->where('clients.sector_id',$report->sector_id)
					 ->where('clients.manager_id',$report->manager_id)
					 ->select('total')
					 ->sum('total');

					 $mb_final=0;
					 $mb_final_gra=0;
   
				   }			 				
			
				   elseif ( $report->option == 9) {
					$tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
					->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
					->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
					->where('sales.sale_date', '>=', $mes_date)
					->where('sales.sale_date', '<=', $initial_date)
					->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
					->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
					->where('clients.sector_id',$report->sector_id)
					->where('clients.manager_id',$report->manager_id)
					->select('kg')
					->sum('kg');

					$soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
	 				 ->where('sales.sale_date', '>=', $mes_date)
					 ->where('sales.sale_date', '<=', $initial_date)
					 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
					 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
					 ->where('clients.sector_id',$report->sector_id)
					 ->where('clients.manager_id',$report->manager_id)
					 ->select('total')
					 ->sum('total');
					 $mb_final=0;
					 $mb_final_gra=0;
   
				   }			 				
			
				   elseif ( $report->option == 10) {
					$tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
					->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
					->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
					->where('sales.sale_date', '>=', $mes_date)
					->where('sales.sale_date', '<=', $initial_date)
					->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
					->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
					->where('clients.sector_id',$report->sector_id)
					->where('clients.manager_id',$report->manager_id)
					->select('kg')
					->sum('kg');

					$soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
	 				 ->where('sales.sale_date', '>=', $mes_date)
					 ->where('sales.sale_date', '<=', $initial_date)
					 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
					 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
					 ->where('clients.sector_id',$report->sector_id)
					 ->where('clients.manager_id',$report->manager_id)
					 ->select('total')
					 ->sum('total');

					 $mb_final=0;
					 $mb_final_gra=0;
   
				   }			 				
			   			
				 }





				 

				if ($report->year == '2024'){

					

						if ( $report->option == 1) {
							$tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
							->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
							->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
							->where('sales.sale_date', '>=', $mes_date)
							->where('sales.sale_date', '<=', $initial_date)
							->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
							->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
							->where('clients.business_unit_id',  $report->business_id)
							->select('kg')
							->sum('kg');
		
							$soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
							->where('sales.sale_date', '>=', $mes_date)
							->where('sales.sale_date', '<=', $initial_date)
							->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,32])
							->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
							->where('clients.business_unit_id',  $report->business_id)
							->select('total')
							->sum('total');
							$mb_final=0;
							$mb_final_gra=0;
		  
		
							}
				
							elseif ( $report->option == 2 ) {
							$tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
							->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
							->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
							->where('sales.sale_date', '>=', $mes_date)
							->where('sales.sale_date', '<=', $initial_date)
							->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
							->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
							->where('clients.manager_id', $report->manager_id)
							->select('kg')
							->sum('kg');
		
							$soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
							->where('sales.sale_date', '>=', $mes_date)
							->where('sales.sale_date', '<=', $initial_date)
							->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
							->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
							->where('clients.manager_id', $report->manager_id)
							->select('total')
							->sum('total');

							$mb_final=0;
							$mb_final_gra=0;
		  
							} 
					
					 elseif ( $report->option == 3) {
						if ($report->orden >=1 && $report->orden <=5)
						{
						 $tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
						 ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
						 ->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
						 ->where('sales.sale_date', '>=', $mes_date)
						 ->where('sales.sale_date', '<=', $initial_date)
						 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
						 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
						 ->where('clients.sector_id',$report->sector_id)
						 ->select('kg')
						 ->sum('kg');
		
						 $soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
						 ->where('sales.sale_date', '>=', $mes_date)
						 ->where('sales.sale_date', '<=', $initial_date)
						 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
						 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
						 ->where('clients.sector_id',$report->sector_id)
						 ->select('total')
						 ->sum('total');

						 $mb_final=0;
						 $mb_final_gra=0;
	   
		
						} 
						elseif ( $report->orden >=6 && $report->orden <=9)
						{ 	$tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
							->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
							->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
							->where('sales.sale_date', '>=', $mes_date)
							->where('sales.sale_date', '<=', $initial_date)
							->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
							->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
							->where('clients.channel_id',$report->channel_id)
							->select('kg')
							->sum('kg');
		
							$soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
							->where('sales.sale_date', '>=', $mes_date)
							->where('sales.sale_date', '<=', $initial_date)
							->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
							->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
							->where('clients.channel_id',$report->channel_id)
							->select('total')
							->sum('total');

							$mb_final=0;
							$mb_final_gra=0;
		  
						}
					 }
								
					 elseif ( $report->option == 4) {
						if ($report->orden >=1 && $report->orden <=24)
						{
						 $tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
						 ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
						 ->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
						 ->where('sales.sale_date', '>=', $mes_date)
						 ->where('sales.sale_date', '<=', $initial_date)
						 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
						 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
						 ->where('clients.route_id',$report->route_id)
						 ->select('kg')
						 ->sum('kg');
						 $soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
						 ->where('sales.sale_date', '>=', $mes_date)
						  ->where('sales.sale_date', '<=', $initial_date)
						  ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
						 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
						 ->where('clients.route_id',$report->route_id)
						 ->select('total')
						 ->sum('total');

						 $mb_final=0;
						 $mb_final_gra=0;
	   
						} 
		
						elseif ($report->orden >=25 && $report->orden <=32) 
						{
						 $tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
						 ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
						 ->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
						 ->where('sales.sale_date', '>=', $mes_date)
						 ->where('sales.sale_date', '<=', $initial_date)
						 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
						 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
						 ->where('clients.route_id',$report->route_id)
						 ->where('clients.zone_id',$report->zone_id)
						 ->select('kg')
						 ->sum('kg');
						 $soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
						  ->where('sales.sale_date', '>=', $mes_date)
						 ->where('sales.sale_date', '<=', $initial_date)
						 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
						 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
						 ->where('clients.route_id',$report->route_id)
						 ->where('clients.zone_id',$report->zone_id)
						 ->select('total')
						 ->sum('total');

						 $mb_final=0;
						 $mb_final_gra=0;
	   
						} 
		
						elseif ($report->orden >=33 && $report->orden <=34) 
						{
						 $tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
						 ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
						 ->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
						 ->where('sales.sale_date', '>=', $mes_date)
						 ->where('sales.sale_date', '<=', $initial_date)
						 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
						 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
						 ->where('clients.route_id',$report->route_id)				
						 ->where('clients.manager_id',$report->manager_id)
						 ->where('clients.zone_id',9)
						 ->select('kg')
						 ->sum('kg');
		
						 $soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
						 ->where('sales.sale_date', '>=', $mes_date)
						 ->where('sales.sale_date', '<=', $initial_date)
						 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
						 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
						 ->where('clients.route_id',$report->route_id)
						 ->where('clients.zone_id',9)
						 ->where('clients.manager_id',$report->manager_id)
						 ->select('total')
						 ->sum('total');

						 $mb_final=0;
						 $mb_final_gra=0;
	   
						}
		
						elseif ($report->orden >=35 && $report->orden <=38) 
						{
						 $tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
						 ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
						 ->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
						 ->where('sales.sale_date', '>=', $mes_date)
						 ->where('sales.sale_date', '<=', $initial_date)
						 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
						 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
						 ->where('clients.route_id',$report->route_id)
						 ->select('kg')
						 ->sum('kg');
		
						 $soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
						 ->where('sales.sale_date', '>=', $mes_date)
						 ->where('sales.sale_date', '<=', $initial_date)
						 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
						 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
						 ->where('clients.route_id',$report->route_id)
						 ->select('total')
						 ->sum('total');

						 $mb_final=0;
						 $mb_final_gra=0;
	   
		
						} 

					}
												
				 
				 elseif ( $report->option == 5) {
					 $tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
					 ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
					 ->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
					 ->where('sales.sale_date', '>=', $mes_date)
					 ->where('sales.sale_date', '<=', $initial_date)
					 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
					 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
					 ->where('clients.channel_id',$report->channel_id)
					 ->where('clients.manager_id',$report->manager_id)
					 ->select('kg')
					 ->sum('kg');
	
					 $soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
					 ->where('sales.sale_date', '>=', $mes_date)
					 ->where('sales.sale_date', '<=', $initial_date)
					 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
					 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
					  ->where('clients.channel_id',$report->channel_id)
					 ->where('clients.manager_id',$report->manager_id)
					 ->select('total')
					 ->sum('total');

					 $mb_final=0;
					 $mb_final_gra=0;
   
	
					}
					
					elseif ( $report->option == 6) {
						$tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
						->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
						->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
						->where('sales.sale_date', '>=', $mes_date)
						->where('sales.sale_date', '<=', $initial_date)
						->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
						->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
						->where('clients.channel_id',$report->channel_id)
						->where('clients.manager_id',$report->manager_id)
						->select('kg')
						->sum('kg');
	
						$soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
						 ->where('sales.sale_date', '>=', $mes_date)
						 ->where('sales.sale_date', '<=', $initial_date)
						 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
						 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
						 ->where('clients.channel_id',$report->channel_id)
						 ->where('clients.manager_id',$report->manager_id)
						 ->select('total')
						 ->sum('total');

						 $mb_final=0;
						 $mb_final_gra=0;
	   
					}			 				
				
	
					elseif ( $report->option == 7) {
						$tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
						->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
						->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
						->where('sales.sale_date', '>=', $mes_date)
						->where('sales.sale_date', '<=', $initial_date)
						->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
						->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
						->where('clients.channel_id',$report->channel_id)
						->where('clients.manager_id',$report->manager_id)
						->select('kg')
						->sum('kg');
	
						$soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
						 ->where('sales.sale_date', '>=', $mes_date)
						 ->where('sales.sale_date', '<=', $initial_date)
						 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
						 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
						 ->where('clients.channel_id',$report->channel_id)
						 ->where('clients.manager_id',$report->manager_id)
						 ->select('total')
						 ->sum('total');

						 $mb_final=0;
						 $mb_final_gra=0;
	   
	
					}			 				
				
					elseif ( $report->option == 8) {
						$tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
						->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
						->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
						->where('sales.sale_date', '>=', $mes_date)
						->where('sales.sale_date', '<=', $initial_date)
						->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
						->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
						->where('clients.sector_id',$report->sector_id)
						->where('clients.manager_id',$report->manager_id)
						->select('kg')
						->sum('kg');
	
						$soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
						 ->where('sales.sale_date', '>=', $mes_date)
						 ->where('sales.sale_date', '<=', $initial_date)
						 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
						 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
						 ->where('clients.sector_id',$report->sector_id)
						 ->where('clients.manager_id',$report->manager_id)
						 ->select('total')
						 ->sum('total');

						 $mb_final=0;
						 $mb_final_gra=0;
	   
					}			 				
				
					elseif ( $report->option == 9) {
						$tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
						->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
						->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
						->where('sales.sale_date', '>=', $mes_date)
						->where('sales.sale_date', '<=', $initial_date)
						->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
						->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
						->where('clients.sector_id',$report->sector_id)
						->where('clients.manager_id',$report->manager_id)
						->select('kg')
						->sum('kg');
	
						$soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
						  ->where('sales.sale_date', '>=', $mes_date)
						 ->where('sales.sale_date', '<=', $initial_date)
						 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
						 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
						 ->where('clients.sector_id',$report->sector_id)
						 ->where('clients.manager_id',$report->manager_id)
						 ->select('total')
						 ->sum('total');

						 $mb_final=0;
						 $mb_final_gra=0;
	   
					}			 				
				
					elseif ( $report->option == 10) {
						$tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
						->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
						->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
						->where('sales.sale_date', '>=', $mes_date)
						->where('sales.sale_date', '<=', $initial_date)
						->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
						->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
						->where('clients.sector_id',$report->sector_id)
						->where('clients.manager_id',$report->manager_id)
						->select('kg')
						->sum('kg');
	
						$soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
						  ->where('sales.sale_date', '>=', $mes_date)
						 ->where('sales.sale_date', '<=', $initial_date)
						 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
						 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
						 ->where('clients.sector_id',$report->sector_id)
						 ->where('clients.manager_id',$report->manager_id)
						 ->select('total')
						 ->sum('total');

						 $mb_final=0;
						 $mb_final_gra=0;
	   
					}

				

				elseif($report->month == '02' || $report->month == '03'){

					if ( $report->option == 1) {

						if ($report->orden ==1 || $report->orden ==2)
						{
						$tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
						->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
						->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
						->where('sales.sale_date', '>=', $mes_date)
						->where('sales.sale_date', '<=', $initial_date)
						->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
						->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
						->where('clients.business_unit_id',  $report->business_id)
						->where('clients.company_id',  $report->company)
						->select('kg')
						->sum('kg');
	
						$soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
						->where('sales.sale_date', '>=', $mes_date)
						->where('sales.sale_date', '<=', $initial_date)
						->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,32])
						->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
						->where('clients.business_unit_id',  $report->business_id)
						->where('clients.company_id',  $report->company)
						->select('total')
						->sum('total');

						$mb_final=0;
						$mb_final_gra=0;
	  

						}elseif ( $report->orden ==3 ){



						$tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
						->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
						->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
						->where('sales.sale_date', '>=', $mes_date)
						->where('sales.sale_date', '<=', $initial_date)
						->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
						->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
						->where('clients.business_unit_id',  $report->business_id)
						->select('kg')
						->sum('kg');
	
						$soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
						->where('sales.sale_date', '>=', $mes_date)
						->where('sales.sale_date', '<=', $initial_date)
						->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,32])
						->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
						->where('clients.business_unit_id',  $report->business_id)
						->select('total')
						->sum('total');

						$mb_final=0;
						$mb_final_gra=0;
	  
						}
						}

						
			
						elseif ( $report->option == 2 ) {

						if ($report->orden >=1 && $report->orden <=8 ||$report->orden == 11 )
						{
						$tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
						->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
						->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
						->where('sales.sale_date', '>=', $mes_date)
						->where('sales.sale_date', '<=', $initial_date)
						->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
						->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
						->where('clients.manager_id', $report->manager_id)
						->select('kg')
						->sum('kg');
	
						$soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
						->where('sales.sale_date', '>=', $mes_date)
						->where('sales.sale_date', '<=', $initial_date)
						->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
						->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
						->where('clients.manager_id', $report->manager_id)
						->select('total')
						->sum('total');


						$mb_final=0;
						$mb_final_gra=0;
	  
						}

					    elseif ( $report->orden >=9 && $report->orden <=10 ){

						$tm_env = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
						->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
						->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
						->where('sales.sale_date', '>=', $mes_date)
						->where('sales.sale_date', '<=', $initial_date)
						->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
						->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
						->where('clients.route_id', 19)
						->where('clients.manager_id', $report->manager_id)
						->select('kg')
						->sum('kg');
	
						$soles_env = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
						->where('sales.sale_date', '>=', $mes_date)
						->where('sales.sale_date', '<=', $initial_date)
						->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
						->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
						->where('clients.manager_id', $report->manager_id)
						->where('clients.route_id', 19)
						->select('total')
						->sum('total');	
						
						
						
						$tm_gra = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
						->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
						->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
						->where('sales.sale_date', '>=', $mes_date)
						->where('sales.sale_date', '<=', $initial_date)
						->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
						->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
						->where('clients.route_id', 26)
						->where('clients.manager_id', $report->manager_id)
						->select('kg')
						->sum('kg');
	
						$soles_gra = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
						->where('sales.sale_date', '>=', $mes_date)
						->where('sales.sale_date', '<=', $initial_date)
						->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
						->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
						->where('clients.manager_id', $report->manager_id)
						->where('clients.route_id', 26)
						->select('total')
						->sum('total');	

						if ($tm_env !=0)
						$mb_e = ((($soles_env/(($tm_env/1000)*100))-$cost_e) *(($tm_env/1000)*100));
						else {
							$mb_e =1;
						}

						if ($tm_gra !=0)

						$mb_g = ((($soles_gra/(($tm_gra/1000)*100))-$cost_g) *(($tm_gra/1000)*100));
						else {
							$mb_g =1;
						}


						$mb_final = $mb_e+$mb_g;


						$mb_final_gra=0;
	  

						$tm_fin = $tm_env+$tm_gra;
						$soles  = $soles_env+$soles_gra;		
					
					
					}

				} 

	
				
				 elseif ( $report->option == 3) {

					if ($report->orden >=1 && $report->orden <=5 )
					{
					 $tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
					 ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
					 ->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
					 ->where('sales.sale_date', '>=', $mes_date)
					 ->where('sales.sale_date', '<=', $initial_date)
					 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
					 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
					 ->where('clients.sector_id',$report->sector_id)
					 ->select('kg')
					 ->sum('kg');
	
					 $soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
					 ->where('sales.sale_date', '>=', $mes_date)
					 ->where('sales.sale_date', '<=', $initial_date)
					 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
					 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
					 ->where('clients.sector_id',$report->sector_id)
					 ->select('total')
					 ->sum('total');

					 $mb_final=0;
					 $mb_final_gra=0;
   
	
					} elseif ( $report->orden >=6 && $report->orden <=7)

					{ 	
						$tm_env = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
						->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
						->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
						->where('sales.sale_date', '>=', $mes_date)
						->where('sales.sale_date', '<=', $initial_date)
						->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
						->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
						->where('clients.route_id', 19)
						->where('clients.channel_id', $report->channel_id)
						->select('kg')
						->sum('kg');
	
						$soles_env = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
						->where('sales.sale_date', '>=', $mes_date)
						->where('sales.sale_date', '<=', $initial_date)
						->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
						->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
						->where('clients.channel_id', $report->channel_id)
						->where('clients.route_id', 19)
						->select('total')
						->sum('total');	
						
						
						
						$tm_gra = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
						->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
						->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
						->where('sales.sale_date', '>=', $mes_date)
						->where('sales.sale_date', '<=', $initial_date)
						->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
						->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
						->where('clients.route_id', 26)
						->where('clients.channel_id', $report->channel_id)
						->select('kg')
						->sum('kg');
	
						$soles_gra = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
						->where('sales.sale_date', '>=', $mes_date)
						->where('sales.sale_date', '<=', $initial_date)
						->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
						->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
						->where('clients.channel_id', $report->channel_id)
						->where('clients.route_id', 26)
						->select('total')
						->sum('total');	


						if ($tm_env !=0)
						$mb_e = ((($soles_env/(($tm_env/1000)*100))-$cost_e) *(($tm_env/1000)*100));
						else {
							$mb_e =1;
						}

						if ($tm_gra !=0)

						$mb_g = ((($soles_gra/(($tm_gra/1000)*100))-$cost_g) *(($tm_gra/1000)*100));
						else {
							$mb_g =1;
						}


						$mb_final = $mb_e+$mb_g;

						$mb_final_gra=0;
	  
						$tm_fin = $tm_env+$tm_gra;
						$soles  = $soles_env+$soles_gra;	
					}

					elseif ( $report->orden >=8 && $report->orden <=9){

						$tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
						->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
						->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
						->where('sales.sale_date', '>=', $mes_date)
						->where('sales.sale_date', '<=', $initial_date)
						->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
						->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
						->where('clients.channel_id',$report->channel_id)
						->select('kg')
						->sum('kg');
	   
						$soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
						->where('sales.sale_date', '>=', $mes_date)
						->where('sales.sale_date', '<=', $initial_date)
						->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
						->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
						->where('clients.channel_id',$report->channel_id)
						->select('total')
						->sum('total');

						$mb_final=0;
						$mb_final_gra=0;
	  
					}				

				 }


					
				 
				 elseif ( $report->option == 4) {
					if ($report->orden >=1 && $report->orden <=26)
					{
					 $tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
					 ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
					 ->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
					 ->where('sales.sale_date', '>=', $mes_date)
					 ->where('sales.sale_date', '<=', $initial_date)
					 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
					 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
					 ->where('clients.route_id',$report->route_id)
					 ->select('kg')
					 ->sum('kg');
					 $soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
					 ->where('sales.sale_date', '>=', $mes_date)
					  ->where('sales.sale_date', '<=', $initial_date)
					  ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
					 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
					 ->where('clients.route_id',$report->route_id)
					 ->select('total')
					 ->sum('total');

					 $mb_final=0;
					 $mb_final_gra=0;
   
					} 
	
					elseif ($report->orden >=27 && $report->orden <=32) 
					{
					 $tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
					 ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
					 ->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
					 ->where('sales.sale_date', '>=', $mes_date)
					 ->where('sales.sale_date', '<=', $initial_date)
					 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
					 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
					 ->where('clients.route_id',$report->route_id)
					 ->where('clients.zone_id',$report->zone_id)
					 ->select('kg')
					 ->sum('kg');
					 $soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
					  ->where('sales.sale_date', '>=', $mes_date)
					 ->where('sales.sale_date', '<=', $initial_date)
					 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
					 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
					 ->where('clients.route_id',$report->route_id)
					 ->where('clients.zone_id',$report->zone_id)
					 ->select('total')
					 ->sum('total');

					 $mb_final=0;
					 $mb_final_gra=0;
   
					} 
	
					elseif ($report->orden >=33 && $report->orden <=34) 
					{
					 $tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
					 ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
					 ->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
					 ->where('sales.sale_date', '>=', $mes_date)
					 ->where('sales.sale_date', '<=', $initial_date)
					 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
					 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
					 ->where('clients.route_id',$report->route_id)				
					 ->where('clients.manager_id',$report->manager_id)
					 ->where('clients.zone_id',11)
					 ->select('kg')
					 ->sum('kg');
	
					 $soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
					 ->where('sales.sale_date', '>=', $mes_date)
					 ->where('sales.sale_date', '<=', $initial_date)
					 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
					 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
					 ->where('clients.route_id',$report->route_id)
					 ->where('clients.zone_id',11)
					 ->where('clients.manager_id',$report->manager_id)
					 ->select('total')
					 ->sum('total');

					 $mb_final=0;
					 $mb_final_gra=0;
   
					}


					elseif ($report->orden >=35 && $report->orden <=36) 
					{
					 $tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
					 ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
					 ->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
					 ->where('sales.sale_date', '>=', $mes_date)
					 ->where('sales.sale_date', '<=', $initial_date)
					 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
					 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
					 ->where('clients.route_id',$report->route_id)				
					 ->where('clients.manager_id',$report->manager_id)
					 ->where('clients.zone_id',9)
					 ->select('kg')
					 ->sum('kg');
	
					 $soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
					 ->where('sales.sale_date', '>=', $mes_date)
					 ->where('sales.sale_date', '<=', $initial_date)
					 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
					 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
					 ->where('clients.route_id',$report->route_id)
					 ->where('clients.zone_id',9)
					 ->where('clients.manager_id',$report->manager_id)
					 ->select('total')
					 ->sum('total');

					 $mb_final=0;
					 $mb_final_gra=0;
   
					}
	
					elseif ($report->orden >=37 && $report->orden <=40) 
					{
					 $tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
					 ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
					 ->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
					 ->where('sales.sale_date', '>=', $mes_date)
					 ->where('sales.sale_date', '<=', $initial_date)
					 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
					 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
					 ->where('clients.route_id',$report->route_id)
					 ->select('kg')
					 ->sum('kg');
	
					 $soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
					 ->where('sales.sale_date', '>=', $mes_date)
					 ->where('sales.sale_date', '<=', $initial_date)
					 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
					 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
					 ->where('clients.route_id',$report->route_id)
					 ->select('total')
					 ->sum('total');

					 $mb_final=0;
					 $mb_final_gra=0;
   
	
					} 

				}
											
			 
			 elseif ( $report->option == 5) {

	
						$tm_env = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
						->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
						->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
						->where('sales.sale_date', '>=', $mes_date)
						->where('sales.sale_date', '<=', $initial_date)
						->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
						->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
						->where('clients.route_id', 19)
						->where('clients.channel_id', $report->channel_id)
						->where('clients.manager_id',$report->manager_id)
						->select('kg')
						->sum('kg');
	
						$soles_env = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
						->where('sales.sale_date', '>=', $mes_date)
						->where('sales.sale_date', '<=', $initial_date)
						->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
						->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
						->where('clients.channel_id', $report->channel_id)
						->where('clients.manager_id',$report->manager_id)
						->where('clients.route_id', 19)
						->select('total')
						->sum('total');	
						
						
						
						$tm_gra = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
						->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
						->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
						->where('sales.sale_date', '>=', $mes_date)
						->where('sales.sale_date', '<=', $initial_date)
						->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
						->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
						->where('clients.route_id', 26)
						->where('clients.channel_id', $report->channel_id)
						->where('clients.manager_id',$report->manager_id)
						->select('kg')
						->sum('kg');
	
						$soles_gra = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
						->where('sales.sale_date', '>=', $mes_date)
						->where('sales.sale_date', '<=', $initial_date)
						->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
						->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
						->where('clients.channel_id', $report->channel_id)
						->where('clients.manager_id',$report->manager_id)
						->where('clients.route_id', 26)
						->select('total')
						->sum('total');	


						if ($tm_env !=0)
						$mb_e = ((($soles_env/(($tm_env/1000)*100))-$cost_e) *(($tm_env/1000)*100));
						else {
							$mb_e =1;
						}

						if ($tm_gra !=0)

						$mb_g = ((($soles_gra/(($tm_gra/1000)*100))-$cost_g) *(($tm_gra/1000)*100));
						else {
							$mb_g =1;
						}


						$mb_final_gra = $mb_e+$mb_g;
						$mb_final=0;
		  

						$tm_fin = $tm_env+$tm_gra;
						$soles  = $soles_env+$soles_gra;	



				}
				
				elseif ( $report->option == 6) {
					$tm_env = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
						->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
						->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
						->where('sales.sale_date', '>=', $mes_date)
						->where('sales.sale_date', '<=', $initial_date)
						->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
						->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
						->where('clients.route_id', 19)
						->where('clients.channel_id', $report->channel_id)
						->where('clients.manager_id',$report->manager_id)
						->select('kg')
						->sum('kg');
	
						$soles_env = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
						->where('sales.sale_date', '>=', $mes_date)
						->where('sales.sale_date', '<=', $initial_date)
						->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
						->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
						->where('clients.channel_id', $report->channel_id)
						->where('clients.manager_id',$report->manager_id)
						->where('clients.route_id', 19)
						->select('total')
						->sum('total');	
						
						
						
						$tm_gra = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
						->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
						->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
						->where('sales.sale_date', '>=', $mes_date)
						->where('sales.sale_date', '<=', $initial_date)
						->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
						->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
						->where('clients.route_id', 26)
						->where('clients.channel_id', $report->channel_id)
						->where('clients.manager_id',$report->manager_id)
						->select('kg')
						->sum('kg');
	
						$soles_gra = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
						->where('sales.sale_date', '>=', $mes_date)
						->where('sales.sale_date', '<=', $initial_date)
						->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
						->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
						->where('clients.channel_id', $report->channel_id)
						->where('clients.manager_id',$report->manager_id)
						->where('clients.route_id', 26)
						->select('total')
						->sum('total');	


						if ($tm_env !=0)
						$mb_e = ((($soles_env/(($tm_env/1000)*100))-$cost_e) *(($tm_env/1000)*100));
						else {
							$mb_e =1;
						}

						if ($tm_gra !=0)

						$mb_g = ((($soles_gra/(($tm_gra/1000)*100))-$cost_g) *(($tm_gra/1000)*100));
						else {
							$mb_g =1;
						}


						$mb_final_gra = $mb_e+$mb_g;

						$mb_final=0;

	  

						$tm_fin = $tm_env+$tm_gra;
						$soles  = $soles_env+$soles_gra;	

				   }			 				
			

				   elseif ( $report->option == 7) {
					$tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
					->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
					->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
					->where('sales.sale_date', '>=', $mes_date)
					->where('sales.sale_date', '<=', $initial_date)
					->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
					->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
					->where('clients.channel_id',$report->channel_id)
					->where('clients.manager_id',$report->manager_id)
					->select('kg')
					->sum('kg');

					$soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
					 ->where('sales.sale_date', '>=', $mes_date)
					 ->where('sales.sale_date', '<=', $initial_date)
					 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
					 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
					 ->where('clients.channel_id',$report->channel_id)
					 ->where('clients.manager_id',$report->manager_id)
					 ->select('total')
					 ->sum('total');

					 $mb_final=0;
					 $mb_final_gra=0;
   

				   }			 				
			
				   elseif ( $report->option == 8) {
					$tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
					->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
					->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
					->where('sales.sale_date', '>=', $mes_date)
					->where('sales.sale_date', '<=', $initial_date)
					->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
					->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
					->where('clients.sector_id',$report->sector_id)
					->where('clients.manager_id',$report->manager_id)
					->select('kg')
					->sum('kg');

					$soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
					 ->where('sales.sale_date', '>=', $mes_date)
					 ->where('sales.sale_date', '<=', $initial_date)
					 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
					 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
					 ->where('clients.sector_id',$report->sector_id)
					 ->where('clients.manager_id',$report->manager_id)
					 ->select('total')
					 ->sum('total');

					 $mb_final=0;
					 $mb_final_gra=0;
   
				   }			 				
			
				   elseif ( $report->option == 9) {
					$tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
					->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
					->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
					->where('sales.sale_date', '>=', $mes_date)
					->where('sales.sale_date', '<=', $initial_date)
					->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
					->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
					->where('clients.sector_id',$report->sector_id)
					->where('clients.manager_id',$report->manager_id)
					->select('kg')
					->sum('kg');

					$soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
					  ->where('sales.sale_date', '>=', $mes_date)
					 ->where('sales.sale_date', '<=', $initial_date)
					 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
					 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
					 ->where('clients.sector_id',$report->sector_id)
					 ->where('clients.manager_id',$report->manager_id)
					 ->select('total')
					 ->sum('total');

					 $mb_final=0;
					 $mb_final_gra=0;
   
				   }			 				
			
				   elseif ( $report->option == 10) {
					$tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
					->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
					->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
					->where('sales.sale_date', '>=', $mes_date)
					->where('sales.sale_date', '<=', $initial_date)
					->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
					->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
					->where('clients.sector_id',$report->sector_id)
					->where('clients.manager_id',$report->manager_id)
					->select('kg')
					->sum('kg');

					$soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
					  ->where('sales.sale_date', '>=', $mes_date)
					 ->where('sales.sale_date', '<=', $initial_date)
					 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
					 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
					 ->where('clients.sector_id',$report->sector_id)
					 ->where('clients.manager_id',$report->manager_id)
					 ->select('total')
					 ->sum('total');

					 $mb_final=0;
					 $mb_final_gra=0;
   
				   }




				}


			}











						 
			if ($tm_env !=0)
						 
			$mb_e = ((($soles_env/(($tm_env/1000)*100))-$cost_e) *(($tm_env/1000)*100));
			
			else{
				$mb_e =1;
			}



$mb_g = ((($soles_gra/(($tm_gra/1000)*100))-$cost_g) *(($tm_gra/1000)*100));


$mb_granel = $mb_e+$mb_g;
						






/*----------------------------------------------------------------------------------------------------------------------------------------------------*/



				//PERMITE HALLAR EL MB DEL MES CONSULTADO
				$report->costo = $report->cost_glp;
				$report->mb_granel=$mb_granel;
				$report->mb_final=$mb_final;
				$report->mb_final_gra=$mb_final_gra;

				

				//------------------------------------------------------


									
					
				//	$report->aprov = $report ['aprov'];
				//	$report->cost_glp_final = $report ['cost_glp'] +$report ['aprov'];

					$report->name = $report ['name'];
					$report->last_tm = $report ['last_tm'];
					$report->tm = $tm_fin/1000;

					if ( $report->tm != 0)
					$report->proy_tm =  ($report ['tm']/ $day)* $report ['dias_mes'];
					else
					{
						$report->proy_tm=0;
					}

					if ( $report->last_tm != 0)
					$report->last_percentaje_tm = ($report  ['tm']/ $report['last_tm'] );
					else
					{
						$report->last_percentaje_tm=0;
					}

					$report->tm_ppto = $report ['tm_ppto'];
					if ( $report->tm_ppto != 0)
					$report->percentaje_tm =($report ['proy_tm']/ $report ['tm_ppto']);
					else
					{
						$report->percentaje_tm=0;
					}
					
					$report->last_mb = $report ['last_mb'];
					
					
					$report->last_total =  $report ['last_total'];
					$report->total =  $soles;

					if ( $report->total != 0)
					$report->proy_soles = ($report ['total']/ $day)* $report ['dias_mes'];
					else
					{
						$report->proy_soles=0;
					}


					if ( $report->last_total != 0)
					$report->percentaje_soles = ($report ['proy_soles']/$report ['last_total']);
					else
					{
						$report->percentaje_soles=0;
					}

					if($report->type ==1)
					{
						if ($report->tm !=0)
						$report->mb = ((($report->total/($report->tm *100))-$report->costo) *($report->tm*100));
						else {
						$report->mb =0;
						}

						}

						elseif ($report->type ==2){
							$report->mb =$report ['mb_granel'];
						}
						elseif ($report->type ==3){
							$report->mb =$report ['mb_final'];
						}
						elseif ($report->type ==4){
							$report->mb =$report ['mb_grane_final'];
						}
					

					if ($report->mb !=0)
					$report->proy_mb = ($report ['mb']/ $day)* $report ['dias_mes'];
					else
					{
						$report->proy_mb=0;
					}

					if ( $report->last_mb != 0)
					$report->last_percentaje_mb =($report  ['mb']/ $report ['last_mb']);
					else
					{
						$report->last_percentaje_mb=0;
					}
					$report->mb_ppto = $report ['mb_ppto'];
					if ( $report->mb_ppto != 0)
					$report->percentaje_mb =  ($report ['proy_mb']/ $report ['mb_ppto']);
					else
					{
						$report->percentaje_mb=0;
					}

					if ( $report->last_tm != 0)
					$report->px_ma =  ($report ['last_total']/ $report ['last_tm'])/100;
					else
					{
						$report->px_ma=0;
					}
					if ( $report->last_tm != 0)
					$report->mbu_ma = ($report ['last_mb']/ $report ['last_tm'])/100;
					else
					{
						$report->mbu_ma=0;
					}

					$report->costo_ma =$report ['px_ma'] - $report ['mbu_ma'];

					if ( $report->tm != 0)
					$report->px= $report ['total']/$report ['tm']/100;
					else
					{
						$report->px=0;
					}

					if ( $report->tm != 0)
					$report->mbu = ($report ['mb']/ $report ['tm'])/100;
					else
					{
						$report->mbu=0;
					}
					
					
					$report->dif_cx =  $report['costo'] - $report['costo_ma'];
					$report->dif_px = $report ['px']- $report ['px_ma'];
					$report->dif_mbu = $report ['mbu']- $report ['mbu_ma'];

					if ( $report->tm_ppto != 0)
					$report->ratio_ppto = ($report ['mb_ppto']/ $report ['tm_ppto']);
					else
					{
						$report->ratio_ppto=0;
					}
					$report->ratio_tm = ($report ['tm']/ $day);
					

					$report->last_cost_glp =  $report->soles_glp_e;
					$report->last_aprov = $report->kgs_glp_e;

					if ( $report->last_tm != 0)
					$report->last_cost_glp_final = $report ['last_mb']/$report ['last_tm'];
					else
					{
						$report->last_cost_glp_final=0;
					}


					
					/* Totals*/
					$totals_last_tm += $report->last_tm;
					$totals_tm += $report->tm;
		            $totals_proy_tm += $report->proy_tm;
		            $totals_tm_ppto += $report->tm_ppto;
		            $totals_last_mb += $report->last_mb;
		            $totals_mb += $report->mb;
		            $totals_proy_mb += $report->proy_mb;
		            $totals_mb_ppto += $report->mb_ppto;
		            $totals_last_total += $report->last_total;
		            $totals_total += $report->total;
		            $totals_proy_soles += $report->proy_soles;

                    if ( $totals_tm!= 0)
					$report->ratio = ($report ['tm']/ $totals_tm);
					else
	                {
					$report->ratio=0;
	                }
					

					$response[] = $report;
				}


				$totals = new stdClass();
				$totals->client_channel_name = 'TOTAL';
				$totals->name = '';
				$totals->last_tm = $totals_last_tm;
				$totals->tm =$totals_tm;
				$totals->proy_tm =$totals_proy_tm;
				$totals->last_percentaje_tm = '';
				$totals->tm_ppto =$totals_tm_ppto;
				$totals->percentaje_tm = '';
				$totals->last_mb = $totals_last_mb;
				$totals->mb = $totals_mb;
				$totals->proy_mb = $totals_proy_mb;
				$totals->last_percentaje_mb = '';
				$totals->mb_ppto = $totals_mb_ppto;
				$totals->percentaje_mb = '';
				$totals->last_total = $totals_last_total;
				$totals->total = $totals_total;
				$totals->proy_soles = $totals_proy_soles;
				$totals->percentaje_soles = '';
				$totals->costo_ma = '';
				$totals->px_ma = '';
				$totals->mbu_ma = '';
				$totals->costo = '';
				$totals->px = '';
				$totals->mbu= '';
			//	$totals->dif_cx = '';
			//	$totals->dif_px = '';
			//	$totals->dif_mbu = '';
			//	$totals->ratio_ppto = '';
			//	$totals->ratio_tm = '';
			//	$totals->ratio = '';
			//	$totals->last_cost_glp = '';
				$totals->last_aprov = '';
				$totals->last_cost_glp_final = '';
			//	$totals->cost_glp = '';
			//	$totals->aprov = '';
			//	$totals->cost_glp_final = '';

				$response[] = $totals;

				$sheet->getStyle('A' . ($row_number + count($group) + 1) . ':Y' . ($row_number + count($group) + 1))->applyFromArray([
					'font' => [
						'color' => array('rgb' => '000000'),
					//	'bold' => true,
						'size' => 12,
					],
					
					'fill' => [
						'fillType' => Fill::FILL_SOLID,
						'startColor' => array('rgb' => 'FFD700')  //#186F18 
					]
				]);

				$sheet->getStyle('Z' . ($row_number  - 1) . ':Y' . ($row_number  - 1))->applyFromArray([
					'font' => [
						'color' => array('rgb' => '000000'),
					//	'bold' => true,
						'size' => 12,
					],
					
					'fill' => [
						'fillType' => Fill::FILL_SOLID,
						'startColor' => array('rgb' => '4B700D')  //#186F18 
					]
				]);


			//	$sheet->insertNewRowBefore('A' . ($row_number + count($group) + 2) . ':AK' . ($row_number + count($group) + 2));



				$row_number++;

				foreach ($response as $index => $element) {
					$index++;
							
					$sheet->setCellValueExplicit('A'.$row_number, $index, DataType::TYPE_NUMERIC);
					$sheet->setCellValue('B'.$row_number, $element->client_channel_name);
					$sheet->setCellValue('C'.$row_number, $element->name);
					$sheet->setCellValue('D'.$row_number, $element->last_tm);
					$sheet->setCellValue('E'.$row_number, $element->tm);
					$sheet->setCellValue('F'.$row_number, $element->proy_tm);
					$sheet->setCellValue('G'.$row_number, $element->last_percentaje_tm);
					$sheet->setCellValue('H'.$row_number, $element->tm_ppto);
					$sheet->setCellValue('I'.$row_number, $element->percentaje_tm);
					$sheet->setCellValue('J'.$row_number, $element->last_mb);
					$sheet->setCellValue('K'.$row_number, $element->mb);
					$sheet->setCellValue('L'.$row_number, $element->proy_mb);
					$sheet->setCellValue('M'.$row_number, $element->last_percentaje_mb);
					$sheet->setCellValue('N'.$row_number, $element->mb_ppto);
					$sheet->setCellValue('O'.$row_number, $element->percentaje_mb);
					$sheet->setCellValue('P'.$row_number, $element->last_total);
					$sheet->setCellValue('Q'.$row_number, $element->total);
					$sheet->setCellValue('R'.$row_number, $element->proy_soles);
					$sheet->setCellValue('S'.$row_number, $element->percentaje_soles);
					$sheet->setCellValue('T'.$row_number, $element->costo_ma);
					$sheet->setCellValue('U'.$row_number, $element->px_ma);
					$sheet->setCellValue('V'.$row_number, $element->mbu_ma);
					$sheet->setCellValue('W'.$row_number, $element->costo);
					$sheet->setCellValue('X'.$row_number, $element->px);
					$sheet->setCellValue('Y'.$row_number, $element->mbu);
				//	$sheet->setCellValue('Z'.$row_number, $element->dif_cx);
				//	$sheet->setCellValue('AA'.$row_number, $element->dif_px);
				//	$sheet->setCellValue('AB'.$row_number, $element->dif_mbu);
				//	$sheet->setCellValue('AC'.$row_number, $element->ratio_ppto);
				//	$sheet->setCellValue('AD'.$row_number, $element->ratio_tm);
				//	$sheet->setCellValue('AE'.$row_number, $element->ratio);
				//	$sheet->setCellValue('AF'.$row_number, $element->last_cost_glp);
				//	$sheet->setCellValue('AG'.$row_number, $element->last_aprov);
				//	$sheet->setCellValue('AH'.$row_number, $element->last_cost_glp_final);
				//	$sheet->setCellValue('AI'.$row_number, $element->cost_glp);
				//	$sheet->setCellValue('AJ'.$row_number, $element->aprov);
				//	$sheet->setCellValue('AK'.$row_number, $element->cost_glp_final);
					
                    $sheet->getStyle('D'.$row_number)->getNumberFormat()->setFormatCode('0,0');
					$sheet->getStyle('E'.$row_number)->getNumberFormat()->setFormatCode('0,0');
					$sheet->getStyle('F'.$row_number)->getNumberFormat()->setFormatCode('0,0');
					$sheet->getStyle('G'.$row_number)->getNumberFormat()->setFormatCode('0.00'.'%');
					$sheet->getStyle('H'.$row_number)->getNumberFormat()->setFormatCode('0,0');
					$sheet->getStyle('I'.$row_number)->getNumberFormat()->setFormatCode('0.00'.'%');
					$sheet->getStyle('J'.$row_number)->getNumberFormat()->setFormatCode('0,0');
					$sheet->getStyle('K'.$row_number)->getNumberFormat()->setFormatCode('0,0');
					$sheet->getStyle('L'.$row_number)->getNumberFormat()->setFormatCode('0,0');
					$sheet->getStyle('M'.$row_number)->getNumberFormat()->setFormatCode('0.00'.'%');
					$sheet->getStyle('N'.$row_number)->getNumberFormat()->setFormatCode('0,0');
					$sheet->getStyle('O'.$row_number)->getNumberFormat()->setFormatCode('0.00'.'%');
					$sheet->getStyle('P'.$row_number)->getNumberFormat()->setFormatCode('0,0');
					$sheet->getStyle('Q'.$row_number)->getNumberFormat()->setFormatCode('0,0');
					$sheet->getStyle('R'.$row_number)->getNumberFormat()->setFormatCode('0,0');
					$sheet->getStyle('S'.$row_number)->getNumberFormat()->setFormatCode('0.00'.'%');
					$sheet->getStyle('T'.$row_number)->getNumberFormat()->setFormatCode('0.00');
					$sheet->getStyle('U'.$row_number)->getNumberFormat()->setFormatCode('0.00');
					$sheet->getStyle('V'.$row_number)->getNumberFormat()->setFormatCode('0.00');
					$sheet->getStyle('W'.$row_number)->getNumberFormat()->setFormatCode('0.00');
					$sheet->getStyle('X'.$row_number)->getNumberFormat()->setFormatCode('0.00');
					$sheet->getStyle('Y'.$row_number)->getNumberFormat()->setFormatCode('0.00');

					$row_number++;
				}
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
			//	$sheet->getColumnDimension('Z')->setAutoSize(true);
			//	$sheet->getColumnDimension('AA')->setAutoSize(true);
			//	$sheet->getColumnDimension('AB')->setAutoSize(true);
			//	$sheet->getColumnDimension('AC')->setAutoSize(true);
			//	$sheet->getColumnDimension('AD')->setAutoSize(true);
			//	$sheet->getColumnDimension('AE')->setAutoSize(true);
			//	$sheet->getColumnDimension('AF')->setAutoSize(true);
			//	$sheet->getColumnDimension('AG')->setAutoSize(true);
			//	$sheet->getColumnDimension('AH')->setAutoSize(true);
			//	$sheet->getColumnDimension('AI')->setAutoSize(true);
			//	$sheet->getColumnDimension('AJ')->setAutoSize(true);
				//	$sheet->getColumnDimension('AK')->setAutoSize(true);
			$writer = new Xls($spreadsheet);

			return $writer->save('php://output');
		} 
		
		
		
		
		
		
		else {
			foreach ($result as $report) {


				$report->unit = $report ['unit'];
				$report->type = $report ['type'];
				
				//
				$year_prev = ($price_mes == '01') ? $price_year - 1 : $price_year;
				$mes_prev = ($price_mes == '01') ? '12' : $price_mes - 1;
				
				//pdd envasado
				$movements_e = WarehouseMovement::join('warehouse_movement_details', 'warehouse_movement_details.warehouse_movement_id', '=', 'warehouse_movements.id')
						->where(function ($query) use ($price_year , $price_mes) {
							$query->where('warehouse_movements.price_año', $price_year)
								->where('warehouse_movements.price_mes', $price_mes)
								->where('warehouse_movements.movement_type_id', 30)
								->where('warehouse_movements.origin', 26);
						})
						->orWhere(function ($query) use ($year_prev , $mes_prev) {
							$query->where('warehouse_movements.price_año', $year_prev)
								->where('warehouse_movements.price_mes', $mes_prev)
								->where('warehouse_movement_details.digit2','<>', null)
								->where('warehouse_movement_details.digit2','<>', 0)
								->where('warehouse_movements.movement_type_id', 30)
								->where('warehouse_movements.origin', 26);
						})
						->where('warehouse_movements.movement_class_id', 2)
						->select('warehouse_movement_details.id',
						 'warehouse_movements.warehouse_type_id',
						 'warehouse_movements.movement_type_id',
						 'warehouse_movements.referral_serie_number',
						 'warehouse_movements.referral_voucher_number',
						 'warehouse_movements.origin',
						 'warehouse_movement_details.digit1',
						 'warehouse_movement_details.digit2',
						 'warehouse_movements.soles_1',
						 'warehouse_movements.soles_2',
						 'warehouse_movements.price_mes'
						 )
						 ->get();
			
						$total_e = 0 ;
						$quantity_e = 0 ;
			
						foreach($movements_e as $e){
			
							if( $e->digit2 != null &&  $e->digit2 != 0){
			
								if($e->price_mes == $price_mes ){
			
									$quantity_e += $e->digit1;
									$total_e += $e->soles_1 ;
			
								}else{
									$quantity_e += $e->digit2;
									$total_e += $e->soles_2 ;
								}
								
							}else{
								$quantity_e += $e->digit1;
								$total_e +=  $e->soles_1 ;
							}
						}  
			
			   $report->soles_glp_e +=$total_e;
			   $report->kgs_glp_e +=$quantity_e;
			
			   //pdd granel
				$movements_g = WarehouseMovement::join('warehouse_movement_details', 'warehouse_movement_details.warehouse_movement_id', '=', 'warehouse_movements.id')
				->where(function ($query) use ($price_year , $price_mes) {
					$query->where('warehouse_movements.price_año', $price_year)
						->where('warehouse_movements.price_mes', $price_mes)
						->where('warehouse_movements.movement_type_id', 30)
						->where('warehouse_movements.origin', 27);
				})
				->orWhere(function ($query) use ($year_prev , $mes_prev) {
					$query->where('warehouse_movements.price_año', $year_prev)
						->where('warehouse_movements.price_mes', $mes_prev)
						->where('warehouse_movement_details.digit2','<>', null)
						->where('warehouse_movement_details.digit2','<>', 0)
						->where('warehouse_movements.movement_type_id', 30)
						->where('warehouse_movements.origin', 27);
				})
				->where('warehouse_movements.movement_class_id', 2)
				->select('warehouse_movement_details.id',
				 'warehouse_movements.warehouse_type_id',
				 'warehouse_movements.movement_type_id',
				 'warehouse_movements.referral_serie_number',
				 'warehouse_movements.referral_voucher_number',
				 'warehouse_movements.origin',
				 'warehouse_movement_details.digit1',
				 'warehouse_movement_details.digit2',
				 'warehouse_movements.soles_1',
				 'warehouse_movements.soles_2',
				 'warehouse_movements.price_mes'
				 )
				 ->get();
			
				$total_g = 0 ;
				$quantity_g = 0 ;
			
				foreach($movements_g as $e){
			
					if( $e->digit2 != null &&  $e->digit2 != 0){
			
						if($e->price_mes == $price_mes ){
			
							$quantity_g += $e->digit1;
							$total_g += $e->soles_1 ;
			
						}else{
							$quantity_g += $e->digit2;
							$total_g += $e->soles_2 ;
						}
						
					}else{
						$quantity_g += $e->digit1;
						$total_g +=  $e->soles_1 ;
					}
				}   
				//
			
			  $report->soles_glp_g +=$total_g;
			  $report->kgs_glp_g +=$quantity_g;
			
			
			  if ( $report->unit != 2)
			
			   $report->cost_glp = $report['kgs_glp_e'] > 0 ? ($report['soles_glp_e']/($report['kgs_glp_e']/1000)/100): null;
			   else
				{
				$report->cost_glp = $report['kgs_glp_g'] > 0 ? ($report['soles_glp_g']/($report['kgs_glp_g']/1000)/100): null;	
				}
			
			//------------------------------------------------------------------------MARGEN DE GRANEL ---------------------------------------------------------
			
			$cost_e =  $report['kgs_glp_e'] > 0 ? ($report['soles_glp_e']/($report['kgs_glp_e']/1000)/100): null;
			
			
			$cost_g = $report['kgs_glp_g'] > 0 ? ($report['soles_glp_g']/($report['kgs_glp_g']/1000)/100): null;	
			
			


			$tm_env = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
						->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
						->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
						->where('sales.sale_date', '>=', $mes_date)
						->where('sales.sale_date', '<=', $initial_date)
						->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
						->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
						->whereIn('clients.route_id',[19])
						->whereIn('clients.channel_id',[5,6,12])
						->whereIn('clients.manager_id',[7,8])
						->select('kg')
						->sum('kg');
	
			$soles_env = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
						 ->where('sales.sale_date', '>=', $mes_date)
						 ->where('sales.sale_date', '<=', $initial_date)
						 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
						 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
						 ->whereIn('clients.route_id',[19])
						 ->whereIn('clients.channel_id',[5,6,12])
						 ->whereIn('clients.manager_id',[7,8])
						 ->select('total')
						 ->sum('total');


 			$tm_gra = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
						 ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
						 ->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
						 ->where('sales.sale_date', '>=', $mes_date)
						 ->where('sales.sale_date', '<=', $initial_date)
						 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
						 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
						 ->whereIn('clients.route_id',[26])
						 ->whereIn('clients.channel_id',[5,6,12])
						 ->whereIn('clients.manager_id',[7,8,9])
						 ->select('kg')
						 ->sum('kg');
	 
 			$soles_gra = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
						  ->where('sales.sale_date', '>=', $mes_date)
						  ->where('sales.sale_date', '<=', $initial_date)
						  ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
						  ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
						  ->whereIn('clients.route_id',[26])
						  ->whereIn('clients.channel_id',[5,6,12])
						  ->whereIn('clients.manager_id',[7,8,9])
						  ->select('total')
						  ->sum('total');






						  if ( $report->year == '2022' || $report->year == '2023'){
				
							// <<--------------------------------------------------------------CALCULO DE TM EXCEL-------------------------------------------------------------------------------->>
			
							if ( $report->option == 1) {
								$tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
								->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
								->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
								->where('sales.sale_date', '>=', $mes_date)
								->where('sales.sale_date', '<=', $initial_date)
								->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
								->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
								->where('clients.business_unit_id',  $report->business_id)
								->select('kg')
								->sum('kg');
			
								$soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
								->where('sales.sale_date', '>=', $mes_date)
								->where('sales.sale_date', '<=', $initial_date)
								->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,32])
								->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
								->where('clients.business_unit_id',  $report->business_id)
								->select('total')
								->sum('total');
			
								$mb_final=0;
								$mb_final_gra=0;
							
			
								}
					
								elseif ( $report->option == 2 ) {
								$tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
								->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
								->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
								->where('sales.sale_date', '>=', $mes_date)
								->where('sales.sale_date', '<=', $initial_date)
								->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
								->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
								->where('clients.manager_id', $report->manager_id)
								->select('kg')
								->sum('kg');
			
								$soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
								->where('sales.sale_date', '>=', $mes_date)
								->where('sales.sale_date', '<=', $initial_date)
								->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
								->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
								->where('clients.manager_id', $report->manager_id)
								->select('total')
								->sum('total');
			
								$mb_final=0;
								$mb_final_gra=0;
			
							
							} 
						
						 elseif ( $report->option == 3) {
							if ($report->orden >=1 && $report->orden <=5)
							{
							 $tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
							 ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
							 ->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
							 ->where('sales.sale_date', '>=', $mes_date)
							 ->where('sales.sale_date', '<=', $initial_date)
							 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
							 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
							 ->where('clients.sector_id',$report->sector_id)
							 ->select('kg')
							 ->sum('kg');
			
							 $soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
							 ->where('sales.sale_date', '>=', $mes_date)
							 ->where('sales.sale_date', '<=', $initial_date)
							 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
							 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
							 ->where('clients.sector_id',$report->sector_id)
							 ->select('total')
							 ->sum('total');
			
							 $mb_final=0;
							 $mb_final_gra=0;
			
							} elseif ( $report->orden >=6 && $report->orden <=9)
							{ 	$tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
								->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
								->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
								->where('sales.sale_date', '>=', $mes_date)
								->where('sales.sale_date', '<=', $initial_date)
								->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
								->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
								->where('clients.channel_id',$report->channel_id)
								->select('kg')
								->sum('kg');
			
								$soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
								->where('sales.sale_date', '>=', $mes_date)
								->where('sales.sale_date', '<=', $initial_date)
								->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
								->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
								->where('clients.channel_id',$report->channel_id)
								->select('total')
								->sum('total');
			
								$mb_final=0;
								$mb_final_gra=0;
							}
						 }
									
						 elseif ( $report->option == 4) {
							if ($report->orden >=1 && $report->orden <=24)
							{
							 $tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
							 ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
							 ->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
							 ->where('sales.sale_date', '>=', $mes_date)
							 ->where('sales.sale_date', '<=', $initial_date)
							 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
							 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
							 ->where('clients.route_id',$report->route_id)
							 ->select('kg')
							 ->sum('kg');
							 $soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
							 ->where('sales.sale_date', '>=', $mes_date)
							  ->where('sales.sale_date', '<=', $initial_date)
							  ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
							 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
							 ->where('clients.route_id',$report->route_id)
							 ->select('total')
							 ->sum('total');
							 $mb_final=0;
							 $mb_final_gra=0;
			
							} 
			
							elseif ($report->orden >=25 && $report->orden <=32) 
							{
							 $tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
							 ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
							 ->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
							 ->where('sales.sale_date', '>=', $mes_date)
							 ->where('sales.sale_date', '<=', $initial_date)
							 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
							 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
							 ->where('clients.route_id',$report->route_id)
							 ->where('clients.zone_id',$report->zone_id)
							 ->select('kg')
							 ->sum('kg');
							 $soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
							  ->where('sales.sale_date', '>=', $mes_date)
							 ->where('sales.sale_date', '<=', $initial_date)
							 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
							 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
							 ->where('clients.route_id',$report->route_id)
							 ->where('clients.zone_id',$report->zone_id)
							 ->select('total')
							 ->sum('total');
							  $mb_final=0;
							  $mb_final_gra=0;
							} 
			
							elseif ($report->orden >=33 && $report->orden <=34) 
							{
							 $tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
							 ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
							 ->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
							 ->where('sales.sale_date', '>=', $mes_date)
							 ->where('sales.sale_date', '<=', $initial_date)
							 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
							 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
							 ->where('clients.route_id',$report->route_id)				
							 ->where('clients.manager_id',$report->manager_id)
							 ->where('clients.zone_id',9)
							 ->select('kg')
							 ->sum('kg');
			
							 $soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
							 ->where('sales.sale_date', '>=', $mes_date)
							 ->where('sales.sale_date', '<=', $initial_date)
							 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
							 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
							 ->where('clients.route_id',$report->route_id)
							 ->where('clients.zone_id',9)
							 ->where('clients.manager_id',$report->manager_id)
							 ->select('total')
							 ->sum('total');
							 $mb_final=0;
							 $mb_final_gra=0;
			
							}
			
							elseif ($report->orden >=35 && $report->orden <=38) 
							{
							 $tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
							 ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
							 ->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
							 ->where('sales.sale_date', '>=', $mes_date)
							 ->where('sales.sale_date', '<=', $initial_date)
							 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
							 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
							 ->where('clients.route_id',$report->route_id)
							 ->select('kg')
							 ->sum('kg');
			
							 $soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
							 ->where('sales.sale_date', '>=', $mes_date)
							 ->where('sales.sale_date', '<=', $initial_date)
							 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
							 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
							 ->where('clients.route_id',$report->route_id)
							 ->select('total')
							 ->sum('total');
							 $mb_final=0;
							 $mb_final_gra=0;
			
			
							} 
			
						 }
						
						 
						 elseif ( $report->option == 5) {
							 $tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
							 ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
							 ->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
							 ->where('sales.sale_date', '>=', $mes_date)
							 ->where('sales.sale_date', '<=', $initial_date)
							 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
							 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
							 ->where('clients.channel_id',$report->channel_id)
							 ->where('clients.manager_id',$report->manager_id)
							 ->select('kg')
							 ->sum('kg');
			
							 $soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
							 ->where('sales.sale_date', '>=', $mes_date)
							 ->where('sales.sale_date', '<=', $initial_date)
							 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
							 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
							  ->where('clients.channel_id',$report->channel_id)
							 ->where('clients.manager_id',$report->manager_id)
							 ->select('total')
							 ->sum('total');
			
							 $mb_final=0;
							 $mb_final_gra=0;
			
			
							}
							
							elseif ( $report->option == 6) {
								$tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
								->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
								->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
								->where('sales.sale_date', '>=', $mes_date)
								->where('sales.sale_date', '<=', $initial_date)
								->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
								->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
								->where('clients.channel_id',$report->channel_id)
								->where('clients.manager_id',$report->manager_id)
								->select('kg')
								->sum('kg');
			
								$soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
								 ->where('sales.sale_date', '>=', $mes_date)
								 ->where('sales.sale_date', '<=', $initial_date)
								 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
								 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
								 ->where('clients.channel_id',$report->channel_id)
								 ->where('clients.manager_id',$report->manager_id)
								 ->select('total')
								 ->sum('total');
			
								 $mb_final=0;
								 $mb_final_gra=0;
			   
							   }			 				
						
			
							   elseif ( $report->option == 7) {
								$tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
								->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
								->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
								->where('sales.sale_date', '>=', $mes_date)
								->where('sales.sale_date', '<=', $initial_date)
								->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
								->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
								->where('clients.channel_id',$report->channel_id)
								->where('clients.manager_id',$report->manager_id)
								->select('kg')
								->sum('kg');
			
								$soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
								 ->where('sales.sale_date', '>=', $mes_date)
								 ->where('sales.sale_date', '<=', $initial_date)
								 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
								 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
								 ->where('clients.channel_id',$report->channel_id)
								 ->where('clients.manager_id',$report->manager_id)
								 ->select('total')
								 ->sum('total');
			
								 $mb_final=0;
								 $mb_final_gra=0;
			   
			
							   }			 				
						
							   elseif ( $report->option == 8) {
								$tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
								->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
								->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
								->where('sales.sale_date', '>=', $mes_date)
								->where('sales.sale_date', '<=', $initial_date)
								->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
								->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
								->where('clients.sector_id',$report->sector_id)
								->where('clients.manager_id',$report->manager_id)
								->select('kg')
								->sum('kg');
			
								$soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
								 ->where('sales.sale_date', '>=', $mes_date)
								 ->where('sales.sale_date', '<=', $initial_date)
								 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
								 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
								 ->where('clients.sector_id',$report->sector_id)
								 ->where('clients.manager_id',$report->manager_id)
								 ->select('total')
								 ->sum('total');
			
								 $mb_final=0;
								 $mb_final_gra=0;
			   
							   }			 				
						
							   elseif ( $report->option == 9) {
								$tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
								->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
								->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
								->where('sales.sale_date', '>=', $mes_date)
								->where('sales.sale_date', '<=', $initial_date)
								->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
								->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
								->where('clients.sector_id',$report->sector_id)
								->where('clients.manager_id',$report->manager_id)
								->select('kg')
								->sum('kg');
			
								$soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
								  ->where('sales.sale_date', '>=', $mes_date)
								 ->where('sales.sale_date', '<=', $initial_date)
								 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
								 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
								 ->where('clients.sector_id',$report->sector_id)
								 ->where('clients.manager_id',$report->manager_id)
								 ->select('total')
								 ->sum('total');
								 $mb_final=0;
								 $mb_final_gra=0;
			   
							   }			 				
						
							   elseif ( $report->option == 10) {
								$tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
								->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
								->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
								->where('sales.sale_date', '>=', $mes_date)
								->where('sales.sale_date', '<=', $initial_date)
								->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
								->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
								->where('clients.sector_id',$report->sector_id)
								->where('clients.manager_id',$report->manager_id)
								->select('kg')
								->sum('kg');
			
								$soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
								  ->where('sales.sale_date', '>=', $mes_date)
								 ->where('sales.sale_date', '<=', $initial_date)
								 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
								 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
								 ->where('clients.sector_id',$report->sector_id)
								 ->where('clients.manager_id',$report->manager_id)
								 ->select('total')
								 ->sum('total');
			
								 $mb_final=0;
								 $mb_final_gra=0;
			   
							   }			 				
									   
							 }
			
							if ($report->year == '2024'){
			
								
			
									if ( $report->option == 1) {
										$tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
										->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
										->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
										->where('sales.sale_date', '>=', $mes_date)
										->where('sales.sale_date', '<=', $initial_date)
										->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
										->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
										->where('clients.business_unit_id',  $report->business_id)
										->select('kg')
										->sum('kg');
					
										$soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
										->where('sales.sale_date', '>=', $mes_date)
										->where('sales.sale_date', '<=', $initial_date)
										->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,32])
										->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
										->where('clients.business_unit_id',  $report->business_id)
										->select('total')
										->sum('total');
										$mb_final=0;
										$mb_final_gra=0;
					  					
										}
							
										elseif ( $report->option == 2 ) {
										$tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
										->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
										->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
										->where('sales.sale_date', '>=', $mes_date)
										->where('sales.sale_date', '<=', $initial_date)
										->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
										->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
										->where('clients.manager_id', $report->manager_id)
										->select('kg')
										->sum('kg');
					
										$soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
										->where('sales.sale_date', '>=', $mes_date)
										->where('sales.sale_date', '<=', $initial_date)
										->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
										->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
										->where('clients.manager_id', $report->manager_id)
										->select('total')
										->sum('total');
			
										$mb_final=0;
										$mb_final_gra=0;
					  
										} 
								
								 elseif ( $report->option == 3) {
									if ($report->orden >=1 && $report->orden <=5)
									{
									 $tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
									 ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
									 ->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
									 ->where('sales.sale_date', '>=', $mes_date)
									 ->where('sales.sale_date', '<=', $initial_date)
									 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
									 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
									 ->where('clients.sector_id',$report->sector_id)
									 ->select('kg')
									 ->sum('kg');
					
									 $soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
									 ->where('sales.sale_date', '>=', $mes_date)
									 ->where('sales.sale_date', '<=', $initial_date)
									 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
									 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
									 ->where('clients.sector_id',$report->sector_id)
									 ->select('total')
									 ->sum('total');
			
									 $mb_final=0;
									 $mb_final_gra=0;
				   
					
									} 
									elseif ( $report->orden >=6 && $report->orden <=9)
									{ 	$tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
										->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
										->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
										->where('sales.sale_date', '>=', $mes_date)
										->where('sales.sale_date', '<=', $initial_date)
										->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
										->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
										->where('clients.channel_id',$report->channel_id)
										->select('kg')
										->sum('kg');
					
										$soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
										->where('sales.sale_date', '>=', $mes_date)
										->where('sales.sale_date', '<=', $initial_date)
										->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
										->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
										->where('clients.channel_id',$report->channel_id)
										->select('total')
										->sum('total');
			
										$mb_final=0;
										$mb_final_gra=0;
					  
									}
								 }
											
								 elseif ( $report->option == 4) {
									if ($report->orden >=1 && $report->orden <=24)
									{
									 $tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
									 ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
									 ->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
									 ->where('sales.sale_date', '>=', $mes_date)
									 ->where('sales.sale_date', '<=', $initial_date)
									 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
									 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
									 ->where('clients.route_id',$report->route_id)
									 ->select('kg')
									 ->sum('kg');
									 $soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
									 ->where('sales.sale_date', '>=', $mes_date)
									  ->where('sales.sale_date', '<=', $initial_date)
									  ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
									 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
									 ->where('clients.route_id',$report->route_id)
									 ->select('total')
									 ->sum('total');
			
									 $mb_final=0;
									 $mb_final_gra=0;
				   
									} 
					
									elseif ($report->orden >=25 && $report->orden <=32) 
									{
									 $tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
									 ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
									 ->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
									 ->where('sales.sale_date', '>=', $mes_date)
									 ->where('sales.sale_date', '<=', $initial_date)
									 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
									 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
									 ->where('clients.route_id',$report->route_id)
									 ->where('clients.zone_id',$report->zone_id)
									 ->select('kg')
									 ->sum('kg');
									 $soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
									  ->where('sales.sale_date', '>=', $mes_date)
									 ->where('sales.sale_date', '<=', $initial_date)
									 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
									 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
									 ->where('clients.route_id',$report->route_id)
									 ->where('clients.zone_id',$report->zone_id)
									 ->select('total')
									 ->sum('total');
			
									 $mb_final=0;
									 $mb_final_gra=0;
				   
									} 
					
									elseif ($report->orden >=33 && $report->orden <=34) 
									{
									 $tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
									 ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
									 ->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
									 ->where('sales.sale_date', '>=', $mes_date)
									 ->where('sales.sale_date', '<=', $initial_date)
									 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
									 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
									 ->where('clients.route_id',$report->route_id)				
									 ->where('clients.manager_id',$report->manager_id)
									 ->where('clients.zone_id',9)
									 ->select('kg')
									 ->sum('kg');
					
									 $soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
									 ->where('sales.sale_date', '>=', $mes_date)
									 ->where('sales.sale_date', '<=', $initial_date)
									 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
									 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
									 ->where('clients.route_id',$report->route_id)
									 ->where('clients.zone_id',9)
									 ->where('clients.manager_id',$report->manager_id)
									 ->select('total')
									 ->sum('total');
			
									 $mb_final=0;
									 $mb_final_gra=0;
				   
									}
					
									elseif ($report->orden >=35 && $report->orden <=38) 
									{
									 $tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
									 ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
									 ->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
									 ->where('sales.sale_date', '>=', $mes_date)
									 ->where('sales.sale_date', '<=', $initial_date)
									 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
									 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
									 ->where('clients.route_id',$report->route_id)
									 ->select('kg')
									 ->sum('kg');
					
									 $soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
									 ->where('sales.sale_date', '>=', $mes_date)
									 ->where('sales.sale_date', '<=', $initial_date)
									 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
									 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
									 ->where('clients.route_id',$report->route_id)
									 ->select('total')
									 ->sum('total');
			
									 $mb_final=0;
									 $mb_final_gra=0;
				   
					
									} 
			
								}
															
							 
							 elseif ( $report->option == 5) {
								 $tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
								 ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
								 ->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
								 ->where('sales.sale_date', '>=', $mes_date)
								 ->where('sales.sale_date', '<=', $initial_date)
								 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
								 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
								 ->where('clients.channel_id',$report->channel_id)
								 ->where('clients.manager_id',$report->manager_id)
								 ->select('kg')
								 ->sum('kg');
				
								 $soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
								 ->where('sales.sale_date', '>=', $mes_date)
								 ->where('sales.sale_date', '<=', $initial_date)
								 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
								 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
								  ->where('clients.channel_id',$report->channel_id)
								 ->where('clients.manager_id',$report->manager_id)
								 ->select('total')
								 ->sum('total');
			
								 $mb_final=0;
								 $mb_final_gra=0;
			   
				
								}
								
								elseif ( $report->option == 6) {
									$tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
									->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
									->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
									->where('sales.sale_date', '>=', $mes_date)
									->where('sales.sale_date', '<=', $initial_date)
									->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
									->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
									->where('clients.channel_id',$report->channel_id)
									->where('clients.manager_id',$report->manager_id)
									->select('kg')
									->sum('kg');
				
									$soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
									 ->where('sales.sale_date', '>=', $mes_date)
									 ->where('sales.sale_date', '<=', $initial_date)
									 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
									 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
									 ->where('clients.channel_id',$report->channel_id)
									 ->where('clients.manager_id',$report->manager_id)
									 ->select('total')
									 ->sum('total');
			
									 $mb_final=0;
									 $mb_final_gra=0;
				   
								}			 				
							
				
								elseif ( $report->option == 7) {
									$tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
									->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
									->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
									->where('sales.sale_date', '>=', $mes_date)
									->where('sales.sale_date', '<=', $initial_date)
									->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
									->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
									->where('clients.channel_id',$report->channel_id)
									->where('clients.manager_id',$report->manager_id)
									->select('kg')
									->sum('kg');
				
									$soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
									 ->where('sales.sale_date', '>=', $mes_date)
									 ->where('sales.sale_date', '<=', $initial_date)
									 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
									 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
									 ->where('clients.channel_id',$report->channel_id)
									 ->where('clients.manager_id',$report->manager_id)
									 ->select('total')
									 ->sum('total');
			
									 $mb_final=0;
									 $mb_final_gra=0;
				   
				
								}			 				
							
								elseif ( $report->option == 8) {
									$tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
									->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
									->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
									->where('sales.sale_date', '>=', $mes_date)
									->where('sales.sale_date', '<=', $initial_date)
									->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
									->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
									->where('clients.sector_id',$report->sector_id)
									->where('clients.manager_id',$report->manager_id)
									->select('kg')
									->sum('kg');
				
									$soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
									 ->where('sales.sale_date', '>=', $mes_date)
									 ->where('sales.sale_date', '<=', $initial_date)
									 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
									 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
									 ->where('clients.sector_id',$report->sector_id)
									 ->where('clients.manager_id',$report->manager_id)
									 ->select('total')
									 ->sum('total');
			
									 $mb_final=0;
									 $mb_final_gra=0;
				   
								}			 				
							
								elseif ( $report->option == 9) {
									$tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
									->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
									->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
									->where('sales.sale_date', '>=', $mes_date)
									->where('sales.sale_date', '<=', $initial_date)
									->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
									->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
									->where('clients.sector_id',$report->sector_id)
									->where('clients.manager_id',$report->manager_id)
									->select('kg')
									->sum('kg');
				
									$soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
									  ->where('sales.sale_date', '>=', $mes_date)
									 ->where('sales.sale_date', '<=', $initial_date)
									 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
									 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
									 ->where('clients.sector_id',$report->sector_id)
									 ->where('clients.manager_id',$report->manager_id)
									 ->select('total')
									 ->sum('total');
			
									 $mb_final=0;
									 $mb_final_gra=0;
				   
								}			 				
							
								elseif ( $report->option == 10) {
									$tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
									->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
									->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
									->where('sales.sale_date', '>=', $mes_date)
									->where('sales.sale_date', '<=', $initial_date)
									->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
									->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
									->where('clients.sector_id',$report->sector_id)
									->where('clients.manager_id',$report->manager_id)
									->select('kg')
									->sum('kg');
				
									$soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
									  ->where('sales.sale_date', '>=', $mes_date)
									 ->where('sales.sale_date', '<=', $initial_date)
									 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
									 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
									 ->where('clients.sector_id',$report->sector_id)
									 ->where('clients.manager_id',$report->manager_id)
									 ->select('total')
									 ->sum('total');
			
									 $mb_final=0;
									 $mb_final_gra=0;
				   
								}
			
							
			
							elseif($report->month == '02' || $report->month == '03'){
			
								if ( $report->option == 1) {
			
									if ($report->orden ==1 || $report->orden ==2)
									{
									$tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
									->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
									->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
									->where('sales.sale_date', '>=', $mes_date)
									->where('sales.sale_date', '<=', $initial_date)
									->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
									->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
									->where('clients.business_unit_id',  $report->business_id)
									->where('clients.company_id',  $report->company)
									->select('kg')
									->sum('kg');
				
									$soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
									->where('sales.sale_date', '>=', $mes_date)
									->where('sales.sale_date', '<=', $initial_date)
									->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,32])
									->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
									->where('clients.business_unit_id',  $report->business_id)
									->where('clients.company_id',  $report->company)
									->select('total')
									->sum('total');
			
									$mb_final=0;
									$mb_final_gra=0;
				  
			
									}elseif ( $report->orden ==3 ){
			
			
									$tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
									->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
									->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
									->where('sales.sale_date', '>=', $mes_date)
									->where('sales.sale_date', '<=', $initial_date)
									->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
									->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
									->where('clients.business_unit_id',  $report->business_id)
									->select('kg')
									->sum('kg');
				
									$soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
									->where('sales.sale_date', '>=', $mes_date)
									->where('sales.sale_date', '<=', $initial_date)
									->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,32])
									->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
									->where('clients.business_unit_id',  $report->business_id)
									->select('total')
									->sum('total');
			
									$mb_final=0;
									$mb_final_gra=0;
				  
									}
									}
			
									
						
									elseif ( $report->option == 2 ) {
			
									if ($report->orden >=1 && $report->orden <=8 ||$report->orden == 11 )
									{
									$tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
									->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
									->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
									->where('sales.sale_date', '>=', $mes_date)
									->where('sales.sale_date', '<=', $initial_date)
									->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
									->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
									->where('clients.manager_id', $report->manager_id)
									->select('kg')
									->sum('kg');
				
									$soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
									->where('sales.sale_date', '>=', $mes_date)
									->where('sales.sale_date', '<=', $initial_date)
									->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
									->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
									->where('clients.manager_id', $report->manager_id)
									->select('total')
									->sum('total');
			
			
									$mb_final=0;
									$mb_final_gra=0;
				  
									}
			
									elseif ( $report->orden >=9 && $report->orden <=10 ){
			
									$tm_env = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
									->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
									->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
									->where('sales.sale_date', '>=', $mes_date)
									->where('sales.sale_date', '<=', $initial_date)
									->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
									->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
									->where('clients.route_id', 19)
									->where('clients.manager_id', $report->manager_id)
									->select('kg')
									->sum('kg');
				
									$soles_env = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
									->where('sales.sale_date', '>=', $mes_date)
									->where('sales.sale_date', '<=', $initial_date)
									->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
									->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
									->where('clients.manager_id', $report->manager_id)
									->where('clients.route_id', 19)
									->select('total')
									->sum('total');	
									
									
									
									$tm_gra = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
									->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
									->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
									->where('sales.sale_date', '>=', $mes_date)
									->where('sales.sale_date', '<=', $initial_date)
									->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
									->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
									->where('clients.route_id', 26)
									->where('clients.manager_id', $report->manager_id)
									->select('kg')
									->sum('kg');
				
									$soles_gra = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
									->where('sales.sale_date', '>=', $mes_date)
									->where('sales.sale_date', '<=', $initial_date)
									->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
									->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
									->where('clients.manager_id', $report->manager_id)
									->where('clients.route_id', 26)
									->select('total')
									->sum('total');	
			
									if ($tm_env !=0)
									$mb_e = ((($soles_env/(($tm_env/1000)*100))-$cost_e) *(($tm_env/1000)*100));
									else {
										$mb_e =1;
									}
			
									if ($tm_gra !=0)
			
									$mb_g = ((($soles_gra/(($tm_gra/1000)*100))-$cost_g) *(($tm_gra/1000)*100));
									else {
										$mb_g =1;
									}
			
			
									$mb_final = $mb_e+$mb_g;
			
			
									$mb_final_gra=0;
				  
			
									$tm_fin = $tm_env+$tm_gra;
									$soles  = $soles_env+$soles_gra;		
								
								
								}
			
							} 
			
				
							
							 elseif ( $report->option == 3) {
			
								if ($report->orden >=1 && $report->orden <=5 )
								{
								 $tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
								 ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
								 ->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
								 ->where('sales.sale_date', '>=', $mes_date)
								 ->where('sales.sale_date', '<=', $initial_date)
								 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
								 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
								 ->where('clients.sector_id',$report->sector_id)
								 ->select('kg')
								 ->sum('kg');
				
								 $soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
								 ->where('sales.sale_date', '>=', $mes_date)
								 ->where('sales.sale_date', '<=', $initial_date)
								 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
								 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
								 ->where('clients.sector_id',$report->sector_id)
								 ->select('total')
								 ->sum('total');
			
								 $mb_final=0;
								 $mb_final_gra=0;
			   
				
								} elseif ( $report->orden >=6 && $report->orden <=7)
			
								{ 	
									$tm_env = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
									->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
									->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
									->where('sales.sale_date', '>=', $mes_date)
									->where('sales.sale_date', '<=', $initial_date)
									->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
									->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
									->where('clients.route_id', 19)
									->where('clients.channel_id', $report->channel_id)
									->select('kg')
									->sum('kg');
				
									$soles_env = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
									->where('sales.sale_date', '>=', $mes_date)
									->where('sales.sale_date', '<=', $initial_date)
									->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
									->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
									->where('clients.channel_id', $report->channel_id)
									->where('clients.route_id', 19)
									->select('total')
									->sum('total');	
									
									
									
									$tm_gra = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
									->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
									->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
									->where('sales.sale_date', '>=', $mes_date)
									->where('sales.sale_date', '<=', $initial_date)
									->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
									->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
									->where('clients.route_id', 26)
									->where('clients.channel_id', $report->channel_id)
									->select('kg')
									->sum('kg');
				
									$soles_gra = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
									->where('sales.sale_date', '>=', $mes_date)
									->where('sales.sale_date', '<=', $initial_date)
									->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
									->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
									->where('clients.channel_id', $report->channel_id)
									->where('clients.route_id', 26)
									->select('total')
									->sum('total');	
			
			
									if ($tm_env !=0)
									$mb_e = ((($soles_env/(($tm_env/1000)*100))-$cost_e) *(($tm_env/1000)*100));
									else {
										$mb_e =1;
									}
			
									if ($tm_gra !=0)
			
									$mb_g = ((($soles_gra/(($tm_gra/1000)*100))-$cost_g) *(($tm_gra/1000)*100));
									else {
										$mb_g =1;
									}
			
			
									$mb_final = $mb_e+$mb_g;
			
									$mb_final_gra=0;
				  
									$tm_fin = $tm_env+$tm_gra;
									$soles  = $soles_env+$soles_gra;	
								}
			
								elseif ( $report->orden >=8 && $report->orden <=9){
			
									$tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
									->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
									->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
									->where('sales.sale_date', '>=', $mes_date)
									->where('sales.sale_date', '<=', $initial_date)
									->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
									->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
									->where('clients.channel_id',$report->channel_id)
									->select('kg')
									->sum('kg');
				   
									$soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
									->where('sales.sale_date', '>=', $mes_date)
									->where('sales.sale_date', '<=', $initial_date)
									->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
									->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
									->where('clients.channel_id',$report->channel_id)
									->select('total')
									->sum('total');
			
									$mb_final=0;
									$mb_final_gra=0;
				  
								}				
			
							 }
			
			
								
							 
							 elseif ( $report->option == 4) {
								if ($report->orden >=1 && $report->orden <=26)
								{
								 $tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
								 ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
								 ->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
								 ->where('sales.sale_date', '>=', $mes_date)
								 ->where('sales.sale_date', '<=', $initial_date)
								 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
								 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
								 ->where('clients.route_id',$report->route_id)
								 ->select('kg')
								 ->sum('kg');
								 $soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
								 ->where('sales.sale_date', '>=', $mes_date)
								  ->where('sales.sale_date', '<=', $initial_date)
								  ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
								 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
								 ->where('clients.route_id',$report->route_id)
								 ->select('total')
								 ->sum('total');
			
								 $mb_final=0;
								 $mb_final_gra=0;
			   
								} 
				
								elseif ($report->orden >=27 && $report->orden <=32) 
								{
								 $tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
								 ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
								 ->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
								 ->where('sales.sale_date', '>=', $mes_date)
								 ->where('sales.sale_date', '<=', $initial_date)
								 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
								 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
								 ->where('clients.route_id',$report->route_id)
								 ->where('clients.zone_id',$report->zone_id)
								 ->select('kg')
								 ->sum('kg');
								 $soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
								  ->where('sales.sale_date', '>=', $mes_date)
								 ->where('sales.sale_date', '<=', $initial_date)
								 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
								 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
								 ->where('clients.route_id',$report->route_id)
								 ->where('clients.zone_id',$report->zone_id)
								 ->select('total')
								 ->sum('total');
			
								 $mb_final=0;
								 $mb_final_gra=0;
			   
								} 
				
								elseif ($report->orden >=33 && $report->orden <=34) 
								{
								 $tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
								 ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
								 ->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
								 ->where('sales.sale_date', '>=', $mes_date)
								 ->where('sales.sale_date', '<=', $initial_date)
								 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
								 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
								 ->where('clients.route_id',$report->route_id)				
								 ->where('clients.manager_id',$report->manager_id)
								 ->where('clients.zone_id',11)
								 ->select('kg')
								 ->sum('kg');
				
								 $soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
								 ->where('sales.sale_date', '>=', $mes_date)
								 ->where('sales.sale_date', '<=', $initial_date)
								 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
								 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
								 ->where('clients.route_id',$report->route_id)
								 ->where('clients.zone_id',11)
								 ->where('clients.manager_id',$report->manager_id)
								 ->select('total')
								 ->sum('total');
			
								 $mb_final=0;
								 $mb_final_gra=0;
			   
								}
			
			
								elseif ($report->orden >=35 && $report->orden <=36) 
								{
								 $tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
								 ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
								 ->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
								 ->where('sales.sale_date', '>=', $mes_date)
								 ->where('sales.sale_date', '<=', $initial_date)
								 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
								 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
								 ->where('clients.route_id',$report->route_id)				
								 ->where('clients.manager_id',$report->manager_id)
								 ->where('clients.zone_id',9)
								 ->select('kg')
								 ->sum('kg');
				
								 $soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
								 ->where('sales.sale_date', '>=', $mes_date)
								 ->where('sales.sale_date', '<=', $initial_date)
								 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
								 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
								 ->where('clients.route_id',$report->route_id)
								 ->where('clients.zone_id',9)
								 ->where('clients.manager_id',$report->manager_id)
								 ->select('total')
								 ->sum('total');
			
								 $mb_final=0;
								 $mb_final_gra=0;
			   
								}
				
								elseif ($report->orden >=37 && $report->orden <=40) 
								{
								 $tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
								 ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
								 ->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
								 ->where('sales.sale_date', '>=', $mes_date)
								 ->where('sales.sale_date', '<=', $initial_date)
								 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
								 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
								 ->where('clients.route_id',$report->route_id)
								 ->select('kg')
								 ->sum('kg');
				
								 $soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
								 ->where('sales.sale_date', '>=', $mes_date)
								 ->where('sales.sale_date', '<=', $initial_date)
								 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
								 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072, 14258])
								 ->where('clients.route_id',$report->route_id)
								 ->select('total')
								 ->sum('total');
			
								 $mb_final=0;
								 $mb_final_gra=0;
			   
				
								} 
			
							}
														
						 
						 elseif ( $report->option == 5) {
			
				
									$tm_env = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
									->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
									->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
									->where('sales.sale_date', '>=', $mes_date)
									->where('sales.sale_date', '<=', $initial_date)
									->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
									->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
									->where('clients.route_id', 19)
									->where('clients.channel_id', $report->channel_id)
									->where('clients.manager_id',$report->manager_id)
									->select('kg')
									->sum('kg');
				
									$soles_env = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
									->where('sales.sale_date', '>=', $mes_date)
									->where('sales.sale_date', '<=', $initial_date)
									->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
									->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
									->where('clients.channel_id', $report->channel_id)
									->where('clients.manager_id',$report->manager_id)
									->where('clients.route_id', 19)
									->select('total')
									->sum('total');	
									
									
									
									$tm_gra = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
									->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
									->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
									->where('sales.sale_date', '>=', $mes_date)
									->where('sales.sale_date', '<=', $initial_date)
									->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
									->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
									->where('clients.route_id', 26)
									->where('clients.channel_id', $report->channel_id)
									->where('clients.manager_id',$report->manager_id)
									->select('kg')
									->sum('kg');
				
									$soles_gra = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
									->where('sales.sale_date', '>=', $mes_date)
									->where('sales.sale_date', '<=', $initial_date)
									->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
									->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
									->where('clients.channel_id', $report->channel_id)
									->where('clients.manager_id',$report->manager_id)
									->where('clients.route_id', 26)
									->select('total')
									->sum('total');	
			
			
									if ($tm_env !=0)
									$mb_e = ((($soles_env/(($tm_env/1000)*100))-$cost_e) *(($tm_env/1000)*100));
									else {
										$mb_e =1;
									}
			
									if ($tm_gra !=0)
			
									$mb_g = ((($soles_gra/(($tm_gra/1000)*100))-$cost_g) *(($tm_gra/1000)*100));
									else {
										$mb_g =1;
									}
			
			
									$mb_final_gra = $mb_e+$mb_g;
									$mb_final=0;
					  
			
									$tm_fin = $tm_env+$tm_gra;
									$soles  = $soles_env+$soles_gra;	
			
			
			
							}
							
							elseif ( $report->option == 6) {
								$tm_env = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
									->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
									->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
									->where('sales.sale_date', '>=', $mes_date)
									->where('sales.sale_date', '<=', $initial_date)
									->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
									->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
									->where('clients.route_id', 19)
									->where('clients.channel_id', $report->channel_id)
									->where('clients.manager_id',$report->manager_id)
									->select('kg')
									->sum('kg');
				
									$soles_env = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
									->where('sales.sale_date', '>=', $mes_date)
									->where('sales.sale_date', '<=', $initial_date)
									->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
									->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
									->where('clients.channel_id', $report->channel_id)
									->where('clients.manager_id',$report->manager_id)
									->where('clients.route_id', 19)
									->select('total')
									->sum('total');	
									
									
									
									$tm_gra = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
									->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
									->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
									->where('sales.sale_date', '>=', $mes_date)
									->where('sales.sale_date', '<=', $initial_date)
									->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
									->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
									->where('clients.route_id', 26)
									->where('clients.channel_id', $report->channel_id)
									->where('clients.manager_id',$report->manager_id)
									->select('kg')
									->sum('kg');
				
									$soles_gra = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
									->where('sales.sale_date', '>=', $mes_date)
									->where('sales.sale_date', '<=', $initial_date)
									->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
									->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
									->where('clients.channel_id', $report->channel_id)
									->where('clients.manager_id',$report->manager_id)
									->where('clients.route_id', 26)
									->select('total')
									->sum('total');	
			
			
									if ($tm_env !=0)
									$mb_e = ((($soles_env/(($tm_env/1000)*100))-$cost_e) *(($tm_env/1000)*100));
									else {
										$mb_e =1;
									}
			
									if ($tm_gra !=0)
			
									$mb_g = ((($soles_gra/(($tm_gra/1000)*100))-$cost_g) *(($tm_gra/1000)*100));
									else {
										$mb_g =1;
									}
			
			
									$mb_final_gra = $mb_e+$mb_g;
			
									$mb_final=0;
			
				  
			
									$tm_fin = $tm_env+$tm_gra;
									$soles  = $soles_env+$soles_gra;	
			
							   }			 				
						
			
							   elseif ( $report->option == 7) {
								$tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
								->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
								->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
								->where('sales.sale_date', '>=', $mes_date)
								->where('sales.sale_date', '<=', $initial_date)
								->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
								->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
								->where('clients.channel_id',$report->channel_id)
								->where('clients.manager_id',$report->manager_id)
								->select('kg')
								->sum('kg');
			
								$soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
								 ->where('sales.sale_date', '>=', $mes_date)
								 ->where('sales.sale_date', '<=', $initial_date)
								 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
								 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
								 ->where('clients.channel_id',$report->channel_id)
								 ->where('clients.manager_id',$report->manager_id)
								 ->select('total')
								 ->sum('total');
			
								 $mb_final=0;
								 $mb_final_gra=0;
			   
			
							   }			 				
						
							   elseif ( $report->option == 8) {
								$tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
								->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
								->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
								->where('sales.sale_date', '>=', $mes_date)
								->where('sales.sale_date', '<=', $initial_date)
								->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
								->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
								->where('clients.sector_id',$report->sector_id)
								->where('clients.manager_id',$report->manager_id)
								->select('kg')
								->sum('kg');
			
								$soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
								 ->where('sales.sale_date', '>=', $mes_date)
								 ->where('sales.sale_date', '<=', $initial_date)
								 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
								 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
								 ->where('clients.sector_id',$report->sector_id)
								 ->where('clients.manager_id',$report->manager_id)
								 ->select('total')
								 ->sum('total');
			
								 $mb_final=0;
								 $mb_final_gra=0;
			   
							   }			 				
						
							   elseif ( $report->option == 9) {
								$tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
								->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
								->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
								->where('sales.sale_date', '>=', $mes_date)
								->where('sales.sale_date', '<=', $initial_date)
								->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
								->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
								->where('clients.sector_id',$report->sector_id)
								->where('clients.manager_id',$report->manager_id)
								->select('kg')
								->sum('kg');
			
								$soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
								  ->where('sales.sale_date', '>=', $mes_date)
								 ->where('sales.sale_date', '<=', $initial_date)
								 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
								 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
								 ->where('clients.sector_id',$report->sector_id)
								 ->where('clients.manager_id',$report->manager_id)
								 ->select('total')
								 ->sum('total');
			
								 $mb_final=0;
								 $mb_final_gra=0;
			   
							   }			 				
						
							   elseif ( $report->option == 10) {
								$tm_fin = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
								->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
								->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
								->where('sales.sale_date', '>=', $mes_date)
								->where('sales.sale_date', '<=', $initial_date)
								->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
								->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
								->where('clients.sector_id',$report->sector_id)
								->where('clients.manager_id',$report->manager_id)
								->select('kg')
								->sum('kg');
			
								$soles = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
								  ->where('sales.sale_date', '>=', $mes_date)
								 ->where('sales.sale_date', '<=', $initial_date)
								 ->whereNotIn('sales.warehouse_document_type_id', [2,3,8,9,10,11,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32])
								 ->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14072,14258])
								 ->where('clients.sector_id',$report->sector_id)
								 ->where('clients.manager_id',$report->manager_id)
								 ->select('total')
								 ->sum('total');
			
								 $mb_final=0;
								 $mb_final_gra=0;
			   
							   }
			
			
			
			
							}
			
			
						}

	
	
   

if ($tm_env !=0)
						 
$mb_e = ((($soles_env/(($tm_env/1000)*100))-$cost_e) *(($tm_env/1000)*100));

else{
	$mb_e =1;
}


$mb_g = ((($soles_gra/(($tm_gra/1000)*100))-$cost_g) *(($tm_gra/1000)*100));


$mb_granel = $mb_e+$mb_g;
						


//PERMITE HALLAR EL MB DEL MES CONSULTADO
   $report->costo = $report->cost_glp;

 
   $report->mb_granel =$mb_granel;
   $report->mb_final = $mb_final;
   $report->mb_final_gra = $mb_final_gra;	


//------------------------------------------------------------------
//	$report->aprov = $report ['aprov'];
//	$report->cost_glp_final = $report ['cost_glp'] +$report ['aprov'];

	$report->name = $report ['name'];
	$report->last_tm = $report ['last_tm'];
	$report->tm = $tm_fin/1000;

	if ( $report->tm != 0)
	$report->proy_tm = (($report ['tm']/ $day)* $report ['dias_mes']);
	else
	{
	$report->proy_tm=0;
	}

	if ( $report->last_tm != 0)
	$report->last_percentaje_tm = ($report  ['tm']/ $report['last_tm'] );
	else
	{
	$report->last_percentaje_tm=0;
	}

	$report->tm_ppto = $report ['tm_ppto'];
	if ( $report->tm_ppto != 0)
	$report->percentaje_tm =($report ['proy_tm']/ $report ['tm_ppto']);
	else
	{
	$report->percentaje_tm=0;
	}
	
	$report->last_mb = $report ['last_mb'];
	
	$report->last_total =  $report ['last_total'];
	$report->total =  $soles;

	if ( $report->total != 0)
	$report->proy_soles =($report ['total']/ $day)* $report ['dias_mes'];
	else
	{
	$report->proy_soles=0;
	}

	if ( $report->last_total != 0)
	$report->percentaje_soles = ($report ['proy_soles']/$report ['last_total']);
	else
	{
		$report->percentaje_soles=0;
	}

   

	if($report->type ==1)
	{
		if ($report->tm !=0)
		$report->mb = ((($report->total/($report->tm *100))-$report->costo) *($report->tm*100));
		else {
		$report->mb =0;
		}

		}

	elseif ($report->type ==2){
		$report->mb =$report->mb_granel;
	}
	elseif ($report->type ==3){
		$report->mb =$report->mb_final;
	}
	elseif ($report->type ==4){
		$report->mb =$report->mb_granel_final;
	}


	if ($report->mb !=0)
	$report->proy_mb =($report ['mb']/ $day)* $report ['dias_mes'];
	else {
		$report->proy_mb =0;
	}



	if ( $report->last_mb != 0)
	$report->last_percentaje_mb =($report  ['mb']/ $report ['last_mb']);
	else
	{
	$report->last_percentaje_mb=0;
	}
	$report->mb_ppto = $report ['mb_ppto'];
	if ( $report->mb_ppto != 0)
	$report->percentaje_mb =  ($report ['proy_mb']/ $report ['mb_ppto']);
	else
	{
	$report->percentaje_mb=0;
	}
	

	if ( $report->last_tm != 0)
	$report->px_ma =  ($report ['last_total']/ $report ['last_tm'])/100;
	else
	{
		$report->px_ma=0;
	}
	if ( $report->last_tm != 0)
	$report->mbu_ma = ($report ['last_mb']/ $report ['last_tm'])/100;
	else
	{
		$report->mbu_ma=0;
	}

	$report->costo_ma =$report ['px_ma'] - $report ['mbu_ma'];

	if ( $report->tm != 0)
	$report->px= $report ['total']/$report ['tm']/100;
	else
	{
		$report->px=0;
	}

	if ( $report->tm != 0)
	$report->mbu = ($report ['mb']/ $report ['tm'])/100;
	else
	{
		$report->mbu=0;
	}
	
	
	$report->dif_cx =  $report['costo'] - $report['costo_ma'];
	$report->dif_px = $report ['px']- $report ['px_ma'];
	$report->dif_mbu = $report ['mbu']- $report ['mbu_ma'];

	if ( $report->tm_ppto != 0)
	$report->ratio_ppto = ($report ['mb_ppto']/ $report ['tm_ppto']);
	else
	{
		$report->ratio_ppto=0;
	}
	$report->ratio_tm = ($report ['tm']/ $day);
	

	$report->last_cost_glp =  $report->soles_glp_e;
	$report->last_aprov = $report->kgs_glp_e;

	if ( $report->last_tm != 0)
	$report->last_cost_glp_final = $report ['last_mb']/$report ['last_tm'];
	else
	{
		$report->last_cost_glp_final=0;
	}

					
					/* Totals*/
					$totals_last_tm += $report->last_tm;
					$totals_tm += $report->tm;
		            $totals_proy_tm += $report->proy_tm;
		            $totals_tm_ppto += $report->tm_ppto;
		            $totals_last_mb += $report->last_mb;
		            $totals_mb += $report->mb;
		            $totals_proy_mb += $report->proy_mb;
		            $totals_mb_ppto += $report->mb_ppto;
		            $totals_last_total += $report->last_total;
		            $totals_total += $report->total;
		            $totals_proy_soles += $report->proy_soles;

                    if ( $totals_tm!= 0)
					$report->ratio = ($report ['tm']/ $totals_tm);
					else
	                {
					$report->ratio=0;
	                }
					

					$response[] = $report;
				}
			

			
			$totals = new stdClass();
			$totals->client_channel_name = 'TOTAL';
			$totals->name = '';
			$totals->last_tm = '';
			$totals->tm = '';
			$totals->proy_tm = '';
			$totals->last_percentaje_tm = '';
			$totals->tm_ppto = '';
			$totals->percentaje_tm = '';
			$totals->last_mb = '';
			$totals->last_percentaje_mb = '';
			$totals->mb_ppto = '';
			$totals->last_total = '';
			$totals->total = '';
			$totals->proy_soles = '';
			$totals->percentaje_soles = '';
			$totals->costo_ma = '';
			$totals->px_ma = '';
			$totals->mbu_ma = '';
			$totals->costo = '';
			$totals->px = '';
			$totals->mbu= '';
			$totals->dif_cx = '';
			$totals->dif_px = '';
			$totals->dif_mbu = '';
			$totals->ratio_ppto = '';
			$totals->ratio_tm = '';
			$totals->ratio = '';
			$totals->last_cost_glp = '';
			$totals->last_aprov = '';
			$totals->last_cost_glp_final = '';
			$totals->cost_glp = '';
			$totals->aprov = '';
			$totals->cost_glp_final = '';

			$response[] = $totals;

			

			return response()->json($response);
		}
	}
}






