<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlantMovementDetail extends Model
{
    use SoftDeletes;
	protected $dates = ['deleted_at'];

    public function plant_movement() {
        return $this->belongsTo(PlantMovement::class);
    }

    public function article() {
        return $this->belongsTo(Article::class, 'article_code');
    }
}
