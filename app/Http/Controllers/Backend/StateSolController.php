<?php

namespace App\Http\Controllers\Backend;

use App\AdmPayment;
use App\Aplication;
use App\AplicationDetail;
use App\Http\Controllers\Controller;
use App\Company;
use App\Currency;
use App\AplicationMovementDetail;
use App\AplicationMovement;
use App\AplicationPlate;
use App\AreaAll;
use App\BankAccount;
use App\WarehouseAccountType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StateSolController extends Controller
{
    public function index()
    {

        $companies = Company::select('id', 'name')->get();
        $currencies = Currency::select('id', 'name')->get();
        $bank_accounts = BankAccount::leftjoin('banks', 'banks.id', '=', 'bank_accounts.bank_id')
            ->select(
                'bank_accounts.id',
                'bank_accounts.account_number',
                'banks.short_name as bank_name'
            )
            ->orderBy('bank_name', 'asc')
            ->get();

        $current_date = Carbon::now()->format('Y-m-d');
        $adm_payments = AdmPayment::select('id', 'name')->get();
        $warehouse_account_types = WarehouseAccountType::select('id', 'name')->whereIn('id', [2, 3])->get();

        return view('backend.state_sol')->with(compact('companies', 'currencies', 'bank_accounts', 'warehouse_account_types', 'adm_payments', 'current_date'));
    }

    public function validateForm()
    {
        $messages = [
            'company_id.required'           => 'Debe seleccionar una Compañía.',
            'area_all_id.required'          => 'Debe seleccionar un Area.',
            'adm_payment_id.required'       => 'Debe seleccionar Condición de Pago:.',

        ];

        $rules = [
            'company_id'             => 'required',
            'area_all_id'            => 'required',
            'adm_payment_id'         => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function getAreaAlls()
    {
        $company_id = request('company_id');

        $area_alls = AreaAll::select('id', 'name')
            ->where('company_id', $company_id)
            ->get();

        return $area_alls;
    }

    public function getImportCont()
    {

        $id = request('id');
        $aplication = Aplication::find($id);

        if ($aplication->adm_payment_id == 7) {

            $detail = AplicationMovementDetail::where('aplication_detail_id', $id)
                ->where('state_id', 2)
                ->first();

            if ($detail) {
                return $detail;
            }

            $firstDetail = Aplication::where('id', $id)->first();

            return $firstDetail;
        }

        $element = AplicationMovementDetail::where('aplication_detail_id', $id)
            ->where('state_id', 2)
            ->first();

        return $element;
    }
    public function getImportFi()
    {

        $id = request('id');

        $aplication = Aplication::find($id);

        if ($aplication->adm_payment_id == 7) {
            $element = AplicationMovementDetail::where('aplication_detail_id', $id)->where('state_id', 5)->first();

            if (!$element) {
                $element = AplicationMovementDetail::where('aplication_detail_id', $id)->where('state_id', 3)->first();
            }
        } else {
            $element = AplicationMovementDetail::where('aplication_detail_id', $id)->where('state_id', 3)->first();
        }



        return $element;
    }

    public function list()
    {

        $company_id = request('company_id');
        $area_all_id = request('area_all_id');
        $adm_payment_id = request('adm_payment_id');

        $elements = Aplication::leftjoin('types', 'types.id', '=', 'aplications.type_id')
            ->leftjoin('sub_types', 'sub_types.type_id', '=', 'types.id')
            ->leftjoin('type_reqs', 'aplications.type_req_id', '=', 'type_reqs.id')
            ->leftjoin('adm_payments', 'aplications.adm_payment_id', '=', 'adm_payments.id')
            ->leftjoin('states', 'aplications.state_id', '=', 'states.id')
            ->leftjoin('companies', 'aplications.company_id', '=', 'companies.id')
            ->select(
                'aplications.id as id',
                DB::Raw('DATE_FORMAT(aplications.created_at, "%Y-%m-%d") as fecha_registro'),
                'companies.name as company_name',
                'aplications.num_mov as num_mov',
                'adm_payments.id as adm_payment_id',
                'adm_payments.name as condicion_pago',
                'types.name as tipo',
                'sub_types.name as sub_tipo',
                'type_reqs.name as tipo_req',
                'aplications.import as import',
                'aplications.neto as neto',
                'aplications.state_id as state_id',
                'states.name as name_state'
            )
            ->where('aplications.company_id', $company_id)
            ->where('aplications.area_all_id', $area_all_id)
            ->where('aplications.adm_payment_id', $adm_payment_id)
            /*
            ->when($adm_payment_id == 7, function ($q) {
                return $q->whereIn('aplications.state_id', [5, 2, 6, 7]);
            })
            */
            ->groupBy('aplications.id')
            ->get();

        $id = Auth::user()->id;

        //4,21,60 -> CONTABILIDAD
        $state = in_array($id, [4, 21, 60]) ? 0 : 1;

        $elements->transform(function ($item) use ($state) {
            $item->state = $state;
            return $item;
        });

        return $elements;
    }

    public function verifyRegisterCont()
    {

        $id = request('id');

        $aplication_movement = AplicationMovementDetail::where('aplication_detail_id', $id)->first();

        return $aplication_movement;
    }

    public function detail()
    {
        $id = request('id');

        $element = Aplication::leftjoin('companies', 'company_id', '=', 'companies.id')
            ->leftjoin('area_alls', 'aplications.area_all_id', '=', 'area_alls.id')
            ->leftjoin('adm_payments', 'aplications.adm_payment_id', '=', 'adm_payments.id')
            ->leftjoin('types', 'types.id', '=', 'aplications.type_id')
            ->leftjoin('sub_types', 'sub_types.type_id', '=', 'types.id')
            ->leftjoin('type_reqs', 'aplications.type_req_id', '=', 'type_reqs.id')
            ->select(
                'aplications.id as id',
                'companies.name as company_name',
                'area_alls.name as area_all_name',
                'adm_payments.name as adm_payment_name',
                'types.name as type_name',
                'sub_types.name as sub_type_name',
                'type_reqs.name as type_req_name',
                'aplications.presupuesto as presupuesto',
                'aplications.num_mov as num_mov',
                'aplications.import as import',
                'aplications.state_id as state_id'
            )
            ->where('aplications.id', $id)
            ->first();

        $details = AplicationDetail::leftjoin('costo_pris', 'costo_pris.id', '=', 'aplication_details.costo_pri_id')
            ->leftjoin('costo_secs', 'costo_secs.id', '=', 'aplication_details.costo_sec_id')
            ->leftjoin('voucher_types', 'aplication_details.voucher_type_id', '=', 'voucher_types.id')
            ->leftjoin('currencies', 'aplication_details.currency_id', '=', 'currencies.id')
            ->select(
                'aplication_details.id as id',
                'costo_pris.name as costo_pri_name',
                'costo_secs.name as costo_sec_name',
                'voucher_types.name as voucher_type_name',
                'aplication_details.issue_date as issue_date',
                'aplication_details.expiry_date as expiry_date',
                'aplication_details.num_comprobante as num_comprobante',
                'aplication_details.document_number as document_number',
                'aplication_details.razon_social as razon_social',
                'currencies.name as currency_name',
                'aplication_details.import as import',
                'aplication_details.description as description',
                'aplication_details.plate as plate',
                DB::raw("
                CONCAT(
                    '" . asset('uploads/solicitudes/requerimientos/requerimiento-') . "',
                    aplication_details.id,
                    '.',
                    aplication_details.ext_archivo
                ) AS ruta_archivo
            ")
            )
            ->where('aplication_id', $id)
            ->get();

        foreach ($details as $d) {
            $d->plates = AplicationPlate::select('id', 'client', 'plate')
                ->where('aplication_detail_id', $d->id)
                ->get();
        }

        return response()->json([
            'element' => $element,
            'details' => $details
        ]);
    }

    public function updateCont()
    {

        $id = request('id');
        $state_1 = request('state_1');
        $state_2 = request('state_2');
        $retencion = request('retencion');
        $detraccion = request('detraccion');
        $porcentaje = request('porcentaje');
        $situacion = request('situacion');
        $moneda = request('moneda');
        $import = request('import');
        $comentario = request('comentario');

        $aplication_movement = new AplicationMovementDetail();
        $aplication_movement->aplication_detail_id = $id;
        $aplication_movement->state_1 = $state_1;
        $aplication_movement->state_2 = $state_2;
        $aplication_movement->retencion = $retencion;
        $aplication_movement->detraccion = $detraccion;
        $aplication_movement->porcentaje = $porcentaje;
        $aplication_movement->situacion = $situacion;
        $aplication_movement->moneda = $moneda;
        $aplication_movement->import = $import;
        $aplication_movement->comentario = $comentario;
        $aplication_movement->encargado = 'Contabilidad';
        $aplication_movement->state_id = 2;
        $aplication_movement->created_at_user = Auth::user()->name;

        $aplication_detail = AplicationDetail::find($id);
        $aplication  = Aplication::find($aplication_detail->aplication_id);

        if ($state_1 == 'DESAPROBADO') {
            Log::info($aplication_detail->import);
            $aplication->neto = $aplication->neto -  $aplication_detail->import;
        } else {
            if ($aplication->adm_payment_id == 5) { // POR PAGAR
                $aplication->neto  = $aplication->neto - ($aplication_detail->import - $aplication_movement->import);
            } else if ($aplication->adm_payment_id == 6) { // REEMBOLSO

                $aplication->neto  = $aplication->neto - $aplication_detail->import + $import;
                $aplication_detail->import = $import;
            } else if ($aplication->adm_payment_id == 7) { // POR RNDIR
                $aplication->neto  = $aplication->neto - ($aplication_detail->import - $aplication_movement->import);
            }
        }

        $aplication->save();
        $aplication_detail->save();

        $aplication_movement->save();

        $type = 1;
        $title = "¡Ok!";
        $msg = "Estado actualizado exitosamente.";

        return response()->json([
            'type' => $type,
            'title' => $title,
            'msg' => $msg
        ]);
    }

    public function aprobarCont()
    {

        $id = request('id');

        $aplication = Aplication::find($id);
        $aplication->state_id = 2;
        $aplication->save();

        $type = 1;
        $title = "¡Ok!";
        $msg = "Estado actualizado exitosamente.";

        return response()->json([
            'type' => $type,
            'title' => $title,
            'msg' => $msg
        ]);
    }
    public function desaprobarCont()
    {

        $id = request('id');

        $aplication = Aplication::find($id);
        $aplication->state_id = 8;
        $aplication->save();

        $type = 1;
        $title = "¡Ok!";
        $msg = "Estado actualizado exitosamente.";

        return response()->json([
            'type' => $type,
            'title' => $title,
            'msg' => $msg
        ]);
    }
    public function updateFi()
    {

        $id = request('id');
        $state_1 = request('state_1');
        $medio_pago = request('medio_pago');
        $import = request('import');
        $comentario = request('comentario');
        $state_id = 3;

        $aplication = Aplication::find($id);

        if ($state_1 == 'DESAPROBADO') {
            $state_id = 9;
        } else {
            if ($aplication->adm_payment_id == 7) { // POR RENDIR
                $list = AplicationMovement::where('aplication_id', $id)->where('state_id', 3)->first();
                if ($list) {
                    $state_id = 6;
                } else {
                    $state_id = 3;
                }
            }
        }

        $aplication_movement = new AplicationMovement();
        $aplication_movement->aplication_id = $id;
        $aplication_movement->state_1 = $state_1;
        $aplication_movement->medio_pago = $medio_pago;
        $aplication_movement->import = $import;
        $aplication_movement->comentario = $comentario;
        $aplication_movement->encargado = 'Finanzas';
        $aplication_movement->state_id = $state_id;
        $aplication_movement->created_at_user = Auth::user()->name;
        $aplication_movement->save();

        $aplication->state_id = $state_id;
        $aplication->save();

        $type = 1;
        $title = "¡Ok!";
        $msg = "Estado actualizado exitosamente.";

        return response()->json([
            'type' => $type,
            'title' => $title,
            'msg' => $msg
        ]);
    }

    public function updateTes()
    {

        $id = request('id');
        $state_2 = request('state_2');
        $medio_pago = request('medio_pago');
        $bank_account_id = request('bank_account_id');
        $fecha_pago = request('fecha_pago');
        $import = request('import');
        $comentario = request('comentario');
        $state_id = 4;

        $aplication = Aplication::find($id);

        if ($state_2 == 'RECHAZADO') {
            $state_id = 9;
        } else {
            if ($aplication->adm_payment_id == 7) { // POR RENDIR
                $list = AplicationMovement::where('aplication_id', $id)->where('state_id', 4)->first();
                if ($list) {
                    $state_id = 7;
                } else {
                    $state_id = 4;
                }
            }
        }

        $aplication_movement = new AplicationMovement();
        $aplication_movement->aplication_id = $id;
        $aplication_movement->state_2 = $state_2;
        $aplication_movement->medio_pago = $medio_pago;
        $aplication_movement->bank_account_id = $bank_account_id;
        $aplication_movement->fecha_pago = Carbon::parse($fecha_pago)->format('Y-m-d');
        $aplication_movement->import = $import;
        $aplication_movement->comentario = $comentario;
        $aplication_movement->encargado = 'Tesoreria';
        $aplication_movement->state_id = $state_id;
        $aplication_movement->created_at_user = Auth::user()->name;
        $aplication_movement->save();

        $aplication->state_id = $state_id;
        $aplication->save();

        $type = 1;
        $title = "¡Ok!";
        $msg = "Estado actualizado exitosamente.";

        return response()->json([
            'type' => $type,
            'title' => $title,
            'msg' => $msg
        ]);
    }
}
