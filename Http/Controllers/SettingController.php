<?php

namespace Modules\Article\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Http\Controllers\Member\MemberBaseController;
use Modules\Article\Entities\ArticleSetting;
use Modules\Article\Entities\ArticleType;
use App\User;
use App\Role;
use App\Helper\Reply;

class SettingController extends MemberBaseController
{
    public function __construct()
    {
        $this->middleware('auth');
        parent::__construct();
        $this->pageTitle = 'Article Management Settings';
        $this->pageIcon = 'ti-settings';
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $this->settings = ArticleSetting::all();
        $this->writerRole = ArticleSetting::where('type', 'writer')->first()->value ?? '';
        $this->inhouseWriterRole = ArticleSetting::where('type', 'inhouse_writer')->first()->value ?? '';
        $this->writerHead = ArticleSetting::where('type', 'writer_head')->first()->value ?? '';
        $this->publisher = ArticleSetting::where('type', 'publisher')->first()->value ?? '';
        $this->outreachHead = ArticleSetting::where('type', 'outreach_head')->first()->value ?? '';
        $this->roles = Role::all();
        $this->employees = User::all();
        $this->types = ArticleType::paginate(10);

        if (!user()->hasRole('admin') && !user()->is_writer() && !user()->is_inhouse_writer() && user()->is_writer()) {
            return abort(403);
        }

        return view('article::settings', $this->data);
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
    public function update(Request $request)
    {
        return Reply::success('article::app.storeSettingSuccess');
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
