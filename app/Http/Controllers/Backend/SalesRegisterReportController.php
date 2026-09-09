<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\Client;
use App\Company;
use App\Exports\PerceptionReportExport;
use App\Payment;
use App\Voucher;
use App\VoucherType;
use App\Exports\SalesReportExport;
use Maatwebsite\Excel\Facades\Excel;

class SalesRegisterReportController extends Controller
{
    public function index()
    {
        $companies = Company::select('id', 'name')->whereIn('id', [1])->get();
        $current_date = date(DATE_ATOM, mktime(0, 0, 0));

        return view('backend.sales_register_report')->with(compact('companies', 'current_date'));
    }

    public function validateForm()
    {
        $messages = [
            'company_id.required'       => 'Debe seleccionar una Compañía.',
            'since_date.required'       => 'Debe seleccionar una Fecha de Inicio.',
            'to_date.required'          => 'Debe seleccionar una Fecha de Fin.',
        ];

        $rules = [
            'company_id'        => 'required',
            'since_date'        => 'required',
            'to_date'           => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function list()
    {

        $company_id = request('model.company_id');
        $initial_date = date('Y-m-d', strtotime(request('model.since_date')));//desde
        $final_date = date('Y-m-d', strtotime(request('model.to_date')));//hasta
        $export = request('export');

        $collection = Voucher::leftjoin('clients', 'clients.id', '=', 'vouchers.client_id')
            ->select(
                'vouchers.id as id',
                'vouchers.serie_number as serie_number',
                'vouchers.voucher_number as voucher_number',
                'vouchers.issue_date as issue_date',
                'clients.document_number as client_document_number',
                'vouchers.client_name as client_name',
                'vouchers.total as total',
                DB::raw('
                CASE 
                    WHEN vouchers.ose = 0 THEN "Por enviar"
                    WHEN vouchers.ose = 1 THEN "Enviado"
                    WHEN vouchers.ose = 3 THEN "Anulado"
                END as ose_status
                '),
                'vouchers.igv_perception_percentage as igv_perception_percentage',
                DB::raw('vouchers.total_perception - vouchers.total as percepcion'),
                'vouchers.total_perception as total_perception',
                'vouchers.credit_note_reference_serie as credit_note_reference_serie',
                'vouchers.credit_note_reference_number as credit_note_reference_number',
                'vouchers.shipping_date as shipping_date'
            )
            ->where('vouchers.voucher_type_id', 14)
            ->where('vouchers.issue_date','>=',$initial_date)
            ->where('vouchers.issue_date','<=',$final_date)
            ->get();


        $meta = new \stdClass();
        $meta->page = 1;
        $meta->pages = 1;
        $meta->perpage = -1;
        $meta->total = $collection->count();
        $meta->field = 'voucher_id';
        for ($i = 1; $i <= $collection->count(); $i++) {
            $meta->rowIds[] = $i;
        };

        if ($export) {


            $data = new PerceptionReportExport($collection->toArray());
            $file = Excel::download($data, 'reporte-de-percepciones.xls');

            return $file;
        } else {
            return response()->json([
                'meta' => $meta,
                'data' => $collection,
            ]);
        }
    }
}
