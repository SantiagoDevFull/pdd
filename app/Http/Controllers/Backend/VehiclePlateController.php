<?php

namespace App\Http\Controllers\Backend;

use App\Company;
use App\Exports\VehiclePlateReportExport;
use App\Http\Controllers\Controller;
use App\Vehicle;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class VehiclePlateController extends Controller
{

    public function index()
    {
        $companies = Company::select('id', 'name')->whereIn('id', [1, 6])->get();

        return view('backend.vehicles_plate')->with(compact('companies'));
    }

    public function validateForm()
    {
        $messages = [
            'company_id.required'        => 'Debe seleccionar una Compañia.',
            'plate.required'             => 'Debe completar la Placa.',
            'client_name.required_if'    => 'Debe completar el nombre del Cliente.',
            'capacity.required'          => 'Debe completar la Capacidad Maxima en KILOS.'
        ];

        $rules = [
            'company_id'      => 'required',
            'plate'           => 'required',
            'client_name'     => 'required_if:company_id,6',
            'capacity'        => 'required'
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function list()
    {

        $vehicles = Vehicle::leftjoin('companies', 'companies.id', 'vehicles.company_id')
            ->select(
                'vehicles.id as id',
                'companies.name as company_name',
                'vehicles.plate as plate',
                'vehicles.client_name as cliente',
                'vehicles.capacity as capacidad'
            )
            //->whereIn('vehicles.company_id', [1, 6])
            ->orderBy('vehicles.id', 'desc')
            ->get();

        return $vehicles;
    }

    public function update()
    {
        $this->validateModalForm();

        $id = request('id');
        $company_id = request('company_id');
        $plate = request('plate');
        $transportist_id = request('company_id');
        $client_name = request('client_name') ? request('client_name') : '';
        $capacity = request('capacity');

        $company = Company::select('name')->where('id', '=', $company_id)->first();
        $transportista = Company::select('document_number')->where('id', '=', $transportist_id)->first();

        $vehicle = Vehicle::findOrFail($id);
        $vehicle->company_id = $company_id;
        $vehicle->dgh = $company->name;
        $vehicle->plate = $plate;
        $vehicle->transportist_id = $transportist_id;
        $vehicle->document_number = $transportista->document_number;
        $vehicle->client_name = $client_name;
        $vehicle->capacity = $capacity;
        $vehicle->updated_at_user = Auth::user()->user;
        $vehicle->update();


        $type = 1;
        $title = '¡Ok!';
        $msg = 'Placa vehicular actualizado exitosamente.';

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
                'vehicles.client_name as cliente'
            )
            ->whereIn('vehicles.company_id', [1, 6])
            ->orderBy('vehicles.id', 'desc')
            ->get();

        $datetime = CarbonImmutable::now()->format('d/m/Y h:i:s a');

        $excel = new VehiclePlateReportExport($datetime, $vehicles);
        return Excel::download($excel, 'reporte-placa-vehiculos.xlsx');
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
        $element->deleted_at_user = Auth::user()->user;
        $element->delete();
    }

    public function validateModalForm()
    {
        $messages = [
            'company_id.required'        => 'Debe seleccionar una Compañia.',
            'plate.required'             => 'Debe completar la Placa.',
            'client_name.required_if'    => 'Debe completar el nombre del Cliente.',
            'capacity.required'          => 'Debe completar la Capacidad Maxima en KILOS.'
        ];

        $rules = [
            'company_id'      => 'required',
            'plate'           => 'required',
            'client_name'     => 'required_if:company_id,6',
            'capacity'        => 'required'
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
        $transportist_id = request('company_id');
        $client_name = request('client_name') ? request('client_name') : '';
        $capacity = request('capacity');

        $company = Company::select('name')->where('id', '=', $company_id)->first();
        $transportista = Company::select('document_number')->where('id', '=', $transportist_id)->first();

        $vehicle = new Vehicle();
        $vehicle->company_id = $company_id;
        $vehicle->dgh = $company->name;
        $vehicle->plate = $plate;
        $vehicle->transportist_id = $transportist_id;
        $vehicle->document_number = $transportista->document_number;
        $vehicle->client_name = $client_name;
        $vehicle->capacity = $capacity;
        $vehicle->created_at_user = Auth::user()->user;
        $vehicle->save();

        $type = 1;
        $title = '¡Ok!';
        $msg = 'Placa vehicular creado exitosamente.';

        return response()->json([
            'type'    => $type,
            'title'    => $title,
            'msg'    => $msg,
        ]);
    }
}
