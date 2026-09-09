<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Client;
use App\ClientAddress;
use App\BlVehicle;
use App\Company;

class BlueMaintenanceController extends Controller
{
    public function index()
    {
        $companies = Company::select('id', 'name')->whereIn('id', [4])->get();
        $bl_vehicles = BlVehicle::select('id', 'plate')->get();
        return view('backend.blue_maintenance')->with(compact('companies', 'bl_vehicles'));
    }

    public function validateForm()
    {
        $messages = [
            'company_id.required'    => 'Debe seleccionar una Compañía.',
            'number_plate.required'  => 'Debes de ingresar una placa'
        ];
        $rules = [
            'company_id'    => 'required',
            'number_plate'    => 'required',
        ];
        request()->validate($rules, $messages);
        return request()->all();
    }

    public function list()
    {
        $vehicles = BlVehicle::leftjoin('companies', 'companies.id', 'bl_vehicles.company_id')
            ->select(
                'bl_vehicles as id',
                'companies.name as company_name',
                'bl_vehicles.client_name as client_name',
                'bl_vehicles.plate as plate'
            )
            ->whereIn('bl_vehicles.company_id', [4])
            ->orderBy('bl_vehicles.id', 'desc')
            ->get();
        return $vehicles;
    }

    public function detail()
    {
        $id = request('id');
        $vehicle = BlVehicle::findOrFail($id);
    }

    public function validateModalForm()
    {
        $messages = [
            'document_type_id.required'            => 'El Tipo de Documento es obligatorio.',
            'document_number.required'            => 'El Número de Documento es obligatorio.',
            //	'client_code.required'				=> 'El Código de Cliente es obligatorio.',
            'business_name.required'            => 'El Nombre o Razón Social es obligatorio.',
            'address.required'                    => 'La Dirección es obligatoria.',
            'ubigeo_id.required'                => 'El Ubigeo es obligatorio.',
            'contact_name_1.required'            => 'El Nombre de Contacto 1 es obligatorio.',
            // 'email.required_if'					=> 'El Email es obligatorio.',
            // 'phone_number_1.required'			=> 'El Teléfono 1 es obligatorio.',
            'business_unit_id.required'            => 'La Unidad de Negocio es obligatoria.',
            'zone_id.required'                    => 'La Zona es obligatoria.',
            'channel_id.required'                => 'El Canal es obligatorio.',
            'route_id.required'                    => 'La Ruta es obligatoria.',
            'sector_id.required'                => 'El Sector es obligatorio.',
            'payment_id.required'                => 'La Condición de Pago es obligatoria.',
            'credit_limit.required_if'            => 'Debe digitar un Límite de Crédito.',
            'credit_limit_days.required_if'        => 'Debe digitar los Días de Crédito.',
            'perception_percentage_id.required'    => 'El Agente de Percepción es obligatorio.',


        ];

        $rules = [
            'document_type_id'            => 'required',
            'document_number'            => 'required',
            //	'client_code'				=> 'required',
            'business_name'                => 'required',
            'address'                    => 'required',
            'ubigeo_id'                    => 'required',
            'contact_name_1'            => 'required',
            // 'email'						=> 'required_if:document_type_id,1',
            // 'phone_number_1'			=> 'required',
            'business_unit_id'            => 'required',
            'zone_id'                    => 'required',
            'channel_id'                => 'required',
            'route_id'                    => 'required',
            'sector_id'                    => 'required',
            'payment_id'                => 'required',
            'credit_limit'                => 'required_if:payment_id,2',
            'credit_limit_days'            => 'required_if:payment_id,2',
            'perception_percentage_id'    => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function store()
    {
        $this->validateModalForm();

        $id = request('id');
        $company_id = request('company_id');
        $business_name = request('business_name');
        $document_type_id = request('document_type_id');
        $document_number = request('document_number');
        $client_code = request('client_code');
        $address = request('address');
        $address_reference = request('address_reference');
        $ubigeo_id = request('ubigeo_id');
        $gps_x = request('gps_x');
        $gps_y = request('gps_y');
        $contact_name_1 = request('contact_name_1');
        $contact_name_2 = request('contact_name_2');
        $email = request('email');
        $email_2 = request('email_2');
        $phone_number_1 = request('phone_number_1');
        $phone_number_2 = request('phone_number_2');
        $phone_number_3 = request('phone_number_3');
        $phone_number_4 = request('phone_number_4');
        $link_client_id = request('link_client_id');
        $business_unit_id = request('business_unit_id');
        $zone_id = request('zone_id');
        $channel_id = request('channel_id');
        $route_id = request('route_id');
        $sector_id = request('sector_id');
        $seller_id = request('seller_id');
        $business_type = request('client_type');
        $dgh = request('dgh');
        $activity_uno = request('activity_uno');
        $activity_id = request('activity_id');
        $activity_tres = request('activity_tres');
        $police = request('police');
        $manager = request('manager');
        $manager_id = request('manager_id');
        $manager_mail = request('manager_mail');
        $payment_id = request('payment_id');
        $credit_limit = request('credit_limit');
        $credit_limit_days = request('credit_limit_days');
        $grupo = request('grupo');
        $email_2 = request('email_2');
        $obs = request('obs');
        $estado = request('estado');
        $codigo_rep = request('codigo_rep');
        $perception_percentage_id = request('perception_percentage_id');
        $frecuencia_id = request('frecuencia_id');
        $frecuencia = request('frecuencia');
        $potencial = request('potencial');
        $zona = request('zona');
        $sector = request('sector');

        if (isset($id)) {
            $element = Client::find($id);
            $msg = 'Registro actualizado exitosamente';
            //	$element->updated_at_user = Auth::user()->user;

            $childElement = ClientAddress::where('client_id', $id)
                ->where('address_type_id', 1)
                ->where('item_number', 1)
                ->first();
            //	$childElement->updated_at_user = Auth::user()->user;
        } else {
        }
        $element->company_id = $company_id;
        $element->business_name = $business_name;
        $element->document_type_id = $document_type_id;
        $element->document_number = $document_number;
        $element->code = $client_code;
        $element->contact_name_1 = $contact_name_1;
        $element->contact_name_2 = $contact_name_2;
        $element->email = $email;
        $element->email_2 = $email_2;
        $element->phone_number_1 = $phone_number_1;
        $element->phone_number_2 = $phone_number_2;
        $element->phone_number_3 = $phone_number_3;
        $element->phone_number_4 = $phone_number_4;
        $element->link_client_id = $link_client_id;
        $element->business_unit_id = $business_unit_id;
        $element->zone_id = $zone_id;
        $element->channel_id = $channel_id;
        $element->route_id = $route_id;
        $element->sector_id = $sector_id;
        $element->seller_id = $seller_id;
        $element->business_type = $business_type;
        $element->dgh = $dgh;
        $element->activity_uno = $activity_uno;
        $element->activity_id = $activity_id;
        $element->activity_tres = $activity_tres;
        $element->police = $police;
        $element->manager = $manager;
        $element->manager_id = $manager_id;
        $element->manager_mail = $manager_mail;
        $element->payment_id = $payment_id;
        $element->grupo = $grupo;
        $element->obs = $obs;
        $element->estado = $estado;
        $element->codigo_rep = $codigo_rep;
        $element->credit_limit = $payment_id == 2 ? $credit_limit : 0;
        $element->credit_limit_days = $payment_id == 2 ? $credit_limit_days : 0;
        $element->perception_percentage_id = $perception_percentage_id;
        $element->frecuencia_id = $frecuencia_id;
        $element->frecuencia = $frecuencia;
        $element->potencial = $potencial;
        $element->zona = $zona;
        $element->sector = $sector;
        $element->save();

        $childElement->client_id = $element->id;
        $childElement->address_type_id = 1;
        $childElement->item_number = 1;
        $childElement->address = $address;
        $childElement->address_reference = $address_reference;
        $childElement->ubigeo_id = $ubigeo_id;
        $childElement->gps_x = $gps_x;
        $childElement->gps_y = $gps_y;
        $childElement->save();

        $type = 1;
        $title = '¡Ok!';

        return response()->json([
            'type'    => $type,
            'title'    => $title,
            'msg'    => $msg,
        ]);
    }

    public function delete()
    {
        $id = request('id');
        $element = Client::findOrFail($id);
        $element->delete();
    }
}
