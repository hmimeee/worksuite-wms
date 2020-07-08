<?php

namespace Modules\Article\Entities;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Modules\Article\Entities\Article;
use Modules\Article\Entities\Invoice;
use Modules\Article\Entities\Writer;

class ArticleActivityLog extends Model
{
    protected $guarded = [];

    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    public function article()
    {
    	return $this->belongsTo(Article::class);
    }

    public function writer()
    {
    	return $this->belongsTo(Writer::class, 'writer_id');
    }

    public function invoice()
    {
    	return $this->belongsTo(Invoice::class);
    }
}
