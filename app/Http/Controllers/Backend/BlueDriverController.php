<?php

namespace App\Http\Controllers\Backend;

use App\Company;
use App\Exports\DriverReportExport;
use App\Http\Controllers\Controller;
use App\BlDriver;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class BlueDriverController extends Controller
{
    //listo
    public function index()
    {
        $companies = Company::select('id', 'name')->whereIn('id', [1,2,4])->get();

        return view('backend.blue_driver')->with(compact(
            'companies'
        ));
    }

    public function validateForm()
    {
        $messages = [
            'company_id.required'            => 'Debe seleccionar una Compañia.',
            'first_name.required'            => 'Debe el nombre del chofer.',
            'last_name.required'             => 'Debe el apellido del chofer.',
            'document_number.required'       => 'Debe el documento del chofer',

            //'f_license_a3.required'             => 'Debe indicar la licencia A3.',
            //'f_license_a4.required'             => 'Debe indicar la licencia A4.',
            //'f_emo.required'                    => 'Debe indicar el EMO.',
            //'f_carnet_solgas.required'          => 'Debe indicar el carnet de solgas.',
            //'f_ant_pen.required'                => 'Debe indicar antecedentes penales.',
            //'f_ant_pol.required'                => 'Debe indicar antecedentes policiales.',
            //'f_carnet_trip.required'            => 'Debe indicar el carnet de tripulante.',


            //'license_a3_pdf.required'                             => 'PDF',
            //'f_license_a4.mimes'                                    => 'PDF',
            //'f_emo.required'                                        => 'PDF',
            //'f_carnet_solgas.mimes'                                 => 'PDF',
            //'f_ant_pen.required'                                    => 'PDF',
            //'f_ant_pol.mimes'                                       => 'PDF',
            //'f_carnet_trip.mimes'                                       => 'PDF',
        ];

        $rules = [
            //'license_a3_pdf'        => 'required',
            'company_id'            => 'required',
            'first_name'            => 'required',
            'last_name'             => 'required',
            'document_number'       => 'required',

            //'f_license_a3'          => 'required',
            //'f_license_a4'          => 'required',
            //'f_emo'                 => 'required',
            //'f_carnet_solgas'       => 'required',
            //'f_ant_pen'             => 'required',
            //'f_ant_pol'             => 'required',
            //'f_carnet_trip'         => 'required'

        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    //listo
    public function list()
    {

        $driver = BlDriver::leftjoin('companies', 'companies.id', 'bl_drivers.company_id')
            ->select(

                'bl_drivers.id as id',
                'companies.name as company_name',
                'bl_drivers.first_name as first_name',
                'bl_drivers.last_name as last_name',
                'bl_drivers.document_number as document_number',
                'bl_drivers.f_curso_nor as f_curso_nor',
                'bl_drivers.f_lib_ter as f_lib_ter'
            )
            //->whereIn('bl_drivers.company_id', [4])
            ->orderBy('bl_drivers.id', 'desc')
            ->get();

        return $driver;
    }

    //listo
    public function update()
    {
        $this->validateModalForm();

        $id = request('id');


        $company_id = request('company_id');
        $transportist_id = request('transportist_id');
        $document_number = request('document_number');
        $f_license_a3 = request('f_license_a3') ? Carbon::parse(request('f_license_a3'))->format('Y-m-d') : null;
        $f_license_a4 = request('f_license_a4') ? Carbon::parse(request('f_license_a4'))->format('Y-m-d') : null;
        $f_emo = request('f_emo') ? Carbon::parse(request('f_emo'))->format('Y-m-d') : null;
        $f_carnet_solgas = request('f_carnet_solgas') ? Carbon::parse(request('f_carnet_solgas'))->format('Y-m-d') : null;
        $f_ant_pen = request('f_ant_pen') ? Carbon::parse(request('f_ant_pen'))->format('Y-m-d') : null;
        $f_ant_pol = request('f_ant_pol') ? Carbon::parse(request('f_ant_pol'))->format('Y-m-d') : null;
        $f_carnet_trip = request('f_carnet_trip') ? Carbon::parse(request('f_carnet_trip'))->format('Y-m-d'):null;
        $f_curso_nor = request('f_curso_nor') ? Carbon::parse(request('f_curso_nor'))->format('Y-m-d'):null;
        $f_lib_ter = request('f_lib_ter') ? Carbon::parse(request('f_lib_ter'))->format('Y-m-d'):null;

        $driver = BlDriver::findOrFail($id);
        $driver->company_id = $company_id;
        $driver->transportist_id = $transportist_id;
        $driver->document_number = $document_number;
        $driver->f_license_a3 = $f_license_a3;
        $driver->f_license_a4 = $f_license_a4;
        $driver->f_emo = $f_emo;
        $driver->f_carnet_solgas = $f_carnet_solgas;
        $driver->f_ant_pen = $f_ant_pen;
        $driver->f_ant_pol = $f_ant_pol;
        $driver->f_carnet_trip = $f_carnet_trip;
        $driver->f_curso_nor = $f_curso_nor;
        $driver->f_lib_ter = $f_lib_ter;
        $driver->update();

        $carpeta = "uploads/BlueLine/choferes";

        if (request()->hasFile('f_document_number_pdf_modal')) {
            $f_document_number_file = request()->file('f_document_number_pdf_modal');
            $f_document_number_ext = $f_document_number_file->getClientOriginalExtension();
            $f_document_number_file->storeAs($carpeta, 'f_document_number' . '-' . $driver->id . '.' . $f_document_number_ext, 'public');
        }
        if (request()->hasFile('f_license_a3_pdf_modal')) {
            $f_license_a3_file = request()->file('f_license_a3_pdf_modal');
            $f_license_a3_ext = $f_license_a3_file->getClientOriginalExtension();
            $f_license_a3_file->storeAs($carpeta, 'f_license_a3' . '-' . $driver->id . '.' . $f_license_a3_ext, 'public');
        }
        if (request()->hasFile('f_license_a4_pdf_modal')) {
            $f_license_a4_file = request()->file('f_license_a4_pdf_modal');
            $f_license_a4_ext = $f_license_a4_file->getClientOriginalExtension();
            $f_license_a4_file->storeAs($carpeta, 'f_license_a4' . '-' . $driver->id . '.' . $f_license_a4_ext, 'public');
        }
        if (request()->hasFile('f_emo_pdf_modal')) {
            $f_emo_file = request()->file('f_emo_pdf_modal');
            $f_emo_ext = $f_emo_file->getClientOriginalExtension();
            $f_emo_file->storeAs($carpeta, 'f_emo' . '-' . $driver->id . '.' . $f_emo_ext, 'public');
        }
        if (request()->hasFile('f_carnet_solgas_pdf_modal')) {
            $f_carnet_solgas_file = request()->file('f_carnet_solgas_pdf_modal');
            $f_carnet_solgas_ext = $f_carnet_solgas_file->getClientOriginalExtension();
            $f_carnet_solgas_file->storeAs($carpeta, 'f_carnet_solgas' . '-' . $driver->id . '.' . $f_carnet_solgas_ext, 'public');
        }

        if (request()->hasFile('f_ant_pen_pdf_modal')) {
            $f_ant_pen_file = request()->file('f_ant_pen_pdf_modal');
            $f_ant_pen_ext = $f_ant_pen_file->getClientOriginalExtension();
            $f_ant_pen_file->storeAs($carpeta, 'f_ant_pen' . '-' . $driver->id . '.' . $f_ant_pen_ext, 'public');
        }

        if (request()->hasFile('f_ant_pol_pdf_modal')) {
            $f_ant_pol_file = request()->file('f_ant_pol_pdf_modal');
            $f_ant_pol_ext = $f_ant_pol_file->getClientOriginalExtension();
            $f_ant_pol_file->storeAs($carpeta, 'f_ant_pol' . '-' . $driver->id . '.' . $f_ant_pol_ext, 'public');
        }

        if (request()->hasFile('f_carnet_trip_pdf_modal')) {
            $f_carnet_trip_file = request()->file('f_carnet_trip_pdf_modal');
            $f_carnet_trip_ext = $f_carnet_trip_file->getClientOriginalExtension();
            $f_carnet_trip_file->storeAs($carpeta, 'f_carnet_trip' . '-' . $driver->id . '.' . $f_carnet_trip_ext, 'public');
        }
        if (request()->hasFile('curso_nor_pdf_modal')) {
            $f_curso_nor_file = request()->file('curso_nor_pdf_modal');
            $f_curso_nor_ext = $f_curso_nor_file->getClientOriginalExtension();
            $f_curso_nor_file->storeAs($carpeta, 'f_curso_nor' . '-' . $driver->id . '.' . $f_curso_nor_ext, 'public');
        }
        if (request()->hasFile('lib_ter_pdf_modal')) {
            $f_lib_ter_file = request()->file('lib_ter_pdf_modal');
            $f_lib_ter_ext = $f_lib_ter_file->getClientOriginalExtension();
            $f_lib_ter_file->storeAs($carpeta, 'f_lib_ter' . '-' . $driver->id . '.' . $f_lib_ter_ext, 'public');
        }


        $type = 1;
        $title = '¡Ok!';
        $msg = 'Conductor actualizado exitosamente.';

        return response()->json([
            'type'    => $type,
            'title'    => $title,
            'msg'    => $msg,
        ]);
    }

    //listo
    public function exportExcel()
    {

        $drivers = BlDriver::leftjoin('companies', 'companies.id', 'bl_drivers.company_id')
            ->select(

                'bl_drivers.id as id',
                'companies.name as company_name',
                'bl_drivers.first_name as first_name',
                'bl_drivers.last_name as last_name',
                'bl_drivers.document_number as document_number',
                'bl_drivers.f_license_a3 as f_license_a3',
                'bl_drivers.f_license_a4 as f_license_a4',
                'bl_drivers.f_emo as f_emo',
                'bl_drivers.f_carnet_solgas as f_carnet_solgas',
                'bl_drivers.f_ant_pen as f_ant_pen',
                'bl_drivers.f_ant_pol as f_ant_pol',
                'bl_drivers.f_carnet_trip as f_carnet_trip',
                'bl_drivers.f_curso_nor as f_curso_nor',
                'bl_drivers.f_lib_ter as f_lib_ter'
            )
            //->whereIn('bl_drivers.company_id', [4])
            ->orderBy('bl_drivers.id', 'desc')
            ->get();

        $datetime = CarbonImmutable::now()->format('d/m/Y h:i:s a');

        $excel = new DriverReportExport($datetime, $drivers);

        return Excel::download($excel, 'reporte-conductores.xlsx');
    }

    //listo
    public function detail()
    {

        $id = request('id');
        $driver = BlDriver::findOrFail($id);
        $driver->url_f_license_a3 = asset('uploads/BlueLine/choferes/f_license_a3-' . $driver->id . '.pdf');
        $driver->url_f_license_a4 = asset('uploads/BlueLine/choferes/f_license_a4-' . $driver->id . '.pdf');
        $driver->url_f_emo = asset('uploads/BlueLine/choferes/f_emo-' . $driver->id . '.pdf');
        $driver->url_f_carnet_solgas = asset('uploads/BlueLine/choferes/f_carnet_solgas-' . $driver->id . '.pdf');
        $driver->url_f_ant_pen = asset('uploads/BlueLine/choferes/f_ant_pen-' . $driver->id . '.pdf');
        $driver->url_f_ant_pol = asset('uploads/BlueLine/choferes/f_ant_pol-' . $driver->id . '.pdf');
        $driver->url_f_curso_nor = asset('uploads/BlueLine/choferes/f_curso_nor-' . $driver->id . '.pdf');
        $driver->url_f_lib_ter = asset('uploads/BlueLine/choferes/f_lib_ter-' . $driver->id . '.pdf');
        $driver->url_f_carnet_trip = asset('uploads/BlueLine/choferes/f_carnet_trip-' . $driver->id . '.pdf');
        $driver->url_f_document_number = asset('uploads/BlueLine/choferes/f_document_number-' . $driver->id . '.pdf');
    
        return $driver;
    }

    public function delete()
    {
        $id = request('id');
        $element = BlDriver::findOrFail($id);
        $element->deleted_at_user = Auth::user()->user;
        $element->delete();
    }

    public function validateModalForm()
    {
        $messages = [];

        $rules = [];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function store()
    {

        $this->validateForm();

        $company_id = request('company_id');
        $transportist_id = request('company_id');
        $first_name = request('first_name');
        $last_name = request('last_name');
        $document_number = request('document_number');

        $f_license_a3 = request('f_license_a3');
        $f_license_a4 = request('f_license_a4');
        $f_emo = request('f_emo');
        $f_carnet_solgas = request('f_carnet_solgas');
        $f_ant_pen = request('f_ant_pen');
        $f_ant_pol = request('f_ant_pol');
        $f_carnet_trip = request('f_carnet_trip');
        $f_curso_nor = request('f_curso_nor');
        $f_lib_ter = request('f_lib_ter');

        $company = Company::select('name')->where('id', '=', $company_id)->first();

        $driver = new BlDriver();
        $driver->company_id = $company_id;
        $driver->transportist_id = $transportist_id;
        $driver->first_name = $first_name;
        $driver->last_name = $last_name;
        $driver->document_number = $document_number;

        $f_license_a3 = $f_license_a3 ? \Carbon\Carbon::createFromFormat('d-m-Y', $f_license_a3)->format('Y-m-d') : null;
        $f_license_a4 = $f_license_a4 ? \Carbon\Carbon::createFromFormat('d-m-Y', $f_license_a4)->format('Y-m-d') : null;
        $f_emo = $f_emo ? \Carbon\Carbon::createFromFormat('d-m-Y', $f_emo)->format('Y-m-d') : null;
        $f_carnet_solgas = $f_carnet_solgas ? \Carbon\Carbon::createFromFormat('d-m-Y', $f_carnet_solgas)->format('Y-m-d') : null;
        $f_ant_pen = $f_ant_pen ? \Carbon\Carbon::createFromFormat('d-m-Y', $f_ant_pen)->format('Y-m-d') : null;
        $f_ant_pol = $f_ant_pol ? \Carbon\Carbon::createFromFormat('d-m-Y', $f_ant_pol)->format('Y-m-d') : null;
        $f_carnet_trip = $f_carnet_trip ? \Carbon\Carbon::createFromFormat('d-m-Y', $f_carnet_trip)->format('Y-m-d') : null;
        $f_curso_nor = $f_curso_nor ? \Carbon\Carbon::createFromFormat('d-m-Y', $f_curso_nor)->format('Y-m-d') : null;
        $f_lib_ter = $f_lib_ter ? \Carbon\Carbon::createFromFormat('d-m-Y', $f_lib_ter)->format('Y-m-d') : null;


        $driver->f_license_a3 = $f_license_a3;
        $driver->f_license_a4 = $f_license_a4;
        $driver->f_emo = $f_emo;
        $driver->f_carnet_solgas = $f_carnet_solgas;
        $driver->f_ant_pen = $f_ant_pen;
        $driver->f_ant_pol = $f_ant_pol;
        $driver->f_carnet_trip = $f_carnet_trip;
        $driver->f_curso_nor = $f_curso_nor;
        $driver->f_lib_ter = $f_lib_ter;

        $driver->save();


        $carpeta = "uploads/BlueLine/choferes";

        if (request()->hasFile('license_a3_pdf')) {
            $f_license_a3_file = request()->file('license_a3_pdf');
            $f_license_a3_ext = $f_license_a3_file->getClientOriginalExtension();
            $f_license_a3_file->storeAs($carpeta, 'f_license_a3' . '-' . $driver->id . '.' . $f_license_a3_ext, 'public');
        }

        if (request()->hasFile('f_license_a4_pdf')) {
            $f_license_a3_file = request()->file('f_license_a4_pdf');
            $f_license_a3_ext = $f_license_a3_file->getClientOriginalExtension();
            $f_license_a3_file->storeAs($carpeta, 'f_license_a4' . '-' . $driver->id . '.' . $f_license_a3_ext, 'public');
        }
        if (request()->hasFile('f_emo_pdf')) {
            $f_license_a3_file = request()->file('f_emo_pdf');
            $f_license_a3_ext = $f_license_a3_file->getClientOriginalExtension();
            $f_license_a3_file->storeAs($carpeta, 'f_emo' . '-' . $driver->id . '.' . $f_license_a3_ext, 'public');
        }
        if (request()->hasFile('f_carnet_solgas_pdf')) {
            $f_license_a3_file = request()->file('f_carnet_solgas_pdf');
            $f_license_a3_ext = $f_license_a3_file->getClientOriginalExtension();
            $f_license_a3_file->storeAs($carpeta, 'f_carnet_solgas' . '-' . $driver->id . '.' . $f_license_a3_ext, 'public');
        }
        if (request()->hasFile('f_ant_pen_pdf')) {
            $f_license_a3_file = request()->file('f_ant_pen_pdf');
            $f_license_a3_ext = $f_license_a3_file->getClientOriginalExtension();
            $f_license_a3_file->storeAs($carpeta, 'f_ant_pen' . '-' . $driver->id . '.' . $f_license_a3_ext, 'public');
        }
        if (request()->hasFile('f_ant_pol_pdf')) {
            $f_license_a3_file = request()->file('f_ant_pol_pdf');
            $f_license_a3_ext = $f_license_a3_file->getClientOriginalExtension();
            $f_license_a3_file->storeAs($carpeta, 'f_ant_pol' . '-' . $driver->id . '.' . $f_license_a3_ext, 'public');
        }
        if (request()->hasFile('f_carnet_trip_pdf')) {
            $f_license_a3_file = request()->file('f_carnet_trip_pdf');
            $f_license_a3_ext = $f_license_a3_file->getClientOriginalExtension();
            $f_license_a3_file->storeAs($carpeta, 'f_carnet_trip' . '-' . $driver->id . '.' . $f_license_a3_ext, 'public');
        }
        if ($f_curso_nor_file = request()->file('curso_nor_pdf')) {
            $f_curso_nor_ext = $f_curso_nor_file->getClientOriginalExtension();
            $f_curso_nor_file->storeAs($carpeta, 'f_curso_nor' . '-' . $driver->id . '.' . $f_curso_nor_ext, 'public');
        }

        if ($f_lib_ter_file = request()->file('lib_ter_pdf')) {
            $f_lib_ter_ext = $f_lib_ter_file->getClientOriginalExtension();
            $f_lib_ter_file->storeAs($carpeta, 'f_lib_ter' . '-' . $driver->id . '.' . $f_lib_ter_ext, 'public');
        }
        
        if ($f_document_number_file = request()->file('document_number_pdf')) {
            $f_document_number_ext = $f_document_number_file->getClientOriginalExtension();
            $f_document_number_file->storeAs($carpeta, 'f_document_number' . '-' . $driver->id . '.' . $f_document_number_ext, 'public');
        }


        $type = 3;
        $title = '¡Ok!';
        $msg = 'Conductor creado exitosamente.';
        $url = route('dashboard.blue_driver');

        return response()->json([
            'type'    => $type,
            'title'    => $title,
            'msg'    => $msg,
            'url'   => $url
        ]);
    }
}
