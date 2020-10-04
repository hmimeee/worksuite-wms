<?php

namespace Modules\Article\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Article\Entities\Invoice;

class InvoiceFile extends Model
{
    protected $fillable = ['invoice_id', 'file'];

    public function invoice()
    {
    	return belongsTo(Invoice::class);
    }
}
