<?php

namespace App\Http\Controllers\Backend;

use App\Article;
use App\Client;
use App\ClientMotor;
use App\Company;
use App\Cot;
use App\CotDetail;
use App\Currency;
use App\Http\Controllers\Controller;
use App\OrderWork;
use App\OrderWorkDetail;
use App\Rate;
use App\Service;
use App\Vehicle;
use App\WarehouseMovement;
use App\WarehouseMovementDetail;
use Illuminate\Support\Facades\Auth;

class CotizacionRegisterController extends Controller
{
    public function index()
    {

        $currencies = Currency::select('id', 'name')->get();
        if (Auth::user()->id == 47) {
            $companies = Company::select('id', 'name')->whereIn('id', [5])->get();
        } else {
            $companies = Company::select('id', 'name')->get();
        }

        $current_date = date(DATE_ATOM, mktime(0, 0, 0));
        return view('backend.cotizacion_register')->with(compact('currencies', 'companies', 'current_date'));
    }


    public function validateForm()
    {
        $messages = [
            'fac_date.required'                => 'Debe seleccionar Fecha de emisión.',
            'company_id.required'              => 'Debe seleccionar la Compañía.',
            'traslate_date.required'           => 'Debe seleccionar Fecha de vigencia.',
            'currency_id.required'             => 'Debe seleccionar Tipo de moneda.',
            'tc.required'                      => 'Debe digitar el Tipo de Cambio.',
            'tc.numeric'                       => 'Debe digitar el Tipo de Cambio numérico.',
            'tc.min'                           => 'Debe digitar el Tipo de Cambio mínimo uno.',
            'vehicle_id.required'              => 'Debe digitar Placa del Vehículo.'
        ];

        $rules = [
            'company_id'            => 'required',
            'fac_date'              => 'required',
            'traslate_date'         => 'required',
            'currency_id'           => 'required',
            'tc'                    => 'required|numeric|min:1',
            'vehicle_id'             => 'required'
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function getClients()
    {

        $q = request('q');

        $clients = Client::select('id', 'business_name')
            ->where('business_name', 'like', '%' . $q . '%')
            ->get();

        $clients->map(function ($item, $index) {
            $item->text = $item->business_name;
            unset($item->business_name);
            return $item;
        });

        return $clients;
    }

    public function list()
    {
        // $this->validateForm();

        $company_id = request('company_id');
        $fac_date = request('fac_date');
        $traslate_date = request('traslate_date');
        $currency_id = request('currency_id');
        $tc = request('tc');
        $vehicle_id = request('vehicle_id');

        $vehicle = Vehicle::findOrFail($vehicle_id);

        $order_work = OrderWork::where('vehicle_id', $vehicle_id)
            ->where('client_motor_id', $vehicle->client_motor_id)
            ->where('state', 1)
            ->first();

        $order_work_details = OrderWorkDetail::select(
            'article_id as id',
            'type as article_type',
            'name'
        )
            ->where('order_work_id', $order_work->id)
            ->get()
            ->map(function ($item, $index) {
                $item->item_number = $index + 1;

                if ($item->article_type == "Bienes") {
                    $item->cost = Article::where('id', $item->id)->value('last_price');
                    $item->price = Article::where('id', $item->id)->value('price');
                    $item->stock_good = Article::where('id', $item->id)->value('stock_good');
                } else if ($item->article_type == "Servicios") {
                    $item->cost = Service::where('id', $item->id)->value('cost');
                    $item->price = Service::where('id', $item->id)->value('price');
                    $item->stock_good = Service::where('id', $item->id)->value('stock_good');
                } else {
                    $item->cost = -1;
                    $item->price = -1;
                    $item->stock_good = '';
                }

                return $item;
            });

        $model = request()->all();

        return response()->json([
            'model'                    => $model,
            'order_work_details'       => $order_work_details,
        ]);
    }

    public function validateModalForm()
    {
        $messages = [
            'article_type.required_if'     => 'Debe seleccionar el Tipo de Repuesto.',
            'article_id.required_if'       => 'Debe seleccionar un Artículo.',
            'quantity.required'            => 'La Cantidad es obligatoria.',
            'quantity.integer'             => 'La Cantidad debe ser un número entero.',
            'quantity.min'                 => 'La Cantidad mínima es 1 .',
            'price.required'               => 'El Precio es obligatoria.',
            'price.numeric'                => 'El Precio debe ser un número real.',
            'price.min'                    => 'El Precio mínimo es 0.01 .',
            'percentage.required'          => 'El % del Descuento es obligatoria.',
            'percentage.numeric'           => 'El % del Descuento debe ser un número real.',
            'percentage.min'               => 'El % del Descuento mínimo es 0 .',
        ];

        $rules = [
            'article_type'      => 'required_if:item_number,=,""',
            'article_id'        => 'required_if:item_number,=,""',
            'quantity'          => 'required|integer|min:1',
            'price'             => 'required|numeric|min:0.01',
            'percentage'        => 'required|numeric|min:0',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function listArticles()
    {

        $q = request('q');
        $article_type = request('article_type');

        if ($article_type == 1) {
            $articles = Article::select('id', 'name', 'last_price', 'price', 'stock_good')
                ->where('name', 'like', '%' . $q . '%')
                ->where('warehouse_type_id', 16)
                ->get();
        } else {
            $articles = Service::select('id', 'name', 'cost as last_price', 'price', 'stock_good')
                ->where('name', 'like', '%' . $q . '%')
                ->get();
        }

        return $articles;
    }

    public function getArticle()
    {

        $article_type = request('model.article_type');
        $id = request('model.article_id');
        $cost = request('model.cost');
        $quantity = request('model.quantity');
        $price = request('model.price');
        $import = request('model.import');
        $percentage = request('model.percentage');
        $discount = request('model.discount');
        $total = request('model.total');
        $item_number = request('item_number');

        $item = Article::select('id', 'name', 'warehouse_type_id')
            ->where('id', '=', $id)
            ->first();

        if ($article_type == 1) {
            $item = Article::select('name', 'stock_good')
                ->where('id', '=', $id)
                ->first();
        } else {
            $item = Service::select('name', 'stock_good')
                ->where('id', '=', $id)
                ->first();
        }

        $item->id = $id;
        $item->cost = $cost;
        $item->price = $price;
        $item->quantity = $quantity;
        $item->import = $import;
        $item->percentage = $percentage;
        $item->discount = $discount;
        $item->total = $total;
        $item->article_type = $article_type == 1 ? "Bienes" : "Servicios";
        $item->item_number = ++$item_number;

        return $item;
    }

    public function getVehicles()
    {

        $q = request('q');
        $company_id = request('company_id');

        $vehicles = Vehicle::join('client_motors', 'client_motors.id', '=', 'vehicles.client_motor_id')
            ->select(
                'vehicles.id',
                'vehicles.plate',
                'client_motors.business_name'
            )
            ->where('vehicles.company_id', '=', $company_id)
            ->where('vehicles.state', '=', 2) //VENDIDO
            ->where('vehicles.plate', 'like', '%' . $q . '%')
            ->get();

        $vehicles->map(function ($item, $index) {
            $item->text = $item->plate . ' | ' . $item->business_name;
            unset($item->plate);
            return $item;
        });

        return $vehicles;
    }
    public function getClient()
    {
        $client = ClientMotor::select('id', 'business_name')
            ->where('vehicle_id', request('vehicle_id'))
            ->first();

        return response()->json([
            'client_id' => $client->id,
            'client_name' => $client->business_name
        ]);
    }

    public function store()
    {
        $company_id = request('model.company_id');
        $fac_date = request('model.fac_date');
        $traslate_date = request('model.traslate_date');
        // $client_id = request('model.client_id');
        $vehicle_id = request('model.vehicle_id');
        $currency_id = request('model.currency_id');
        $tc = request('model.tc');
        $articles = request('article_list');

        $vehicle = Vehicle::findOrFail($vehicle_id);
        $vehicle->state = 3; //cotizado
        $vehicle->save();

        $serie_number = Company::select('short_name')
            ->where('id', '=', $company_id)
            ->first();

        $correlative = Cot::select('correlative')
            ->where('company_id', '=', $company_id)
            ->max('correlative');

        $account = ClientMotor::join('client_address_motors', 'client_address_motors.client_motor_id', 'client_motors.id')
            ->select(
                'client_motors.id as client_id',
                'client_motors.business_name as client_name',
                'client_motors.document_number as client_document_number',
                'client_address_motors.address as client_address'
            )
            ->findOrFail($vehicle->client_motor_id);

        $order_work = OrderWork::where('vehicle_id', $vehicle_id)
            ->where('client_motor_id', $vehicle->client_motor_id)
            ->where('state', 1) //ot aprobado
            ->first();

        $cot = new Cot();
        $cot->order_work_id = $order_work->id;
        $cot->company_id = $company_id;
        $cot->serie = $serie_number->short_name;
        $cot->correlative = $correlative ? $correlative + 1 : 1;

        $cot->vehicle_id = $vehicle_id;

        $cot->client_id = $account->client_id;
        $cot->client_document_number = $account->client_document_number;
        $cot->client_name = $account->client_name;
        $cot->client_address = $account->client_address;

        $cot->issue_date = date('Y-m-d', strtotime($fac_date));
        $cot->expiry_date = date('Y-m-d', strtotime($traslate_date));
        $cot->currency_id = $currency_id;
        $cot->tc = $tc;

        $cot->status = 0; //generado
        $cot->user = Auth::user();
        $cot->save();

        foreach ($articles as $obj) {

            if ($obj['article_type'] == 'Bienes') {
                $item = Article::select('name')->findOrFail($obj['id']);
            } else {
                $item = Service::select('name')->findOrFail($obj['id']);
            }

            $detail = new CotDetail();
            $detail->cots_id = $cot->id;
            $detail->type = $obj['article_type'];
            $detail->article_id = $obj['id'];
            $detail->name = $item->name;
            $detail->cost = $obj['cost'];
            $detail->quantity = $obj['quantity'];
            $detail->price = $obj['price'];
            $detail->import = $obj['import'];
            $detail->desc_percentage = $obj['percentage'];
            $detail->desc = $obj['discount'];
            $detail->neto = $obj['total'];
            $detail->created_at = date('Y-m-d', strtotime($fac_date));;
            $detail->updated_at = null;

            $detail->save();
        }

        $rate = Rate::select('value')
            ->where('description', '=', 'IGV')
            ->first();

        $cot->subtotal = array_sum(array_column($articles, 'total'));
        $cot->igv_percentage = $rate->value;
        $cot->igv = $rate->value / 100 *  $cot->subtotal;
        $cot->total = $cot->subtotal + $cot->igv;
        $cot->created_at = date('Y-m-d', strtotime($fac_date));
        $cot->updated_at = null;
        $cot->user = Auth::user()->user;;
        $cot->save();
    }
}
