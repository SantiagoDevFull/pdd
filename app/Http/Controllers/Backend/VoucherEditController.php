<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Voucher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class VoucherEditController extends Controller
{
    public function index()
    {


        return view('backend.voucher_edit');
    }

    public function list()
    {

        $vouchers = Voucher::leftjoin('companies', 'companies.id', '=', 'vouchers.company_id')
            ->leftjoin('voucher_types', 'voucher_types.id', '=', 'vouchers.voucher_type_id')
            ->leftjoin('payments', 'payments.id', '=', 'vouchers.payment_id')
            ->select(
                'vouchers.id as voucher_id',
                'companies.name as company_name',
                'vouchers.client_name as cliente',
                'voucher_types.name as voucher_type_name',
                DB::raw('CONCAT(vouchers.serie_number,"-",vouchers.voucher_number) as factura'),
                'vouchers.issue_date as emision',
                'vouchers.expiry_date as venc',
                'payments.name as payment_name',
                'vouchers.total_perception as total',
                'vouchers.scop_number as scop'
            )
            ->whereIn('vouchers.voucher_type_id', [1, 5])
            ->whereIn('vouchers.ose', [0, 1])
            ->orderBy('vouchers.id', 'desc')
            ->get();


        return $vouchers;
    }

    public function validateModalForm()
    {
        $messages = [
            'client_id.required'            => 'Debe seleccionar un Cliente.',
        ];

        $rules = [
            'client_id'                        => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }



    public function detail()
    {

        $voucher_id = request('voucher_id');
        $voucher = Voucher::leftjoin('voucher_types', 'voucher_types.id', '=', 'vouchers.voucher_type_id')
            ->select(
                'vouchers.id as voucher_id',
                'voucher_types.name as voucher_type_name',
                DB::raw('CONCAT(vouchers.serie_number,"-",vouchers.voucher_number) as factura'),
                'vouchers.client_name as client_name',
                'vouchers.scop_number as scop_number'
            )
            ->findOrFail($voucher_id);

        return $voucher;
    }
    public function update()
    {

        $voucher_id = request('voucher_id');
        $scop_number = request('scop_number');

        $voucher = Voucher::findOrFail($voucher_id);
        $voucher->scop_number = $scop_number;
        $voucher->user = Auth::user()->user;;
        $voucher->update();

        $type = 1;
        $title = '¡Ok!';
        $msg = 'Registro actualizado exitosamente.';

        return response()->json([
            'type'    => $type,
            'title'    => $title,
            'msg'    => $msg,
        ]);
    }
}
