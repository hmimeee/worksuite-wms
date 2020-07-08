<?php

namespace Modules\Article\Entities;

use Illuminate\Database\Eloquent\Model;

class ArticleSetting extends Model
{
	protected $guarded = [];

	protected $table = 'article_settings';
}
