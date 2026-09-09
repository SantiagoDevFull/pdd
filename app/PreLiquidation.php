<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PreLiquidation extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function deposit()
    {
        return $this->belongsTo(Deposit::class, 'deposit_id');
    }
}
