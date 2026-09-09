<?php

namespace App\Http\Controllers\Backend;

use App\Agent;
use App\Company;
use App\Http\Controllers\Controller;
use App\WarehouseType;
use Auth;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
    public function index()
    {
        $companies = Company::select('id', 'name')->get();
        $agents = Agent::select('id', 'activity', 'name')->get();

        return view('backend.store')->with(compact('companies', 'agents'));
    }

    public function validateForm()
    {
        $messages = [
            'company_id.required' => 'Debe seleccionar la Compañía.',
        ];

        $rules = [
            'company_id' => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function list()
    {
        $company_id = request('company_id');

        $elements = WarehouseType::leftjoin('agents', 'agents.id', '=', 'warehouse_types.agent_id')
            ->select(
                'warehouse_types.id as warehouse_type_id',
                'warehouse_types.name as warehouse_type_name',
                'warehouse_types.short_name as warehouse_type_short_name',
                'warehouse_types.business_name as warehouse_type_business_name',
                'warehouse_types.ruc as warehouse_type_ruc',
                'agents.id as agent_id',
                DB::Raw('CONCAT(agents.activity," - ",agents.name) as agent_name'),
                'warehouse_types.agent_uno as warehouse_type_agent_uno',
                'warehouse_types.agent_dos as warehouse_type_agent_dos'
            )
            ->where('company_id', $company_id)
            ->get();

        return response()->json([
            'data'    => $elements,
        ]);
    }

    public function detail()
    {
        $id = request('id');

        $element = WarehouseType::findOrFail($id);

        return $element;
    }

    public function delete()
    {
        $id = request('id');
        $element = WarehouseType::findOrFail($id);
        $element->delete();
    }

    public function validateModalForm()
    {
        $messages = [
            'name.required'             => 'Debe llenar nombre del Almacén.',
            'short_name.required'       => 'Debe llenar Nombre Corto.',
            'business_name.required'    => 'Debe llenar Proveedor.',
            'ruc.required'              => 'Debe llenar RUC.',
            'agent_id.required'         => 'Debe seleccionar el Agente.',
            'agent_uno.required'        => 'Debe llenar el Registro Uno.',
            'agent_dos.required'        => 'Debe llenar el Registro Dos.',
        ];

        $rules = [
            'name'                  => 'required',
            'short_name'            => 'required',
            'business_name'         => 'required',
            'ruc'                   => 'required',
            'agent_id'              => 'required',
            'agent_uno'             => 'required',
            'agent_dos'             => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }


    public function store()
    {
        $this->validateModalForm();

        $id = request('id');
        $company_id = request('company_id');
        $name = request('name');
        $short_name = request('short_name');
        $business_name = request('business_name');
        $ruc = request('ruc');
        $agent_id = request('agent_id');
        $agent_uno = request('agent_uno');
        $agent_dos = request('agent_dos');


        if (isset($id)) {
            $element = WarehouseType::find($id);
            $msg = 'Registro actualizado exitosamente';
        } else {
            $element = new WarehouseType();
            $msg = 'Registro creado exitosamente';
        }
        $element->company_id = $company_id;
        $element->name = $name;
        $element->short_name = $short_name;
        $element->business_name = $business_name;
        $element->ruc = $ruc;
        $element->agent_id = $agent_id;
        $element->agent_uno = $agent_uno;
        $element->agent_dos = $agent_dos;
        $element->save();

        $type = 1;
        $title = '¡Ok!';

        return response()->json([
            'type'    => $type,
            'title'    => $title,
            'msg'    => $msg,
        ]);
    }
}
