<?php

namespace App\Http\Controllers\Backend;

use App\ClientSector;
use App\Exports\ListPriceReportMultiSheetExport;
use App\Http\Controllers\Controller;
use App\PriceList;
use Carbon\CarbonImmutable;
use Maatwebsite\Excel\Facades\Excel;

class ListPriceReportController extends Controller
{
    public function index()
    {
        $client_sectors = ClientSector::whereIn('id', [1, 5])->get();
        return view('backend.list_price_report')->with(compact('client_sectors'));
    }

    public function validateForm()
    {
        $messages = [
            'client_sector_id.required'    => 'Debe seleccionar un Canal',
        ];

        $rules = [
            'client_sector_id' => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }


    public function export()
    {


        $client_sector_id = request('model.client_sector_id');
        $current_date = CarbonImmutable::now()->format('Y-m-d');

        if ($client_sector_id == 1) { // ENVASADO

            $articleIds = [
                // Chamba
                'C05K' => 10368,
                'C10K' => 10369,
                'C15K' => 10370,
                'C45K' => 10371,
                // El Gas
                'E05K'  => 13,
                'E10K' => 14,
                'E15K' => 15,
                'E45K' => 16,
                // Maquigas
                'M05K' => 17,
                'M10K' => 18,
                'M15K' => 19,
                'M45K' => 20,
                // Plus Gas
                'P05K' => 9,
                'P10K' => 10,
                'P15K' => 11,
                'P45K' => 12,
                // Rinde Gas
                'R05K' => 1,
                'R10K' => 2,
                'R15K' => 3,
                'R45K' => 4,
            ];

            // 1. Obtener clientes
            $clients = PriceList::join('clients', 'clients.id', '=', 'price_lists.client_id')
                ->join('client_routes', 'client_routes.id', '=', 'clients.route_id')
                ->join('client_channels', 'client_channels.id', '=', 'clients.channel_id')
                ->join('client_zones', 'client_zones.id', '=', 'clients.zone_id')
                ->select(
                    'clients.id as client_id',
                    'clients.business_name as client_name',
                    'client_routes.short_name as route_name',
                    'client_channels.name as channel_name',
                    'client_zones.name as zone_name'
                )
                ->where('clients.sector_id', $client_sector_id)
                ->where('price_lists.final_effective_date', '>=', $current_date)
                //->whereNotIn('clients.route_id', [96, 97, 98, 99])
                ->where('price_lists.state', 1)
                ->where('price_lists.warehouse_type_id', 5)
                ->orderBy('client_channels.name')
                ->orderBy('client_routes.short_name')
                ->distinct()
                ->get();

            $clientIds = $clients->pluck('client_id')->toArray();

            // 2. UNA sola query para todos los precios
            $allPrices = PriceList::whereIn('client_id', $clientIds)
                ->whereIn('article_id', array_values($articleIds))
                ->where('state', 1)
                ->where('final_effective_date', '>=', $current_date)
                ->orderByDesc('id')
                ->get(['id', 'client_id', 'article_id', 'price_igv'])
                ->groupBy('client_id');


            // 3. Mapear precios al cliente (sin queries adicionales)
            $flipArticles = array_flip($articleIds); // article_id => key (ej: 10368 => 'C05K')

            $prices = $clients->map(function ($item) use ($allPrices, $articleIds) {

                $clientPrices = $allPrices->get($item->client_id, collect());

                foreach ($articleIds as $key => $articleId) {

                    $price = $clientPrices
                        ->where('article_id', $articleId)
                        ->first(); // toma el primero porque ya está ordenado DESC por id

                    $item->$key = optional($price)->price_igv;
                }

                return $item;
            });

            $excel = new ListPriceReportMultiSheetExport($prices, $client_sector_id);
            return Excel::download($excel, 'reporte-lista-precios.xlsx');
        } else if ($client_sector_id == 5) { // COMERCIO

            $articleIds = [
                // PREMIUM
                'PREM05K' => 5,
                'PREM10K' => 6,
                'PREM15K' => 7,
                'PREM45K' => 8,
            ];

            // 1. Obtener clientes
            $clients = PriceList::join('clients', 'clients.id', '=', 'price_lists.client_id')
                ->join('client_routes', 'client_routes.id', '=', 'clients.route_id')
                ->join('client_channels', 'client_channels.id', '=', 'clients.channel_id')
                ->join('client_zones', 'client_zones.id', '=', 'clients.zone_id')
                ->join('client_types', 'client_types.id', '=', 'clients.business_type')
                ->select(
                    'clients.id as client_id',
                    'clients.business_name as client_name',
                    'client_routes.short_name as route_name',
                    'client_types.name as business_type_name',
                    'client_channels.name as channel_name',
                    'client_zones.name as zone_name'
                )
                ->where('clients.sector_id', $client_sector_id)
                ->where('price_lists.final_effective_date', '>=', $current_date)
                //->whereIn('clients.route_id', [34, 35, 37, 45, 38])
                ->where('price_lists.state', 1)
                ->where('price_lists.warehouse_type_id', 5)
                ->orderBy('client_channels.name')
                ->orderBy('client_routes.short_name')
                ->distinct()
                ->get();

            $clientIds = $clients->pluck('client_id')->toArray();

            // 2. UNA sola query para todos los precios
            $allPrices = PriceList::whereIn('client_id', $clientIds)
                ->whereIn('article_id', array_values($articleIds))
                ->where('state', 1)
                ->where('final_effective_date', '>=', $current_date)
                ->orderByDesc('id')
                ->get(['id', 'client_id', 'article_id', 'price_igv'])
                ->groupBy('client_id');

            $prices = $clients->map(function ($item) use ($allPrices, $articleIds) {

                $clientPrices = $allPrices->get($item->client_id, collect());

                foreach ($articleIds as $key => $articleId) {

                    $item->$key = optional(
                        $clientPrices->firstWhere('article_id', $articleId)
                    )->price_igv;
                }

                return $item;
            });

            $excel = new ListPriceReportMultiSheetExport($prices, $client_sector_id);
            return Excel::download($excel, 'reporte-lista-precios.xlsx');
        }
    }
}
