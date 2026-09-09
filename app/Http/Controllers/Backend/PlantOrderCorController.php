<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Employee;
use App\ClientRoute;
use App\PlantOrder;
use App\Vehicle;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PlantOrderCorController extends Controller
{
    public function index()
    {
        $date = CarbonImmutable::now()->startOfDay();
        $current_date = $date->startOfDay()->toAtomString();
        $max_datetime = $date->startOfDay()->addDays(2)->toAtomString();
        $client_routes = ClientRoute::WhereIn('company_id', [1, 2])->where('id', '!=', '20')->orderBy('name', 'asc')->get();

        $vehicles = Vehicle::leftjoin('companies', 'vehicles.company_id', '=', 'companies.id')
            ->select(
                'vehicles.id as id',
                'vehicles.plate as plate',
                'companies.short_name as company_name'
            )
            ->whereIn('vehicles.company_id', [1, 2, 6])
            ->orderBy('vehicles.plate', 'asc')
            ->get();


        return view('backend.plant_order_cor_register')->with(compact(
            'current_date',
            'max_datetime',
            'vehicles',
            'client_routes'
        ));
    }

    public function validateModalForm()
    {
        $messages = [
            'vehicle_id.required'    => 'Debe seleccionar el Vehículo.',
            'employee_id_2.required' => 'Debe seleccionar el Chofer.',
        ];

        $rules = [
            'vehicle_id'    => 'required',
            'employee_id_2' => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function update()
    {
        $this->validateModalForm();

        $id = request('id');
        $vehicle_id = request('vehicle_id');
        $employee_id = request('employee_id_2');

        $element = PlantOrder::findOrFail($id);
        $element->vehicle_id = $vehicle_id;
        $element->employee_id = $employee_id;
        $element->update();

        $type = 1;
        $title = $element->serie . '-' . $element->correlative;
        $msg = 'Pedido actualizado exitosamente.';

        return response()->json([
            'type'  => $type,
            'title' => $title,
            'msg'   => $msg
        ]);
    }

    public function getSelect2()
    {
        $employee_id = request('employee_id');
        $employee = '';

        if (isset($employee_id)) {
            $employee = Employee::select('id', 'first_name', 'last_name')
                ->where('id', $employee_id)
                ->first();

            $employee->text = $employee->first_name . ' ' . $employee->last_name;
            unset($employee->first_name);
            unset($employee->last_name);
        }


        return $employee;
    }

    public function detail()
    {
        $id = request('id');

        $element = PlantOrder::findOrFail($id);

        return $element;
    }

    public function list()
    {
        $elements = PlantOrder::leftjoin('client_routes', 'plant_orders.route_id', '=', 'client_routes.id')
            ->leftjoin('employees', 'plant_orders.employee_id', '=', 'employees.id')
            ->leftjoin('vehicles', 'plant_orders.vehicle_id', '=', 'vehicles.id')
            ->select(
                'plant_orders.id as id',
                'client_routes.name as route_name',
                'plant_orders.issue_date as issue_date',
                'plant_orders.traslate_date as traslate_date',
                DB::Raw("CONCAT(plant_orders.serie, '-', plant_orders.correlative) as pedido"),
                DB::Raw("CONCAT(employees.first_name, ' ', employees.last_name) as employee_name"),
                'vehicles.plate as vehicle_plate'
            )
            ->orderBy('plant_orders.id', 'desc')
            ->where('plant_orders.state_id', 0) // COORDINADOR
            ->get();

        return $elements;
    }

    public function getEmployees()
    {
        $q = request('q');

        $elements = Employee::select('id', 'first_name', 'last_name')
            ->where(function ($query) use ($q) {
                $query->where('first_name', 'like', '%' . $q . '%')
                    ->orWhere('last_name', 'like', '%' . $q . '%');
            })
            ->get();

        $elements->map(function ($item, $index) {
            $item->text = $item->first_name . ' ' . $item->last_name;
            unset($item->first_name);
            unset($item->last_name);

            return $item;
        });

        return $elements;
    }

    public function validateForm()
    {
        $messages = [
            'route_id.required'      => 'Debe seleccionar la Ruta.',
            'vehicle_id.required'   => 'Debe seleccionar el Vehículo.',
            'traslate_date.required' => 'Debe seleccionar la fecha de traslado.',
            'employee_id.required' => 'Debe seleccionar el Chofer.',
        ];

        $rules = [
            'route_id'      => 'required',
            'vehicle_id'   => 'required',
            'traslate_date' => 'required',
            'employee_id' => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function store()
    {
        $this->validateForm();

        $serie = 'P001';
        $movement_type_id = 11; // PREVENTA
        $route_id      = request('route_id');
        $issue_date    = Carbon::now()->format('Y-m-d');
        $traslate_date =  Carbon::parse(request('traslate_date'))->format('Y-m-d');
        $employee_id  = request('employee_id');
        $vehicle_id    = request('vehicle_id');
        $company_id = ClientRoute::find($route_id)->company_id;

        $plant_order = new PlantOrder();
        $plant_order->movement_type_id = $movement_type_id;
        $plant_order->company_id    = $company_id;
        $plant_order->route_id      = $route_id;
        $plant_order->issue_date    = $issue_date;
        $plant_order->employee_id  = $employee_id;
        $plant_order->vehicle_id    = $vehicle_id;
        $plant_order->created_at_user = Auth::user()->name;

        $movement_number = PlantOrder::select('movement_number')
            ->where('company_id', $company_id)
            ->max('movement_number');
        $movement_number = ($movement_number ? $movement_number + 1 : 1);

        $correlative = PlantOrder::where('serie', $serie)
            ->where('company_id', $company_id)
            ->max('correlative');

        $plant_order->movement_number = $movement_number;
        $plant_order->serie = $serie;
        $plant_order->correlative = ($correlative ? $correlative + 1 : 1);


        $plant_order->traslate_date = $traslate_date;
        $plant_order->state_id = 0;
        $plant_order->save();

        $type = 1;
        $title  = $plant_order->serie . '-' . $plant_order->correlative;
        $msg = 'El pedido ha sido registrado exitosamente.';

        return response()->json([
            'type' => $type,
            'title' => $title,
            'msg' => $msg
        ]);
    }
}
