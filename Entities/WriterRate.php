<?php

namespace Modules\Article\Entities;

use Illuminate\Database\Eloquent\Model;

class WriterRate extends Model
{
    protected $fillable = ['user_id', 'rate'];
}
