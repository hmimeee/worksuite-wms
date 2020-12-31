<?php

namespace Modules\Article\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Routing\Controller;
use App\Http\Controllers\Admin\AdminBaseController;
use Modules\Article\Entities\Article;
use Modules\Article\Entities\ArticleDetails;
use Modules\Article\Entities\ArticleType;
use Modules\Article\Entities\Writer;
use Modules\Article\Entities\ArticleFile;
use Modules\Article\Entities\ArticleComment;
use Modules\Article\Entities\ArticleSetting;
use Modules\Article\Notifications\NewArticle;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\Response;
use App\Helper\Reply;
use Illuminate\Http\File;
use App\User;
use App\Project;
use App\Setting;
use App\RoleUser;
use App\Role;
use App\Task;
use Pusher\Pusher;
use Carbon\Carbon;
use Modules\Article\Datatables\WritersDataTable;

class AdminArticleController extends AdminBaseController
{

    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
        parent::__construct();
        $this->pageTitle = __('article::app.menu.article');
        $this->pageIcon = 'icon-pencil';
        $this->writerRole = ArticleSetting::where('type', 'writer')->first()->value ?? '';
        $this->inhouseWriterRole = ArticleSetting::where('type', 'inhouse_writer')->first()->value ?? '';
        $this->writerHead = ArticleSetting::where('type', 'writer_head')->first()->value ?? '';
        $this->publisher = ArticleSetting::where('type', 'publisher')->first()->value ?? '';
        $this->outreachHead = ArticleSetting::where('type', 'outreach_head')->first()->value ?? '';
        $this->outreachCategory = ArticleSetting::where('type', 'outreach_category')->first()->value ?? '';
    }

    //Writers
    public function getInhouseWriters()
    {
        $this->roleName = ArticleSetting::where('type', 'inhouse_writer')->first()->value;
        $this->writers = User::withoutGlobalScope('active')->join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->select('users.id', 'users.name', 'users.image', 'users.email', 'users.created_at')
            ->where('roles.name', $this->roleName)->get();
        return $this->writers;
    }

    //Writers
    public function getWriters()
    {
        $this->roleName = ArticleSetting::where('type', 'writer')->first()->value;
        $this->writers = User::withoutGlobalScope('active')->join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->select('users.id', 'users.name', 'users.image', 'users.email', 'users.created_at')
            ->where('roles.name', $this->roleName)->get();
        $this->writers = $this->writers->merge($this->getInhouseWriters());

        return $this->writers;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $type = $request->type;
        $orderBy = Schema::hasColumn('articles', $request->orderBy) ? $request->orderBy : 'writing_deadline';
        $orderType = $request->orderType == 'desc' ? 'desc' : 'asc';
        $paginate = is_numeric($request->entries) ? $request->entries : 10;
        $this->startDate = $request->startDate ?? Carbon::today()->subDays(15)->format('Y-m-d');
        $this->endDate = $request->endDate ?? Carbon::today()->addDays(15)->format('Y-m-d');

        $this->articles = Article::join('projects', 'projects.id', '=', 'articles.project_id')
            ->join('users', 'users.id', '=', 'articles.assignee')
            ->select('articles.*', 'projects.project_name', 'users.name');

        //Editable articles
        $this->editable_articles = Article::leftJoin('article_details', 'article_id', '=', 'articles.id')
            ->select('articles.*', 'article_details.label', 'article_details.value')
            ->where('articles.writing_status', 1)
            ->where('article_details.label', 'article_review_writer');

        if (auth()->user()->hasRole($this->inhouseWriterRole)) {
            $this->editable_articles = $this->editable_articles->where('article_details.value', auth()->id());
        }

        if ($request->startDate != null || $request->endDate != null) {
            $this->articles = $this->articles->whereBetween(\DB::raw('DATE(articles.`writing_deadline`)'), [$this->startDate, $this->endDate]);
        }

        //Filter any articles
        if ($request->writer != null) {
            $this->articles = $this->articles->where('articles.assignee', $request->writer);
        }

        if ($request->category != null) {
            $this->articles = $this->articles->where('articles.type', $request->category);
        }

        if ($request->project != null) {
            $this->articles = $this->articles->where('articles.project_id', $request->project);
        }

        if ($request->hide == 'on') {
            $this->articles = $this->articles->where('articles.writing_status', '<>', 2);
        }

        if (auth()->user()->hasRole($this->writerRole) || auth()->user()->hasRole($this->inhouseWriterRole)) {
            $this->articles = $this->articles->where('assignee', auth()->id());
        }

        if (auth()->id() == $this->publisher) {
            $this->articles = $this->articles->where('publisher', auth()->id());
        }

        if ($type == "writingNotStarted") {
            $this->articles = $this->articles->where('working_status', null);
        }

        if ($type == "writingStarted") {
            $this->articles = $this->articles->where('working_status', 1)->where('writing_status', 0);
        }

        if ($type == "overdueArticles") {
            $this->articles = $this->articles->where('writing_deadline', '<', date('Y-m-d'))->where('writing_status', 0);
        }

        if ($type == "myArticles") {
            if (auth()->id() != $this->publisher) {
                $this->articles = $this->articles->where('assignee', auth()->id())->where('writing_status', 0);
            } else {
                $this->articles = $this->articles->where('publisher', auth()->id())->where('publishing_status', null)->orWhere('publishing_status', 0);
            }
        }

        if ($type == "assignByMe") {
            $this->articles = $this->articles->where('creator', auth()->id())->where('writing_status', 0);
        }

        if ($type == "pendingAproval") {
            if (auth()->user()->hasRole('admin')) {
                $this->articles = $this->articles->where('writing_status', 1);
            } else {
                $this->articles = $this->articles->where('creator', auth()->id())->where('writing_status', 1);
            }
        }

        if ($type == "completedArticles") {
            if (auth()->user()->hasRole($this->writerRole)) {
                $this->articles = $this->articles->where('article_status', null)->where('writing_status', 2)->where('assignee', auth()->id());
            } else {
                $this->articles = $this->articles->where('article_status', null)->where('writing_status', 2);
            }
        }

        if ($type == "paidArticles") {
            $this->articles = $this->articles->leftJoin('article_invoices', 'article_invoices.id', '=', 'articles.invoice_id')->where('article_status', 1)->where('article_invoices.status', 1)->where('writing_status', 2);
            if (auth()->user()->hasRole($this->writerRole)) {
                $this->articles = $this->articles->where('assignee', auth()->id());
            }
        }

        if ($type == "unpaidArticles") {
            $this->articles = $this->articles->leftJoin('article_invoices', 'article_invoices.id', '=', 'articles.invoice_id')->where('article_status', 1)->where('article_invoices.status', '<>', 1)->where('writing_status', 2);
            if (auth()->user()->hasRole($this->writerRole)) {
                $this->articles = $this->articles->where('assignee', auth()->id());
            }
        }
        if ($type == "waitingPublish") {
            $ids = ArticleDetails::where('label', 'publish_work_status')->pluck('article_id');

            $this->articles = $this->articles->where('publishing_status', null)->where('publisher', '<>', null)->where('writing_status', 2)->whereNotIn('articles.id', $ids);
        }

        if ($type == "startedPublish") {
            $ids = ArticleDetails::where('label', 'publish_work_status')->pluck('article_id');

            $this->articles = $this->articles->where('publishing_status', null)->where('publisher', '<>', null)->where('writing_status', 2)->whereIn('articles.id', $ids);
        }

        if ($type == "completePublish") {
            $this->articles = $this->articles->where('publishing_status', 1)->where('publisher', '<>', null);
        }

        if ($type == "search") {
            $this->articles = $this->articles->where('articles.id', 'LIKE', '%' . $request->q . '%')->orWhere('title', 'LIKE', '%' . $request->q . '%')->orWhere('projects.project_name', 'LIKE', '%' . $request->q . '%')->orWhere('users.name', 'LIKE', '%' . $request->q . '%');
        }

        if ($request->type == 'edited') {
            $this->articles = $this->editable_articles->where('articles.writing_status', 2);
        }

        if ($request->type == 'edited') {
            $this->articles = Article::leftJoin('article_details', 'article_id', '=', 'articles.id')
                ->select('articles.*', 'article_details.label', 'article_details.value')
                ->where('article_details.label', 'article_review_writer')->where('articles.writing_status', 2);
        }

        //Editable articles
        $this->editable_articles = $this->editable_articles->get();

        $this->all_articles = Article::all();

        $this->articles = $this->articles->orderBy($orderBy, $orderType)->paginate($paginate);


        $this->projects = Project::all();
        $this->categories = ArticleType::all();
        $this->writers = $this->getWriters();
        $this->writers = $this->writers->merge($this->getInhouseWriters());
        $this->pageTitle = "Article Management";
        return view('article::admin.index', $this->data);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $this->article = Article::findOrFail($id);
        if ($this->article->assignee != auth()->id() && auth()->user()->hasRole($this->writerRole)) {
            return Reply::error('You are not authorized for this task!');
        }

<<<<<<< HEAD
        $this->editors = Writer::leftJoin('role_user', 'user_id', '=', 'users.id')
            ->select('role_user.role_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->select('roles.name', 'users.*')
            ->where('roles.name', $this->inhouseWriterRole)
            ->get();

        return view('article::admin.show', $this->data);
    }

    /**
     * Show the list of writers.
     * @return Response
     */
    public function writers(Request $request)
    {
        $this->pageTitle = 'Article Writers';
        $this->pageIcon = 'ti-user';

        $this->writers = Writer::withoutGlobalScope('active')->join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->select('users.*')
            ->where('roles.name', $this->writerRole)
            ->orWhere('roles.name', $this->inhouseWriterRole)
            ->with('articles')
            ->with('rate');
=======
/**
 * Show the list of writers.
 * @return Response
 */
public function writers(Request $request, WritersDataTable $dataTable)
{
    $this->pageTitle = 'Article Writers';
    $this->pageIcon = 'ti-user';

    return $dataTable->render('article::admin.writers', $this->data);
}
}
>>>>>>> 1f3322c3c6355e9545e648b0c49b0cc25f11bbd2

        $this->totalWriters = count($this->writers->get());
        $this->writers = $this->writers->paginate(is_numeric($request->entries) ? $request->entries : 10);

        return view('article::admin.writers', $this->data);
    }
}
