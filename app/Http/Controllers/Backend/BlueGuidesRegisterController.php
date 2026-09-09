<?php

namespace App\Http\Controllers\Backend;

use App\Agent;
use App\Http\Controllers\Controller;
use App\Company;
use App\MoventClass;
use App\MoventType;
use App\WarehouseDocumentType;
use App\WarehouseType;
use App\Article;
use App\Currency;
use App\WarehouseAccountType;
use App\Client;
use App\Employee;
use App\MovementStockType;
use App\Provider;
use App\Rate;
use App\WarehouseMovement;
use App\WarehouseMovementDetail;
use App\ClientRoute;
use App\ElectSeries;
use App\Guide;
use App\GuidesDetail;
use App\GuidesSerie;
use App\Note;
use App\NoteDetail;
use App\PlantMovement;
use App\PlantMovementDetail;
use App\Ubigeo;
use App\Vehicle;
use Auth;
use Carbon\CarbonImmutable;

class BlueGuidesRegisterController extends Controller
{
    public function index()
    {

        $movement_classes = MoventClass::select('id', 'name')->get();
        $movement_types = MoventType::select('id', 'movent_class', 'name')->get();
        $movement_stock_types = MovementStockType::select('id', 'name')->get();
        $warehouse_types = WarehouseType::select('id', 'name')->get();
        $companies = Company::select('id', 'name')->whereIn('id',[4])->get();
        $currencies = Currency::select('id', 'name', 'symbol')->get();
        $current_date = date('d-m-Y');
        $date = CarbonImmutable::now()->startOfDay();
        $current_date = $date->startOfDay()->modify("-2 day")->toAtomString();
        //$current_date2 = $date->startOfDay()->modify("+1 day")->toAtomString();
        // $current_date2 = $date->startOfDay()->toAtomString();
        $min_datetime = $date->startOfDay()->toAtomString();

        $max_datetime = $date->startOfDay()->addDays(2)->toAtomString();
        $warehouse_account_types = WarehouseAccountType::select('id', 'name')->get();
        $warehouse_document_types = WarehouseDocumentType::select('id', 'name')->get();
        $client_routes = ClientRoute::select('id', 'name')->get();
        $vehicles = Vehicle::select('id', 'plate', 'transportist_id')->get();
        $guide_series = GuidesSerie::select('id', 'num_serie', 'correlative')->get();


        $igv = Rate::select('description', 'value')
            ->where('description', 'IGV')
            ->where('state', 1)
            ->first();

        return view('backend.blue_guides_register')->with(compact(
            'movement_classes',
            'movement_types',
            'movement_stock_types',
            'warehouse_types',
            'companies',
            'currencies',
            'current_date',
            'min_datetime',
            'max_datetime',
            'warehouse_account_types',
            'warehouse_document_types',
            'igv',
            'client_routes',
            'guide_series',
            'vehicles'
        ));
    }

    public function getUbigeos()
    {
        $q = request('q');

        $ubigeos = Ubigeo::select('id', 'district', 'province', 'department', 'country')
            ->where('district', 'like', '%' . $q . '%')
            ->orderBy('district', 'asc')
            ->get();

        $ubigeos->map(function ($item, $index) {
            $item->text = $item->district . ' - ' . $item->province . ' - ' . $item->department . ' - ' . $item->country;
            unset($item->district);
            unset($item->province);
            unset($item->department);
            unset($item->country);
            return $item;
        });

        return $ubigeos;
    }

    public function getEmployees()
    {
        $q = request('q');

        $employees = Employee::select('id', 'document_number', 'first_name', 'last_name')
            ->where('first_name', 'like', '%' . $q . '%')
            ->orWhere('last_name', 'like', '%' . $q . '%')
            ->orWhere('document_number', 'like', '%' . $q . '%')
            ->get();

        $employees->map(function ($item, $index) {
            $item->text = $item->first_name . ' ' . $item->last_name . ' - ' . $item->document_number;
            unset($item->first_name);
            unset($item->last_name);
            unset($item->document_number);
            return $item;
        });

        return $employees;
    }

    public function getElectSeries()
    {
        $company_id = request('company_id');
        $voucher_type_id = 13;

        $series = ElectSeries::select('id', 'voucher_type_id', 'serie', 'company_id')
            ->where('company_id', $company_id)
            ->where('voucher_type_id', $voucher_type_id)
            ->get();

        return $series;
    }


    public function getNextcorrelative()
    {
        $last_guide_serie = GuidesSerie::select('correlative')
            ->latest('id')
            ->where('company_id', request('company'))
            ->where('num_serie', request('guide_serie'))
            ->first();

        $next_correlative = $last_guide_serie ? $last_guide_serie->correlative + 1 : 0;

        return response()->json($next_correlative, 200);
        return response()->json($next_correlative, 200);
    }
    public function getAccounts()
    {
        $company_id = request('company_id');
        $warehouse_account_type_id = request('warehouse_account_type_id');
        $q = request('q');

        if ($warehouse_account_type_id == 1) {
            $clients = Client::select('id', 'business_name')
                ->where('company_id', $company_id)
                ->where('business_name', 'like', '%' . $q . '%')
                ->get();

            $clients->map(function ($item, $index) {
                $item->text = $item->business_name;
                unset($item->business_name);

                return $item;
            });
        } elseif ($warehouse_account_type_id == 2) {
            $clients = Provider::select('id', 'business_name')
                ->where('business_name', 'like', '%' . $q . '%')
                ->get();

            $clients->map(function ($item, $index) {
                $item->text = $item->business_name;
                unset($item->business_name);

                return $item;
            });
        } elseif ($warehouse_account_type_id == 3) {
            $clients = Employee::select('id', 'first_name', 'last_name')
                ->where(function ($query) use ($q) {
                    $query->where('first_name', 'like', '%' . $q . '%')
                        ->orWhere('last_name', 'like', '%' . $q . '%');
                })
                ->get();

            $clients->map(function ($item, $index) {
                $item->text = $item->first_name . ' ' . $item->last_name;
                unset($item->first_name);
                unset($item->last_name);

                return $item;
            });
        }

        return $clients;
    }

    public function validateForm()
    {
        $messages = [
            'movement_type_id.required'                            => 'Debe seleccionar el Tipo de Movimiento.',
            'warehouse_account_type_id.required'                => 'Debe seleccionar el Tipo.',
            //'company_id.required_if'							=> 'Debe seleccionar una Compañía.',
            'company_id.required'                                => 'Debe seleccionar una Compañía.',
            'since_date.required'                                => 'Debe seleccionar una Fecha.',
            //'referral_guide_series.required_if'			    => 'Debe digitar la Serie de Guía de Remisión.',
            //'referral_guide_number.required_if'				=> 'Debe digitar el Número de Guía de Remisión.',
            'referral_guide_series.required'                    => 'Debe seleccionar la Serie de Guía de Remisión.',
            'referral_guide_number.required'                    => 'Debe digitar el Número de Guía de Remisión.',
            //'license_plate.required'							=> 'Debe digitar el Número de Placa.',
            'traslate_date.required'                               => 'Debe seleccionar la fecha de traslado.',
            'route_id.required'                                 => 'Debe seleccionar la ruta.',
            //'employee_id.required_if'							=> 'Debe seleccionar al Chofer.',
            'warehouse_account_id.required'                        => 'Debe seleccionar la razón social.',
            'ubigeo_id.required'                                => 'Debe seleccionar el Ubigeo.',
            'vehicle_id.required'                                => 'Debe seleccionar el vehículo que ira en el Reporte Osinergmin.',
            'vehicle2_id.required'                                => 'Debe seleccionar el vehículo real para control interno.',
            'ose.required'                                        => 'Debe seleccionar el Tipo de guía.',
            'serie.required_if'                                    => 'Debe seleccionar la Serie Electrónico.',
            'referral_number.required_if'                        => 'Debe digitar el N° de Referencia',
            'activity.required_if'                                 => 'Debe digitar el N° de Referencia',
            'activity.required_if'                                    => 'Debe digitar el Reg. Hidrocarburos.',
        ];

        $rules = [
            'movement_type_id'                        => 'required',
            'warehouse_account_type_id'                => 'required',
            //'company_id'							=> 'required_if:movement_type_id,1,2,11,12,13,14,15,16,17,19,20,21,22',
            'company_id'                            => 'required',
            'since_date'                            => 'required',
            //'referral_guide_series'					=> 'required_if:movement_type_id,1,7,8,9,11,13,15,19,20',
            'referral_guide_series'                    => 'required',
            'referral_guide_number'                    => 'required',
            'scop_number'                            => 'required_if:movement_type_id,1,12,13,14,15,16',
            //'vehicle_id'							=> 'required_if:movement_type_id,11,12,13,14,19,20',
            //'license_plate'						=> 'required',
            'traslate_date'                            => 'required',
            'route_id'                                => 'required',
            //'employee_id'							=> 'required_if:ose,0',
            'warehouse_account_id'                    => 'required',
            'ubigeo_id'                                => 'required',
            'vehicle_id'                                => 'required',
            'vehicle2_id'                                => 'required',
            'ose'                                    => 'required',
            'serie'                                    => 'required_if:ose,0',
            'referral_number'                        => 'required_if:referral_guide_series,137',
            'activity'                                => 'required_if:ose,0',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function list()
    {
        $this->validateForm();

        $movement_class_id = 2;
        $movement_type_id = request('movement_type_id');
        $warehouse_type_id = 5;
        $company_id = request('company_id');
        $traslate_date = request('traslate_date');
        $since_date = request('since_date');
        $warehouse_account_type_id = request('warehouse_account_type_id');
        $warehouse_account_id = request('warehouse_account_id');
        $referral_guide_series = request('referral_guide_series');
        $referral_guide_number = request('referral_guide_number');
        $scop_number = request('scop_number');
        $vehicle_id = request('vehicle_id');
        $route_id = request('route_id');

        $model = request()->all();


        // Porcentage de Percepción del Cliente/Proveedor
        if ($warehouse_account_type_id == 1) {
            $perception_percentage = Client::select('perception_percentage_id')
                ->where('id', $warehouse_account_id)
                ->first();

            $perception_percentage = $perception_percentage->perception_percentage->value;
        } elseif ($warehouse_account_type_id == 2) {
            $perception_percentage = Provider::select('perception_agent_id')
                ->where('id', $warehouse_account_id)
                ->first();

            $perception_percentage = $perception_percentage->perception_agent->value;
        } else {
            $perception_percentage = 0;
        }

        // Obtener artículos
        $articles = Article::select(
            'id',
            'code',
            'name',
            'group_id',
            'package_sale',
            'sale_unit_id',
            'package_warehouse',
            'warehouse_unit_id',
            'igv',
            'perception',
            'stock_good',
            'stock_repair',
            'stock_return',
            'stock_damaged'
        )
            ->where('warehouse_type_id', 5)
            ->whereIn('group_id', [26, 27])
            ->orderBy('code', 'asc')
            ->get();

        $articles->map(function ($item, $index) {
            $item->sale_unit_id = $item->sale_unit['name'];
            $item->warehouse_unit_id = $item->warehouse_unit['name'];
        });



        return response()->json([
            'model'                    => $model,
            'perception_percentage' => $perception_percentage,

            'articles'                => $articles
        ]);
    }

    public function validateModalForm()
    {
        $messages = [
            'article_id.required'    => 'Debe seleccionar un Artículo.',
            'quantity.required'        => 'La Cantidad es obligatoria.',
        ];

        $rules = [
            'article_id'    => 'required',
            'quantity'        => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function getArticle()
    {
        // $this->validateModalForm();

        $article_id = request('model.article_id');
        $quantity = request('model.quantity');
        $price = request('model.price');
        $sale_value = request('model.sale_value');
        $inaccurate_value = request('model.inaccurate_value');
        $igv = request('model.igv');
        $total = request('model.total');
        $perception = request('model.perception');
        $igv_percentage = request('igv_percentage');
        $perception_percentage = request('perception_percentage');
        $item_number = request('item_number');
        $movement_type_id = request('movement_type_id');


        $article = Article::leftjoin('operation_types', 'operation_types.id', '=', 'articles.operation_type_id')
            ->where('articles.id', $article_id)
            ->select('articles.id', 'code', 'articles.name', 'package_sale', 'sale_unit_id', 'operation_type_id', 'factor', 'operation_types.name as operation_type_name')
            ->first();

        $article->item_number = ++$item_number;
        $article->sale_unit_id = $article->sale_unit->name;
        $article->digit_amount = number_format($quantity, 4, '.', ',');
        if ($movement_type_id == 1 || $movement_type_id == 2) {
            if ($article->operation_type_name == 'Suma') {
                $article->converted_amount = number_format($quantity + $article->factor, 4, '.', ',');
            } elseif ($article->operation_type_name == 'Resta') {
                $article->converted_amount = number_format($quantity - $article->factor, 4, '.', ',');
            } elseif ($article->operation_type_name == 'Multiplica') {
                $article->converted_amount = number_format($quantity * $article->factor, 4, '.', ',');
            } elseif ($article->operation_type_name == 'Divide') {
                $article->converted_amount = number_format($quantity / $article->factor, 4, '.', ',');
            }
        } else {
            $article->converted_amount = number_format($quantity, 4, '.', ',');
        }

        $article->price = number_format($price, 4, '.', ',');
        $article->sale_value = number_format($sale_value, 4, '.', ',');
        $article->inaccurate_value = number_format($inaccurate_value, 4, '.', ',');
        $article->igv = number_format($igv, 4, '.', ',');
        $article->total = number_format($total, 4, '.', ',');
        $article->perception = number_format($perception, 4, '.', ',');
        $article->igv_percentage = ($igv == 0 ? 0 : $igv_percentage);
        $article->perception_percentage = $perception_percentage;

        return $article;
    }

    public function store()
    {
        $movement_class_id = 2;
        $movement_type_id = request('model.movement_type_id');
        $warehouse_type_id = 5;
        $company_id = request('model.company_id');
        $since_date = request('model.since_date');
        $traslate_date = request('model.traslate_date');
        $fac_date = request('model.traslate_date');
        $warehouse_account_type_id = request('model.warehouse_account_type_id');
        $warehouse_account_id = request('model.warehouse_account_id');
        $referral_guide_series = request('model.referral_guide_series');
        $referral_guide_number = request('model.referral_guide_number');
        $scop_number = request('model.scop_number');
        $license_plate = request('model.vehicle_id');
        $license_plate2 = request('model.vehicle2_id');
        $route_id = request('model.route_id');
        $articles = request('article_list');
        $ose = request('model.ose');
        $serie = request('model.serie');
        $ubigeo_id = request('model.ubigeo_id');
        $employee_id = request('model.employee_id');
        $activity = request('model.activity') ? request('model.activity') : 'xxx-xxx-xxx';
        /*? request('model.activity') : 'xxx-xxx-xxx';*/
        $referral_serie = request('model.referral_serie');
        $referral_number = request('model.referral_number');

        $tmpGuideSerie = GuidesSerie::where('company_id', $company_id)
            ->where('num_serie', $referral_guide_series)
            ->orderBy('correlative', 'desc')
            ->first();

        preg_match('/-(.*?)-/', $activity, $matches);

        $agent = Agent::where('activity', $matches[1])->first();

        if ($tmpGuideSerie) {
            $tmpGuideSerie->correlative = $tmpGuideSerie->correlative ? ($tmpGuideSerie->correlative + 1) : 1;
            $tmpGuideSerie->save();
        }

        $movement_number = WarehouseMovement::select('movement_number')
            ->where('movement_class_id', $movement_class_id)
            ->where('warehouse_type_id', $warehouse_type_id)
            ->where('company_id', $company_id)
            ->max('movement_number');

        $movement_number = ($movement_number ? $movement_number + 1 : 1);

        if ($warehouse_account_type_id == 1) {
            $account = Client::select('business_name', 'document_number')
                ->where('id', $warehouse_account_id)
                ->first();
        } elseif ($warehouse_account_type_id == 2) {
            $account = Provider::select('business_name', 'document_number')
                ->where('id', $warehouse_account_id)
                ->first();
        } elseif ($warehouse_account_type_id == 3) {
            $account = Employee::select('first_name', 'last_name')
                ->where('id', $warehouse_account_id)
                ->first();

            $account->business_name = $account->first_name . ' ' . $account->last_name;
            $account->document_number = '';
        }

        $movement_type = MoventType::find($movement_type_id);

        $transportist = Vehicle::select('id', 'plate', 'transportist_id')
            ->where('plate', $license_plate)
            ->first();


        //guia electronicas
        if ($ose == 0) {

            $serie_guide_number = Guide::where('referral_guide_series', $serie)
                ->where('company_id', $company_id)
                ->max('referral_guide_number');

            $guides = new Guide();
            $guides->company_id = $company_id;
            $guides->warehouse_type_id = $warehouse_type_id;
            $guides->movement_class_id = $movement_class_id;
            $guides->movement_type_id = $movement_type_id;
            $guides->movement_number = $movement_number;
            $guides->warehouse_account_type_id = $warehouse_account_type_id;
            $guides->account_id = $warehouse_account_id;
            $guides->account_document_number = $account->document_number;
            $guides->account_name = $account->business_name;
            $guides->referral_guide_series = $serie;

            $guides->referral_guide_number = $serie_guide_number ? $serie_guide_number + 1 : 1;
            $guides->referral_serie_number = $referral_guide_series;
            $guides->referral_voucher_number = $referral_guide_number == 0 ? 1 : $referral_guide_number;
            $guides->scop_number = $scop_number;

            $guides->vehicle_id = $transportist->id;

            $guides->taxed_operation = array_sum(array_column($articles, 'sale_value'));
            $guides->unaffected_operation = array_sum(array_column($articles, 'inaccurate_value'));
            $guides->exonerated_operation = 0;
            $guides->igv = array_sum(array_column($articles, 'igv'));
            $guides->total = array_sum(array_column($articles, 'total'));
            $guides->total_perception = array_sum(array_column($articles, 'perception'));
            $guides->action_type_id = ($movement_type ? $movement_type->action_type_id : '');
            $guides->created_at = date('Y-m-d', strtotime($since_date));
            $guides->created_at_user = Auth::user()->user;
            $guides->updated_at_user = Auth::user()->user;
            $guides->traslate_date = date('Y-m-d', strtotime($traslate_date));
            //$guides->fac_date = date('Y-m-d', strtotime($traslate_date));
            $guides->route_id = $route_id;
            $guides->ubigeo_id = $ubigeo_id;
            $guides->employee_id = $employee_id;
            $guides->activity = $activity;
            $guides->agent =  $agent ? $agent->name : 'No se encontró';
            $guides->referral_serie = $referral_serie;
            $guides->referral_number = $referral_number;
            //$guides->transportist_id = $transportist->transportist_id;
            $guides->save();


            foreach ($articles as $item) {
                $article = Article::where('warehouse_type_id', $guides->warehouse_type_id)
                    ->where('id', $item['id'])
                    ->firstOrFail();

                $digit_amount = str_replace(',', '', $item['digit_amount']);
                $converted_amount = str_replace(',', '', $item['converted_amount']);
                $price = str_replace(',', '', $item['price']);
                $sale_value = str_replace(',', '', $item['sale_value']);
                $inaccurate_value = str_replace(',', '', $item['inaccurate_value']);
                $igv = str_replace(',', '', $item['igv']);
                $total = str_replace(',', '', $item['total']);
                $igv_perception = str_replace(',', '', $item['perception']);

                $guides_detail = new GuidesDetail();
                $guides_detail->guides_id = $guides->id;
                $guides_detail->item_number = $item['item_number'];
                $guides_detail->article_code = $item['id'];
                $guides_detail->digit_amount = $digit_amount;
                $guides_detail->converted_amount = $converted_amount;
                $guides_detail->new_stock_good = $article->stock_good;
                $guides_detail->price = $price;
                $guides_detail->sale_value = $sale_value;
                $guides_detail->exonerated_value = 0;
                $guides_detail->inaccurate_value = $inaccurate_value;
                $guides_detail->igv = $igv;
                $guides_detail->total = $total;
                $guides_detail->igv_perception = $igv_perception;
                $guides_detail->igv_percentage = $item['igv_percentage'];
                $guides_detail->igv_perception_percentage = $item['perception_percentage'];
                $guides_detail->created_at_user = Auth::user()->user;
                $guides_detail->updated_at_user = Auth::user()->user;
                $guides_detail->save();
            }
        }

        $code = 0;
        foreach ($articles as $item) {

            $art_conv = Article::where('warehouse_type_id', $warehouse_type_id)
                ->where('id', $item['id'])
                ->firstOrFail();

            switch ($item['id']) {
                //GLP 05 KG (MARCA) - MERCADERIA
                case 1:
                case 5:
                case 9:
                case 13:
                case 17:
                case 10261:
                    $code = 9288;
                    //FIERRO
                    $article_pre = Article::where('id', $code)
                        ->firstOrFail();
                    break;
                //GLP 10 KG (MARCA) - MERCADERIA
                case 2:
                case 6:
                case 10:
                case 14:
                case 18:
                case 10260:
                    $code = 9289;
                    //FIERRO
                    $article_pre = Article::where('id', $code)
                        ->firstOrFail();
                    break;
                //GLP 15 KG (MARCA) - MERCADERIA
                case 3:
                case 7:
                case 11:
                case 15:
                case 19:
                    $code = 9290;
                    //FIERRO
                    $article_pre = Article::where('id', $code)
                        ->firstOrFail();
                    break;
                //GLP 45 KG (MARCA) - MERCADERIA
                case 4:
                case 8:
                case 12:
                case 16:
                case 20:
                    $code = 9291;
                    //FIERRO
                    $article_pre = Article::where('id', $code)
                        ->firstOrFail();
                    break;
            }

            $digit_amount = str_replace(',', '', $item['digit_amount']);
            $converted_amount = str_replace(',', '', $item['converted_amount']);
            $price = str_replace(',', '', $item['price']);
            $sale_value = str_replace(',', '', $item['sale_value']);
            $inaccurate_value = str_replace(',', '', $item['inaccurate_value']);
            $igv = str_replace(',', '', $item['igv']);
            $total = str_replace(',', '', $item['total']);
            $igv_perception = str_replace(',', '', $item['perception']);

            $code_granel = 9312;
            $granel = Article::where('id', $code_granel)
                ->firstOrFail();

            if ($movement_type_id == 11) { //PREVENTA
                if ($item['id'] != 23 && $item['id'] != 24) { // granel kg - gln
                    if ($art_conv->stock_good >= $converted_amount) {
                        //producto terminado
                        $art_conv->stock_good = $art_conv->stock_good - $converted_amount;
                        //fierro
                        $article_pre->lleno_tran = $article_pre->lleno_tran + $converted_amount;
                    } else {
                        //fierro
                        $article_pre->stock_good = $article_pre->stock_good - ($converted_amount - $art_conv->stock_good);
                        $article_pre->lleno_tran = $article_pre->lleno_tran + $converted_amount;
                        //granel kg
                        $granel->stock_good = $granel->stock_good - (($converted_amount - $art_conv->stock_good) * $art_conv->convertion);
                        //producto terminado
                        $art_conv->stock_good = 0;
                    }
                } else {
                    $granel->stock_good = $granel->stock_good - $converted_amount;
                }
            } else if ($movement_type_id == 12) { //VENTA PLANTA
                if ($item['id'] != 23 && $item['id'] != 24) {
                    $granel->stock_good = $granel->stock_good - ($converted_amount * $art_conv->convertion);
                } else {
                    $granel->stock_good = $granel->stock_good - $converted_amount;
                }
            }

            if ($movement_type_id == 11) { //PREVENTA
                if ($item['id'] != 23 && $item['id'] != 24) { // granel kg - gln
                    if ($art_conv->stock_good >= $converted_amount) {
                        $plan_movement = new PlantMovement();
                        $plan_movement->company_id = $company_id;
                        $plan_movement->warehouse_type_id = 22;
                        $plan_movement->movement_class_id = $movement_class_id;
                        $plan_movement->movement_type_id = 21;
                        $plan_movement->movement_number = $movement_number;
                        $plan_movement->referral_guide_series = $referral_guide_series;
                        $plan_movement->referral_guide_number = $referral_guide_number;
                        $plan_movement->scop_number = $scop_number;
                        $plan_movement->save();
                        $planMovementDetail = new PlantMovementDetail();
                        $planMovementDetail->plant_movement_id = $plan_movement->id;
                        $planMovementDetail->item_number = $item['item_number'];
                        $planMovementDetail->article_code = $item['id'];
                        $planMovementDetail->digit_amount = $digit_amount;
                        $planMovementDetail->converted_amount = $converted_amount;
                        $planMovementDetail->save();

                        $plan_movement = new PlantMovement();
                        $plan_movement->company_id = $company_id;
                        $plan_movement->warehouse_type_id = 22;
                        $plan_movement->movement_class_id = $movement_class_id;
                        $plan_movement->movement_type_id = 21;
                        $plan_movement->movement_number = $movement_number;
                        $plan_movement->referral_guide_series = $referral_guide_series;
                        $plan_movement->referral_guide_number = $referral_guide_number;
                        $plan_movement->scop_number = $scop_number;
                        $plan_movement->save();
                        $planMovementDetail = new PlantMovementDetail();
                        $planMovementDetail->plant_movement_id = $plan_movement->id;
                        $planMovementDetail->item_number = $item['item_number'];
                        $planMovementDetail->article_code = $code;
                        $planMovementDetail->digit_amount = $digit_amount;
                        $planMovementDetail->converted_amount = $converted_amount;
                        $planMovementDetail->save();
                    } else {
                        $plan_movement = new PlantMovement();
                        $plan_movement->company_id = $company_id;
                        $plan_movement->warehouse_type_id = 22;
                        $plan_movement->movement_class_id = $movement_class_id;
                        $plan_movement->movement_type_id = 21;
                        $plan_movement->movement_number = $movement_number;
                        $plan_movement->referral_guide_series = $referral_guide_series;
                        $plan_movement->referral_guide_number = $referral_guide_number;
                        $plan_movement->scop_number = $scop_number;
                        $plan_movement->save();
                        $planMovementDetail = new PlantMovementDetail();
                        $planMovementDetail->plant_movement_id = $plan_movement->id;
                        $planMovementDetail->item_number = $item['item_number'];
                        $planMovementDetail->article_code = $code;
                        $planMovementDetail->digit_amount = $digit_amount;
                        $planMovementDetail->converted_amount = $converted_amount;
                        $planMovementDetail->save();

                        $plan_movement = new PlantMovement();
                        $plan_movement->company_id = $company_id;
                        $plan_movement->warehouse_type_id = 22;
                        $plan_movement->movement_class_id = $movement_class_id;
                        $plan_movement->movement_type_id = 21;
                        $plan_movement->movement_number = $movement_number;
                        $plan_movement->referral_guide_series = $referral_guide_series;
                        $plan_movement->referral_guide_number = $referral_guide_number;
                        $plan_movement->scop_number = $scop_number;
                        $plan_movement->save();
                        $planMovementDetail = new PlantMovementDetail();
                        $planMovementDetail->plant_movement_id = $plan_movement->id;
                        $planMovementDetail->item_number = $item['item_number'];
                        $planMovementDetail->article_code = $code_granel;
                        $planMovementDetail->digit_amount = $digit_amount;
                        $planMovementDetail->converted_amount = $converted_amount;
                        $planMovementDetail->save();

                        $plan_movement = new PlantMovement();
                        $plan_movement->company_id = $company_id;
                        $plan_movement->warehouse_type_id = 22;
                        $plan_movement->movement_class_id = $movement_class_id;
                        $plan_movement->movement_type_id = 21;
                        $plan_movement->movement_number = $movement_number;
                        $plan_movement->referral_guide_series = $referral_guide_series;
                        $plan_movement->referral_guide_number = $referral_guide_number;
                        $plan_movement->scop_number = $scop_number;
                        $plan_movement->save();
                        $planMovementDetail = new PlantMovementDetail();
                        $planMovementDetail->plant_movement_id = $plan_movement->id;
                        $planMovementDetail->item_number = $item['item_number'];
                        $planMovementDetail->article_code = $item['id'];
                        $planMovementDetail->digit_amount = $digit_amount;
                        $planMovementDetail->converted_amount = $converted_amount;
                        $planMovementDetail->save();
                    }
                } else {
                    $plan_movement = new PlantMovement();
                    $plan_movement->company_id = $company_id;
                    $plan_movement->warehouse_type_id = 22;
                    $plan_movement->movement_class_id = $movement_class_id;
                    $plan_movement->movement_type_id = 21;
                    $plan_movement->movement_number = $movement_number;
                    $plan_movement->referral_guide_series = $referral_guide_series;
                    $plan_movement->referral_guide_number = $referral_guide_number;
                    $plan_movement->scop_number = $scop_number;
                    $plan_movement->save();
                    $planMovementDetail = new PlantMovementDetail();
                    $planMovementDetail->plant_movement_id = $plan_movement->id;
                    $planMovementDetail->item_number = $item['item_number'];
                    $planMovementDetail->article_code = $code_granel;
                    $planMovementDetail->digit_amount = $digit_amount;
                    $planMovementDetail->converted_amount = $converted_amount;
                    $planMovementDetail->save();
                }
            } else if ($movement_type_id == 12) { //VENTA PLANTA
                if ($item['id'] != 23 && $item['id'] != 24) {
                    $plan_movement = new PlantMovement();
                    $plan_movement->company_id = $company_id;
                    $plan_movement->warehouse_type_id = 22;
                    $plan_movement->movement_class_id = $movement_class_id;
                    $plan_movement->movement_type_id = 21;
                    $plan_movement->movement_number = $movement_number;
                    $plan_movement->referral_guide_series = $referral_guide_series;
                    $plan_movement->referral_guide_number = $referral_guide_number;
                    $plan_movement->scop_number = $scop_number;
                    $plan_movement->save();
                    $planMovementDetail = new PlantMovementDetail();
                    $planMovementDetail->plant_movement_id = $plan_movement->id;
                    $planMovementDetail->item_number = $item['item_number'];
                    $planMovementDetail->article_code = $code_granel;
                    $planMovementDetail->digit_amount = $digit_amount;
                    $planMovementDetail->converted_amount = $converted_amount;
                    $planMovementDetail->save();
                } else {
                    $plan_movement = new PlantMovement();
                    $plan_movement->company_id = $company_id;
                    $plan_movement->warehouse_type_id = 22;
                    $plan_movement->movement_class_id = $movement_class_id;
                    $plan_movement->movement_type_id = 21;
                    $plan_movement->movement_number = $movement_number;
                    $plan_movement->referral_guide_series = $referral_guide_series;
                    $plan_movement->referral_guide_number = $referral_guide_number;
                    $plan_movement->scop_number = $scop_number;
                    $plan_movement->save();
                    $planMovementDetail = new PlantMovementDetail();
                    $planMovementDetail->plant_movement_id = $plan_movement->id;
                    $planMovementDetail->item_number = $item['item_number'];
                    $planMovementDetail->article_code = $code_granel;
                    $planMovementDetail->digit_amount = $digit_amount;
                    $planMovementDetail->converted_amount = $converted_amount;
                    $planMovementDetail->save();
                }
            }
        }


        $movement = new WarehouseMovement();
        $movement->company_id = $company_id;
        $movement->warehouse_type_id = $warehouse_type_id;
        $movement->movement_class_id = $movement_class_id;
        $movement->movement_type_id = $movement_type_id;
        $movement->movement_number = $movement_number;
        $movement->warehouse_account_type_id = $warehouse_account_type_id;
        $movement->account_id = $warehouse_account_id;
        $movement->account_document_number = $account->document_number;
        $movement->account_name = $account->business_name;
        $movement->referral_guide_series = $referral_guide_series;
        $movement->referral_guide_number = $referral_guide_number;
        $movement->scop_number = $scop_number;
        $movement->license_plate = $license_plate;
        $movement->license_plate_2 = $license_plate2;
        $movement->taxed_operation = array_sum(array_column($articles, 'sale_value'));
        $movement->unaffected_operation = array_sum(array_column($articles, 'inaccurate_value'));
        $movement->exonerated_operation = 0;
        $movement->igv = array_sum(array_column($articles, 'igv'));
        $movement->total = array_sum(array_column($articles, 'total'));
        $movement->total_perception = array_sum(array_column($articles, 'perception'));
        $movement->action_type_id = ($movement_type ? $movement_type->action_type_id : '');
        $movement->created_at = date('Y-m-d', strtotime($since_date));
        $movement->created_at_user = Auth::user()->user;
        $movement->updated_at_user = Auth::user()->user;
        $movement->traslate_date = date('Y-m-d', strtotime($traslate_date));
        $movement->fac_date = date('Y-m-d', strtotime($traslate_date));
        $movement->route_id = $route_id;
        $movement->transportist_id = $transportist->transportist_id;

        $movement->activity = $activity;
        $movement->agent = $agent ? $agent->name : 'No se encontró';
        $movement->referral_serie = $referral_serie;
        $movement->referral_number = $referral_number;

        if ($movement_type_id == 12) {
            $movement->employee_id = $employee_id;
        }

        if ($ose == 0) {
            $movement->referral_serie_number =  $guides->referral_guide_series;
            $movement->referral_voucher_number =  $guides->referral_guide_number;
        }

        $movement->save();

        //NOTE
        $note = new Note();
        $note->company_id = $company_id;
        $note->warehouse_type_id = $warehouse_type_id;
        $note->movement_class_id = $movement_class_id;
        $note->movement_type_id = $movement_type_id;
        $note->movement_number = $movement_number;
        $note->warehouse_account_type_id = $warehouse_account_type_id;
        $note->account_id = $warehouse_account_id;
        $note->account_document_number = $account->document_number;
        $note->account_name = $account->business_name;
        $note->referral_guide_series = $referral_guide_series;
        $note->referral_guide_number = $referral_guide_number;
        $note->scop_number = $scop_number;
        $note->license_plate = $license_plate;
        $note->taxed_operation = array_sum(array_column($articles, 'sale_value'));
        $note->unaffected_operation = array_sum(array_column($articles, 'inaccurate_value'));
        $note->exonerated_operation = 0;
        $note->igv = array_sum(array_column($articles, 'igv'));
        $note->total = array_sum(array_column($articles, 'total'));
        $note->total_perception = array_sum(array_column($articles, 'perception'));
        $note->action_type_id = ($movement_type ? $movement_type->action_type_id : '');
        $note->created_at = date('Y-m-d', strtotime($since_date));
        $note->created_at_user = Auth::user()->user;
        $note->updated_at_user = Auth::user()->user;
        $note->traslate_date = date('Y-m-d', strtotime($traslate_date));
        $note->fac_date = date('Y-m-d', strtotime($traslate_date));
        $note->route_id = $route_id;
        $note->transportist_id = $transportist->transportist_id;
        if ($movement_type_id == 12) {
            $note->employee_id = $employee_id;
        }
        $note->save();

        foreach ($articles as $item) {
            $article = Article::where('warehouse_type_id', $movement->warehouse_type_id)
                ->where('id', $item['id'])
                ->firstOrFail();

            $digit_amount = str_replace(',', '', $item['digit_amount']);
            $converted_amount = str_replace(',', '', $item['converted_amount']);
            $price = str_replace(',', '', $item['price']);
            $sale_value = str_replace(',', '', $item['sale_value']);
            $inaccurate_value = str_replace(',', '', $item['inaccurate_value']);
            $igv = str_replace(',', '', $item['igv']);
            $total = str_replace(',', '', $item['total']);
            $igv_perception = str_replace(',', '', $item['perception']);

            $movementDetail = new WarehouseMovementDetail();
            $movementDetail->warehouse_movement_id = $movement->id;
            $movementDetail->item_number = $item['item_number'];
            $movementDetail->article_code = $item['id'];
            $movementDetail->digit_amount = $digit_amount;
            $movementDetail->converted_amount = $converted_amount;
            //$movementDetail->new_stock_good = $article->stock_good;
            $movementDetail->price = $price;
            $movementDetail->sale_value = $sale_value;
            $movementDetail->exonerated_value = 0;
            $movementDetail->inaccurate_value = $inaccurate_value;
            $movementDetail->igv = $igv;
            $movementDetail->total = $total;
            $movementDetail->igv_perception = $igv_perception;
            $movementDetail->igv_percentage = $item['igv_percentage'];
            $movementDetail->igv_perception_percentage = $item['perception_percentage'];
            $movementDetail->created_at_user = Auth::user()->user;
            $movementDetail->updated_at_user = Auth::user()->user;
            $movementDetail->save();

            //NOTE DETAILS

            $noteDetail = new NoteDetail();
            $noteDetail->warehouse_movement_id = $note->id;
            $noteDetail->item_number = $item['item_number'];
            $noteDetail->article_code = $item['id'];
            $noteDetail->digit_amount = $digit_amount;
            $noteDetail->converted_amount = $converted_amount;
            //$noteDetail->new_stock_good = $article->stock_good;
            $noteDetail->price = $price;
            $noteDetail->sale_value = $sale_value;
            $noteDetail->exonerated_value = 0;
            $noteDetail->inaccurate_value = $inaccurate_value;
            $noteDetail->igv = $igv;
            $noteDetail->total = $total;
            $noteDetail->igv_perception = $igv_perception;
            $noteDetail->igv_percentage = $item['igv_percentage'];
            $noteDetail->igv_perception_percentage = $item['perception_percentage'];
            $noteDetail->created_at_user = Auth::user()->user;
            $noteDetail->updated_at_user = Auth::user()->user;
            $noteDetail->save();

            $article->edit = 1;
            $article->save();
        }

        return $articles;
    }
}
