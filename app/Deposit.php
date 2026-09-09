<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Deposit extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function bank()
    {
        return $this->belongsTo(BankAccount::class, 'bank_id');
    }
    public function bank_account()
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }
    
	
}
