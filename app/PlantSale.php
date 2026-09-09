<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlantSale extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

	public function sale_details() {
		return $this->hasMany(PlantSaleDetail::class);
	}
}
