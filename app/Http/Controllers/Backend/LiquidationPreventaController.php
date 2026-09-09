<?php

namespace App\Http\Controllers\Backend;

use App\ClientRoute;
use App\Http\Controllers\Controller;
use App\Money;
use App\PlantLiquidation;
use App\PlantOrder;
use App\PlantSale;
use App\PlantSaleDetail;
use App\PlantVoucher;
use Illuminate\Support\Facades\DB;

class LiquidationPreventaController extends Controller
{
    public function index()
    {

        $client_routes = ClientRoute::select('id', 'name')->get();

        return view('backend.liquidations_preventa')->with(compact('client_routes'));
    }


    public function validateForm()
    {
        $messages = [
            'client_route_id.required'               => 'Debe seleccionar una Ruta.',
            'warehouse_movement_id.required'         => 'El Nº de Parte es obligatorio.',
        ];

        $rules = [
            'client_route_id'            => 'required',
            'warehouse_movement_id'      => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }
    public function getWarehouseMovements()
    {
        $client_route_id = request('client_route_id');

        $elements = PlantOrder::leftjoin('vehicles', 'vehicles.id', '=', 'plant_orders.vehicle_id')
            ->select(
                'plant_orders.id as id',
                DB::Raw("CONCAT(plant_orders.serie,'-',plant_orders.correlative) as pedido"),
                'plant_orders.traslate_date',
                'vehicles.plate as plate'
            )
            ->where('plant_orders.route_id', $client_route_id)
            ->where('plant_orders.state_id', 6)
            ->orderBy('plant_orders.id', 'desc')
            ->get();

        $elements->map(function ($item, $index) {
            $item->creation_date = date('d-m-Y', strtotime($item->traslate_date));
        });

        return $elements;
    }

    public function listSaleDetail()
    {
        $sale_id = request('sale_id');

        $detail = PlantSaleDetail::leftjoin('articles', 'articles.id', '=', 'plant_sale_details.article_id')
            ->select(
                'plant_sale_details.id as detail_id',
                'articles.name as article_name',
                'plant_sale_details.quantity as sale_detail_quantity',
                'plant_sale_details.price_igv as sale_detail_price_igv',
                'plant_sale_details.total_perception as sale_detail_total_perception'
            )
            ->where('plant_sale_details.plant_sale_id', $sale_id)->get();

        return $detail;
    }

    public function validateModal()
    {
        $messages = [
            'scop_number.required'               => 'Debe ingresar el Scop.',
        ];
        $rules = [
            'scop_number'            => 'required',
        ];
        request()->validate($rules, $messages);
        return request()->all();
    }

    public function registerMoney()
    {

        $id = request('id');
        $plant_order_id = request('plant_order_id');
        $billete_uno = request('billete_uno');
        $billete_dos = request('billete_dos');
        $billete_tres = request('billete_tres');
        $billete_cuatro = request('billete_cuatro');
        $billete_cinco = request('billete_cinco');
        $total_1 = request('total_1');
        $moneda_uno = request('moneda_uno');
        $moneda_dos = request('moneda_dos');
        $moneda_tres = request('moneda_tres');
        $moneda_cuatro = request('moneda_cuatro');
        $moneda_cinco = request('moneda_cinco');
        $moneda_seis = request('moneda_seis');
        $total_2 = request('total_2');

        if ($id) {
            $money = Money::where('id', $id)->first();
            $msg = 'Monedas actualizado exitosamente.';
        } else {
            $money = new Money();
            $msg = 'Monedas registrados exitosamente.';
        }

        $money->plant_order_id = $plant_order_id;
        $money->billete_uno = $billete_uno;
        $money->billete_dos = $billete_dos;
        $money->billete_tres = $billete_tres;
        $money->billete_cuatro = $billete_cuatro;
        $money->billete_cinco = $billete_cinco;
        $money->total_1 = $total_1;
        $money->moneda_uno = $moneda_uno;
        $money->moneda_dos = $moneda_dos;
        $money->moneda_tres = $moneda_tres;
        $money->moneda_cuatro = $moneda_cuatro;
        $money->moneda_cinco = $moneda_cinco;
        $money->moneda_seis = $moneda_seis;
        $money->total_2 = $total_2;
        $money->save();

        $type = 1;
        $title = '¡Ok!';

        return response()->json([
            'type'     => $type,
            'title'    => $title,
            'msg'      => $msg,
        ]);
    }

    public function detailMoney()
    {
        $warehouse_movement_id = request('model.warehouse_movement_id');

        $money = Money::where('plant_order_id', $warehouse_movement_id)->first();

        return $money ?? new Money(['plant_order_id' => $warehouse_movement_id]);
    }

    public function updateScop()
    {

        $this->validateModal();
        $sale_id = request('id');
        $scop_number = request('scop_number');

        $sale = PlantSale::where('id', $sale_id)->first();
        $sale->scop_number = $scop_number;
        $sale->update();

        if ($sale->warehouse_document_type_id == 5) { //FACTURA ELECTRONICA

            $voucher = PlantVoucher::where(function ($q) use ($sale) {
                $q->where('serie_number', 'F' . $sale->referral_serie_number)
                    ->where('voucher_number', $sale->referral_voucher_number);
            })
                ->orWhere(function ($q) use ($sale) {
                    $q->where('credit_note_reference_serie', 'F' . $sale->referral_serie_number)
                        ->where('credit_note_reference_number', $sale->referral_voucher_number);
                })
                ->get();

            foreach ($voucher as $v) {
                $v->scop_number = $scop_number;
                $v->update();
            }
        }
    }

    public function asignScop()
    {
        $sale_id = request('sale_id');

        $sale = PlantSale::find($sale_id);

        return $sale;
    }
    public function listLiquidationDetail()
    {
        $sale_id = request('sale_id');

        $detail = PlantLiquidation::leftjoin('payment_methods', 'plant_liquidations.payment_method_id', '=', 'payment_methods.id')
            ->leftjoin('payments', 'payments.id', '=', 'payment_methods.payment_id')
            ->leftjoin('currencies', 'currencies.id', '=', 'plant_liquidations.currency_id')
            ->leftjoin('bank_accounts', 'bank_accounts.id', '=', 'plant_liquidations.bank_account_id')
            ->select(
                'plant_liquidations.id as liquidation_id',
                'payments.name as payment_name',
                'payment_methods.name as payment_method_name',
                'currencies.name as currency_name',
                'bank_accounts.account_number as bank_account_name',
                'plant_liquidations.operation_number as liq_operation_number',
                DB::raw("DATE_FORMAT(plant_liquidations.created_at, '%Y-%m-%d') as liquidation_date"),
                DB::raw('ROUND(plant_liquidations.amount, 2) as liquidation_amount')
            )
            ->where('plant_liquidations.plant_sale_id', $sale_id)
            ->get();

        return $detail;
    }


    public function finishLiquidation()
    {

        $money = Money::where('plant_order_id',request('model.warehouse_movement_id'))->first();
        

        if (!$money) {
            $type = 2;
            $title = '¡Advertencia!';
            $msg = '¡Falta registrar Monedas!';

            return response()->json([
                'type'     => $type,
                'title'    => $title,
                'msg'      => $msg,
            ]);
        }

        $sales = request('sales');
        $sale_elements = PlantSale::whereIn('id', $sales)->get();

        foreach ($sale_elements as $sale_element) {
            $sale_element->estado = 1;
            $sale_element->update();
        }

        $warehouse_movement_id = request('model.warehouse_movement_id');

        $sales_total = PlantSale::where('plant_order_id', $warehouse_movement_id)->get();

        $sales_validados = PlantSale::where('plant_order_id', $warehouse_movement_id)
            ->where('estado', '=', '1')
            ->get();

        $sales_total = PlantSale::where('plant_order_id', $warehouse_movement_id)->get();
        $sales_validados = PlantSale::where('plant_order_id', $warehouse_movement_id)
            ->where('estado', '=', '1')
            ->get();

        $total_registros = $sales_total->count();
        $total_validados = $sales_validados->count();

        if ($total_registros === $total_validados) {
            $element = PlantOrder::findOrFail($warehouse_movement_id);
            $element->state_id = 7;
            $element->save();
        }


        $type = 3;
        $title = '¡Ok!';
        $msg = '¡La liquidación se ha finalizado correctamente!';
        $url = route('dashboard.liquidation_preventa.index');

        return response()->json([
            'type'     => $type,
            'title'    => $title,
            'msg'      => $msg,
            'url'      => $url
        ]);
    }

    public function listSales()
    {

        $warehouse_movement_id = request('model.warehouse_movement_id');

        $sales = PlantSale::leftjoin('companies', 'plant_sales.company_id', '=', 'companies.id')
            ->leftjoin('clients', 'plant_sales.client_id', '=', 'clients.id')
            ->leftjoin('client_routes', 'clients.route_id', '=', 'client_routes.id')
            ->leftjoin('warehouse_document_types', 'plant_sales.warehouse_document_type_id', '=', 'warehouse_document_types.id')
            ->leftjoin('currencies', 'plant_sales.currency_id', '=', 'currencies.id')
            ->leftjoin('voucher_types', 'warehouse_document_types.voucher_type_id', '=', 'voucher_types.id')

            ->select(
                'plant_sales.id as sale_id',
                'companies.name as company_name',
                'plant_sales.sale_date as sale_date',
                'plant_sales.expiry_date as expiry_date',
                'plant_sales.warehouse_document_type_id as document_type_id',
                'warehouse_document_types.name as document_type_name',
                'clients.business_name as client_name',
                'client_routes.name as client_route_name',
                'voucher_types.serie_type as voucher_serie_type',
                DB::Raw('CONCAT(IFNULL(voucher_types.serie_type,""),plant_sales.referral_serie_number,"-",plant_sales.referral_voucher_number) as comprobante'),
                'plant_sales.license_plate as license_plate',
                'currencies.name as currency_name',
                'plant_sales.total_perception as total_venta',
                'plant_sales.balance as saldo_venta',
                'plant_sales.paid as pagado_venta',
                'plant_sales.scop_number as scop_number'
            )
            ->where('plant_sales.plant_order_id', $warehouse_movement_id)
            ->where(function ($q) {
                $q->where('plant_sales.estado', '!=', 1)
                    ->orWhereNull('plant_sales.estado');
            })
            ->get();

        return $sales;
    }
}
