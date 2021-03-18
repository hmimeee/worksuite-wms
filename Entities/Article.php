<?php

namespace Modules\Article\Entities;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Modules\Article\Entities\ArticleFile;
use Modules\Article\Entities\ArticleActivityLog;
use Modules\Article\Entities\ArticleComment;
use Modules\Article\Entities\Writer;
use Modules\Article\Entities\Invoice;
use Modules\Article\Entities\ArticleDetails;
use App\Task;
use App\Project;

class Article extends Model
{
    protected $table = 'articles';
    protected $guarded = [];

    public function getAssignee()
    {
        return $this->belongsTo(Writer::class, 'assignee');
    }

    public function getCreator()
    {
        return $this->belongsTo(User::class, 'creator');
    }

    public function getPublisher()
    {
        return $this->belongsTo(User::class, 'publisher');
    }

    public function files()
    {
        return $this->hasMany(ArticleFile::class, 'article_id');
    }

    public function comments()
    {
        return $this->hasMany(ArticleComment::class, 'article_id');
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'parent_task');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function logs()
    {
        return $this->hasMany(ArticleActivityLog::class);
    }

    public function completedLog()
    {
        return $this->logs()->where(function ($q) {
            return $q->where('details', 'submitted the article for approval.')
                ->orWhere('details', 'submitted the article for approval and waiting for review.');
        });
    }

    public function details()
    {
        return $this->hasMany(ArticleDetails::class);
    }

    public function publish()
    {
        return $this->hasOne(ArticleDetails::class)->where('label', 'publish_work_status');
    }

    public function publish_website()
    {
        return $this->hasOne(ArticleDetails::class)->where('label', 'publish_website');
    }

    public function reviewWriter()
    {
        return $this->hasOne(ArticleDetails::class)->where('label', 'article_review_writer');
    }

    public function reviewStatus()
    {
        return $this->hasOne(ArticleDetails::class)->where('label', 'article_review_status');
    }
}
