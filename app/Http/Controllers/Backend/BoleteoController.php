<?php

namespace App\Http\Controllers\Backend;

use App\Article;
use App\Http\Controllers\Controller;
use App\Voucher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BoleteoController extends Controller
{
    public function index()
    {

        $articles = Article::select('id', 'name')
            ->where('warehouse_type_id', 5)
            ->where('group_id', 26)
            ->orderBy('presentation', 'asc')
            ->get();

        $current_date = date(DATE_ATOM, strtotime('-1 day', mktime(0, 0, 0)));

        return view('backend.boleteo')->with(compact('articles', 'current_date'));
    }

    public function validateForm()
    {

        $messages = [
            'initial_date.required'           => 'Debe seleccionar una fecha de Inicio.',
            'final_date.required'             => 'Debe seleccionar una fecha Final.',
        ];

        $rules = [
            'initial_date' => 'required',
            'final_date' => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function list()
    {


        $initial_date = date('Y-m-d', strtotime(request('model.initial_date')));
        $final_date = date('Y-m-d', strtotime(request('model.final_date')));
        $article_id = request('model.article_id');

        $vouchers = Voucher::join('voucher_details', 'voucher_details.voucher_id', '=', 'vouchers.id')
            ->join('voucher_types', 'voucher_types.id', '=', 'vouchers.voucher_type_id')
            ->join('articles', 'articles.id', '=', 'voucher_details.article_id')
            ->where('vouchers.voucher_type_id', 2)
            ->whereIn('vouchers.serie_number', ['B004', 'B022','B038'])
            ->when($article_id, function ($query, $article_id) {
                return $query->where('voucher_details.article_id', $article_id);
            })
            ->whereBetween('vouchers.issue_date', [$initial_date, $final_date])
            ->select(
                'vouchers.id as voucher_id',
                'vouchers.issue_date as fecha_emision',
                'voucher_types.name as voucher_type_name',
                'voucher_details.article_id as article_cod',
                'voucher_details.name as article_name',
                'vouchers.serie_number as serie',
                DB::Raw('MIN(vouchers.voucher_number) as min_voucher'),
                DB::Raw('MAX(vouchers.voucher_number) as max_voucher'),
                DB::raw('SUM(voucher_details.quantity) as total_cantidad'),
                DB::raw('SUM(voucher_details.quantity * articles.convertion) as total_kilos'),
                DB::raw('SUM(voucher_details.total) as total_venta'),
                'voucher_details.original_price as price_unit'
            )
            ->groupBy('article_name','serie')
            ->orderBy('vouchers.serie_number')
            ->get();


        return response()->json([
            'data' => $vouchers,
        ]);
    }


    public function validateModalForm()
    {
        $messages = [
            'price_kg.required'                => 'El Precio es obligatorio.',
            'price_kg.numeric'                 => 'El Precio tiene que ser un número.',
            'price_kg.min'                     => 'El Precio mínimo es 0.0001',
        ];

        $rules = [
            'price_kg'                         => 'required|numeric|min:0.0001'
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function store()
    {
        $this->validateModalForm();

        $issue_date = date('Y-m-d', strtotime(request('issue_date')));
        $serie = request('serie');
        $min_voucher = request('min_voucher');
        $max_voucher = request('max_voucher');
        $price_kg = request('price_kg');
        $terminal_date = request('terminal_date');

        $vouchers = Voucher::join('voucher_details', 'voucher_details.voucher_id', '=', 'vouchers.id')
            ->select(
                'vouchers.id as voucher_id',
                'vouchers.total_perception',
                'vouchers.total',
                'vouchers.taxed_operation',
                'vouchers.igv',
                'vouchers.user',
                'voucher_details.id as detail_id',
                'voucher_details.quantity',
                'voucher_details.original_price',
                'voucher_details.unit_price',
                'voucher_details.sale_value',
                'voucher_details.total as detail_total',
                'voucher_details.igv as detail_igv',
                'voucher_details.user as detail_user'
            )
            ->where('vouchers.voucher_type_id', 2)
            ->where('vouchers.serie_number', $serie)
            ->whereBetween('vouchers.voucher_number', [$min_voucher, $max_voucher])
            ->where('vouchers.issue_date', $issue_date)
            ->get();


        foreach ($vouchers as $obj) {
            $voucher_total = $price_kg * $obj->quantity;
            $voucher_taxed_operation = $voucher_total / 1.18;
            $voucher_igv = $voucher_total - $voucher_taxed_operation;
            $user_id = Auth::id();

            $updateData = [
                'total_perception' => $voucher_total,
                'total' => $voucher_total,
                'taxed_operation' => $voucher_taxed_operation,
                'igv' => $voucher_igv,
                'user' => $user_id,
            ];

            if (!empty($terminal_date)) {

                $terminal_date = date('Y-m-d', strtotime(request('terminal_date')));
                $updateData['issue_date'] = $terminal_date;
            }

            Voucher::where('id', $obj->voucher_id)->update($updateData);

            DB::table('voucher_details')->where('id', $obj->detail_id)->update([
                'original_price' => $price_kg,
                'unit_price' => $price_kg / 1.18,
                'sale_value' => $price_kg,
                'total' => $voucher_total,
                'igv' => $voucher_igv,
                'user' => $user_id,
            ]);
        }

        $msg = 'Registro actualizado exitosamente';
        $type = 1;
        $title = '¡Ok!';

        return response()->json([
            'type'    => $type,
            'title'    => $title,
            'msg'    => $msg,
        ]);
    }
}
