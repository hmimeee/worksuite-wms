<?php

namespace Modules\Article\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Controllers\Member\MemberBaseController;
use Modules\Article\Entities\Article;
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
use App\Setting;
use App\RoleUser;
use App\Role;
use App\Task;
use Pusher\Pusher;

class ArticleTypeController extends MemberBaseController
{
    public function __construct()
    {
        $this->middleware('auth');
        parent::__construct();
        $this->pageTitle = __('article::app.menu.article');
        $this->pageIcon = 'icon-pencil';
        $this->user = auth()->user();
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $this->types = ArticleType::paginate(10);
        return view('article::type.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('article::type', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $type = ArticleType::create([
            'name' => $request->name,
            'description' => $request->description
        ]);
        $type->save();
        return Reply::success('Type created!');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return view('article::show');
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
    public function destroy(ArticleType $type)
    {
        $type->delete();
        return Reply::success('Type deleted!');
    }
}
