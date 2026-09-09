<?php

namespace App\Http\Controllers\Backend;

use App\Area;
use App\Company;
use App\Exports\ServiceReportExport;
use App\Http\Controllers\Controller;
use App\Sede;
use App\Service;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ServiceController extends Controller
{
    public function index()
    {
        if (Auth::user()->id == 47) {
            $companies = Company::select('id', 'name')->whereIn('id', [5])->get();
        } else {
            $companies = Company::select('id', 'name')->get();
        }

        $sedes = Sede::select('id', 'name')->get();
        return view('backend.services')->with(compact('companies', 'sedes'));
    }

    public function validateForm()
    {
        $messages = [
            'company_id.required'           => 'Debe seleccionar una compañía.',
        ];

        $rules = [
            'company_id' => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function listServices()
    {

        $company_id = request('company_id');

        $services = Service::join('companies', 'services.company_id', 'companies.id')
            ->join('sedes', 'services.sede_id', 'sedes.id')
            ->join('areas', 'services.area_id', 'areas.id')
            ->select(
                'services.id as service_id',
                'companies.name as company_name',
                'sedes.name as sede_name',
                DB::Raw('CONCAT(areas.name," - ",areas.short_name) as area_name'),
                'services.code as service_code',
                'services.name as service_name',
                'services.cost as service_cost',
                'services.price as service_price'
            )
            ->where('services.company_id', $company_id)
            ->get();

        return response()->json([
            'data' => $services,
        ]);
    }

    public function delete()
    {

        $id = request('id');
        $obj = Service::findOrFail($id);
        $obj->delete();
    }

    public function getAreas()
    {

        $company_id = request('company_id');
        $areas = Area::select('id', 'name', 'short_name')
            ->where('company_id', $company_id)
            ->get();

        $sedes = Sede::select('id', 'name', 'short_name')
            ->where('company_id', $company_id)
            ->get();

        return response()->json([
            'areas' => $areas,
            'sedes' => $sedes
        ]);
    }

    public function detail()
    {
        $id = request('id');

        $services = Service::join('companies', 'services.company_id', 'companies.id')
            ->join('sedes', 'services.sede_id', 'sedes.id')
            ->join('areas', 'services.area_id', 'areas.id')
            ->select(
                'services.id as service_id',
                'services.sede_id as sede_id',
                'services.area_id as area_id',
                'services.company_id as company_id',
                'companies.name as company_name',
                'sedes.name as sede_name',
                'areas.name as area_name',
                'services.code as code',
                'services.name as name',
                'services.cost as cost',
                'services.price as price'
            )
            ->findOrFail($id);

        return $services;
    }

    public function validateModalForm()
    {
        $messages = [
            'name.required'                    => 'La Descripción es obligatoria.',
            'cost.required'                 => 'El Costo es obligatorio.',
            'cost.numeric'                  => 'El Costo tiene que ser un número.',
            'cost.min'                      => 'El Costo mínimo es 0.0001',
            'price.required'                => 'El Precio es obligatorio.',
            'price.numeric'                 => 'El Precio tiene que ser un número.',
            'price.min'                     => 'El Precio mínimo es 0.0001',
            'stock_good.required'                => 'El Stock es obligatorio.',
            'stock_good.numeric'                 => 'El Stock tiene que ser un número.',
            'stock_good.min'                     => 'El Stock mínimo es 0',
            'sede_id.required'              => 'Debe seleccionar una Sede.',
            'area_id.required'              => 'Debe seleccionar el Área.',
        ];

        $rules = [
            'name'              => 'required',
            'cost'              => 'required|numeric|min:0.0001',
            'price'             => 'required|numeric|min:0.0001',
            'stock_good'        => 'required|numeric|min:0',
            'area_id'           => 'required',
            'sede_id'           => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function store()
    {
        $this->validateModalForm();

        $company_id = request('company_id');
        $service_id = request('service_id');
        $name = request('name');
        $cost = request('cost');
        $price = request('price');
        $stock_good = request('stock_good');
        $sede_id = request('sede_id');
        $area_id = request('area_id');

        $company_short_name = Company::select('short_name')
            ->findOrFail($company_id);
        $sede_short_name = Sede::select('short_name')
            ->findOrFail($sede_id);

        if (isset($service_id)) {
            $element = Service::find($service_id);
            $msg = 'Registro actualizado exitosamente';
            $element->updated_at_user = Auth::user()->user;
        } else {
            $element = new Service();
            $msg = 'Registro creado exitosamente';
            $element->company_id = $company_id;
            $element->created_at_user = Auth::user()->user;
            $element->updated_at_user = Auth::user()->user;
        }

        $element->sede_id = $sede_id;
        $element->area_id = $area_id;
        $element->name = $name;
        $element->cost = $cost;
        $element->price = $price;
        $element->stock_good = $stock_good;
        $element->save();

        if (!isset($service_id)) {
            $element->code = $company_short_name->short_name . '-' . $sede_short_name->short_name . '-' . $element->id;
            $element->update();
        }

        $type = 1;
        $title = '¡Ok!';

        return response()->json([
            'type'    => $type,
            'title'    => $title,
            'msg'    => $msg,
        ]);
    }

    public function exportRecord()
    {
        $company_id = request('company_id');

        $services = Service::join('companies', 'services.company_id', 'companies.id')
            ->join('sedes', 'services.sede_id', 'sedes.id')
            ->join('areas', 'services.area_id', 'areas.id')
            ->select(
                'services.id as service_id',
                'services.sede_id as sede_id',
                'services.area_id as area_id',
                'services.company_id as company_id',
                'companies.name as company_name',
                'sedes.name as sede_name',
                'areas.name as area_name',
                'services.code as code',
                'services.name as name',
                'services.cost as cost',
                'services.price as price'
            )
            ->where('services.company_id', $company_id)
            ->get();

        $datetime = CarbonImmutable::now()->format('d/m/Y h:m:s a');

        $excel = new ServiceReportExport($datetime, $services);
        return Excel::download($excel, 'reporte-articulos.xlsx');
    }
}
