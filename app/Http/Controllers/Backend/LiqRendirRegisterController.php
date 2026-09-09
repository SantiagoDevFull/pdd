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

class LiqRendirRegisterController extends Controller
{
    public function index()
    {

        $companies = Company::select('id', 'name')->whereIn('id', [1, 2, 3, 4, 5, 7])->get();
        $type_reqs = TypeReq::select('id', 'name')->get();
        $adm_payments = AdmPayment::select('id', 'name')->get();
        $area_alls = AreaAll::select('id', 'name')->get();
        $currencies = Currency::select('id', 'name')->whereIn('id', [1, 2])->get();
        $voucher_types = VoucherType::select('id', 'name')->whereIn('id', [1, 2, 15, 17, 19, 20])->get();
        $current_date = date(DATE_ATOM, mktime(0, 0, 0));
        $num_movs = Aplication::where('adm_payment_id', 7)->where('state_id', 4)->get();
        $types = Type::select('id', 'name')->get();
        $sub_types = SubType::select('id', 'name')->get();

        return view('backend.liq_rendir_register')->with(compact(
            'companies',
            'type_reqs',
            'adm_payments',
            'currencies',
            'voucher_types',
            'current_date',
            'num_movs',
            'area_alls',
            'types',
            'sub_types'
        ));
    }

    public function getAplication()
    {

        $id = request('id');

        $aplication = Aplication::find($id);

        return $aplication;
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

    public function validateForm()
    {
        $messages = [
            'id.required' => 'Debe seleccionar una Aplicación.',
        ];

        $rules = [
            'id' => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function store()
    {
        $this->validateForm();

        $id = request('id');

        $aplication = Aplication::find($id);

        $facturas = json_decode(request('facturas'), true);

        $total_import = 0;

        foreach ($facturas as $i => $factura) {

            $key = "comprobante_$i";
            
            $element = new AplicationDetail();
            $element->aplication_id = $id;
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

        $aplication->neto = $total_import;
        $aplication->state_id = 5;
        $aplication->save();

        $title = "¡Ok!";
        $msg = "Documentos guardados exitosamente.";
        $url = route('dashboard.operations.liq_rendir');


        return response()->json([
            'title' => $title,
            'msg' => $msg,
            'url' => $url
        ]);
    }
}
