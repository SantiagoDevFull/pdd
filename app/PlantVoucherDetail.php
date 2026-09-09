<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlantVoucherDetail extends Model
{
	public function voucher() {
		return $this->belongsTo(PlantVoucher::class);
	}

	public function unit() {
		return $this->belongsTo(Unit::class);
	}
}
