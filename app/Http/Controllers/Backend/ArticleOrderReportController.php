<?php

namespace App\Http\Controllers\Backend;

use App\Company;
use App\Http\Controllers\Controller;
use App\Order;
use App\OrderDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Luecano\NumeroALetras\NumeroALetras;
use PDF;

class ArticleOrderReportController extends Controller
{
   public function index(){
        $companies=Company::select('id','name')->get();
        return view('backend.article_order_report')->with(compact('companies'));
   }

   public function validateForm()
	{
		$messages = [
			'company_id.required'							=> 'Debe seleccionar una compañía.',
		];

		$rules = [
			'company_id'						            => 'required',
		];

		request()->validate($rules, $messages);
		return request()->all();
	}

    public function list()
    {
        $company_id=request('model.company_id');

        $orders=Order::join('companies','company_id','companies.id')
        ->join('providers','provider_id','providers.id')
        ->join('currencies','currency_id','currencies.id')
        ->select(
        'orders.id as orders_id',
        'companies.name as company_name',
        'providers.business_name as provider_name',
        'currencies.name as currency_name',
        'orders.tc as orders_tc',
        'orders.type as orders_type',
        'orders.order_date as orders_order_date',
        'orders.subtotal as orders_subtotal',
        'orders.igv as orders_igv',
        'orders.import as orders_import',
        DB::Raw('CONCAT(orders.serie,"-",orders.correlative) as orders_code')
        )
        ->where('company_id',$company_id)
        ->orderBy('orders.order_date','desc')
        ->get();

        return $orders;
    }

    public function detail(){
        $orders_id=request('orders_id');

        $order=Order::join('companies','company_id','companies.id')
        ->join('providers','provider_id','providers.id')
        ->join('currencies','currency_id','currencies.id')
        ->join('areas','area_id','areas.id')
        ->select(
            DB::Raw('CONCAT(" [ ",companies.name," - ",providers.business_name," - ",areas.name," ] ",orders.order_date) as title'),
            'orders.subtotal as subtotal',
            'orders.igv as igv',
            'orders.import as neto',
            'currencies.name as currency_name',
            'orders.tc as orders_tc',
            DB::Raw('CONCAT(orders.serie,"-",orders.correlative) as orders_id'),
            'orders.type as type',
            'orders.type_pay as type_pay',
            'orders.reference_id as reference_id'
        )
        ->findOrFail($orders_id);

        $detail=OrderDetails::join('articles','article_id','articles.id')
        ->join('warehouse_types','articles.warehouse_type_id','warehouse_types.id')
        ->join('units','articles.sale_unit_id','units.id')
        ->select(
            'warehouse_types.name as warehouse_type_name',
            'articles.name as article_name',
            'order_details.id as detail_id',
            'order_details.quantity as detail_quantity',
            DB::Raw('CONCAT(units.name," X",articles.package_warehouse) as detail_unit'),
            'order_details.price_unit as detail_price',
            'order_details.value as detail_import',
            'order_details.converted as detail_converted'
        )
        ->where('order_details.order_id',$orders_id)
        ->get();

        return response()->json([
            'detail'    => $detail,
            'order'     => $order
        ]);
    }

    public function export(){

        $formatter = new NumeroALetras();

        $ids=request('ids');

       foreach($ids as $id){

        $orders=Order::join('companies','company_id','companies.id')
        ->join('company_addresses','companies.id','company_addresses.company_id')
        ->join('document_types as companies_document_types','companies.document_type_id','companies_document_types.id')
        ->join('providers','provider_id','providers.id')
        ->join('document_types as providers_document_types','providers.document_type_id','providers_document_types.id')
        ->join('currencies','currency_id','currencies.id')
        ->join('areas','orders.area_id','areas.id')
        ->select(
        DB::Raw('CONCAT(orders.serie,"-",orders.correlative) as orders_id'),
        'companies.id as company_id',
        'companies_document_types.name as company_document_type',
        'companies.document_number as company_document_number',
        'companies.name as company_name',
        'company_addresses.address as company_address',
        'providers_document_types.name as provider_document_type',
        'providers.document_number as provider_document_number',
        'providers.business_name as provider_name',
        'providers.address as provider_address',
        'providers.phone_number_1 as provider_number_1',
        'providers.email as provider_email',
        'currencies.name as currency_name',
        'currencies.symbol as currency_symbol',
        'orders.tc as orders_tc',
        'orders.type as orders_type',
        'orders.order_date as orders_order_date',
        'orders.subtotal as orders_subtotal',
        'orders.igv as orders_igv',
        'orders.import as orders_import',
        'areas.name as area_name',
        'areas.short_name as area_short_name',
        'orders.type_pay as orders_type_pay',
        'orders.reference_id as orders_reference_id',
        'orders.description as description'
        
        )
        ->findOrFail($id);
        $orders->total_text = $formatter->toInvoice($orders->orders_import, 2,$orders->currency_name);

        $detail=OrderDetails::join('articles','article_id','articles.id')
        ->join('units','articles.sale_unit_id','units.id')
        ->select(
            'articles.id as article_id',
            'articles.name as article_name',
            'order_details.id as detail_id',
            'order_details.quantity as detail_quantity',
            DB::Raw('CONCAT(units.name," X",articles.package_warehouse) as detail_unit'),
            'order_details.price_unit as detail_price',
            'order_details.value as detail_import',
            'order_details.converted as detail_converted'
        )
        ->where('order_details.order_id',$id)
        ->get();

        $pdf = PDF::loadView('backend.pdf.order_detail', compact('orders','detail'));
        return $pdf->download('.pdf');
       }
      
    }
}
