<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Exports\PartitionReportMultiSheetExport;
use App\WarehouseMovement;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ReportPartitionController extends Controller
{
    public function index()
    {

        $current_date = date(DATE_ATOM, mktime(0, 0, 0));
        return view('backend.partition_report')->with(compact('current_date'));
    }

    public function validateForm()
    {
        $messages = [
            'final_date.required'    => 'Debe seleccionar una Fecha.',
        ];

        $rules = [
            'final_date'    => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function list()
    {
        $final_date = CarbonImmutable::createFromDate(request('model.final_date'))->startOfDay()->format('Y-m-d');

        $initialDate = Carbon::parse($final_date);
        $startPreviousMonth = $initialDate->copy()->startOfMonth()->subMonth();
        $endPreviousMonth   = $startPreviousMonth->copy()->endOfMonth();
        $startCurrentMonth  = $initialDate->copy()->startOfMonth();
        $endCurrentMonth    = $initialDate->copy()->endOfDay();

        $analisis_costo = WarehouseMovement::join('warehouse_movement_details', 'warehouse_movement_details.warehouse_movement_id', 'warehouse_movements.id')
            ->join('companies', 'companies.id', 'warehouse_movements.company_id')
            ->join('warehouse_types', 'warehouse_types.id', 'warehouse_movements.warehouse_type_id')
            ->select(
                'warehouse_movements.id',
                'warehouse_movements.created_at',
                'warehouse_movement_details.digit1',
                'warehouse_movement_details.digit2',
                'warehouse_movements.soles_1',
                'warehouse_movements.soles_2',
                'warehouse_movements.created_at',
                'companies.short_name as company_name',
                DB::raw('DATE_FORMAT(warehouse_movements.created_at, "%Y-%m-%d") as fecha_emision'),
                DB::Raw('CONCAT(warehouse_movements.referral_serie_number, "-", warehouse_movements.referral_voucher_number) as factura'),
                'warehouse_types.name as warehouse_type_name',
                'warehouse_movements.origin',
                'warehouse_movements.referral_guide_number as guia',
                'warehouse_movements.license_plate as cisterna',
                'warehouse_movements.license_plate_2 as tracto',
                'warehouse_movements.cost_glp as cost_glp',
                'warehouse_types.short_name as carga_terminal',
                'warehouse_movements.scop_number as scop',
                'warehouse_movements.account_name as chofer'
            )
            ->whereBetween('warehouse_movements.created_at', [$startPreviousMonth, $endCurrentMonth])
            ->where(function ($query) use ($startPreviousMonth, $endPreviousMonth, $startCurrentMonth, $endCurrentMonth) {
                $query->where(function ($q) use ($startPreviousMonth, $endPreviousMonth) {
                    $q->whereBetween('warehouse_movements.created_at', [$startPreviousMonth, $endPreviousMonth])
                        ->where('warehouse_movement_details.digit2', '>', 0);
                })
                    ->orWhere(function ($q) use ($startCurrentMonth, $endCurrentMonth) {
                        $q->whereBetween('warehouse_movements.created_at', [$startCurrentMonth, $endCurrentMonth])
                            ->where('warehouse_movement_details.digit1', '>', 0);
                    });
            })
            ->orderBy('warehouse_movements.created_at', 'ASC')
            ->get()
            ->map(function ($item) use ($startCurrentMonth, $endCurrentMonth) {
                if ($item->created_at >= $startCurrentMonth && $item->created_at <= $endCurrentMonth) {
                    $item->digit = $item->digit1;
                    $item->soles = $item->soles_1;
                    $item->tm = $item->digit / 1000;
                } else {
                    $item->digit = $item->digit2;
                    $item->soles = $item->soles_2;
                    $item->tm = $item->digit / 1000;
                }
                $mes = Carbon::parse($item->created_at)->locale('es')->isoFormat('MMMM');
                $item->nombre_mes = Str::ucfirst($mes);

                if ($item->origin != 27) {
                    $item->article_name = 'E';
                } else {
                    $item->article_name = 'G';
                }

                return $item;
            });


        $datetime = CarbonImmutable::now()->format('d/m/Y h:i:s a');

        $excel = new PartitionReportMultiSheetExport($datetime, $analisis_costo);
        return Excel::download($excel, 'reporte-abastecimiento-particiones.xlsx');
    }
}
