<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PreSaleDetail extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
	protected $fillable = ['kg'];

	public function sale() {
		return $this->belongsTo(PreSale::class);
	}

	public function article() {
		return $this->belongsTo(Article::class);
	}
}
