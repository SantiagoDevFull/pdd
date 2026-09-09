<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PreSale extends Model
{
	use SoftDeletes;
	protected $dates = ['deleted_at'];

	public function sale_details()
	{
		return $this->hasMany(PreSaleDetail::class);
	}

	public function warehouseDocumentType()
	{
		return $this->belongsTo(WarehouseDocumentType::class, 'warehouse_document_type_id');
	}

	public function client()
	{
		return $this->belongsTo(Client::class, 'client_id');
	}

	public function pre_liquidations()
    {
        return $this->hasMany(PreLiquidation::class); 
    }
	public function discount() {
        return $this->hasOne(PreDiscount::class);
    }

}
