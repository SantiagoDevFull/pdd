<?php

namespace App\Http\Controllers\Backend;

use App\Article;
use App\Http\Controllers\Controller;
use App\ClientRoute;
use App\PlantOrder;
use App\PlantOrderDetail;
use App\ReceptionPlant;
use App\ReceptionPlantDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PlantOrderController extends Controller
{
    public function index()
    {
        $client_routes = ClientRoute::whereIn('company_id', [1, 2])->get();
        return view('backend.plant_order_register')->with(compact('client_routes'));
    }

    public function getPlanOrder()
    {
        $route_id = request('route_id');

        $elements = PlantOrder::leftjoin('vehicles', 'vehicles.id', '=', 'plant_orders.vehicle_id')
            ->select(
                'plant_orders.id as id',
                'vehicles.plate as plate',
                DB::raw("CONCAT(plant_orders.serie , '-', plant_orders.correlative) as pedido"),
                'plant_orders.traslate_date as traslate_date'
            )
            ->where('route_id', $route_id)
            ->where(function ($q) use ($route_id) {

                if ($route_id == 20) {
                    $q->where('state_id', 5);
                } else {
                    $q->where('state_id', 0);
                }
            })
            ->get();

        return $elements;
    }

    public function getPlan()
    {
        $plant_order_id = request('plant_order_id');

        $plant_order = PlantOrder::find($plant_order_id);
        $result = [];

        if ($plant_order->route_id == 20) {

            $reception = ReceptionPlant::where('plant_order_id', $plant_order_id)
                ->latest()
                ->first();
            $details = ReceptionPlantDetail::where('reception_plant_id', $reception->id)->get();

            $result = [
                'rinde_gas'   => [5 => 0, 10 => 0, 15 => 0, 45 => 0],
                'premium_gas' => [5 => 0, 10 => 0, 15 => 0, 45 => 0],
                'el_gas'      => [5 => 0, 10 => 0, 15 => 0, 45 => 0],
                'plus_gas'    => [5 => 0, 10 => 0, 15 => 0, 45 => 0],
                'maqui_gas'   => [5 => 0, 10 => 0, 15 => 0, 45 => 0],
                'rechazados'  => [5 => 0, 10 => 0, 15 => 0, 45 => 0],

                'planta'     => [5 => 0, 10 => 0, 15 => 0, 45 => 0],
                'ajenos'     => [5 => 0, 10 => 0, 15 => 0, 45 => 0],
                'cambios'    => [5 => 0, 10 => 0, 15 => 0, 45 => 0],
                'prestamo'   => [5 => 0, 10 => 0, 15 => 0, 45 => 0],
                'devolucion' => [5 => 0, 10 => 0, 15 => 0, 45 => 0],
                'venta'      => [5 => 0, 10 => 0, 15 => 0, 45 => 0],

                'total' => [
                    5  => $reception->total_5_kg,
                    10 => $reception->total_10_kg,
                    15 => $reception->total_15_kg,
                    45 => $reception->total_45_kg,
                ],
                'burros' => $reception->burros
            ];

            $articleMap = [
                1 => ['brand' => 'rinde_gas',   'kg' => 5],
                2 => ['brand' => 'rinde_gas',   'kg' => 10],
                3 => ['brand' => 'rinde_gas',   'kg' => 15],
                4 => ['brand' => 'rinde_gas',   'kg' => 45],

                5 => ['brand' => 'premium_gas', 'kg' => 5],
                6 => ['brand' => 'premium_gas', 'kg' => 10],
                7 => ['brand' => 'premium_gas', 'kg' => 15],
                8 => ['brand' => 'premium_gas', 'kg' => 45],

                9  => ['brand' => 'plus_gas', 'kg' => 5],
                10 => ['brand' => 'plus_gas', 'kg' => 10],
                11 => ['brand' => 'plus_gas', 'kg' => 15],
                12 => ['brand' => 'plus_gas', 'kg' => 45],

                13 => ['brand' => 'el_gas', 'kg' => 5],
                14 => ['brand' => 'el_gas', 'kg' => 10],
                15 => ['brand' => 'el_gas', 'kg' => 15],
                16 => ['brand' => 'el_gas', 'kg' => 45],

                17 => ['brand' => 'maqui_gas', 'kg' => 5],
                18 => ['brand' => 'maqui_gas', 'kg' => 10],
                19 => ['brand' => 'maqui_gas', 'kg' => 15],
                20 => ['brand' => 'maqui_gas', 'kg' => 45],

                25   => ['brand' => 'rechazados', 'kg' => 5],
                26   => ['brand' => 'rechazados', 'kg' => 10],
                1284 => ['brand' => 'rechazados', 'kg' => 15],
                1285 => ['brand' => 'rechazados', 'kg' => 45],
            ];

            foreach ($details as $detail) {

                if (!isset($articleMap[$detail->article_id])) {
                    continue;
                }

                $brand = $articleMap[$detail->article_id]['brand'];
                $kg    = $articleMap[$detail->article_id]['kg'];

                $result[$brand][$kg] = $detail->quantity;

                if ($brand === 'rechazados') {
                    $result['planta'][$kg]     = $detail->planta;
                    $result['ajenos'][$kg]     = $detail->ajenos;
                    $result['cambios'][$kg]    = $detail->cambios;
                    $result['prestamo'][$kg]   = $detail->prestamo;
                    $result['devolucion'][$kg] = $detail->devolucion;
                    $result['venta'][$kg]      = $detail->venta;
                }
            }
        }

        return response()->json([
            'obj'       => $plant_order,
            'result'    => $result
        ]);
    }

    public function validateForm()
    {
        $messages = [
            'route_id.required'      => 'Debe seleccionar la Ruta.',
            'plant_order_id.required' => 'Debe seleccionar la Orden de Trabajo.',

            'rinde_gas.*.required'    => 'El campo Rinde Gas es obligatorio.',
            'premium_gas.*.required'  => 'El campo Premium Gas es obligatorio.',
            'el_gas.*.required'       => 'El campo EL Gas es obligatorio.',
            'plus_gas.*.required'     => 'El campo Plus Gas es obligatorio.',
            'maqui_gas.*.required'    => 'El campo Maqui Gas es obligatorio.',
            'total.*.required'        => 'El campo Total es obligatorio.',
            'rechazados.*.required'   => 'El campo Rechazados es obligatorio.',
        ];

        $rules = [
            'route_id'      => 'required',
            'plant_order_id' => 'required',

            'rinde_gas.*'   => 'required',
            'premium_gas.*' => 'required',
            'el_gas.*'      => 'required',
            'plus_gas.*'    => 'required',
            'maqui_gas.*'   => 'required',
            'total.*'       => 'required',
            'rechazados.*'  => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function store()
    {
        $this->validateForm();

        $plant_order_id = request('plant_order_id');
        $plant_order = PlantOrder::find($plant_order_id);

        $marcas = [
            'rinde_gas'    => request('rinde_gas'),
            'premium_gas'  => request('premium_gas'),
            'el_gas'       => request('el_gas'),
            'plus_gas'     => request('plus_gas'),
            'maqui_gas'    => request('maqui_gas'),
            'rechazados'   => request('rechazados'),
        ];

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
            'rechazados' => [
                5  => 25,
                10 => 26,
                15 => 1284,
                45 => 1285,
            ],
        ];

        foreach ($marcas as $marca => $cantidades) {
            foreach ([5, 10, 15, 45] as $kg) {
                $quantity = $cantidades[$kg] ?? 0;

                if ($quantity > 0) {
                    $article = Article::find($articleMap[$marca][$kg]);

                    $detail = new PlantOrderDetail();
                    $detail->plant_order_id = $plant_order->id;
                    $detail->article_id = $articleMap[$marca][$kg];
                    $detail->article_name = $article->name;
                    $detail->quantity = $quantity;
                    $detail->return = 0;
                    $detail->new_quantity = $quantity;
                    $detail->kg = $quantity * $kg;
                    $detail->created_at_user = Auth::user()->name;
                    $detail->save();
                }
            }
        }

        $total = request('total');
        $plant_order->total_5_kg  = $total[5];
        $plant_order->total_10_kg = $total[10];
        $plant_order->total_15_kg = $total[15];
        $plant_order->total_45_kg = $total[45];
        $plant_order->state_id = 1;
        $plant_order->update();

        $type = 3;
        $title  = $plant_order->serie . '-' . $plant_order->correlative;
        $msg = 'El pedido ha sido registrado/actualizado exitosamente.';
        $url = route('dashboard.operations.plant_order_register');

        return response()->json([
            'type' => $type,
            'title' => $title,
            'msg' => $msg,
            'url' => $url
        ]);
    }
}
