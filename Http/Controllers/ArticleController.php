<?php

namespace Modules\Article\Http\Controllers;

use App\Role;
use App\Task;
use App\User;
use App\Leave;
use App\Holiday;
use App\Project;
use App\Setting;
use App\RoleUser;
use Carbon\Carbon;
use Pusher\Pusher;
use App\Helper\Reply;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Modules\Article\Entities\Writer;
use Modules\Article\Entities\Article;
use Illuminate\Support\Facades\Schema;
use Modules\Article\Entities\ArticleFile;
use Modules\Article\Entities\ArticleType;
use Modules\Article\Entities\WriterDetails;
use Illuminate\Support\Facades\Notification;
use Modules\Article\Entities\ArticleComment;
use Modules\Article\Entities\ArticleDetails;
use Modules\Article\Entities\ArticleSetting;
use Modules\Article\Notifications\NewArticle;
use Modules\Article\Entities\WriterPaymentInfo;
use Modules\Article\Entities\ArticleActivityLog;
use Modules\Article\Notifications\ArticleDelete;
use Modules\Article\Notifications\ArticleUpdate;
use Modules\Article\Notifications\ArticleReminder;
use Modules\Article\Notifications\WriterRateAdmin;
use Modules\Article\Notifications\NewArticleReview;
use App\Http\Controllers\Member\MemberBaseController;
use Modules\Article\Notifications\ArticleReviewReturn;
use Modules\Article\Notifications\ArticleWritingReturn;
use Modules\Article\Notifications\NewArticlePublishing;
use Modules\Article\Notifications\WriterUnavailability;
use Modules\Article\Notifications\ArticleReviewComplete;
use Modules\Article\Notifications\ArticleWritingApprove;
use Modules\Article\Notifications\ArticleWritingComplete;
use Modules\Article\Notifications\ArticlePublishingReturn;
use Modules\Article\Notifications\ArticlePublishingComplete;
use Modules\Article\Notifications\ArticlePublishingReminder;

class ArticleController extends MemberBaseController
{

    public function __construct()
    {
        $this->middleware('auth');
        parent::__construct();
        $this->pageTitle = __('article::app.menu.article');
        $this->pageIcon = 'icon-pencil';
        $this->writerRole = ArticleSetting::where('type', 'writer')->first()->value ?? '';
        $this->inhouseWriterRole = ArticleSetting::where('type', 'inhouse_writer')->first()->value ?? '';
        $this->writerHead = ArticleSetting::where('type', 'writer_head')->first()->value ?? '';
        $this->writerHeadAssistant = ArticleSetting::where('type', 'writer_head_assistant')->first()->value ?? '';
        $this->defaulEditor = ArticleSetting::where('type', 'default_editor')->first()->value ?? '';
        $this->publisher = ArticleSetting::where('type', 'publisher')->first()->value ?? '';
        $this->outreachHead = ArticleSetting::where('type', 'outreach_head')->first()->value ?? '';
        $this->publishers = ArticleSetting::where('type', 'publishers')->first()->value ?? '';
        $this->teamLeaders = ArticleSetting::where('type', 'team_leaders')->first()->value ?? '';
        $this->outreachCategory = ArticleSetting::where('type', 'outreach_category')->first()->value ?? '';
        $this->user = auth()->user();
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
            ->where('article_details.label', 'article_review_writer');

        if ($request->startDate != null || $request->endDate != null) {
            $this->articles = $this->articles->whereBetween(\DB::raw('DATE(articles.`writing_deadline`)'), [$this->startDate, $this->endDate]);
        }

        if (auth()->user()->hasRole($this->inhouseWriterRole)) {
            $this->editable_articles = $this->editable_articles->where('article_details.value', auth()->id())->where('article_details.label', 'article_review_writer');
        }

        if (auth()->id() == $this->outreachHead) {
            $this->articles = $this->articles->where('publisher', auth()->id());
        }

        if ($request->writer != null) {
            $this->articles = $this->articles->where('articles.assignee', $request->writer);
        }

        if ($request->category != null) {
            $this->articles = $this->articles->where('articles.type', $request->category);
        }

        if ($request->project) {
            $this->articles = $this->articles->where('project_id', $request->project);
        }

        if ($request->hide == 'on') {
            $this->articles = $this->articles->where('articles.writing_status', '<>', 2);
        }

        if (auth()->id() != $this->writerHeadAssistant && (auth()->user()->hasRole($this->writerRole) || auth()->user()->hasRole($this->inhouseWriterRole))) {
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
            if (auth()->user()->hasRole($this->writerRole) || auth()->user()->hasRole($this->inhouseWriterRole)) {
                $this->articles = $this->articles->where('assignee', auth()->id())->where('writing_status', 0);
            } elseif (in_array(auth()->id(), explode(',', $this->publishers))) {
                $this->articles = $this->articles->whereNull('publishing_status')->whereNotNull('publisher')->where('writing_status', 2);
            } else {
                $this->articles = $this->articles->where('publisher', auth()->id())->where('publishing_status', null)->orWhere('publishing_status', 0);
            }
        }

        if ($type == "assignByMe") {
            $this->articles = $this->articles->where('creator', auth()->id())->where('writing_status', 0);
        }

        if ($type == "pendingAproval") {
            $this->articles = $this->articles->where('writing_status', 1);
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
            $this->articles = $this->articles->where('publishing_status', 1)->where('publisher', '<>', null)->where('writing_status', 2);
        }

        if ($type == "search") {
            if (auth()->user()->hasRole($this->writerRole) || auth()->user()->hasRole($this->inhouseWriterRole)) {
                $this->articles = $this->articles->where('articles.assignee', auth()->id())->where('title', 'LIKE', '%' . $request->q . '%');
            } else {
                $this->articles = $this->articles->where('articles.id', 'LIKE', '%' . $request->q . '%')->orWhere('title', 'LIKE', '%' . $request->q . '%')->orWhere('projects.project_name', 'LIKE', '%' . $request->q . '%')->orWhere('users.name', 'LIKE', '%' . $request->q . '%');
            }
        }

        if ($request->type == 'editable') {
            $this->articles = $this->editable_articles->where('articles.writing_status', 1);
        }

        if ($request->type == 'edited' && !auth()->user()->hasRole('admin')) {
            $this->articles = Article::leftJoin('article_details', 'article_id', '=', 'articles.id')
                ->select('articles.*', 'article_details.label', 'article_details.value')
                ->where('article_details.label', 'article_review_writer')->where('articles.writing_status', 2)->where('article_details.value', auth()->id());
        }

        //Editable articles
        $this->editable_articles = $this->editable_articles->where('articles.writing_status', 1)->get();

        //All articles
        $this->all_articles = Article::all();

        //Customized articles
        $this->articles = $this->articles->orderBy($orderBy, $orderType)->paginate($paginate);

        $this->projects = Project::all();
        $this->categories = ArticleType::all();
        $this->writers = $this->getWriters();
        $this->writers = $this->writers->merge($this->getInhouseWriters());
        $this->pageTitle = "Article Management";
        return view('article::index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $this->tasks = Task::where('board_column_id', '<>', 2);
        $this->projects = Project::all();
        $this->articleTypes = ArticleType::all();
        $this->writers = $this->getWriters();

        return view('article::create', $this->data);
    }

    public function projectData($id)
    {
        $projectTasks = Task::projectOpenTasks($id);

        return Reply::dataOnly(['tasks' => $projectTasks]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        if (Writer::find($request->self ? $request->self : $request->assignee)->hasRole($this->inhouseWriterRole) || $request->self != null) {
            $writerRate = 0;
        } else {
            if (Writer::find($request->self ? $request->self : $request->assignee)->rate == null) {
                return Reply::error("Please update writer's rate for this writer!");
            }
            $writerRate = Writer::find($request->assignee)->rate->rate;
        }

        $count = count($request->title);
        for ($i = 0; $i < $count; $i++) {
            if ($request->title[$i] == null || $request->type[$i] == null || $request->word_count[$i] == null || $request->description[$i] == null || $request->writing_deadline[$i] == null || $request->project == null || ($request->self == null && $request->assignee == null) || $request->priority == null) {
                return Reply::error('article::app.storeNewArticleError');
            }
        }

        for ($i = 0; $i < $count; $i++) {
            if (isset($request->publishing[$i])) {
                $publishing = 1;
            } else {
                $publishing = 0;
            }
            if ($request->parent != null) {
                $parent_task = $request->parent_task;
            } else {
                $parent_task = null;
            }

            $article = new Article([
                'title' => $request->title[$i],
                'type' => $request->type[$i],
                'word_count' => $request->word_count[$i],
                'rate' => $writerRate,
                'publishing' => $publishing,
                'description' => $request->description[$i],
                'project_id' => $request->project,
                'parent_task' => $parent_task,
                'writing_deadline' => $request->writing_deadline[$i],
                'writing_status' => 0,
                'assignee' => $request->self ? $request->self : $request->assignee,
                'creator' => auth()->id(),
                'priority' => $request->priority

            ]);
            $article->save();

            //Store in log
            ArticleActivityLog::create([
                'user_id' => auth()->id(),
                'type' => 'Article',
                'article_id' => $article->id,
                'label' => 'article_create',
                'details' => 'assigned this article.'
            ]);

            //Send notification
            $notifyTo = User::find($request->self ? $request->self : $request->assignee);
            Notification::send($notifyTo, new NewArticle($article));

            $article_id[] = $article->id;
        }

        return  Reply::successWithData('Article assigned, checking attachements!', ['articles' => implode(',', $article_id)]);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function showModal($id)
    {
        $this->article = Article::findOrFail($id);
        if ($this->article->assignee != auth()->id() && auth()->user()->hasRole($this->writerRole)) {
            return Reply::error('You are not authorized for this task!');
        }

        $view = view('article::showModal', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
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
            return abort(403);
        }

        $this->editors = Writer::leftJoin('role_user', 'user_id', '=', 'users.id')
            ->select('role_user.role_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->select('roles.name', 'users.*')
            ->where('roles.name', $this->inhouseWriterRole)
            ->get();

        return view('article::show', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $this->article = Article::findOrFail($id);
        $this->projects = Project::all();
        $this->writers = $this->getWriters();
        $this->articleTypes = ArticleType::all();

        return view('article::edit', $this->data);
        //return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }


    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $this->article = Article::findOrFail($id);
        $this->article->title = $request->title;
        $this->article->type = $request->type;
        $this->article->word_count = $request->word_count;
        if (isset($request->publishing)) {
            $this->article->publishing = 1;
        }
        $this->article->description = $request->description;

        if (isset($request->parent)) {
            $this->article->parent_task = $request->parent_task;
        }

        if (isset($request->project)) {
            $this->article->project_id = $request->project;
        }

        //If Change Writer
        $assignee = $request->self ? $request->self : $request->assignee;
        if ($this->article->assignee != $assignee) {
            if (Writer::find($assignee)->hasRole($this->inhouseWriterRole) || $assignee == $this->writerHead) {
                $writerRate = 0;
            } else {
                if (Writer::find($assignee)->rate == null) {
                    return Reply::error("Please update writer's rate for this writer!");
                }
                $writerRate = Writer::find($assignee)->rate->rate;
            }
            $this->article->rate = $writerRate;

            $message = 'updated the details & changed the writer ' . $this->article->getAssignee->name . ' to ' . Writer::find($assignee)->name;
            $this->article->assignee = $assignee;
        }

        if ($request->publishing) {
            $this->article->publisher = $this->publisher;
        }

        $this->article->writing_deadline = $request->writing_deadline;
        $this->article->priority = $request->priority;
        $this->article->save();

        //Store in log
        ArticleActivityLog::create([
            'user_id' => auth()->id(),
            'type' => 'Article',
            'article_id' => $this->article->id,
            'label' => 'article_update',
            'details' => $message ?? 'updated the article details.'
        ]);

        //Notify about update
        if ($this->article->writing_status == 2 && $this->article->publishing == 1) {
            $notifyTo = User::find($this->article->publisher);
        } else {
            $notifyTo = Writer::find($this->article->assignee);
        }
        Notification::send($notifyTo, new ArticleUpdate($this->article));

        return Reply::success('article::app.updateArticle');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function publishing(Article $article)
    {
        $this->details = ArticleDetails::create([
            'article_id' => $article->id,
            'user_id' => auth()->id(),
            'label' => 'publish_work_status',
            'value' => '1'
        ]);

        //Store in log
        ArticleActivityLog::create([
            'user_id' => auth()->id(),
            'type' => 'Article',
            'article_id' => $article->id,
            'label' => 'article_publishing_status',
            'details' => 'started publishing the article.'
        ]);

        return Reply::success('Started publishing!');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function workStatus(Request $request, $id, $status)
    {
        $article = Article::find($id);
        $article->working_status = $status;
        $article->save();

        //Store in log
        ArticleActivityLog::create([
            'user_id' => auth()->id(),
            'type' => 'Article',
            'article_id' => $article->id,
            'label' => 'article_work_status',
            'details' => 'started writing the article.'
        ]);

        return Reply::success('Work started!');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function review(Request $request, Article $article)
    {
        //Retrive editing && editor status
        $article_review = $article->reviewStatus;
        $article_editor = $article->reviewWriter;

        if (isset($request->editor)) {

            $result = ArticleDetails::updateOrCreate(
                [
                    'article_id' => $article->id,
                    'label' => 'article_review_writer',
                ],
                [
                    'user_id' => auth()->id(),
                    'value' => $request->editor,
                    'description' => 'assigned the article for review'
                ]
            );

            $notifyTo = User::find($request->editor);
            Notification::send($notifyTo, new NewArticleReview($article));
        }

        /* ======== If review article completed and submitted ======== */

        if (isset($request->status)) {
            if ($request->status == 'completed' && is_null($request->rating) && is_null($request->word_count)) {
                return Reply::error('Please fill the required fields!');
            } elseif ($request->status == 'completed' && !is_null($request->rating) && !is_null($request->word_count)) {
                $article = Article::find($article->id);
                $article->rating = $request->rating;
                $article->word_count = $request->word_count;
                $article->publishing_deadline = $request->deadline ?? date('Y-m-d');
                $article->writing_status = 2;

                if ($article->publishing == 1) {
                    if ($article->type == ArticleType::find($this->outreachCategory)->name) {
                        $article->publisher = $this->outreachHead;
                    } else {
                        $article->publisher = $this->publisher;
                    }
                }

                $article->save();

                if ($article->publishing == 1) {
                    //Notify about update
                    $notifyTo = User::find($article->publisher);
                    Notification::send($notifyTo, new NewArticlePublishing($article));
                }
            }

            if (is_null($article_review)) {
                $result = ArticleDetails::create([
                    'article_id' => $article->id,
                    'user_id' => auth()->id(),
                    'label' => 'article_review_status',
                    'value' => $request->status,
                    'description' => 'completed the article review and transferred for publishing.'
                ]);
            } else {

                $article_review = ArticleDetails::find($article_review->id);
                $article_review->value = $request->status;

                if ($request->status == 'completed') {
                    $article_review->description = 'completed article review and approved.';
                } else {
                    $article_review->description = 'return the article for check again.';
                }

                $article_review->save();

                //For activity description
                $result = $article_review;
            }

            if ($request->status == 'completed') {
                $notifyTo = User::find([$this->writerHead, $this->writerHeadAssistant]);
                Notification::send($notifyTo, new ArticleReviewComplete($article));
            } elseif ($request->status == 'incomplete') {
                $notifyTo = User::find($article_editor->value);
                Notification::send($notifyTo, new ArticleReviewReturn($article));
            }
        }

        //Store in activity log
        ArticleActivityLog::create([
            'user_id' => auth()->id(),
            'type' => 'Article',
            'article_id' => $article->id,
            'label' => 'article_review',
            'details' => $result->description . '.'
        ]);

        return Reply::success(ucfirst($result->description) . '!');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function updateStatus(Request $request, $id, $status)
    {
        if (!is_numeric($status))
            return Reply::error('Invalid status value!');

        $article = Article::find($id);
        if ($article->publishing == 1 && $status == 2) {
            if ($request->deadline != null && $request->wordCount != '') {
                $article->writing_status = 2;
                $article->word_count = $request->wordCount;
                if ($article->type == ArticleType::find($this->outreachCategory)->name) {
                    $article->publisher = $this->outreachHead;
                } else {
                    $article->publisher = $this->publisher;
                }
                $article->publishing_deadline = $request->deadline;
                $article->rating = $request->rating;
                $article->save();

                if ($article->publish_website != null && $request->website != null) {
                    $publish_website = ArticleDetails::findOrFail($article->publish_website->id);
                    $publish_website->value = $request->website;
                    $publish_website->save();
                } elseif ($request->website != null) {
                    $publish_website = ArticleDetails::create([
                        'article_id' => $article->id,
                        'user_id' => auth()->id(),
                        'label' => 'publish_website',
                        'value' => $request->website
                    ]);
                }

                //Notify about update
                $notifyTo = User::find($article->publisher);
                Notification::send($notifyTo, new NewArticlePublishing($article));
            } else {
                return Reply::error('Please fill up all the fields!');
            }
        } elseif ($status == 2 && $article->publishing != 1) {
            if ($request->wordCount == null && $request->rating == null) {
                return Reply::error('Please fill up all the fields!');
            }
            $article->word_count = $request->wordCount;
            $article->writing_status = $status;
            $article->rating = $request->rating;
            $article->save();
        } else {
            if ($status == 0 && $article->invoice) {
                return Reply::error('The article has a payslip, can\'t return after generating payslip!');
            }

            if ($status == 1) {
                $comment = $article->comments()->first();
                if ($comment == null) {
                    return Reply::error('Please attach your worked files or write a comment first!');
                }

                // if(!$request->submittedWordCount || !is_numeric($request->submittedWordCount)) {
                //     return Reply::error('Please enter word count first!');
                // }

                // $article->submitted_words = $request->submittedWordCount;
            }
            $article->writing_status = $status;
            $article->save();

            // if ($status == 1) {
            //     $result = ArticleDetails::updateOrCreate(
            //         [
            //             'article_id' => $article->id,
            //             'label' => 'article_review_writer',
            //         ],
            //         [
            //             'user_id' => auth()->id(),
            //             'value' => $this->defaulEditor,
            //             'description' => 'assigned the article for review'
            //         ]
            //     );

            //     $notifyTo = User::find($this->defaulEditor);
            //     Notification::send($notifyTo, new NewArticleReview($article));
            // }
        }

        //Store in log
        if ($status == 1) {
            // $message = 'submitted the article for approval and waiting for review.';
            $message = 'submitted the article for approval.';
        } elseif ($article->publishing == 1 && $status == 2) {
            $message = 'approved the article and transferred for publishing.';
        } elseif ($status == 2 && $article->publishing != 1) {
            $message = 'approved the article.';
        } elseif ($status == 0) {
            $message = 'returned the article for review.';
        }
        ArticleActivityLog::create([
            'user_id' => auth()->id(),
            'type' => 'Article',
            'article_id' => $article->id,
            'label' => 'article_writing_status',
            'details' => $message
        ]);


        if ($status == 1) {
            $notifyTo = User::find([$this->writerHead, $this->writerHeadAssistant]);
            Notification::send($notifyTo, new ArticleWritingComplete($article));
        } elseif ($status == 2) {
            $notifyTo = Writer::find($article->assignee);
            Notification::send($notifyTo, new ArticleWritingApprove($article));
        } elseif ($status == 0) {
            $notifyTo = Writer::find($article->assignee);
            Notification::send($notifyTo, new ArticleWritingReturn($article));
        }

        return Reply::success('article::app.updateArticle');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function updatePublishStatus(Request $request, $id)
    {
        $article = Article::findOrFail($id);
        if ($request->status == 1) {
            $article->publishing_status = 1;
        } else {
            $article->publishing_status = null;
        }
        if (isset($request->link)) {
            $article->publish_link = $request->link;
        } else {
            $article->publish_link = null;
        }

        $article->save();

        if ($request->status == 1) {
            $message = 'completed publishing the article.';
        } elseif ($request->status == 0) {
            $message = 'returned the article publishing for review.';
        }

        //Store in log
        ArticleActivityLog::create([
            'user_id' => auth()->id(),
            'type' => 'Article',
            'article_id' => $article->id,
            'label' => 'article_publishing_status',
            'details' => $message
        ]);

        if ($request->status == 1) {
            $notifyTo = User::find($article->creator);
            Notification::send($notifyTo, new ArticlePublishingComplete($article));
        } else {
            $notifyTo = User::find($article->publisher);
            Notification::send($notifyTo, new ArticlePublishingReturn($article));
        }
        return Reply::success('article::app.updateArticle');
    }

    /**
     * Send a reminder notification.
     * @param int $article
     * @return Response
     */
    public function sendReminder(Article $article)
    {
        if ($article->writing_status == 2 && $article->publishing == 1) {
            $notifyTo = User::find($article->publisher);
            Notification::send($notifyTo, new ArticlePublishingReminder($article));
        } else {
            $notifyTo = Writer::find($article->assignee);
            Notification::send($notifyTo, new ArticleReminder($article));
        }

        //Store in log
        ArticleActivityLog::create([
            'user_id' => auth()->id(),
            'type' => 'Article',
            'article_id' => $article->id,
            'label' => 'article_reminder',
            'details' => 'send a reminder to ' . $notifyTo->name . '.'
        ]);

        return Reply::success('Reminder send to the responsible!');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy(Request $request, $id)
    {
        if (!auth()->user()->hasRole('admin')) {
            return Reply::error('You are not authorized to delete this task!');
        }

        if (!Hash::check($request->password, auth()->user()->password)) {
            return Reply::error('Your password is incorrect!');
        }

        $article = Article::findOrFail($id);

        if ($article->writing_status == 2 && $article->publishing == 1) {
            $notifyTo = User::find($article->publisher);
            Notification::send($notifyTo, new ArticleDelete($article));
        }

        $notifyTo = Writer::find($article->assignee);
        Notification::send($notifyTo, new ArticleDelete($article));

        $notifyTo = User::find($article->creator);
        Notification::send($notifyTo, new ArticleDelete($article));

        $notifyTo = RoleUser::where('role_id', 1)->get();
        foreach ($notifyTo as $notifyTo) {
            $notifyTo = User::find($notifyTo->user_id);
            Notification::send($notifyTo, new ArticleDelete($article));
        }

        $article->delete();

        return Reply::success('Article deleted!');
    }

    /**
     * Show the list of writers.
     * @return Response
     */
    public function writers(Request $request)
    {
        if (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole($this->writerRole) && !auth()->user()->hasRole($this->inhouseWriterRole) && auth()->id() != $this->writerHead && !in_array(auth()->id(), explode(',', $this->teamLeaders))) {
            return abort(403);
        }
        $this->pageTitle = 'Article Writers';
        $this->pageIcon = 'ti-user';
        if (auth()->id() == $this->writerHead || auth()->id() == $this->writerHeadAssistant || auth()->user()->hasRole('admin')) {
            $writers = Writer::whereHas('roles', function ($q) {
                return $q->whereIn('name', [$this->inhouseWriterRole, $this->writerRole]);
            });

            if ($request->search != null) {
                $writers->where('name', $request->search);
            }
        } else {
            $writers = Writer::where('id', auth()->id());
        }

        if ($request->status == 'Unavailable')
            $writers = $writers->whereHas('unavailable');

        if ($request->status == 'Available')
            $writers = $writers->whereDoesntHave('unavailable');

        if ($request->type == 'Inhouse')
            $writers = $writers->whereHas('roles', function ($q) {
                return $q->where('name', $this->inhouseWriterRole);
            });

        if ($request->type == 'Freelance')
            $writers = $writers->whereHas('roles', function ($q) {
                return $q->where('name', $this->writerRole);
            });

        $this->totalWriters = $writers->count();

        $this->writers = $writers->get();

        return view('article::writers', $this->data);
    }

    /**
     * Show details of writers.
     * @return Response
     */
    public function writerView($id)
    {
        $this->writer = Writer::findOrFail($id);
        if (auth()->id() == $id || auth()->id() == $this->writerHead || in_array(auth()->id(), explode(',', $this->writerHeadAssistant)) || auth()->user()->hasRole('admin')) {
            //Continue
        } else {
            return Reply::error('You are not authorized to view this page!');
        }

        $this->articles = $this->writer->articles;
        $this->earning = 0;
        $this->rating = 0;
        $this->employeeRole = $this->writer->role->where('role_id', 2)->first();
        foreach ($this->articles->where('writing_status', 2) as $article) {
            if ($article->article_status == 1) {
                $this->earning += $article['rate'] / 1000 * $article['word_count'];
            }
            $this->rating += $article['rating'] / count($this->articles->where('writing_status', 2));
        }

        $startDate = request()->startDate ? Carbon::create(request()->startDate) : now()->startOfMonth();
        $endDate = request()->endDate ? Carbon::create(request()->endDate) : now();

        request()->startDate = $startDate->format('Y-m-d');
        request()->endDate = $endDate->format('Y-m-d');

        $holidays = Holiday::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])->get();
        $leaves = Leave::where('user_id', $id)
            ->where('duration', '<>', 'half day')
            ->whereBetween('leave_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get();
        $halfleaves = Leave::where('user_id', $id)
            ->where('duration', 'half day')
            ->whereBetween('leave_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get();

        $this->incompleteArticles = Article::where('assignee', $id)->where('writing_status', '<>', 2)->get();

        $this->articles = Article::where('assignee', $id)
            ->whereHas('logs', function ($q) use ($startDate, $endDate) {
                return $q->where('label', 'article_writing_status')
                    ->where(function ($q) {
                        return $q->where('details', 'submitted the article for approval.')
                            ->orWhere('details', 'submitted the article for approval and waiting for review.');
                    })
                    ->whereBetween('created_at', [$startDate->format('Y-m-d H:i:s'), $endDate->format('Y-m-d H:i:s')]);
            })
            ->get();

        $this->range_words = 0;
        foreach ($this->articles as $article) {
            $this->range_words = $this->range_words + $article->word_count;
        }

        if ($this->writer->hasRole('Inhouse_writer')) {
            $this->edited_articles = Article::where('writing_status', 2)
                ->whereHas('reviewWriter', function ($q) use ($id) {
                    return $q->where('value', $id);
                })
                ->whereHas('logs', function ($q) use ($startDate, $endDate) {
                    return $q->where('label', 'article_review')
                        ->whereBetween('created_at', [$startDate->format('Y-m-d H:i:s'), $endDate->format('Y-m-d H:i:s')]);
                })
                ->get();

            $this->edited_words = 0;
            foreach ($this->edited_articles as $eart) {
                $this->edited_words += $eart->word_count;
            }
        }

        $this->days = $startDate->diffInDays($endDate) - ($holidays->count() + $leaves->count() + ($halfleaves->count() / 2));

        return view('article::writerView', $this->data);
    }

    public function writerStats($id)
    {
        $startDate = (request()->startDate ? Carbon::create(request()->startDate, 'Asia/Dhaka') : now()->startOfMonth())->setTimezone($this->global->timezone)->startOfDay();
        $endDate = (request()->endDate ? Carbon::create(request()->endDate, 'Asia/Dhaka') : now())->setTimezone($this->global->timezone)->endOfDay();
        $writer = Writer::find($id);
        $holidays = Holiday::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])->get();
        $leaves = Leave::where('user_id', $id)
            ->where('duration', '<>', 'half day')
            ->whereBetween('leave_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get();
        $halfleaves = Leave::where('user_id', $id)
            ->where('duration', 'half day')
            ->whereBetween('leave_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get();

        $articles = Article::with('logs')
            ->where('assignee', $id)
            ->whereHas('logs', function ($q) use ($startDate, $endDate) {
                return $q->where(function ($q) {
                    return $q->where('details', 'submitted the article for approval.')
                        ->orWhere('details', 'submitted the article for approval and waiting for review.');
                })
                    ->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->get();

        if ($writer->hasRole('Inhouse_writer')) {
            $edited_articles = Article::with('logs')
                ->whereHas('reviewWriter', function ($q) use ($id) {
                    return $q->where('value', $id);
                })
                ->whereHas('logs', function ($q) use ($startDate, $endDate) {
                    return $q->where('label', 'article_review')
                        ->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->get();

            $ewords = 0;
            foreach ($edited_articles as $eart) {
                $ewords = $ewords + $eart->word_count;
            }
        }

        $words = 0;
        foreach ($articles as $article) {
            $words = $words + $article->word_count;
        }

        $days = $startDate->diffInDays($endDate) - ($holidays->count() + $leaves->count() + ($halfleaves->count() / 2));

        return Reply::dataOnly([
            'articles' => $articles,
            'words' => $words,
            'days' => $days,
            'earticles' => $edited_articles ?? [],
            'ewords' => $ewords ?? 0
        ]);
    }

    public function writerPaymentDetails($id)
    {
        $this->writer = Writer::find($id);
        return view('article::writerAddDetails', $this->data);
    }

    public function writerPaymentDetailsDelete(Request $request, WriterPaymentInfo $payment)
    {
        if (!Hash::check($request->password, auth()->user()->password)) {
            return Reply::error('Your password is incorrect!');
        }

        //Store in log
        ArticleActivityLog::create([
            'user_id' => auth()->id(),
            'type' => 'Writer',
            'writer_id' => $payment->user_id,
            'label' => 'writer_payment_info_delete',
            'details' => 'deleted a payment details.'
        ]);

        $payment->delete();

        return Reply::success('Details deleted!');
    }

    public function writerPaymentUpdate(Request $request, $id)
    {
        if (!Hash::check($request->password, auth()->user()->password)) {
            return Reply::error('Your password is incorrect!');
        }

        $this->writer = Writer::find($id);
        $this->details = WriterPaymentInfo::find($request->details_id);

        if ($this->writer->paymentDetails == null) {
            $this->payment = WriterDetails::create([
                'user_id' => $id,
                'label' => 'payment_details',
                'title' => $this->details->payment_method,
                'details' => $this->details->payment_details
            ]);
        } else {
            $this->payment = WriterDetails::find($this->writer->paymentDetails->id);
            if ($this->payment->updated_at->format('d-m-Y') == \Carbon\Carbon::now()->format('d-m-Y')) {
                return Reply::error("You can update payment details once a day!");
            }
            $this->payment->user_id = $id;
            $this->payment->label = 'payment_details';
            $this->payment->title = $this->details->payment_method;
            $this->payment->details = $this->details->payment_details;
            $this->payment->save();
        }

        //Store in log
        ArticleActivityLog::create([
            'user_id' => auth()->id(),
            'type' => 'Writer',
            'writer_id' => $this->payment->user_id,
            'label' => 'writer_payment_details',
            'details' => 'updated default payment details.'
        ]);

        return Reply::success('Details updated successfully!');
    }

    public function writerPaymentDetailsStore(Request $request, $id)
    {
        if (is_null($request->details)) {
            return Reply::error('Please add details!');
        }

        $writer = Writer::find($id);

        $this->payment = WriterPaymentInfo::create([
            'user_id' => $writer->id,
            'payment_method' => $request->method,
            'payment_details' => str_replace(':break', '<br/>', strip_tags(str_replace('</p>', ':break', $request->details)))
        ]);

        //Store in log
        ArticleActivityLog::create([
            'user_id' => auth()->id(),
            'type' => 'Writer',
            'writer_id' => $this->payment->user_id,
            'label' => 'writer_payment_info_add',
            'details' => 'added a new payment details.'
        ]);

        return Reply::success('Details added successfully!');
    }

    /**
     * Delete employee role of a writer.
     * @return Response
     */
    public function writerAvailability(Writer $writer, Request $request)
    {
        if ($request->status && !$writer->unavailable) {
            WriterDetails::create([
                'user_id' => $writer->id,
                'label' => 'writer_unavailable',
                'title' => 'Availability',
                'details' => $request->note
            ]);

            $message = 'changed the writer status to Unavailable';
        } else {
            $writer->unavailable->delete();
            $message = 'changed the writer status to Available';
        }

        //Store in log
        ArticleActivityLog::create([
            'user_id' => auth()->id(),
            'type' => 'Writer',
            'writer_id' => $writer->id,
            'label' => 'writer_unavailability',
            'details' => $message
        ]);

        // $notifyTo = User::find(10);
        // Notification::send($notifyTo, new WriterUnavailability($writer));

        return Reply::success(ucfirst($message));
    }

    public function changePublisher(Request $request, $id)
    {
        $article = Article::find($id);

        if (!$article)
            return false;

        $article->update([
            'publisher' => $request->publisher
        ]);

        //Store in log
        ArticleActivityLog::create([
            'user_id' => auth()->id(),
            'type' => 'Article',
            'article_id' => $article->id,
            'label' => 'article_update',
            'details' => 'updated the publisher'
        ]);

        return Reply::success('Publisher updated successfully');
    }
}
