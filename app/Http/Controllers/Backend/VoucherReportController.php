<?php

namespace App\Http\Controllers\Backend;

use App\Client;
use App\Deposit;
use App\Http\Controllers\Controller;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use stdClass;


class VoucherReportController extends Controller
{
	public function index()
	{
		$current_date = date(DATE_ATOM, mktime(0, 0, 0));
		return view('backend.voucher_report')->with(compact('current_date'));
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


		$elements = Deposit::leftjoin('bank_accounts', 'deposits.bank_account_id', '=', 'bank_accounts.id')
			->join('client_routes', 'deposits.route_id', '=', 'client_routes.id')
			->leftjoin('banks', 'bank_accounts.bank_id', '=', 'banks.id')
			->where('deposits.bank_date', '>=', $initial_date)
			->where('deposits.bank_date', '<=', $final_date)
			->select(
				'deposits.id',
				'deposits.bank_date',
				'deposits.total as total',
				'deposits.operation_number as operation_number',
				'deposits.total_pend as total_pend',
				'client_routes.name as client_route_name',
				DB::Raw('DATE_FORMAT(deposits.created_at, "%Y-%m-%d") as liquidation_date'),
				DB::Raw('CONCAT(banks.short_name, "-", bank_accounts.account_number) as bank_account')
			)

			->orderBy('bank_date')

			->get();
		$response = [];

		foreach ($elements as $saledetail) {
			//	$saledetail->company_short_name = $saledetail['company_short_name'];
			$saledetail->bank_date = $saledetail['bank_date'];
			$saledetail->liquidation_date = $saledetail['liquidation_date'];
			$saledetail->client_route_name = $saledetail['client_route_name'];
			$saledetail->bank_account = $saledetail['bank_account'];
			$saledetail->operation_number = $saledetail['operation_number'];
			$saledetail->total = $saledetail['total'];
			$saledetail->total_pend = $saledetail['total_pend'];
			$response[] = $saledetail;
		}

		if ($export) {
			$spreadsheet = new Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();
			$sheet->mergeCells('A1:H1');
			$sheet->setCellValue('A1', 'REPORTE DE DEPOSITOS ' . CarbonImmutable::now()->format('d/m/Y H:i:s'));
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
			$sheet->setCellValue('B3', 'Fecha de Liquidación');
			$sheet->setCellValue('C3', 'Fecha de Voucher');
			$sheet->setCellValue('D3', 'Destino');
			$sheet->setCellValue('E3', 'N° Operación');
			$sheet->setCellValue('F3', 'Monto');
			$sheet->setCellValue('G3', 'Saldo Disponible');
			$sheet->setCellValue('H3', 'Ruta');


			$sheet->getStyle('A3:H3')->applyFromArray([
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
				$sheet->setCellValue('B' . $row_number, $element->liquidation_date);
				$sheet->setCellValue('C' . $row_number, $saleDateYear);
				$sheet->setCellValue('D' . $row_number, $element->bank_account);
				$sheet->setCellValue('E' . $row_number, $element->operation_number);
				$sheet->setCellValue('F' . $row_number, $element->total);
				$sheet->setCellValue('G' . $row_number, $element->total_pend);
				$sheet->setCellValue('H' . $row_number, $element->client_route_name);


				$sheet->getStyle('F' . $row_number)->getNumberFormat()->setFormatCode('0.00');
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
