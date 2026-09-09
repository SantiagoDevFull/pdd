<?php

namespace App\Http\Controllers\Backend;

use App\ClientMotor;
use App\Exports\VehiclePmReportExport;
use App\Http\Controllers\Controller;
use App\Vehicle;
use Carbon\CarbonImmutable;
use Maatwebsite\Excel\Facades\Excel;

class AssignVehicleController extends Controller
{
    public function index()
    {
        return view('backend.assign_vehicle');
    }

    public function list()
    {

        $export = request('flag_export');

        $vehicles = Vehicle::leftjoin('companies', 'companies.id', 'vehicles.company_id')
            ->leftjoin('companies as transportista', 'transportista.id', 'vehicles.company_id')
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
                'vehicles.year_model as year_model',
                'vehicles.state as state'
            )
            ->where('vehicles.company_id', 5)
            ->get();

        if ($export) {
            $datetime = CarbonImmutable::now()->format('d/m/Y h:i:s a');

            $excel = new VehiclePmReportExport($datetime, $vehicles);
            return Excel::download($excel, 'reporte-vehiculos-punto-motor.xlsx');
        }

        return $vehicles;
    }

    public function delete()
    {
        $id = request('id');

        $element = Vehicle::findOrFail($id);
        $element->client_motor_id = '';
        $element->state = 0;
        $element->save();

        /*
        $client = ClientMotor::where('vehicle_id', $id)->first();
        $client->vehicle_id = '';
        $client->save();
        */
    }

    public function getClients()
    {

        $q = request('q');

        $clients = ClientMotor::leftJoin('vehicles', 'client_motors.id', '=', 'vehicles.client_motor_id')
            ->select(
                'client_motors.id',
                'client_motors.business_name'
            )
            ->when($q, function ($query, $q) {
                return $query->where('business_name', 'like', '%' . $q . '%')
                    ->where('vehicles.client_motor_id', '=', null);
            })
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

        /*
        $element = ClientMotor::findOrFail($client_id);
        $element->vehicle_id = $vehicle_id;
        $element->save();
        */

        $vehicle = Vehicle::findOrFail($vehicle_id);
        $vehicle->client_motor_id = $client_id;
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
        $vehicle_id = request('id');
        $vehicle = Vehicle::findOrFail($vehicle_id);

        $element = ClientMotor::findOrFail($vehicle->client_motor_id);

        return $element->business_name;
    }
}
