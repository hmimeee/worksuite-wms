<?php

namespace Modules\Article\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Http\Controllers\Admin\AdminBaseController;
use Modules\Article\Entities\Writer;
use Modules\Article\Entities\Article;
use Modules\Article\Entities\ArticleSetting;
use Modules\Article\Entities\WriterLeave;
use App\Helper\Reply;
use App\User;
use Modules\Article\Notifications\LeaveGranted;
use Modules\Article\Notifications\LeaveApplied;
use Carbon\Carbon;

class AdminLeavesController extends AdminBaseController
{
    public function __construct()
    {
        $this->middleware(['auth','role:admin']);
        parent::__construct();
        $this->pageTitle = 'Writer\'s Leaves';
        $this->pageIcon = 'ti-notepad';
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
    public function index(Request $request)
    {
        $paginate = is_numeric($request->entries) ? $request->entries : 10;
        $this->startDate = $request->startDate ?? Carbon::today()->subDays(20)->format('Y-m-d');
        $this->endDate = $request->endDate ?? Carbon::today()->addDays(20)->format('Y-m-d');

        $leaves = WriterLeave::whereBetween(\DB::raw('DATE(`created_at`)'), [$this->startDate, $this->endDate]);

        if (auth()->user()->hasRole($this->writerRole)) {
            $leaves->where('user_id', auth()->id());
        }

        if ($request->writer) {
            $leaves->where('user_id', $request->writer);
        }

        $this->writers = Writer::whereHas('roles', function($q){
            return $q->where('name', $this->writerRole);
        })->get();
        $this->applications = $leaves->paginate($paginate);

        return view('article::admin.leaves', $this->data);
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
    public function show(WriterLeave $leave)
    {
        //
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
    public function update(Request $request, WriterLeave $leave)
    {
        $leave->status = 1;
        $leave->save();

        //Send notification
        $notifyTo = $leave->writer;
        \Notification::send($notifyTo, new LeaveGranted($leave));

        return Reply::success('Application status changed!');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy(WriterLeave $leave)
    {
        $leave->delete();
        return Reply::success('Application has been deleted!');
    }
}
