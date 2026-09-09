<?php

namespace App\Http\Controllers\Backend;

use App\Guide;
use App\GuidesDetail;
use App\Http\Controllers\Controller;
use App\WarehouseMovement;
use App\WarehouseMovementDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class GuideEditController extends Controller
{
    public function index()
    {
        return view('backend.guide_edit');
    }

    public function list()
    {

        $guias_elect_cilindros = Guide::leftjoin('companies', 'companies.id', '=', 'guides.company_id')
            ->leftjoin('vehicles', 'vehicles.id', '=', 'guides.vehicle_id')
            ->leftjoin('movent_types', 'movent_types.id', '=', 'guides.movement_type_id')
            ->leftjoin('client_routes', 'client_routes.id', '=', 'guides.route_id')
            ->leftjoin('client_channels', 'client_routes.channel_id', '=', 'client_channels.id')
            ->select(
                'guides.id as guide_id',
                'companies.short_name as company_name',
                'movent_types.name as movement_type',
                'guides.traslate_date as fecha_despacho',
                'client_routes.short_name as ruta',
                'client_channels.name as sub_canal',
                'guides.account_name as chofer',
                'guides.scop_number as scop',
                'vehicles.plate as placa_principal',
                DB::Raw('date_format(guides.created_at, "%Y-%m-%d") as fecha_remision'),
                DB::Raw('CONCAT(guides.referral_guide_series,"-", guides.referral_guide_number) as guia'),
                DB::Raw('CONCAT(guides.referral_serie_number,"-", guides.referral_voucher_number) as guia_fisica'),
                'guides.close as cerrado'
            )
            ->where('guides.company_id', 1)
            ->whereIn('guides.movement_type_id', [11, 29])
            ->orderBy('guides.id', 'desc')
            ->get();


        return $guias_elect_cilindros;
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

        $guide_id = request('guide_id');

        $guide = Guide::select(
            'id as guide_id',
            'referral_serie_number',
            'referral_voucher_number',
            DB::Raw('CONCAT(referral_guide_series,"-", referral_guide_number) as guia'),
            DB::Raw('CONCAT(guides.referral_serie_number,"-", guides.referral_voucher_number) as guia_fisica'),
            'account_name',
            'scop_number'
        )
            ->findOrFail($guide_id);

        $movement = WarehouseMovement::where('referral_guide_series', $guide->referral_serie_number)
            ->where('referral_guide_number', $guide->referral_voucher_number)
            ->first();


        $guides_detail = WarehouseMovementDetail::leftjoin('articles', 'articles.id', '=', 'warehouse_movement_details.article_code')
            ->select(
                'warehouse_movement_details.id as detail_id',
                'warehouse_movement_details.item_number as item',
                'articles.name as article_name',
                DB::raw('ROUND(COALESCE(warehouse_movement_details.digit_amount, 0), 0) as digit_amount'),
                DB::raw('ROUND(COALESCE(warehouse_movement_details.new_stock_return, 0), 0) as new_stock_return'),
                DB::raw('ROUND(COALESCE(warehouse_movement_details.digit_amount, 0) - COALESCE(warehouse_movement_details.new_stock_return, 0), 0) as cantidad')
            )
            ->where('warehouse_movement_details.warehouse_movement_id', $movement->id)
            ->get();

        return response()->json([
            'guide'    => $guide,
            'detail'    => $guides_detail
        ]);
    }
    public function update()
    {

        $guide_id = request('guide_id');
        $scop_number = request('scop_number');
        $finished = request('finished');

        $guide = Guide::findOrFail($guide_id);
        $guide->scop_number = $scop_number;
        $guide->updated_at_user = Auth::user()->user;

        if ($finished) {
            $guide->close = 1;
        }

        $guide->update();

        $warehouse_movement = WarehouseMovement::where('referral_guide_series', $guide->referral_serie_number)->where('referral_guide_number', $guide->referral_voucher_number)->first();
        $warehouse_movement->scop_number = $scop_number;
        $warehouse_movement->updated_at_user = Auth::user()->user;;
        $warehouse_movement->update();

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
