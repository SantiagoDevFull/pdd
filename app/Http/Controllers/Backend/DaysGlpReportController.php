<?php

namespace App\Http\Controllers\Backend;

use App\WarehouseMovement;
use App\WarehouseMovementDetail;
use App\SaleDetail;
use App\Sale;
use App\Article;
use App\Client;
use App\Exports\CostGlpReportExport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\CarbonImmutable;
use Carbon\Carbon;
use strtotime;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use stdClass;


class 	DaysGlpReportController extends Controller
{
	public function index()
	{

		$current_date = date(DATE_ATOM, mktime(0, 0, 0));
		return view('backend.days_glp_report')->with(compact('current_date'));
	}

	public function validateForm()
	{
		$messages = [

			'initial_date.required'	=> 'Debe seleccionar una Fecha final.',
			'origin.required'		=> 'Debe seleccionar un Producto.',
		];

		$rules = [

			'initial_date'	=> 'required',
			'origin'		=> 'required',
		];

		request()->validate($rules, $messages);
		return request()->all();
	}



	public function list()
	{

		$export = request('export');

		$final_date = CarbonImmutable::createFromDate(request('model.initial_date'))->endOfDay()->format('m');
		$year_date = CarbonImmutable::createFromDate(request('model.initial_date'))->endOfDay()->format('Y');
		$date = CarbonImmutable::createFromDate(request('model.initial_date'))->endOfDay()->format('Y-m-d');

		$year_prev = ($final_date == '01') ? $year_date - 1 : $year_date;
		$final_prev = ($final_date == '01') ? '12' : $final_date - 1;

		$origin=request('model.origin');
		
		$elements = WarehouseMovement::join('warehouse_movement_details', 'warehouse_movement_details.warehouse_movement_id', '=', 'warehouse_movements.id')
			->join('warehouse_types', 'warehouse_movements.warehouse_type_id', '=', 'warehouse_types.id')
			->join('companies','warehouse_movements.company_id','companies.id')
			->join('articles', 'warehouse_movement_details.article_code', '=', 'articles.id')

			->where(function ($query) use ($year_date , $final_date, $origin) {
				$query->where('warehouse_movements.price_año', $year_date)
					->where('warehouse_movements.price_mes', $final_date)
					->where('warehouse_movements.movement_type_id', 30)
					->where('warehouse_movements.origin', $origin);
			})

			->orWhere(function ($query) use ($year_prev , $final_prev, $origin) {
				$query->where('warehouse_movements.price_año', $year_prev)
					->where('warehouse_movements.price_mes', $final_prev)
					->where('warehouse_movement_details.digit2','<>', null)
					->where('warehouse_movement_details.digit2','<>', 0)
					->where('warehouse_movements.movement_type_id', 30)
					->where('warehouse_movements.origin', $origin);
			})
			//->where('warehouse_movements.created_at','>=',$date)
			->whereIn('warehouse_movements.warehouse_type_id', [8, 9, 10, 11])
			->where('warehouse_movements.movement_class_id', 2)
			->select(
				'warehouse_movements.movement_type_id as movement_type_id',
				'warehouse_movements.id',
				//'warehouse_movements.price_mes',
				DB::Raw('DATE_FORMAT(warehouse_movements.created_at, "%m") as price_mes'),
				'warehouse_movements.origin as origin',
				'warehouse_movements.id as company_short_name',
				DB::Raw('DATE_FORMAT(warehouse_movements.created_at, "%Y-%m-%d") as issue_date'),
				DB::Raw('CONCAT(referral_serie_number, "-", referral_voucher_number) as factura'),
				'warehouse_types.name as warehouse_name',
				'articles.name as article_name',
				'warehouse_movements.referral_guide_number as referral_guide',
				'warehouse_movements.license_plate as license_plate',
				'warehouse_movements.license_plate_2 as license_plate_2',
				'warehouse_movement_details.converted_amount as quantity',
				//'warehouse_movements.cost_glp as price_kg',
				//'warehouse_movements.total as soles',
				'warehouse_types.short_name as warehouse_short_name',
				'warehouse_movements.scop_number',
				'warehouse_movements.account_name as account_name',
				DB::Raw('(warehouse_movement_details.converted_amount / 1000) as tm'),
				'warehouse_movements.referral_guide_series as pedido_fact',
				'warehouse_movements.mezcla as mezcla',
				
				'warehouse_movements.referral_serie_number as referral_serie_number',
				'warehouse_movements.referral_voucher_number as referral_voucher_number', 
				'warehouse_movement_details.converted_amount as cantidad',
				'warehouse_types.convertion as convertion',
				'warehouse_movement_details.digit1 as digit1',
				'warehouse_movement_details.digit2 as digit2'
			)
			->orderBy('issue_date')
			->get();

			$movement_details = collect([]);

			$elements->map(function ($item, $index) use ($movement_details, $year_date , $final_date, $year_prev , $final_prev, $export) {
				$item->warehouse_movement_details;
				$item->warehouse_movement_details->map(function ($detail, $detailIndex) use ($item, $movement_details, $year_date , $final_date, $year_prev , $final_prev, $export) {

					$cost_glp = WarehouseMovement::where('referral_voucher_number', $item['referral_voucher_number'])
					->where('referral_serie_number',$item['referral_serie_number'])
					->where('movement_class_id', 1)
					->select('referral_guide_number')
					->sum('cost_glp');
					//$cost_glp=67774.94892/22720;

					$pedido = WarehouseMovement::where('referral_voucher_number', $item['referral_voucher_number'])
					->where('referral_serie_number',$item['referral_serie_number'])
					->where('movement_class_id', 1)
					->select('referral_guide_number')
					->sum('referral_guide_number');

					$total = WarehouseMovement::where('referral_voucher_number', $item['referral_voucher_number'])
					->where('referral_serie_number',$item['referral_serie_number'])
					->where('movement_class_id', 1)
					->select('total')
					->sum('total');

					$cantidad = WarehouseMovement::where('referral_voucher_number', $item['referral_voucher_number'])
					->where('referral_serie_number',$item['referral_serie_number'])
					->leftjoin('warehouse_movement_details', 'warehouse_movements.id', '=', 'warehouse_movement_details.warehouse_movement_id')
					->where('movement_class_id', 1)
					->select('converted_amount')
					->sum('converted_amount');

					$detail->movement_type_id=$item->movement_type_id;
					$detail->company_short_name = $item->company_short_name ;
					$detail->issue_date = $item->issue_date ;
					$detail->factura = $item->factura ;
					$detail->warehouse_name = $item->warehouse_name ;
					//$detail->article_name = $item->article_name ;
					$detail->article_name = $item['origin'] == '27' ? 'G' : 'E' ;
					$detail->referral_guide = $item->referral_guide ;
					$detail->license_plate = $item->license_plate ;
					$detail->license_plate_2 = $item->license_plate_2 ;
					$detail->quantity = $item->quantity ;
					$detail->price_kg = ( $cantidad > 0 ) ? $cost_glp : 0 ;
					$detail->warehouse_short_name = $item->warehouse_short_name ;
					$detail->scop_number = $item->scop_number ;
					$detail->account_name = $item->account_name ;
					
					$detail->pedido_fact = $item->pedido_fact ;
					$detail->referral_serie_number = $item->referral_serie_number ;
					$detail->referral_voucher_number = $item->referral_voucher_number ;
					//$detail->cantidad = $item->cantidad ;
					$detail->price_dol = ( $cantidad > 0 ) ? $total / $cantidad * 1000 : 0 ;
					$detail->convertion =$item->convertion;
					$detail->destiny = 'PLANTA';
					$detail->mezcla = $item->mezcla;
					$detail->pedido_m = $pedido ;
					$detail->aprov = $detail->convertion * $detail->quantity ;

					$detail->sub_total = $item['cantidad'] * $detail->price_kg / 1.18 ;
					$detail->igv = $detail->sub_total*0.18;
					$detail->pond = ($detail->converted_amount * $detail->price_kg) / $item['tm'];
					$detail->partition='SI';
					$detail->price_mes = $this-> ObtenerMesName($item->price_mes) ;

					if($item['digit2'] != null && $item['digit2'] != 0){
						
						if($item->price_mes == $final_date ){
							$detail->quantity = $item['digit1'] ;
						}else{
							$detail->quantity = $item['digit2'] ;
						}

						$detail->soles = $detail->quantity * $detail->price_kg ;
						$detail->tm = $detail->quantity / 1000 ;
						$movement_details->push($detail);
					}else{
						$detail->partition='NO';
						$detail->soles = $detail->quantity * $detail->price_kg ;
						$detail->pond = ($detail->converted_amount * $detail->price_kg) / $item['tm'];
						$detail->tm = $detail->quantity / 1000 ;
						$movement_details->push($detail);
					}

				});
			});

        if ($export) {
			$data = new CostGlpReportExport($movement_details);
			$file = Excel::download($data, 'reporte-costo-glp-nuevo-' . time() . '.xls');

			return $file;
		} 
		
	}

	public function ObtenerMesName($mes){
		$name='';
		switch($mes){
			case '01':$name='Enero';break;
			case '02':$name='Febrero';break;
			case '03':$name='Marzo';break;
			case '04':$name='Abril';break;
			case '05':$name='Mayo';break;
			case '06':$name='Junio';break;
			case '07':$name='Julio';break;
			case '08':$name='Agosto';break;
			case '09':$name='Setiembre';break;
			case '10':$name='Octubre';break;
			case '11':$name='Noviembre';break;
			case '12':$name='Diciembre';break;
		}
		return $name;
	}

}
