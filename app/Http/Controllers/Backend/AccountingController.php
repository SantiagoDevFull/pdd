<?php

namespace App\Http\Controllers\Backend;

use App\Article;
use App\ContPrice;
use App\Exports\AccountingReportExport;
use App\Exports\SecurityReportExport;
use App\Http\Controllers\Controller;
use App\User;
use App\WarehouseMovement;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class AccountingController extends Controller
{
    public function index()
    {
        $max_datetime = Carbon::now()->toDateString();
        $articles = Article::select('id', 'name')
            ->where('warehouse_type_id', 5)
            ->where('group_id', 26)
            ->where('presentation', 10)
            ->orderBy('presentation', 'asc')
            ->get();

        return view('backend.accounting')->with(compact('max_datetime', 'articles'));
    }

    public function validateForm()
    {
        $messages = [
            'article_id.required'        => 'Debe seleccionar el artículo.',
            'price.required'             => 'Debe ingresar el precio.',
            'price.numeric'              => 'Debe ingresar un precio válido.',
            'initial_date.required'      => 'Debe ingresar la fecha de inicio.',
            'final_date.required'        => 'Debe ingresar la fecha final.',
            'state.required'             => 'Debe seleccionar el estado.'
        ];

        $rules = [
            'article_id'            => 'required',
            'price'                 => 'required',
            'price'                 => 'numeric',
            'initial_date'          => 'required',
            'final_date'            => 'required',
            'state'                 => 'required'
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function store()
    {
        $this->validateForm();

        $id = request('id');
        $article_id = request('article_id');
        $price = request('price');
        $initial_date = Carbon::parse(request('initial_date'))->format('Y-m-d');
        $final_date = Carbon::parse(request('final_date'))->format('Y-m-d');
        $state = request('state');

        if (isset($id)) {
            $element = ContPrice::findOrFail($id);
            $msg = 'Precio actualizado exitosamente.';
        } else {
            $element = new ContPrice();
            $msg = 'Precio registrado exitosamente.';
        }
        $element->article_id = $article_id;
        $element->price = $price;
        $element->initial_date = $initial_date;
        $element->final_date = $final_date;
        $element->state = $state;
        $element->created_at_user = Auth::user()->name;
        $element->updated_at_user = Auth::user()->name;
        $element->save();

        $type = 1;
        $title = 'Bien';

        return response()->json([
            'type'  => $type,
            'title' => $title,
            'msg'   => $msg
        ]);
    }

    public function list()
    {

        $cont_prices = ContPrice::leftjoin('articles', 'articles.id', '=', 'cont_prices.article_id')
            ->select(
                'cont_prices.id',
                'articles.name as article_name',
                'cont_prices.price',
                'cont_prices.initial_date',
                'cont_prices.final_date',
                DB::Raw("CASE WHEN cont_prices.state = 0 THEN 'Inactivo' ELSE 'Activo' END AS state")

            )
            ->orderBy('id', 'desc')
            ->get();

        return $cont_prices;
    }

    public function delete()
    {
        $element = ContPrice::findOrFail(request('id'));
        $element->delete();
    }

    public function detail()
    {
        $element = ContPrice::select(
            'id',
            'article_id',
            'price',
            'initial_date',
            'final_date',
            'state'
        )
            ->findOrFail(request('id'));
        return $element;
    }

    public function export()
    {
        $elements = ContPrice::select(
            'id',
            'price',
            'initial_date',
            'final_date',
            DB::raw("CASE WHEN state = 0 THEN 'Inactivo' ELSE 'Activo' END AS state")
        )
            ->orderBy('id', 'desc')
            ->get();
        $datetime = CarbonImmutable::now()->format('d/m/Y h:i:s a');

        $excel = new AccountingReportExport($datetime, $elements);
        return Excel::download($excel, 'reporte-precios-contabilidad.xlsx');
    }
}
