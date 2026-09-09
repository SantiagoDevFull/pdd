<?php

namespace App\Http\Controllers\Backend;

use App\ClientMotor;
use App\Exports\OrderWorkReportExport;
use App\Http\Controllers\Controller;
use App\OrderWork;
use App\OrderWorkDetail;
use App\Vehicle;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Fpdf;

class OrderWorkReportController extends Controller
{
	public function index()
	{
		return view('backend.order_work_report');
	}

	public function list()
	{

		$export = request('flag_export');

		$order_works = OrderWork::leftjoin('companies', 'companies.id', '=', 'order_works.company_id')
			->leftjoin('asesores', 'asesores.id', '=', 'order_works.asesor_id')
			->leftjoin('vehicles', 'vehicles.id', '=', 'order_works.vehicle_id')
			->leftjoin('company_securities', 'company_securities.id', '=', 'order_works.security_id')
			->leftjoin('client_motors', 'client_motors.id', '=', 'order_works.client_motor_id')
			->select(
				'order_works.id as order_id',
				'companies.name as company_name',
				'asesores.name as asesor_name',
				'vehicles.plate as vehicle_plate',
				'company_securities.name as security_name',
				'client_motors.business_name as client_motor_name',
				DB::Raw('CONCAT(order_works.serie,"-",order_works.correlative) as serie'),
				'order_works.km as km',
				'order_works.observation as observation',
				'order_works.issue_date as issue_date',
				'order_works.state as state'
			)
			->get();

		if ($export) {
			
			$datetime = CarbonImmutable::now()->format('d/m/Y h:i:s a');

			$excel = new OrderWorkReportExport($datetime, $order_works);
			return Excel::download($excel, 'reporte-orden-trabajo.xlsx');
		}

		return $order_works;
	}

	public function delete()
	{
		$order_work_id = request('order_id');

		$order_work = OrderWork::findOrFail($order_work_id);

		$element = Vehicle::findOrFail($order_work->vehicle_id);
		$element->client_motor_id = '';
		$element->state = 0;
		$element->save();

		/*
		$client = ClientMotor::findOrFail($order_work->client_motor_id);
		$client->vehicle_id = '';
		$client->save();
		*/

		$order_work->state = 2;
		$order_work->save();
	}

	public function aprobar()
	{
		$order_work_id = request('order_id');
		$order_work = OrderWork::findOrFail($order_work_id);

		$element = Vehicle::findOrFail($order_work->vehicle_id);
		$element->state = 2;
		$element->save();

		$order_work->state = 1;
		$order_work->save();
	}

	public function getClients()
	{

		$q = request('q');

		$clients = ClientMotor::select('id', 'business_name')
			->where('business_name', 'like', '%' . $q . '%')
			->get();

		$clients->map(function ($item, $index) {
			$item->text = $item->business_name;
			unset($item->business_name);

			return $item;
		});

		return $clients;
	}

	public function validateModalForm()
	{
		$messages = [
			'client_id.required'            => 'Debe seleccionar un Cliente.',
		];

		$rules = [
			'client_id'                        => 'required',
		];

		request()->validate($rules, $messages);
		return request()->all();
	}

	public function assignClient()
	{

		$this->validateModalForm();

		$vehicle_id = request('id');
		$client_id = request('client_id');

		$element = ClientMotor::findOrFail($client_id);
		$element->vehicle_id = $vehicle_id;
		$element->save();

		$vehicle = Vehicle::findOrFail($vehicle_id);
		$vehicle->state = 1;
		$vehicle->save();

		$type = 1;
		$title = '¡Ok!';
		$msg = 'Cliente asignado exitosamente.';

		return response()->json([
			'type'     => $type,
			'title'    => $title,
			'msg'      => $msg,
		]);
	}

	public function view_asign()
	{
		$order_work_id = request('order_id');

		$work_details = OrderWorkDetail::where('order_work_id', $order_work_id)->get();

		return $work_details;
	}


	public function formato()
	{
		$mytime = Carbon::now('America/Lima');
		$mytime->toDateTimeString();
		$mytime = $mytime->format('Y-m-d');

		$order_work_id = request('order_id');

		$ordenTrabajo = OrderWork::leftjoin('companies', 'companies.id', '=', 'order_works.company_id')
			->leftjoin('vehicles', 'vehicles.id', '=', 'order_works.vehicle_id')
			->leftjoin('client_motors', 'client_motors.id', '=', 'order_works.client_motor_id')
			->leftjoin('document_types', 'document_types.id', '=', 'client_motors.document_type_id')
			->leftjoin('client_address_motors', 'client_address_motors.client_motor_id', '=', 'client_motors.id')
			->leftjoin('sedes', 'sedes.company_id', '=', 'companies.id')
			->leftjoin('brands', 'brands.id', '=', 'vehicles.brand_id')
			->leftjoin('modelos', 'modelos.id', '=', 'vehicles.modelo_id')
			->leftjoin('asesores', 'asesores.id', '=', 'order_works.asesor_id')
			->select(
				'order_works.client_name as client_name',
				DB::Raw('CONCAT(order_works.serie,"-",order_works.correlative) as serie'),
				//DB::Raw('DATE_FORMAT(issue_date, "%d-%m-%Y") as fecha'),
				'order_works.created_at as fecha',
				'vehicles.plate as placa',
				'document_types.name as document_type_name',
				'client_motors.document_number as document_number_client',
				'client_address_motors.address as direccion',
				'client_motors.phone_number_1 as telefono',
				'client_motors.email as correo',
				'sedes.name as sede',
				'vehicles.chasis as chasis',
				'vehicles.color as color',
				'brands.description as marca',
				'vehicles.year_model as anio_mod',
				'modelos.description as modelo',
				'order_works.km as kms_vehiculo',
				'vehicles.motor as motor',
				'asesores.name as asesor',
				'order_works.observation as observacion'
			)
			->where('order_works.id', $order_work_id)
			->first();

		$detalle = OrderWorkDetail::select('type', 'name')
			->where('order_work_id', $order_work_id)
			->get();

		$pdf = app('Fpdf');
		$pdf->SetAutoPageBreak(true, 5);
		$pdf->AddPage();
		$pdf->SetTextColor(4, 4, 4);
		$pdf->SetFont('Arial', 'B', 10);

		$pdf->setX(160);
		//		$pdf->Cell(5,5, $ordenTrabajo->codigo);
		$pdf->Cell(5, 5, $ordenTrabajo->serie);
		$pdf->Ln();

		$cellBox = 190;
		//Bloque Cliente
		$pdf->Rect(10, 28, $cellBox, 15);
		//Bloque Vehiculo
		$pdf->Rect(10, 45, $cellBox, 20);
		//Bloque Inventario
		$pdf->Rect(10, 150, $cellBox, 102);

		$pdf->SetFont('Arial', 'B', 10);
		$pdf->Cell(0, 0, utf8_decode("Placa Nº"), 0, "", "C");
		$pdf->Ln();

		$pdf->SetFont('Arial', 'B', 14);
		$pdf->Cell(0, 10, utf8_decode($ordenTrabajo->placa), 0, "", "C");
		$pdf->Image(asset('backend/img/AdminLTELogoC.png'), 20, 8, 65, 18, 'PNG', '');
		$pdf->Image(asset('backend/img/rayado.png'), 155, 110, 6, 6, 'PNG', '');
		$pdf->Image(asset('backend/img/golpeado.png'), 155, 115, 6, 6, 'PNG', '');
		$pdf->Image(asset('backend/img/quinado.png'), 155, 120, 6, 6, 'PNG', '');
		$pdf->Ln();

		$pdf->SetFont('Arial', '', 7);
		$pdf->SetXY(160, 15);
		$pdf->Cell(0, 5, utf8_decode("Fecha: " . $ordenTrabajo->fecha));

		$pdf->SetXY(160, 20);
		$pdf->Cell(5, 5, "Sede: " . $ordenTrabajo->sede);

		//Cabecera Cliente
		$pdf->SetXY(10, 28);

		$pdf->SetFont('Arial', '', 9);
		$pdf->Cell(20, 5, utf8_decode($ordenTrabajo->document_type_name . ":"), 0, "", "L");
		$pdf->Cell(40, 5, utf8_decode($ordenTrabajo->document_number_client), 0, "", "L");
		$pdf->Cell(20, 5, utf8_decode("Teléfono:"), 0, "", "L");
		$pdf->Cell(40, 5, utf8_decode($ordenTrabajo->telefono), 0, "", "L");
		$pdf->Ln();

		$pdf->SetFont('Arial', '', 9);
		$pdf->Cell(20, 5, utf8_decode("Cliente:"), 0, "", "L");
		$pdf->Cell(40, 5, utf8_decode($ordenTrabajo->client_name), 0, "", "L");
		$pdf->Cell(20, 5, utf8_decode("Dirección:"), 0, "", "L");
		$pdf->Cell(170, 5, utf8_decode($ordenTrabajo->direccion), 0, "", "L");
		$pdf->Ln();

		$pdf->SetFont('Arial', '', 9);
		$pdf->Cell(20, 5, utf8_decode("Correo:"), 0, "", "L");
		$pdf->Cell(170, 5, utf8_decode($ordenTrabajo->correo), 0, "", "L");
		$pdf->Ln();

		//Cabecera Vehiculo
		$pdf->SetXY(10, 45);
		$pdf->SetFont('Arial', '', 9);
		$pdf->Cell(20, 5, utf8_decode("Chasis:"), 0, "", "L");
		$pdf->Cell(40, 5, utf8_decode($ordenTrabajo->chasis), 0, "", "L");
		$pdf->Cell(20, 5, utf8_decode("Color:"), 0, "", "L");
		$pdf->Cell(30, 5, utf8_decode($ordenTrabajo->color), 0, "", "L");
		$pdf->Ln();

		$pdf->Cell(20, 5, utf8_decode("Marca:"), 0, "", "L");
		$pdf->Cell(40, 5, utf8_decode($ordenTrabajo->marca), 0, "", "L");
		$pdf->Cell(20, 5, utf8_decode("Asesor:"), 0, "", "L");
		$pdf->Cell(40, 5, utf8_decode($ordenTrabajo->asesor), 0, "", "L");
		$pdf->Ln();

		$pdf->Cell(20, 5, utf8_decode("Modelo:"), 0, "", "L");
		$pdf->Cell(40, 5, utf8_decode($ordenTrabajo->modelo), 0, "", "L");
		$pdf->Cell(20, 5, utf8_decode("Año Modelo:"), 0, "", "L");
		$pdf->Cell(20, 5, utf8_decode($ordenTrabajo->anio_mod), 0, "", "L");
		$pdf->Ln();

		$pdf->Cell(20, 5, utf8_decode("Km entrante:"), 0, "", "L");
		$pdf->Cell(40, 5, utf8_decode($ordenTrabajo->kms_vehiculo), 0, "", "L");
		$pdf->Cell(20, 5, utf8_decode("Motor:"), 0, "", "L");
		$pdf->Cell(20, 5, utf8_decode($ordenTrabajo->motor), 0, "", "L");
		$pdf->Ln();

		//Detalle Servicios
		$pdf->SetXY(10, 66);
		$pdf->SetFont('Arial', '', 9);
		$pdf->Cell(20, 5, utf8_decode("El cliente solicita los siguientes servicios:"), 0, "", "L");

		$y = 72;
		$recty = 72;
		$pdf->SetFont('Arial', '', 7);

		foreach ($detalle as $item) {
			//$pdf->Rect(10, $recty, 20, 3);
			$pdf->Rect(10, $recty, 190, 3);

			//$pdf->setXY(10,$y);
			//$pdf->Cell(20,3,utf8_decode($reg->codigo),0,"","C");
			$pdf->setXY(10, $y);
			$pdf->Cell(190, 3, utf8_decode($item->type . ' - ' . $item->name), 0, "", "L");

			$y = $y + 3;
			$recty = $recty + 3;
		}

		$pdf->Image(asset('backend/img/left.png'), 20, 85, 36, 26, 'PNG', '');
		$pdf->Image(asset('backend/img/right.png'), 20, 115, 36, 26, 'PNG', '');
		$pdf->Image(asset('backend/img/front.png'), 70, 85, 26, 26, 'PNG', '');
		$pdf->Image(asset('backend/img/later.png'), 70, 115, 26, 26, 'PNG', '');

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetFont('Arial', '', 8);

		$pdf->SetXY(120, 90);
		$pdf->Cell(50, 5, utf8_decode("TARJETA DE PROP Y SOAT"), 1, "", "L");

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->tarjeta_soat == '1') {
			$pdf->SetXY(170, 90);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 5, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(170, 90);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 5, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);


		$pdf->SetXY(120, 95);
		$pdf->Cell(50, 5, utf8_decode("MANUAL PROPIETARIO"), 1, "", "L");

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->manual_prop == '1') {
			$pdf->SetXY(170, 95);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 5, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(170, 95);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 5, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(120, 100);
		$pdf->Cell(50, 5, utf8_decode("MANUAL SERV Y GARANTIA"), 1, "", "L");

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->manual_serv_gar == '1') {
			$pdf->SetXY(170, 100);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 5, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(170, 100);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 5, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);




		$pdf->SetXY(120, 110);
		$pdf->Cell(30, 5, utf8_decode("RAYADO"), 1, "", "L");

		$pdf->SetXY(150, 110);
		$pdf->Cell(15, 5, '', 1, 0, 'C', false);

		$pdf->SetXY(120, 115);
		$pdf->Cell(30, 5, utf8_decode("GOLPEADO"), 1, "", "L");

		$pdf->SetXY(150, 115);
		$pdf->Cell(15, 5, '', 1, 0, 'C', false);

		$pdf->SetXY(120, 120);
		$pdf->Cell(30, 5, utf8_decode("QUIÑADO"), 1, "", "L");

		$pdf->SetXY(150, 120);
		$pdf->Cell(15, 5, '', 1, 0, 'C', false);

		$pdf->SetFont('Arial', 'B', 7);
		$pdf->SetXY(15, 150);
		$pdf->Cell(40, 5, "PARTES O ACCESORIOS", 0, "", "L");
		$pdf->Cell(6, 5, "SI", 0, "", "L");
		$pdf->Cell(5, 5, "NO", 0, "", "L");
		$pdf->Cell(5, 5, "CANT", 0, "", "L");

		$pdf->SetXY(75, 150);
		$pdf->Cell(40, 5, "PARTES O ACCESORIOS", 0, "", "L");
		$pdf->Cell(6, 5, "SI", 0, "", "L");
		$pdf->Cell(5, 5, "NO", 0, "", "L");
		$pdf->Cell(5, 5, "CANT", 0, "", "L");

		$pdf->SetXY(135, 150);
		$pdf->Cell(40, 5, "PARTES O ACCESORIOS", 0, "", "L");
		$pdf->Cell(6, 5, "SI", 0, "", "L");
		$pdf->Cell(5, 5, "NO", 0, "", "L");
		$pdf->Cell(5, 5, "CANT", 0, "", "L");

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$textypos = 154;
		$off = $textypos + 2;

		$pdf->SetFont('Arial', '', 8);

		$pdf->SetXY(15, $off);
		$pdf->Cell(40, 4, utf8_decode("Antena de Radio"), 0, "", "L");


		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->ante_radio == '1') {
			$pdf->SetXY(55, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(55, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->ante_radio == '0') {
			$pdf->SetXY(61, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(61, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(67, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_ante_radio, 1, 0, 'C', true);


		$pdf->SetXY(75, $off);
		$pdf->Cell(40, 4, utf8_decode("Manija de Puertas"), 0, "", "L");
		$pdf->SetXY(115, $off);
		$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		$pdf->SetXY(121, $off);
		$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->mani_puer == '1') {
			$pdf->SetXY(115, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(115, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->mani_puer == '0') {
			$pdf->SetXY(121, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(121, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(127, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_mani_puer, 1, 0, 'C', true);

		$pdf->SetXY(135, $off);
		$pdf->Cell(40, 4, utf8_decode("Cinturón de seguridad"), 0, "", "L");

		if ($ordenTrabajo->cint_segu == '1') {
			$pdf->SetXY(175, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(175, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->cint_segu == '0') {
			$pdf->SetXY(181, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(181, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);


		$pdf->SetXY(187, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_cint_segu, 1, 0, 'C', true);

		$off = $off + 5;

		$pdf->SetXY(15, $off);
		$pdf->Cell(40, 4, utf8_decode("Alarma de retro"), 0, "", "L");
		if ($ordenTrabajo->alar_retro == '1') {

			$pdf->SetXY(55, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(55, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->alar_retro == '0') {
			$pdf->SetXY(61, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(61, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(67, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_alar_retro, 1, 0, 'C', true);

		$pdf->SetXY(75, $off);
		$pdf->Cell(40, 4, utf8_decode("Mangueras de carreta"), 0, "", "L");
		if ($ordenTrabajo->man_carre == '1') {

			$pdf->SetXY(115, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(115, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->man_carre == '0') {
			$pdf->SetXY(121, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(121, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(127, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_man_carre, 1, 0, 'C', true);

		$pdf->SetXY(135, $off);
		$pdf->Cell(40, 4, utf8_decode("Consola de instrumentos"), 0, "", "L");
		if ($ordenTrabajo->cons_inst == '1') {

			$pdf->SetXY(175, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(175, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->cons_inst == '0') {
			$pdf->SetXY(181, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(181, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);
		$pdf->SetXY(187, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_cons_inst, 1, 0, 'C', true);

		$off = $off + 5;

		$pdf->SetXY(15, $off);
		$pdf->Cell(40, 4, utf8_decode("Batería (marca)"), 0, "", "L");
		if ($ordenTrabajo->bateria == '1') {

			$pdf->SetXY(55, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(55, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->bateria == '0') {
			$pdf->SetXY(61, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(61, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(67, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_bateria, 1, 0, 'C', true);

		$pdf->SetXY(75, $off);
		$pdf->Cell(40, 4, utf8_decode("Protector de motor izq/der"), 0, "", "L");
		if ($ordenTrabajo->prot_motor == '1') {

			$pdf->SetXY(115, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(115, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->prot_motor == '0') {
			$pdf->SetXY(121, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(121, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);



		$pdf->SetXY(127, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_prot_motor, 1, 0, 'C', true);

		$pdf->SetXY(135, $off);
		$pdf->Cell(40, 4, utf8_decode("Tablero de instrumentos"), 0, "", "L");
		if ($ordenTrabajo->tab_inst == '1') {

			$pdf->SetXY(175, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(175, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->tab_inst == '0') {
			$pdf->SetXY(181, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(181, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(187, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_tab_inst, 1, 0, 'C', true);

		$off = $off + 5;

		$pdf->SetXY(15, $off);
		$pdf->Cell(40, 4, utf8_decode("Brazo de plumilla"), 0, "", "L");
		if ($ordenTrabajo->braz_plum == '1') {

			$pdf->SetXY(55, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(55, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->braz_plum == '0') {
			$pdf->SetXY(61, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(61, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(67, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_braz_plum, 1, 0, 'C', true);

		$pdf->SetXY(75, $off);
		$pdf->Cell(40, 4, utf8_decode("Tapa de batería"), 0, "", "L");
		if ($ordenTrabajo->tapa_bateria == '1') {

			$pdf->SetXY(115, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(115, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->tapa_bateria == '0') {
			$pdf->SetXY(121, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(121, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(127, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_tapa_bateria, 1, 0, 'C', true);

		$pdf->SetXY(135, $off);
		$pdf->Cell(40, 4, utf8_decode("Colchón"), 0, "", "L");

		if ($ordenTrabajo->colchon == '1') {

			$pdf->SetXY(175, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(175, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->colchon == '0') {
			$pdf->SetXY(181, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(181, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);
		$pdf->SetXY(187, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_colchon, 1, 0, 'C', true);

		$off = $off + 5;

		$pdf->SetXY(15, $off);
		$pdf->Cell(40, 4, utf8_decode("Plumillas"), 0, "", "L");
		if ($ordenTrabajo->plumilla == '1') {

			$pdf->SetXY(55, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(55, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->plumilla == '0') {
			$pdf->SetXY(61, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(61, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(67, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_plumilla, 1, 0, 'C', true);

		$pdf->SetXY(75, $off);
		$pdf->Cell(40, 4, utf8_decode("Tapa de combustible"), 0, "", "L");
		if ($ordenTrabajo->tapa_combus == '1') {

			$pdf->SetXY(115, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(115, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->tapa_combus == '0') {
			$pdf->SetXY(121, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(121, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(127, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_tapa_combus, 1, 0, 'C', true);

		$pdf->SetXY(135, $off);
		$pdf->Cell(40, 4, utf8_decode("Cuerpo de radio musical"), 0, "", "L");
		if ($ordenTrabajo->cuerpo_radio == '1') {
			$pdf->SetXY(175, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(175, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->cuerpo_radio == '0') {
			$pdf->SetXY(181, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(181, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(187, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_cuerpo_radio, 1, 0, 'C', true);

		$off = $off + 5;

		$pdf->SetXY(15, $off);
		$pdf->Cell(40, 4, utf8_decode("Cabezal de filtro separador"), 0, "", "L");
		if ($ordenTrabajo->cabe_filtro == '1') {
			$pdf->SetXY(55, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(55, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->cabe_filtro == '0') {
			$pdf->SetXY(61, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(61, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(67, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_cabe_filtro, 1, 0, 'C', true);

		$pdf->SetXY(75, $off);
		$pdf->Cell(40, 4, utf8_decode("Quinta rueda"), 0, "", "L");
		if ($ordenTrabajo->quin_rueda == '1') {
			$pdf->SetXY(115, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(115, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->quin_rueda == '0') {
			$pdf->SetXY(121, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(121, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(127, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_quin_rueda, 1, 0, 'C', true);

		$pdf->SetXY(135, $off);
		$pdf->Cell(40, 4, utf8_decode("Encendedor de cigarro"), 0, "", "L");
		if ($ordenTrabajo->ence_ciga == '1') {
			$pdf->SetXY(175, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(175, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->ence_ciga == '0') {
			$pdf->SetXY(181, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(181, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(187, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_ence_ciga, 1, 0, 'C', true);

		$off = $off + 5;

		$pdf->SetXY(15, $off);
		$pdf->Cell(40, 4, utf8_decode("Cable eléctrico de carreta"), 0, "", "L");
		if ($ordenTrabajo->cable_elec == '1') {
			$pdf->SetXY(55, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(55, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->cable_elec == '0') {
			$pdf->SetXY(61, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(61, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(67, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_cable_elec, 1, 0, 'C', true);

		$pdf->SetXY(75, $off);
		$pdf->Cell(40, 4, utf8_decode("Deflector izq/der/sup"), 0, "", "L");
		if ($ordenTrabajo->deflector == '1') {
			$pdf->SetXY(115, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(115, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->deflector == '0') {
			$pdf->SetXY(121, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(121, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(127, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_deflector, 1, 0, 'C', true);

		$pdf->SetXY(135, $off);
		$pdf->Cell(40, 4, utf8_decode("Luz de salón"), 0, "", "L");
		if ($ordenTrabajo->luz_salo == '1') {
			$pdf->SetXY(175, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(175, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->luz_salo == '0') {
			$pdf->SetXY(181, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(181, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(187, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_luz_salo, 1, 0, 'C', true);

		$off = $off + 5;

		$pdf->SetXY(15, $off);
		$pdf->Cell(40, 4, utf8_decode("Claxon eléctrico"), 0, "", "L");
		if ($ordenTrabajo->clax_elec == '1') {
			$pdf->SetXY(55, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(55, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->clax_elec == '0') {
			$pdf->SetXY(61, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(61, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(67, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_clax_elec, 1, 0, 'C', true);

		$pdf->SetXY(75, $off);
		$pdf->Cell(40, 4, utf8_decode("Tubo de escape"), 0, "", "L");
		if ($ordenTrabajo->tubo_esca == '1') {
			$pdf->SetXY(115, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(115, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->tubo_esca == '0') {
			$pdf->SetXY(121, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(121, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(127, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_tubo_esca, 1, 0, 'C', true);

		$pdf->SetXY(135, $off);
		$pdf->Cell(40, 4, utf8_decode("Faros de puerta"), 0, "", "L");
		if ($ordenTrabajo->faro_puer == '1') {
			$pdf->SetXY(175, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(175, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->faro_puer == '0') {
			$pdf->SetXY(181, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(181, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(187, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_faro_puer, 1, 0, 'C', true);

		$off = $off + 5;

		$pdf->SetXY(15, $off);
		$pdf->Cell(40, 4, utf8_decode("Claxon de aire"), 0, "", "L");
		if ($ordenTrabajo->clax_aire == '1') {
			$pdf->SetXY(55, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(55, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->clax_aire == '0') {
			$pdf->SetXY(61, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(61, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(67, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_clax_aire, 1, 0, 'C', true);

		$pdf->SetXY(75, $off);
		$pdf->Cell(40, 4, utf8_decode("Compresor y faja A/C"), 0, "", "L");
		if ($ordenTrabajo->comp_faja == '1') {
			$pdf->SetXY(115, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(115, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->comp_faja == '0') {
			$pdf->SetXY(121, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(121, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(127, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_comp_faja, 1, 0, 'C', true);

		$pdf->SetXY(135, $off);
		$pdf->Cell(40, 4, utf8_decode("Llave de contacto"), 0, "", "L");
		if ($ordenTrabajo->llav_cont == '1') {
			$pdf->SetXY(175, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(175, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->llav_cont == '0') {
			$pdf->SetXY(181, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(181, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(187, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_llav_cont, 1, 0, 'C', true);

		$off = $off + 5;

		$pdf->SetXY(15, $off);
		$pdf->Cell(40, 4, utf8_decode("Emblema de capot"), 0, "", "L");
		if ($ordenTrabajo->emb_cap == '1') {
			$pdf->SetXY(55, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(55, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->emb_cap == '0') {
			$pdf->SetXY(61, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(61, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(67, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_emb_cap, 1, 0, 'C', true);

		$pdf->SetXY(75, $off);
		$pdf->Cell(40, 4, utf8_decode("Tapa de caja de fusibles"), 0, "", "L");
		if ($ordenTrabajo->tapa_fus == '1') {
			$pdf->SetXY(115, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(115, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->tapa_fus == '0') {
			$pdf->SetXY(121, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(121, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(127, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_tapa_fus, 1, 0, 'C', true);

		$pdf->SetXY(135, $off);
		$pdf->Cell(40, 4, utf8_decode("Paquete de switch"), 0, "", "L");
		if ($ordenTrabajo->paq_swit == '1') {
			$pdf->SetXY(175, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(175, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->paq_swit == '0') {
			$pdf->SetXY(181, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(181, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(187, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_paq_swit, 1, 0, 'C', true);

		$off = $off + 5;

		$pdf->SetXY(15, $off);
		$pdf->Cell(40, 4, utf8_decode("Emblema de puertas"), 0, "", "L");
		if ($ordenTrabajo->emb_pue == '1') {
			$pdf->SetXY(55, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(55, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->emb_pue == '0') {
			$pdf->SetXY(61, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(61, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(67, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_emb_pue, 1, 0, 'C', true);

		$pdf->SetXY(75, $off);
		$pdf->Cell(40, 4, utf8_decode("Carcaza y tapa de filtro de aire"), 0, "", "L");
		if ($ordenTrabajo->car_tapa == '1') {
			$pdf->SetXY(115, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(115, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->car_tapa == '0') {
			$pdf->SetXY(121, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(121, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(127, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_car_tapa, 1, 0, 'C', true);

		$pdf->SetXY(135, $off);
		$pdf->Cell(40, 4, utf8_decode("Tapa de fusible de cabina"), 0, "", "L");
		if ($ordenTrabajo->tapa_fus == '1') {
			$pdf->SetXY(175, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(175, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->tapa_fus == '0') {
			$pdf->SetXY(181, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(181, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(187, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_tapa_fus, 1, 0, 'C', true);

		$off = $off + 5;

		$pdf->SetXY(15, $off);
		$pdf->Cell(40, 4, utf8_decode("Escarpines y soportes"), 0, "", "L");
		$pdf->SetXY(55, $off);
		$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		$pdf->SetXY(61, $off);
		$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		if ($ordenTrabajo->esc_sop == '1') {
			$pdf->SetXY(55, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(55, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->esc_sop == '0') {
			$pdf->SetXY(61, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(61, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(67, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_esc_sop, 1, 0, 'C', true);

		$pdf->SetXY(75, $off);
		$pdf->Cell(40, 4, utf8_decode("Tapa de TQ de expansión"), 0, "", "L");
		if ($ordenTrabajo->tapa_tq_exp == '1') {
			$pdf->SetXY(115, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(115, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->tapa_tq_exp == '0') {
			$pdf->SetXY(121, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(121, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(127, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_tapa_tq_exp, 1, 0, 'C', true);

		$pdf->SetXY(135, $off);
		$pdf->Cell(40, 4, utf8_decode("Tapasol"), 0, "", "L");
		if ($ordenTrabajo->tapasol == '1') {
			$pdf->SetXY(175, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(175, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->tapasol == '0') {
			$pdf->SetXY(181, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(181, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(187, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_tapasol, 1, 0, 'C', true);

		$off = $off + 5;

		$pdf->SetXY(15, $off);
		$pdf->Cell(40, 4, utf8_decode("Guardabarros"), 0, "", "L");
		if ($ordenTrabajo->guardabarro == '1') {
			$pdf->SetXY(55, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(55, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->guardabarro == '0') {
			$pdf->SetXY(61, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(61, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(67, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_guardabarro, 1, 0, 'C', true);

		$pdf->SetXY(75, $off);
		$pdf->Cell(40, 4, utf8_decode("Tapa de TQ de recuperación"), 0, "", "L");
		if ($ordenTrabajo->tapa_tq_recu == '1') {
			$pdf->SetXY(115, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(115, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->tapa_tq_recu == '0') {
			$pdf->SetXY(121, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(121, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(127, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_tapa_tq_recu, 1, 0, 'C', true);

		$pdf->SetXY(135, $off);
		$pdf->Cell(40, 4, utf8_decode("Cono de seguridad"), 0, "", "L");
		if ($ordenTrabajo->cono_seg == '1') {
			$pdf->SetXY(175, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(175, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->cono_seg == '0') {
			$pdf->SetXY(181, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(181, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(187, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_cono_seg, 1, 0, 'C', true);

		$off = $off + 5;

		$pdf->SetXY(15, $off);
		$pdf->Cell(40, 4, utf8_decode("Espejos de capot"), 0, "", "L");
		$pdf->SetXY(55, $off);
		$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		$pdf->SetXY(61, $off);
		$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		if ($ordenTrabajo->esp_cap == '1') {
			$pdf->SetXY(55, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(55, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->esp_cap == '0') {
			$pdf->SetXY(61, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(61, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(67, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_esp_cap, 1, 0, 'C', true);

		$pdf->SetXY(75, $off);
		$pdf->Cell(40, 4, utf8_decode("Tapa de TQ de plumillas"), 0, "", "L");
		if ($ordenTrabajo->tapa_tq_plu == '1') {
			$pdf->SetXY(115, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(115, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->tapa_tq_plu == '0') {
			$pdf->SetXY(121, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(121, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(127, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_tapa_tq_plu, 1, 0, 'C', true);

		$pdf->SetXY(135, $off);
		$pdf->Cell(40, 4, utf8_decode("Extintor"), 0, "", "L");
		if ($ordenTrabajo->extintor == '1') {
			$pdf->SetXY(175, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(175, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->extintor == '0') {
			$pdf->SetXY(181, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(181, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(187, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_extintor, 1, 0, 'C', true);

		$off = $off + 5;

		$pdf->SetXY(15, $off);
		$pdf->Cell(40, 4, utf8_decode("Espejos de puertas"), 0, "", "L");
		if ($ordenTrabajo->esp_pue == '1') {
			$pdf->SetXY(55, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(55, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->esp_pue == '0') {
			$pdf->SetXY(61, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(61, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(67, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_esp_pue, 1, 0, 'C', true);

		$pdf->SetXY(75, $off);
		$pdf->Cell(40, 4, utf8_decode("Varilla medidora"), 0, "", "L");
		if ($ordenTrabajo->var_med == '1') {
			$pdf->SetXY(115, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(115, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->var_med == '0') {
			$pdf->SetXY(121, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(121, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(127, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_var_med, 1, 0, 'C', true);

		$pdf->SetXY(135, $off);
		$pdf->Cell(40, 4, utf8_decode("Gata y palanca"), 0, "", "L");
		if ($ordenTrabajo->gata_pal == '1') {
			$pdf->SetXY(175, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(175, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->gata_pal == '0') {
			$pdf->SetXY(181, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(181, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(187, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_gata_pal, 1, 0, 'C', true);

		$off = $off + 5;

		$pdf->SetXY(15, $off);
		$pdf->Cell(40, 4, utf8_decode("Ext. de parachoque ext."), 0, "", "L");
		if ($ordenTrabajo->para_ext == '1') {
			$pdf->SetXY(55, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(55, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->para_ext == '0') {
			$pdf->SetXY(61, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(61, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(67, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_para_ext, 1, 0, 'C', true);

		$pdf->SetXY(75, $off);
		$pdf->Cell(40, 4, utf8_decode("Tapa de llenado aceite"), 0, "", "L");
		if ($ordenTrabajo->llen_acei == '1') {
			$pdf->SetXY(115, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(115, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->llen_acei == '0') {
			$pdf->SetXY(121, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(121, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(127, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_llen_acei, 1, 0, 'C', true);

		$pdf->SetXY(135, $off);
		$pdf->Cell(40, 4, utf8_decode("Llave de Rueda"), 0, "", "L");
		if ($ordenTrabajo->llav_rue == '1') {
			$pdf->SetXY(175, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(175, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->llav_rue == '0') {
			$pdf->SetXY(181, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(181, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(187, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_llav_rue, 1, 0, 'C', true);

		$off = $off + 5;

		$pdf->SetXY(15, $off);
		$pdf->Cell(40, 4, utf8_decode("Ext. de parachoque int."), 0, "", "L");
		if ($ordenTrabajo->para_inf == '1') {
			$pdf->SetXY(55, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(55, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->para_inf == '0') {
			$pdf->SetXY(61, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(61, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(67, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_para_inf, 1, 0, 'C', true);

		$pdf->SetXY(75, $off);
		$pdf->Cell(40, 4, utf8_decode("Varilla de aceite hidráulico"), 0, "", "L");
		if ($ordenTrabajo->vari_acei_hidr == '1') {
			$pdf->SetXY(115, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(115, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->vari_acei_hidr == '0') {
			$pdf->SetXY(121, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(121, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(127, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_vari_acei_hidr, 1, 0, 'C', true);

		$pdf->SetXY(135, $off);
		$pdf->Cell(40, 4, utf8_decode("Aro y llanta de repuesto"), 0, "", "L");
		if ($ordenTrabajo->aro_llan_rep == '1') {
			$pdf->SetXY(175, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(175, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->aro_llan_rep == '0') {
			$pdf->SetXY(181, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(181, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(187, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_aro_llan_rep, 1, 0, 'C', true);

		$off = $off + 5;

		$pdf->SetXY(15, $off);
		$pdf->Cell(40, 4, utf8_decode("Faro pirata"), 0, "", "L");
		if ($ordenTrabajo->faro_pir == '1') {
			$pdf->SetXY(55, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(55, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->faro_pir == '0') {
			$pdf->SetXY(61, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(61, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(67, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_faro_pir, 1, 0, 'C', true);

		$pdf->SetXY(75, $off);
		$pdf->Cell(40, 4, utf8_decode("Bandeja posa vaso"), 0, "", "L");
		if ($ordenTrabajo->band_vas == '1') {
			$pdf->SetXY(115, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(115, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->band_vas == '0') {
			$pdf->SetXY(121, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(121, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(127, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_band_vas, 1, 0, 'C', true);

		$pdf->SetXY(135, $off);
		$pdf->Cell(40, 4, utf8_decode("Triángulo de seguridad"), 0, "", "L");
		if ($ordenTrabajo->tri_seg == '1') {
			$pdf->SetXY(175, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(175, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->tri_seg == '0') {
			$pdf->SetXY(181, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(181, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(187, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_tri_seg, 1, 0, 'C', true);

		$off = $off + 5;

		$pdf->SetXY(15, $off);
		$pdf->Cell(40, 4, utf8_decode("Faro neblinero"), 0, "", "L");
		if ($ordenTrabajo->faro_neb == '1') {
			$pdf->SetXY(55, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(55, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->faro_neb == '0') {
			$pdf->SetXY(61, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(61, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(67, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_faro_neb, 1, 0, 'C', true);

		$pdf->SetXY(75, $off);
		$pdf->Cell(40, 4, utf8_decode("Cenicero"), 0, "", "L");
		if ($ordenTrabajo->cenicero == '1') {
			$pdf->SetXY(115, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(115, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->cenicero == '0') {
			$pdf->SetXY(121, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(121, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(127, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_cenicero, 1, 0, 'C', true);

		$pdf->SetXY(135, $off);
		$pdf->Cell(40, 4, utf8_decode("Comando de carrocero"), 0, "", "L");
		$pdf->SetXY(175, $off);
		$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		$pdf->SetXY(181, $off);
		$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		if ($ordenTrabajo->coma_carr == '1') {
			$pdf->SetXY(175, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(175, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}

		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		if ($ordenTrabajo->coma_carr == '0') {
			$pdf->SetXY(181, $off);
			$pdf->SetFillColor(4, 4, 4);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		} else {
			$pdf->SetXY(181, $off);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(4, 4, 4);
			$pdf->Cell(5, 4, '', 1, 0, 'C', true);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(4, 4, 4);

		$pdf->SetXY(187, $off);
		$pdf->Cell(7, 4, $ordenTrabajo->cant_coma_carr, 1, 0, 'C', true);

		$pdf->Rect(10, 265, $cellBox, 10);
		$pdf->SetXY(10, 260);
		$pdf->SetFont('Arial', '', 7);
		$pdf->Cell(20, 5, utf8_decode("Observaciones:"), 0, "", "L");
		$pdf->SetXY(10, 266);
		$pdf->MultiCell(190, 3, utf8_decode($ordenTrabajo->observacion), 0, 'L', 0);

		$pdf->SetFont('Arial', '', 8);
		$pdf->SetXY(10, 279);
		$pdf->Cell(0, 5, utf8_decode("----------------------------------------------"), 0, "", "L");
		$pdf->SetXY(18, 281);
		$pdf->Cell(0, 5, utf8_decode("VºBº OPERACIONES"), 0, "", "L");

		$pdf->SetXY(58, 279);
		$pdf->Cell(0, 5, utf8_decode("----------------------------------------------"), 0, "", "L");
		$pdf->SetXY(68, 281);
		$pdf->Cell(0, 5, utf8_decode("VºBº SEGURIDAD"), 0, "", "L");

		$pdf->SetXY(106, 279);
		$pdf->Cell(0, 5, utf8_decode("----------------------------------------------"), 0, "", "L");
		$pdf->SetXY(110, 281);
		$pdf->Cell(0, 5, utf8_decode("CONDUCTOR SALIENTE"), 0, "", "L");

		$pdf->SetXY(154, 279);
		$pdf->Cell(0, 5, utf8_decode("----------------------------------------------"), 0, "", "L");
		$pdf->SetXY(160, 281);
		$pdf->Cell(0, 5, utf8_decode("CONDUCTOR ENTRANTE"), 0, "", "L");

		$pdfContent = $pdf->Output();

		return response($pdfContent);
	}
}
