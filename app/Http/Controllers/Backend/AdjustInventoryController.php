<?php

namespace App\Http\Controllers\Backend;

use App\Article;
use App\CierrePlanta;
use App\Company;
use App\Exports\InventoryReportExport;
use App\Http\Controllers\Controller;
use App\Inventory;
use App\PlantMovement;
use App\PlantMovementDetail;
use App\WarehouseType;
use Carbon\CarbonImmutable;
use PDF;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class AdjustInventoryController extends Controller
{
	public function index()
	{

		$companies = Company::select('id', 'name')->whereIn('id', [1])->get();
		$current_date = date(DATE_ATOM, mktime(0, 0, 0));

		return view('backend.adjustinventories')->with(compact('companies', 'current_date'));
	}

	public function validateForm()
	{
		$messages = [
			'company_id.required'           => 'Debe seleccionar una Compañía.',
			'warehouse_type_id.required'     => 'Debe seleccionar un Almacén.',
			'creation_date.required'        => 'Debe seleccionar una Fecha.',
		];

		$rules = [
			'company_id'                   => 'required',
			'warehouse_type_id'        => 'required',
			'creation_date'                 => 'required',
		];

		request()->validate($rules, $messages);
		return request()->all();
	}

	public function list()
	{
		$company_id = request('model.company_id');
		$warehouse_type_id = request('model.warehouse_type_id');
		$creation_date = request('model.creation_date');
		$creation_date = date('Y-m-d', strtotime($creation_date));

		$p = request('pagination');
		$q = request('query');
		$page = (int)$p['page'];
		$perpage = (int)($p['perpage'] ? $p['perpage'] : 10);
		$search = $q['generalSearch'];
		request()->replace(['page' => $page]);

		$elements = Inventory::join('articles', 'articles.id', 'inventories.article_id')
			->join('warehouse_types', 'warehouse_types.id', 'inventories.warehouse_type_id')
			->select(
				'inventories.id as id',
				'inventories.company_id as company_id',
				'inventories.warehouse_type_id as warehouse_type_id',
				'inventories.article_id as article_id',
				'inventories.found_stock_good as found_stock_good',
				'inventories.found_stock_damaged as found_stock_damaged',
				'inventories.observations as observations',
				'warehouse_types.name as warehouse_type_name',
				'inventories.state as state'
			)
			->where('inventories.company_id', $company_id)
			->where('inventories.warehouse_type_id', $warehouse_type_id)
			->where('inventories.creation_date', $creation_date)
			->orderBy('inventories.id', 'ASC')
			->when($search, function ($query, $search) {
				return $query->where('articles.name', 'like', '%' . $search . '%')
					->orWhere('articles.code', 'like', '%' . $search . '%');
			})
			->paginate($perpage);

		$elements->map(function ($item, $index) {
			$item->company_short_name = $item->company->short_name;
			$item->article_code = $item->article->code;
			$item->article_name = $item->article->name;
		});

		$meta = new \stdClass();
		$meta->page = $page;
		$meta->pages = $elements->lastPage();
		$meta->perpage = $perpage;
		$meta->total = $elements->total();
		$meta->field = 'id';
		for ($i = 1; $i <= $elements->total(); $i++) {
			$meta->rowIds[] = $i;
		}

		return response()->json([
			'meta'        => $meta,
			'data'        => $elements->items(),
			'state'        => ($elements->total() > 0 ? $elements[0]->state : ''),
		]);
	}

	public function createRecord()
	{
		$company_id = request('model.company_id');
		$warehouse_type_id = request('model.warehouse_type_id');
		$creation_date = date('Y-m-d', strtotime(request('model.creation_date')));
		$creation_date_initial = date_create($creation_date . ' 00:00:00');
		$creation_date_final = date_create($creation_date . ' 23:59:59');


		$elements = Article::select('id', 'warehouse_type_id', 'code', 'name', 'stock_good', 'stock_repair', 'stock_return', 'stock_damaged', 'created_at')
			->where('warehouse_type_id', $warehouse_type_id)
			//->whereIn('id', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 25, 26, 27, 28])
			->get();

		$elements->each(function ($item, $index) use ($company_id, $creation_date) {

			$element = new Inventory();
			$element->company_id = $company_id;
			$element->warehouse_type_id = $item->warehouse_type_id;
			$element->article_id = $item->id;
			$element->creation_date = $creation_date;
			$element->stock_good = $item->stock_good;
			$element->stock_damaged = 0;
			$element->found_stock_good = 0;
			$element->found_stock_damaged = 0;
			$element->state = 0;
			$element->created_at_user = Auth::user()->user;
			$element->updated_at_user = Auth::user()->user;
			$element->save();
		});

		return response()->json([
			'total'        => $elements->count(),
		]);
	}

	public function getWarehouseTypes()
	{

		$company_id = request('company_id');

		$warehouse_types = WarehouseType::where('company_id', $company_id)->where('id', 22)->get();
		return $warehouse_types;
	}

	public function getArticlesWarehouseType()
	{

		$warehouse_type_id = request('warehouse_type_id');

		$warehouse_type = WarehouseType::findOrFail($warehouse_type_id);
		$articles = Article::where('warehouse_type_id', $warehouse_type_id)->get();

		$articles->transform(function ($article, $key) {
			$article->item_number = $key + 1;
			return $article;
		});

		return response()->json([
			'articles'            =>    $articles,
			'total'                => $articles->count(),
			'warehouse_type_name'    => $warehouse_type->name
		]);
	}

	public function getArticles()
	{
		$q = request('q');
		$warehouse_type_id = request('warehouse_type_id');

		$articles = Article::select('id', 'code', 'name', 'package_warehouse', 'warehouse_unit_id')
			->where('warehouse_type_id', $warehouse_type_id)
			//->whereIn('id', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 25, 26, 27, 28])
			->when($q, function ($query, $q) {
				return $query->where('code', 'like', '%' . $q . '%')
					->orWhere('name', 'like', '%' . $q . '%');
			})
			->orderBy('code', 'asc')
			->get();

		$articles->map(function ($item, $index) {
			$item->text = $item->code . ' - ' . $item->name . ' ' . $item->warehouse_unit->name . ' x ' . $item->package_warehouse;
		});

		return $articles;
	}

	public function detail()
	{
		$id = request('id');

		$element = Inventory::select('id', 'article_id', 'found_stock_good', 'found_stock_damaged', 'observations')
			->where('id', $id)
			->first();

		return $element;
	}

	public function getSelect2()
	{
		$article_id = request('article_id');

		$element = Article::select('id', 'code', 'name', 'package_warehouse', 'warehouse_unit_id')
			->where('id', $article_id)
			->first();

		$element->text = $element->code . ' - ' . $element->name . ' ' . $element->warehouse_unit->name . ' x ' . $element->package_warehouse;

		return $element;
	}

	public function validateModalForm()
	{
		$messages = [
			'found_stock_good.required'        => 'El total contado es obligatoria.',
			'found_stock_good.numeric'        => 'El total contado debe ser numérico.',
			'found_stock_good.min'            => 'El total contado debe ser mínimo 0.',
		];

		$rules = [
			'found_stock_good'             => 'required|numeric|min:0',
		];

		request()->validate($rules, $messages);
		return request()->all();
	}

	public function store()
	{
		$this->validateModalForm();

		$id = request('id');
		$found_stock_good = request('found_stock_good');
		$observations = request('observations');

		$element = Inventory::findOrFail($id);
		$element->found_stock_good = $found_stock_good;
		$element->observations = $observations;
		$element->updated_at_user = Auth::user()->user;

		$msg = 'Registro actualizado exitosamente';


		$element->save();

		$type = 1;
		$title = '¡Ok!';

		return response()->json([
			'type'    => $type,
			'title'    => $title,
			'msg'    => $msg,
		]);
	}

	public function delete()
	{
		$id = request('id');
		$element = Inventory::findOrFail($id);
		$element->delete();
	}

	public function closeRecord()
	{
		$company_id = request('company_id');
		$warehouse_type_id = request('warehouse_type_id');
		$creation_date = date('Y-m-d', strtotime(request('creation_date')));
		$data_flag = 0;

		$yesterday_sale_date = CarbonImmutable::createFromDate(request('creation_date'))->startOfDay()->subDay()->format('Y-m-d');

		$yes_cierre_planta = CierrePlanta::where('cierre_date', $yesterday_sale_date)
			->where('state', 1) // Cerrado
			->where('company_id', 1)
			->first();

		if ($yes_cierre_planta) { //Cerrado


			$cierre_planta = CierrePlanta::where('cierre_date', $creation_date)
				->where('company_id', 1)
				->first();

			if ($cierre_planta) {
				$cierre_planta->state = 1; // Cerrado
				$cierre_planta->save();



				$elements = Inventory::select()
					->where('company_id', $company_id)
					->where('warehouse_type_id', $warehouse_type_id)
					->where('creation_date', $creation_date)
					->get();

				$index = 1;
				foreach ($elements as $element) {

					$article = Article::find($element->article_id);

					$diff = $article->stock_good - $element->found_stock_good;

					$article->stock_good = $element->found_stock_good;
					$article->save();

					if ($diff > 0) {
						$movement_class_id = 1;
					} else {
						$movement_class_id = 2;
					}

					$id = PlantMovement::insertGetId([
						'company_id' => $company_id,
						'warehouse_type_id' => $element->warehouse_type_id,
						'movement_class_id' => $movement_class_id,
						'movement_type_id' => 22, //Diferencia de Inventario
						'warehouse_account_type_id' => 3, //Trabajador
						'total' => $diff,
						'created_at' => $creation_date,
						'updated_at' => $creation_date,
					]);

					PlantMovementDetail::insert([
						'plant_movement_id' => $id,
						'item_number' => $index++,
						'article_code' => $article->id,
						'new_stock_good' => $article->stock_good,
						'new_stock_damaged' => $article->stock_damaged,
						'converted_amount' => $diff,
						'total' => $diff,
						'created_at' => $creation_date,
						'updated_at' => $creation_date,
					]);

					$element->state = 1;
					$element->save();
				}

				$type = 3;
				$title = 'Éxito !';
				$msg = 'El cierre de planta e inventario del día ' . $creation_date . ' se ha realizado correctamente.';
				$url = route('dashboard.report.finanzas_detail_total');
			} else {
				$type = 2;
				$title = 'Advertencia !';
				$msg = 'Debe aperturar la planta del día actual antes de continuar con el cierre de planta.';
				$url = '';
			}
		} else {
			$type = 2;
			$title = 'Advertencia !';
			$msg = 'Debe cerrar la planta del día anterior antes de continuar con el cierre de planta del día actual.';
			$url = '';
		}


		return response()->json([
			'type'  => $type,
			'title' => $title,
			'msg'   => $msg,
			'url'   => $url
		], 200);
	}

	public function formRecord()
	{

		$company_id = request('company_id');
		$warehouse_type_id = request('warehouse_type_id');
		$creation_date = date('Y-m-d', strtotime(request('creation_date')));

		$elements = Inventory::select('id', 'company_id', 'warehouse_type_id', 'article_id', 'creation_date', 'found_stock_good', 'found_stock_damaged', 'observations')
			->where('company_id', $company_id)
			->where('warehouse_type_id', $warehouse_type_id)
			->where('creation_date', $creation_date)
			->get();

		$elements->map(function ($item, $index) {
			$item->article_code = $item->article->code;
			$item->article_name = $item->article->name;
			$item->warehouse_unit_short_name = $item->article->warehouse_unit->short_name;
			$item->package_warehouse = $item->article->package_warehouse;
		});

		$company = Company::select('id', 'name')->where('id', $company_id)->first();
		$warehouse_type = WarehouseType::select('id', 'name')->where('id', $warehouse_type_id)->first();

		$pdf = PDF::loadView('backend.inventories_pdf', compact('company', 'warehouse_type', 'elements', 'creation_date'));

		return $pdf->download('formulario-inventario-' . $creation_date . '.pdf');
	}

	public function exportRecord()
	{
		$company_id = request('company_id');
		$warehouse_type_id = request('warehouse_type_id');
		$creation_date = date('Y/m/d', strtotime(request('creation_date')));

		$elements = Inventory::join('warehouse_types', 'warehouse_types.id', 'inventories.warehouse_type_id')
			->select(
				'inventories.id as id',
				'inventories.company_id as company_id',
				'inventories.warehouse_type_id as warehouse_type_id',
				'inventories.article_id as article_id',
				'inventories.creation_date as creation_date',
				'inventories.stock_good as stock_good',
				'inventories.stock_damaged as stock_damaged',
				'inventories.found_stock_good as found_stock_good',
				'inventories.found_stock_damaged as found_stock_damaged',
				'inventories.observations as observations',
				'inventories.state as state',
				'warehouse_types.name as warehouse_type_name'
			)
			->where('inventories.company_id', $company_id)
			->where('inventories.warehouse_type_id', $warehouse_type_id)
			->where('inventories.creation_date', $creation_date)
			->get();

		$elements->map(function ($item, $index) {
			$item->article_code = $item->article->code;
			$item->article_name = $item->article->name;
			$item->warehouse_unit_short_name = $item->article->warehouse_unit->short_name;
			$item->package_warehouse = $item->article->package_warehouse;
			$item->difference_stock_good = $item->found_stock_good - $item->stock_good;
			$item->difference_stock_damaged = $item->found_stock_damaged - $item->stock_damaged;
		});

		$company = Company::select('id', 'name')->where('id', $company_id)->first();
		$warehouse_type = WarehouseType::select('id', 'name')->where('id', $company_id)->first();
		$state = ($elements->count() > 0 ? $elements[0]->state : '');

		$excel = new InventoryReportExport($elements, $company, $warehouse_type, $creation_date, $state);
		return Excel::download($excel, 'inventario.xlsx');
	}

	public function test()
	{
		$warehouse_type_id = request('warehouse_type_id');
		$inventories = Inventory::where('warehouse_type_id', $warehouse_type_id)
			->get();

		foreach ($inventories as $inventory) {
			$article = Article::where('id', $inventory->article_id)->first();
			$article->stock_good = $inventory->found_stock_good;
			$article->stock_damaged = $inventory->found_stock_damaged;
			$article->save();
		}
	}
}
