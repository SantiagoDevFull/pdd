<?php
foreach ($articles as $item) {

    $art_conv = Article::where('warehouse_type_id', $warehouse_type_id)
        ->where('id', $item['id'])
        ->firstOrFail();

    switch ($item['id']) {
        case 1:
        case 5:
        case 9:
        case 13:
        case 17:
            $code = 9288;
            $article_pre = Article::where('id', $code)
                ->firstOrFail();
            break;
        case 2:
        case 6:
        case 10:
        case 14:
        case 18:
            $code = 9289;
            $article_pre = Article::where('id', $code)
                ->firstOrFail();
            break;
        case 3:
        case 7:
        case 11:
        case 15:
        case 19:
            $code = 9290;
            $article_pre = Article::where('id', $code)
                ->firstOrFail();
            break;
        case 4:
        case 8:
        case 12:
        case 16:
        case 20:
            $code = 9291;
            $article_pre = Article::where('id', $code)
                ->firstOrFail();
            break;
    }

    $digit_amount = str_replace(',', '', $item['digit_amount']);
    $converted_amount = str_replace(',', '', $item['converted_amount']);
    $price = str_replace(',', '', $item['price']);
    $sale_value = str_replace(',', '', $item['sale_value']);
    $inaccurate_value = str_replace(',', '', $item['inaccurate_value']);
    $igv = str_replace(',', '', $item['igv']);
    $total = str_replace(',', '', $item['total']);
    $igv_perception = str_replace(',', '', $item['perception']);

    $code_granel = 9312;
    $granel = Article::where('id', $code_granel)
        ->firstOrFail();

    if ($movement_type_id == 11) {
        if ($item['id'] != 23 && $item['id'] != 24) {
            if ($art_conv->stock_good >= $converted_amount) {
                //producto terminado
                $art_conv->stock_good = $art_conv->stock_good - $converted_amount;
                //fierro
                $article_pre->lleno_tran = $article_pre->lleno_tran + $converted_amount;
                $article_pre->lleno_plan = $art_conv->stock_good;
            } else {
                //fierro
                $article_pre->stock_good = $article_pre->stock_good - ($converted_amount - $art_conv->stock_good);
                $article_pre->lleno_tran = $article_pre->lleno_tran + $converted_amount;
                //granel kg
                $granel->stock_good = $granel->stock_good - (($converted_amount - $art_conv->stock_good) * $art_conv->convertion);
                //producto terminado
                $art_conv->stock_good = 0;
            }
        } else {
            $granel->stock_good = $granel->stock_good - $converted_amount;
        }
    } else if ($movement_type_id == 12) {
        if ($item['id'] != 23 && $item['id'] != 24) {
            $granel->stock_good = $granel->stock_good - ($converted_amount * $art_conv->convertion);
        } else {
            $granel->stock_good = $granel->stock_good - $converted_amount;
        }
    }

    if ($movement_type_id == 11) {

        $plan_movement = new PlantMovement();
        $plan_movement->company_id = $company_id;
        $plan_movement->warehouse_type_id = $warehouse_type_id;
        $plan_movement->movement_class_id = $movement_class_id;
        $plan_movement->movement_type_id = $movement_type_id;
        $plan_movement->movement_number = $movement_number;
        $plan_movement->warehouse_account_type_id = $warehouse_account_type_id;
        $plan_movement->account_id = $warehouse_account_id;
        $plan_movement->account_document_number = $account->document_number;
        $plan_movement->account_name = $account->business_name;
        $plan_movement->referral_guide_series = $referral_guide_series;
        $plan_movement->referral_guide_number = $referral_guide_number;
        $plan_movement->scop_number = $scop_number;
        $plan_movement->license_plate = $license_plate;
        $plan_movement->taxed_operation = array_sum(array_column($articles, 'sale_value'));
        $plan_movement->unaffected_operation = array_sum(array_column($articles, 'inaccurate_value'));
        $plan_movement->exonerated_operation = 0;
        $plan_movement->igv = array_sum(array_column($articles, 'igv'));
        $plan_movement->total = array_sum(array_column($articles, 'total'));
        $plan_movement->total_perception = array_sum(array_column($articles, 'perception'));
        $plan_movement->action_type_id = ($movement_type ? $movement_type->action_type_id : '');
        $plan_movement->created_at = date('Y-m-d', strtotime($since_date));
        $plan_movement->created_at_user = Auth::user()->user;
        $plan_movement->updated_at_user = Auth::user()->user;
        $plan_movement->traslate_date = date('Y-m-d', strtotime($traslate_date));
        $plan_movement->fac_date = date('Y-m-d', strtotime($traslate_date));
        $plan_movement->route_id = $route_id;
        $plan_movement->transportist_id = $transportist->transportist_id;
        $plan_movement->save();

        $plan_movement_2 = $plan_movement->replicate();
        $plan_movement_2->warehouse_type_id = 22;
        $plan_movement_2->movement_type_id = 21;
        $plan_movement_2->save();

        $plan_movement_3 = $plan_movement->replicate();
        $plan_movement_3->warehouse_type_id = 22;
        $plan_movement_3->movement_type_id = 21;
        $plan_movement_3->save();

        $planMovementDetail = new PlantMovementDetail();
        $planMovementDetail->plant_movement_id = $plan_movement->id;
        $planMovementDetail->item_number = $item['item_number'];
        $planMovementDetail->article_code = $item['id'];
        $planMovementDetail->digit_amount = $digit_amount;
        $planMovementDetail->converted_amount = $converted_amount;
        $planMovementDetail->new_stock_good = $article_pre->stock_good;
        $planMovementDetail->price = $price;
        $planMovementDetail->sale_value = $sale_value;
        $planMovementDetail->exonerated_value = 0;
        $planMovementDetail->inaccurate_value = $inaccurate_value;
        $planMovementDetail->igv = $igv;
        $planMovementDetail->total = $total;
        $planMovementDetail->igv_perception = $igv_perception;
        $planMovementDetail->igv_percentage = $item['igv_percentage'];
        $planMovementDetail->igv_perception_percentage = $item['perception_percentage'];
        $planMovementDetail->created_at_user = Auth::user()->user;
        $planMovementDetail->updated_at_user = Auth::user()->user;
        $planMovementDetail->save();

        $planMovementDetail_2 = new PlantMovementDetail();
        $planMovementDetail_2->plant_movement_id = $plan_movement_2->id;
        $planMovementDetail_2->item_number = $item['item_number'];
        $planMovementDetail_2->article_code = $code;
        $planMovementDetail_2->digit_amount = $digit_amount;
        $planMovementDetail_2->converted_amount = $converted_amount;
        $planMovementDetail_2->created_at_user = Auth::user()->user;
        $planMovementDetail_2->updated_at_user = Auth::user()->user;
        $planMovementDetail_2->save();

        $planMovementDetail_3 = new PlantMovementDetail();
        $planMovementDetail_3->plant_movement_id = $plan_movement_3->id;
        $planMovementDetail_3->item_number = $item['item_number'];
        $planMovementDetail_3->article_code = $code_granel;
        $planMovementDetail_3->digit_amount = $digit_amount;
        $planMovementDetail_3->converted_amount = $converted_amount;
        $planMovementDetail_3->created_at_user = Auth::user()->user;
        $planMovementDetail_3->updated_at_user = Auth::user()->user;
        $planMovementDetail_3->save();

        $art_conv->save();
        $article_pre->save();
        $granel->save();
    }
}
