<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CocheraMovementDetail extends Model
{
    use SoftDeletes;
	protected $dates = ['deleted_at'];

    public function cochera_movement() {
        return $this->belongsTo(CocheraMovement::class);
    }

    public function article() {
        return $this->belongsTo(Article::class, 'article_code');
    }
}
