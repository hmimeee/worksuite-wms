<?php

namespace Modules\Article\Entities;

use Illuminate\Database\Eloquent\Model;
use App\User;

class WriterDetails extends Model
{
    protected $guarded = [];

    public function user()
	{
		return $this->belongsTo(User::class, 'user_id');
	}
}
