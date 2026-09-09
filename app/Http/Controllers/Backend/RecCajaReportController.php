<?php

namespace App\Http\Controllers\Backend;

use App\ClientRoute;
use App\Deposit;
use App\Efective;
use App\Exports\RecCajaReportExport;
use App\Http\Controllers\Controller;
use App\Liquidation;
use App\Sale;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class RecCajaReportController extends Controller
{
    public function index()
    {

        $date = CarbonImmutable::now()->startOfDay();
        $max_datetime = $date->startOfDay()->addDays(2)->toAtomString();





        return view('backend.rec_caja_report')->with(compact('max_datetime'));
    }

    public function validateForm()
    {
        $messages = [
            'initial_date.required'                            => 'Debe seleccionar Fecha Inicial.',
            'final_date.required'                              => 'Debe seleccionar Fecha Final.',
        ];

        $rules = [
            'initial_date'                                    => 'required',
            'final_date'                                      => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function export()
    {

        $this->validateForm();
        $initial_date = CarbonImmutable::createFromDate(request('initial_date'))->startOfDay()->format('Y-m-d');
        $final_date = CarbonImmutable::createFromDate(request('final_date'))->startOfDay()->format('Y-m-d');

        // 1. DEPÓSITOS
        $depositos = Deposit::join('companies', 'companies.id', '=', 'deposits.company_id')
            ->join('liquidations', 'liquidations.deposit_id', '=', 'deposits.id')
            ->select(
                'companies.short_name as company_short_name',
                'deposits.route_id as route_id',
                DB::raw('DATE_FORMAT(deposits.created_at, "%Y-%m-%d") as deposit_created_at'),
                DB::raw('SUM(liquidations.amount) as total_deposit')
            )
            ->groupBy('deposits.route_id', DB::raw('DATE_FORMAT(deposits.created_at, "%Y-%m-%d")'))
            ->orderBy('deposits.created_at', 'desc')
            ->orderBy('deposits.route_id', 'asc')
            ->whereDate('deposits.created_at', '>=', $initial_date)
            ->whereDate('deposits.created_at', '<=', $final_date)
            ->whereNotNull('deposits.route_id')
            ->get();

        // 2. EFECTIVOS
        $efectives = Efective::join('warehouse_movements', 'warehouse_movements.id', '=', 'efectives.warehouse_movement_id')
            ->select(
                'warehouse_movements.route_id',
                'warehouse_movements.account_name as client_name',
                DB::raw('DATE_FORMAT(efectives.created_at, "%Y-%m-%d") as efective_created_at'),
                DB::raw('SUM(efectives.total) as total_efective')
            )
            ->groupBy('warehouse_movements.route_id', DB::raw('DATE_FORMAT(efectives.created_at, "%Y-%m-%d")'))
            ->whereDate('efectives.created_at', '>=', $initial_date)
            ->whereDate('efectives.created_at', '<=', $final_date)
            ->get();

        // 3. RUTAS
        $rutas = ClientRoute::select('id', 'name', 'short_name')->get();
        $rutasIndex = [];
        foreach ($rutas as $ruta) {
            $rutasIndex[$ruta->id] = $ruta->name;
        }

        // 4. INDEXAR EFECTIVOS
        $efectivesIndexed = [];
        $clientNames = [];
        foreach ($efectives as $efective) {
            $key = $efective->route_id . '_' . $efective->bank_date;
            $efectivesIndexed[$key] = [
                'total_efective' => $efective->total_efective,
            ];
            $clientNames[$key] = $efective->client_name;
        }

        // 5. LIQUIDACIONES - pendiente agregar sales y que sea de total perception el sum
        $liquidaciones = DB::table('liquidations')
            ->join('deposits', 'liquidations.deposit_id', '=', 'deposits.id')
            ->join('sales', 'sales.id', '=', 'liquidations.sale_id')
            ->select(
                'sales.route_id',
                'deposits.bank_date',
                DB::raw('SUM(DISTINCT sales.total_perception) as total_liquidation')
            )
            ->whereDate('deposits.created_at', '>=', $initial_date)
            ->whereDate('deposits.created_at', '<=', $final_date)
            ->whereIn('liquidations.payment_method_id', [1, 2])
            ->groupBy('sales.route_id', 'deposits.bank_date')
            ->get();


        $liquidacionesIndex = [];
        foreach ($liquidaciones as $liq) {
            $key = $liq->route_id . '_' . $liq->bank_date;
            $liquidacionesIndex[$key] = $liq->total_liquidation;
        }

        // 6. GENERAR LISTA FINAL
        $lista = [];
        foreach ($depositos as $deposito) {
            $key = $deposito->route_id . '_' . $deposito->bank_date;

            $lista[] = [
                'company_short_name' => $deposito->company_short_name,
                'client_name' => $clientNames[$key] ?? null,
                'route_id' => $deposito->route_id,
                'route_name' => $rutasIndex[$deposito->route_id] ?? null,
                'bank_date' => $deposito->bank_date,
                'total_deposit' => $deposito->total_deposit,
                'total_efective' => $efectivesIndexed[$key]['total_efective'] ?? 0,
                'total_liquidation' => $liquidacionesIndex[$key] ?? 0
            ];
        }

        $excel = new RecCajaReportExport($lista);
        return Excel::download($excel, 'reporte-recaudacion-caja.xlsx');
    }
}
