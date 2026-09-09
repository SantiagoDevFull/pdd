<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BlVehicleNotificationSent extends Model
{
    protected $fillable = ['vehicle_id', 'tipo', 'fecha_envio'];
    public $timestamps = true;
}
