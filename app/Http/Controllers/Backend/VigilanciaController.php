<?php

namespace App\Http\Controllers\Backend;

use App\Client;
use App\Http\Controllers\Controller;
use App\ClientRoute;
use App\Employee;
use App\MoventClass;
use App\PlantOrder;
use App\Security;
use App\Vehicle;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VigilanciaController extends Controller
{
    public function index()
    {
        $date = CarbonImmutable::now()->startOfDay();
        $current_date = $date->startOfDay()->toAtomString();
        $max_datetime = $date->startOfDay()->addDays(2)->toAtomString();
        $client_routes = ClientRoute::select('id', 'name')->get();
        $movement_classes = MoventClass::select('id', 'name')->get();
        $vehicles = Vehicle::select('id', 'plate', 'transportist_id')->get();

        return view('backend.vigilancia_register')->with(compact(
            'current_date',
            'max_datetime',
            'client_routes',
            'movement_classes',
            'vehicles'
        ));
    }

    public function getPlanOrder()
    {
        $route_id = request('route_id');

        $elements = PlantOrder::leftjoin('vehicles', 'vehicles.id', '=', 'plant_orders.vehicle_id')
            ->select(
                'plant_orders.id as id',
                'vehicles.plate as plate',
                DB::Raw("CONCAT(plant_orders.serie,'-',plant_orders.correlative) as pedido"),
                'plant_orders.traslate_date as traslate_date'
            )
            ->where('route_id', $route_id)
            ->where(function ($q) use ($route_id) {

                if ($route_id == 20) {
                    $q->where('state_id', 7);
                } else {
                    $q->whereIn('state_id', [2, 3]);
                }
            })
            ->get();

        return $elements;
    }

    public function getMovementClass()
    {

        $plant_order_id = request('plant_order_id');

        $plant_order = PlantOrder::find($plant_order_id);

        $movement_class_id = '';

        if ($plant_order->state_id == 2) {
            $movement_class_id = 2;
        } else if ($plant_order->state_id == 3) {
            $movement_class_id = 1;
        } else {
            $movement_class_id = '';
        }

        return $movement_class_id;
    }

    public function getClients()
    {
        $route_id = request('route_id');
        $q = request('q');

        $elements = Client::select('id', 'business_name')
            ->where('route_id', $route_id)
            ->where('business_name', 'like', '%' . $q . '%')
            ->get();

        $elements->map(function ($item, $index) {
            $item->text = $item->business_name;
            unset($item->business_name);

            return $item;
        });

        return $elements;
    }

    public function getEmployees()
    {
        $route_id = request('route_id');
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
            'route_id.required'               => 'Debe seleccionar la Ruta.',
            'plant_order_id.required_unless'  => 'Debe seleccionar la Orden de Trabajo.',
            'movement_class_id.required'      => 'Debe seleccionar el Tipo de Movimiento.',

            'client_id.required_if'           => 'Debe seleccionar el Cliente.',
            'employee_id.required_if'         => 'Debe seleccionar el Chofer.',
            'vehicle_id.required_if'          => 'Debe seleccionar el Vehículo.',
            'traslate_date.required_if'       => 'Debe seleccionar la fecha de traslado.',

            'total.*.required'                => 'El campo Total es obligatorio.',
            'rechazados.*.required'           => 'El campo Rechazados es obligatorio.',
        ];

        $rules = [
            'route_id'               => 'required',
            'plant_order_id'         => 'required_unless:route_id,20',
            'movement_class_id'      => 'required',

            'client_id'              => 'required_if:plant_order_id,,',
            'employee_id'            => 'required_if:plant_order_id,,',
            'vehicle_id'             => 'required_if:plant_order_id,,',
            'traslate_date'          => 'required_if:plant_order_id,,',

            'total.*'                => 'required',
            'rechazados.*'           => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }


    public function store()
    {
        $this->validateForm();

        $route_id = request('route_id');
        $movement_class_id      = request('movement_class_id');
        $plant_order_id         = request('plant_order_id');
        $client_id = request('client_id');
        $employee_id = request('employee_id');
        $vehicle_id = request('vehicle_id');
        $traslate_date =  Carbon::parse(request('traslate_date'))->format('Y-m-d');
        $title = '';

        $element = new Security();

        if ($route_id == 20) {

            if (isset($plant_order_id)) {
                $plant_order = PlantOrder::find($plant_order_id);
                $plant_order->state_id = 3;
                $plant_order->save();

                $element->plant_order_id = $plant_order->id;
                $title  = $plant_order->serie . '-' . $plant_order->correlative;
                $msg = 'Registro creado exitosamente.';
            } else {

                $company_id = ClientRoute::find($route_id)->company_id;
                $serie = 'P001';

                $plant_order = new PlantOrder();
                $plant_order->movement_type_id = 12;
                $plant_order->company_id = $company_id;
                $plant_order->route_id = $route_id;
                $plant_order->issue_date = Carbon::now()->format('Y-m-d');
                $plant_order->client_id = $client_id;
                $plant_order->employee_id = $employee_id;
                $plant_order->vehicle_id = $vehicle_id;
                $plant_order->traslate_date = $traslate_date;
                $plant_order->state_id = 4;
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

                $plant_order->save();

                $element->plant_order_id = $plant_order->id;
                $title  = $plant_order->serie . '-' . $plant_order->correlative;
                $msg = 'Nuevo Pedido creado exitosamente.';
            }
        } else {

            $order = PlantOrder::find($plant_order_id);

            if ($movement_class_id == 1) { // INGRESO
                $order->state_id = 4;
            } else if ($movement_class_id == 2) { // SALIDA
                $order->state_id = 3;
            }

            $order->save();
            $element->plant_order_id = $plant_order_id;

            $plant_order = PlantOrder::find($plant_order_id);
            $title  = $plant_order->serie . '-' . $plant_order->correlative;
            $msg = 'Registro creado exitosamente.';
        }

        $element->movement_class_id = $movement_class_id;

        $total = request('total');
        $rechazados = request('rechazados');

        $element->total_5_kg  = $total[5];
        $element->total_10_kg = $total[10];
        $element->total_15_kg = $total[15];
        $element->total_45_kg = $total[45];

        $element->rechazado_5_kg  = $rechazados[5];
        $element->rechazado_10_kg = $rechazados[10];
        $element->rechazado_15_kg = $rechazados[15];
        $element->rechazado_45_kg = $rechazados[45];

        $element->created_at_user = Auth::user()->name;
        $element->save();

        $type = 3;
        $url = route('dashboard.operations.vigilancia_register');

        return response()->json([
            'type' => $type,
            'title' => $title,
            'msg' => $msg,
            'url' => $url
        ]);
    }
}
