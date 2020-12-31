<?php

namespace Modules\Article\Entities;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Role;
use Modules\Article\Entities\Article;
use Modules\Article\Entities\WriterRate;
use Modules\Article\Entities\WriterPaymentInfo;
use Modules\Article\Entities\ArticleActivityLog;
use Modules\Article\Entities\WriterDetails;

class Writer extends User
{
	protected $table = 'users';

	public function articles()
	{
		return $this->hasMany(Article::class, 'assignee');
	}

	public function rate()
	{
		return $this->hasOne(WriterRate::class, 'user_id');
	}

	public function paymentInfos()
	{
		return $this->hasMany(WriterPaymentInfo::class, 'user_id');
	}

	public function paymentDetails()
	{
		return $this->hasOne(WriterDetails::class, 'user_id')->where('label', 'payment_details');
	}

	public function logs()
	{
		return $this->hasMany(ArticleActivityLog::class, 'writer_id');
	}

	public function leaves()
	{
		return $this->hasMany(WriterLeave::class, 'user_id');
	}

	public function unavailable()
	{
		return $this->hasOne(WriterDetails::class, 'user_id')->where('label', 'writer_unavailable');
	}
}
