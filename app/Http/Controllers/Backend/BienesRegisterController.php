<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\WarehouseType;
use App\Article;
use App\Classification;
use App\Company;
use App\Exports\ArticleReportExport;
use App\OperationType;
use App\Sede;
use App\Unit;
use Auth;
use Carbon\CarbonImmutable;
use Maatwebsite\Excel\Facades\Excel;

class BienesRegisterController extends Controller
{
    public function index()
    {
        $warehouse_types = WarehouseType::select('id', 'name')->get();
        $families = Classification::select('id', 'name')->where('classification_type_id', 1)->get();
        $groups = Classification::select('id', 'name')->where('classification_type_id', 2)->get();
        $subgroups = Classification::select('id', 'name')->where('classification_type_id', 3)->get();
        $operations = OperationType::select('id', 'name')->get();
        $units = Unit::select('id', 'name')->get();

        if (Auth::user()->id == 47) {
            $companies = Company::select('id', 'name')->whereIn('id', [5])->get();
        } else {
            $companies = Company::select('id', 'name')->get();
        }

        return view('backend.articles_register')->with(compact(
            'warehouse_types',
            'families',
            'groups',
            'subgroups',
            'operations',
            'units',
            'companies'
        ));
    }

    public function validateForm()
    {
        $messages = [
            'company_id.required'           => 'Debe seleccionar una Compañía.',
            'sede_id.required'               => 'Debe seleccionar una Sede.',
            'warehouse_type_id.required'    => 'Debe seleccionar un Almacén.',
        ];

        $rules = [
            'company_id'        => 'required',
            'sede_id'              => 'required',
            'warehouse_type_id' => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function list()
    {

        $q = request('query');
        $search = $q['generalSearch'];

        $warehouse_type_id = request('warehouse_type_id');

        $articles = Article::leftjoin('units', 'units.id', 'articles.sale_unit_id')
            ->leftjoin('classifications as family', 'family.id', 'articles.family_id')
            ->leftjoin('classifications as group', 'group.id', 'articles.group_id')
            ->leftjoin('classifications as subgroup', 'subgroup.id', 'articles.subgroup_id')
            ->select(
                'articles.id as id',
                'articles.code as code',
                'articles.code_int as code_int',
                'articles.code_fab as code_fab',
                'articles.name as name',
                'units.name as sale_unit_name',
                'articles.package_sale as package_sale',
                'articles.stock_good as stock_good',
                'articles.stock_repair as stock_repair',
                'articles.stock_return as stock_return',
                'articles.stock_damaged as stock_damaged',
                'family.name as family_name',
                'group.name as group_name',
                'subgroup.name as subgroup_name',
                'articles.last_price as last_price',
                'articles.edit as edit'
            )
            ->when($search, function ($query, $search) {
                return $query->where('articles.name', 'like', '%' . $search . '%')->orWhere('articles.code', 'like', '%' . $search . '%');
            })
            ->where('articles.warehouse_type_id', $warehouse_type_id)
            ->orderBy('articles.id', 'desc')
            ->get();

        $user_name = Auth::user()->user;

        foreach ($articles as $obj) {
            if ($user_name !== 'gps1' && $user_name !== 'sistemas1') {
                $obj->edit = 1;
            }
        }

        return $articles;
    }

    public function detail()
    {
        $id = request('id');

        $article = Article::findOrFail($id);
        return $article;
    }

    public function delete()
    {
        $id = request('id');
        $element = Article::findOrFail($id);
        $element->delete();
    }

    public function validateModalForm()
    {
        $messages = [
            //'code.required'					=> 'El código es obligatorio.',
            'name.required'                    => 'La Descripción es obligatoria.',
            'family_id.required'            => 'Debe seleccionar una Familia/Marca.',
            'group_id.required'                => 'Debe seleccionar un Grupo.',
            'subgroup_id.required'            => 'Debe seleccionar un Sub-Grupo.',
            'sale_unit_id.required'            => 'Debe seleccionar una Unidad de Medida.',
            'package_sale.required'            => 'El Empaque de Compra es obligatorio.',
            'operation_type_id.required'    => 'Debe seleccionar una Operación de Conversión.',
            'factor.required'                => 'El Factor es obligatorio.',
            'warehouse_unit_id.required'    => 'Debe seleccionar una Unidad de Medida.',
            'package_warehouse.required'    => 'El Empaque de Almacén es obligatorio.',
            'igv.required'                    => 'Debe seleccionar si está Afecto a IGV.',
            'perception.required'            => 'Debe seleccionar si está Afecto a Percepción.',
            'last_price.required'            => 'El precio es obligatorio.',
            'last_price.min'                => 'El precio mínimo es cero.',
            'last_price.numeric'            => 'El precio debe ser numérico.',
        ];

        $rules = [
            //'code'				=> 'required',
            'name'                => 'required',
            'family_id'         => 'required',
            'group_id'          => 'required',
            'subgroup_id'       => 'required',
            'sale_unit_id'      => 'required',
            'package_sale'      => 'required',
            'operation_type_id' => 'required',
            'factor'            => 'required',
            'warehouse_unit_id' => 'required',
            'package_warehouse' => 'required',
            'igv'               => 'required',
            'perception'        => 'required',
            'last_price'        => 'required|min:0|numeric',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function store()
    {
        $this->validateModalForm();

        $id = request('id');
        $company_id = request('company_id');
        $sede_id = request('sede_id');
        $warehouse_type_id = request('warehouse_type_id');

        $code = request('code');
        $code_fab = request('code_fab');
        $name = request('name');
        $family_id = request('family_id');
        $group_id = request('group_id');
        $subgroup_id = request('subgroup_id');
        $sale_unit_id = request('sale_unit_id');
        $package_sale = request('package_sale');
        $operation_type_id = request('operation_type_id');
        $factor = request('factor');
        $warehouse_unit_id = request('warehouse_unit_id');
        $package_warehouse = request('package_warehouse');
        $igv = request('igv');
        $perception = request('perception');
        $stock_good = request('stock_good');
        $stock_minimum = request('stock_minimum');
        $ubication = request('ubication');
        $last_price = request('last_price');
        $price = request('price');

        if (isset($id)) {
            $element = Article::find($id);
            $element->edit = 1;
            $element->stock_good = $stock_good;
            $msg = 'Registro actualizado exitosamente';
            $element->updated_at_user = Auth::user()->user;
        } else {
            $element = new Article();
            $msg = 'Registro creado exitosamente';
            $element->edit = 0;
            $element->stock_good = 0;
            $element->created_at_user = Auth::user()->user;
            $element->updated_at_user = Auth::user()->user;
        }
        $element->warehouse_type_id = $warehouse_type_id;
        $element->code = $code;
        $element->code_fab = $code_fab;
        $element->name = $name;
        $element->package_sale = $package_sale;
        $element->sale_unit_id = $sale_unit_id;
        $element->package_warehouse = $package_warehouse;
        $element->warehouse_unit_id = $warehouse_unit_id;
        $element->operation_type_id = $operation_type_id;
        $element->factor = $factor;
        $element->family_id = $family_id;
        $element->group_id = $group_id;
        $element->subgroup_id = $subgroup_id;
        $element->igv = $igv;
        $element->perception = $perception;

        $element->stock_repair = 0;
        $element->stock_return = 0;
        $element->stock_damaged = 0;
        $element->stock_minimum = ($stock_minimum ? $stock_minimum : 0);
        $element->ubication = $ubication;
        $element->last_price = $last_price;
        $element->price = $price;
        $element->save();

        if (!isset($id)) {
            $cod_1 = WarehouseType::findOrFail($warehouse_type_id);
            $cod_2 = Company::findOrFail($company_id);
            $cod_3 = Sede::findOrFail($sede_id);
            $cod_int = $cod_1->short_name . '-' . $cod_2->short_name . '-' . $cod_3->short_name . '-';

            $element->code_int =  $cod_int . $element->id;
            $element->save();
        }

        $type = 1;
        $title = '¡Ok!';



        return response()->json([
            'type'    => $type,
            'title'    => $title,
            'msg'    => $msg,
        ]);
    }

    public function exportRecord()
    {
        $warehouse_type_id = request('warehouse_type_id');
        // $warehouse_type_id = 1;

        $elements = Article::join('units as sale_units', 'sale_units.id', '=', 'sale_unit_id')
            ->join('units as warehouse_units', 'warehouse_units.id', '=', 'warehouse_unit_id')
            ->join('classifications as families', 'families.id', '=', 'family_id')
            ->join('classifications as groups', 'groups.id', '=', 'group_id')
            ->join('classifications as subgroups', 'subgroups.id', '=', 'subgroup_id')
            ->select(
                'articles.id',
                'code as article_code',
                'articles.name as article_name',
                'articles.last_price as article_price',
                'sale_units.name as sale_unit_name',
                'warehouse_units.name as warehouse_unit_name',
                'package_sale',
                'package_warehouse',
                'families.name as family_name',
                'groups.name as group_name',
                'subgroups.name as subgroup_name',
                'stock_good',
                'stock_repair',
                'stock_return',
                'stock_damaged',
                'ubication',
                'code_int',
                'code_fab'
            )
            ->where('warehouse_type_id', $warehouse_type_id)
            ->orderBy('id', 'desc')
            ->get();

        $warehouse_type_name = WarehouseType::find($warehouse_type_id)->first()->name;
        $datetime = CarbonImmutable::now()->format('d/m/Y h:m:s a');

        $excel = new ArticleReportExport($warehouse_type_name, $datetime, $elements);
        return Excel::download($excel, 'reporte-articulos.xlsx');
    }

    public function getWarehouseType()
    {
        $sede_id = request("sede_id");

        if (Auth::user()->id == 72) {
            $WarehouseTypes = WarehouseType::select('id', 'name')->where('sede_id', '=', $sede_id)->whereIn('id', [23])->get(); // ALMACEN DE PRODUCCION
        } else {
            $WarehouseTypes = WarehouseType::select('id', 'name')->where('sede_id', '=', $sede_id)->get();
        }



        return $WarehouseTypes;
    }

    public function getSede()
    {
        $company_id = request('company_id');

        $sedes = Sede::select('id', 'name')
            ->where('company_id', $company_id)
            ->get();

        return $sedes;
    }
}
