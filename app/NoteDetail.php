<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NoteDetail extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function note()
    {
        return $this->belongsTo(Note::class);
    }

    public function article()
    {
        return $this->belongsTo(Article::class, 'article_code');
    }
}
