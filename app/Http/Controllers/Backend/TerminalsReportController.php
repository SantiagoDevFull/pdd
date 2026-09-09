<?php

namespace App\Http\Controllers\Backend;

use App\Company;
use App\Exports\TerminalsReportExport;
use App\Http\Controllers\Controller;
use App\MoventClass;
use App\MoventType;
use App\WarehouseAccountType;
use App\WarehouseDocumentType;
use App\WarehouseMovement;
use App\WarehouseMovementDetail;
use App\WarehouseType;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use stdClass;

class TerminalsReportController extends Controller
{
    public function index()
    {
        $movement_classes = MoventClass::select('id', 'name')->get();
        $movement_types = MoventType::select('id', 'movent_class', 'name')->get();

        $warehouse_types = WarehouseType::select('id', 'name')->get();
        $warehouse_account_types = WarehouseAccountType::select('id', 'name')->get();
        $warehouse_document_types = WarehouseDocumentType::select('id', 'name')->get();

        $companies = Company::select('id', 'name')->get();
        $current_date_one = date(DATE_ATOM, strtotime('first day of this month'));
        $current_date = date(DATE_ATOM, mktime(0, 0, 0));

        return view('backend.terminals_report')->with(compact(
            'movement_classes',
            'movement_types',
            'warehouse_types',
            'companies',
            'current_date',
            'current_date_one',
            'warehouse_account_types',
            'warehouse_document_types'
        ));
    }

    public function validateForm()
    {
        $messages = [
            'company_id.required'                    => 'Debe seleccionar una Compañia.',
            'initial_date.required'                    => 'Debe seleccionar una Fecha Inicial.',
            'final_date.required'                    => 'Debe seleccionar una Fecha Final.',
        ];

        $rules = [
            'company_id'        => 'required',
            'initial_date'        => 'required',
            'final_date'        => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function delete()
    {
        $id = request('id');

        WarehouseMovementDetail::where('warehouse_movement_id', $id)->delete();
        WarehouseMovement::find($id)->delete();
    }

    public function list()
    {
        $export = request('export');

        $initial_date = date_create(request('model.initial_date') . ' 00:00:00');
        $final_date = date_create(request('model.final_date') . ' 23:59:59');
        $company_id = request('model.company_id');
        $initial_date = date_format($initial_date, 'Y-m-d H:i:s');
        $final_date = date_format($final_date, 'Y-m-d H:i:s');

        $q = request('query');
        $search = $q['generalSearch'];

        $movements = WarehouseMovement::select(
            'id',
            'company_id',
            'movement_class_id',
            'movement_type_id',
            'warehouse_type_id',
            'movement_stock_type_id',
            'movement_number',
            'created_at',
            'warehouse_account_type_id',
            'account_id',
            'account_document_number',
            'account_name',
            'license_plate_2',
            'mezcla',
            'origin',
            'referral_guide_series',
            DB::Raw('CONCAT(referral_serie_number, "-", referral_voucher_number) as factura'),
            'referral_guide_number',
            'referral_voucher_number',
            'traslate_date',
            'scop_number',
            'license_plate',
            'state'
        )
            ->whereIn('warehouse_type_id', [8, 9, 10, 11, 20, 24])
            ->where('movement_class_id', 2)
            ->whereIn('movement_type_id', [30, 31])

            ->when($search, function ($query, $search) {
                return $query->where('referral_guide_series', '=', /*'%'.*/ $search/*.'%'*/)
                    ->orWhere('scop_number', '=', /*'%'.*/ $search/*.'%'*/);
            })

            ->where('created_at', '>=', $initial_date)
            ->where('created_at', '<=', $final_date)
            ->where('company_id', $company_id)
            ->orderBy('company_id', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        $movement_details = collect([]);


        $facturas = $movements->pluck('factura')->filter()->unique();

        // 2. Agrupamos $resumen por la concatenación
        $resumen = WarehouseMovement::where('movement_class_id', 1)
            ->whereIn(DB::raw('CONCAT(referral_serie_number, "-", referral_voucher_number)'), $facturas)
            ->select(
                DB::raw('CONCAT(referral_serie_number, "-", referral_voucher_number) as factura'),
                DB::raw('SUM(total) as total'),
                DB::raw('SUM(tc) as tc'),
                DB::raw('SUM(referral_guide_number) as pedido')
            )
            ->groupBy(DB::raw('CONCAT(referral_serie_number, "-", referral_voucher_number)'))
            ->get()
            ->keyBy('factura');

        // 3. Agrupamos $cantidades por la concatenación
        $cantidades = WarehouseMovement::leftJoin(
            'warehouse_movement_details',
            'warehouse_movements.id',
            '=',
            'warehouse_movement_details.warehouse_movement_id'
        )
            ->where('warehouse_movements.movement_class_id', 1)
            ->whereIn(DB::raw('CONCAT(warehouse_movements.referral_serie_number, "-", warehouse_movements.referral_voucher_number)'), $facturas)
            ->select(
                DB::raw('CONCAT(warehouse_movements.referral_serie_number, "-", warehouse_movements.referral_voucher_number) as factura'),
                DB::raw('SUM(warehouse_movement_details.converted_amount) as cantidad')
            )
            ->groupBy(DB::raw('CONCAT(warehouse_movements.referral_serie_number, "-", warehouse_movements.referral_voucher_number)'))
            ->get()
            ->keyBy('factura');
        $movements->map(function ($item, $index) use ($movement_details, $export, $resumen, $cantidades) {

            $ref = $item->factura;

            $total    = $resumen[$ref]->total ?? 0;
            $tc       = $resumen[$ref]->tc ?? 0;
            $pedido   = $resumen[$ref]->pedido ?? 0;
            $cantidad = $cantidades[$ref]->cantidad ?? 0;

            $item->warehouse_movement_details->map(function ($detail, $detailIndex) use ($item, $movement_details, $export, $total, $tc, $pedido, $cantidad) {

                $detail->article;
                $detail->cantidad += $cantidad;
                $detail->total += $total;
                $detail->pedido_m += $pedido;
                $detail->factura = $item->factura;
                $detail->quantity = $detail->converted_amount;
                $detail->warehouse_movement_id = $item->id;
                $detail->company_short_name = $item->company->short_name;
                $detail->movement_class = $item->movement_class->name;
                $detail->movement_type = $item->movement_type->name;
                $detail->warehouse_name = $item->warehouse_type->name;
                $detail->convertion = $item->warehouse_type->convertion;
                $detail->aprov = $detail->quantity * $detail->convertion;
                $detail->warehouse_short_name = $item->warehouse_type->short_name;
                $detail->movement_number = $item->movement_number;

                if ($detail->cantidad != 0) {
                    //$detail->price_kg=$cantidad;
                    $detail->price_kg = $total * $tc / $cantidad;
                    //$detail->price_kg = $total*$tc/$detail->cantidad;
                } else {
                    $detail->price_kg = 0;
                }
                if ($detail->cantidad != 0) {
                    $detail->soles = $detail->quantity * $detail->price_kg;
                } else {
                    $detail->soles = 0;
                }
                if ($detail->cantidad != 0) {
                    $detail->price_dol = $total / $cantidad * 1000;
                } else {
                    $detail->price_dol = 0;
                }
                if ($detail->cantidad != 0) {
                    $detail->sub_total = $detail->quantity * ($total * $tc / $cantidad) / 1.18;
                } else {
                    $detail->sub_total = 0;
                }


                $detail->igv = $detail->soles - $detail->sub_total;
                $detail->date = date('d-m-Y', strtotime($item->created_at));
                $detail->article_code = $detail->article->code;

                if ($item->origin != 27) {
                    $detail->article_name = 'E';
                } else {
                    $detail->article_name = 'G';
                }


                $detail->movement_stock_type = ($item->movement_stock_type_id ? $item->movement_stock_type->name : '');
                $detail->account_document_number = $item->account_document_number;
                $detail->account_name = $item->account_name;
                $detail->referral_guide = $item->referral_guide_number;
                $detail->scop_number = $item->scop_number;
                $detail->tracto = $item->license_plate_2;
                $detail->mezcla = $item->mezcla;
                $detail->pedido_fact = $item->referral_guide_series;
                $detail->destiny = 'PLANTA';
                $detail->license_plate = $item->license_plate;
                $detail->tm = $detail->quantity / 1000;

                if ($detail->cantidad != 0) {
                    $detail->pond = ($detail->converted_amount * ($total * $tc / $cantidad)) / $detail->tm;
                } else {
                    $detail->pond = 0;
                }

                $detail->traslate_date = $item->traslate_date;
                $detail->old_stock = $detail->old_stock_return;
                $detail->old_stock_r = $detail->old_stock_damaged;
                $detail->peso_neto = $detail->old_stock_r - $detail->old_stock;
                $detail->dif = $detail->peso_neto - $detail->quantity;

                $detail->concat = $detail->warehouse_name . "_" . $detail->warehouse_short_name . $detail->article_name;

                if (Auth::user()->user == 'comercial4' || Auth::user()->user == 'comercial1' || Auth::user()->user == 'admin' || Auth::user()->user == 'sistemas1') {
                    $detail->state = $item->state;
                } else {
                    $detail->state = 1;
                }
                $movement_details->push($detail);
            });
        });
        if ($export) {
            $data = new TerminalsReportExport($movement_details);
            $file = Excel::download($data, 'reporte-movimientos-de-abastecimientos-glp' . time() . '.xls');
            return $file;
        } else {
            return $movement_details;
        }
    }

    public function detail()
    {
        $id = request('id');
        $element = WarehouseMovement::join('warehouse_movement_details', 'warehouse_movement_details.warehouse_movement_id', 'warehouse_movements.id')
            ->select(
                'warehouse_movements.id',
                'warehouse_movements.created_at',
                'warehouse_movements.movement_type_id',
                'warehouse_movements.account_id',
                'warehouse_movements.referral_guide_series',
                'warehouse_movements.referral_guide_number',
                'warehouse_movements.referral_serie_number',
                'warehouse_movements.referral_voucher_number',
                'warehouse_movements.scop_number',
                'warehouse_movements.license_plate_2',
                'warehouse_movements.license_plate',
                'warehouse_movements.traslate_date',
                'warehouse_movements.price_mes',
                'warehouse_movement_details.converted_amount',
                'warehouse_movement_details.digit2'
            )
            ->where('warehouse_movements.id', $id)
            ->first();

        $date_emi = CarbonImmutable::createFromDate(date('Y-m-d', strtotime($element->created_at)));
        $date = CarbonImmutable::createFromDate(date('Y-m-d', strtotime($element->traslate_date)));
        $element->date = $date->startOfDay()->toAtomString();
        $element->date_emi = $date_emi->startOfDay()->toAtomString();
        $element->min_datetime = $date->startOfDay()->subDays(2)->toAtomString();
        $element->max_datetime = $date->startOfDay()->addDays(2)->toAtomString();
        $element->typing_error = $element->movement_type_id == 29 ? 1 : 0;

        return $element;
    }

    public function validateModalEdit()
    {
        $messages = [
            'digit2.required'                    => 'Debe ingresar un valor.',
        ];

        $rules = [
            'digit2'                            => 'required',
        ];
        request()->validate($rules, $messages);
        return request()->all();
    }

    public function update()
    {

        $this->validateModalEdit();

        $id = request('id');
        $account_id = request('account_id');
        $article_code = request('article_code');
        $referral_guide_series = request('referral_guide_series');
        $referral_guide_number = request('referral_guide_number');
        $referral_serie_number = request('referral_serie_number');
        $referral_voucher_number = request('referral_voucher_number');
        $scop_number = request('scop_number');
        $license_plate = request('license_plate');
        $license_plate_2 = request('license_plate_2');
        $price_mes = request('price_mes');
        $date = request('date');
        $date_emi = request('date_emi');
        $typing_error = request('typing_error');
        $digit2 = request('digit2');

        $compra = WarehouseMovement::where('referral_serie_number', $referral_serie_number)
            ->where('referral_voucher_number', $referral_voucher_number)
            ->where('movement_type_id', 1) //compra
            ->first();

        //$detalle = WarehouseMovementDetail::where('warehouse_movement_id', $compra->id)->first();

        $element = WarehouseMovement::findOrFail($id);
        $element->referral_guide_series = $referral_guide_series;
        $element->referral_guide_number = $referral_guide_number;
        $element->referral_serie_number = $referral_serie_number;
        $element->referral_voucher_number = $referral_voucher_number;
        $element->scop_number = $scop_number;
        $element->license_plate = $license_plate;
        $element->license_plate_2 = $license_plate_2;

        $element->cost_glp = $compra->cost_glp;

        $element->price_año = ($price_mes == '01' && $price_mes != $element->price_mes) ? $element->price_año + 1 : $element->price_año;
        $element->price_mes = $price_mes;

        $det = WarehouseMovementDetail::where('warehouse_movement_id', $id)->first();
        $det->digit1 =  $det->converted_amount - $digit2;
        $det->digit2 =  $digit2;
        $det->update();

        if ($typing_error) {
            $element->movement_type_id = 29;
            $element->action_type_id = null;
        }

        $element->soles_1 = $det->digit1 * $element->cost_glp;
        $element->soles_2 = $det->digit2 * $element->cost_glp;
        $element->total = $det->converted_amount * $element->cost_glp;
        $element->igv = $element->total * 0.18;

        $element->traslate_date = date('Y-m-d', strtotime($date));
        $element->created_at = date('Y-m-d', strtotime($date_emi));
        $element->save();

        $data = new stdClass();
        $data->type = 1;
        $data->title = '¡Ok!';
        $data->msg = 'Registro actualizado exitosamente.';

        return response()->json($data);
    }
}
