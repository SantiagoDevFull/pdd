<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentMethod extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    public const CREDIT = 7;

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }
}
