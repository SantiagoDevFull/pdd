<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Company;
use App\CocheraVehicle;
use App\MoventType;
use App\WarehouseDocumentType;
use App\Article;
use App\Area;
use App\Costo;
use App\Currency;
use App\WarehouseAccountType;
use App\Client;
use App\Employee;
use App\Provider;
use App\Rate;
use App\CocheraMovement;
use App\CocheraMovementDetail;
use App\CostoSecundario;
use Auth;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CajaRegisterController extends Controller
{
	public function index()
	{
		$companies = Company::select('id', 'name')->get();
		$currencies = Currency::select('id', 'name', 'symbol')->whereIn('id', [1])->get();
		// $current_date = date('d-m-Y');
		$date = CarbonImmutable::now()->startOfDay();
		$current_date = $date->startOfDay()->toAtomString();
		$min_datetime = CarbonImmutable::now()->subDays(7)->startOfDay()->toAtomString();
		$cochera_vehicles = CocheraVehicle::select('id', 'plate')->get();
		$max_datetime = $date->startOfDay()->addDays(2)->toAtomString();
		$warehouse_account_types = WarehouseAccountType::select('id', 'name')->whereIn('id', [2])->get();
		$warehouse_document_types = WarehouseDocumentType::select('id', 'name')->whereIn('id', [5,33])->get();
		$igv = Rate::select('description', 'value')
			->where('description', 'IGV')
			->where('state', 1)
			->first();

		return view('backend.caja_register')->with(compact(
			'companies',
			'currencies',
			'current_date',
			'min_datetime',
			'max_datetime',
			'warehouse_account_types',
			'warehouse_document_types',
			'igv',
			'cochera_vehicles'
		));
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
			'company_id.required'                                => 'Debe seleccionar una Compañía.',
			'currency.required'                                  => 'Debe seleccionar Tipo de moneda.',
			'warehouse_account_type_id.required'                 => 'Debe seleccionar Tipo.',
			'warehouse_account_id.required'                      => 'Debe completar Nombre o Razón Social.',
			'area_id.required'                                   => 'Debe seleccionar un Área.',
			'costo_id.required'                                  => 'Debe seleccionar una Centro de Costo.',
			'costo_secundario_id.required'                       => 'Debe seleccionar una Costo Secundario.',
			//	'payment_id.required'                                => 'Debe seleccionar una Condición de Pago.',
			//	'credit_limit_id.required'                           => 'Debe seleccionar los Días de Crédito.',
			'referral_warehouse_document_type_id.required'       => 'Debe seleccionar un Tipo Referencia.',
			'referral_serie_number.required'                     => 'Debe completar la Serie de Referencia.',
			'referral_voucher_number.required'                   => 'Debe completar el Número de Referencia.',
			'subarea.required'                                   => 'Debe seleccionar un Sub Área.',
			'tipo.required'                                      => 'Debe seleccionar un Tipo.',
			'ppto.required'                                      => 'Debe seleccionar el Presupuestado.',
		];

		$rules = [
			'company_id'                             => 'required',
			'currency'                               => 'required',
			'warehouse_account_type_id'              => 'required',
			'warehouse_account_id'                   => 'required',
			'area_id'                                => 'required',
			'costo_id'                               => 'required',
			'costo_secundario_id'                    => 'required',
			//	'payment_id'                             => 'required',
			//	'credit_limit_id'                        => 'required',
			'referral_warehouse_document_type_id'    => 'required',
			'referral_serie_number'                  => 'required',
			'referral_voucher_number'                => 'required',
			'subarea'                                => 'required',
			'tipo'                                   => 'required',
			'ppto'                                   => 'required',
		];

		request()->validate($rules, $messages);
		return request()->all();
	}

	public function getAreas()
	{
		$company_id = request('company_id');
		$areas = Area::select('id', 'name', 'type')
			->where('company_id', $company_id)
			->where('type', 1)
			->get();

		return $areas;
	}

	public function getCostos()
	{
		$area_id = request('area_id');
		$costos = Costo::select('id', 'name')
			->where('area_id', $area_id)
			->get();

		return $costos;
	}

	public function getCostoSecs()
	{
		$costo_id = request('costo_id');
		$costo_secundarios = CostoSecundario::select('id', 'name')
			->where('costo_id', $costo_id)
			->get();

		return $costo_secundarios;
	}

	public function list()
	{
		$this->validateForm();
		$warehouse_type_id = 21;
		$currency_id = request('currency');

		$model = request()->all();

		// Obtener artículos
		$articles = Article::select('id', 'code', 'name', 'package_sale', 'sale_unit_id', 'package_warehouse', 'warehouse_unit_id', 'igv', 'perception', 'stock_good', 'stock_repair', 'stock_return', 'stock_damaged')
			->where('warehouse_type_id', $warehouse_type_id)
			->orderBy('code', 'asc')
			->get();

		$articles->map(function ($item, $index) {
			$item->sale_unit_id = $item->sale_unit['name'];
			$item->warehouse_unit_id = $item->warehouse_unit['name'];
		});

		// Obtener tipo de moneda
		$currency = Currency::select('id', 'name', 'symbol')
			->where('id', $currency_id)
			->first();

		return response()->json([
			'model'                    => $model,
			'currency'                => $currency,
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
		$currency_id = request('currency');
		$price = request('model.price');
		$sale_value = request('model.sale_value');
		$igv = request('model.igv');
		$total = request('model.total');
		$perception = request('model.perception');
		$igv_percentage = request('igv_percentage');
		$perception_percentage = request('perception_percentage');
		$item_number = request('item_number');
		$movement_type_id = request('movement_type_id');

		$currency = Currency::select('name')
			->where('id', $currency_id)
			->first();

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

		$article->currency = $currency->name;
		$article->currency_id = $currency_id;
		$article->price = number_format($price, 4, '.', ',');
		$article->sale_value = number_format($sale_value, 4, '.', ',');
		$article->igv = number_format($igv, 4, '.', ',');
		$article->total = number_format($total, 4, '.', ',');
		$article->perception = number_format($perception, 4, '.', ',');
		$article->igv_percentage = ($igv == 0 ? 0 : $igv_percentage);
		$article->perception_percentage = $perception_percentage;

		return $article;
	}

	public function store(Request $request)
	{

		$model = json_decode($request->input('model'), true);
		$articles = json_decode($request->input('article_list'), true);

		$movement_class_id = 2;
		$movement_type_id = 2;
		$warehouse_type_id = 21;
		$company_id = $model['company_id'];
		$area_id = $model['area_id'];
		$costo_id = $model['costo_id'];
		$costo_secundario_id = $model['costo_secundario_id'];
		$currency_id = $model['currency'];
		$since_date = $model['since_date'];
		$warehouse_account_type_id = $model['warehouse_account_type_id'];
		$warehouse_account_id = $model['warehouse_account_id'];
		$referral_warehouse_document_type_id = $model['referral_warehouse_document_type_id'];
		$referral_serie_number = $model['referral_serie_number'];
		$referral_voucher_number = $model['referral_voucher_number'];
		$limit_credit_id = 7;
		$payment_id = 2; //CREDITO
		$subarea = $model['subarea'];
		$ppto = $model['ppto'];
		$tipo = $model['tipo'];



		$movement_number = CocheraMovement::select('movement_number')
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

		$movement = new CocheraMovement();
		$movement->company_id = $company_id;
		$movement->warehouse_type_id = $warehouse_type_id;
		$movement->movement_class_id = $movement_class_id;
		$movement->movement_type_id = $movement_type_id;
		$movement->movement_number = $movement_number;
		$movement->warehouse_account_type_id = $warehouse_account_type_id;
		$movement->account_id = $warehouse_account_id;
		$movement->account_document_number = $account->document_number;
		$movement->account_name = $account->business_name;
		$movement->referral_warehouse_document_type_id = $referral_warehouse_document_type_id;
		$movement->referral_serie_number = $referral_serie_number;
		$movement->referral_voucher_number = $referral_voucher_number;
		$movement->currency_id = $currency_id;
		$movement->taxed_operation = array_sum(array_column($articles, 'sale_value'));
		$movement->unaffected_operation = 0;
		$movement->exonerated_operation = 0;
		$movement->igv = array_sum(array_column($articles, 'igv'));
		$movement->total = array_sum(array_column($articles, 'total'));
		$movement->total_perception = array_sum(array_column($articles, 'perception'));
		$movement->action_type_id = ($movement_type ? $movement_type->action_type_id : '');
		$movement->date_at = date('Y-m-d', strtotime($since_date));
		$movement->created_at_user = Auth::user()->user;
		$movement->updated_at_user = Auth::user()->user;
		$movement->area_id = $area_id;
		$movement->costo_id = $costo_id;
		$movement->costo_secundario_id = $costo_secundario_id;
		$movement->payment_id = $payment_id;
		$movement->subarea = $subarea;
		$movement->ppto = $ppto;
		$movement->tipo = $tipo;
		$movement->save();


		$carpeta = "uploads/cochera/facturas";
		$fechaActual = date('Y-m-d', strtotime($since_date));
		$file = $request->file('file');

		$fileName = $file->getClientOriginalName();
		$extension = $file->getClientOriginalExtension();
		$automatic = "COCHERA-" . sprintf('%010d', $movement->id) . '.' . $extension;

		$ruta = $carpeta . '/' . $fechaActual . '/';
		$filePath = $file->storeAs($ruta, $automatic, 'public');

		$movement->route_automatic = $automatic;
		$movement->route_name = $fileName;

		$fact_date = date('Y-m-d', strtotime($movement->date_at));
		$balance = 0;

		$expiry_date = $fact_date;

		if ($payment_id == 2) {
			$balance = array_sum(array_column($articles, 'total'));
			$expiry_date = CarbonImmutable::createFromFormat('Y-m-d', $fact_date)->addDays($limit_credit_id);
			$movement->limit_credit_id = $limit_credit_id;
		}

		$movement->balance = $balance;
		$movement->expiry_date = $expiry_date;
		$movement->save();

		foreach ($articles as $item) {

			$digit_amount = str_replace(',', '', $item['quantity']);
			$converted_amount = str_replace(',', '', $item['quantity']);
			$price = str_replace(',', '', $item['price']);
			$sale_value = str_replace(',', '', $item['sale_value']);
			$igv = str_replace(',', '', $item['igv']);
			$total = str_replace(',', '', $item['total']);

			$movementDetail = new CocheraMovementDetail();
			$movementDetail->cochera_movement_id = $movement->id;
			$movementDetail->item_number = $item['item_number'];
			$movementDetail->article_name = $item['article_name'];
			$movementDetail->digit_amount = $digit_amount;
			$movementDetail->converted_amount = $converted_amount;
			$movementDetail->currency_id = $currency_id;
			$movementDetail->price = $price;
			$movementDetail->sale_value = $sale_value;
			$movementDetail->igv = $igv;
			$movementDetail->total = $total;
			$movementDetail->igv_percentage = $item['igv_percentage'];
			$movementDetail->plate = $item['placa'];
			$movementDetail->created_at_user = Auth::user()->user;
			$movementDetail->updated_at_user = Auth::user()->user;
			$movementDetail->save();
		}

		$type = 3;
		$title = '¡Ok!';
		$msg = "Factura registrada correctamente.";
		$url = '/transportes/registro-facturas';


		return response()->json([
			'type'    => $type,
			'title'    => $title,
			'msg'    => $msg,
			'url'    => $url,
		]);
	}
}
