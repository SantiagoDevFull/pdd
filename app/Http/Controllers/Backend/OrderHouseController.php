<?php

namespace App\Http\Controllers\Backend;

use App\Article;
use App\BusinessUnit;
use App\Client;
use App\ClientAddress;
use App\ClientChannel;
use App\ClientRoute;
use App\ClientSector;
use App\ClientType;
use App\ClientZone;
use App\Company;
use App\DocumentType;
use App\Employee;
use App\HouseOrder;
use App\Http\Controllers\Controller;
use App\OriginHouse;
use App\OsiAgent;
use App\Payment;
use App\PaymentMethod;
use App\Rate;
use App\Ubigeo;
use App\Voucher;
use App\VoucherDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderHouseController extends Controller
{
    public function index()
    {
        $origin_houses = OriginHouse::select('id', 'name')->get();
        $articles = Article::select('id', 'name')->whereIn('id', [10385, 10386, 10387, 10388, 10273, 10274, 10275, 10276, 25, 26, 1284, 1285])->get();
        $payment_methods = PaymentMethod::whereIn('id', [1, 11])->get();
        $max_correlative = HouseOrder::max('correlative') ? HouseOrder::max('correlative') + 1 : 1;
        $current_date = date(DATE_ATOM, mktime(0, 0, 0));

        $companies = Company::select('id', 'name')->whereIn('id', [2])->get();
        $document_types = DocumentType::select('id', 'name')->whereIn('id', [1,2, 5])->get();
        $payments = Payment::select('id', 'name')->whereIn('id', [1])->get();
        $perceptions = Rate::select('id', 'description', 'value')
            ->where('id', 3)
            ->orderBy('id', 'desc')
            ->get();
        $perceptions->map(function ($item, $index) {
            $item->value = number_format($item->value, 1, '.', '');
        });

        $business_units = BusinessUnit::select('id', 'name')->whereIn('id', [3])->get();
        $client_zones = ClientZone::select('id', 'name')->whereIn('id', [5])->get();
        $client_channels = ClientChannel::select('id', 'name')->whereIn('id', [7, 24])->get();
        $client_types = ClientType::select('id', 'name')->get();
        $client_routes = ClientRoute::select('id', 'name')->whereIn('id', [81,83])->get();
        $client_sectors = ClientSector::select('id', 'name')->whereIn('id', [4])->get();
        $osi_agents = OsiAgent::select('id', 'name', 'short_name')->get();

        return view('backend.order_house')->with(compact(
            'origin_houses',
            'articles',
            'payment_methods',
            'max_correlative',
            'current_date',
            'companies',
            'document_types',
            'payments',
            'perceptions',
            'business_units',
            'client_zones',
            'client_channels',
            'client_types',
            'client_routes',
            'client_sectors',
            'osi_agents'
        ));
    }

    public function getVoucher()
    {
        $voucher = Voucher::where('serie_number', 'B310')->max('voucher_number');
        $nextVoucher = $voucher ? $voucher + 1 : 1;

        return $nextVoucher;
    }

    public function validateModalForm()
    {
        $messages = [
            'document_type_id.required'            => 'El Tipo de Documento es obligatorio.',
            'document_number.required'            => 'El Número de Documento es obligatorio.',
            'business_name.required'            => 'El Nombre o Razón Social es obligatorio.',
            'business_unit_id.required'            => 'La Unidad de Negocio es obligatoria.',
            'zone_id.required'                    => 'La Zona es obligatoria.',
            'channel_id.required'                => 'El Canal es obligatorio.',
            'route_id.required'                    => 'La Ruta es obligatoria.',
            'sector_id.required'                => 'El Sector es obligatorio.',
            'payment_id.required'                => 'La Condición de Pago es obligatoria.',
            'credit_limit.required_if'            => 'Debe digitar un Límite de Crédito.',
            'credit_limit_days.required_if'        => 'Debe digitar los Días de Crédito.',
            'perception_percentage_id.required'    => 'El Agente de Percepción es obligatorio.',
            'estado.required'                   => 'El Estado es obligatorio.',
        ];

        $rules = [
            'document_type_id'            => 'required',
            'document_number'            => 'required',
            'business_name'                => 'required',
            'business_unit_id'            => 'required',
            'zone_id'                    => 'required',
            'channel_id'                => 'required',
            'route_id'                    => 'required',
            'sector_id'                    => 'required',
            'payment_id'                => 'required',
            'credit_limit'                => 'required_if:payment_id,2',
            'credit_limit_days'            => 'required_if:payment_id,2',
            'perception_percentage_id'    => 'required',
            'estado'                 => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function registerClient()
    {
        $this->validateModalForm();

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
        $phone_number_1 = request('phone_number_1');
        $business_unit_id = request('business_unit_id');
        $zone_id = request('zone_id');
        $channel_id = request('channel_id');
        $route_id = request('route_id');
        $sector_id = request('sector_id');
        $seller_id = request('seller_id');
        $business_type = request('client_type');
        $activity_uno = request('activity_uno');
        $activity_tres = request('activity_tres');
        $manager = request('manager');
        $manager_id = request('manager_id');
        $payment_id = request('payment_id');
        $credit_limit = request('credit_limit');
        $credit_limit_days = request('credit_limit_days');
        $codigo_rep = request('codigo_rep');
        $perception_percentage_id = request('perception_percentage_id');
        $frecuencia_id = request('frecuencia_id');
        $frecuencia = request('frecuencia');
        $potencial = request('potencial');
        $zona = request('zona');
        $sector = request('sector');
        $estado = request('estado');
        $razon = request('razon');

        $element = new Client();
        $msg = 'Registro creado exitosamente';
        $element->created_at_user = Auth::user()->user;
        $element->updated_at_user = Auth::user()->user;

        $childElement = new ClientAddress();
        $childElement->created_at_user = Auth::user()->user;
        $childElement->updated_at_user = Auth::user()->user;

        $element->company_id = $company_id;
        $element->business_name = $business_name;
        $element->document_type_id = $document_type_id;
        $element->document_number = $document_number;
        $element->code = $client_code;
        $element->contact_name_1 = $contact_name_1;
        $element->phone_number_1 = $phone_number_1;
        $element->business_unit_id = $business_unit_id;
        $element->zone_id = $zone_id;
        $element->channel_id = $channel_id;
        $element->route_id = $route_id;
        $element->sector_id = $sector_id;
        $element->seller_id = $seller_id;
        $element->business_type = $business_type;
        $element->activity_uno = $activity_uno;
        $element->activity_tres = $activity_tres;
        $element->manager = $manager;
        $element->manager_id = $manager_id;
        $element->payment_id = $payment_id;
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
        $element->razon = $razon;
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
            'id'    => $element->id,
            'client_name'    => $element->business_name,
            'type'    => $type,
            'title'    => $title,
            'msg'    => $msg,
        ]);
    }

    public function getUbigeos()
    {
        $q = request('q');

        $ubigeos = Ubigeo::select('id', 'district', 'province', 'department', 'country')
            ->where('district', 'like', '%' . $q . '%')
            ->orderBy('district', 'asc')
            ->get();

        $ubigeos->map(function ($item, $index) {
            $item->text = $item->district . ' - ' . $item->province . ' - ' . $item->department . ' - ' . $item->country;
            unset($item->district);
            unset($item->province);
            unset($item->department);
            unset($item->country);
            return $item;
        });

        return $ubigeos;
    }

    public function validateForm()
    {
        $messages = [
            'client_id.required'         => 'Debe seleccionar Cliente.',
            'origin_id.required'         => 'Debe seleccionar Origen.',
            'serie.required'             => 'Debe completar Serie.',
            'correlative.required'       => 'Debe completar Correlativo.',
            'product_type.required'      => 'Debe seleccionar Tipo de Producto.',
            'article_id.required'        => 'Debe seleccionar Producto.',
            'price.required'             => 'Debe completar Precio.',
            'price.numeric'              => 'Debe ingresar un Precio válido.',
            'price.min'                  => 'Debe ingresar un Precio mínimo 0.01',
            'quantity.required'          => 'Debe completar Cantidad.',
            'quantity.integer'           => 'Debe ingresar una Cantidad válida.',
            'quantity.min'               => 'Debe ingresar una Cantidad mínima 1',
            'monto.required_if'          => 'Debe completar Monto.',
            'monto.numeric'              => 'Debe ingresar un Monto válido.',
            'monto.min'                  => 'Debe ingresar un Monto mínimo 0.01',
            'turno.required'            => 'Debe seleccionar Turno.',
            'employee_id.required'       => 'Debe seleccionar Empleado.',
            'issue_date.required'        => 'Debe completar Fecha de Emisión.',

        ];

        $rules = [
            'client_id'                  => 'required',
            'origin_id'                  => 'required',
            'serie'                      => 'required',
            'correlative'                => 'required',
            'product_type'               => 'required',
            'article_id'                 => 'required',
            'price'                      => 'required|numeric|min:0.01',
            'quantity'                   => 'required|integer|min:1',
            'monto'                      => 'required_if:payment_method_id,1|numeric|min:0.01',
            'turno'                      => 'required',
            'employee_id'                => 'required',
            'issue_date'                 => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function delete()
    {
        $id = request('id');
        $element = HouseOrder::findOrFail($id);
        $element->updated_at_user = Auth::user()->name;
        $element->delete();

        $type = 3;
        $title = 'Bien';
        $msg = 'Pedido eliminado exitosamente.';
        $url = route('dashboard.order_house.index');

        return response()->json([
            'type'  => $type,
            'title' => $title,
            'msg'   => $msg,
            'url'   => $url
        ]);
    }


    public function store()
    {
        $this->validateForm();

        $client_id = request('client_id');
        $origin_id = request('origin_id');
        $serie = request('serie');
        $correlative = request('correlative');
        $product_type = request('product_type');
        $article_id = request('article_id');
        $price = request('price');
        $quantity = request('quantity');
        $total = request('total');
        $turno = request('turno');
        $employee_id = request('employee_id');
        $issue_date = Carbon::parse(request('issue_date'))->setTimeFromTimeString(now()->format('H:i:s'));
        $hora_llamada        = request('hora_llamada');
        $hora_entrega_balon  = request('hora_entrega_balon');
        $nuevo               = request('nuevo') ? 1 : 0;
        $recuperado          = request('recuperado') ? 1 : 0;

        $element = new HouseOrder();
        $element->client_id = $client_id;
        $element->origin_id = $origin_id;
        $element->serie = $serie;
        $element->correlative = $correlative;
        $element->product_type = $product_type;
        $element->article_id = $article_id;
        $element->price = $price;
        $element->quantity = $quantity;
        $element->total = $total;
        $element->turno = $turno;
        $element->employee_id = $employee_id;
        $element->created_at = $issue_date;
        $element->hora_llamada        = $hora_llamada;
        $element->hora_entrega_balon  = $hora_entrega_balon;
        $element->nuevo               = $nuevo;
        $element->recuperado          = $recuperado;
        $element->created_at_user = Auth::user()->name;
        $element->state = 'Pendiente de Entrega';
        $element->save();

        $type = 3;
        $title = 'Bien';
        $msg = 'Pedido registrado exitosamente.';
        $url = route('dashboard.order_house.index');

        return response()->json([
            'type'  => $type,
            'title' => $title,
            'msg'   => $msg,
            'url'   => $url
        ]);
    }

    public function getClients()
    {

        $q = request('q');

        $clients = Client::select('id', 'business_name')
            ->where('company_id', 2)
            ->where('sector_id', 4) //LOCALES
            ->where(function ($query) use ($q) {
                $query->where('business_name', 'like', "%{$q}%")
                    ->orWhere('phone_number_1', 'like', "%{$q}%");
            })
            ->get();

        $clients->map(function ($item, $index) {
            $item->text = $item->business_name;
            unset($item->business_name);

            return $item;
        });


        return $clients;
    }
    public function getEmployees()
    {

        $q = request('q');

        $employees = Employee::select('id', 'first_name', 'last_name')
            ->where(function ($query) use ($q) {
                $query->where('first_name', 'like', '%' . $q . '%')
                    ->orWhere('last_name', 'like', '%' . $q . '%');
            })
            ->where('employee_type_id',7) //LOCALES
            ->get();

        $employees->map(function ($item, $index) {
            $item->text = $item->first_name . ' ' . $item->last_name;
            unset($item->first_name);
            unset($item->last_name);

            return $item;
        });


        return $employees;
    }

    public function list()
    {

        $elements = HouseOrder::leftjoin('clients', 'clients.id', '=', 'house_orders.client_id')
            ->leftjoin('origin_houses', 'origin_houses.id', '=', 'house_orders.origin_id')
            ->leftjoin('articles', 'articles.id', '=', 'house_orders.article_id')
            ->leftjoin('payment_methods', 'payment_methods.id', '=', 'house_orders.payment_method_id')
            ->leftjoin('employees', 'employees.id', '=', 'house_orders.employee_id')
            ->leftjoin('vouchers', 'vouchers.id', '=', 'house_orders.boleta_id')
            ->select(
                'house_orders.id as house_order_id',
                'clients.business_name as client_name',
                'origin_houses.name as origin_name',
                DB::raw("CONCAT(house_orders.serie, '-', house_orders.correlative) as voucher"),
                DB::raw("
                    CASE house_orders.product_type
                        WHEN 1 THEN 'GAS'
                        WHEN 2 THEN 'VALVULA'
                        WHEN 3 THEN 'CILINDRO'
                        ELSE 'NA'
                    END AS product_type
                "),
                'articles.name as article_name',
                'house_orders.price as house_order_price',
                'house_orders.quantity as house_order_quantity',
                'house_orders.total as house_order_total',
                'payment_methods.name as payment_method_name',
                'house_orders.monto as house_order_monto',
                'house_orders.vuelto as house_order_vuelto',
                'house_orders.turno as house_order_turno',
                DB::raw("CONCAT(employees.first_name, ' ', employees.last_name) as employee_name"),
                DB::raw("DATE_FORMAT(house_orders.hora_llamada, '%H:%i') as house_order_hora_llamada"),
                DB::raw("DATE_FORMAT(house_orders.hora_entrega_balon, '%H:%i') as house_order_hora_entrega_balon"),
                DB::raw("DATE_FORMAT(house_orders.hora_salida_pedido, '%H:%i') as house_order_hora_salida_pedido"),
                DB::raw("DATE_FORMAT(house_orders.hora_entrega_pedido, '%H:%i') as house_order_hora_entrega_pedido"),
                DB::raw("DATE_FORMAT(house_orders.hora_retorno, '%H:%i') as house_order_hora_retorno"),
                'house_orders.motivo_demora as house_order_motivo_demora',
                'house_orders.obs_cli as house_order_obs_cli',
                'house_orders.state as house_order_state',
                DB::Raw('CONCAT(vouchers.serie_number, "-", vouchers.voucher_number) as voucher_number')
            )
            ->where('house_orders.state', '!=', 'Entregado')
            ->where('house_orders.state', '!=', 'Cancelado')
            ->orderBy('house_orders.correlative', 'desc')
            ->get();


        return $elements;
    }

    public function detail()
    {
        $id = request('id');
        $element = HouseOrder::leftjoin('vouchers', 'vouchers.id', '=', 'house_orders.boleta_id')
            ->leftjoin('clients', 'clients.id', '=', 'house_orders.client_id')
            ->select(
                'house_orders.id as id',
                'house_orders.serie as serie',
                'house_orders.correlative as correlative',
                'house_orders.hora_llamada as hora_llamada',
                'house_orders.hora_entrega_balon as hora_entrega_balon',
                'house_orders.hora_salida_pedido as hora_salida_pedido',
                'house_orders.hora_entrega_pedido as hora_entrega_pedido',
                'house_orders.hora_retorno as hora_retorno',
                'house_orders.motivo_demora as motivo_demora',
                'house_orders.obs_cli as obs_cli',
                'house_orders.state as state',
                'house_orders.tiempo_total as tiempo_total',
                'vouchers.voucher_number as voucher',
                DB::raw('IF(vouchers.voucher_number IS NOT NULL, true, false) as res'),
                'house_orders.nuevo as nuevo',
                'house_orders.payment_method_id as payment_method_id',
                'house_orders.price as price',
                'house_orders.quantity as quantity',
                'house_orders.total as total',
                'house_orders.monto as monto',
                'house_orders.vuelto as vuelto',
                DB::raw("
                    CASE
                        WHEN clients.channel_id = 7 THEN DATE_ADD(house_orders.created_at, INTERVAL 30 DAY)
                        WHEN clients.channel_id = 8 THEN DATE_ADD(house_orders.created_at, INTERVAL 8 DAY)
                        ELSE NULL
                    END AS fecha_proxima
                ")
            )
            ->where('house_orders.id', $id)
            ->first();

        return $element;
    }

    public function validateModal()
    {
        $messages = [
            'price.required'               => 'El Precio es obligatorio.',
            'quantity.required'            => 'La Cantidad es obligatoria.',
        ];

        $rules = [
            'price'                        => 'required',
            'quantity'                     => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function update()
    {
        $this->validateModal();

        $voucher   = request('voucher');
        $respuesta = request('respuesta');

        $id                  = request('id');
        $hora_salida_pedido  = request('hora_salida_pedido');
        $hora_entrega_pedido = request('hora_entrega_pedido');
        $hora_retorno        = request('hora_retorno');
        $tiempo_total        = request('tiempo_total') ?: null;
        $motivo_demora       = request('motivo_demora') ?: null;
        $obs_cli             = request('obs_cli') ?: null;
        $state               = request('state');
        $price               = request('price');
        $quantity            = request('quantity');
        $total               = $price * $quantity;
        $fecha_proxima       = Carbon::parse(request('fecha_proxima'))->format('Y-m-d') ?: null;
        $payment_method_id = request('payment_method_id') ?: null;
        $monto = request('monto') ?: 0;
        $vuelto = request('vuelto') ?: 0;

        $element = HouseOrder::findOrFail($id);
        $element->hora_salida_pedido  = $hora_salida_pedido;
        $element->hora_entrega_pedido = $hora_entrega_pedido;
        $element->hora_retorno        = $hora_retorno;
        $element->tiempo_total        = $tiempo_total;
        $element->motivo_demora       = $motivo_demora;
        $element->obs_cli             = $obs_cli;
        $element->state               = $state;
        $element->price               = $price;
        $element->quantity            = $quantity;
        $element->total               = $total;
        $element->fecha_proxima       = $fecha_proxima;
        $element->payment_method_id   = $payment_method_id;
        $element->monto              = $monto;
        $element->vuelto             = $vuelto;
        $element->updated_at_user     = Auth::user()->name;

        if ($respuesta == 1) {

            $element_voucher = new Voucher();
            $element_voucher->company_id = 2;
            $element_voucher->client_id = $element->client_id;
            $element_voucher->original_client_id = $element->client_id;

            $client = Client::find($element->client_id);
            $client_address = ClientAddress::where('client_id', $element->client_id)
                ->where('address_type_id', 1)
                ->first();

            $element_voucher->client_name = $client->business_name;
            $element_voucher->client_address = $client_address->address;
            $element_voucher->voucher_type_id = 2;
            $element_voucher->serie_number = 'B310';
            $element_voucher->voucher_number = $voucher;
            $element_voucher->issue_date = $element->created_at->format('Y-m-d');
            $element_voucher->issue_hour = $element->created_at->format('H:i:s');
            $element_voucher->expiry_date = $element->created_at->format('Y-m-d');
            $element_voucher->currency_id = 1;
            $element_voucher->payment_id = 1;
            $element_voucher->taxed_operation = $element->total / 1.18;
            $element_voucher->unaffected_operation = 0;
            $element_voucher->exonerated_operation = 0;
            $element_voucher->igv = $element->total - $element->total / 1.18;
            $element_voucher->total = $element->total;
            $element_voucher->igv_perception = 0;
            $element_voucher->total_perception = $element->total;
            $element_voucher->igv_percentage = 18;
            $element_voucher->igv_perception_percentage = 0;
            $element_voucher->ose = 0;
            $element_voucher->user = Auth::user()->name;
            $element_voucher->fact_man = 1;
            $element_voucher->save();

            $voucher_detail = new VoucherDetail();
            $voucher_detail->voucher_id = $element_voucher->id;
            $voucher_detail->unit_id = 1;

            $article = Article::where('id', $element->article_id)->first();

            $voucher_detail->name = $article->name;
            $voucher_detail->quantity = $element->quantity;
            $voucher_detail->original_price = $element->price;
            $voucher_detail->unit_price = $element->price / 1.18;
            $voucher_detail->sale_value = $element->price;
            $voucher_detail->exonerated_value = 0;
            $voucher_detail->inaccurate_value = 0;
            $voucher_detail->igv = $element->total - $element->total / 1.18;
            $voucher_detail->total = $element->total;
            $voucher_detail->user = Auth::user()->name;
            $voucher_detail->article_id = $element->article_id;
            $voucher_detail->save();

            $element->boleta_id = $element_voucher->id;
            $element->state = 'Entregado';
        } else {
            $element->boleta_id = null;
        }

        $element->save();

        return response()->json([
            'type'  => 1,
            'title' => 'Bien',
            'msg'   => 'Pedido actualizado exitosamente.'
        ]);
    }
}
