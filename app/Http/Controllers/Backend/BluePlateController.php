<?php

namespace App\Http\Controllers\Backend;

use App\Company;
use App\Exports\VehiclePlateReportExport;
use App\Http\Controllers\Controller;
use App\BlVehicle;
use App\BlClient;
use App\Brand;
use App\BodyType;
use App\BlAsesore;
use App\BlClase;
use App\Modelo;
use App\BlService;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class BluePlateController extends Controller
{

    public function index()
    {
        $companies = Company::select('id', 'name')->get();
        $bl_asesores = BlAsesore::select('id', 'name')->get();
        $bl_clients = BlClient::select('id', 'description')->get();
        $bl_services = BlService::select('id', 'description')->get();
        $bl_clases = BLClase::select('id', 'description')->get();
        $bl_body_types = BodyType::select('id', 'description')->get();
        $bl_brands = Brand::select('id', 'description')->get();
        $bl_modelos = Modelo::select('id', 'description')->get();

        return view('backend.blue_plate')->with(compact(
            'companies',
            'bl_asesores',
            'bl_clients',
            'bl_services',
            'bl_clases',
            'bl_body_types',
            'bl_brands',
            'bl_modelos'
        ));
    }

    public function validateForm()
    {
        $messages = [
            'company_id.required'        => 'Debe seleccionar una Compañia.',
            'plate.required'             => 'Debe completar la Placa.',
            'blclase_id.required_if'          => 'Debe indicar la clase.',
            'body_type_id.required'      => 'Debe indicar el tipo.',
            'brand_id.required'          => 'Debe seleccionar la marca.',
            'modelo_id.required'          => 'Debe seleccionar el modelo.',
            'motor.required'             => 'Debe seleccionar el numero de motor.',
            'chasis.required'             => 'Debe seleccionar el numero de la serie.',
            'eje.required'             => 'Debe seleccionar el numero de ejes.',
            'cng.required'             => 'Debe seleccionar el numero de ejes.',
            'cna.required'             => 'Debe seleccionar el numero de ejes.',

            'year_model.required'             => 'Debe seleccionar año.',
            //'fecha_soat.required'             => 'Debe seleccionar el numero de ejes.',
            //'fecha_rt.required'             => 'Debe seleccionar el numero de ejes.',
            //'fecha_rtmg.required'             => 'Debe seleccionar el numero de ejes.',
            //'fecha_bon.required'             => 'Debe seleccionar el numero de ejes.',
            //'fecha_vcto.required'             => 'Debe seleccionar el numero de ejes.',
            //'fecha_tanque.required'             => 'Debe seleccionar el numero de ejes.',
            //'fecha_tanque_cil.required'             => 'Debe seleccionar el numero de ejes.',
            //'f_p_callao.required'             => 'Debe seleccionar el numero de ejes.',
            //'f_p_lima.required'             => 'Debe seleccionar el numero de ejes.',
            //'f_circulation_card.required'             => 'Debe seleccionar fecha de tarjeta de circulacion.',
            //'f_cert_insp.required'             => 'Debe seleccionar fecha de tarjeta de circulacion.',
            //'f_curso_nor.required'             => 'Debe seleccionar fecha de tarjeta de circulacion.',
            //'f_lib_ter.required'             => 'Debe seleccionar fecha de tarjeta de circulacion.',
            //'f_cer_fum.required'             => 'Debe seleccionar fecha de tarjeta de circulacion.',

            //'soat_pdf.required'                                   => 'PDF',
            //'soat_pdf.mimes'                                      => 'PDF',
            //'rt_pdf.required'                                     => 'PDF',
            //'rt_pdf.mimes'                                        => 'PDF',
            //'rtmg_pdf.required'                                   => 'PDF',
            //'rtmg_pdf.mimes'                                      => 'PDF',
            //'bon_pdf.required'                                    => 'PDF',
            //'bon_pdf.mimes'                                       => 'PDF',
            //'vcto_pdf.required'                                   => 'PDF',
            //'vcto_pdf.mimes'                                      => 'PDF',
            //'tanque_pdf.required'                                 => 'PDF',
            //'tanque_pdf.mimes'                                    => 'PDF',
            //'tanque_cil_pdf.required'                             => 'PDF',
            //'tanque_cil_pdf.mimes'                                => 'PDF',
            //'p_callao_pdf.required'                               => 'PDF',
            //'p_callao_pdf.mimes'                                  => 'PDF',
            //'p_lima_pdf.required'                                 => 'PDF',
            //'p_lima_pdf.mimes'                                    => 'PDF',
            //'circulation_card_pdf.required'                        => 'PDF',
            //'circulation_card_pdf.mimes'                           => 'PDF',
            //'cert_insp_pdf.required'                              => 'PDF',
            //'cert_insp_pdf.mimes'                                 => 'PDF',
            //'curso_nor_pdf.required'                              => 'PDF',
            //'curso_nor_pdf.mimes'                                 => 'PDF',
            //'lib_ter_pdf.required'                                 => 'PDF',
            //'lib_ter_pdf.mimes'                                    => 'PDF',
            //'cer_fum_pdf.required'                                 => 'PDF',
            //'cer_fum_pdf.mimes'                                    => 'PDF',

        ];

        $rules = [
            'company_id'      => 'required',
            'plate'           => 'required',
            'blclase_id'      => 'required',
            'body_type_id'    => 'required',
            'brand_id'        => 'required',
            'modelo_id'       => 'required',
            'motor'           => 'required',
            'chasis'          => 'required',
            'eje'             => 'required',
            'cng'             => 'required',
            'cna'            => 'required',

            'year_model'        => 'required',
            //'fecha_soat'        => 'required',
            //'fecha_rt'          => 'required',
            //'fecha_rtmg'        => 'required',
            //'fecha_bon'         => 'required',
            //'fecha_vcto'        => 'required',
            //'fecha_tanque'      => 'required',
            //'fecha_tanque_cil'  => 'required',
            //'f_p_callao'        => 'required',
            //'f_p_lima'          => 'required',
            //'f_circulation_card' => 'required',
            //'f_cert_insp'       => 'required',
            //'f_curso_nor'       => 'required',
            //'f_lib_ter'       => 'required',
            //'f_cer_fum'       => 'required',

            //'soat_pdf'          => 'required|mimes:pdf',
            //'rt_pdf'    => 'required|mimes:pdf',
            //'rtmg_pdf'  => 'required|mimes:pdf',
            //'bon_pdf'   => 'required|mimes:pdf',
            //'vcto_pdf'  => 'required|mimes:pdf',
            //'tanque_pdf' => 'required|mimes:pdf',
            //'tanque_cil_pdf' => 'required|mimes:pdf',
            //'p_callao_pdf' => 'required|mimes:pdf',
            //'p_lima_pdf' => 'required|mimes:pdf',
            //'circulation_card_pdf' => 'required|mimes:pdf',
            //'cert_insp_pdf' => 'required|mimes:pdf',
            //'curso_nor_pdf' => 'required|mimes:pdf',
            //'lib_ter_pdf' => 'required|mimes:pdf',
            //'cer_fum_pdf' => 'required|mimes:pdf',


        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function list()
    {

        $vehicles = BlVehicle::leftjoin('companies', 'companies.id', 'bl_vehicles.company_id')
            ->select(
                'bl_vehicles.id as id',
                'companies.name as company_name',
                'bl_vehicles.plate as plate',

                'bl_vehicles.clase_id as clase_id',
                'bl_vehicles.body_type_id as body_type_id',
                'bl_vehicles.brand_id as brand_id',
                'bl_vehicles.modelo_id as modelo_id',
                'bl_vehicles.motor as motor',
                'bl_vehicles.chasis as chasis',
                'bl_vehicles.eje as eje',
                'bl_vehicles.cng as cng',
                'bl_vehicles.cna as cna',
                'bl_vehicles.year_model as year_model',
                'bl_vehicles.fecha_soat as fecha_soat',
                'bl_vehicles.fecha_rt as fecha_rt',
                'bl_vehicles.fecha_rtmg as fecha_rtmg',
                'bl_vehicles.fecha_bon as fecha_bon',
                'bl_vehicles.fecha_vcto as fecha_vcto',
                'bl_vehicles.fecha_tanque as fecha_tanque',
                'bl_vehicles.fecha_tanque_cil as fecha_tanque_cil',
                'bl_vehicles.f_p_callao as f_p_callao',
                'bl_vehicles.f_p_lima as f_p_lima',
                'bl_vehicles.f_circulation_card as f_circulation_card',
                'bl_vehicles.f_cert_insp as f_cert_insp',
                'bl_vehicles.f_curso_nor as f_curso_nor',
                'bl_vehicles.f_lib_ter as f_lib_ter',
                'bl_vehicles.f_cer_fum as f_cer_fum'
            )
            ->orderBy('bl_vehicles.id', 'desc')
            ->get();

        return $vehicles;
    }

    public function update()
    {

        $this->validateModalForm();

        $id = request('id');
        $company_id = request('company_id');
        $transportist_id = request('transportist_id');
        $fecha_soat         = request('fecha_soat')         ? Carbon::parse(request('fecha_soat'))->format('Y-m-d')         : null;
        $fecha_rt           = request('fecha_rt')           ? Carbon::parse(request('fecha_rt'))->format('Y-m-d')           : null;
        $fecha_rtmg         = request('fecha_rtmg')         ? Carbon::parse(request('fecha_rtmg'))->format('Y-m-d')         : null;
        $fecha_bon          = request('fecha_bon')          ? Carbon::parse(request('fecha_bon'))->format('Y-m-d')          : null;
        $fecha_vcto         = request('fecha_vcto')         ? Carbon::parse(request('fecha_vcto'))->format('Y-m-d')         : null;
        $fecha_tanque       = request('fecha_tanque')       ? Carbon::parse(request('fecha_tanque'))->format('Y-m-d')       : null;
        $fecha_tanque_cil   = request('fecha_tanque_cil')   ? Carbon::parse(request('fecha_tanque_cil'))->format('Y-m-d')   : null;
        $f_p_callao         = request('f_p_callao')         ? Carbon::parse(request('f_p_callao'))->format('Y-m-d')         : null;
        $f_circulation_card = request('f_circulation_card') ? Carbon::parse(request('f_circulation_card'))->format('Y-m-d') : null;
        $f_p_lima           = request('f_p_lima')           ? Carbon::parse(request('f_p_lima'))->format('Y-m-d')           : null;
        $f_cert_insp        = request('f_cert_insp')        ? Carbon::parse(request('f_cert_insp'))->format('Y-m-d')        : null;
        $f_curso_nor        = request('f_curso_nor')        ? Carbon::parse(request('f_curso_nor'))->format('Y-m-d')        : null;
        $f_lib_ter          = request('f_lib_ter')          ? Carbon::parse(request('f_lib_ter'))->format('Y-m-d')          : null;
        $f_cer_fum          = request('f_cer_fum')          ? Carbon::parse(request('f_cer_fum'))->format('Y-m-d')          : null;
        $f_cubicacion       = request('f_cubicacion')       ? Carbon::parse(request('f_cubicacion'))->format('Y-m-d')       : null;

        $vehicle = BlVehicle::findOrFail($id);
        $vehicle->company_id = $company_id;
        $vehicle->transportist_id = $transportist_id;
        $vehicle->fecha_soat = $fecha_soat;
        $vehicle->fecha_rt = $fecha_rt;
        $vehicle->fecha_rtmg = $fecha_rtmg;
        $vehicle->fecha_bon = $fecha_bon;
        $vehicle->fecha_vcto = $fecha_vcto;
        $vehicle->fecha_tanque = $fecha_tanque;
        $vehicle->fecha_tanque_cil = $fecha_tanque_cil;
        $vehicle->f_p_callao = $f_p_callao;
        $vehicle->f_circulation_card = $f_circulation_card;
        $vehicle->f_p_lima = $f_p_lima;
        $vehicle->f_cert_insp = $f_cert_insp;
        $vehicle->f_curso_nor = $f_curso_nor;
        $vehicle->f_lib_ter = $f_lib_ter;
        $vehicle->f_cer_fum = $f_cer_fum;
        $vehicle->f_cubicacion = $f_cubicacion;

        $vehicle->updated_at_user = Auth::user()->user;
        $vehicle->update();

        $carpeta = "uploads/BlueLine/vehiculos";

        if (request()->file('soat_pdf_modal')) {
            $fecha_soat_file = request()->file('soat_pdf_modal');
            $fecha_soat_ext = strtolower($fecha_soat_file->getClientOriginalExtension());
            $fecha_soat_file->storeAs($carpeta, 'fecha_soat' . '-' . $vehicle->id . '.' . $fecha_soat_ext, 'public');
        }

        if (request()->file('rt_pdf_modal')) {
            $fecha_rt_file = request()->file('rt_pdf_modal');
            $fecha_rt_ext = strtolower($fecha_rt_file->getClientOriginalExtension());
            $fecha_rt_file->storeAs($carpeta, 'fecha_rt' . '-' . $vehicle->id . '.' . $fecha_rt_ext, 'public');
        }
        if (request()->file('rtmg_pdf_modal')) {
            $fecha_rtmg_file = request()->file('rtmg_pdf_modal');
            $fecha_rtmg_ext = strtolower($fecha_rtmg_file->getClientOriginalExtension());
            $fecha_rtmg_file->storeAs($carpeta, 'fecha_rtmg' . '-' . $vehicle->id . '.' . $fecha_rtmg_ext, 'public');
        }
        if (request()->file('bon_pdf_modal')) {
            $fecha_bon_file = request()->file('bon_pdf_modal');
            $fecha_bon_ext = strtolower($fecha_bon_file->getClientOriginalExtension());
            $fecha_bon_file->storeAs($carpeta, 'fecha_bon' . '-' . $vehicle->id . '.' . $fecha_bon_ext, 'public');
        }
        if (request()->file('vcto_pdf_modal')) {
            $fecha_vcto_file = request()->file('vcto_pdf_modal');
            $fecha_vcto_ext = strtolower($fecha_vcto_file->getClientOriginalExtension());
            $fecha_vcto_file->storeAs($carpeta, 'fecha_vcto' . '-' . $vehicle->id . '.' . $fecha_vcto_ext, 'public');
        }
        if (request()->file('tanque_pdf_modal')) {
            $fecha_tanque_file = request()->file('tanque_pdf_modal');
            $fecha_tanque_ext = strtolower($fecha_tanque_file->getClientOriginalExtension());
            $fecha_tanque_file->storeAs($carpeta, 'fecha_tanque' . '-' . $vehicle->id . '.' . $fecha_tanque_ext, 'public');
        }
        if (request()->file('tanque_cil_pdf_modal')) {
            $fecha_tanque_cil_file = request()->file('tanque_cil_pdf_modal');
            $fecha_tanque_cil_ext = strtolower($fecha_tanque_cil_file->getClientOriginalExtension());
            $fecha_tanque_cil_file->storeAs($carpeta, 'fecha_tanque_cil' . '-' . $vehicle->id . '.' . $fecha_tanque_cil_ext, 'public');
        }
        if (request()->file('p_callao_pdf_modal')) {
            $f_p_callao_file = request()->file('p_callao_pdf_modal');
            $f_p_callao_ext = strtolower($f_p_callao_file->getClientOriginalExtension());
            $f_p_callao_file->storeAs($carpeta, 'f_p_callao' . '-' . $vehicle->id . '.' . $f_p_callao_ext, 'public');
        }
        if (request()->file('p_lima_pdf_modal')) {
            $f_p_lima_file = request()->file('p_lima_pdf_modal');
            $f_p_lima_ext = strtolower($f_p_lima_file->getClientOriginalExtension());
            $f_p_lima_file->storeAs($carpeta, 'f_p_lima' . '-' . $vehicle->id . '.' . $f_p_lima_ext, 'public');
        }
        if (request()->file('circulation_card_pdf_modal')) {
            $f_circulation_card_file = request()->file('circulation_card_pdf_modal');
            $f_circulation_card_ext = strtolower($f_circulation_card_file->getClientOriginalExtension());
            $f_circulation_card_file->storeAs($carpeta, 'f_circulation_card' . '-' . $vehicle->id . '.' . $f_circulation_card_ext, 'public');
        }
        if (request()->file('cert_insp_pdf_modal')) {
            $f_cert_insp_file = request()->file('cert_insp_pdf_modal');
            $f_cert_insp_ext = strtolower($f_cert_insp_file->getClientOriginalExtension());
            $f_cert_insp_file->storeAs($carpeta, 'f_cert_insp' . '-' . $vehicle->id . '.' . $f_cert_insp_ext, 'public');
        }
        if (request()->file('curso_nor_pdf_modal')) {
            $f_curso_nor_file = request()->file('curso_nor_pdf_modal');
            $f_curso_nor_ext = strtolower($f_curso_nor_file->getClientOriginalExtension());
            $f_curso_nor_file->storeAs($carpeta, 'f_curso_nor' . '-' . $vehicle->id . '.' . $f_curso_nor_ext, 'public');
        }
        if (request()->file('lib_ter_pdf_modal')) {
            $f_lib_ter_file = request()->file('lib_ter_pdf_modal');
            $f_lib_ter_ext = strtolower($f_lib_ter_file->getClientOriginalExtension());
            $f_lib_ter_file->storeAs($carpeta, 'f_lib_ter' . '-' . $vehicle->id . '.' . $f_lib_ter_ext, 'public');
        }
        if (request()->file('cer_fum_pdf_modal')) {
            $f_cer_fum_file = request()->file('cer_fum_pdf_modal');
            $f_cer_fum_ext = strtolower($f_cer_fum_file->getClientOriginalExtension());
            $f_cer_fum_file->storeAs($carpeta, 'f_cer_fum' . '-' . $vehicle->id . '.' . $f_cer_fum_ext, 'public');
        }
        if (request()->file('cubicacion_pdf_modal')) {
            $f_cubicacion_file = request()->file('cubicacion_pdf_modal');
            $f_cubicacion_ext = strtolower($f_cubicacion_file->getClientOriginalExtension());
            $f_cubicacion_file->storeAs($carpeta, 'f_cubicacion' . '-' . $vehicle->id . '.' . $f_cubicacion_ext, 'public');
        }

        $type = 3;
        $title = '¡Ok!';
        $msg = 'Placa vehicular actualizado exitosamente.';
        $url = route('dashboard.blue_plate');

        return response()->json([
            'type'    => $type,
            'title'    => $title,
            'msg'    => $msg,
            'url'    => $url,
        ]);
    }

    public function exportExcel()
    {

        $vehicles = BlVehicle::leftjoin('companies', 'companies.id', 'bl_vehicles.company_id')
            ->leftjoin('brands', 'brands.id', 'bl_vehicles.brand_id')
            ->leftjoin('body_types', 'body_types.id', 'bl_vehicles.body_type_id')
            ->leftjoin('modelos', 'modelos.id', 'bl_vehicles.modelo_id')
            ->leftjoin('bl_clients', 'bl_clients.id', 'bl_vehicles.cliente_id')
            ->leftjoin('bl_clases', 'bl_clases.id', 'bl_vehicles.clase_id')
            ->leftjoin('bl_asesores', 'bl_asesores.id', 'bl_vehicles.asesor_id')
            ->leftjoin('bl_services', 'bl_services.id', 'bl_vehicles.servicio_id')
            ->select(
                'bl_vehicles.id as id',
                'companies.name as company_name',
                'bl_vehicles.plate as plate',
                'bl_vehicles.year_model as year_model',
                'bl_vehicles.motor as motor',
                'bl_vehicles.chasis as chasis',
                'bl_vehicles.eje as ejes',
                'bl_vehicles.cng as cng',
                'bl_vehicles.cna as cna',
                'brands.description as brand_description',
                'body_types.description as body_types_description',
                'modelos.description as modelos_description',
                'bl_clients.description as clients_description',
                'bl_clases.description as clases_description',
                'bl_asesores.name as name_asesores',
                'bl_services.description as services_description',
                'bl_vehicles.fecha_soat as fecha_soat',
                'bl_vehicles.fecha_rt as fecha_rt',
                'bl_vehicles.fecha_rtmg as fecha_rtmg',
                'bl_vehicles.fecha_bon as fecha_bon',
                'bl_vehicles.fecha_vcto as fecha_vcto',
                'bl_vehicles.fecha_tanque as fecha_tanque',
                'bl_vehicles.fecha_tanque_cil as fecha_tanque_cil',
                'bl_vehicles.f_p_callao as f_p_callao',
                'bl_vehicles.f_circulation_card as f_circulation_card',
                'bl_vehicles.f_p_lima as f_p_lima',
                'bl_vehicles.f_cert_insp as f_cert_insp',
                'bl_vehicles.f_curso_nor as f_curso_nor',
                'bl_vehicles.f_lib_ter as f_lib_ter',
                'bl_vehicles.f_cer_fum as f_cer_fum'


            )
            //    ->whereIn('bl_vehicles.company_id', [4])
            ->orderBy('bl_vehicles.id', 'desc')
            ->get();

        $datetime = CarbonImmutable::now()->format('d/m/Y h:i:s a');

        $excel = new VehiclePlateReportExport($datetime, $vehicles);

        return Excel::download($excel, 'reporte-placa-vehiculos.xlsx');
    }

    public function detail()
    {

        $timestamp = now()->timestamp;

        $id = request('id');
        $vehicle = BlVehicle::findOrFail($id);

        $vehicle->url_fecha_soat = asset('uploads/BlueLine/vehiculos/fecha_soat-' . $vehicle->id . '.pdf') . '?t=' . $timestamp;

        $vehicle->url_fecha_soat = asset('uploads/BlueLine/vehiculos/fecha_soat-' . $vehicle->id . '.pdf') . '?t=' . $timestamp;
        $vehicle->url_fecha_rt = asset('uploads/BlueLine/vehiculos/fecha_rt-' . $vehicle->id . '.pdf') . '?t=' . $timestamp;
        $vehicle->url_fecha_rtmg = asset('uploads/BlueLine/vehiculos/fecha_rtmg-' . $vehicle->id . '.pdf') . '?t=' . $timestamp;
        $vehicle->url_fecha_bon = asset('uploads/BlueLine/vehiculos/fecha_bon-' . $vehicle->id . '.pdf') . '?t=' . $timestamp;
        $vehicle->url_fecha_vcto = asset('uploads/BlueLine/vehiculos/fecha_vcto-' . $vehicle->id . '.pdf') . '?t=' . $timestamp;
        $vehicle->url_fecha_tanque = asset('uploads/BlueLine/vehiculos/fecha_tanque-' . $vehicle->id . '.pdf') . '?t=' . $timestamp;
        $vehicle->url_fecha_tanque_cil = asset('uploads/BlueLine/vehiculos/fecha_tanque_cil-' . $vehicle->id . '.pdf') . '?t=' . $timestamp;
        $vehicle->url_f_p_callao = asset('uploads/BlueLine/vehiculos/f_p_callao-' . $vehicle->id . '.pdf') . '?t=' . $timestamp;
        $vehicle->url_f_circulation_card = asset('uploads/BlueLine/vehiculos/f_circulation_card-' . $vehicle->id . '.pdf') . '?t=' . $timestamp;
        $vehicle->url_f_p_lima = asset('uploads/BlueLine/vehiculos/f_p_lima-' . $vehicle->id . '.pdf') . '?t=' . $timestamp;
        $vehicle->url_f_cert_insp = asset('uploads/BlueLine/vehiculos/f_cert_insp-' . $vehicle->id . '.pdf') . '?t=' . $timestamp;
        $vehicle->url_f_curso_nor = asset('uploads/BlueLine/vehiculos/f_curso_nor-' . $vehicle->id . '.pdf') . '?t=' . $timestamp;
        $vehicle->url_f_lib_ter = asset('uploads/BlueLine/vehiculos/f_lib_ter-' . $vehicle->id . '.pdf') . '?t=' . $timestamp;
        $vehicle->url_f_cer_fum = asset('uploads/BlueLine/vehiculos/f_cer_fum-' . $vehicle->id . '.pdf') . '?t=' . $timestamp;
        $vehicle->url_f_cubicacion = asset('uploads/BlueLine/vehiculos/f_cubicacion-' . $vehicle->id . '.pdf') . '?t=' . $timestamp;


        return $vehicle;
    }

    public function delete()
    {
        $id = request('id');
        $element = BlVehicle::findOrFail($id);
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
        $plate = request('plate');
        $year_model = request('year_model');
        $motor = request('motor');
        $chasis = request('chasis');
        $eje = request('eje');
        $cng = request('cng');
        $cna = request('cna');
        $transportist_id = request('company_id');
        $clase_id = request('blclase_id');
        $body_type_id = request('body_type_id');
        $brand_id = request('brand_id');
        $modelo_id = request('modelo_id');
        $fecha_soat = request('fecha_soat');
        $fecha_rt = request('fecha_rt');
        $fecha_rtmg = request('fecha_rtmg');
        $fecha_bon = request('fecha_bon');
        $fecha_vcto = request('fecha_vcto');
        $fecha_tanque = request('fecha_tanque');
        $fecha_tanque_cil = request('fecha_tanque_cil');
        $f_p_callao = request('f_p_callao');
        $f_p_lima = request('f_p_lima');
        $f_circulation_card = request('f_circulation_card');
        $f_cert_insp = request('f_cert_insp');
        $f_curso_nor = request('f_curso_nor');
        $f_lib_ter = request('f_lib_ter');
        $f_cer_fum = request('f_cer_fum');
        $f_cubicacion = request('f_cubicacion');

        $company = Company::select('name')->where('id', '=', $company_id)->first();
        $transportista = Company::select('document_number')->where('id', '=', $transportist_id)->first();

        $vehicle = new BlVehicle();
        $vehicle->company_id = $company_id;
        $vehicle->dgh = $company->name;
        $vehicle->plate = $plate;
        $vehicle->motor = $motor;
        $vehicle->chasis = $chasis;
        $vehicle->eje = $eje;
        $vehicle->cng = $cng;
        $vehicle->cna = $cna;

        $fecha_soat         = $fecha_soat         ? \Carbon\Carbon::createFromFormat('d-m-Y', $fecha_soat)->format('Y-m-d') : null;
        $fecha_rt           = $fecha_rt           ? \Carbon\Carbon::createFromFormat('d-m-Y', $fecha_rt)->format('Y-m-d') : null;
        $fecha_rtmg         = $fecha_rtmg         ? \Carbon\Carbon::createFromFormat('d-m-Y', $fecha_rtmg)->format('Y-m-d') : null;
        $fecha_bon          = $fecha_bon          ? \Carbon\Carbon::createFromFormat('d-m-Y', $fecha_bon)->format('Y-m-d') : null;
        $fecha_vcto         = $fecha_vcto         ? \Carbon\Carbon::createFromFormat('d-m-Y', $fecha_vcto)->format('Y-m-d') : null;
        $fecha_tanque       = $fecha_tanque       ? \Carbon\Carbon::createFromFormat('d-m-Y', $fecha_tanque)->format('Y-m-d') : null;
        $fecha_tanque_cil   = $fecha_tanque_cil   ? \Carbon\Carbon::createFromFormat('d-m-Y', $fecha_tanque_cil)->format('Y-m-d') : null;
        $f_p_callao         = $f_p_callao         ? \Carbon\Carbon::createFromFormat('d-m-Y', $f_p_callao)->format('Y-m-d') : null;
        $f_p_lima           = $f_p_lima           ? \Carbon\Carbon::createFromFormat('d-m-Y', $f_p_lima)->format('Y-m-d') : null;
        $f_circulation_card = $f_circulation_card ? \Carbon\Carbon::createFromFormat('d-m-Y', $f_circulation_card)->format('Y-m-d') : null;
        $f_cert_insp        = $f_cert_insp        ? \Carbon\Carbon::createFromFormat('d-m-Y', $f_cert_insp)->format('Y-m-d') : null;
        $f_curso_nor        = $f_curso_nor        ? \Carbon\Carbon::createFromFormat('d-m-Y', $f_curso_nor)->format('Y-m-d') : null;
        $f_lib_ter          = $f_lib_ter          ? \Carbon\Carbon::createFromFormat('d-m-Y', $f_lib_ter)->format('Y-m-d') : null;
        $f_cer_fum          = $f_cer_fum          ? \Carbon\Carbon::createFromFormat('d-m-Y', $f_cer_fum)->format('Y-m-d') : null;
        $f_cubicacion       = $f_cubicacion       ? \Carbon\Carbon::createFromFormat('d-m-Y', $f_cubicacion)->format('Y-m-d') : null;

        $vehicle->year_model = $year_model;
        $vehicle->fecha_soat = $fecha_soat;
        $vehicle->fecha_rt = $fecha_rt;
        $vehicle->fecha_rtmg = $fecha_rtmg;
        $vehicle->fecha_bon = $fecha_bon;
        $vehicle->fecha_vcto = $fecha_vcto;
        $vehicle->fecha_tanque = $fecha_tanque;
        $vehicle->fecha_tanque_cil = $fecha_tanque_cil;
        $vehicle->f_p_callao = $f_p_callao;
        $vehicle->f_p_lima = $f_p_lima;
        $vehicle->f_circulation_card = $f_circulation_card;
        $vehicle->transportist_id = $transportist_id;
        $vehicle->document_number = $transportista->document_number;
        $vehicle->clase_id = $clase_id;
        $vehicle->body_type_id = $body_type_id;
        $vehicle->brand_id = $brand_id;
        $vehicle->modelo_id = $modelo_id;
        $vehicle->f_cert_insp = $f_cert_insp;
        $vehicle->f_curso_nor = $f_curso_nor;
        $vehicle->f_lib_ter = $f_lib_ter;
        $vehicle->f_cer_fum = $f_cer_fum;
        $vehicle->f_cubicacion = $f_cubicacion;

        $vehicle->created_at_user = Auth::user()->user;
        $vehicle->save();

        $carpeta = "uploads/BlueLine/vehiculos";

        if ($fecha_soat_file = request()->file('soat_pdf')) {
            $fecha_soat_ext = strtolower($fecha_soat_file->getClientOriginalExtension());
            $fecha_soat_file->storeAs($carpeta, 'fecha_soat' . '-' . $vehicle->id . '.' . $fecha_soat_ext, 'public');
        }

        if ($fecha_rt_file = request()->file('rt_pdf')) {
            $fecha_rt_ext = strtolower($fecha_rt_file->getClientOriginalExtension());
            $fecha_rt_file->storeAs($carpeta, 'fecha_rt' . '-' . $vehicle->id . '.' . $fecha_rt_ext, 'public');
        }

        if ($fecha_rtmg_file = request()->file('rtmg_pdf')) {
            $fecha_rtmg_ext = strtolower($fecha_rtmg_file->getClientOriginalExtension());
            $fecha_rtmg_file->storeAs($carpeta, 'fecha_rtmg' . '-' . $vehicle->id . '.' . $fecha_rtmg_ext, 'public');
        }

        if ($fecha_bon_file = request()->file('bon_pdf')) {
            $fecha_bon_ext = strtolower($fecha_bon_file->getClientOriginalExtension());
            $fecha_bon_file->storeAs($carpeta, 'fecha_bon' . '-' . $vehicle->id . '.' . $fecha_bon_ext, 'public');
        }

        if ($fecha_vcto_file = request()->file('vcto_pdf')) {
            $fecha_vcto_ext = strtolower($fecha_vcto_file->getClientOriginalExtension());
            $fecha_vcto_file->storeAs($carpeta, 'fecha_vcto' . '-' . $vehicle->id . '.' . $fecha_vcto_ext, 'public');
        }

        if ($fecha_tanque_file = request()->file('tanque_pdf')) {
            $fecha_tanque_ext = strtolower($fecha_tanque_file->getClientOriginalExtension());
            $fecha_tanque_file->storeAs($carpeta, 'fecha_tanque' . '-' . $vehicle->id . '.' . $fecha_tanque_ext, 'public');
        }

        if ($fecha_tanque_cil_file = request()->file('tanque_cil_pdf')) {
            $fecha_tanque_cil_ext = strtolower($fecha_tanque_cil_file->getClientOriginalExtension());
            $fecha_tanque_cil_file->storeAs($carpeta, 'fecha_tanque_cil' . '-' . $vehicle->id . '.' . $fecha_tanque_cil_ext, 'public');
        }

        if ($f_p_callao_file = request()->file('p_callao_pdf')) {
            $f_p_callao_ext = strtolower($f_p_callao_file->getClientOriginalExtension());
            $f_p_callao_file->storeAs($carpeta, 'f_p_callao' . '-' . $vehicle->id . '.' . $f_p_callao_ext, 'public');
        }

        if ($f_p_lima_file = request()->file('p_lima_pdf')) {
            $f_p_lima_ext = strtolower($f_p_lima_file->getClientOriginalExtension());
            $f_p_lima_file->storeAs($carpeta, 'f_p_lima' . '-' . $vehicle->id . '.' . $f_p_lima_ext, 'public');
        }

        if ($f_circulation_card_file = request()->file('circulation_card_pdf')) {
            $f_circulation_card_ext = strtolower($f_circulation_card_file->getClientOriginalExtension());
            $f_circulation_card_file->storeAs($carpeta, 'f_circulation_card' . '-' . $vehicle->id . '.' . $f_circulation_card_ext, 'public');
        }

        if ($f_cert_insp_file = request()->file('cert_insp_pdf')) {
            $f_cert_insp_ext = strtolower($f_cert_insp_file->getClientOriginalExtension());
            $f_cert_insp_file->storeAs($carpeta, 'f_cert_insp' . '-' . $vehicle->id . '.' . $f_cert_insp_ext, 'public');
        }

        if ($f_curso_nor_file = request()->file('curso_nor_pdf')) {
            $f_curso_nor_ext = strtolower($f_curso_nor_file->getClientOriginalExtension());
            $f_curso_nor_file->storeAs($carpeta, 'f_curso_nor' . '-' . $vehicle->id . '.' . $f_curso_nor_ext, 'public');
        }

        if ($f_lib_ter_file = request()->file('lib_ter_pdf')) {
            $f_lib_ter_ext = strtolower($f_lib_ter_file->getClientOriginalExtension());
            $f_lib_ter_file->storeAs($carpeta, 'f_lib_ter' . '-' . $vehicle->id . '.' . $f_lib_ter_ext, 'public');
        }

        if ($f_cer_fum_file = request()->file('cer_fum_pdf')) {
            $f_cer_fum_ext = strtolower($f_cer_fum_file->getClientOriginalExtension());
            $f_cer_fum_file->storeAs($carpeta, 'f_cer_fum' . '-' . $vehicle->id . '.' . $f_cer_fum_ext, 'public');
        }

        if ($f_cubicacion_file = request()->file('cubicacion_pdf')) {
            $f_cubicacion_ext = strtolower($f_cubicacion_file->getClientOriginalExtension());
            $f_cubicacion_file->storeAs($carpeta, 'f_cubicacion' . '-' . $vehicle->id . '.' . $f_cubicacion_ext, 'public');
        }



        $type = 3;
        $title = '¡Ok!';
        $msg = 'Placa vehicular creado exitosamente.';
        $url = route('dashboard.blue_plate');

        return response()->json([
            'type'    => $type,
            'title'    => $title,
            'msg'    => $msg,
            'url'   => $url
        ]);
    }
}
