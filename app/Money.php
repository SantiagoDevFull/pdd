<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Money extends Model
{
    protected $table = 'moneys';

    protected $fillable = ['plant_order_id'];
}
