<?php

namespace App\Http\Controllers\Backend;

use App\Client;
use App\Efective;
use App\Http\Controllers\Controller;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use stdClass;


class EfectivesReportController extends Controller
{
	public function index()
	{
		$current_date = date(DATE_ATOM, mktime(0, 0, 0));
		return view('backend.efectives_report')->with(compact('current_date'));
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


		$elements = Efective::leftjoin('warehouse_movements', 'warehouse_movements.id', '=', 'efectives.warehouse_movement_id')
			->leftjoin('client_routes', 'client_routes.id', '=', 'warehouse_movements.route_id')
			->where('efectives.bank_date', '>=', $initial_date)
			->where('efectives.bank_date', '<=', $final_date)
			->select(
				'efectives.id',
				'efectives.bank_date',
				'efectives.total as total',
				DB::Raw('CONCAT(efectives.serie_number, "-", efectives.guide_number) as guide'),
				'client_routes.name as ruta'
			)
			->orderBy('bank_date')
			->orderBy('guide')
			->get();
		$response = [];

		$total_general = 0;
		foreach ($elements as $saledetail) {

			//	$saledetail->company_short_name = $saledetail['company_short_name'];
			$saledetail->bank_date = $saledetail['bank_date'];
			$saledetail->guide = $saledetail['guide'];
			$saledetail->ruta = $saledetail['ruta'];
			$saledetail->total = $saledetail['total'];
			$total_general += $saledetail->total;
			$response[] = $saledetail;
		}


		$totals = new stdClass();
		$totals->bank_date = 'TOTAL';
		//	$totals->bank_date = '';
		$totals->guide = '';
		$totals->ruta = '';
		$totals->total = $total_general;
		$response[] = $totals;

		if ($export) {
			$spreadsheet = new Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();
			$sheet->mergeCells('A1:AB1');
			$sheet->setCellValue('A1', 'REPORTE DE INGRESO DE EFECTIVO ' . CarbonImmutable::now()->format('d/m/Y H:m:s'));
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
			//	$sheet->setCellValue('B3', 'Compañía');
			$sheet->setCellValue('B3', 'Fecha de Ingreso');
			$sheet->setCellValue('C3', 'Guía');
			$sheet->setCellValue('D3', 'RUTA');
			$sheet->setCellValue('E3', 'Total');


			$sheet->getStyle('A3:E3')->applyFromArray([
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

				if ($element->bank_date) {
					$saleDateObject = date('d/m/Y', strtotime($element->bank_date));
					$saleDateYear = $saleDateObject;
					//		$saleDateMonth = str_pad($saleDateObject->month, 2, '0', STR_PAD_LEFT);
					//		$saleDateDay = str_pad($saleDateObject->day, 2, '0', STR_PAD_LEFT);
				}




				$sheet->setCellValueExplicit('A' . $row_number, $index, DataType::TYPE_NUMERIC);
				//	$sheet->setCellValue('B'.$row_number, $element->company_short_name);
				$sheet->setCellValue('B' . $row_number, $saleDateYear);
				$sheet->setCellValue('C' . $row_number, $element->guide);
				$sheet->setCellValue('D' . $row_number, $element->ruta);
				$sheet->setCellValue('E' . $row_number, $element->total);

				$sheet->getStyle('D' . $row_number)->getNumberFormat()->setFormatCode('0.00');
				$row_number++;
			}

			$sheet->getColumnDimension('A')->setAutoSize(true);
			$sheet->getColumnDimension('B')->setAutoSize(true);
			$sheet->getColumnDimension('C')->setAutoSize(true);
			$sheet->getColumnDimension('D')->setAutoSize(true);
			$sheet->getColumnDimension('E')->setAutoSize(true);

			$writer = new Xls($spreadsheet);
			return $writer->save('php://output');
		} else {
			return response()->json($response);
		}
	}
}
