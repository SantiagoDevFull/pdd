<?php

namespace App\Http\Controllers\Backend;

use App\BodyType;
use App\Brand;
use App\Company;
use App\Http\Controllers\Controller;
use App\Exports\VehicleReportExport;
use App\FuelType;
use App\Modelo;
use App\Vehicle;
use Carbon\CarbonImmutable;

use Maatwebsite\Excel\Facades\Excel;

class VehicleController extends Controller
{

    public function index()
    {
        $companies = Company::select('id', 'name')->get();
        $modelos = Modelo::select('id', 'description')->get();
        $brands = Brand::select('id', 'description')->get();
        $fuel_types = FuelType::select('id', 'description')->get();
        $body_types = BodyType::select('id', 'description')->get();

        return view('backend.vehicles')->with(compact('companies', 'modelos', 'brands', 'fuel_types', 'body_types'));
    }

    public function validateForm()
    {
        $messages = [
            'company_id.required'        => 'Debe seleccionar una Compañia.',
            'plate.required'             => 'Debe completar la Placa.',
            'capacity.required'          => 'Debe completar la Capacidad.',
            //'license.required'           => 'Debe completar la Licencia.',
            'transportist_id.required'   => 'Debe seleccionar al Transportista.',
            'chasis.required'            => 'Debe completar el Chasis.',
            'brand_id.required'          => 'Debe seleccionar la Marca.',
            'fuel_type_id.required'      => 'Debe seleccionar el Tipo de Combustible.',
            'body_type_id.required'      => 'Debe seleccionar el Tipo de Carrocería.',
            'modelo_id.required'         => 'Debe seleccionar el Modelo.',
            'color.required'             => 'Debe completar el Color.',
            'motor.required'             => 'Debe completar el Motor.',
            'nro_llantas.required'       => 'Debe completar el N° de Llantas.',
            'year_model.required'        => 'Debe completar el Año de Modelo.',
        ];

        $rules = [
            'company_id'      => 'required',
            'plate'           => 'required',
            'capacity'        => 'required',
            //'license'         => 'required',
            'transportist_id' => 'required',
            'chasis'          => 'required',
            'brand_id'        => 'required',
            'fuel_type_id'    => 'required',
            'body_type_id'    => 'required',
            'modelo_id'       => 'required',
            'modelo_id'       => 'required',
            'color'           => 'required',
            'motor'           => 'required',
            'nro_llantas'     => 'required',
            'year_model'      => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function list()
    {

        $vehicles = Vehicle::leftjoin('companies', 'companies.id', 'vehicles.company_id')
            ->leftjoin('companies as transportista', 'transportista.id', 'vehicles.transportist_id')
            ->leftjoin('brands', 'brands.id', 'vehicles.brand_id')
            ->leftjoin('fuel_types', 'fuel_types.id', 'vehicles.fuel_type_id')
            ->leftjoin('body_types', 'body_types.id', 'vehicles.body_type_id')
            ->leftjoin('modelos', 'modelos.id', 'vehicles.modelo_id')
            ->select(
                'vehicles.id as id',
                'companies.name as company_name',
                'vehicles.plate as plate',
                'vehicles.capacity as capacity',
                'vehicles.license as license',
                'transportista.name as transportist_name',
                'vehicles.chasis as chasis',
                'brands.description as marca',
                'fuel_types.description as tipo_combustible',
                'body_types.description as tipo_carroceria',
                'modelos.description as modelo',
                'vehicles.color as color',
                'vehicles.motor as motor',
                'vehicles.nro_llantas as nro_llantas',
                'vehicles.year_model as year_model'
            )
            ->get();

        return $vehicles;
    }

    public function update()
    {
        $this->validateModalForm();

        $id = request('id');
        $company_id = request('company_id');
        $plate = request('plate');
        $capacity = request('capacity');
        $license = request('license');
        $transportist_id = request('transportist_id');
        $chasis = request('chasis');
        $brand_id = request('brand_id');
        $fuel_type_id = request('fuel_type_id');
        $body_type_id = request('body_type_id');
        $modelo_id = request('modelo_id');
        $color = request('color');
        $motor = request('motor');
        $nro_llantas = request('nro_llantas');
        $year_model = request('year_model');

        $company = Company::select('name')->where('id', '=', $company_id)->first();
        $transportista = Company::select('document_number')->where('id', '=', $transportist_id)->first();

        $vehicle = Vehicle::findOrFail($id);
        $vehicle->company_id = $company_id;
        $vehicle->dgh = $company->name;
        $vehicle->plate = $plate;
        $vehicle->capacity = $capacity;
        $vehicle->license = $license;
        $vehicle->transportist_id = $transportist_id;
        $vehicle->document_number = $transportista->document_number;
        $vehicle->chasis = $chasis;
        $vehicle->brand_id = $brand_id;
        $vehicle->fuel_type_id = $fuel_type_id;
        $vehicle->body_type_id = $body_type_id;
        $vehicle->modelo_id = $modelo_id;
        $vehicle->color = $color;
        $vehicle->km = 0;
        $vehicle->motor = $motor;
        $vehicle->nro_llantas = $nro_llantas;
        $vehicle->year_model = $year_model;
        $vehicle->update();


        $type = 1;
        $title = '¡Ok!';
        $msg = 'Vehículo actualizado exitosamente.';

        return response()->json([
            'type'    => $type,
            'title'    => $title,
            'msg'    => $msg,
        ]);
    }

    public function exportExcel()
    {

        $vehicles = Vehicle::leftjoin('companies', 'companies.id', 'vehicles.company_id')
            ->leftjoin('companies as transportista', 'transportista.id', 'vehicles.transportist_id')
            ->leftjoin('brands', 'brands.id', 'vehicles.brand_id')
            ->leftjoin('fuel_types', 'fuel_types.id', 'vehicles.fuel_type_id')
            ->leftjoin('body_types', 'body_types.id', 'vehicles.body_type_id')
            ->leftjoin('modelos', 'modelos.id', 'vehicles.modelo_id')
            ->select(
                'vehicles.id as id',
                'companies.name as company_name',
                'vehicles.plate as plate',
                'vehicles.capacity as capacity',
                'vehicles.license as license',
                'transportista.name as transportist_name',
                'vehicles.chasis as chasis',
                'brands.description as marca',
                'fuel_types.description as tipo_combustible',
                'body_types.description as tipo_carroceria',
                'modelos.description as modelo',
                'vehicles.color as color',
                'vehicles.motor as motor',
                'vehicles.nro_llantas as nro_llantas',
                'vehicles.year_model as year_model'
            )
            ->get();

        $datetime = CarbonImmutable::now()->format('d/m/Y h:i:s a');

        $excel = new VehicleReportExport($datetime, $vehicles);
        return Excel::download($excel, 'reporte-vehiculos.xlsx');
    }

    public function detail()
    {

        $id = request('id');
        $vehicle = Vehicle::findOrFail($id);

        return $vehicle;
    }

    public function delete()
    {
        $id = request('id');
        $element = Vehicle::findOrFail($id);
        $element->delete();
    }

    public function validateModalForm()
    {
        $messages = [
            'company_id.required'        => 'Debe seleccionar una Compañia.',
            'plate.required'             => 'Debe completar la Placa.',
            'capacity.required'          => 'Debe completar la Capacidad.',
            //'license.required'           => 'Debe completar la Licencia.',
            'transportist_id.required'   => 'Debe seleccionar al Transportista.',
            'chasis.required'            => 'Debe completar el Chasis.',
            'brand_id.required'          => 'Debe seleccionar la Marca.',
            'fuel_type_id.required'      => 'Debe seleccionar el Tipo de Combustible.',
            'body_type_id.required'      => 'Debe seleccionar el Tipo de Carrocería.',
            'modelo_id.required'         => 'Debe seleccionar el Modelo.',
            'color.required'             => 'Debe completar el Color.',
            'motor.required'             => 'Debe completar el Motor.',
            'nro_llantas.required'       => 'Debe completar el N° de Llantas.',
            'year_model.required'        => 'Debe completar el Año de Modelo.',
        ];

        $rules = [
            'company_id'      => 'required',
            'plate'           => 'required',
            'capacity'        => 'required',
            //'license'         => 'required',
            'transportist_id' => 'required',
            'chasis'          => 'required',
            'brand_id'        => 'required',
            'fuel_type_id'    => 'required',
            'body_type_id'    => 'required',
            'modelo_id'       => 'required',
            'modelo_id'       => 'required',
            'color'           => 'required',
            'motor'           => 'required',
            'nro_llantas'     => 'required',
            'year_model'      => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function store()
    {

        $this->validateForm();

        $company_id = request('company_id');
        $plate = request('plate');
        $capacity = request('capacity');
        $license = request('license');
        $transportist_id = request('transportist_id');
        $chasis = request('chasis');
        $brand_id = request('brand_id');
        $fuel_type_id = request('fuel_type_id');
        $body_type_id = request('body_type_id');
        $modelo_id = request('modelo_id');
        $color = request('color');
        $motor = request('motor');
        $nro_llantas = request('nro_llantas');
        $year_model = request('year_model');

        $company = Company::select('name')->where('id', '=', $company_id)->first();
        $transportista = Company::select('document_number')->where('id', '=', $transportist_id)->first();

        $vehicle = new Vehicle();
        $vehicle->company_id = $company_id;
        $vehicle->dgh = $company->name;
        $vehicle->plate = $plate;
        $vehicle->capacity = $capacity;
        $vehicle->license = $license;
        $vehicle->transportist_id = $transportist_id;
        $vehicle->document_number = $transportista->document_number;
        $vehicle->chasis = $chasis;
        $vehicle->brand_id = $brand_id;
        $vehicle->fuel_type_id = $fuel_type_id;
        $vehicle->body_type_id = $body_type_id;
        $vehicle->modelo_id = $modelo_id;
        $vehicle->color = $color;
        $vehicle->km = 0;
        $vehicle->motor = $motor;
        $vehicle->nro_llantas = $nro_llantas;
        $vehicle->year_model = $year_model;
        $vehicle->state = 0;
        $vehicle->save();

        $type = 1;
        $title = '¡Ok!';
        $msg = 'Vehículo creado exitosamente.';

        return response()->json([
            'type'    => $type,
            'title'    => $title,
            'msg'    => $msg,
        ]);
    }
}
