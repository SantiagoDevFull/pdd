<?php

namespace App\Http\Controllers\Backend;

use App\AdmPayment;
use App\Aplication;
use App\AplicationDetail;
use App\AplicationPlate;
use App\AreaAll;
use App\Http\Controllers\Controller;
use App\Company;
use App\CostoPri;
use App\CostoSec;
use App\Currency;
use App\Presupuesto;
use App\Type;
use App\SubType;
use App\TypeReq;
use App\VoucherType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SolReqRegisterController extends Controller
{
    public function index()
    {

        $companies = Company::select('id', 'name')->whereIn('id', [1, 2, 3, 4, 5, 7])->get();
        $type_reqs = TypeReq::select('id', 'name')->get();
        $adm_payments = AdmPayment::select('id', 'name')->get();
        $currencies = Currency::select('id', 'name')->whereIn('id', [1, 2])->get();
        $voucher_types = VoucherType::select('id', 'name')->whereIn('id', [1, 2, 15, 17, 19, 20])->get();
        $current_date = date(DATE_ATOM, mktime(0, 0, 0));

        return view('backend.sol_req_register')->with(compact(
            'companies',
            'type_reqs',
            'adm_payments',
            'currencies',
            'voucher_types',
            'current_date'
        ));
    }

    public function getCostos()
    {

        $area_all_id = request('area_all_id');

        $costs = CostoPri::select(
            'id',
            'name'
        )
            ->where('area_all_id', $area_all_id)
            ->get();

        return $costs;
    }
    public function getCostoSecs()
    {

        $costo_pri_id = request('costo_pri_id');

        $costs = CostoSec::select(
            'id',
            'name'
        )
            ->where('costo_pri_id', $costo_pri_id)
            ->get();

        return $costs;
    }

    public function getPresupuesto()
    {

        $area_all_id = request('area_all_id');

        $presupuesto = Presupuesto::select('id', 'monto')
            ->where('mes', date('m'))
            ->where('anio', date('Y'))
            ->where('area_all_id', $area_all_id)
            ->first();

        return $presupuesto;
    }

    public function getTypes()
    {
        $area_all_id = request('area_all_id');

        $types = Type::select('id', 'name')
            ->where('area_all_id', $area_all_id)
            ->get();

        return $types;
    }
    public function getSubTypes()
    {
        $type_id = request('type_id');

        $sub_types = SubType::select('id', 'name')
            ->where('type_id', $type_id)
            ->get();

        return $sub_types;
    }

    public function getAreas()
    {
        $company_id = request('company_id');

        $areas = AreaAll::select('id', 'name')
            ->where('company_id', $company_id)
            ->get();

        return $areas;
    }

    public function getNumMov()
    {

        $area_all_id = request('area_all_id');

        $area_all = AreaAll::find($area_all_id);
        $company = Company::find($area_all->company_id);

        $aplication = Aplication::where('anio', date('Y'))
            ->max('movement_number');

        $movement_number = $aplication ? $aplication + 1 : 1;
        $movement_number = str_pad($movement_number, 4, '0', STR_PAD_LEFT);

        $num_mov = 'SP-' . date('Y') . '-' . $company->abbreviation . '-' . $area_all->abbreviation . $movement_number;

        return $num_mov;
    }

    public function validateForm()
    {
        $messages = [
            'company_id.required'       => 'Debe seleccionar una Compañía.',
            'area_all_id.required'      => 'Debe seleccionar un Area.',
            'adm_payment_id.required'   => 'Debe seleccionar Condición de Pago.',
            'type_id.required'          => 'Debe seleccionar un Tipo.',
            'type_req_id.required'      => 'Debe seleccionar un Tipo de Solicitud.',
            'num_mov.required'          => 'Debe ingresar un Número de Movimiento.',
            'import.required_if'        => 'Debe ingresar un Importe.',
        ];

        $rules = [
            'company_id'                => 'required',
            'area_all_id'               => 'required',
            'adm_payment_id'            => 'required',
            'type_id'                   => 'required',
            'type_req_id'               => 'required',
            'num_mov'                   => 'required',
            'import'                    => 'required_if:adm_payment_id,7',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function store()
    {
        $this->validateForm();

        $company_id = request('company_id');
        $area_all_id = request('area_all_id');
        $adm_payment_id = request('adm_payment_id');
        $type_id = request('type_id');
        $sub_type_id = request('sub_type_id');
        $type_req_id = request('type_req_id');
        $presupuesto = request('presupuesto');
        $num_mov = request('num_mov');
        $import = request('import');
        $facturas = json_decode(request('facturas'), true);

        $aplication = new Aplication();
        $aplication->company_id = $company_id;
        $aplication->area_all_id = $area_all_id;
        $aplication->adm_payment_id = $adm_payment_id;
        $aplication->type_id = $type_id;
        $aplication->sub_type_id = $sub_type_id;
        $aplication->type_req_id = $type_req_id;
        $aplication->presupuesto = $presupuesto;
        $aplication->num_mov = $num_mov;
        $aplication->state_id = 1;
        $aplication->created_at_user = Auth::user()->name;

        $anio = Carbon::now()->format('Y');
        $movement_number_max = Aplication::where('anio', $anio)->max('movement_number');

        $aplication->anio = $anio;
        $aplication->movement_number = $movement_number_max + 1;
        $aplication->save();


        if ($adm_payment_id == 5 || $adm_payment_id == 6) {

            $total_import = 0;

            foreach ($facturas as $i => $factura) {

                $key = "comprobante_$i";

                $element = new AplicationDetail();
                $element->aplication_id = $aplication->id;
                $element->costo_pri_id = $factura['principal'];
                $element->costo_sec_id = $factura['secundario'];
                $element->voucher_type_id = $factura['voucher_type_id'];
                $element->issue_date = Carbon::parse($factura['issue_date'])->format('Y-m-d');
                $element->expiry_date = Carbon::parse($factura['expiry_date'])->format('Y-m-d');
                $element->num_comprobante = $factura['num_comprobante'];
                $element->document_number = $factura['document_number'];
                $element->razon_social = $factura['razon_social'];
                $element->currency_id = $factura['currency_id'];
                $element->import = $factura['import'];
                $element->description = $factura['description'];
                $element->created_at_user = Auth::user()->name;
                $element->save();

                $total_import += floatval($factura['import']);

                if ($file = request()->file($key)) {

                    $carpeta = "uploads/solicitudes/requerimientos";
                    $ext = strtolower($file->getClientOriginalExtension());
                    $file->storeAs($carpeta, "requerimiento-{$element->id}.$ext", "public");
                    $element->ext_archivo = $ext;
                    $element->save();
                }

                if (isset($factura['items']) && is_array($factura['items'])) {

                    foreach ($factura['items'] as $detalle) {

                        $plate = new AplicationPlate();
                        $plate->aplication_detail_id = $element->id;
                        $plate->client = $detalle['client'];
                        $plate->plate = $detalle['plate'];
                        $plate->created_at_user = Auth::user()->name;
                        $plate->save();
                    }
                }
            }

            $aplication->import = $total_import;
            $aplication->neto = $total_import;
        } else {
            $aplication->import = $import;
            $aplication->neto = $import;
        }

        $aplication->save();

        $title = "¡Ok!";
        $msg = "Solicitud " . $num_mov . " registrada correctamente.";
        $url = route('dashboard.operations.sol_req_register');


        return response()->json([
            'title' => $title,
            'msg' => $msg,
            'url' => $url
        ]);
    }
}
