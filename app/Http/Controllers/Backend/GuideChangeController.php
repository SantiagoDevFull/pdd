<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\ClientRoute;
use App\GuideChange;
use App\ReceptionPlant;
use App\ReceptionPlantDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GuideChangeController extends Controller
{
    public function index()
    {
        $client_routes = ClientRoute::select('id', 'name')->get();

        return view('backend.guide_change')->with(compact('client_routes'));
    }

    public function getReceptionPlant()
    {
        $route_id = request('route_id');

        $elements = ReceptionPlant::leftjoin('plant_orders', 'plant_orders.id', '=', 'reception_plants.plant_order_id')
            ->leftjoin('vehicles', 'vehicles.id', '=', 'plant_orders.vehicle_id')
            ->select(
                'reception_plants.id as id',
                'reception_plants.plant_order_id as plant_order_id',
                'vehicles.plate as plate',
                'plant_orders.traslate_date as traslate_date',
                DB::Raw("CONCAT(plant_orders.serie,'-',plant_orders.correlative) as pedido")
            )
            ->where('plant_orders.route_id', $route_id)
            ->where(function ($q) {
                $q->where('reception_plants.state_cambio', '!=', 1)
                    ->orWhereNull('reception_plants.state_cambio');
            })
            ->get();

        return $elements;
    }

    public function getPlan()
    {
        $reception_plant_id = request('reception_plant_id');

        $exist = GuideChange::where('reception_plant_id', $reception_plant_id)->exists();

        if ($exist) {
            return response()->json([
                'exists' => false,
                'msg' => 'Ya se ha registrado los cambios del pedido',
            ], 200);
        }

        $details = ReceptionPlantDetail::where('reception_plant_id', $reception_plant_id)->get();

        $result = [
            'cambios'    => [5 => 0, 10 => 0, 15 => 0, 45 => 0],
        ];

        $articleMap = [
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
                $result['cambios'][$kg]    = $detail->cambios;
            }
        }

        return $result;
    }

    public function validateForm()
    {
        $messages = [
            'route_id.required'            => 'El campo Ruta es obligatorio.',
            'reception_plant_id.required'  => 'El campo Recepcion es obligatorio.',
            'a.required'                   => 'El campo A es obligatorio.',
            'b.required'                   => 'El campo B es obligatorio.',
            'c.required'                   => 'El campo C es obligatorio.',
            'd.required'                   => 'El campo D es obligatorio.',
            'e.required'                   => 'El campo E es obligatorio.',
            'f.required'                   => 'El campo F es obligatorio.',
            'g.required'                   => 'El campo G es obligatorio.',
            'repintado.required'           => 'El campo Repintado es obligatorio.',
            'obs.required'                 => 'El campo Observaciones es obligatorio.',
        ];

        $rules = [
            'route_id'                    => 'required',
            'reception_plant_id'          => 'required',
            'a'                           => 'required',
            'b'                           => 'required',
            'c'                           => 'required',
            'd'                           => 'required',
            'e'                           => 'required',
            'f'                           => 'required',
            'g'                           => 'required',
            'repintado'                   => 'required',
            'obs'                         => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }


    public function store()
    {
        $this->validateForm();

        $reception_plant_id = request('reception_plant_id');
        $a = request('a');
        $b = request('b');
        $c = request('c');
        $d = request('d');
        $e = request('e');
        $f = request('f');
        $g = request('g');
        $total = $a + $b + $c + $d + $e + $f + $g;
        $repintado = request('repintado');
        $obs = request('obs');

        $change = new GuideChange();
        $change->reception_plant_id = $reception_plant_id;
        $change->a = $a;
        $change->b = $b;
        $change->c = $c;
        $change->d = $d;
        $change->e = $e;
        $change->f = $f;
        $change->g = $g;
        $change->total = $total;
        $change->repintado = $repintado;
        $change->obs = $obs;
        $change->created_at_user = Auth::user()->name;
        $change->save();

        $reception = ReceptionPlant::find($reception_plant_id);
        $reception->state_cambio = 1;
        $reception->save();

        $type = 3;
        $title  = 'Bien';
        $msg = 'Los Cambios han sido registradas exitosamente.';
        $url = route('dashboard.operations.guide_change');

        return response()->json([
            'type' => $type,
            'title' => $title,
            'msg' => $msg,
            'url' => $url
        ]);
    }
}
