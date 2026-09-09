<?php

namespace App\Http\Controllers\Backend;

use App\Article;
use App\Asesore;
use App\ClientMotor;
use App\Company;
use App\CompanySecurity;
use App\FactSerie;
use App\Http\Controllers\Controller;
use App\OrderWork;
use App\OrderWorkDetail;
use App\Service;
use App\Vehicle;
use Illuminate\Support\Facades\Auth;

class OrderWorkRegisterController extends Controller
{
    public function index()
    {
        if (Auth::user()->id == 47) {
            $companies = Company::select('id', 'name')->whereIn('id', [5])->get();
        } else {
            $companies = Company::select('id', 'name')->get();
        }
        $company_securities = CompanySecurity::select('id', 'name')->get();
        $asesores = Asesore::select('id', 'document_number', 'name')->get();
        $series = FactSerie::select('id', 'num_serie')->get();

        return view('backend.ot_register')->with(compact('company_securities', 'companies', 'asesores', 'series'));
    }

    public function validateForm()
    {
        $messages = [
            'company_id.required'                  => 'Debe seleccionar la Compañía.',
            //'security_id.required'                 => 'Debe seleccionar la Compañía de Seguro.',
            'asesor_id.required'                   => 'Debe seleccionar al Asesor.',
            'issue_date.required'                    => 'Debe seleccionar Fecha de Emisión.',
            'serie.required'                       => 'Debe seleccionar la Serie.',
            'correlative.required'                 => 'Este campo se autocompleta con la Serie.',
            'vehicle_id.required'                  => 'Debe seleccionar el Vehículo.',
            'km.required'                          => 'Debe digitar KM del vehículo.'
        ];

        $rules = [
            'company_id'                        => 'required',
            //'security_id'                       => 'required',
            'asesor_id'                         => 'required',
            'issue_date'                        => 'required',
            'serie'                             => 'required',
            'correlative'                       => 'required',
            'vehicle_id'                        => 'required',
            'km'                                => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function getSeries()
    {
        $company_id = request('company_id');

        $series = FactSerie::select('id', 'num_serie')
            ->where('company_id', $company_id)
            ->get();

        return $series;
    }

    public function getNextcorrelative()
    {

        $serie = FactSerie::findOrFail(request('serie'));

        $order_work = OrderWork::select('correlative')
            ->where('company_id', request('company'))
            ->where('serie', $serie->num_serie)
            ->max('correlative');

        $next_correlative = $order_work ? $order_work + 1 : 1;

        return response()->json($next_correlative, 200);
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
            ->where('vehicles.state', '=', 1) //ASIGNADO
            ->where('vehicles.plate', 'like', '%' . $q . '%')
            ->get();

        $vehicles->map(function ($item, $index) {
            $item->text = $item->plate.' | '.$item->business_name;
            unset($item->plate);
            return $item;
        });

        return $vehicles;
    }

    public function list()
    {
        $this->validateForm();

        $company_id = request('company_id');
        $security_id = request('security_id');
        $asesor_id = request('asesor_id');
        $issue_date = request('issue_date');
        $serie = request('serie');
        $correlative = request('correlative');
        $km = request('km');
        $vehicle_id = request('vehicle_id');
        $client_id = request('client_id');
        $client_name = request('client_name');
        $observation = request('observation');

        $model = request()->all();

        return response()->json([
            'model'                    => $model,
        ]);
    }

    public function validateModalForm()
    {
        $messages = [
            'article_type.required'        => 'Debe seleccionar el Tipo de Repuesto.',
            'article_id.required'        => 'Debe seleccionar un Artículo.',
        ];

        $rules = [
            'article_type'    => 'required',
            'article_id'    => 'required',
            //'quantity'        => 'required|integer|min:1',
            //'price'            => 'required|numeric|min:0.01',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function listArticles()
    {

        $q = request('q');
        $article_type = request('article_type');

        if ($article_type == 1) {
            $articles = Article::select('id', 'name')
                ->where('name', 'like', '%' . $q . '%')
                ->where('warehouse_type_id', 16) //ALMACEN PUNTO MOTOR
                ->get();
        } else {
            $articles = Service::select('id', 'name')
                ->where('name', 'like', '%' . $q . '%')
                ->get();
        }

        return $articles;
    }

    public function getArticle()
    {

        $article_type = request('model.article_type');
        $id = request('model.article_id');
        $item_number = request('item_number');

        $item = Article::select('id', 'name', 'warehouse_type_id')
            ->where('id', '=', $id)
            ->first();

        if ($article_type == 1) {
            $item = Article::select('name')
                ->where('id', '=', $id)
                ->first();
        } else {
            $item = Service::select('name')
                ->where('id', '=', $id)
                ->first();
        }

        $item->id = $id;
        $item->article_type = $article_type == 1 ? "Bienes" : "Servicios";
        $item->item_number = ++$item_number;

        return $item;
    }

    public function store()
    {
        $company_id = request('model.company_id');
        $security_id = request('model.security_id');
        $asesor_id = request('model.asesor_id');
        $issue_date = request('model.issue_date');
        $serie_id = request('model.serie');
        $correlative = request('model.correlative');
        $km = request('model.km');
        $vehicle_id = request('model.vehicle_id');

        $observation = request('model.observation');
        $articles = request('article_list');

        $serie = FactSerie::findOrFail($serie_id);
        $vehicle = Vehicle::findOrFail($vehicle_id);
        $cliente = ClientMotor::findOrFail($vehicle->client_motor_id);

        $order_work = new OrderWork();
        $order_work->company_id = $company_id;
        $order_work->asesor_id = $asesor_id;
        $order_work->vehicle_id = $vehicle_id;
        $order_work->security_id = $security_id;
        $order_work->client_motor_id = $cliente->id;
        $order_work->client_name = $cliente->business_name;
        $order_work->serie = $serie->num_serie;
        $order_work->correlative = $correlative;
        $order_work->km = $km;
        $order_work->observation = $observation;
        $order_work->issue_date = date('Y-m-d', strtotime($issue_date));
        $order_work->state = 0; //PENDIENTE
        $order_work->user = Auth::user()->user;
        $order_work->save();

        foreach ($articles as $obj) {

            if ($obj['article_type'] == 'Bienes') {
                $item = Article::select('name')->findOrFail($obj['id']);
            } else {
                $item = Service::select('name')->findOrFail($obj['id']);
            }

            $order_work_detail = new OrderWorkDetail();
            $order_work_detail->order_work_id = $order_work->id;
            $order_work_detail->type = $obj['article_type'];
            $order_work_detail->article_id = $obj['id'];
            $order_work_detail->name = $item->name;
            $order_work_detail->save();
        }
    }
}
