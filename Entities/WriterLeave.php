<?php

namespace Modules\Article\Entities;

use Illuminate\Database\Eloquent\Model;

class WriterLeave extends Model
{
    protected $guarded = [];

    public function writer()
    {
    	return $this->belongsTo(Writer::class, 'user_id');
    }
}
