<?php

namespace App\Http\Controllers\Backend;

use App\Client;
use App\Company;
use App\Discount;
use App\Efective;
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


class DescuentoReportController extends Controller
{
	public function index()
	{
		$current_date = date(DATE_ATOM, mktime(0, 0, 0));
		return view('backend.descuento_report')->with(compact('current_date'));
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


		$elements = Discount::leftjoin('sales', 'discounts.sale_id', '=', 'sales.id')
			->leftjoin('clients', 'sales.client_id', '=', 'clients.id')
			->leftjoin('companies', 'clients.company_id', '=', 'companies.id')
			->leftjoin('employees', 'discounts.employee_id', '=', 'employees.id')
			->leftjoin('client_routes', 'clients.route_id', '=', 'client_routes.id')
			->leftjoin('managers', 'clients.manager_id', '=', 'managers.id')
			->where('discounts.sale_date', '>=', $initial_date)
			->where('discounts.sale_date', '<=', $final_date)
			->select(
				'discounts.id',
				'discounts.sale_id',
				'discounts.sale_date',
				'discounts.total as total',
				'discounts.paid as paid',
				'discounts.balance as balance',
				'sales.client_id as client_id',
				'clients.business_name as client_name',
				'companies.name as companies_name',
				'client_routes.short_name as routes_short_name',
				'managers.name as managers_name',
				DB::Raw('CONCAT(employees.first_name, " ", employees.last_name) as employ_name')
			)
			->where('discounts.balance', '>', 0)

			//	->orderBy('bank_date')
			//    ->orderBy('guide')
			->get();
		$response = [];



		foreach ($elements as $discount) {

			//	$discount->company_short_name = $discount['company_short_name'];
			$discount->sale_date = $discount['sale_date'];
			$discount->total = $discount['total'];
			$discount->paid = $discount['paid'];
			$discount->balance = $discount['balance'];
			$discount->client_id = $discount['client_id'];
			$discount->client_name = $discount['client_name'];
			$discount->companies_name = $discount['companies_name'];
			$discount->routes_short_name = $discount['routes_short_name'];
			$discount->managers_name = $discount['managers_name'];
			$discount->employ_name = $discount['employ_name'];
			$response[] = $discount;
		}

		//	$totals->bank_date = '';
		$totals = new stdClass();
		$totals->sale_date = 'TOTAL';
		$totals->total = '';
		$totals->paid = '';
		$totals->balance = '';
		$totals->client_id = 'client_id';
		$totals->client_name = 'client_name';
		$totals->companies_name = 'companies_name';
		$totals->routes_short_name = 'routes_short_name';
		$totals->managers_name = 'managers_name';
		$totals->employ_name = 'employ_name';
		$response[] = $totals;


		if ($export) {
			$spreadsheet = new Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();
			$sheet->mergeCells('A1:J1');
			$sheet->setCellValue('A1', 'REPORTE DE DEPOSITOS PENDIENTES ' . CarbonImmutable::now()->format('d/m/Y H:i:s'));
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
			$sheet->setCellValue('C3', 'Fecha de venta');
			$sheet->setCellValue('D3', 'Ruta');
			$sheet->setCellValue('E3', 'Monto Total');
			$sheet->setCellValue('F3', 'Pago a Cuenta');
			$sheet->setCellValue('G3', 'Saldo');
			$sheet->setCellValue('H3', 'Cliente');
			$sheet->setCellValue('I3', 'Supervisor');
			$sheet->setCellValue('J3', 'Cargo a Cuenta');

			$sheet->getStyle('A3:J3')->applyFromArray([
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
				$sheet->setCellValue('B' . $row_number, $element->companies_name);
				$sheet->setCellValue('C' . $row_number, $element->sale_date);
				$sheet->setCellValue('D' . $row_number, $element->routes_short_name);
				$sheet->setCellValue('E' . $row_number, $element->total);
				$sheet->setCellValue('F' . $row_number, $element->paid);
				$sheet->setCellValue('G' . $row_number, $element->balance);
				$sheet->setCellValue('H' . $row_number, $element->client_name);
				$sheet->setCellValue('I' . $row_number, $element->managers_name);
				$sheet->setCellValue('J' . $row_number, $element->employ_name);


				$sheet->getStyle('D' . $row_number)->getNumberFormat()->setFormatCode('0.00');
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

			$writer = new Xls($spreadsheet);
			return $writer->save('php://output');
		} else {
			return response()->json($response);
		}
	}
}
