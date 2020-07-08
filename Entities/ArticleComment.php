<?php

namespace Modules\Article\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Article\Entities\Article;
use App\User;

class ArticleComment extends Model
{
    protected $guarded = [];
    protected $table = 'article_comments';

    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    public function article()
    {
    	return $this->belongsTo(Article::class);
    }
}
