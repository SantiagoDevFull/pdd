<?php

namespace App\Http\Controllers\Backend;

use App\BusinessUnit;
use App\ClientChannel;
use App\Http\Controllers\Controller;
use App\ClientRoute;
use App\ClientSector;
use App\Exports\PresupuestoReportExport;
use App\SalePresupuesto;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use stdClass;

class SalePresupuestoController extends Controller
{
    public function index()
    {

        $business_units = BusinessUnit::all();

        $client_sectors = ClientSector::leftjoin('business_units', 'client_sectors.business_unit_id', 'business_units.id')
            ->select(
                'client_sectors.id as id',
                DB::Raw('CONCAT( client_sectors.name," (UNIDAD : ",business_units.name , ")" ) AS name')
            )->get();

        $client_channels = ClientChannel::leftjoin('client_sectors', 'client_channels.client_sector_id', 'client_sectors.id')
            ->select(
                'client_channels.id as id',
                DB::Raw('CONCAT( client_channels.name," (CANAL : ",client_sectors.name, ")") AS name')
            )->get();

        $client_routes = ClientRoute::join('client_channels', 'client_routes.channel_id', 'client_channels.id')
            ->select(
                'client_routes.id as id',
                DB::Raw('CONCAT( client_routes.short_name," (SUBCANAL : ",client_channels.name, ")") AS short_name')
            )->get();

        $managers = DB::table('managers')
            ->select('id', 'name')
            ->whereIn('id', [4, 5, 30, 15, 7, 9, 11, 12, 14, 16, 18, 19, 20, 21, 22, 23, 25, 26, 27, 28, 31, 32])
            ->orderBy('name', 'asc')
            ->get();

        return view('backend.sale_presupuesto')->with(compact('business_units', 'client_sectors', 'client_channels', 'client_routes', 'managers'));
    }

    public function validateForm()
    {
        $messages = [
            'anio.required'    => 'Debe seleccionar un Año.',
            'mes.required'    => 'Debe seleccionar un Mes.',
        ];

        $rules = [
            'anio'    => 'required',
            'mes'    => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function list()
    {
        $anio = request('anio');
        $mes = request('mes');

        $p = request('pagination');
        $q = request('query');
        $page = (int)$p['page'];
        $perpage = (int)($p['perpage'] ? $p['perpage'] : 10);
        $search = $q['generalSearch'];
        request()->replace(['page' => $page]);

        $elements = SalePresupuesto::leftjoin('client_routes', 'sale_presupuestos.client_route_id', '=', 'client_routes.id')
            ->join('managers', 'sale_presupuestos.manager_id', '=', 'managers.id')
            ->join('business_units', 'sale_presupuestos.business_unit_id', '=', 'business_units.id')
            ->join('client_sectors', 'sale_presupuestos.client_sector_id', '=', 'client_sectors.id')
            ->join('client_channels', 'sale_presupuestos.client_channel_id', '=', 'client_channels.id')
            ->select(
                'sale_presupuestos.id',
                'sale_presupuestos.anio',
                'sale_presupuestos.mes',
                'client_routes.short_name as ruta',
                'business_units.name as business_unit_name',
                'client_sectors.name as client_sector_name',
                'client_channels.name as client_channel_name',
                'managers.name as manager_name',
                'sale_presupuestos.ppto_venta',
                'sale_presupuestos.ppto_mb'
            )
            ->when($search, function ($query, $search) {
                return $query->where('managers.name', 'like', '%' . $search . '%');
            })
            ->where('sale_presupuestos.anio', $anio)
            ->where('sale_presupuestos.mes', $mes)
            ->orderBy('id', 'asc')
            ->paginate($perpage);

        $elements->getCollection()->transform(function ($item) {
            $item->mes_nombre = ucfirst(
                Carbon::create()->month($item->mes)
                    ->locale('es')
                    ->isoFormat('MMMM')
            );
            return $item;
        });

        $meta = new \stdClass();
        $meta->page = $page;
        $meta->pages = $elements->lastPage();
        $meta->perpage = $perpage;
        $meta->total = $elements->total();
        $meta->field = 'id';
        for ($i = 1; $i <= $elements->total(); $i++) {
            $meta->rowIds[] = $i;
        }

        return response()->json([
            'meta'    => $meta,
            'data'    => $elements->items(),
        ]);
    }

    public function detail()
    {
        $id = request('id');

        $element = SalePresupuesto::select(
            'id',
            'anio',
            'mes',
            'business_unit_id',
            'client_sector_id',
            'client_channel_id',
            'client_route_id',
            'manager_id',
            'ppto_venta',
            'ppto_mb'
        )->findOrFail($id);

        $element->mes = ucfirst(
            Carbon::create()->month($element->mes)
                ->locale('es')
                ->isoFormat('MMMM')
        );

        return $element;
    }

    public function validateModalForm()
    {
        $messages = [
            'business_unit_id.required'    => 'Debe seleccionar una Unidad.',
            'client_sector_id.required'    => 'Debe seleccionar un Sector.',
            'client_channel_id.required'    => 'Debe seleccionar un Canal.',
            //'client_route_id.required'    => 'Debe seleccionar una Ruta.',
            'manager_id.required'    => 'Debe seleccionar un Supervisor.',
            'ppto_venta.required'    => 'Debe ingresar un Ppto. de Venta.',
            'ppto_mb.required'    => 'Debe ingresar un Ppto. de MB.',
        ];

        $rules = [
            'business_unit_id'    => 'required',
            'client_sector_id'    => 'required',
            'client_channel_id'    => 'required',
            //'client_route_id'    => 'required',
            'manager_id'    => 'required',
            'ppto_venta'    => 'required',
            'ppto_mb'    => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }
    public function store()
    {
        $this->validateModalForm();

        $id = request('id');

        $anio = request('anio');

        $mes = request('mes');

        if ($mes == 'Enero') $mes = 1;
        else if ($mes == 'Febrero') $mes = 2;
        else if ($mes == 'Marzo') $mes = 3;
        else if ($mes == 'Abril') $mes = 4;
        else if ($mes == 'Mayo') $mes = 5;
        else if ($mes == 'Junio') $mes = 6;
        else if ($mes == 'Julio') $mes = 7;
        else if ($mes == 'Agosto') $mes = 8;
        else if ($mes == 'Setiembre') $mes = 9;
        else if ($mes == 'Octubre') $mes = 10;
        else if ($mes == 'Noviembre') $mes = 11;
        else if ($mes == 'Diciembre') $mes = 12;

        $business_unit_id = request('business_unit_id');
        $client_sector_id = request('client_sector_id');
        $client_channel_id = request('client_channel_id');
        $client_route_id = request('client_route_id');
        $manager_id = request('manager_id');
        $ppto_venta = request('ppto_venta');
        $ppto_mb = request('ppto_mb');

        if (isset($id)) {
            $element = SalePresupuesto::find($id);
            $msg = 'Registro actualizado exitosamente';
            $element->updated_at_user = Auth::user()->user;
        } else {
            $element = new SalePresupuesto();
            $msg = 'Registro creado exitosamente';
            $element->created_at_user = Auth::user()->user;
            $element->updated_at_user = Auth::user()->user;
        }

        $element->anio = $anio;
        $element->mes = $mes;
        $element->business_unit_id = $business_unit_id;
        $element->client_sector_id = $client_sector_id;
        $element->client_channel_id = $client_channel_id;
        $element->client_route_id = $client_route_id;
        $element->manager_id = $manager_id;
        $element->ppto_venta = $ppto_venta;
        $element->ppto_mb = $ppto_mb;
        $element->save();

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
        $element = SalePresupuesto::findOrFail($id);
        $element->delete();
    }

    public function export()
    {

        $anio = request('anio');
        $mes = request('mes');

        $elements = SalePresupuesto::leftjoin('client_routes', 'sale_presupuestos.client_route_id', '=', 'client_routes.id')
            ->join('managers', 'sale_presupuestos.manager_id', '=', 'managers.id')
            ->join('business_units', 'sale_presupuestos.business_unit_id', '=', 'business_units.id')
            ->join('client_sectors', 'sale_presupuestos.client_sector_id', '=', 'client_sectors.id')
            ->join('client_channels', 'sale_presupuestos.client_channel_id', '=', 'client_channels.id')
            ->select(
                'sale_presupuestos.id',
                'sale_presupuestos.anio',
                'sale_presupuestos.mes',
                'client_routes.short_name as ruta',
                'business_units.name as business_unit_name',
                'client_sectors.name as client_sector_name',
                'client_channels.name as client_channel_name',
                'managers.name as manager_name',
                'sale_presupuestos.ppto_venta',
                'sale_presupuestos.ppto_mb'
            )
            ->where('sale_presupuestos.anio', $anio)
            ->where('sale_presupuestos.mes', $mes)
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($item) {
                $item->mes = ucfirst(
                    Carbon::create()->month($item->mes)
                        ->locale('es')
                        ->isoFormat('MMMM')
                );
                return $item;
            });


        $datetime = CarbonImmutable::now()->format('d/m/Y h:i:s a');

        $excel = new PresupuestoReportExport($datetime, $elements);
        return Excel::download($excel, 'reporte-de-presupuestos-ventas.xls');
    }
}
