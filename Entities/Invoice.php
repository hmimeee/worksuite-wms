<?php

namespace Modules\Article\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Article\Entities\Writer;
use Modules\Article\Entities\ArticleActivityLog;

class Invoice extends Model
{
    protected $guarded = [];
    protected $table = 'article_invoices';

    public function user()
    {
    	return $this->belongsTo(Writer::class, 'paid_to');
    }

    public function articles()
    {
    	return $this->hasMany(Article::class, 'invoice_id');
    }

    public function logs()
    {
        return $this->hasMany(ArticleActivityLog::class, 'invoice_id');
    }

    public function receipts()
    {
        return $this->hasMany(InvoiceFile::class);
    }
}
