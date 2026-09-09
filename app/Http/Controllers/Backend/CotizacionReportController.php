<?php

namespace App\Http\Controllers\Backend;

use App\Article;
use App\Client;
use App\ClientMotor;
use App\Company;
use App\Cot;
use App\CotDetail;
use App\Currency;
use App\Http\Controllers\Controller;
use App\Rate;
use App\Service;
use App\Vehicle;
use App\Voucher;
use App\VoucherDetail;
use App\VoucherType;
use App\WarehouseMovement;
use App\WarehouseMovementDetail;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Luecano\NumeroALetras\NumeroALetras;
use PDF;
use QrCode;

class CotizacionReportController extends Controller
{
    public function index()
    {

        $firstDayOfMonth = new DateTime('first day of this month midnight');

        $current_date_1 = $firstDayOfMonth->format(DATE_ATOM);
        $current_date_2 = date(DATE_ATOM, mktime(0, 0, 0));

        return view('backend.cotizacion_report')->with(compact('current_date_1', 'current_date_2'));
    }

    public function validateForm()
    {
        $messages = [
            'issue_date_1.required'                            => 'Debe seleccionar Fecha Inicio de Emisión.',
            'issue_date_2.required'                            => 'Debe seleccionar Fecha Fin de Emisión.',
        ];

        $rules = [
            'issue_date_1'                                    => 'required',
            'issue_date_2'                                    => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function validateModalArticle()
    {
        $messages = [
            'quantity.required'            => 'La Cantidad es obligatoria.',
            'quantity.integer'             => 'La Cantidad debe ser un número entero.',
            'quantity.min'                 => 'La Cantidad mínima es 1 .',
            'price.required'               => 'El Precio es obligatoria.',
            'price.numeric'                => 'El Precio debe ser un número real.',
            'price.min'                    => 'El Precio mínimo es 0.01 .',
            'percentage.required'          => 'El % de Descuento es obligatoria.',
            'percentage.numeric'           => 'El % de Descuento debe ser un número real.',
            'percentage.min'               => 'El % de Descuento mínimo es %0 .',
        ];

        $rules = [
            'quantity'          => 'required|integer|min:1',
            'price'             => 'required|numeric|min:0.01',
            'percentage'             => 'required|numeric|min:0.00',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function deleteDetailArticle()
    {

        $id_detail = request('id_detail');
        $cots_id = request('cots_id');

        $item = CotDetail::findOrFail($id_detail);
        $item->delete();

        $detail = CotDetail::where('cots_id', $cots_id)->sum('neto');

        $cot = Cot::findOrFail($cots_id);
        $cot->subtotal = $detail;
        $cot->igv =  $detail * 0.18;
        $cot->total = $detail + $cot->igv;
        $cot->save();

        $cot_detail = CotDetail::where('cots_id', $cots_id)
            ->get();

        return response()->json([
            'cot_detail'    => $cot_detail
        ]);
    }

    public function storeArticle()
    {

        $this->validateModalArticle();

        $id = request('id');
        $cots_id = request('cots_id');
        $article_type = request('article_type');
        $article_id = request('article_id');
        $cost = request('cost');
        $quantity = request('quantity');
        $price = request('price');
        $import = request('import');
        $percentage = request('percentage');
        $discount = request('discount');
        $total = request('total');

        if (isset($id)) {
            $cot_detail = CotDetail::find($id);
            $msg = 'Registro actualizado exitosamente';
        } else {
            $cot_detail = new CotDetail();
            $cot_detail->cots_id = $cots_id;
            $msg = 'Registro creado exitosamente';
        }

        if ($article_type == 1) {
            $cot_detail->type = "Bienes";
            $article = Article::findOrFail($article_id);
        } else if ($article_type == 2) {
            $cot_detail->type = "Servicios";
            $article = Service::findOrFail($article_id);
        } else {
            $cot_detail->type = "NN";
        }

        $cot_detail->article_id = $article_id;
        $cot_detail->name = $article->name;
        $cot_detail->cost = $cost;
        $cot_detail->quantity = $quantity;
        $cot_detail->price = $price;
        $cot_detail->import = $import;
        $cot_detail->desc_percentage = $percentage;
        $cot_detail->desc = $discount;
        $cot_detail->neto = $total;
        $cot_detail->save();

        $detail = CotDetail::where('cots_id', $cots_id)->sum('neto');

        $cot = Cot::findOrFail($cots_id);
        $cot->subtotal = $detail;
        $cot->igv =  $detail * 0.18;
        $cot->total = $detail + $cot->igv;
        $cot->save();

        $cot_detail = CotDetail::where('cots_id', $cots_id)
            ->get();

        $type = 1;
        $title = '¡Ok!';

        return response()->json([
            'type'          => $type,
            'title'         => $title,
            'msg'           => $msg,
            'cot_detail'    => $cot_detail
        ]);
    }

    public function list()
    {
        $issue_date_1 = date('Y-m-d', strtotime(request('issue_date_1')));
        $issue_date_2 = date('Y-m-d', strtotime(request('issue_date_2')));

        $cots = Cot::join('companies', 'companies.id', 'cots.company_id')
            ->join('currencies', 'currencies.id', 'cots.currency_id')
            ->join('order_works', 'order_works.id', 'cots.order_work_id')
            ->join('vehicles', 'vehicles.id', 'cots.vehicle_id')
            ->select(
                'cots.id as cots_id',
                DB::Raw('DATE_FORMAT(cots.issue_date, "%Y-%m-%d") as cots_issue_date'),
                DB::Raw('DATE_FORMAT(cots.expiry_date, "%Y-%m-%d") as cots_expiry_date'),
                DB::Raw('CONCAT(cots.serie,"-",cots.correlative) as serie_cot'),
                DB::Raw('CONCAT(order_works.serie,"-",order_works.correlative) as serie_ot'),
                'cots.client_document_number as cots_client_document_number',
                'cots.client_name as cots_client_name',
                'currencies.name as currency_name',
                'cots.subtotal as subtotal',
                'cots.igv as igv',
                'cots.total as total',
                'cots.status',
                'vehicles.plate as placa'
            )
            ->where('cots.issue_date', '>=', $issue_date_1)
            ->where('cots.issue_date', '<=', $issue_date_2)
            ->get();

        return $cots;
    }

    public function aprobarCotizacion()
    {
        $id = request('id');

        $obj = Cot::find($id);
        $obj->status = 1;
        $obj->save();
    }

    public function generarFactura()
    {

        $id = request('id');

        $obj = Cot::find($id);
        $obj->status = 2;
        $obj->save();

        $vehicle = Vehicle::findOrFail($obj->vehicle_id);
        $vehicle->state = 4; //facturado
        $vehicle->save();

        $client = ClientMotor::find($obj->client_id);

        $detalle = CotDetail::where('cots_id', $id)->get();

        $voucher_type = VoucherType::select('serie_type')
            ->where('id', '=', 1)
            ->first();

        switch ($obj->company_id) {
            case 1:
                $fact_serie = '001';
                break;
            case 2:
                $fact_serie = '002';
                break;
            case 3:
                $fact_serie = '003';
                break;
            case 4:
                $fact_serie = '004';
                break;
            case 5:
                $fact_serie = '005';
                break;
        }

        $serie_number = $voucher_type->serie_type . $fact_serie;

        $voucher_number = Voucher::select('voucher_number')
            ->where('voucher_type_id', '=', 1)
            ->where('serie_number', '=', $serie_number)
            ->max('voucher_number');

        $voucher = new Voucher();
        $voucher->company_id = $obj->company_id;
        $voucher->client_id = $obj->client_id;
        $voucher->original_client_id = $obj->client_id;
        $voucher->client_name = $obj->client_name;
        $voucher->client_address = $obj->client_address;
        $voucher->voucher_type_id = 1; //FACTURA ELECTRÓNICA
        $voucher->serie_number = $serie_number;
        $voucher->voucher_number = $voucher_number ? $voucher_number + 1 : 1;
        $voucher->referral_guide_series = $obj->serie;
        $voucher->referral_guide_number = $obj->correlative;
        $voucher->issue_date = $obj->issue_date;
        $voucher->expiry_date = $obj->expiry_date;
        $voucher->currency_id = $obj->currency_id;

        $voucher->payment_id = $client->payment_id; //CONTADO

        $voucher->taxed_operation = $obj->subtotal;
        $voucher->igv_percentage = $obj->igv_percentage;
        $voucher->igv = $obj->igv;
        $voucher->total = $obj->total;
        $voucher->ose = 5;//GENERADO
        $voucher->user = Auth::user()->user;
        $voucher->save();

        foreach ($detalle as $item) {
            $det = new VoucherDetail();
            $det->voucher_id = $voucher->id;
            $det->unit_id = 1; //UNIDAD
            $det->name = $item->name;
            $det->quantity = $item->quantity;
            $det->original_price = $item->cost;
            $det->unit_price = $item->price;
            $det->sale_value = $item->import;
            $det->desc_percentage = $item->desc_percentage;
            $det->desc = $item->desc;
            $det->total = $item->neto;
            $det->type_article = $item->type;
            $det->article_id = $item->article_id;
            $det->user = Auth::user()->user;
            $det->save();
        }
    }

    public function detail()
    {

        $cot_id = request('id');

        $cot_detail = CotDetail::where('cots_id', $cot_id)
            ->get();


        return $cot_detail;
    }

    public function getDetailArticle()
    {

        $id = request('id');

        $detail_article = CotDetail::findOrFail($id);


        return $detail_article;
    }

    public function exportFactura()
    {
        $formatter = new NumeroALetras();

        $id = request('id');
        $cotizacion = Cot::find($id);

        $voucher_id = Voucher::select('id', 'referral_guide_series', 'referral_guide_number')
            ->where('referral_guide_series', '=', $cotizacion->serie)
            ->where('referral_guide_number', '=', $cotizacion->correlative)
            ->first();

        $obj = Voucher::join('companies', 'companies.id', 'vouchers.company_id')
            ->join('company_addresses', 'company_addresses.company_id', 'companies.id')
            ->join('voucher_types', 'voucher_types.id', 'vouchers.voucher_type_id')
            ->join('currencies', 'currencies.id', 'vouchers.currency_id')
            ->join('clients', 'clients.id', 'vouchers.client_id')
            ->join('document_types', 'document_types.id', 'clients.document_type_id')
            ->join('client_addresses', 'client_addresses.client_id', 'clients.id')
            ->select(
                'vouchers.company_id as company_id',
                'companies.name as company_name',
                'companies.document_number as company_document_number',
                'company_addresses.address as company_address',
                'company_addresses.district as company_district',
                'company_addresses.province as company_province',
                'company_addresses.department as company_department',
                'voucher_types.name as voucher_type_name',
                'vouchers.serie_number as serie_number',
                'vouchers.voucher_number as voucher_number',
                DB::Raw('DATE_FORMAT(vouchers.issue_date, "%Y-%m-%d") as issue_date'),
                DB::Raw('DATE_FORMAT(vouchers.expiry_date, "%Y-%m-%d") as expiry_date'),
                'currencies.name as currency_name',
                'currencies.symbol as currency_symbol',
                'vouchers.client_name as client_name',
                'clients.document_number as client_document_number',
                'document_types.name as client_document_name',
                'client_addresses.address as client_address',
                'vouchers.taxed_operation as taxed_operation',
                'vouchers.igv as igv',
                'vouchers.total as total'
            )
            ->findOrFail($voucher_id->id);

        $services = VoucherDetail::select(
            'name as article_name',
            'type_article as type_article',
            'quantity as article_quantity',
            'unit_price as article_price',
            'sale_value as subtotal',
            'desc_percentage',
            'desc',
            'total'
        )
            ->where('voucher_id', '=', $voucher_id->id)
            ->where('type_article', 'Servicios')
            ->get();

        $bienes = VoucherDetail::select(
            'name as article_name',
            'type_article as type_article',
            'quantity as article_quantity',
            'unit_price as article_price',
            'sale_value as subtotal',
            'desc_percentage',
            'desc',
            'total'
        )
            ->where('voucher_id', '=', $voucher_id->id)
            ->where('type_article', 'Bienes')
            ->get();

        $document_qrcode = base64_encode(QrCode::format('png')->size(100)->generate('| ' . $obj->company_document_number . ' | ' . $obj->serie_number . '-' . $obj->voucher_number . ' | ' . $obj->igv . ' | ' . $obj->total . ' | ' . $obj->issue_date . ' | ' . $obj->client_document_number));
        $total_text = $formatter->toInvoice($obj->total, 2, $obj->currency_name);

        $pdf = PDF::loadView('backend.pdf_view_facturar', compact('obj', 'services', 'bienes', 'total_text', 'voucher_id', 'document_qrcode'));
        return $pdf->download('.pdf');
    }

    public function exportCotizacion()
    {

        $formatter = new NumeroALetras();
        $id = request('id');

        $cotizacion = Cot::join('companies', 'companies.id', 'cots.company_id')
            ->join('company_addresses', 'company_addresses.company_id', 'companies.id')
            ->join('currencies', 'currencies.id', 'cots.currency_id')
            ->join('client_motors', 'client_motors.id', 'cots.client_id')
            ->join('client_address_motors', 'client_address_motors.client_motor_id', 'client_motors.id')
            ->join('document_types', 'document_types.id', 'companies.document_type_id')
            ->join('order_works', 'order_works.id', 'cots.order_work_id')
            ->select(
                'cots.id as cot_id',
                DB::Raw('CONCAT(cots.serie,"-",cots.correlative) as serie_cot'),
                DB::Raw('CONCAT(order_works.serie,"-",order_works.correlative) as serie_ot'),
                'cots.issue_date as cot_issue_date',
                'cots.expiry_date as cot_expiry_date',
                'currencies.name as currency_name',
                'cots.company_id as company_id',
                'cots.client_document_number as client_document_number',
                'cots.client_name as client_name',
                'cots.client_address as client_address',
                'companies.name as company_name',
                'company_addresses.address as company_address',
                'client_address_motors.address as client_adress',
                'client_motors.phone_number_1 as client_phone_1',
                'companies.document_number as company_document_number',
                'client_motors.phone_number_2 as client_phone_2',
                'client_motors.email as client_email',
                'document_types.name as company_document_type',
                'cots.tc as tc',
                'cots.subtotal as cot_subtotal',
                'cots.igv as cot_igv',
                'cots.total as cot_total'
            )
            ->findOrFail($id);

        $services = CotDetail::select('name', 'quantity', 'price', 'import', 'desc_percentage', 'desc', 'neto')
            ->where('cots_id', $id)
            ->where('type', 'Servicios')
            ->get();

        $articles = CotDetail::select('name', 'quantity', 'price', 'import', 'desc_percentage', 'desc', 'neto')
            ->where('cots_id', $id)
            ->where('type', 'Bienes')
            ->get();

        $total_text = $formatter->toInvoice($cotizacion->cot_total, 4, $cotizacion->currency_name);

        $pdf = PDF::loadView('backend.pdf.cotizacion', compact('cotizacion', 'services', 'articles', 'total_text'));
        return $pdf->download('.pdf');
    }
}
