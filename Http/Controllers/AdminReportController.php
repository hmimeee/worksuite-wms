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
        $startDate = $request->start_date ? Carbon::create($request->start_date)->startOfDay() : Carbon::now()->subDays(7);
        $endDate = $request->end_date ? Carbon::create($request->end_date)->startOfDay() : Carbon::now();
        $this->startDate = $startDate->format('Y-m-d');
        $this->endDate = $endDate->format('Y-m-d');

        $this->articles = $articles->where('writing_status', 2)
        ->whereHas('logs', function ($q) use ($startDate, $endDate) {
            return $q->where('label', 'article_writing_status')
            ->where('details', 'submitted the article for approval.')
            ->whereBetween('created_at', [$startDate->format('Y-m-d H:i:s'), $endDate->format('Y-m-d H:i:s')]);
        });
        
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

    public function dailyReports(Request $request)
    {
        $this->pageTitle = 'Daily Reports';
        $this->date = $request->date ?? Carbon::now()->format('Y-m-d');
        $this->submittedArticles = Article::whereHas('logs', function($q){
            return $q->where('details', 'submitted the article for approval.')->whereDate('created_at', $this->date);
        })->get();

        $this->submittedArticlesWords = $this->submittedArticles->sum('word_count');

        $this->approvedArticles = Article::whereHas('logs', function ($q) {
            return $q->whereDate('created_at', $this->date)
            ->where(function ($query) {
                return $query->where('details', 'approved the article and transferred for publishing.')->orWhere('details', 'approved the article.');
            });
        })->get();

        $this->approvedArticlesWords = $this->approvedArticles->sum('word_count');

        $this->assignedArticles = Article::whereHas('logs', function ($q) {
            return $q->where('details', 'assigned this article.')
            ->whereDate('created_at', $this->date);
        })->get();
        $this->assignedArticlesWords = $this->assignedArticles->sum('word_count');

        $this->projects = Project::all();

        return view('article::admin.daily-reports', $this->data);
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
