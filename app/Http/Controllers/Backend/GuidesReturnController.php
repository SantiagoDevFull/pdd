<?php

namespace App\Http\Controllers\Backend;

use App\Article;
use App\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\WarehouseMovement;
use App\WarehouseMovementDetail;
use stdClass;
use App\PlantMovement;
use App\PlantMovementDetail;

class GuidesReturnController extends Controller
{
    public function index()
    {
        $companies = Company::select('id', 'name')->whereIn('id', [1, 2])->get();

        return view('backend.guides_return')->with(compact('companies'));
    }

    public function validateForm()
    {
        $messages = [
            'company_id.required'               => 'Debe seleccionar una Compañía.',
            'warehouse_movement_id.required'    => 'El Nº de Parte es obligatorio.',
        ];

        $rules = [
            'company_id'            => 'required',
            'warehouse_movement_id' => 'required',
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function getWarehouseMovements()
    {
        $company_id = request('company_id');

        $elements = WarehouseMovement::select(
            'id',
            'referral_guide_series',
            'referral_guide_number',
            'license_plate',
            'created_at'
        )
            ->where('company_id', $company_id)
            ->where('warehouse_type_id', 5)
            ->where(function ($query) {
                $query->where('action_type_id', 3)
                    ->orWhere('action_type_id', 4)
                    ->orWhere('action_type_id', 6)
                    ->orWhere('action_type_id', 7)
                    ->orWhere('action_type_id', 8);
            })
            ->where('state', 0)
            /*
            ->when($company_id == 1, function ($query) {
                $query->where('movement_type_id', 11);
            })
                */
            ->orderBy('movement_number', 'asc')
            ->get();

        $elements->map(function ($item, $index) {
            $item->creation_date = date('d-m-Y', strtotime($item->created_at));
        });

        $array_movments = array();

        foreach ($elements as $element) {
            array_push($array_movments, $element);
        };

        $array_movments = array_reverse($array_movments);

        return $array_movments;
    }

    public function list()
    {

        $company_id = request('model.company_id');
        $warehouse_movement_id = request('model.warehouse_movement_id');

        $movementDetails = WarehouseMovementDetail::select(
            'id',
            'warehouse_movement_id',
            'item_number',
            'article_code',
            'digit_amount',
            'converted_amount'
        )
            ->where('warehouse_movement_id', $warehouse_movement_id)
            ->orderBy('item_number', 'asc')
            ->get();

        $movementDetails->map(function ($item, $index) {

            $parse_converted_amount = intval(floatval($item->converted_amount));
            $parse_digit_amount = intval(floatval($item->digit_amount));

            $item->parent = null;
            $item->article_id = $item->article->id;
            $item->article_code = $item->article->code;
            $item->article_name = $item->article->name . ' ' . $item->article->warehouse_unit->name . ' x ' . $item->article->package_warehouse;
            $item->presale = $parse_digit_amount;
            $item->presale_converted_amount = $parse_converted_amount;
            $item->retorno = 0;
            $item->retorno_press = 0;
            $item->cambios = 0;
            $item->prestamo = 0;
            $item->cesion = 0;
            $item->vacios = 0;
            $item->robo_vacio = 0;
            $item->robo_lleno = 0;
            $item->group_id = $item->article->group_id;

            if ($item->article->group_id == 26) {
                $item->liquidar = $parse_digit_amount;
            } else {
                $item->liquidar = 0;
            };

            $item->retorno = 0;
            $item->cambios = 0;
            $item->prestamo = 0;
            $item->cesion = 0;
            $item->vacios = $item->liquidar;
        });

        $elements = array();

        foreach ($movementDetails as $val) {
            array_push($elements, $val);
        };

        $elements = array_filter($elements, function ($val) {
            return $val->group_id != 7;
        });

        return $elements;
    }

    public function detail()
    {
        $id = request('id');

        $element = WarehouseMovementDetail::select('id', 'new_stock_return')
            ->findOrFail($id);

        return $element;
    }

    public function update(Request $request)
    {

        $articles = $request->articles;
        $warehouse_movement_id = $request->warehouse_movement_id;
        $warehouse_type_id = 5;


        foreach ($articles as $article) {

            $warehouse_movement_detail_id = $article['id'];

            $warehouse_movement_detail = WarehouseMovementDetail::find($warehouse_movement_detail_id);

            $articleGeneral = Article::where('warehouse_type_id', 5)
                ->where('id', $article['article_id'])
                ->first();


            switch ($article['article_id']) {
                //GLP 05 KG (MARCA) - MERCADERIA
                case 1:
                case 5:
                case 9:
                case 13:
                case 17:
                case 10261:
                case 10368:
                case 10372:
                case 10376:

                    $code = 9288;
                    //FIERRO - PLANTA DORADO
                    $articleBalon = Article::where('id', $code)
                        ->firstOrFail();
                    break;
                //GLP 10 KG (MARCA) - MERCADERIA
                case 2:
                case 6:
                case 10:
                case 14:
                case 18:
                case 10260:
                case 10369:
                case 10373:
                case 10377:
                case 10398:
                case 10397:
                case 10400:
                case 10403:
                case 10406:
                case 10409:
                    $code = 9289;
                    //FIERRO - PLANTA DORADO
                    $articleBalon = Article::where('id', $code)
                        ->firstOrFail();
                    break;
                //GLP 15 KG (MARCA) - MERCADERIA
                case 3:
                case 7:
                case 11:
                case 15:
                case 19:
                case 10370:
                case 10374:
                case 10378:
                case 10399:
                case 10401:
                case 10404:
                case 10407:
                case 10410:
                    $code = 9290;
                    //FIERRO - PLANTA DORADO
                    $articleBalon = Article::where('id', $code)
                        ->firstOrFail();
                    break;
                //GLP 45 KG (MARCA) - MERCADERIA
                case 4:
                case 8:
                case 12:
                case 16:
                case 20:
                case 10371:
                case 10375:
                case 10379:
                case 10402:
                case 10405:
                case 10408:
                case 10411:
                    $code = 9291;
                    //FIERRO - PLANTA DORADO
                    $articleBalon = Article::where('id', $code)
                        ->firstOrFail();
                    break;
            }



            if ($article['retorno'] > 0) {

                /********** Retornos Llenos - Movimiento *********/
                //Generar Movimiento de Ingreso - Producción
                $id = PlantMovement::insertGetId([
                    'company_id' => 1,
                    'warehouse_type_id' => 4, //Producción
                    'movement_class_id' => 1, //Ingreso
                    'movement_type_id' => 19, //Retorno de Pre-Venta
                    'warehouse_account_type_id' => 1,
                    'total' => $article['retorno'],
                    'created_at' => date('Y-m-d'),
                    'updated_at' => date('Y-m-d'),
                ]);

                PlantMovementDetail::insert([
                    'plant_movement_id' => $id,
                    'item_number' => 1,
                    'article_code' => $article['article_id'],
                    'new_stock_good' => $article['retorno'],
                    'converted_amount' => $article['retorno'],
                    'total' => $article['retorno'],
                    'created_at' => date('Y-m-d'),
                    'updated_at' => date('Y-m-d'),
                ]);
                $articleGeneral->lleno_tran = $articleGeneral->lleno_tran + $article['retorno'];
            }

            if ($article['retorno_press'] > 0) {

                /********** Retornos Llenos - Movimiento *********/
                //Generar Movimiento de Ingreso - Producción
                $id = PlantMovement::insertGetId([
                    'company_id' => 1,
                    'warehouse_type_id' => 4, //Producción
                    'movement_class_id' => 1, //Ingreso
                    'movement_type_id' => 19, //Retorno de Pre-Venta
                    'warehouse_account_type_id' => 1,
                    'total' => $article['retorno_press'],
                    'created_at' => date('Y-m-d'),
                    'updated_at' => date('Y-m-d'),
                ]);

                PlantMovementDetail::insert([
                    'plant_movement_id' => $id,
                    'item_number' => 1,
                    'article_code' => $articleBalon->id,
                    'new_stock_good' => $article['retorno_press'],
                    'converted_amount' => $article['retorno_press'],
                    'total' => $article['retorno'],
                    'created_at' => date('Y-m-d'),
                    'updated_at' => date('Y-m-d'),
                ]);
            }

            if ($article['cambios'] > 0) {

                /********** Cambios - Movimiento *********/

                //Generar Movimiento de Ingreso - Producción
                $id = PlantMovement::insertGetId([
                    'company_id' => 1,
                    'warehouse_type_id' => 4, //Producción
                    'movement_class_id' => 1, //Ingreso
                    'movement_type_id' => 32, //Retorno por Cambios
                    'warehouse_account_type_id' => 1,
                    'total' => $article['cambios'],
                    'created_at' => date('Y-m-d'),
                    'updated_at' => date('Y-m-d'),
                ]);

                PlantMovementDetail::insert([
                    'plant_movement_id' => $id,
                    'item_number' => 1,
                    'article_code' => $articleBalon->id,
                    'new_stock_good' => $article['cambios'],
                    'converted_amount' => $article['cambios'],
                    'total' => $article['cambios'],
                    'created_at' => date('Y-m-d'),
                    'updated_at' => date('Y-m-d'),
                ]);

                $articleGeneral->stock_good = $articleGeneral->stock_good + $article['cambios'];
            }

            /**here && $article['parent'] */
            if ($article['prestamo'] > 0) {

                /********** Préstamos - Movimiento *********/

                //Generar Movimiento de Salida - Producción
                $id = PlantMovement::insertGetId([
                    'company_id' => 1,
                    'warehouse_type_id' => 4, //Producción
                    'movement_class_id' => 2, //Salida
                    'movement_type_id' => 33, //Préstamos de Balones
                    'warehouse_account_type_id' => 1,
                    'total' => $article['prestamo'],
                    'press' => 1,
                    'created_at' => date('Y-m-d'),
                    'updated_at' => date('Y-m-d'),
                ]);

                PlantMovementDetail::insert([
                    'plant_movement_id' => $id,
                    'item_number' => 1,
                    'article_code' => $articleBalon->id,
                    'new_stock_good' => $article['prestamo'],
                    'converted_amount' => $article['prestamo'],
                    'total' => $article['prestamo'],
                    'created_at' => date('Y-m-d'),
                    'updated_at' => date('Y-m-d'),
                ]);
                $articleBalon->lleno_tran = $articleBalon->lleno_tran - $article['prestamo'];
                $articleBalon->prestamo = $articleBalon->prestamo + $article['prestamo'];
            }

            if ($article['cesion'] > 0) {
                $warehouse_movement = WarehouseMovement::select('referral_guide_series', 'referral_guide_number', 'license_plate')
                    ->where('id', $warehouse_movement_id)
                    ->first();

                $referral_guide_series = $warehouse_movement->referral_guide_series;
                $referral_guide_number = $warehouse_movement->referral_guide_number;
                $license_plate = $warehouse_movement->license_plate;

                /********** Cesión de uso - Movimiento *********/

                //Generar Movimiento de Salida - Producción
                $id = PlantMovement::insertGetId([
                    'company_id' => 1,
                    'warehouse_type_id' => 4, //Producción
                    'movement_class_id' => 2, //Salida
                    'movement_type_id' => 28, //Cesión de Uso
                    'referral_guide_series' => $referral_guide_series,
                    'referral_guide_number' => $referral_guide_number,
                    'license_plate' => $license_plate,
                    'warehouse_account_type_id' => 1,
                    'total' => $article['cesion'],
                    'created_at' => date('Y-m-d'),
                    'updated_at' => date('Y-m-d'),
                ]);

                PlantMovementDetail::insert([
                    'plant_movement_id' => $id,
                    'item_number' => 1,
                    'article_code' => $articleBalon->id,
                    'new_stock_good' => $article['cesion'],
                    // 'new_stock_return' => $article['presale_converted_amount'] - $article['cesion'],
                    'converted_amount' => $article['cesion'],
                    'total' => $article['cesion'],
                    'created_at' => date('Y-m-d'),
                    'updated_at' => date('Y-m-d'),
                ]);

                $articleBalon->lleno_tran = $articleBalon->lleno_tran - $article['cesion'];
                $articleBalon->cesion_uso = $articleBalon->cesion_uso + $article['cesion'];

                $warehouse_movement_detail->old_stock_cesion = $warehouse_movement_detail->new_stock_cesion;
                $warehouse_movement_detail->new_stock_cesion = $article['cesion'];
            }
            $warehouse_movement_detail->save();
            /**Off */

            $movimiento = WarehouseMovement::findOrFail($warehouse_movement_id);
            if ($article['robo_vacio'] > 0) {
                $id = PlantMovement::insertGetId([
                    'company_id' => 1,
                    'warehouse_type_id' => 4, //Producción
                    'movement_class_id' => 2, //Ingreso
                    'movement_type_id' => 34, //Robo de Balones Vacío
                    'warehouse_account_type_id' => 3, //Trabajador
                    'employee_id' => $movimiento->employee_id,
                    'referral_guide_series' => $movimiento->referral_guide_series,
                    'referral_guide_number' => $movimiento->referral_guide_number,
                    'referral_serie_number' => $movimiento->referral_serie_number,
                    'referral_voucher_number' => $movimiento->referral_voucher_number,
                    'license_plate' => $movimiento->license_plate,
                    'total' => $article['robo_vacio'],
                    'created_at' => $movimiento->created_at,
                    'traslate_date' => $movimiento->traslate_date,
                    'updated_at' => date('Y-m-d'),
                ]);

                PlantMovementDetail::insert([
                    'plant_movement_id' => $id,
                    'item_number' => 1,
                    'article_code' => $articleBalon->id,
                    'converted_amount' => $article['robo_vacio'],
                    'total' => $article['retorno'],
                    'created_at' => date('Y-m-d'),
                    'updated_at' => date('Y-m-d'),
                ]);
            }
            if ($article['robo_lleno'] > 0) {
                $id = PlantMovement::insertGetId([
                    'company_id' => 1,
                    'warehouse_type_id' => 4, //Producción
                    'movement_class_id' => 2, //Ingreso
                    'movement_type_id' => 35, //Robo de Balones Llenos
                    'warehouse_account_type_id' => 3, //Trabajador
                    'employee_id' => $movimiento->employee_id,
                    'referral_guide_series' => $movimiento->referral_guide_series,
                    'referral_guide_number' => $movimiento->referral_guide_number,
                    'referral_serie_number' => $movimiento->referral_serie_number,
                    'referral_voucher_number' => $movimiento->referral_voucher_number,
                    'license_plate' => $movimiento->license_plate,
                    'total' => $article['robo_lleno'],
                    'created_at' => $movimiento->created_at,
                    'traslate_date' => $movimiento->traslate_date,
                    'updated_at' => date('Y-m-d'),
                ]);

                PlantMovementDetail::insert([
                    'plant_movement_id' => $id,
                    'item_number' => 1,
                    'article_code' => $articleBalon->id,
                    'converted_amount' => $article['robo_lleno'],
                    'total' => $article['retorno'],
                    'created_at' => date('Y-m-d'),
                    'updated_at' => date('Y-m-d'),
                ]);
            }

            //Actualizar new_stock_return
            if ($article['article']['group_id'] == 26) {
                WarehouseMovementDetail::where('warehouse_movement_id', $article['warehouse_movement_id'])
                    ->where('article_code', $article['article_id'])
                    ->update([
                        'new_stock_return' =>  $article['retorno'] + $article['cambios'] + $article['robo_lleno'],
                    ]);
            } else if ($article['article']['group_id'] == 7) {
                // el new_stock_return deberia solo registrar los blones en buen estado y no los prestamos ni los mal estado
                WarehouseMovementDetail::where('warehouse_movement_id', $article['warehouse_movement_id'])
                    ->where('article_code', $article['article_id'])
                    ->update([
                        'new_stock_return' =>  $article['converted_amount'] - $article['cesion'] - $article['prestamo'] - $article['cambios'] + $article['robo_vacio'],
                    ]);
            }

            $warehouse_movement_id = $article['warehouse_movement_id'];
        }

        $data = new stdClass();
        $data->type = 1;
        $data->title = '¡Ok!';
        $data->msg = 'Registro actualizado exitosamente.';

        return  $articles;
        return $warehouse_movement_id;
    }
}
