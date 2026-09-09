<?php

namespace App\Http\Controllers\Backend;

use App\Area;
use App\Article;
use App\ArticleOrder;
use App\Company;
use App\Currency;
use App\Http\Controllers\Controller;
use App\Order;
use App\OrderDetail;
use App\OrderDetails;
use App\Provider;
use App\WarehouseType;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class ArticleOrderController extends Controller
{

    public function index()
    {
        $companies = Company::select('id', 'name')->get();
        $providers = Provider::select('id', 'business_name')->get();
        $currencies = Currency::select('id', 'name', 'symbol')->get();

        return view('backend.article_order')->with(compact('companies', 'providers', 'currencies'));
    }

    public function validateForm()
    {
        $messages = [
            'company_id.required'                            => 'Debe seleccionar una compañía.',
            'providers_id.required'                         => 'Debe seleccionar un proveedor.',
            'currency_id.required'                          => 'Debe seleccionar el Tipo de Moneda.',
            'tc.required'                                   => 'Debe digitar el Tipo de Cambio.',
            'tc.numeric'                                    => 'Debe digitar el Tipo de Cambio numérico.',
            'tc.min'                                        => 'Debe digitar el Tipo de Cambio mínimo uno.',
            'type.required'                                 => 'Debe seleccionar el Tipo de Orden.',
            'area_id.required'                              => 'Debe seleccionar el Área solicitante.',
            'reference_id.required'                         => 'Debe seleccionar el tipo de referencia.',
            'type_pay.required'                             => 'Debe escribir el Tipo de Pago.',
            'description.required'                          => 'Debe completar concepto tipo de referencia.'
        ];

        $rules = [
            'company_id'                        => 'required',
            'providers_id'                      => 'required',
            'currency_id'                       => 'required',
            'tc'                                => 'required|numeric|min:1',
            'type'                              => 'required',
            'area_id'                           => 'required',
            'reference_id'                      => 'required',
            'type_pay'                          => 'required',
            'description'                       => 'required'
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }


    public function validateModal()
    {
        $messages = [
            'article.required'                                => 'Debe seleccionar un artículo.',
            'quantity.required'                             => 'Debe digitar una cantidad.',
            'quantity.integer'                              => 'Debe digitar una cantidad entera.',
            'quantity.min'                                  => 'Debe digitar una cantidad mínima de cero.',
            'price_unit.required'                           => 'Debe digitar un precio.',
            'price_unit.numeric'                            => 'Debe digitar un precio real.',
            'price_unit.min'                                => 'Debe digitar un precio mínimo de 0.01',
        ];

        $rules = [
            'article'                            => 'required',
            'quantity'                          => 'required|integer|min:1',
            'price_unit'                        => 'required|numeric|min:0.01',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function list()
    {
        $this->validateForm();

        $company = request('company_id');
        $warehouse = request('providers_id');
        $currency_id = request('currency_id');
        $tc = request('tc');
        $typeOrder = request('type');
        $model = request()->all();

        $articles = Article::join('warehouse_types', 'warehouse_type_id', 'warehouse_types.id')
            ->leftjoin('units', 'articles.warehouse_unit_id', 'units.id')
            ->select(
                'articles.id as article_id',
                DB::Raw('CONCAT(articles.name," ::: ",units.name," (X",articles.package_warehouse,")") as article_name'),
                'warehouse_types.name as warehouse_types_name'
            )
            ->orderBy('warehouse_types.name', 'ASC')
            ->get();

        return response()->json([
            'model'     => $model,
            'articles'  =>  $articles,
        ]);
    }

    public function getArea()
    {
        $company_id = request('company_id');
        $areas = Area::select('id', 'name', 'short_name')
            ->where('company_id', $company_id)
            ->get();

        return $areas;
    }

    public function getArticulo()
    {
        $id = request('model.article_id');
        $quantity = request('model.quantity');
        $price_unit = request('model.price_unit');

        $article = Article::join('warehouse_types', 'warehouse_type_id', 'warehouse_types.id')
            ->select(
                'articles.id as article_id',
                'articles.name as article_name',
                'warehouse_types.name as warehouse_types_name'
            )
            ->findOrFail($id);
        $article->quantity = $quantity;
        $article->price_unit = $price_unit;

        return $article;
    }

    public function store()
    {

        $company_id = request('model.company_id');
        $providers_id = request('model.providers_id');
        $currency_id = request('model.currency_id');
        $tc = request('model.tc');
        $typeOrder = request('model.type');
        $area_id = request('model.area_id');
        $type_pay = request('model.type_pay');
        $reference_id = request('model.reference_id');
        $description = request('model.description');

        $articles = request('article_list');

        $subtotal = 0;
        foreach ($articles as $obj) {
            $subtotal = $subtotal + ($obj['quantity'] * $obj['price_unit']);
        }

        $let = ($typeOrder == "ORDEN DE COMPRA") ? "OC" : "OS";

        switch ($company_id) {
            case 1:
                $serie = "PDD-" . $let . "-";
                break;
            case 2:
                $serie = "C-" . $let . "-";
                break;
            case 3:
                $serie = "GS-" . $let . "-";
                break;
            case 4:
                $serie = "BL-" . $let . "-";
                break;
            case 5:
                $serie = "PM-" . $let . "-";
                break;
            default:
                $serie = "ERROR-" . $let . "-";
                break;
        }

        $dateActual = CarbonImmutable::now();
        $yearActual = CarbonImmutable::parse($dateActual)->format('Y');

        $order_correlative = Order::select('correlative')
            ->where('company_id', $company_id)
            ->whereYear('created_at', $yearActual)
            ->where('type', $typeOrder)
            ->max('correlative');

        $orders = new Order();
        $orders->company_id = $company_id;
        $orders->provider_id = $providers_id;
        $orders->currency_id = $currency_id;
        $orders->tc = $tc;
        $orders->type = $typeOrder;
        $orders->subtotal = $subtotal;
        $orders->igv = $subtotal * 0.18;
        $orders->import = $subtotal + $orders->igv;
        $orders->area_id = $area_id;
        $orders->type_pay = $type_pay;
        $orders->reference_id = $reference_id;
        $orders->created_at_user = Auth::user()->user;
        $orders->description = $description;
        $orders->save();
        $orders->order_date = $orders->created_at;
        $orders->serie = $serie . CarbonImmutable::parse($orders->created_at)->format('Y');
        $orders->correlative = $order_correlative ? $order_correlative + 1 : 1;
        $orders->update();

        foreach ($articles as $obj) {
            $detail = new OrderDetails();
            $detail->order_id = $orders->id;
            $detail->article_id = $obj['article_id'];
            $detail->quantity = $obj['quantity'];
            $detail->price_unit = $obj['price_unit'];
            $detail->value = $obj['quantity'] * $obj['price_unit'];
            $detail->converted = $detail->value * $tc;
            $detail->created_at_user = Auth::user()->user;
            $detail->save();
        }

        $type = 3;
        $title = "Bien";
        $msg = $typeOrder . " CREADO EXITOSAMENTE";
        $url = route('dashboard.logistics.index');

        return response()->json([
            'type'  => $type,
            'title' => $title,
            'msg'   => $msg,
            'url'   => $url
        ], 200);
    }
}
