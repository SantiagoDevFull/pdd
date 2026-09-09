<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BlChoferNotificationSent extends Model
{
    protected $fillable = ['chofer_id', 'tipo', 'fecha_envio'];
    public $timestamps = true;
}
