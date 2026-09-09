<?php

namespace App\Http\Controllers\Backend;

use App\BusinessUnit;
use App\Client;
use App\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Liquidation;
use App\WarehouseDocumentType;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use stdClass;

class CollectionTotalReportController extends Controller
{
    public function index() {
		$companies = Company::select('id', 'name')->get();
		$max_datetime = CarbonImmutable::now()->toAtomString();
		$warehouse_document_types = WarehouseDocumentType::select('id', 'name')->get();
		$business_units = BusinessUnit::select('id', 'name')->get();

		return view('backend.collection_total_report')->with(compact('companies', 'max_datetime', 'warehouse_document_types', 'business_units'));
	}

	public function getClients() {
		$company_id = request('company_id');
		$q = request('q');

		$clients = Client::select('id', 'business_name as text')
			->where('company_id', $company_id)
			->where('business_name', 'like', '%'.$q.'%')
			->get();

		return $clients;
	}

	public function validateForm() {
		$messages = [
			'date_type_id.required'					=> 'Debe seleccionar un Tipo de Fecha.',
			'initial_date.required'					=> 'Debe seleccionar una Fecha inicial.',
			'final_date.required'					=> 'Debe seleccionar una Fecha final.',
		];

		$rules = [
			'date_type_id'					=> 'required',
			'initial_date'					=> 'required',
			'final_date'					=> 'required',
		];

		request()->validate($rules, $messages);
		return request()->all();
	}

	public function list() {
		$export = request('export');
		$company_id = request('model.company_id');
		$date_type_id = request('model.date_type_id');
		$initial_date = CarbonImmutable::createFromDate(request('model.initial_date'));
		$final_date = CarbonImmutable::createFromDate(request('model.final_date'));
		$client_type_id = request('model.client_type_id');
		$client_id = request('model.client_id');
		$warehouse_document_type_id = request('model.warehouse_document_type_id');
		$business_unit_id = request('model.business_unit_id');

		$elements = Liquidation::leftjoin('sales', 'liquidations.sale_id', '=', 'sales.id')
			->leftjoin('companies', 'sales.company_id', '=', 'companies.id')
			->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->leftjoin('client_routes', 'clients.route_id', '=', 'client_routes.id')
			->leftjoin('document_types', 'clients.document_type_id', '=', 'document_types.id')
			->leftjoin('warehouse_document_types', 'sales.warehouse_document_type_id', '=', 'warehouse_document_types.id')
			->leftjoin('payment_methods', 'liquidations.payment_method_id', '=', 'payment_methods.id')
			->leftjoin('bank_accounts', 'liquidations.bank_account_id', '=', 'bank_accounts.id')
			->leftjoin('banks', 'bank_accounts.bank_id', '=', 'banks.id')
			->when($company_id, function ($query, $company_id) {
				return $query->where('sales.company_id', $company_id);
			})
			->when($date_type_id == 1, function ($query) use ($initial_date, $final_date) {
				return $query->where('sales.sale_date', '>=', $initial_date->format('Y-m-d'))
							 ->where('sales.sale_date', '<=', $final_date->format('Y-m-d'));
			})
			->when($date_type_id == 2, function ($query) use ($initial_date, $final_date) {
				return $query->where('liquidations.created_at', '>=', $initial_date->startOfDay()->format('Y-m-d H:i:s'))
							 ->where('liquidations.created_at', '<=', $final_date->endOfDay()->format('Y-m-d H:i:s'));
			})
			->when($business_unit_id, function ($query, $business_unit_id) {
				return $query->where('clients.business_unit_id', $business_unit_id);
			})
			->select('companies.short_name as company_short_name','liquidations.collection', DB::Raw('DATE_FORMAT(liquidations.created_at, "%Y-%m-%d") as liquidation_created_at'),
			'sales.sale_date', DB::Raw('(SELECT SUM(liquidations.amount)) AS amount'),
            'payment_methods.name as payment_method_name', 'clients.route_id as route_id', 'client_routes.short_name as route_name')


            ->groupBy('liquidation_created_at','route_id','payment_method_name','liquidations.collection')
			->orderBy('company_short_name')
			->orderBy('liquidation_created_at')
			->orderBy('sale_date')
			->orderBy('route_id')
			->get();


		$elements->map(function ($item, $index) use (&$company_short_name, &$amount, $elements) {
			$item->origin = 'Liquidación';
			if ( $item->collection == 1 ) {
				$item->origin = 'Cobranza';
			}
			
			$item->amount_soles = $item->amount;
			
			return $item;
		});

		$response = [];
		$last_element = count($elements) - 1;
		$company_short_name = '';
		$sum_amount_soles = 0;
		$sum_amount_dolares = 0;
		$total_sum_amount_soles = 0;
		$total_sum_amount_dolares = 0;

		foreach ($elements as $index => $element) {
			if ( $index == 0 ) {
				$company_short_name = $element->company_short_name;
			}

			if ( $company_short_name !== $element->company_short_name ) {				
				$total = new stdClass();
				$total->company_short_name = '';
				$total->route_name = '';
				$total->liquidation_created_at = 'TOTAL ' . $company_short_name;
				$total->sale_date = '';
				$total->amount_soles = number_format($sum_amount_soles, 4, '.', '');
				$total->payment_method_name = '';
				$total->origin = '';

				$total_sum_amount_soles += $sum_amount_soles;
				$total_sum_amount_dolares += $sum_amount_dolares;
				$sum_amount_soles = 0;
				$sum_amount_dolares = 0;

				$response[] = $total;

				$company_short_name = $element->company_short_name;
			}

			$response[] = $element;
			$sum_amount_soles += $element->amount_soles;
			$sum_amount_dolares += $element->amount_dolares;

			if ( $index == $last_element ) {
				$total = new stdClass();
				$total->company_short_name = '';
				$total->route_name = '';
				$total->liquidation_created_at = 'TOTAL ' . $company_short_name;
				$total->sale_date = '';
				$total->amount_soles = number_format($sum_amount_soles, 4, '.', '');
				$total->payment_method_name = '';
				$total->origin = '';

				$total_sum_amount_soles += $sum_amount_soles;
				$total_sum_amount_dolares += $sum_amount_dolares;
				$sum_amount_soles = 0;
				$sum_amount_dolares = 0;

				$response[] = $total;

				$sumTotal = new stdClass();
				$sumTotal->company_short_name = '';
				$sumTotal->route_name = '';
				$sumTotal->liquidation_created_at = 'TOTAL GENERAL';
				$sumTotal->sale_date = '';
				$sumTotal->amount_soles = number_format($total_sum_amount_soles, 4, '.', '');
				$sumTotal->amount_dolares = number_format($total_sum_amount_dolares, 4, '.', '');
				$sumTotal->payment_method_name = '';
				$sumTotal->origin = '';

				$response[] = $sumTotal;
			}
		}

		if ( $export ) {
			$spreadsheet = new Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();
			$sheet->mergeCells('A1:H1');
			$sheet->setCellValue('A1', 'RELACIÓN DE COBRANZAS '.$initial_date->format('d/m/Y').' AL '.$final_date->format('d/m/Y'));
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
			$sheet->setCellValue('C3', 'Ruta');
			$sheet->setCellValue('D3', 'Origen');
			$sheet->setCellValue('E3', 'Fecha cobranza');
			$sheet->setCellValue('F3', 'Fecha emisión');
			$sheet->setCellValue('G3', 'Monto S/');
			$sheet->setCellValue('H3', 'Forma pago');
			$sheet->getStyle('A3:H3')->applyFromArray([
				'font' => [
					'bold' => true,
				],
			]);

			$row_number = 4;
			foreach ($response as $index => $element) {
				$index++;
				$sheet->setCellValueExplicit('A'.$row_number, $index, DataType::TYPE_NUMERIC);
				$sheet->setCellValue('B'.$row_number, $element->company_short_name);
				$sheet->setCellValue('C'.$row_number, $element->route_name);
				$sheet->setCellValue('D'.$row_number, $element->origin);
				$sheet->setCellValue('E'.$row_number, $element->liquidation_created_at);
				$sheet->setCellValue('F'.$row_number, $element->sale_date);
				$sheet->setCellValue('G'.$row_number, $element->amount_soles);
				$sheet->setCellValue('H'.$row_number, $element->payment_method_name);


				$sheet->getStyle('G'.$row_number)->getNumberFormat()->setFormatCode('0.0000');
				

				if ( $element->company_short_name == '' ) {
					$sheet->getStyle('B'.$row_number.':H'.$row_number)->applyFromArray([
						'font' => [
							'bold' => true,
						],
						'alignment' => [
							'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT
						]
					]);
				}

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
			

			$writer = new Xls($spreadsheet);
			return $writer->save('php://output');
		} else {
			return response()->json($response);
		}
	}
}
