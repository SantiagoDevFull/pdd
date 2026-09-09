<?php

namespace App\Http\Controllers\Backend;

use App\Client;
use App\Company;
use App\Article;
use App\Cicle;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SaleDetail;
use App\Sale;
use App\BusinessUnit;
use App\Liquidation;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use stdClass;


class CreditTotalReportController extends Controller
{
	public function index()
	{
		$companies = Company::select('id', 'name')->get();
		$current_date = CarbonImmutable::now()->toAtomString();
		return view('backend.credit_total_report')->with(compact('companies', 'current_date'));
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
		$company_id = request('company_id');
		$q = request('q');

		$clients = Client::select('id', 'business_name as text')
			->when($company_id, function ($query, $company_id) {
				return $query->where('company_id', $company_id);
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
		$company_id = request('model.company_id');
		$client_id = request('model.client_id');

		$elements = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
			->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->leftjoin('cicles', 'sales.cicle_id', '=', 'cicles.id')
			->leftjoin('articles', 'sale_details.article_id', '=', 'articles.id')
			->leftjoin('warehouse_document_types', 'sales.warehouse_document_type_id', '=', 'warehouse_document_types.id')
			->leftjoin('warehouse_movements', 'sales.warehouse_movement_id', '=', 'warehouse_movements.id')
			->where('sales.sale_date', '>=', $initial_date)
			->where('sales.sale_date', '<=', $final_date)
			->whereIn('sales.warehouse_document_type_id', [4, 5, 6, 7, 13])
			->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14258, 14072])
			->select(
				'sales.sale_date',
				DB::Raw('DATE_FORMAT(sales.created_at, "%Y-%m-%d") as liquidation_date'),
				'cicles.name as mes_name',
				'cicles.inicio as fecha_inicio',
				'cicles.final as fecha_final'
			)

			->when($company_id, function ($query, $company_id) {
				return $query->where('clients.company_id', $company_id);
			})
			->when($client_id, function ($query, $client_id) {
				return $query->where('sales.client_id', $client_id);
			})


			->groupBy('mes_name')
			->orderBy('sales.sale_date')
			->get();


		$response = [];
		$totals_total_per = 0;
		$totals_total = 0;
		$totals_sum_total = 0;
		$totals_deposit = 0;
		$totals_efective = 0;
		$totals_pre_balance = 0;
		$totals_contado = 0;
		$totals_dif = 0;
		$totals_credit_pers = 0;
		$totals_balance = 0;
		$totals_cobranza_mes = 0;
		$totals_cobranza_rep = 0;
		//	$totals_cancelacion = 0;


		foreach ($elements as $saledetail) {

		
			$saledetail->liquidation_date = $saledetail['liquidation_date'];
			$saledetail->sale_date = $saledetail['sale_date'];
			$saledetail->mes_name = $saledetail['mes_name'];
			$saledetail->fecha_inicio = $saledetail['fecha_inicio'];
			$saledetail->fecha_final = $saledetail['fecha_final'];


			$sum_total = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
				->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				->whereIn('sales.warehouse_document_type_id', [13, 5, 6, 7, 4,6])
				->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14258, 14072])
				->where('sales.sale_date', '>=',   $saledetail['fecha_inicio'])
				->where('sales.sale_date', '<=', $saledetail['fecha_final'])
				->select('sale_details.kg')
				->sum('sale_details.kg');

			$sum_soles = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
				->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				->whereIn('sales.warehouse_document_type_id', [13, 5, 7, 4,6])
				->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14258, 14072])
				->where('sales.sale_date', '>=',   $saledetail['fecha_inicio'])
				->where('sales.sale_date', '<=', $saledetail['fecha_final'])
				->select('sale_details.total')
				->sum('sale_details.total');

            $sum_soles_per = SaleDetail::leftjoin('sales', 'sale_details.sale_id', '=', 'sales.id')
				->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				->whereIn('sales.warehouse_document_type_id', [13, 5, 7, 4,6])
				->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14258, 14072])
				->whereIn('sales.company_id', [1,2])
				->where('sales.sale_date', '>=',   $saledetail['fecha_inicio'])
				->where('sales.sale_date', '<=', $saledetail['fecha_final'])
				->select('sale_details.total_perception')
				->sum('sale_details.total_perception');    


			$sum_pre_balance = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				->whereIn('sales.warehouse_document_type_id', [13, 5, 7, 4,6])
				->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14258, 14072])
				->where('sales.sale_date', '>=',   $saledetail['fecha_inicio'])
				->where('sales.sale_date', '<=', $saledetail['fecha_final'])
				->select('sales.pre_balance')
				->sum('sales.pre_balance');

            $sum_efective = Liquidation::leftjoin('sales', 'liquidations.sale_id', '=', 'sales.id')
			    ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				->whereIn('sales.warehouse_document_type_id', [13, 5, 7, 4,6])
				->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14258, 14072])
				->where('sales.sale_date', '>=',   $saledetail['fecha_inicio'])
				->where('sales.sale_date', '<=', $saledetail['fecha_final'])
				->where('liquidations.payment_method_id', '=', 1)
                ->where('liquidations.collection', '=', 0)
				->select('liquidations.amount')
				->sum('liquidations.amount');

            $sum_deposits = Liquidation::leftjoin('sales', 'liquidations.sale_id', '=', 'sales.id')
			    ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				->whereIn('sales.warehouse_document_type_id', [13, 5, 7, 4,6])
				->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14258, 14072])
				->where('sales.sale_date', '>=',   $saledetail['fecha_inicio'])
				->where('sales.sale_date', '<=', $saledetail['fecha_final'])
				->where('liquidations.payment_method_id', '=', 2)
                ->where('liquidations.collection', '=', 0)
				->select('liquidations.amount')
				->sum('liquidations.amount');    


			$sum_credit_pers = Liquidation::leftjoin('sales', 'liquidations.sale_id', '=', 'sales.id')
			    ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				->whereIn('sales.warehouse_document_type_id', [13, 5, 7, 4,6])
				->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14258, 14072])
				->where('sales.sale_date', '>=',   $saledetail['fecha_inicio'])
				->where('sales.sale_date', '<=', $saledetail['fecha_final'])
				->where('liquidations.payment_method_id', '=', 10)
				->select('liquidations.amount')
				->sum('liquidations.amount');


            $sum_cobranza_mes = Liquidation::leftjoin('sales', 'liquidations.sale_id', '=', 'sales.id')
			    ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				->whereIn('sales.warehouse_document_type_id', [13, 5, 7, 4,6])
				->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14258, 14072])
				->where('sales.sale_date', '>=',   $saledetail['fecha_inicio'])
				->where('sales.sale_date', '<=', $saledetail['fecha_final'])
                ->where(DB::Raw('DATE_FORMAT(liquidations.created_at, "%Y-%m-%d") '), '>=', $saledetail['fecha_inicio'])
                ->where(DB::Raw('DATE_FORMAT(liquidations.created_at, "%Y-%m-%d") '), '<=',  $saledetail['fecha_final'])
				->whereIn('liquidations.payment_method_id', [1,2])
                ->whereIn('liquidations.collection', [1,2])
				->select('liquidations.amount')
				->sum('liquidations.amount');    

            $sum_cobranza_rep = Liquidation::leftjoin('sales', 'liquidations.sale_id', '=', 'sales.id')
			    ->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				->whereIn('sales.warehouse_document_type_id', [13, 5, 7, 4,6])
				->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14258, 14072])
				->where('sales.sale_date', '<', $saledetail['fecha_inicio'])
				->where(DB::Raw('DATE_FORMAT(liquidations.created_at, "%Y-%m-%d") '), '>=', $saledetail['fecha_inicio'])
                ->where(DB::Raw('DATE_FORMAT(liquidations.created_at, "%Y-%m-%d") '), '<=',  $saledetail['fecha_final'])
				->whereIn('liquidations.payment_method_id', [1,2])
                ->whereIn('liquidations.collection', [1,2])
				->select('liquidations.amount')
				->sum('liquidations.amount'); 

            $sum_balance = Sale::leftjoin('clients', 'sales.client_id', '=', 'clients.id')
				->whereIn('sales.warehouse_document_type_id', [13, 5, 7, 4,6])
				->whereNotIn('sales.client_id', [1031, 427, 13326, 13775, 14258, 14072])
				->where('sales.sale_date', '>=', $saledetail['fecha_inicio'])
				->where('sales.sale_date', '<=', $saledetail['fecha_final'])
				->select('sales.balance')
				->sum('sales.balance');    


			$saledetail->sum_total = ($sum_total / 1000);
			$saledetail->deposit = $sum_deposits;
			$saledetail->efective = $sum_efective;
			$saledetail->contado = $sum_efective+$sum_deposits;
			$saledetail->pre_balance = $sum_pre_balance;
			$saledetail->total = $sum_soles;
			$saledetail->total_per = $sum_soles_per;
			$saledetail->cobranza_mes= $sum_cobranza_mes;
			$saledetail->credit_pers = $sum_credit_pers;
			$saledetail->cobranza_rep= $sum_cobranza_rep;



			$totals_total += $saledetail['total'];
			$totals_sum_total += $saledetail['sum_total'];
			$totals_deposit += $saledetail['deposit'];
			$totals_efective += $saledetail['efective'];
			$totals_contado += $saledetail['contado'];
			$totals_credit_pers += $saledetail['credit_pers'];
			$totals_pre_balance += $saledetail['pre_balance'];
			$totals_total_per += $saledetail['total_per'];
			$totals_cobranza_mes += $saledetail['cobranza_mes'];
			$totals_cobranza_rep += $saledetail['cobranza_rep'];

			$response[] = $saledetail;
		}


		$totals = new stdClass();
		$totals->mes_name = 'TOTAL';
		$totals->sum_total = $totals_sum_total;
		$totals->total = $totals_total;
		$totals->total_per = $totals_total_per;
		$totals->credit_pers = $totals_credit_pers;
		$totals->contado = $totals_contado;
		$totals->deposit = $totals_deposit;
		$totals->efective = $totals_efective;
		$totals->pre_balance = $totals_pre_balance;
		$totals->cobranza_mes = $totals_cobranza_mes;
		$totals->cobranza_rep = $totals_cobranza_rep;

		$response[] = $totals;

		//if ($export) {
			$spreadsheet = new Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();
			$sheet->mergeCells('A1:L1');
			$sheet->setCellValue('A1', 'REPORTE CREDITOS CONSOLIDADO ' . CarbonImmutable::now()->format('d/m/Y H:m:s'));
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
			$sheet->setCellValue('B3', 'Mes');
			$sheet->setCellValue('C3', 'TM');
			$sheet->setCellValue('D3', 'VENTA S./');
			$sheet->setCellValue('E3', 'VENTA PERCEP. S./');
			$sheet->setCellValue('F3', 'CONTADO');
			$sheet->setCellValue('G3', 'CREDITO');
			$sheet->setCellValue('H3', 'DESCUENTO A PERS.');
			$sheet->setCellValue('I3', 'COBRANZA DEL MES');
			$sheet->setCellValue('J3', 'COBRANZA DE MESES ANT.');
			$sheet->setCellValue('K3', 'TOTAL COBRANZA');
			$sheet->setCellValue('L3', 'COBRANZA BANCOS');
			$sheet->setCellValue('M3', 'COBRANZA CAJA');
			$sheet->setCellValue('N3', 'CUADRE');
			$sheet->setCellValue('O3', 'CREDITO ACUM. PEND');

			$sheet->getStyle('A3:O3')->applyFromArray([
				'font' => [
					'bold' => true,
				],
			]);

			$row_number = 4;
			foreach ($response as $index => $element) {
				$index++;
				$sheet->setCellValueExplicit('A' . $row_number, $index, DataType::TYPE_NUMERIC);				
				$sheet->setCellValue('B' . $row_number, $element->mes_name);
				$sheet->setCellValue('C' . $row_number, $element->sum_total);
				$sheet->setCellValue('D' . $row_number, $element->total);
				$sheet->setCellValue('E' . $row_number, $element->total_per);
				$sheet->setCellValue('F' . $row_number, $element->contado);
				$sheet->setCellValue('G' . $row_number, $element->pre_balance);
				$sheet->setCellValue('H' . $row_number, $element->credit_pers);
				$sheet->setCellValue('I' . $row_number, $element->cobranza_mes);
				$sheet->setCellValue('J' . $row_number, $element->cobranza_rep);
			//	$sheet->setCellValue('J' . $row_number, $element->pre_balance);
			//	$sheet->setCellValue('K' . $row_number, $element->dif);


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
			$sheet->getColumnDimension('L')->setAutoSize(true);


			$writer = new Xls($spreadsheet);
			return $writer->save('php://output');
		//} else {
		//	return response()->json($response);
		//}
	}
}
