<?php

namespace Modules\Article\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Http\Controllers\Admin\AdminBaseController;
use Modules\Article\Entities\Article;
use Modules\Article\Entities\ArticleSetting;
use App\Helper\Reply;
use App\Project;
use App\User;
use Carbon\Carbon;

class AdminReportController extends AdminBaseController
{
 public function __construct()
 {
    $this->middleware(['auth','role:admin']);
    parent::__construct();
    $this->pageTitle = 'Article Reports';
    $this->pageIcon = 'ti-stats-up';
    $this->user = auth()->user();
    $this->writerRole = ArticleSetting::where('type', 'writer')->first()->value;
    $this->inhouseWriterRole = ArticleSetting::where('type', 'inhouse_writer')->first()->value;
    $this->writerHead = ArticleSetting::where('type', 'writer_head')->first()->value;
    $this->publisher = ArticleSetting::where('type', 'publisher')->first()->value;
}

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request, Article $articles)
    {
        $this->articles = $articles;
        $this->startDate = $request->start_date ?? Carbon::now()->subDays(7)->format('Y-m-d');
        $this->endDate = $request->end_date ?? Carbon::now()->format('Y-m-d');

        $this->articles = Article::leftJoin('article_activity_logs', 'article_activity_logs.article_id', '=', 'articles.id')->select('articles.*', 'article_activity_logs.details', 'article_activity_logs.article_id')->where('article_activity_logs.details', 'submitted the article for approval.')->whereBetween(\DB::raw('DATE(article_activity_logs.`created_at`)'), [Carbon::create($this->startDate)->format('Y-m-d'), Carbon::create($this->endDate)->format('Y-m-d')]);

        if ($request->project != null) {
            $this->articles =  $this->articles->where('project_id', $request->project);
        }

        if ($request->writer != null) {
            $writers = User::withoutGlobalScope('active')->join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->select('users.id')
            ->where('roles.name', $request->writer)->pluck('users.id');

            if ($request->writer == $this->inhouseWriterRole) {
                $writerHead = User::find($this->writerHead)->id;
                $writers = $writers->merge($writerHead);
            }

            $this->articles =  $this->articles->whereIn('assignee', $writers);
        }

        if ($request->assignee != null) {
            $this->articles =  $this->articles->where('assignee', $request->assignee);
        }

        $this->articles = $this->articles->get()->unique();

        //Get writers
        $control = new AdminArticleController();
        $this->writers = $control->getWriters();

        $this->projects = Project::all();
        $this->words = 0;
        $this->cost = 0;
        foreach ($this->articles as $article) {
            $this->words += $article->word_count;
            $this->cost += ($article->word_count*$article->rate)/1000;
        }

        return view('article::admin.reports', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('article::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show(Request $request, Article $articles)
    {
        $this->articles = $articles;
        $this->startDate = $request->start_date ?? Carbon::now()->subDays(7)->format('Y-m-d');
        $this->endDate = $request->end_date ?? Carbon::now()->format('Y-m-d');

        $this->articles = Article::whereBetween('writing_deadline', [$this->startDate, $this->endDate]);

        if ($request->project != null) {
            $this->articles =  $this->articles->where('project_id', $request->project);
        }

        if ($request->writer != null) {
            $writers = User::withoutGlobalScope('active')->join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->select('users.id')
            ->where('roles.name', $request->writer)->pluck('users.id');

            if ($request->writer == $this->inhouseWriterRole) {
                $writerHead = User::find($this->writerHead)->id;
                $writers = $writers->merge($writerHead);
            }

            $this->articles =  $this->articles->whereIn('assignee', $writers);
        }

        if ($request->assignee != null) {
            $this->articles =  $this->articles->where('assignee', $request->assignee);
        }

        $this->articles = $this->articles->get();
        $this->projects = Project::all();
        $this->words = 0;
        $this->cost = 0;
        foreach ($this->articles as $article) {
            $this->words += $article->word_count;
            $this->cost += ($article->word_count*$article->rate)/1000;
        }

        return view('article::reportPrint', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return view('article::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
