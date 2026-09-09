<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlantSaleDetail extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
	protected $fillable = ['kg'];

	public function sale() {
		return $this->belongsTo(PlantSale::class);
	}

	public function article() {
		return $this->belongsTo(Article::class);
	}
}
