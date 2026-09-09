<?php

namespace App\Http\Controllers\Backend;

use App\Company;
use App\Guide;
use App\GuidesDetail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use PDF;

class ConstancyGuideController extends Controller
{
    public function index()
    {
        $companies = Company::select('id', 'name')->whereIn('id', [1, 2])->get();
        return view('backend.constancy_guide')->with(compact('companies'));
    }

    public function validateForm()
    {
        $messages = [
            'company_id.required'                           => 'Debe seleccionar una Compañía.',
            'movement_type_id.required'                     => 'Debe seleccionar Tipo de Movimiento.',
        ];

        $rules = [
            'company_id'                                    => 'required',
            'movement_type_id'                              => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function detail()
    {
        $guide_id = request('id');

        $guides = Guide::find($guide_id, ['referral_guide_series', 'referral_guide_number']);
        $guides_detail = GuidesDetail::join('articles', 'article_code', 'articles.id')
            ->where('guides_id', $guide_id)
            ->select(
                'guides_details.id as id',
                'articles.name as name',
                'guides_details.price as unit_price',
                'guides_details.digit_amount as quantity'
            )
            ->get();

        return response()->json([
            'voucher'            => $guides,
            'voucher_details'    => $guides_detail
        ]);
    }

    public function list()
    {
        $q = request('query');
        $search = $q['generalSearch'];

        $company_id = request('company_id');
        $movement_type_id = request('movement_type_id');

        if ($movement_type_id == 11) {
            $guides = Guide::leftjoin('companies', 'companies.id', 'guides.company_id')
                ->leftjoin('movent_types', 'movent_types.id', 'guides.movement_type_id')
                ->leftjoin('employees', 'employees.id', 'guides.account_id')
                ->leftjoin('client_routes', 'client_routes.id', 'guides.route_id')
                ->select(
                    'guides.id as id',
                    'client_routes.name as route_name',
                    'companies.name as company_name',
                    'guides.movement_type_id as movement_type_id',
                    'movent_types.name as movement_type_name',
                    'guides.referral_guide_series as referral_guide_series',
                    'guides.referral_guide_number as referral_guide_number',
                    DB::Raw('CONCAT(employees.first_name," ",employees.last_name) as chofer')
                )
                ->when($search, function ($query, $search) {
                    return $query->where('guides.referral_guide_series', '=', /*'%'.*/ $search/*.'%'*/)
                        ->orWhere('guides.referral_guide_number', '=', /*'%'.*/ $search/*.'%'*/);
                })
                ->where('guides.company_id', $company_id)
                ->where('guides.movement_type_id', $movement_type_id)
                ->where('guides.warehouse_document_type_id', 1)//GUIA DE REMISION
                ->orderBy('guides.id', 'desc')
                ->get();
        } else if ($movement_type_id = 12) {
            $guides = Guide::leftjoin('companies', 'companies.id', 'guides.company_id')
                ->leftjoin('movent_types', 'movent_types.id', 'guides.movement_type_id')
                ->leftjoin('employees', 'employees.id', 'guides.employee_id')
                ->leftjoin('clients', 'clients.id', 'guides.account_id')
                ->leftjoin('client_routes', 'client_routes.id', 'guides.route_id')
                ->select(
                    'guides.id as id',
                    'client_routes.name as route_name',
                    'companies.name as company_name',
                    'guides.movement_type_id as movement_type_id',
                    'movent_types.name as movement_type_name',
                    'guides.referral_guide_series as referral_guide_series',
                    'guides.referral_guide_number as referral_guide_number',
                    'clients.business_name as cliente',
                    DB::Raw('CONCAT(employees.first_name," ",employees.last_name) as chofer')
                )
                ->when($search, function ($query, $search) {
                    return $query->where('guides.referral_guide_series', '=', /*'%'.*/ $search/*.'%'*/)
                        ->orWhere('guides.referral_guide_number', '=', /*'%'.*/ $search/*.'%'*/);
                })
                ->where('guides.company_id', $company_id)
                ->where('guides.movement_type_id', $movement_type_id)
                ->where('guides.warehouse_document_type_id', 1)//GUIA DE REMISION
                ->orderBy('guides.id', 'desc')
                ->get();
        }

        return $guides;
    }

    public function generarPdf()
    {

        $id = request('id');

        $obj = Guide::leftjoin('vehicles', 'guides.vehicle_id', '=', 'vehicles.id')
            ->select(
                'guides.id as id',
                DB::Raw('CONCAT(guides.referral_guide_series,"-",guides.referral_guide_number) as guia'),
                DB::Raw('DATE_FORMAT(guides.traslate_date, "%Y-%m-%d") as traslate_date'),
                'vehicles.plate as vehicle_placa',
                'vehicles.largo as vehicle_largo',
                'vehicles.ancho as vehicle_ancho',
                'vehicles.alto as vehicle_alto',
                'vehicles.conf as vehicle_conf',
                'vehicles.capacity as vehicle_capacity'
            )
            ->findOrFail($id);


        $details = GuidesDetail::join('articles', 'articles.id', 'guides_details.article_code')
            ->select(
                'articles.name as article_name',
                'articles.convertion as article_convertion',
                DB::raw('ROUND(guides_details.digit_amount, 0) as quantity')
            )
            ->where('guides_details.guides_id', $id)
            ->get();

        $kg = 0;
        foreach ($details as $item) {
            $kg = $kg + ($item->article_convertion * $item->quantity);
        }


        $pdf = PDF::loadView('backend.pdf_guia_constancy', compact('obj', 'details', 'kg'));
        return $pdf->download('SD.pdf');
    }
}
