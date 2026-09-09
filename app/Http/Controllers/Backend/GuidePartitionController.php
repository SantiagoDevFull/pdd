<?php

namespace App\Http\Controllers\Backend;

use App\Agent;
use App\Client;
use App\Http\Controllers\Controller;
use App\ClientRoute;
use App\Employee;
use App\Guide;
use App\GuidesDetail;
use App\PlantOrder;
use App\PlantOrderDetail;
use App\MoventType;
use App\Vehicle;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GuidePartitionController extends Controller
{
    public function index()
    {
        $date = CarbonImmutable::now()->startOfDay();
        $current_date = $date->startOfDay()->toAtomString();
        $max_datetime = $date->startOfDay()->addDays(2)->toAtomString();
        $client_routes = ClientRoute::select('id', 'name')->get();
        $vehicles = Vehicle::select('id', 'plate', 'transportist_id')->get();

        return view('backend.partition_guide_register')->with(compact(
            'current_date',
            'max_datetime',
            'client_routes',
            'vehicles'
        ));
    }

    public function getPlanOrder()
    {
        $route_id = request('route_id');

        $elements = PlantOrder::leftjoin('vehicles', 'vehicles.id', '=', 'plant_orders.vehicle_id')
            ->select(
                'plant_orders.id as id',
                DB::Raw("CONCAT(serie, '-',correlative) as pedido"),
                'vehicles.plate as plate',
                'plant_orders.traslate_date as traslate_date'
            )
            ->where('route_id', $route_id)->where('state_id', 1)->get();

        return $elements;
    }

    public function getPlan()
    {
        $plan_order_id = request('plant_order_id');

        $plantOrder = PlantOrder::leftjoin('vehicles', 'vehicles.id', '=', 'plant_orders.vehicle_id')
            ->leftjoin('client_routes', 'client_routes.id', '=', 'plant_orders.route_id')
            ->leftjoin('ubigeos', 'ubigeos.id', '=', 'client_routes.ubigeo_id')

            ->leftjoin('clients', 'clients.id', '=', 'plant_orders.client_id')
            ->leftJoin(DB::raw("
                (
                    SELECT ca1.*
                    FROM client_addresses ca1
                    WHERE ca1.address_type_id = 1
                    AND ca1.id = (
                        SELECT MIN(ca2.id)
                        FROM client_addresses ca2
                        WHERE ca2.client_id = ca1.client_id
                        AND ca2.address_type_id = 1
                    )
                ) as client_addresses
            "), function ($join) {
                $join->on('client_addresses.client_id', '=', 'clients.id');
            })
            ->leftjoin('ubigeos as ubigeos_client', 'ubigeos_client.id', '=', 'client_addresses.ubigeo_id')
            ->select(
                'plant_orders.route_id as route_id',
                'plant_orders.id as plant_order_id',
                'vehicles.capacity as capacity_camion',
                DB::raw('SUM(plant_orders.total_5_kg * 5 + plant_orders.total_10_kg * 10 + plant_orders.total_15_kg * 15 + plant_orders.total_45_kg * 45) as capacity_permitida'),

                DB::raw("
                CASE 
                    WHEN plant_orders.route_id = 20 THEN client_addresses.ubigeo_id
                    ELSE client_routes.ubigeo_id
                END as ubigeo_id
                "),
                DB::raw("
                CASE 
                    WHEN plant_orders.route_id = 20 THEN 
                        CONCAT(
                            ubigeos_client.district, ' - ',
                            ubigeos_client.province, ' - ',
                            ubigeos_client.department, ' - ',
                            ubigeos_client.country
                        )
                    ELSE 
                        CONCAT(
                            ubigeos.district, ' - ',
                            ubigeos.province, ' - ',
                            ubigeos.department, ' - ',
                            ubigeos.country
                        )
                END as ubigeo_name
                "),
                'plant_orders.total_5_kg as total_5_kg',
                'plant_orders.total_10_kg as total_10_kg',
                'plant_orders.total_15_kg as total_15_kg',
                'plant_orders.total_45_kg as total_45_kg'
            )
            ->find($plan_order_id);
        $elements = PlantOrderDetail::where('plant_order_id', $plan_order_id)->get();

        $result = [
            'rinde_gas' => [5 => 0, 10 => 0, 15 => 0, 45 => 0],
            'premium_gas' => [5 => 0, 10 => 0, 15 => 0, 45 => 0],
            'el_gas' => [5 => 0, 10 => 0, 15 => 0, 45 => 0],
            'plus_gas' => [5 => 0, 10 => 0, 15 => 0, 45 => 0],
            'maqui_gas' => [5 => 0, 10 => 0, 15 => 0, 45 => 0],
            'total' => [5 => 0, 10 => 0, 15 => 0, 45 => 0],
            'rechazados' => [5 => 0, 10 => 0, 15 => 0, 45 => 0],
        ];

        foreach ($elements as $element) {
            $name = strtoupper($element->article_name);

            if (str_contains($name, 'RINDE GAS')) {
                $brand = 'rinde_gas';
            } elseif (str_contains($name, 'PREMIUM GAS')) {
                $brand = 'premium_gas';
            } elseif (str_contains($name, 'EL GAS')) {
                $brand = 'el_gas';
            } elseif (str_contains($name, 'PLUS GAS')) {
                $brand = 'plus_gas';
            } elseif (str_contains($name, 'MAQUIGAS')) {
                $brand = 'maqui_gas';
            } elseif (str_contains($name, 'BALON')) {
                $brand = 'rechazados';
            } else {
                continue;
            }

            if (str_contains($name, '05 KG')) {
                $kg = 5;
            } elseif (str_contains($name, '10 KG')) {
                $kg = 10;
            } elseif (str_contains($name, '15 KG')) {
                $kg = 15;
            } elseif (str_contains($name, '45 KG')) {
                $kg = 45;
            } else {
                continue;
            }

            $result[$brand][$kg] = $element->quantity;
        }

        $result['total'][5]  = $plantOrder->total_5_kg ?? 0;
        $result['total'][10] = $plantOrder->total_10_kg ?? 0;
        $result['total'][15] = $plantOrder->total_15_kg ?? 0;
        $result['total'][45] = $plantOrder->total_45_kg ?? 0;

        $result = array_merge($result, $plantOrder->toArray());

        return $result;
    }

    public function validateForm()
    {
        $messages = [
            'route_id.required'       => 'El campo Ruta es obligatorio.',
            'plant_order_id.required' => 'El campo Pedido es obligatorio.',

            'activity.required'      => 'El campo Hidrocarburos es obligatorio.',
            'partition.required'     => 'El campo Particion es obligatorio.',
            'ubigeo_id.required'     => 'El campo Ubigeo es obligatorio.',
            'ubigeo_name.required'   => 'El campo Ubigeo es obligatorio.',
            'scop.required'          => 'El campo Scope es obligatorio.',
        ];

        $rules = [
            'route_id'       => 'required',
            'plant_order_id' => 'required',

            'activity'       => 'required',
            'partition'      => 'required',
            'ubigeo_id'      => 'required',
            'ubigeo_name'    => 'required',
            'scop'           => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function store()
    {
        $this->validateForm();

        $plant_order_id = request('plant_order_id');
        $movement_class_id = 2;
        $warehouse_type_id = 5;
        $referral_serie_number = 'TC41';
        $activity = request('activity') ? request('activity') : 'xxx-xxx-xxx';
        preg_match('/-(.*?)-/', $activity, $matches);
        $agent = Agent::where('activity', $matches[1] ?? null)->first();

        $ubigeo_id = request('ubigeo_id');
        $scop = request('scop');

        $order = PlantOrder::find($plant_order_id);
        $vehicle = Vehicle::find($order->vehicle_id);

        $partitions = json_decode(request('partitions'), true);

        $articleMap = [
            'rinde_gas' => [
                5  => 1,
                10 => 2,
                15 => 3,
                45 => 4,
            ],
            'premium_gas' => [
                5  => 5,
                10 => 6,
                15 => 7,
                45 => 8,
            ],
            'el_gas' => [
                5  => 13,
                10 => 14,
                15 => 15,
                45 => 16,
            ],
            'plus_gas' => [
                5  => 9,
                10 => 10,
                15 => 11,
                45 => 12,
            ],
            'maqui_gas' => [
                5  => 17,
                10 => 18,
                15 => 19,
                45 => 20,
            ],
        ];

        foreach ($partitions as $index => $partition) {

            //GUIDES
            $guide = new Guide();
            $guide->warehouse_document_type_id = 1;
            $guide->company_id = $order->company_id;
            $guide->warehouse_type_id = $warehouse_type_id;
            $guide->movement_class_id = $movement_class_id;
            $guide->scop_number = $scop;

            if ($order->movement_type_id == 12) {
                $guide->movement_type_id = 12;
                $guide->warehouse_account_type_id = 1;
                $client = Client::find($order->client_id);
                $guide->account_id = $client->id;
                $guide->account_document_number = $client->document_number;
                $guide->account_name = $client->business_name;
                $guide->employee_id = $order->employee_id;
            } else {
                $guide->movement_type_id = 11;
                $guide->warehouse_account_type_id = 3;
                $employee = Employee::find($order->employee_id);
                $guide->account_id = $employee->id;
                $guide->account_name = $employee->first_name . ' ' . $employee->last_name;
            }
            $guide->referral_guide_series = $referral_serie_number;

            $serie_guide_number = Guide::where('referral_guide_series', $referral_serie_number)
                ->where('company_id', $order->company_id)
                ->max('referral_guide_number');

            $guide->referral_guide_number = $serie_guide_number ? $serie_guide_number + 1 : 1;
            $movement_type = MoventType::find($guide->movement_type_id);
            $guide->action_type_id = $movement_type->action_type_id;
            $guide->state = 0;
            $guide->created_at_user = Auth::user()->name;
            $guide->traslate_date = $order->traslate_date;
            $guide->route_id = $order->route_id;
            $guide->vehicle_id = $vehicle->id;
            $guide->activity = $activity;
            $guide->agent = $agent ? $agent->name : 'No se encontró';
            $guide->created_at_user = Auth::user()->name;
            $guide->ubigeo_id = $ubigeo_id;
            $guide->plant_order_id = $order->id;
            $guide->save();

            $item_number_guide = 1;
            foreach (['rinde_gas', 'premium_gas', 'el_gas', 'plus_gas', 'maqui_gas'] as $marca) {
                foreach ([5, 10, 15, 45] as $kg) {
                    $quantity = isset($partition[$marca][$kg]) ? (int)$partition[$marca][$kg] : 0;

                    if ($quantity > 0) {
                        $guide_detail = new GuidesDetail();
                        $guide_detail->guides_id = $guide->id;
                        $guide_detail->item_number = $item_number_guide++;
                        $guide_detail->article_code = $articleMap[$marca][$kg];
                        $guide_detail->digit_amount = $quantity;
                        $guide_detail->converted_amount = $quantity;
                        $guide_detail->created_at_user = Auth::user()->name;
                        $guide_detail->save();
                    }
                }
            }


            //GUIA TRANSPORTISTA
            if (($order->company_id == 1 && $vehicle->company_id == 2) || ($order->company_id == 2 && $vehicle->company_id == 1)) {
                $guide_transportist = $guide->replicate();
                if ($order->company_id == 1 && $vehicle->company_id == 2) {
                    $guide_transportist->company_id = 2;
                } else if ($order->company_id == 2 && $vehicle->company_id == 1) {
                    $guide_transportist->company_id = 1;
                    $guide_transportist->warehouse_account_type_id = 1;
                    $guide_transportist->account_id = 1031;

                    $account = Client::find(1031);

                    $guide_transportist->account_document_number = $account->document_number;
                    $guide_transportist->account_name = $account->business_name;

                    $guide_transportist->employee_id = $guide->account_id;
                }

                $referral_serie_number_transport = 'V021';

                $guide_transportist->warehouse_document_type_id = 2; // GUIA DE TRANSPORTISTA
                $guide_transportist->movement_type_id = 6; // TRANSFERENCIA ENTRE ALMACENES

                $serie_guide_number = Guide::where('referral_guide_series', $referral_serie_number_transport)
                    ->where('company_id', $guide_transportist->company_id)
                    ->max('referral_guide_number');

                $guide_transportist->referral_guide_series = $referral_serie_number_transport;
                $guide_transportist->referral_guide_number = $serie_guide_number ? $serie_guide_number + 1 : 1;
                $guide_transportist->referral_warehouse_document_type_id = 1;
                $guide_transportist->referral_serie_number = $guide->referral_guide_series;
                $guide_transportist->referral_voucher_number = $guide->referral_guide_number;

                $movement_type = MoventType::find($guide_transportist->movement_type_id);
                $guide_transportist->action_type_id = $movement_type->action_type_id;
                $guide_transportist->created_at_user = Auth::user()->name;
                $guide_transportist->save();

                foreach (['rinde_gas', 'premium_gas', 'el_gas', 'plus_gas', 'maqui_gas'] as $marca) {
                    foreach ([5, 10, 15, 45] as $kg) {
                        $quantity = isset($partition[$marca][$kg]) ? (int)$partition[$marca][$kg] : 0;

                        if ($quantity > 0) {

                            $guide_detail_transportist = new GuidesDetail();
                            $guide_detail_transportist->guides_id = $guide_transportist->id;
                            $guide_detail_transportist->item_number = $item_number_guide++;
                            $guide_detail_transportist->article_code = $articleMap[$marca][$kg];
                            $guide_detail_transportist->digit_amount = $quantity;
                            $guide_detail_transportist->converted_amount = $quantity;
                            $guide_detail_transportist->created_at_user = Auth::user()->name;
                            $guide_detail_transportist->save();
                        }
                    }
                }
            }
        }

        $order->state_id = 2;
        $order->save();

        $type = 3;
        $title  = $order->serie . '-' . $order->correlative;
        $msg = 'Las particiones (Guias Electronicas) han sido registradas exitosamente.';
        $url = route('dashboard.operations.partition_guide_register');

        return response()->json([
            'type' => $type,
            'title' => $title,
            'msg' => $msg,
            'url' => $url
        ]);
    }
}
