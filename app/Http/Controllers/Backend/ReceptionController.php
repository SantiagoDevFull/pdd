<?php

namespace App\Http\Controllers\Backend;

use App\Article;
use App\Http\Controllers\Controller;
use App\ReceptionPlant;
use App\ClientRoute;
use App\PlantOrder;
use App\PlantOrderDetail;
use App\ReceptionPlantDetail;
use App\Vehicle;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReceptionController extends Controller
{
    public function index()
    {
        $date = CarbonImmutable::now()->startOfDay();
        $current_date = $date->startOfDay()->toAtomString();
        $max_datetime = $date->startOfDay()->addDays(2)->toAtomString();
        $client_routes = ClientRoute::select('id', 'name')->get();
        $vehicles = Vehicle::select('id', 'plate', 'transportist_id')->get();

        return view('backend.reception_register')->with(compact(
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
                'vehicles.plate as plate',
                DB::Raw("CONCAT(plant_orders.serie,'-',plant_orders.correlative) as pedido"),
                'plant_orders.traslate_date as traslate_date'
            )
            ->where('route_id', $route_id)
            ->where(function ($q) use ($route_id) {

                $q->where('plant_orders.state_id', 4)
                    ->orWhere(function ($q2) {
                        $q2->where('plant_orders.state_id', 5)
                            ->whereNull('plant_orders.guias');
                    });
            })
            ->get();

        return $elements;
    }

    public function getPlan()
    {
        $plant_order_id = request('plant_order_id');

        $exists = 0;
        $permiso = 0;
        $user_id = Auth::user()->id;

        if ($user_id == 65) //operario
        {
            $permiso = 1;
        }

        $reception = ReceptionPlant::where('plant_order_id', $plant_order_id)
            ->latest()
            ->first();

        if (!$reception) {
            return response()->json([
                'exists' => 1,
                'permiso' => $permiso,
                'msg' => 'No existe recepción registrada para este pedido'
            ], 200);
        }

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

        return response()->json([
            'exists'        => $exists,
            'permiso'        => $permiso,
            'reception_id'  => $reception->id,
            'data'          => $result
        ]);
    }

    public function validateForm()
    {
        $messages = [
            'route_id.required'       => 'Debe seleccionar la Ruta.',
            'plant_order_id.required' => 'Debe seleccionar el Pedido.',

            'planta.*.required'       => 'El campo Planta es obligatorio.',
            'ajenos.*.required'       => 'El campo Ajenos es obligatorio.',

            'rinde_gas.*.required'    => 'El campo Rinde Gas es obligatorio.',
            'premium_gas.*.required'  => 'El campo Premium Gas es obligatorio.',
            'el_gas.*.required'       => 'El campo EL Gas es obligatorio.',
            'plus_gas.*.required'     => 'El campo Plus Gas es obligatorio.',
            'maqui_gas.*.required'    => 'El campo Maqui Gas es obligatorio.',
            'cambios.*.required'      => 'El campo Cambios es obligatorio.',
            'rechazados.*.required'   => 'El campo Rechazados es obligatorio.',
            'total.*.required'        => 'El campo Total es obligatorio.',

            'prestamo.*.required'     => 'El campo Prestamo es obligatorio.',
            'devolucion.*.required'   => 'El campo Devolucion es obligatorio.',
            'venta.*.required'        => 'El campo Venta es obligatorio.',
        ];

        $rules = [
            'route_id'      => 'required',
            'plant_order_id' => 'required',

            'planta.*'      => 'required',
            'ajenos.*'      => 'required',

            'rinde_gas.*'   => 'required',
            'premium_gas.*' => 'required',
            'el_gas.*'      => 'required',
            'plus_gas.*'    => 'required',
            'maqui_gas.*'   => 'required',
            'cambios.*'   => 'required',
            'rechazados.*'  => 'required',
            'total.*'       => 'required',

            'prestamo.*'    => 'required',
            'devolucion.*'  => 'required',
            'venta.*'       => 'required',

        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function store()
    {
        $this->validateForm();

        $plant_order_id      = request('plant_order_id');
        $burros              = request('burros');
        $readonly_operario   = filter_var(request('readonly_operario'), FILTER_VALIDATE_BOOLEAN);
        $article_return = 0;
        $plant_order = PlantOrder::find($plant_order_id);

        $receptionIds = ReceptionPlant::where('plant_order_id', $plant_order_id)
            ->pluck('id');

        $reception_plant = new ReceptionPlant();

        if ($receptionIds->count() > 0) {
            ReceptionPlantDetail::whereIn('reception_plant_id', $receptionIds)->delete();
            $reception_plant = ReceptionPlant::where('plant_order_id', $plant_order_id)->first();
            $article_return = 1;
        }

        $reception_plant->plant_order_id = $plant_order_id;
        $reception_plant->burros = $burros;
        $reception_plant->created_at_user = Auth::user()->name;
        $reception_plant->save();

        $planta     = request('planta');
        $ajenos     = request('ajenos');
        $cambios    = request('cambios');
        $prestamo   = request('prestamo');
        $devolucion = request('devolucion');
        $venta      = request('venta');

        $marcas = [
            'rinde_gas'     => request('rinde_gas'),
            'premium_gas'   => request('premium_gas'),
            'el_gas'        => request('el_gas'),
            'plus_gas'      => request('plus_gas'),
            'maqui_gas'     => request('maqui_gas'),
            'rechazados'    => request('rechazados'),
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

        $articles_return = PlantOrderDetail::whereIn('article_id', [
            1,
            2,
            3,
            4,
            5,
            6,
            7,
            8,
            9,
            10,
            11,
            12,
            13,
            14,
            15,
            16,
            17,
            18,
            19,
            20
        ])
            ->where('plant_order_id', $plant_order_id)
            ->get()->keyBy('article_id');

        foreach ($marcas as $marca => $cantidades) {
            foreach ([5, 10, 15, 45] as $kg) {
                $quantity = $cantidades[$kg] ?? 0;

                $articleId = $articleMap[$marca][$kg];
                $article = Article::find($articleId);

                if ($articles_return->has($articleId) && $article_return == 0 && $plant_order->route_id != 20) {
                    $returnArticle = $articles_return[$articleId];
                    $returnArticle->return = $quantity;
                    $returnArticle->new_quantity = $returnArticle->new_quantity - $quantity;
                    $returnArticle->save();
                }

                $detail = new ReceptionPlantDetail();
                $detail->reception_plant_id = $reception_plant->id;
                $detail->article_id = $articleId;
                $detail->article_name = $article->name;
                $detail->quantity = $quantity;
                $detail->kg = $quantity * $kg;
                $detail->created_at_user = Auth::user()->name;

                if ($marca === 'rechazados') {
                    $detail->planta = $planta[$kg] ?? 0;
                    $detail->ajenos = $ajenos[$kg] ?? 0;
                    $detail->cambios    = $cambios[$kg] ?? 0;
                    $detail->rechazados  = $quantity;
                    $detail->prestamo   = $prestamo[$kg] ?? 0;
                    $detail->devolucion = $devolucion[$kg] ?? 0;
                    $detail->venta      = $venta[$kg] ?? 0;
                }

                $detail->save();
            }
        }

        $total = request('total');
        $reception_plant->total_5_kg  = $total[5];
        $reception_plant->total_10_kg = $total[10];
        $reception_plant->total_15_kg = $total[15];
        $reception_plant->total_45_kg = $total[45];
        $reception_plant->update();

        $plant_order->state_id = 5;

        if ($readonly_operario) {
            $plant_order->guias = 1;
        } else {
            $plant_order->operario = 1;
        }

        $plant_order->save();

        $type = 3;
        $title  = $plant_order->serie . '-' . $plant_order->correlative;
        $msg = 'El pedido ha sido recepcionado correctamente.';
        $url = route('dashboard.operations.reception_register');

        return response()->json([
            'type' => $type,
            'title' => $title,
            'msg' => $msg,
            'url' => $url
        ]);
    }
}
