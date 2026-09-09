<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\WarehouseMovement;
use App\Efective;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RegisterEfectiveController extends Controller
{

    public function showRegisterEfective()
    {

        $date = CarbonImmutable::now()->startOfDay();

        $data = [
            'min_datetime' => $date->subDays(1)->startOfDay()->toAtomString(),
            'max_datetime' => $date->startOfDay()->toAtomString()
        ];

        return view('backend.register_efective', $data);
    }

    public function list()
    {


        $hoy = Carbon::today()->toDateString();
        $ayer = Carbon::yesterday()->toDateString();

        $efectives = Efective::join('warehouse_movements', 'warehouse_movements.id', '=', 'efectives.warehouse_movement_id')
            ->join('companies', 'companies.id', '=', 'warehouse_movements.company_id')
            ->select(
                'efectives.id as id',
                'efectives.bank_date as bank_date',
                'companies.name as company_name',
                DB::raw("CONCAT(
				warehouse_movements.referral_guide_series,
				'-', warehouse_movements.referral_guide_number,
				' | ', warehouse_movements.license_plate,
				' | ', warehouse_movements.traslate_date
				) as nro_parte"),
                'efectives.total as total'
            )
            ->whereIn('efectives.bank_date', [$hoy, $ayer])
            ->orderBy('efectives.id', 'desc')
            ->get();

        return response()->json($efectives);
    }

    public function getWarehouseMovements()
    {

        $hoy = Carbon::today()->toDateString();
        $ayer = Carbon::now()->subDays(11)->toDateString();

        $q = request('q');

        $elements = WarehouseMovement::select('id', 'movement_number', 'referral_guide_series', 'referral_guide_number', 'license_plate', 'traslate_date')
            ->when($q, function ($query) use ($q) {
                return $query->where('referral_guide_number', 'like',  $q);
            })
            ->where('warehouse_type_id', 5)
            ->whereIn('movement_type_id', [11, 12])
            ->whereBetween('warehouse_movements.traslate_date', [$ayer, $hoy])
            ->orderBy('movement_number', 'asc')
            ->get();

        $elements->map(function ($item, $index) {
            $item->text = $item->referral_guide_series . '-' . $item->referral_guide_number . ' | ' . $item->license_plate . ' | ' . $item->traslate_date;
            unset($item->movement_number);
            unset($item->referral_guide_series);
            unset($item->referral_guide_number);
            unset($item->license_plate);
            unset($item->traslate_date);
            return $item;
        });

        return $elements;
    }

    public function validateForm()
    {
        $messages = [
            'bank_date.required' => 'Debe seleccionar fecha',
            'warehouse_movement_id.required' => 'Debe seleccionar movimiento de almacén',
            'total.required' => 'Debe ingresar el total',
        ];

        $rules = [
            'bank_date' => 'required',
            'warehouse_movement_id' => 'required',
            'total' => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function registerEfective()
    {

        $this->validateForm();

        $bank_date = Carbon::parse(request('bank_date'))->format('Y-m-d');
        $warehouse_movement_id = request('warehouse_movement_id');
        $total = request('total');

        $warehouse_movement = WarehouseMovement::find(
            $warehouse_movement_id,
            ['id', 'company_id', 'referral_guide_series', 'referral_guide_number', 'scop_number', 'license_plate', 'state', 'traslate_date']
        );

        $deposit = new Efective();
        $deposit->company_id = $warehouse_movement->company_id;
        $deposit->bank_date = $bank_date;
        $deposit->warehouse_movement_id = $warehouse_movement->id;
        $deposit->guia_date =  date('Y-m-d', strtotime($warehouse_movement->traslate_date));
        $deposit->guide_number = $warehouse_movement->referral_guide_number;
        $deposit->serie_number = $warehouse_movement->referral_guide_series;
        $deposit->total = $total;
        $deposit->total_pend = $total;
        $deposit->created_at_user = Auth::user()->name;
        $deposit->save();

        $type = 1;
        $title = 'Registro de Efectivo';
        $msg = 'Efectivo registrado correctamente';


        return response()->json([
            'type' => $type,
            'title' => $title,
            'msg' => $msg
        ]);
    }

    public function delete()
    {

        $id = request('id');

        Efective::find($id)->delete();

        return response()->json(null, 200);
    }

    public function detail()
    {
        $id = request('id');

        $detail = Efective::find($id);
        $warehouse_movement = WarehouseMovement::select(
            'id',
            DB::raw("CONCAT(referral_guide_series, '-', referral_guide_number, ' | ', license_plate, ' | ', traslate_date) as text")
        )
            ->where('id', $detail->warehouse_movement_id)
            ->first();

        return response()->json([
            'detail' => $detail,
            'warehouse_movement' => $warehouse_movement
        ]);
    }

    public function validateModalForm()
    {
        $messages = [
            'bank_date.required' => 'Debe seleccionar fecha',
            'warehouse_movement_id_2.required' => 'Debe seleccionar movimiento de almacén',
            'total.required' => 'Debe ingresar el total',
        ];

        $rules = [
            'bank_date' => 'required',
            'warehouse_movement_id_2' => 'required',
            'total' => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function update()
    {

        $this->validateModalForm();

        $id = request('id');
        $bank_date = Carbon::parse(request('bank_date'))->format('Y-m-d');
        $warehouse_movement_id = request('warehouse_movement_id_2');
        $total = request('total');

        $warehouse_movement = WarehouseMovement::find(
            $warehouse_movement_id,
            ['id', 'company_id', 'referral_guide_series', 'referral_guide_number', 'scop_number', 'license_plate', 'state', 'traslate_date']
        );

        $deposit = Efective::find($id);
        $deposit->company_id = $warehouse_movement->company_id;
        $deposit->bank_date = $bank_date;
        $deposit->warehouse_movement_id = $warehouse_movement->id;
        $deposit->guia_date =  date('Y-m-d', strtotime($warehouse_movement->traslate_date));
        $deposit->guide_number = $warehouse_movement->referral_guide_number;
        $deposit->serie_number = $warehouse_movement->referral_guide_series;
        $deposit->total = $total;
        $deposit->total_pend = $total;
        $deposit->updated_at_user = Auth::user()->name;
        $deposit->save();

        $type = 1;
        $title = 'Registro de Efectivo';
        $msg = 'Registro de efectivo actualizado correctamente';


        return response()->json([
            'type' => $type,
            'title' => $title,
            'msg' => $msg
        ]);
    }
}
